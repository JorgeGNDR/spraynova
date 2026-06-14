const cart = [];

const cartDrawer = document.querySelector(".cart-drawer");
const backdrop = document.querySelector(".drawer-backdrop");
const cartItems = document.querySelector(".cart-items");
const cartCount = document.querySelector(".cart-count");
const drawerCount = document.querySelector(".drawer-count");
const cartTotal = document.querySelector(".cart-total");
const searchPanel = document.querySelector(".search-panel");
const searchInput = document.querySelector("#site-search");
const productCards = [...document.querySelectorAll(".product-card")];
const filterButtons = [...document.querySelectorAll(".product-filters button")];
const menuToggle = document.querySelector(".menu-toggle");
const desktopNav = document.querySelector(".desktop-nav");

function formatPrice(value) {
  return `${value.toFixed(2).replace(".", ",")} €`;
}

function openCart() {
  cartDrawer.classList.add("open");
  backdrop.classList.add("open");
  document.body.classList.add("drawer-open");
}

function closeCart() {
  cartDrawer.classList.remove("open");
  backdrop.classList.remove("open");
  document.body.classList.remove("drawer-open");
}

function renderCart() {
  cartCount.textContent = cart.length;
  drawerCount.textContent = `(${cart.length})`;
  cartTotal.textContent = formatPrice(cart.reduce((sum, item) => sum + item.price, 0));

  if (!cart.length) {
    cartItems.innerHTML = `
      <div class="empty-cart">
        <span>0</span>
        <h3>Tu carrito está vacío</h3>
        <p>Añade un poco de color.</p>
      </div>
    `;
    return;
  }

  cartItems.innerHTML = cart
    .map(
      (item, index) => `
        <div class="cart-line">
          <div class="cart-thumb">NOVA</div>
          <div>
            <h3>${item.name}</h3>
            <p>${formatPrice(item.price)}</p>
          </div>
          <button class="remove-item" data-index="${index}" aria-label="Eliminar ${item.name}">×</button>
        </div>
      `,
    )
    .join("");
}

function setFilter(filter) {
  let visibleProducts = 0;

  filterButtons.forEach((button) => {
    button.classList.toggle("active", button.dataset.filter === filter);
  });

  productCards.forEach((card) => {
    const visible = filter === "todos" || card.dataset.category === filter;
    card.classList.toggle("hidden", !visible);
    if (visible) visibleProducts += 1;
  });

  document.querySelector(".empty-results").style.display = visibleProducts ? "none" : "block";
}

document.querySelector(".cart-toggle").addEventListener("click", openCart);
document.querySelector(".cart-close").addEventListener("click", closeCart);
backdrop.addEventListener("click", closeCart);

menuToggle.addEventListener("click", () => {
  const isOpen = desktopNav.classList.toggle("open");
  menuToggle.classList.toggle("open", isOpen);
  menuToggle.setAttribute("aria-expanded", String(isOpen));
});

desktopNav.addEventListener("click", () => {
  desktopNav.classList.remove("open");
  menuToggle.classList.remove("open");
  menuToggle.setAttribute("aria-expanded", "false");
});

document.querySelectorAll(".quick-add").forEach((button) => {
  button.addEventListener("click", () => {
    cart.push({
      name: button.dataset.product,
      price: Number(button.dataset.price),
    });
    renderCart();
    openCart();
  });
});

cartItems.addEventListener("click", (event) => {
  const removeButton = event.target.closest(".remove-item");
  if (!removeButton) return;
  cart.splice(Number(removeButton.dataset.index), 1);
  renderCart();
});

filterButtons.forEach((button) => {
  button.addEventListener("click", () => setFilter(button.dataset.filter));
});

document.querySelectorAll(".category-card").forEach((card) => {
  card.addEventListener("click", () => {
    setFilter(card.dataset.filter);
    document.querySelector("#catalogo").scrollIntoView({ behavior: "smooth" });
  });
});

document.querySelector(".search-toggle").addEventListener("click", () => {
  searchPanel.classList.toggle("open");
  if (searchPanel.classList.contains("open")) searchInput.focus();
});

document.querySelector(".search-close").addEventListener("click", () => {
  searchPanel.classList.remove("open");
  searchInput.value = "";
  setFilter("todos");
});

searchInput.addEventListener("input", () => {
  const query = searchInput.value.trim().toLocaleLowerCase("es");
  let visibleProducts = 0;

  filterButtons.forEach((button) => button.classList.remove("active"));
  productCards.forEach((card) => {
    const visible = card.dataset.name.toLocaleLowerCase("es").includes(query);
    card.classList.toggle("hidden", !visible);
    if (visible) visibleProducts += 1;
  });

  document.querySelector(".empty-results").style.display = visibleProducts ? "none" : "block";
});

document.querySelector(".newsletter-form").addEventListener("submit", (event) => {
  event.preventDefault();
  const message = document.querySelector(".newsletter-message");
  message.textContent = "Gracias. Ya estás dentro.";
  event.currentTarget.reset();
});

document.addEventListener("keydown", (event) => {
  if (event.key !== "Escape") return;
  closeCart();
  searchPanel.classList.remove("open");
  desktopNav.classList.remove("open");
  menuToggle.classList.remove("open");
  menuToggle.setAttribute("aria-expanded", "false");
});

renderCart();
