const { chromium } = require("playwright");
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage();
  const errors = [];
  for (const route of ["contacto", "aviso-legal"]) {
    const response = await p.goto("http://127.0.0.1:9400/" + route + "/");
    if (response.status() !== 200) throw Error(route + " " + response.status());
    for (const width of [320, 375, 390, 768, 1024, 1440, 1920]) {
      await p.setViewportSize({ width, height: 900 });
      await p.waitForTimeout(200);
      if (
        await p.evaluate(
          () => document.documentElement.scrollWidth > innerWidth + 1,
        )
      )
        errors.push({ route, width });
    }
    if (route === "contacto") {
      await p.setViewportSize({ width: 390, height: 900 });
      await p.screenshot({
        path: "/tmp/spray-local-contact-390.png",
        fullPage: true,
      });
    }
  }
  console.log(errors);
  await b.close();
  if (errors.length) process.exitCode = 1;
})().catch((e) => {
  console.error(e);
  process.exit(1);
});
