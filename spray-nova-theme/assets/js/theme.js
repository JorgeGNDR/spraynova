(function ($) {
  "use strict";

  const body = document.body;
  const cartDrawer = document.querySelector(".cart-drawer");
  const backdrop = document.querySelector(".drawer-backdrop");
  const searchPanel = document.querySelector(".search-panel");
  const searchInput = document.querySelector("#site-search");
  const menuToggle = document.querySelector(".menu-toggle");
  const desktopNav = document.querySelector(".desktop-nav");
  const filterButtons = [...document.querySelectorAll(".product-filters button")];
  const productCards = [...document.querySelectorAll("#catalogo .product-card")];
  const emptyResults = document.querySelector("#catalogo .empty-results");

  function openCart() {
    if (!cartDrawer || !backdrop) return;
    cartDrawer.classList.add("open");
    backdrop.classList.add("open");
    body.classList.add("drawer-open");
  }

  function closeCart() {
    if (!cartDrawer || !backdrop) return;
    cartDrawer.classList.remove("open");
    backdrop.classList.remove("open");
    body.classList.remove("drawer-open");
  }

  function closeMenu() {
    if (!desktopNav || !menuToggle) return;
    desktopNav.classList.remove("open");
    menuToggle.classList.remove("open");
    menuToggle.setAttribute("aria-expanded", "false");
  }

  function setFilter(filter) {
    let visibleProducts = 0;

    filterButtons.forEach((button) => {
      button.classList.toggle("active", button.dataset.filter === filter);
    });

    productCards.forEach((card, index) => {
      const categories = (card.dataset.categories || "").split(" ");
      const visible = filter === "todos" ? index < 4 : categories.includes(filter);
      card.classList.toggle("hidden", !visible);
      if (visible) visibleProducts += 1;
    });

    if (emptyResults) {
      emptyResults.style.display = visibleProducts ? "none" : "block";
    }
  }

  document.addEventListener("click", (event) => {
    if (event.target.closest(".cart-toggle")) openCart();
    if (event.target.closest(".cart-close") || event.target === backdrop) closeCart();

    if (event.target.closest(".search-toggle") && searchPanel) {
      searchPanel.classList.toggle("open");
      if (searchPanel.classList.contains("open") && searchInput) searchInput.focus();
    }

    if (event.target.closest(".search-close") && searchPanel) {
      searchPanel.classList.remove("open");
    }
  });

  if (menuToggle && desktopNav) {
    menuToggle.addEventListener("click", () => {
      const isOpen = desktopNav.classList.toggle("open");
      menuToggle.classList.toggle("open", isOpen);
      menuToggle.setAttribute("aria-expanded", String(isOpen));
    });
    desktopNav.addEventListener("click", closeMenu);
  }

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => setFilter(button.dataset.filter));
  });

  const newsletter = document.querySelector(".newsletter-form");
  if (newsletter) {
    newsletter.addEventListener("submit", (event) => {
      event.preventDefault();
      const message = document.querySelector(".newsletter-message");
      if (message) message.textContent = "Gracias. Ya estás dentro.";
      newsletter.reset();
    });
  }

  $(document.body).on("added_to_cart", openCart);

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;
    closeCart();
    closeMenu();
    if (searchPanel) searchPanel.classList.remove("open");
  });

  if (productCards.length) setFilter("todos");
})(jQuery);
