const fs = require("fs");
const path = require("path");
const ExcelJS = require("exceljs");

const root = path.resolve(__dirname, "..");
const dataDir = path.join(root, "data");
const outFile = path.join(root, "spray-nova-catalogo-productos.xlsx");
const publicAssetBase = "https://spray-nova.vercel.app/";

function parseCsv(content) {
  const rows = [];
  let row = [];
  let value = "";
  let quoted = false;

  for (let index = 0; index < content.length; index += 1) {
    const char = content[index];
    const next = content[index + 1];

    if (char === '"' && quoted && next === '"') {
      value += '"';
      index += 1;
    } else if (char === '"') {
      quoted = !quoted;
    } else if (char === "," && !quoted) {
      row.push(value);
      value = "";
    } else if ((char === "\n" || char === "\r") && !quoted) {
      if (char === "\r" && next === "\n") index += 1;
      row.push(value);
      if (row.some((cell) => cell !== "")) rows.push(row);
      row = [];
      value = "";
    } else {
      value += char;
    }
  }

  if (value || row.length) {
    row.push(value);
    rows.push(row);
  }

  const [headers, ...body] = rows;
  return body.map((line) =>
    Object.fromEntries(headers.map((header, index) => [header, line[index] ?? ""])),
  );
}

function readCsv(file) {
  return parseCsv(fs.readFileSync(path.join(dataDir, file), "utf8"));
}

