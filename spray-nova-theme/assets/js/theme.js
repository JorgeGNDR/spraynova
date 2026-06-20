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
  const spraySelector = document.querySelector(".spray-color-selector");

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

  function formatMoney(value) {
    return new Intl.NumberFormat("es-ES", {
      style: "currency",
      currency: "EUR",
    }).format(value);
  }

  function refreshFragments(fragments) {
    if (!fragments) return;

    Object.entries(fragments).forEach(([selector, html]) => {
      document.querySelectorAll(selector).forEach((element) => {
        element.outerHTML = html;
      });
    });
  }

  function initSpraySelector() {
    if (!spraySelector) return;

    const cards = [...spraySelector.querySelectorAll(".spray-color-card")];
    const search = spraySelector.querySelector(".spray-color-search input");
    const familyButtons = [...spraySelector.querySelectorAll(".spray-family-filters button")];
    const addButton = spraySelector.querySelector(".spray-add-pack");
    const countLabel = spraySelector.querySelector(".spray-selected-count");
    const totalLabel = spraySelector.querySelector(".spray-selected-total");
    const message = spraySelector.querySelector(".spray-selector-message");
    const empty = spraySelector.querySelector(".spray-color-empty");
    let activeFamily = "todos";

    function selectedItems() {
      return cards
        .map((card) => {
          const input = card.querySelector("input");
          return {
            variation_id: Number(card.dataset.variationId),
            quantity: Math.max(0, Number(input.value || 0)),
            price: Number(card.dataset.price || 0),
          };
        })
        .filter((item) => item.quantity > 0);
    }

    function updateSummary() {
      const items = selectedItems();
      const totalQuantity = items.reduce((sum, item) => sum + item.quantity, 0);
      const totalPrice = items.reduce((sum, item) => sum + item.quantity * item.price, 0);

      if (countLabel) {
        countLabel.textContent = `${totalQuantity} ${
          totalQuantity === 1 ? "lata seleccionada" : "latas seleccionadas"
        }`;
      }
      if (totalLabel) totalLabel.textContent = formatMoney(totalPrice);
      if (addButton) addButton.disabled = totalQuantity === 0;
    }

    function applySprayFilters() {
      const query = (search?.value || "").trim().toLocaleLowerCase("es");
      let visible = 0;

      cards.forEach((card) => {
        const familyMatches = activeFamily === "todos" || card.dataset.family === activeFamily;
        const textMatches = !query || (card.dataset.label || "").includes(query);
        const show = familyMatches && textMatches;
        card.classList.toggle("hidden", !show);
        if (show) visible += 1;
      });

      if (empty) empty.style.display = visible ? "none" : "block";
    }

    cards.forEach((card) => {
      const input = card.querySelector("input");
      const minus = card.querySelector(".spray-qty-minus");
      const plus = card.querySelector(".spray-qty-plus");
      const swatch = card.querySelector(".spray-swatch");

      function setQuantity(value) {
        input.value = Math.max(0, Number(value || 0));
        card.classList.toggle("is-selected", Number(input.value) > 0);
        updateSummary();
      }

      minus?.addEventListener("click", () => setQuantity(Number(input.value || 0) - 1));
      plus?.addEventListener("click", () => setQuantity(Number(input.value || 0) + 1));
      swatch?.addEventListener("click", () => setQuantity(Number(input.value || 0) + 1));
      input?.addEventListener("input", () => setQuantity(input.value));
    });

    search?.addEventListener("input", applySprayFilters);
    familyButtons.forEach((button) => {
      button.addEventListener("click", () => {
        activeFamily = button.dataset.family;
        familyButtons.forEach((item) => item.classList.toggle("active", item === button));
        applySprayFilters();
      });
    });

    addButton?.addEventListener("click", () => {
      const items = selectedItems().map((item) => ({
        variation_id: item.variation_id,
        quantity: item.quantity,
      }));

      if (!items.length || !window.sprayNova?.ajaxUrl) return;

      addButton.disabled = true;
      addButton.classList.add("is-loading");
      if (message) message.textContent = "Añadiendo selección...";

      $.post(window.sprayNova.ajaxUrl, {
        action: "spray_nova_add_spray_pack",
        nonce: window.sprayNova.nonce,
        product_id: spraySelector.dataset.productId,
        items: JSON.stringify(items),
      })
        .done((response) => {
          if (!response?.success) {
            throw new Error(response?.data?.message || "No se pudo añadir la selección.");
          }

          refreshFragments(response.data.fragments);

          cards.forEach((card) => {
            const input = card.querySelector("input");
            input.value = 0;
            card.classList.remove("is-selected");
          });
          updateSummary();
          if (message) message.textContent = response.data.message;
          $(document.body).trigger("added_to_cart", [
            response.data.fragments || {},
            response.data.cart_hash || "",
            addButton,
          ]);
          openCart();
        })
        .fail((xhr) => {
          const error = xhr.responseJSON?.data?.message || "No se pudo añadir la selección.";
          if (message) message.textContent = error;
        })
        .always(() => {
          addButton.classList.remove("is-loading");
          updateSummary();
        });
    });

    applySprayFilters();
    updateSummary();
  }

  function colorForLabel(label) {
    const value = label.toLocaleLowerCase("es");
    const colors = [
      [["negro", "black"], "#171717"],
      [["blanco", "white"], "#f8f6f0"],
      [["gris", "grey", "gray"], "#969696"],
      [["plata", "silver"], "#b7b9bc"],
      [["oro", "gold"], "#c49a45"],
      [["amarillo", "yellow"], "#f2cf35"],
      [["naranja", "orange"], "#ef7d22"],
      [["rojo", "red"], "#d33b35"],
      [["rosa", "pink", "fucsia"], "#e977b3"],
      [["morado", "purple", "violeta", "violet", "lila"], "#9f71c8"],
      [["azul", "blue", "cyan"], "#3c7dcb"],
      [["verde", "green", "lime", "oliva"], "#559b55"],
      [["marron", "marrón", "brown", "beige", "arena"], "#956b49"],
    ];
    return colors.find(([names]) => names.some((name) => value.includes(name)))?.[1] || "#c6a0eb";
  }

  function variationHex(form, attributeName, value, label) {
    const variations = $(form).data("product_variations") || [];
    const match = variations.find((variation) => {
      const attributes = variation.attributes || {};
      return attributes[attributeName] === value || (!attributes[attributeName] && value);
    });
    return match?.spray_nova_color_hex || colorForLabel(label);
  }

  function initVariableProductSelectors() {
    document.querySelectorAll("form.variations_form").forEach((form) => {
      if (form.closest(".spray-color-selector") || form.dataset.sprayNovaReady) return;
      form.dataset.sprayNovaReady = "true";

      form.querySelectorAll("table.variations select").forEach((select) => {
        const options = [...select.options].filter((option) => option.value);
        if (!options.length) return;

        const picker = document.createElement("div");
        picker.className = "spray-variation-options";
        picker.setAttribute("role", "listbox");
        picker.setAttribute("aria-label", select.getAttribute("aria-label") || "Elige una opción");

        options.forEach((option) => {
          const button = document.createElement("button");
          const attributeName = select.name;
          button.type = "button";
          button.className = "spray-variation-option";
          button.dataset.value = option.value;
          button.setAttribute("role", "option");
          button.setAttribute("aria-selected", "false");
          button.innerHTML = `<span style="--variation-color:${variationHex(form, attributeName, option.value, option.textContent)}"></span><strong></strong>`;
          button.querySelector("strong").textContent = option.textContent;
          button.addEventListener("click", () => {
            select.value = option.value;
            $(select).trigger("change");
          });
          picker.append(button);
        });

        select.insertAdjacentElement("afterend", picker);

        const sync = () => {
          picker.querySelectorAll(".spray-variation-option").forEach((button) => {
            const selected = button.dataset.value === select.value;
            button.classList.toggle("is-selected", selected);
            button.setAttribute("aria-selected", String(selected));
          });
        };
        select.addEventListener("change", sync);
        sync();
      });

      $(form).on("reset_data", () => {
        form.querySelectorAll(".spray-variation-option").forEach((button) => {
          button.classList.remove("is-selected");
          button.setAttribute("aria-selected", "false");
        });
      });
    });
  }

  function initProductCart() {
    const forms = [...document.querySelectorAll("form.cart")].filter(
      (form) => !form.closest(".spray-color-selector"),
    );

    forms.forEach((form) => {
      const quantity = form.querySelector(".quantity");
      const input = form.querySelector('input[name="quantity"]');

      if (quantity && input && !quantity.querySelector(".spray-simple-qty-minus")) {
        quantity.classList.add("spray-simple-qty");

        const minus = document.createElement("button");
        minus.type = "button";
        minus.className = "spray-simple-qty-minus";
        minus.textContent = "-";

        const plus = document.createElement("button");
        plus.type = "button";
        plus.className = "spray-simple-qty-plus";
        plus.textContent = "+";

        quantity.prepend(minus);
        quantity.append(plus);

        const min = Number(input.getAttribute("min") || 1);
        const max = input.getAttribute("max") ? Number(input.getAttribute("max")) : null;

        function setSimpleQuantity(value) {
          let nextValue = Math.max(min, Number(value || min));
          if (max) nextValue = Math.min(max, nextValue);
          input.value = nextValue;
          input.dispatchEvent(new Event("change", { bubbles: true }));
        }

        minus.addEventListener("click", () => setSimpleQuantity(Number(input.value || min) - 1));
        plus.addEventListener("click", () => setSimpleQuantity(Number(input.value || min) + 1));
        input.addEventListener("input", () => setSimpleQuantity(input.value));
      }

      if (form.classList.contains("variations_form")) return;

      form.addEventListener("submit", (event) => {
        const submitButton = form.querySelector('[type="submit"][name="add-to-cart"]');
        const productId = Number(submitButton?.value || form.querySelector('[name="add-to-cart"]')?.value || 0);
        const quantity = Number(form.querySelector('input[name="quantity"]')?.value || 1);

        if (!productId || !window.sprayNova?.ajaxUrl) return;

        event.preventDefault();
        submitButton?.classList.add("is-loading");
        if (submitButton) submitButton.disabled = true;

        $.post(window.sprayNova.ajaxUrl, {
          action: "spray_nova_add_simple_product",
          nonce: window.sprayNova.nonce,
          product_id: productId,
          quantity,
        })
          .done((response) => {
            if (!response?.success) {
              throw new Error(response?.data?.message || "No se pudo añadir al carrito.");
            }
            refreshFragments(response.data.fragments);
            $(document.body).trigger("added_to_cart", [
              response.data.fragments || {},
              response.data.cart_hash || "",
              submitButton,
            ]);
            openCart();
          })
          .fail((xhr) => {
            const error = xhr.responseJSON?.data?.message || "No se pudo añadir al carrito.";
            form.querySelector(".spray-simple-cart-message")?.remove();
            const message = document.createElement("p");
            message.className = "spray-simple-cart-message";
            message.textContent = error;
            form.append(message);
          })
          .always(() => {
            submitButton?.classList.remove("is-loading");
            if (submitButton) submitButton.disabled = false;
          });
      });
    });
  }

  function initPacklinkCheckoutFix() {
    const checkout = document.querySelector(".wc-block-checkout");
    if (!checkout) return;

    let cleanupScheduled = false;

    function cleanDuplicateDropOffs() {
      cleanupScheduled = false;
      const containers = [...document.querySelectorAll('[id="packlink-drop-off"]')];
      if (containers.length < 2) return;

      const checkedPickup = [...document.querySelectorAll('.wc-block-components-radio-control__option')].find(
        (option) =>
          option.querySelector('input[type="radio"]:checked') &&
          /pick\s*up|recogida/i.test(option.textContent || ""),
      );
      const selectedContainers = checkedPickup
        ? [...checkedPickup.querySelectorAll('[id="packlink-drop-off"]')]
        : [];
      const keeper =
        containers.find((container) => container.querySelector("#packlink-drop-off-picker")) ||
        selectedContainers[selectedContainers.length - 1] ||
        null;

      containers.forEach((container) => {
        if (container !== keeper && !container.children.length) container.remove();
      });
    }

    function scheduleCleanup() {
      if (cleanupScheduled) return;
      cleanupScheduled = true;
      window.requestAnimationFrame(cleanDuplicateDropOffs);
    }

    cleanDuplicateDropOffs();
    new MutationObserver(scheduleCleanup).observe(checkout, {
      childList: true,
      subtree: true,
    });
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
  initSpraySelector();
  initVariableProductSelectors();
  initProductCart();
  initPacklinkCheckoutFix();
})(jQuery);
