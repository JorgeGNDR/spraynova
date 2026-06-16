const fs = require("fs");
const path = require("path");

const root = path.resolve(__dirname, "..");
const dataDir = path.join(root, "data");
const imageDir = path.join(dataDir, "images", "ceras");

const urls = [
  "https://iborgraffitishop.com/categoria-producto/rotuladores/ceras-de-colores/",
  "https://iborgraffitishop.com/categoria-producto/rotuladores/ceras-de-colores/page/2/",
  "https://iborgraffitishop.com/categoria-producto/rotuladores/ceras-de-colores/page/3/",
];

function htmlDecode(value) {
  return String(value || "")
    .replace(/&nbsp;/g, " ")
    .replace(/&amp;/g, "&")
    .replace(/&euro;/g, "€")
    .replace(/&#8211;/g, "-")
    .replace(/&#8217;/g, "'")
    .replace(/&#038;/g, "&")
    .replace(/<[^>]+>/g, "")
    .trim();
}

function slugify(value) {
  return String(value || "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toUpperCase()
    .replace(/[^A-Z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

function csvEscape(value) {
  const stringValue = String(value ?? "");
  return /[",\n\r]/.test(stringValue) ? `"${stringValue.replace(/"/g, '""')}"` : stringValue;
}

function writeCsv(file, rows, headers) {
  const output = [headers.join(",")].concat(
    rows.map((row) => headers.map((header) => csvEscape(row[header])).join(",")),
  );
  fs.writeFileSync(path.join(dataDir, file), `${output.join("\n")}\n`);
}

function bestImageFromCard(card) {
  const srcset = card.match(/data-lazy-srcset="([^"]+)"/)?.[1] || card.match(/srcset="([^"]+)"/)?.[1] || "";
  const candidates = srcset
    .split(",")
    .map((item) => item.trim().split(/\s+/)[0])
    .filter(Boolean);

  return (
    candidates.find((url) => url.includes("650x650")) ||
    candidates.find((url) => url.includes("300x300")) ||
    candidates[0] ||
    card.match(/data-lazy-src="([^"]+)"/)?.[1] ||
    card.match(/src="([^"]+)"/)?.[1] ||
    ""
  );
}

function priceFromCard(card) {
  const priceBlock = card.match(/<span class="price">([\s\S]*?)<\/span>\s*<\/span>/)?.[0] || "";
  const decoded = htmlDecode(priceBlock);
  const match = decoded.match(/(\d+[,.]\d{2})/);
  return match ? match[1].replace(",", ".") : "";
}

async function downloadImage(url, filename) {
  if (!url) return "";
  fs.mkdirSync(imageDir, { recursive: true });
  const extension = path.extname(new URL(url).pathname).split("?")[0] || ".png";
  const localName = `${filename}${extension}`;
  const localPath = path.join(imageDir, localName);

  if (!fs.existsSync(localPath)) {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Could not download ${url}: ${response.status}`);
    const bytes = Buffer.from(await response.arrayBuffer());
    fs.writeFileSync(localPath, bytes);
  }

  return path.relative(root, localPath).replace(/\\/g, "/");
}

async function main() {
  const products = [];

  for (const [pageIndex, url] of urls.entries()) {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Could not fetch ${url}: ${response.status}`);
    const html = await response.text();
    const cards = html.match(/<li[^>]*class="[^"]*product[^"]*"[\s\S]*?<\/li>/g) || [];

    for (const card of cards) {
      const title = htmlDecode(card.match(/<h2 class="woocommerce-loop-product__title">([\s\S]*?)<\/h2>/)?.[1]);
      const link = card.match(/<a href="([^"]+)" class="woocommerce-LoopProduct-link/)?.[1] || "";
      const imageUrl = bestImageFromCard(card);
      const sku = `CERA-${slugify(title)}`;
      const sourceStock = card.includes("outofstock") ? "outofstock" : "instock";
      const price = priceFromCard(card);
      const localImage = await downloadImage(imageUrl, sku);

      if (!title || products.some((product) => product.sku === sku)) continue;

      products.push({
        sku,
        name: title,
        type: "simple",
        categories: "Ceras",
        brand: title.startsWith("Hand Mixed") ? "Hand Mixed" : "",
        price,
        stock_status: "instock",
        manage_stock: "0",
        image_file: localImage,
        authorized_image_url: imageUrl,
        source_url: link,
        source_stock: sourceStock,
        source_page: String(pageIndex + 1),
        notes: "Imagen autorizada por proveedor",
      });
    }
  }

  products.sort((a, b) => a.name.localeCompare(b.name, "es"));

  writeCsv("ceras-productos.csv", products, [
    "sku",
    "name",
    "type",
    "categories",
    "brand",
    "price",
    "stock_status",
    "manage_stock",
    "image_file",
    "authorized_image_url",
    "source_url",
    "source_stock",
    "source_page",
    "notes",
  ]);

  writeCsv(
    "woocommerce-ceras-import.csv",
    products.map((product) => ({
      Type: "simple",
      SKU: product.sku,
      Name: product.name,
      Published: "1",
      "Visibility in catalog": "visible",
      "Regular price": product.price,
      Categories: "Ceras",
      "In stock?": "1",
      "Manage stock?": "0",
      Images: `https://spray-nova.vercel.app/${product.image_file.replace(/\\/g, "/")}`,
      "Attribute 1 name": "Marca",
      "Attribute 1 value(s)": product.brand,
      "Attribute 1 visible": product.brand ? "1" : "0",
      "Attribute 1 global": "0",
    })),
    [
      "Type",
      "SKU",
      "Name",
      "Published",
      "Visibility in catalog",
      "Regular price",
      "Categories",
      "In stock?",
      "Manage stock?",
      "Images",
      "Attribute 1 name",
      "Attribute 1 value(s)",
      "Attribute 1 visible",
      "Attribute 1 global",
    ],
  );

  console.log(`Extracted ${products.length} wax products`);
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