function csvEscape(value) {
  const stringValue = String(value ?? "");
  return /[",\n\r]/.test(stringValue) ? `"${stringValue.replace(/"/g, '""')}"` : stringValue;
}

function writeCsv(file, rows, headers) {
  const lines = [headers.join(",")].concat(
    rows.map((row) => headers.map((header) => csvEscape(row[header])).join(",")),
  );
  fs.writeFileSync(path.join(dataDir, file), `${lines.join("\n")}\n`);
}

function publicImageUrl(imageFile) {
  if (!imageFile) return "";
  return `${publicAssetBase}${String(imageFile).replace(/\\/g, "/").replace(/^\/+/, "")}`;
}

function familyFromHex(hex, fallback = "otros") {
  if (!hex || !/^#[0-9a-f]{6}$/i.test(hex)) return fallback;
  const r = parseInt(hex.slice(1, 3), 16) / 255;
  const g = parseInt(hex.slice(3, 5), 16) / 255;
  const b = parseInt(hex.slice(5, 7), 16) / 255;
  const max = Math.max(r, g, b);
  const min = Math.min(r, g, b);
  const light = (max + min) / 2;
  const delta = max - min;

  if (delta < 0.05) {
    if (light < 0.18) return "negros";
    if (light > 0.86) return "blancos";
    return "grises";
  }

  let hue;
  if (max === r) hue = ((g - b) / delta + (g < b ? 6 : 0)) * 60;
  else if (max === g) hue = ((b - r) / delta + 2) * 60;
  else hue = ((r - g) / delta + 4) * 60;

  if (hue < 15 || hue >= 345) return "rojos";
  if (hue < 45) return "naranjas";
  if (hue < 70) return "amarillos";
  if (hue < 165) return "verdes";
  if (hue < 245) return "azules";
  if (hue < 295) return "morados";
  if (hue < 345) return "rosas";
  return fallback;
}

const families = [
  "negros",
  "blancos",
  "grises",
  "rojos",
  "naranjas",
  "amarillos",
  "verdes",
  "azules",
  "morados",
  "marrones",
  "rosas",
  "otros",
];

const productLines = readCsv("spray-product-lines.csv");
const dopeClassic = readCsv("dope-classic-colors.csv");
const nbqEternal400 = readCsv("nbq-eternal-400-colors.csv");
const waxProducts = readCsv("ceras-productos.csv");

const colorSheets = [
  {
    sheet: "DOPE Classic 400",
    sku: "DOPE-CLASSIC-400",
    product: "DOPE Classic 400ml",
    colors: dopeClassic,
    price: "3.95",
  },
  {
    sheet: "NBQ Eternal 400",
    sku: "NBQ-ETERNAL-400",
    product: "NBQ Eternal 400ml",
    colors: nbqEternal400,
    price: "3.95",
  },
  {
    sheet: "NBQ Fast 400",
    sku: "NBQ-FAST-400",
    product: "NBQ Fast 400ml",
    colors: Array.from({ length: 46 }, (_, index) => ({
      code: `NBQ-F400-${String(index + 1).padStart(2, "0")}`,
      name: "",
      hex: "",
      family: "",
    })),
    price: "3.95",
  },
  {
    sheet: "NBQ Eternal 800",
    sku: "NBQ-ETERNAL-800",
    product: "NBQ Eternal 800",
    colors: Array.from({ length: 15 }, (_, index) => ({
      code: `NBQ-E800-${String(index + 1).padStart(2, "0")}`,
      name: "",
      hex: "",
      family: "",
    })),
    price: "",
  },
  {
    sheet: "DOPE Action 2",
    sku: "DOPE-ACTION-600",
    product: "DOPE Action 2.0 600ml",
    colors: Array.from({ length: 10 }, (_, index) => ({
      code: `DA2-${String(index + 1).padStart(2, "0")}`,
      name: "",
      hex: "",
      family: "",
    })),
    price: "",
  },
  {
    sheet: "Plata o Plomo 500",
    sku: "PLATA-O-PLOMO-500",
    product: "Plata o Plomo 500ml",
    colors: [
      { code: "POP-SILVER", name: "Plata", hex: "#B0B0B0", family: "grises" },
      { code: "POP-LEAD", name: "Plomo", hex: "#535C5B", family: "grises" },
    ],
    price: "",
  },
];

function getLineBySku(sku) {
  return productLines.find((line) => line.sku === sku) || {};
}

function productAttributeValues(colors) {
  return colors
    .filter((color) => color.name)
    .map((color) => `${color.code} ${color.name}`.trim())
    .join(", ");
}

function woocommerceRows() {
  const headers = [
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
    "Parent",
    "Meta: _spray_nova_brand",
    "Meta: _spray_nova_format",
    "Meta: _spray_nova_pressure",
    "Meta: _spray_nova_finish",
    "Meta: _spray_nova_color_hex",
    "Meta: _spray_nova_color_code",
    "Meta: _spray_nova_color_family",
  ];

  const rows = [];

  colorSheets.forEach((sheet) => {
    const line = getLineBySku(sheet.sku);
    const usableColors = sheet.colors.filter((color) => color.name);
    rows.push({
      Type: "variable",
      SKU: sheet.sku,
      Name: sheet.product,
      Published: "1",
      "Visibility in catalog": "visible",
      "Regular price": "",
      Categories: "Sprays",
      "In stock?": "1",
      "Manage stock?": "0",
      "Attribute 1 name": "Color",
      "Attribute 1 value(s)": productAttributeValues(usableColors),
      "Attribute 1 visible": "1",
      "Attribute 1 global": "0",
      Parent: "",
      "Meta: _spray_nova_brand": line.brand || "",
      "Meta: _spray_nova_format": line.format || "",
      "Meta: _spray_nova_pressure": line.pressure || "",
      "Meta: _spray_nova_finish": line.finish || "",
      "Meta: _spray_nova_color_hex": "",
      "Meta: _spray_nova_color_code": "",
      "Meta: _spray_nova_color_family": "",
    });

    usableColors.forEach((color) => {
      rows.push({
        Type: "variation",
        SKU: color.code,
        Name: `${sheet.product} - ${color.code} ${color.name}`,
        Published: "1",
        "Visibility in catalog": "visible",
        "Regular price": sheet.price || "",
        Categories: "Sprays",
        "In stock?": "1",
        "Manage stock?": "0",
        "Attribute 1 name": "Color",
        "Attribute 1 value(s)": `${color.code} ${color.name}`.trim(),
        "Attribute 1 visible": "1",
        "Attribute 1 global": "0",
        Parent: sheet.sku,
        "Meta: _spray_nova_brand": line.brand || "",
        "Meta: _spray_nova_format": line.format || "",
        "Meta: _spray_nova_pressure": line.pressure || "",
        "Meta: _spray_nova_finish": line.finish || "",
        "Meta: _spray_nova_color_hex": color.hex,
        "Meta: _spray_nova_color_code": color.code,
        "Meta: _spray_nova_color_family": color.family || familyFromHex(color.hex),
      });
    });
  });

  waxProducts.forEach((product) => {
    rows.push({
      Type: "simple",
      SKU: product.sku,
      Name: product.name,
      Published: "1",
      "Visibility in catalog": "visible",
      "Regular price": product.price || "",
      Categories: "Ceras",
      "In stock?": "1",
      "Manage stock?": "0",
      Images: publicImageUrl(product.image_file) || product.authorized_image_url || "",
      "Attribute 1 name": "Marca",
      "Attribute 1 value(s)": product.brand || "",
      "Attribute 1 visible": product.brand ? "1" : "0",
      "Attribute 1 global": "0",
      Parent: "",
      "Meta: _spray_nova_brand": product.brand || "",
      "Meta: _spray_nova_format": "",
      "Meta: _spray_nova_pressure": "",
      "Meta: _spray_nova_finish": "",
      "Meta: _spray_nova_color_hex": "",
      "Meta: _spray_nova_color_code": "",
      "Meta: _spray_nova_color_family": "",
    });
  });

  return { headers, rows };
}

function colorArgb(hex) {
  if (!hex || !/^#[0-9a-f]{6}$/i.test(hex)) return "FFFFFFFF";
  return `FF${hex.slice(1).toUpperCase()}`;
}

function styleHeader(row) {
  row.eachCell((cell) => {
    cell.font = { bold: true, color: { argb: "FFFFFFFF" } };
    cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF111111" } };
    cell.alignment = { vertical: "middle" };
    cell.border = {
      top: { style: "thin", color: { argb: "FF111111" } },
      left: { style: "thin", color: { argb: "FF111111" } },
      bottom: { style: "thin", color: { argb: "FF111111" } },
      right: { style: "thin", color: { argb: "FF111111" } },
    };
  });
}

function setAutoFilterAndFreeze(sheet) {
  sheet.views = [{ state: "frozen", ySplit: 1 }];
  sheet.autoFilter = {
    from: { row: 1, column: 1 },
    to: { row: 1, column: sheet.columnCount },
  };
}

function addValidation(sheet, columnLetter, fromRow, toRow, values) {
  for (let row = fromRow; row <= toRow; row += 1) {
    sheet.getCell(`${columnLetter}${row}`).dataValidation = {
      type: "list",
      allowBlank: true,
      formulae: [`"${values.join(",")}"`],
    };
  }
}

async function main() {
  const workbook = new ExcelJS.Workbook();
  workbook.creator = "Codex";
  workbook.created = new Date();

  const instructions = workbook.addWorksheet("LEEME");
  instructions.columns = [{ width: 110 }];
  [
    "SPRAY NOVA - Catálogo maestro",
    "",
    "Edita las hojas de colores. Las columnas importantes son: code, name, hex, family, price, sku, stock.",
    "El tema usa hex, code y family para pintar la carta visual de colores.",
    "Si no llevas stock, deja manage_stock en 0 y stock vacío.",
    "Para importar en WooCommerce exporta/usa la hoja WooCommerce Import como CSV.",
    "Precio actual: las latas de 400ml importadas están a 3.95. WooCommerce decide la moneda desde Ajustes > General.",
    "",
    "Productos incluidos:",
    ...productLines.map((line) => `- ${line.name}: ${line.color_count || "por confirmar"} colores, ${line.pressure}, ${line.finish}`),
  ].forEach((line, index) => {
    const cell = instructions.getCell(index + 1, 1);
    cell.value = line;
    if (index === 0) {
      cell.font = { bold: true, size: 18 };
      cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FFC6A0EB" } };
    }
  });

  const linesSheet = workbook.addWorksheet("Gamas");
  linesSheet.columns = [
    { header: "sku", key: "sku", width: 22 },
    { header: "name", key: "name", width: 28 },
    { header: "brand", key: "brand", width: 12 },
    { header: "format", key: "format", width: 12 },
    { header: "real_capacity", key: "real_capacity", width: 14 },
    { header: "color_count", key: "color_count", width: 12 },
    { header: "pressure", key: "pressure", width: 12 },
    { header: "finish", key: "finish", width: 18 },
    { header: "paint_type", key: "paint_type", width: 14 },
    { header: "product_type", key: "product_type", width: 14 },
    { header: "category", key: "category", width: 14 },
    { header: "notes", key: "notes", width: 36 },
  ];
  linesSheet.addRows(productLines);
  styleHeader(linesSheet.getRow(1));
  setAutoFilterAndFreeze(linesSheet);

  colorSheets.forEach((config) => {
    const sheet = workbook.addWorksheet(config.sheet);
    sheet.columns = [
      { header: "code", key: "code", width: 18 },
      { header: "name", key: "name", width: 26 },
      { header: "hex", key: "hex", width: 12 },
      { header: "swatch", key: "swatch", width: 11 },
      { header: "family", key: "family", width: 14 },
      { header: "price", key: "price", width: 10 },
      { header: "sku", key: "sku", width: 22 },
      { header: "manage_stock", key: "manage_stock", width: 14 },
      { header: "stock", key: "stock", width: 10 },
      { header: "published", key: "published", width: 10 },
      { header: "notes", key: "notes", width: 30 },
    ];

    config.colors.forEach((color) => {
      const family = color.family || familyFromHex(color.hex);
      sheet.addRow({
        code: color.code,
        name: color.name,
        hex: color.hex,
        swatch: "",
        family,
        price: config.price,
        sku: color.code,
        manage_stock: 0,
        stock: "",
        published: color.name ? 1 : 0,
        notes: color.name ? "" : "Completar color",
      });
    });

    styleHeader(sheet.getRow(1));
    setAutoFilterAndFreeze(sheet);
    addValidation(sheet, "E", 2, Math.max(sheet.rowCount, 80), families);
    addValidation(sheet, "H", 2, Math.max(sheet.rowCount, 80), ["0", "1"]);
    addValidation(sheet, "J", 2, Math.max(sheet.rowCount, 80), ["0", "1"]);

    for (let row = 2; row <= sheet.rowCount; row += 1) {
      const hex = sheet.getCell(row, 3).value;
      const swatch = sheet.getCell(row, 4);
      swatch.fill = { type: "pattern", pattern: "solid", fgColor: { argb: colorArgb(hex) } };
      swatch.border = {
        top: { style: "thin", color: { argb: "FF111111" } },
        left: { style: "thin", color: { argb: "FF111111" } },
        bottom: { style: "thin", color: { argb: "FF111111" } },
        right: { style: "thin", color: { argb: "FF111111" } },
      };
    }
  });

  const waxSheet = workbook.addWorksheet("Ceras");
  waxSheet.columns = [
    { header: "sku", key: "sku", width: 34 },
    { header: "name", key: "name", width: 34 },
    { header: "brand", key: "brand", width: 16 },
    { header: "price", key: "price", width: 10 },
    { header: "stock_status", key: "stock_status", width: 14 },
    { header: "manage_stock", key: "manage_stock", width: 14 },
    { header: "image_file", key: "image_file", width: 42 },
    { header: "authorized_image_url", key: "authorized_image_url", width: 62 },
    { header: "woo_import_image_url", key: "woo_import_image_url", width: 62 },
    { header: "source_url", key: "source_url", width: 56 },
    { header: "source_stock", key: "source_stock", width: 14 },
    { header: "notes", key: "notes", width: 34 },
  ];
  waxSheet.addRows(waxProducts.map((product) => ({
    ...product,
    woo_import_image_url: publicImageUrl(product.image_file) || product.authorized_image_url,
  })));
  styleHeader(waxSheet.getRow(1));
  setAutoFilterAndFreeze(waxSheet);
  addValidation(waxSheet, "E", 2, Math.max(waxSheet.rowCount, 80), ["instock", "outofstock"]);
  addValidation(waxSheet, "F", 2, Math.max(waxSheet.rowCount, 80), ["0", "1"]);

  const { headers, rows } = woocommerceRows();
  const importSheet = workbook.addWorksheet("WooCommerce Import");
  importSheet.columns = headers.map((header) => ({ header, key: header, width: Math.min(Math.max(header.length + 2, 14), 34) }));
  importSheet.addRows(rows);
  styleHeader(importSheet.getRow(1));
  setAutoFilterAndFreeze(importSheet);

  workbook.eachSheet((sheet) => {
    sheet.eachRow((row) => {
      row.eachCell((cell) => {
        cell.alignment = { vertical: "middle", wrapText: false };
      });
    });
  });

  await workbook.xlsx.writeFile(outFile);

  writeCsv("woocommerce-spray-master-import.csv", rows, headers);
  [
    ["DOPE-CLASSIC-400", "woocommerce-dope-classic-400ml-import.csv"],
    ["NBQ-ETERNAL-400", "woocommerce-nbq-eternal-400-import.csv"],
  ].forEach(([sku, file]) => {
    writeCsv(file, rows.filter((row) => row.SKU === sku || row.Parent === sku), headers);
  });
  console.log(`Created ${outFile}`);
  console.log(`WooCommerce rows: ${rows.length}`);
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
