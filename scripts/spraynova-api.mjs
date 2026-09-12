#!/usr/bin/env node

import { readFile } from "node:fs/promises";
import { basename, extname, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const root = resolve(fileURLToPath(new URL("..", import.meta.url)));

async function loadEnv() {
  const path = resolve(root, ".env.spraynova");
  let content;
  try {
    content = await readFile(path, "utf8");
  } catch {
    throw new Error(`Falta ${path}. Copia .env.spraynova.example y añade las credenciales.`);
  }

  for (const rawLine of content.split(/\r?\n/)) {
    const line = rawLine.trim();
    if (!line || line.startsWith("#")) continue;
    const separator = line.indexOf("=");
    if (separator < 1) continue;
    const key = line.slice(0, separator).trim();
    let value = line.slice(separator + 1).trim();
    if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
      value = value.slice(1, -1);
    }
    if (!(key in process.env)) process.env[key] = value;
  }
}

function required(name) {
  const value = process.env[name]?.trim();
  if (!value) throw new Error(`Falta ${name} en .env.spraynova.`);
  return value;
}

function siteUrl() {
  const url = new URL(required("SPRAYNOVA_URL"));
  if (url.protocol !== "https:") throw new Error("SPRAYNOVA_URL debe usar HTTPS.");
  return url.origin;
}

function basicAuth(user, password) {
  return `Basic ${Buffer.from(`${user}:${password}`).toString("base64")}`;
}

async function apiRequest(url, options = {}) {
  const response = await fetch(url, options);
  const text = await response.text();
  let body = null;
  if (text) {
    try {
      body = JSON.parse(text);
    } catch {
      body = text;
    }
  }

  if (!response.ok) {
    const message = body?.message || (typeof body === "string" ? body.slice(0, 300) : "Error sin detalle");
    throw new Error(`HTTP ${response.status}: ${message}`);
  }
  return body;
}

async function wcRequest(path, method = "GET", payload) {
  const key = required("SPRAYNOVA_WC_KEY");
  const secret = required("SPRAYNOVA_WC_SECRET");
  const url = new URL(`/wp-json/wc/v3/${path.replace(/^\//, "")}`, siteUrl());
  return apiRequest(url, {
    method,
    headers: {
      Authorization: basicAuth(key, secret),
      ...(payload ? { "Content-Type": "application/json" } : {}),
    },
    body: payload ? JSON.stringify(payload) : undefined,
  });
}

async function wpRequest(path, method = "GET", payload) {
  const user = required("SPRAYNOVA_WP_USER");
  const password = required("SPRAYNOVA_WP_APP_PASSWORD").replace(/\s+/g, "");
  const url = new URL(`/wp-json/wp/v2/${path.replace(/^\//, "")}`, siteUrl());
  return apiRequest(url, {
    method,
    headers: {
      Authorization: basicAuth(user, password),
      ...(payload ? { "Content-Type": "application/json" } : {}),
    },
    body: payload ? JSON.stringify(payload) : undefined,
  });
}

function print(value) {
  process.stdout.write(`${JSON.stringify(value, null, 2)}\n`);
}

function productSummary(product) {
  return {
    id: product.id,
    name: product.name,
    sku: product.sku,
    type: product.type,
    status: product.status,
    price: product.price,
    stock_status: product.stock_status,
    permalink: product.permalink,
  };
}

async function status() {
  const index = await apiRequest(new URL("/wp-json/", siteUrl()));
  const result = {
    site: siteUrl(),
    wordpress: index?.name || "disponible",
    woocommerce: "sin comprobar",
    media: "sin comprobar",
  };

  try {
    const products = await wcRequest("products?per_page=1");
    result.woocommerce = `conectado (${products.length} producto consultado)`;
  } catch (error) {
    result.woocommerce = error.message;
  }

  try {
    const me = await wpRequest("users/me?context=edit");
    result.media = `conectado como ${me.name || me.slug || me.id}`;
  } catch (error) {
    result.media = error.message;
  }

  print(result);
}

