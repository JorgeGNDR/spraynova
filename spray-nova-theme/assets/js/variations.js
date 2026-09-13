(function ($) {
  "use strict";
  function colorForLabel(label) {
    const value = label.toLocaleLowerCase("es");
    const colors = [
      [["negro", "black"], "#171717"],
      [["blanco", "white"], "#f8f6f0"],
      [["gris", "grey", "gray"], "#969696"],
      [["plata", "silver", "cromo", "chrome"], "#b7b9bc"],
      [["oro", "gold"], "#d4af37"],
      [["cobre", "copper"], "#b87333"],
      [["amarillo", "yellow"], "#f2cf35"],
      [["naranja", "orange"], "#ef7d22"],
      [["rojo", "red"], "#d33b35"],
      [["rosa", "pink", "fucsia"], "#e977b3"],
      [["morado", "purple", "violeta", "violet", "lila"], "#9f71c8"],
      [["azul", "blue", "cyan"], "#3c7dcb"],
      [["verde", "green", "lime", "oliva"], "#559b55"],
      [["marron", "marrón", "brown", "beige", "arena"], "#956b49"],
    ];
    return (
      colors.find(([names]) =>
        names.some((name) => value.includes(name)),
      )?.[1] || "#c6a0eb"
    );
  }

  function variationHex(form, attributeName, value, label) {
    const data = $(form).data("product_variations");
    const variations = Array.isArray(data) ? data : [];
    const match = variations.find((variation) => {
      const attributes = variation.attributes || {};
      return (
        attributes[attributeName] === value ||
        (!attributes[attributeName] && value)
      );
    });
    return match?.spray_nova_color_hex || colorForLabel(label);
  }

  function initVariableProductSelectors() {
    document.querySelectorAll("form.variations_form").forEach((form) => {
      if (form.closest(".spray-color-selector") || form.dataset.sprayNovaReady)
        return;
      form.dataset.sprayNovaReady = "true";

      form.querySelectorAll("table.variations select").forEach((select) => {
        const options = [...select.options].filter((option) => option.value);
        if (!options.length) return;

        const picker = document.createElement("div");
        picker.className = "spray-variation-options";
        picker.setAttribute("role", "group");
        picker.setAttribute(
          "aria-label",
          select.getAttribute("aria-label") || "Elige una opción",
        );

        options.forEach((option) => {
          const button = document.createElement("button");
          const attributeName = select.name;
          button.type = "button";
          button.className = "spray-variation-option";
          button.dataset.value = option.value;

          button.setAttribute("aria-pressed", "false");
          button.innerHTML = `<span style="--variation-color:${variationHex(form, attributeName, option.value, option.textContent)}"></span><strong></strong>`;
          button.querySelector("strong").textContent = option.textContent;
          button.addEventListener("click", () => {
            select.value = button.dataset.value;
            $(select).trigger("change");
            sync();
          });
          picker.append(button);
        });

        select.insertAdjacentElement("afterend", picker);
        select.classList.add("spray-variation-native");

        const selectionLabel = document.createElement("p");
        selectionLabel.className = "spray-selected-color-label";
        selectionLabel.setAttribute("aria-live", "polite");
        picker.after(selectionLabel);

        const sync = () => {
          const currentOption = select.options[select.selectedIndex];
          selectionLabel.textContent =
            select.value && currentOption
              ? `Seleccionado: ${currentOption.textContent}`
              : "Elige un color";
          picker
            .querySelectorAll(".spray-variation-option")
            .forEach((button) => {
              const available = [...select.options].find(
                (item) => item.value === button.dataset.value,
              );
              button.disabled = !available || available.disabled;
              const selected = button.dataset.value === select.value;
              button.setAttribute("aria-pressed", String(selected));
            });
        };
        $(select).on("change", sync);
        $(form).on(
          "woocommerce_update_variation_values found_variation reset_data",
          sync,
        );
        sync();
      });
    });
  }

  $(initVariableProductSelectors);
})(jQuery);
