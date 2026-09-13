/* Run with NODE_PATH pointing to a development install of playwright. Read-only live HTML, local theme assets. */
const { chromium } = require("playwright");
const fs = require("fs");
(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.route("**/assets/css/theme.css*", (route) =>
    route.fulfill({
      contentType: "text/css",
      body: fs.readFileSync("spray-nova-theme/assets/css/theme.css", "utf8"),
    }),
  );
  await page.route("**/assets/js/theme.js*", (route) =>
    route.fulfill({
      contentType: "application/javascript",
      body: fs.readFileSync("spray-nova-theme/assets/js/theme.js", "utf8"),
    }),
  );
  await page.route("**/hero-nbq-poster.jpg", (route) =>
    route.fulfill({
      contentType: "image/jpeg",
      body: fs.readFileSync(
        "spray-nova-theme/assets/images/hero-nbq-poster.jpg",
      ),
    }),
  );
  const results = [];
  for (const url of [
    "https://spraynova.es/",
    "https://spraynova.es/producto/dripper-dope-18-mm-ink/",
    "https://spraynova.es/tienda/",
    "https://spraynova.es/categoria-producto/rotuladores/",
    "https://spraynova.es/carrito/",
    "https://spraynova.es/finalizar-compra/",
  ]) {
    await page.goto(url, { waitUntil: "domcontentloaded" });
    await page.locator("body").waitFor();
    if (url === "https://spraynova.es/") {
      await page
        .locator("#spray-nova-theme-inline-css")
        .evaluateAll((nodes) => nodes.forEach((n) => n.remove()));
      await page.addStyleTag({ path: "spray-nova-theme/assets/css/home.css" });
    }
    if (url.includes("dripper")) {
      await page.addScriptTag({
        path: "spray-nova-theme/assets/js/variations.js",
      });
      const buttons = page.locator(".spray-variation-option");
      await buttons.first().waitFor();
      for (let i = 0; i < (await buttons.count()); i++) {
        await buttons.nth(i).click();
        if (
          (await page
            .locator('.spray-variation-option[aria-pressed="true"]')
            .count()) !== 1
        )
          throw Error("Selection state mismatch");
        const label = await buttons.nth(i).locator("strong").textContent();
        if (
          !(
            await page.locator(".spray-selected-color-label").textContent()
          ).includes(label)
        )
          throw Error("Selection label mismatch");
      }
    }
    for (const width of [320, 375, 390, 768, 1024, 1440, 1920]) {
      await page.setViewportSize({ width, height: 900 });
      await page.evaluate(() => window.scrollTo(0, 0));
      if (
        (width === 390 || width === 1440) &&
        (url === "https://spraynova.es/" || url.includes("dripper"))
      )
        await page.screenshot({
          path:
            "/tmp/spray-" +
            (url.includes("dripper") ? "dripper" : "home") +
            "-" +
            width +
            ".png",
          fullPage: true,
        });
      const result = await page.evaluate(() => {
        const bad = [
          ...document.querySelectorAll(
            "main h1,main h2,.hero h1,.product-grid,.category-grid,form.cart",
          ),
        ]
          .filter((el) => {
            const r = el.getBoundingClientRect();
            return (
              r.width &&
              getComputedStyle(el).clip === "auto" &&
              (r.right > innerWidth + 1 ||
                r.left < -1 ||
                el.scrollWidth > el.clientWidth + 2)
            );
          })
          .map((el) => el.className || el.tagName);
        const offenders = [...document.querySelectorAll("body *")]
          .filter((e) => {
            const r = e.getBoundingClientRect();
            return (
              r.width &&
              r.right > innerWidth + 1 &&
              getComputedStyle(e).visibility !== "hidden"
            );
          })
          .slice(0, 8)
          .map((e) => ({
            tag: e.tagName,
            cls: e.className,
            html: e.outerHTML.slice(0, 350),
          }));
        return {
          bad,
          offenders,
          overflow: document.documentElement.scrollWidth > innerWidth + 1,
        };
      });
      results.push({ url, width, ...result });
    }
  }
  fs.writeFileSync(
    "/tmp/spraynova-responsive-results.json",
    JSON.stringify(results, null, 2),
  );
  await browser.close();
  console.log(
    JSON.stringify(
      results.filter((x) => x.bad.length || x.overflow),
      null,
      2,
    ),
  );
  if (results.some((x) => x.bad.length || x.overflow)) process.exitCode = 1;
})().catch((e) => {
  console.error(e);
  process.exitCode = 1;
});