async function listProducts(search = "") {
  const query = new URLSearchParams({ per_page: "100" });
  if (search) query.set("search", search);
  const products = await wcRequest(`products?${query}`);
  print(products.map(productSummary));
}

async function getProduct(id) {
  print(await wcRequest(`products/${Number(id)}`));
}

async function listVariations(productId) {
  const variations = await wcRequest(`products/${Number(productId)}/variations?per_page=100`);
  print(variations.map((variation) => ({
    id: variation.id,
    sku: variation.sku,
    price: variation.price,
    stock_status: variation.stock_status,
    attributes: variation.attributes,
    color_meta: variation.meta_data?.filter((item) => item.key.startsWith("_spray_nova_color_")),
  })));
}

async function updateProduct(id, jsonPath) {
  const payload = JSON.parse(await readFile(resolve(jsonPath), "utf8"));
  const updated = await wcRequest(`products/${Number(id)}`, "PUT", payload);
  print(productSummary(updated));
}

async function setVariationColor(productId, variationId, hex, family = "") {
  if (!/^#[0-9a-f]{6}$/i.test(hex)) throw new Error("El HEX debe tener el formato #RRGGBB.");
  const meta = [{ key: "_spray_nova_color_hex", value: hex.toUpperCase() }];
  if (family) meta.push({ key: "_spray_nova_color_family", value: family });
  const updated = await wcRequest(`products/${Number(productId)}/variations/${Number(variationId)}`, "PUT", { meta_data: meta });
  print({
    id: updated.id,
    sku: updated.sku,
    color_meta: updated.meta_data?.filter((item) => item.key.startsWith("_spray_nova_color_")),
  });
}

async function uploadMedia(filePath, altText = "") {
  const path = resolve(filePath);
  const bytes = await readFile(path);
  const mimeTypes = {
    ".jpg": "image/jpeg",
    ".jpeg": "image/jpeg",
    ".png": "image/png",
    ".webp": "image/webp",
  };
  const mime = mimeTypes[extname(path).toLowerCase()];
  if (!mime) throw new Error("Formato admitido: JPG, PNG o WebP.");

  const user = required("SPRAYNOVA_WP_USER");
  const password = required("SPRAYNOVA_WP_APP_PASSWORD").replace(/\s+/g, "");
  const media = await apiRequest(new URL("/wp-json/wp/v2/media", siteUrl()), {
    method: "POST",
    headers: {
      Authorization: basicAuth(user, password),
      "Content-Type": mime,
      "Content-Disposition": `attachment; filename="${basename(path).replace(/["\\]/g, "-")}"`,
    },
    body: bytes,
  });

  if (altText) await wpRequest(`media/${media.id}`, "POST", { alt_text: altText });
  print({ id: media.id, source_url: media.source_url, alt_text: altText });
}

function help() {
  console.log(`Uso:
  node scripts/spraynova-api.mjs status
  node scripts/spraynova-api.mjs products [busqueda]
  node scripts/spraynova-api.mjs product <id>
  node scripts/spraynova-api.mjs variations <id-producto>
  node scripts/spraynova-api.mjs update-product <id> <archivo.json>
  node scripts/spraynova-api.mjs set-color <id-producto> <id-variacion> <#RRGGBB> [familia]
  node scripts/spraynova-api.mjs upload-media <archivo> [texto-alt]`);
}

const [command, ...args] = process.argv.slice(2);

try {
  if (!command || command === "help" || command === "--help") {
    help();
    process.exit(0);
  }

  await loadEnv();
  if (command === "status") await status();
  else if (command === "products") await listProducts(args.join(" "));
  else if (command === "product" && args[0]) await getProduct(args[0]);
  else if (command === "variations" && args[0]) await listVariations(args[0]);
  else if (command === "update-product" && args[0] && args[1]) await updateProduct(args[0], args[1]);
  else if (command === "set-color" && args[0] && args[1] && args[2]) await setVariationColor(...args);
  else if (command === "upload-media" && args[0]) await uploadMedia(args[0], args.slice(1).join(" "));
  else help();
} catch (error) {
  console.error(error.message);
  process.exitCode = 1;
}
