const { chromium } = require("playwright");
const fs = require("fs");
(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const errors = [];
  page.on("pageerror", (e) => errors.push(e.message));
  const base = "http://127.0.0.1:9400";
  const results = [];
  async function check(name) {
    for (const width of [320, 375, 390, 768, 1024, 1440, 1920]) {
      await page.setViewportSize({ width, height: 900 });
      await page.evaluate(() => window.scrollTo(0, 0));
      await page.waitForTimeout(220);
      const result = await page.evaluate(() => ({
        overflow: document.documentElement.scrollWidth > innerWidth + 1,
        bad: [
          ...document.querySelectorAll(
            "main h1,main h2,.hero h1,.product-grid,.category-grid,form.cart",
          ),
        ]
          .filter((e) => {
            const r = e.getBoundingClientRect();
            return (
              r.width &&
              !e.closest(".screen-reader-text, .sr-only") &&
              getComputedStyle(e).clip === "auto" &&
              (r.right > innerWidth + 1 ||
                r.left < -1 ||
                e.scrollWidth > e.clientWidth + 2)
            );
          })
          .map((e) => e.className || e.tagName),
      }));
      results.push({ name, width, ...result });
      if ([390, 1440].includes(width))
        await page.screenshot({
          path: `/tmp/spray-local-${name}-${width}.png`,
          fullPage: true,
        });
    }
  }
  await page.goto(base, { waitUntil: "load" });
  await page.locator(".hero").waitFor();
  const product = await page
    .locator(".product-card a")
    .first()
    .getAttribute("href");
  await check("home");
  await page.setViewportSize({ width: 390, height: 900 });
  await page.locator(".menu-toggle").click();
  if (!(await page.locator(".desktop-nav").isVisible()))
    throw Error("Mobile menu hidden");
  await page.locator(".menu-toggle").click();
  await page.locator(".desktop-nav").waitFor({ state: "hidden" });
  await page.locator(".search-toggle").click();
  await page.locator(".search-panel.open #site-search").waitFor();
  await page.locator(".search-close").click();

  await page.goto(product, { waitUntil: "load" });
  const buttons = page.locator(".spray-variation-option");
  await buttons.first().waitFor();
  if (await page.locator("select.spray-variation-native").isVisible())
    throw Error("Duplicate native selector visible");
  for (let i = 0; i < (await buttons.count()); i++) {
    await buttons.nth(i).click();
    await page.waitForFunction(
      () => Number(document.querySelector("input.variation_id").value) > 0,
    );
    if (
      (await page
        .locator('[aria-pressed="true"].spray-variation-option')
        .count()) !== 1
    )
      throw Error("Selection count");
    await page.waitForFunction(
      () =>
        getComputedStyle(
          document.querySelector(
            '.spray-variation-option[aria-pressed="true"]',
          ),
        ).backgroundColor === "rgb(198, 160, 235)",
    );
    const style = await buttons.nth(i).evaluate((e) => ({
      bg: getComputedStyle(e).backgroundColor,
      after: getComputedStyle(e, "::after").content,
    }));
    if (
      style.bg !== "rgb(198, 160, 235)" ||
      (style.after !== "none" &&
        style.after !== "normal" &&
        style.after !== '""')
    )
      throw Error(JSON.stringify(style));
  }
  await buttons.first().focus();
  await page.keyboard.press("Enter");
  if ((await buttons.first().getAttribute("aria-pressed")) !== "true")
    throw Error("Keyboard selection");
  await page.locator(".reset_variations").click();
  if (
    await page.locator('.spray-variation-option[aria-pressed="true"]').count()
  )
    throw Error("Reset failed");
  await buttons.last().click();
  await check("product");
  await page.locator("button.single_add_to_cart_button").click();
  await page.waitForTimeout(1200);
  await page.goto(base + "/cart/", { waitUntil: "load" });
  await page
    .getByText("Violeta", { exact: false })
    .first()
    .waitFor({ timeout: 30000 });
  console.log("CART", (await page.locator("main").innerText()).slice(0, 1400));
  if (!(await page.locator("main").innerText()).includes("Violeta"))
    throw Error("Cart missing chosen variation");
  await check("cart");
  await page.locator(".cart-toggle").click();
  await page.locator(".cart-drawer.open").waitFor();
  if (!(await page.locator(".cart-drawer").innerText()).includes("Violeta"))
    throw Error("Drawer color missing");
  await page.locator(".cart-close").click();
  await page.goto(base + "/checkout/", { waitUntil: "load" });
  await check("checkout");
  await page.goto(base + "/shop/", { waitUntil: "load" });
  await check("shop");
  await page.goto(base + "/wp-login.php", { waitUntil: "load" });
  if (await page.locator("#user_login").count()) {
    await page.locator("#user_login").fill("admin");
    await page.locator("#user_pass").fill("password");
    await Promise.all([
      page.waitForURL((u) => !u.pathname.includes("wp-login")),
      page.locator("#wp-submit").click(),
    ]);
  }
  await page.goto(base + "/wp-admin/customize.php", { waitUntil: "load" });
  await page.waitForFunction(() =>
    window.wp?.customize?.("spray_nova_content_order"),
  );
  console.log(
    "CUSTOMIZER",
    await page.evaluate(() =>
      [
        "spray_nova_content_order",
        "spray_nova_hero_media_width",
        "spray_nova_category_card_height",
        "spray_nova_products_columns",
      ].map((k) => ({ id: k, value: wp.customize(k).get() })),
    ),
  );
  const target = await page.evaluate(() => {
    const categoriesFirst =
      wp.customize("spray_nova_content_order").get() !== "categories_products";
    const mediaWidth =
      wp.customize("spray_nova_hero_media_width").get() === 70 ? 55 : 70;
    wp.customize("spray_nova_content_order").set(
      categoriesFirst ? "categories_products" : "products_categories",
    );
    wp.customize("spray_nova_hero_media_width").set(mediaWidth);
    wp.customize("spray_nova_products_columns").set(2);
    return {
      first: categoriesFirst ? "categorias" : "catalogo",
      ratio: mediaWidth + "fr",
    };
  });
  await page.locator("#save").click();
  await page.waitForFunction(() => wp.customize.state("saved").get() === true);
  await page.goto(base, { waitUntil: "load" });
  const settings = await page.evaluate(() => ({
    sections: [...document.querySelectorAll("main > section[id]")].map(
      (e) => e.id,
    ),
    columns: getComputedStyle(document.querySelector(".product-grid"))
      .gridTemplateColumns,
    ratio: getComputedStyle(document.documentElement).getPropertyValue(
      "--home-media",
    ),
  }));
  console.log("SAVED", settings);
  if (
    settings.sections[0] !== target.first ||
    settings.ratio.trim() !== target.ratio
  )
    throw Error("Settings not applied");
  await check("customized");
  fs.writeFileSync(
    "/tmp/spray-local-results.json",
    JSON.stringify({ results, errors }, null, 2),
  );
  console.log(
    "FAILURES",
    results.filter((r) => r.overflow || r.bad.length),
    errors,
  );
  await browser.close();
  if (results.some((r) => r.overflow || r.bad.length) || errors.length)
    process.exitCode = 1;
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
