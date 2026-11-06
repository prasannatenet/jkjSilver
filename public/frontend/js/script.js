
// Sticky Header and Navbar--------------------------------------------------------
const mainHeader = document.querySelector(".main-header");
const categoryNav = document.querySelector(".category-nav");
const categoryToggle = document.querySelector(".category-toggle");

function updateScrolledClass() {
    const isScrolled = window.scrollY > 0;
    mainHeader.classList.toggle("is-scrolled", isScrolled);

    if (!isScrolled) {
        categoryNav.classList.remove("is-scrolled");
    } else if (!categoryNav.classList.contains("is-open")) {
        categoryNav.classList.add("is-scrolled");
    }
}

window.addEventListener("scroll", updateScrolledClass, { passive: true });
updateScrolledClass();

const searchInput = document.querySelector(".main-header .search input");
const searchCategoryNames = searchInput
    ? JSON.parse(searchInput.dataset.categoryNames || "[]").filter((name) => typeof name === "string" && name.trim())
    : [];

if (searchInput) {
    const searchForm = searchInput.closest("form");
    const suggestions = searchForm.querySelector(".search-suggestions");
    let suggestionController;
    let suggestionTimer;
    let activeSuggestion = -1;

    const closeSuggestions = () => {
        suggestions.hidden = true;
        suggestions.replaceChildren();
        searchInput.setAttribute("aria-expanded", "false");
        activeSuggestion = -1;
    };

    const showSuggestions = (data) => {
        suggestions.replaceChildren();
        const groups = [
            ["Categories", data.categories || []],
            ["Products", data.products || []],
        ];

        groups.forEach(([label, items]) => {
            if (!items.length) return;

            const group = document.createElement("div");
            group.className = "search-suggestion-group";
            group.setAttribute("role", "group");
            group.setAttribute("aria-label", label);

            const heading = document.createElement("p");
            heading.className = "search-suggestion-heading";
            heading.textContent = label;
            group.append(heading);

            items.forEach((item) => {
                const link = document.createElement("a");
                link.className = "search-suggestion";
                link.href = item.url;
                link.textContent = item.name;
                link.setAttribute("role", "option");
                group.append(link);
            });

            suggestions.append(group);
        });

        if (!suggestions.childElementCount) {
            closeSuggestions();
            return;
        }

        suggestions.hidden = false;
        searchInput.setAttribute("aria-expanded", "true");
        activeSuggestion = -1;
    };

    searchInput.addEventListener("input", () => {
        window.clearTimeout(suggestionTimer);
        suggestionController?.abort();

        const term = searchInput.value.trim();
        if (term.length <= 3) {
            closeSuggestions();
            return;
        }

        suggestionTimer = window.setTimeout(async () => {
            suggestionController = new AbortController();
            try {
                const url = new URL(searchInput.dataset.suggestionsUrl, window.location.origin);
                url.searchParams.set("q", term);
                const response = await fetch(url, {
                    headers: { Accept: "application/json" },
                    signal: suggestionController.signal,
                });
                if (!response.ok) throw new Error(`Search suggestions failed (${response.status}).`);

                const data = await response.json();
                if (searchInput.value.trim() === term) showSuggestions(data);
            } catch (error) {
                if (error.name !== "AbortError") {
                    closeSuggestions();
                    console.error(error);
                }
            }
        }, 200);
    });

    searchInput.addEventListener("keydown", (event) => {
        const options = [...suggestions.querySelectorAll('[role="option"]')];
        if (suggestions.hidden || !options.length) {
            if (event.key === "Escape") closeSuggestions();
            return;
        }

        if (event.key === "ArrowDown" || event.key === "ArrowUp") {
            event.preventDefault();
            activeSuggestion = (activeSuggestion + (event.key === "ArrowDown" ? 1 : -1) + options.length) % options.length;
            options.forEach((option, index) => option.setAttribute("aria-selected", String(index === activeSuggestion)));
            options[activeSuggestion].scrollIntoView({ block: "nearest" });
        } else if (event.key === "Enter" && activeSuggestion >= 0) {
            event.preventDefault();
            options[activeSuggestion].click();
        } else if (event.key === "Escape") {
            closeSuggestions();
        }
    });

    searchInput.addEventListener("blur", (event) => {
        if (!searchForm.contains(event.relatedTarget)) closeSuggestions();
    });
    searchForm.addEventListener("submit", () => closeSuggestions());
    document.addEventListener("click", (event) => {
        if (!searchForm.contains(event.target)) closeSuggestions();
    });
}

if (searchInput && searchCategoryNames.length) {
    const defaultPlaceholder = "What are you looking for?";
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    let categoryIndex = 0;
    let characterIndex = 0;
    let deleting = false;
    let typingTimer;

    const typeCategory = () => {
        if (document.activeElement === searchInput || searchInput.value) return;

        const categoryName = searchCategoryNames[categoryIndex];
        characterIndex += deleting ? -1 : 1;
        searchInput.placeholder = categoryName.slice(0, characterIndex);

        let delay = deleting ? 55 : 100;
        if (!deleting && characterIndex === categoryName.length) {
            deleting = true;
            delay = 1200;
        } else if (deleting && characterIndex === 0) {
            deleting = false;
            categoryIndex = (categoryIndex + 1) % searchCategoryNames.length;
            delay = 250;
        }

        typingTimer = window.setTimeout(typeCategory, delay);
    };

    const startCategoryTyping = () => {
        window.clearTimeout(typingTimer);
        if (reducedMotion) {
            searchInput.placeholder = searchCategoryNames[0];
            return;
        }

        characterIndex = 0;
        deleting = false;
        searchInput.placeholder = "";
        typingTimer = window.setTimeout(typeCategory, 250);
    };

    searchInput.addEventListener("focus", () => {
        window.clearTimeout(typingTimer);
        searchInput.placeholder = defaultPlaceholder;
    });
    searchInput.addEventListener("blur", () => {
        if (!searchInput.value) startCategoryTyping();
    });
    searchInput.addEventListener("input", () => {
        if (searchInput.value) {
            window.clearTimeout(typingTimer);
            searchInput.placeholder = defaultPlaceholder;
        } else if (document.activeElement !== searchInput) {
            startCategoryTyping();
        }
    });

    startCategoryTyping();
}

function closeCategoryNavigation() {
    categoryNav.classList.remove("is-open");
    categoryNav.classList.toggle("is-scrolled", window.scrollY > 0);
    categoryToggle.classList.remove("is-open");
    categoryToggle.setAttribute("aria-expanded", "false");
    categoryToggle.setAttribute("aria-label", "Show categories");
    categoryNav.querySelectorAll(".category-dropdown-toggle").forEach((toggle) => {
        toggle.setAttribute("aria-expanded", "false");
        toggle.closest(".category-item").classList.remove("is-dropdown-open");
    });
}

categoryToggle.addEventListener("click", () => {
    const isOpen = categoryNav.classList.toggle("is-open");
    categoryNav.classList.toggle("is-scrolled", !isOpen && window.scrollY > 0);
    categoryToggle.classList.toggle("is-open", isOpen);
    categoryToggle.setAttribute("aria-expanded", String(isOpen));
    categoryToggle.setAttribute("aria-label", isOpen ? "Hide categories" : "Show categories");
    if (!isOpen) closeCategoryNavigation();
});

categoryNav.querySelectorAll(".category-dropdown-toggle").forEach((toggle) => {
    toggle.addEventListener("click", () => {
        const categoryItem = toggle.closest(".category-item");
        const isOpen = !categoryItem.classList.contains("is-dropdown-open");

        categoryNav.querySelectorAll(".category-item.is-dropdown-open").forEach((item) => {
            item.classList.remove("is-dropdown-open");
            item.querySelector(".category-dropdown-toggle").setAttribute("aria-expanded", "false");
        });

        categoryItem.classList.toggle("is-dropdown-open", isOpen);
        toggle.setAttribute("aria-expanded", String(isOpen));
    });
});

categoryNav.addEventListener("click", (event) => {
    if (event.target.closest("a") && categoryNav.classList.contains("is-open")) {
        closeCategoryNavigation();
    }
});

document.addEventListener("click", (event) => {
    if (
        window.matchMedia("(max-width: 767px)").matches &&
        categoryNav.classList.contains("is-open") &&
        !event.target.closest("#category-nav, .category-toggle")
    ) {
        closeCategoryNavigation();
    }
});

document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;

    const openDropdown = categoryNav.querySelector(".category-item.is-dropdown-open");
    if (openDropdown) {
        openDropdown.classList.remove("is-dropdown-open");
        openDropdown.querySelector(".category-dropdown-toggle").setAttribute("aria-expanded", "false");
    } else if (categoryNav.classList.contains("is-open")) {
        closeCategoryNavigation();
        categoryToggle.focus();
    }
});

const wishlistDrawer = document.querySelector(".wishlist-drawer");
const wishlistDrawerTrigger = document.querySelector(".wishlist-drawer-trigger");
const wishlistDrawerClose = document.querySelector(".wishlist-drawer-close");
const wishlistDrawerBackdrop = document.querySelector(".wishlist-drawer-backdrop");

function closeWishlistDrawer() {
    if (!wishlistDrawer?.classList.contains("is-open")) return;

    wishlistDrawer.classList.remove("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "true");
    wishlistDrawer.inert = true;
    wishlistDrawerTrigger.setAttribute("aria-expanded", "false");
    wishlistDrawerBackdrop.hidden = true;
    document.body.classList.remove("wishlist-drawer-open");
    wishlistDrawerTrigger.focus();
}

wishlistDrawerTrigger?.addEventListener("click", () => {
    wishlistDrawer.classList.add("is-open");
    wishlistDrawer.setAttribute("aria-hidden", "false");
    wishlistDrawer.inert = false;
    wishlistDrawerTrigger.setAttribute("aria-expanded", "true");
    wishlistDrawerBackdrop.hidden = false;
    document.body.classList.add("wishlist-drawer-open");
    wishlistDrawerClose.focus();
});

wishlistDrawerClose?.addEventListener("click", closeWishlistDrawer);
wishlistDrawerBackdrop?.addEventListener("click", closeWishlistDrawer);
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeWishlistDrawer();
});

const cartDrawer = document.querySelector(".cart-drawer");
const cartDrawerTrigger = document.querySelector(".cart-drawer-trigger");
const cartDrawerClose = document.querySelector(".cart-drawer-close");
const cartDrawerBackdrop = document.querySelector(".cart-drawer-backdrop");

function closeCartDrawer() {
    if (!cartDrawer?.classList.contains("is-open")) return;

    cartDrawer.classList.remove("is-open");
    cartDrawer.setAttribute("aria-hidden", "true");
    cartDrawer.inert = true;
    cartDrawerTrigger.setAttribute("aria-expanded", "false");
    cartDrawerBackdrop.hidden = true;
    document.body.classList.remove("cart-drawer-open");
    cartDrawerTrigger.focus();
}

function openCartDrawer() {
    if (!cartDrawer || !cartDrawerTrigger || !cartDrawerClose || !cartDrawerBackdrop) return;

    cartDrawer.classList.add("is-open");
    cartDrawer.setAttribute("aria-hidden", "false");
    cartDrawer.inert = false;
    cartDrawerTrigger.setAttribute("aria-expanded", "true");
    cartDrawerBackdrop.hidden = false;
    document.body.classList.add("cart-drawer-open");
    cartDrawerClose.focus();
}

cartDrawerTrigger?.addEventListener("click", openCartDrawer);

cartDrawerClose?.addEventListener("click", closeCartDrawer);
cartDrawerBackdrop?.addEventListener("click", closeCartDrawer);
document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeCartDrawer();
});

function updateCartDrawer(result) {
    const drawerContent = cartDrawer?.querySelector(".cart-drawer-content-wrapper");
    const titleCount = cartDrawer?.querySelector("#cart-drawer-title span");
    if (!drawerContent || !titleCount) throw new Error("The shopping bag could not be refreshed.");

    drawerContent.innerHTML = result.drawer_html;
    titleCount.textContent = `(${result.count})`;
    document.querySelectorAll(".cart-count").forEach((counter) => {
        counter.textContent = String(result.count);
    });
    cartDrawerTrigger?.setAttribute(
        "aria-label",
        `Open shopping bag, ${result.count} ${result.count === 1 ? "item" : "items"}`,
    );
}

document.addEventListener("click", async (event) => {
    const removeButton = event.target.closest("[data-cart-remove-url]");
    if (!removeButton) return;

    event.preventDefault();
    if (removeButton.disabled) return;
    removeButton.disabled = true;

    try {
        const payload = removeButton.dataset.cartId
            ? { cart_id: Number(removeButton.dataset.cartId) }
            : {
                product_id: Number(removeButton.dataset.productId),
                product_variation_id: removeButton.dataset.productVariationId
                    ? Number(removeButton.dataset.productVariationId)
                    : null,
            };
        const response = await fetch(removeButton.dataset.cartRemoveUrl, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify(payload),
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            const validationError = Object.values(result.errors || {}).flat()[0];
            throw new Error(validationError || result.message || "Unable to remove this item.");
        }

        updateCartDrawer(result);
    } catch (error) {
        console.error("Unable to remove the item from the shopping bag.", error);
        const toast = document.querySelector(".wishlist-toast");
        if (toast) {
            toast.textContent = error instanceof Error ? error.message : "Unable to remove this item.";
            toast.classList.add("is-visible");
            window.clearTimeout(toast.hideTimer);
            toast.hideTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 3500);
        }
    } finally {
        removeButton.disabled = false;
    }
});

document.addEventListener("click", async (event) => {
    const addButton = event.target.closest("[data-cart-add-url][data-product-id]");
    if (!addButton) return;

    event.preventDefault();
    if (addButton.disabled) return;

    const originalLabel = addButton.textContent;
    addButton.disabled = true;

    try {
        const response = await fetch(addButton.dataset.cartAddUrl, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({
                product_id: Number(addButton.dataset.productId),
                quantity: 1,
            }),
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            const validationError = Object.values(result.errors || {}).flat()[0];
            throw new Error(validationError || result.message || "Unable to add this item to your bag.");
        }

        updateCartDrawer(result);

        addButton.textContent = "ADDED";
        openCartDrawer();
        const toast = document.querySelector(".wishlist-toast");
        if (toast) {
            toast.textContent = "Added to your shopping bag.";
            toast.classList.add("is-visible");
            window.clearTimeout(toast.hideTimer);
            toast.hideTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 2500);
        }
    } catch (error) {
        console.error("Unable to add the product to the shopping bag.", error);
        const toast = document.querySelector(".wishlist-toast");
        if (toast) {
            toast.textContent = error instanceof Error ? error.message : "Unable to add this item to your bag.";
            toast.classList.add("is-visible");
            window.clearTimeout(toast.hideTimer);
            toast.hideTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 3500);
        }
    } finally {
        window.setTimeout(() => {
            addButton.textContent = originalLabel;
            addButton.disabled = false;
        }, 1200);
    }
});


// HERO SLIDER------------------------------------------------
const heroSwiper = new Swiper(".hero-slider", {
    loop: true,
    effect: "fade",

    fadeEffect: {
        crossFade: true,
    },

    speed: 500,

    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },

    navigation: {
        prevEl: ".hero-prev",
        nextEl: ".hero-next",
    },

    pagination: {
        el: ".hero-pagination",
        clickable: true,
    },
});

const categorySliderElement = document.querySelector(".category-slider");
if (categorySliderElement) {
    const categorySlideCount = categorySliderElement.querySelectorAll(".swiper-slide").length;
    const categoryAutoplay = window.matchMedia("(prefers-reduced-motion: reduce)").matches
        ? false
        : {
            delay: 3500,
            pauseOnMouseEnter: true,
            disableOnInteraction: false,
        };

    new Swiper(categorySliderElement, {
        slidesPerView: 1,
        slidesPerGroup: 1,
        spaceBetween: 12,
        speed: 600,
        rewind: true,
        watchOverflow: true,
        autoplay: categorySlideCount > 1 ? categoryAutoplay : false,
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 16,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 16,
            },
            992: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
        },
    });
}

// PRODUCT SLIDER------------------------------------------------
const productSwiper = new Swiper(".productSwiper", {
    slidesPerView: 5,
    slidesPerGroup: 1,
    spaceBetween: 30,
    speed: 600,
    watchOverflow: true,

    navigation: {
        nextEl: ".product-next",
        prevEl: ".product-prev",
    },

    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 15,
        },
        576: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        992: {
            slidesPerView: 4,
            spaceBetween: 25,
        },
        1200: {
            slidesPerView: 5,
            spaceBetween: 30,
        },
    },
});

const newArrivalsElement = document.querySelector(".new-arrivals-slider");
const newArrivalsCount = newArrivalsElement?.querySelectorAll(".swiper-slide").length ?? 0;
const newArrivalsAutoplay = window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ? false
    : {
        delay: 3000,
        pauseOnMouseEnter: true,
        disableOnInteraction: false,
    };

const newArrivalsSwiper = new Swiper(".new-arrivals-slider", {
    slidesPerView: 3,
    slidesPerGroup: 1,
    spaceBetween: 20,
    rewind: true,
    autoplay: newArrivalsCount > 1 ? newArrivalsAutoplay : false,
    speed: 600,
    watchOverflow: true,

    navigation: {
        nextEl: ".new-arrivals-next",
        prevEl: ".new-arrivals-prev",
    },

    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 15,
        },
        576: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        992: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    },
});

// Handpicked SLIDER------------------------------------------------
const handpickedElement = document.querySelector(".handpickedSwiper");
const handpickedCount = handpickedElement?.querySelectorAll(".swiper-slide").length ?? 0;
const handpickedReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const handpickedSwiper = new Swiper(".handpickedSwiper", {
    slidesPerView: 4,
    slidesPerGroup: 1,
    spaceBetween: 20,
    loop: handpickedCount > 4,
    autoplay: handpickedCount > 4 && !handpickedReducedMotion ? {
        delay: 3000,
        pauseOnMouseEnter: true, // Pauses autoplay when mouse enters the container
        disableOnInteraction: false, // Keeps autoplay enabled after user interactions
    } : false,
    speed: 600,
    watchOverflow: true,


    navigation: {
        nextEl: ".handpicked-next",
        prevEl: ".handpicked-prev",
    },

    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 15,
        },
        576: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        768: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    },
});

const lookSwipers = new Map();

document.querySelectorAll(".right-section .look-products").forEach((panel) => {
    const navigation = panel.querySelector(".look-navigation");
    const swiper = new Swiper(panel.querySelector(".lookSwiper"), {
        slidesPerView: 3,
        slidesPerGroup: 1,
        spaceBetween: 20,
        speed: 600,
        watchOverflow: true,

        navigation: {
            nextEl: navigation.querySelector(".look-next"),
            prevEl: navigation.querySelector(".look-prev"),
        },

        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15,
            },
            576: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            992: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
        },
    });

    lookSwipers.set(panel.id, swiper);
});


// LOOKS TAB
document.querySelectorAll(".look-tab").forEach((tab) => {
    tab.addEventListener("click", () => {
        const productsPanelId = tab.dataset.lookTab === "toeRing" ? "toeRing-products" : "payal-products";

        document.querySelectorAll(".look-tab").forEach((item) => {
            const isSelected = item === tab;
            item.classList.toggle("is-active", isSelected);
            item.setAttribute("aria-selected", String(isSelected));
        });

        document.getElementById("payal-products").hidden = productsPanelId !== "payal-products";
        document.getElementById("toeRing-products").hidden = productsPanelId !== "toeRing-products";

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                lookSwipers.get(productsPanelId)?.update();
            });
        });
    });
});

// Looks Hotspot
document.querySelectorAll(".look-hotspot").forEach((hotspot) => {
    const x = Number(hotspot.dataset.x);
    const y = Number(hotspot.dataset.y);
    const productIndex = Number(hotspot.dataset.productIndex);

    if (![x, y, productIndex].every(Number.isFinite) || x < 0 || x > 100 || y < 0 || y > 100) {
        console.warn("Skipping hotspot with invalid coordinates or product index.", hotspot);
        return;
    }

    hotspot.style.setProperty("--hotspot-x", `${x}%`);
    hotspot.style.setProperty("--hotspot-y", `${y}%`);
    hotspot.addEventListener("click", () => {
        const activeCategory = document.querySelector(".look-tab.is-active")?.dataset.lookTab;
        const productsPanelId = activeCategory === "toeRing" ? "toeRing-products" : "payal-products";
        lookSwipers.get(productsPanelId)?.slideTo(productIndex);
    });
});

// Product Card

document.querySelectorAll(".product-card").forEach((card) => {
    const image = card.querySelector(".product-image");
    if (!image) return;

    const mainImage = image.querySelector("img[data-hover-image]");
    const name = card.querySelector("h3")?.textContent.trim() || "product";

    if (mainImage?.dataset.hoverImage) {
        const hoverImage = document.createElement("img");
        hoverImage.className = "product-hover-image";
        hoverImage.src = mainImage.dataset.hoverImage;
        hoverImage.alt = "";
        hoverImage.loading = "lazy";
        hoverImage.decoding = "async";
        hoverImage.setAttribute("aria-hidden", "true");
        image.append(hoverImage);
    }

    const wishlistButton = image.querySelector(".add-to-wishlist");
    if (!wishlistButton) return;

    const icon = wishlistButton.querySelector(".material-symbols-outlined");
    const productId = wishlistButton.dataset.productId;
    const wishlistKey = wishlistButton.dataset.wishlistKey;
    const setWishlistState = (isWishlisted) => {
        wishlistButton.setAttribute("aria-pressed", String(isWishlisted));
        wishlistButton.setAttribute(
            "aria-label",
            `${isWishlisted ? "Remove" : "Add"} ${name} ${isWishlisted ? "from" : "to"} wishlist`,
        );
        wishlistButton.classList.toggle("is-wishlisted", isWishlisted);
        if (icon) icon.textContent = isWishlisted ? "favorite" : "favorite_border";
    };

    setWishlistState(wishlistButton.getAttribute("aria-pressed") === "true");
    if ((!productId && !wishlistKey) || !wishlistButton.dataset.wishlistUrl) {
        wishlistButton.disabled = true;
        wishlistButton.setAttribute("aria-label", `${name} cannot be added to the wishlist`);
        return;
    }

    wishlistButton.addEventListener("click", async () => {
        if (wishlistButton.disabled) return;
        wishlistButton.disabled = true;

        try {
            const response = await fetch(wishlistButton.dataset.wishlistUrl, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(productId
                    ? { product_id: Number(productId) }
                    : { fallback_key: wishlistKey }),
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || "Unable to update your wishlist. Please try again.");
            }

            document.querySelectorAll(".add-to-wishlist").forEach((button) => {
                if (button.dataset.wishlistKey !== result.wishlist_key) return;

                button.setAttribute("aria-pressed", String(result.in_wishlist));
                button.classList.toggle("is-wishlisted", result.in_wishlist);
                const buttonIcon = button.querySelector(".material-symbols-outlined");
                if (buttonIcon) {
                    buttonIcon.textContent = result.in_wishlist ? "favorite" : "favorite_border";
                }
                const cardName = button.closest(".product-card")?.querySelector("h3")?.textContent.trim() || "product";
                button.setAttribute(
                    "aria-label",
                    `${result.in_wishlist ? "Remove" : "Add"} ${cardName} ${result.in_wishlist ? "from" : "to"} wishlist`,
                );
            });

            const count = Number(result.count) || 0;
            const wishlistTrigger = document.querySelector(".wishlist-drawer-trigger");
            const wishlistBadge = wishlistTrigger?.querySelector(".wishlist-count");
            const wishlistTitleCount = document.querySelector("#wishlist-drawer-title span");
            if (wishlistBadge) wishlistBadge.textContent = String(count);
            if (wishlistTitleCount) wishlistTitleCount.textContent = `(${count})`;
            if (wishlistTrigger) {
                wishlistTrigger.setAttribute(
                    "aria-label",
                    `Open wishlist, ${count} ${count === 1 ? "item" : "items"}`,
                );
            }

            const drawerContent = document.querySelector(".wishlist-drawer-content");
            const existingItem = drawerContent
                ? [...drawerContent.querySelectorAll(".wishlist-drawer-item")]
                    .find((item) => item.dataset.wishlistKey === result.wishlist_key)
                : null;
            if (result.in_wishlist && drawerContent && !existingItem) {
                drawerContent.querySelector(".wishlist-drawer-empty")?.remove();
                const item = document.createElement("a");
                item.className = "wishlist-drawer-item";
                item.dataset.productId = result.product.id ? String(result.product.id) : "";
                item.dataset.wishlistKey = result.wishlist_key;
                item.href = result.product.href;

                const itemImage = document.createElement("img");
                itemImage.src = result.product.image;
                itemImage.alt = "";
                itemImage.loading = "lazy";

                const details = document.createElement("span");
                details.className = "wishlist-drawer-item-details";
                const itemName = document.createElement("span");
                itemName.className = "wishlist-drawer-item-name";
                itemName.textContent = result.product.name;
                const itemPrice = document.createElement("span");
                itemPrice.className = "wishlist-drawer-item-price";
                itemPrice.textContent = `₹ ${Number(result.product.price).toLocaleString("en-IN", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })}`;
                const arrow = document.createElement("span");
                arrow.className = "material-symbols-outlined wishlist-drawer-item-arrow";
                arrow.setAttribute("aria-hidden", "true");
                arrow.textContent = "arrow_forward";
                details.append(itemName, itemPrice);
                item.append(itemImage, details, arrow);
                drawerContent.prepend(item);
            } else if (!result.in_wishlist) {
                existingItem?.remove();
                if (drawerContent && !drawerContent.querySelector(".wishlist-drawer-item")) {
                    const emptyMessage = document.createElement("p");
                    emptyMessage.className = "wishlist-drawer-empty";
                    emptyMessage.textContent = "Your wishlist is empty.";
                    drawerContent.append(emptyMessage);
                }
            }

            const toast = document.querySelector(".wishlist-toast");
            if (toast) {
                toast.textContent = result.in_wishlist ? "Added to wishlist." : "Removed from wishlist.";
                toast.classList.add("is-visible");
                window.clearTimeout(toast.hideTimer);
                toast.hideTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 2500);
            }
        } catch (error) {
            console.error("Wishlist update failed.", error);
            const toast = document.querySelector(".wishlist-toast");
            if (toast) {
                toast.textContent = error instanceof Error ? error.message : "Unable to update your wishlist.";
                toast.classList.add("is-visible");
            }
        } finally {
            wishlistButton.disabled = false;
        }
    });
});

const productProgress = document.querySelector(".product-scrollbar-progress");
const productProgressTrack = document.querySelector(".product-scrollbar");

function updateProductProgress() {
    if (!productProgress || !productProgressTrack) return;
    const progress = productSwiper.progress;
    const percentage = Math.round(progress * 100);
    productProgress.style.transform = `scaleX(${progress})`;
    productProgressTrack.setAttribute("aria-valuenow", percentage);
}

productSwiper.on("slideChange", updateProductProgress);
productSwiper.on("resize", updateProductProgress);
productSwiper.on("transitionEnd", updateProductProgress);
productSwiper.on("touchEnd", updateProductProgress);
updateProductProgress();

const bestSellersLoadUrl = document.querySelector(".productSwiper")?.dataset.loadMoreUrl;
let bestSellersPage = 2;
let bestSellersHasMore = Boolean(bestSellersLoadUrl);
let bestSellersLoading = false;

async function loadMoreBestSellers() {
    if (!bestSellersLoadUrl || !bestSellersHasMore || bestSellersLoading) return;

    bestSellersLoading = true;
    try {
        const url = new URL(bestSellersLoadUrl, window.location.href);
        url.searchParams.set("page", String(bestSellersPage));
        const response = await fetch(url, {
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        });
        if (!response.ok) {
            throw new Error(`Best Sellers request failed with status ${response.status}.`);
        }

        const result = await response.json();
        const template = document.createElement("template");
        template.innerHTML = result.html;
        const newSlides = [...template.content.children];

        if (newSlides.length) {
            productSwiper.appendSlide(newSlides);
            bestSellersPage += 1;
        }
        bestSellersHasMore = Boolean(result.has_more);
    } catch (error) {
        console.error("Unable to load more Best Sellers.", error);
    } finally {
        bestSellersLoading = false;
    }
}

function preloadBestSellersAtCarouselEnd() {
    const visibleSlides = Number(productSwiper.params.slidesPerView) || 1;
    if (productSwiper.activeIndex >= productSwiper.slides.length - visibleSlides - 2) {
        loadMoreBestSellers();
    }
}

productSwiper.on("slideChange", preloadBestSellersAtCarouselEnd);
productSwiper.on("reachEnd", loadMoreBestSellers);

// PRODUCT DETAIL GALLERY AND PURCHASE CONTROLS
const productFeatureImage = document.querySelector(".product-feature-image");
let productThumbnails = [...document.querySelectorAll(".product-thumbnail")];
const productMainImage = document.querySelector(".product-main-image");
const productZoomWindow = document.querySelector(".product-zoom-window");
const productZoomImage = productZoomWindow?.querySelector("img");
const productZoomTrigger = document.querySelector(".product-image-zoom-trigger");
const productZoomLens = document.querySelector(".product-zoom-lens");
const productLightbox = document.querySelector(".product-lightbox");
const productMainVideo = document.querySelector("#mainProductVideo");
const productLightboxImage = document.querySelector(".product-lightbox-image");
const productLightboxThumbnails = document.querySelector(".product-lightbox-thumbnails");
const productLightboxCounter = document.querySelector(".product-lightbox-counter");
let activeProductImage = productThumbnails.findIndex((thumbnail) => thumbnail.getAttribute("aria-pressed") === "true");

function selectProductImage(index) {
    if (!productThumbnails.length) return;
    activeProductImage = (index + productThumbnails.length) % productThumbnails.length;
    const activeThumbnail = productThumbnails[activeProductImage];
    if (productZoomTrigger) productZoomTrigger.style.display = "";
    if (productMainVideo) {
        productMainVideo.pause();
        productMainVideo.style.display = "none";
    }

    productThumbnails.forEach((thumbnail, thumbnailIndex) => {
        const isActive = thumbnailIndex === activeProductImage;
        thumbnail.classList.toggle("is-active", isActive);
        thumbnail.setAttribute("aria-pressed", String(isActive));
    });

    productFeatureImage.src = activeThumbnail.dataset.image;
    productFeatureImage.alt = activeThumbnail.dataset.alt;
    productZoomImage.src = activeThumbnail.dataset.image;
    productLightboxImage.src = activeThumbnail.dataset.image;
    productLightboxImage.alt = activeThumbnail.dataset.alt;
    productLightboxCounter.textContent = `${activeProductImage + 1} / ${productThumbnails.length}`;

    productLightboxThumbnails.querySelectorAll(".product-lightbox-thumbnail").forEach((thumbnail, thumbnailIndex) => {
        const isActive = thumbnailIndex === activeProductImage;
        thumbnail.classList.toggle("is-active", isActive);
        thumbnail.setAttribute("aria-pressed", String(isActive));
    });
}

function switchToVideo(videoUrl) {
    if (!productMainVideo || !productZoomTrigger) return;

    productZoomTrigger.style.display = "none";
    productMainImage?.classList.remove("is-zooming");
    productMainVideo.src = videoUrl;
    productMainVideo.style.display = "";
    productMainVideo.load();
}

function bindProductThumbnails() {
    productThumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener("click", () => selectProductImage(index));
    });

    productLightboxThumbnails?.replaceChildren();
    productThumbnails.forEach((thumbnail, index) => {
        const lightboxThumbnail = document.createElement("button");
        lightboxThumbnail.className = "product-lightbox-thumbnail";
        lightboxThumbnail.type = "button";
        lightboxThumbnail.setAttribute("aria-label", thumbnail.getAttribute("aria-label"));
        lightboxThumbnail.setAttribute("aria-pressed", "false");

        const image = document.createElement("img");
        image.src = thumbnail.dataset.image;
        image.alt = "";
        lightboxThumbnail.append(image);
        lightboxThumbnail.addEventListener("click", () => selectProductImage(index));
        productLightboxThumbnails?.append(lightboxThumbnail);
    });

    selectProductImage(Math.max(activeProductImage, 0));
}

function renderGalleryForVariation(varId) {
    const option = document.querySelector(`#product-variation option[data-variation-id="${varId}"]`);
    const thumbnails = document.querySelector(".product-thumbnails");
    if (!option || !thumbnails) return;

    const images = JSON.parse(option.dataset.images || "[]");
    if (!images.length) return;

    const videoThumbnails = [...thumbnails.querySelectorAll(".gallery-thumb-video")]
        .map((thumbnail) => thumbnail.cloneNode(true));
    thumbnails.replaceChildren();
    images.forEach((image, index) => {
        const thumbnail = document.createElement("button");
        thumbnail.className = `product-thumbnail${index === 0 ? " is-active" : ""}`;
        thumbnail.type = "button";
        thumbnail.setAttribute("aria-label", `View product image ${index + 1}`);
        thumbnail.setAttribute("aria-pressed", String(index === 0));
        thumbnail.dataset.image = image.url;
        thumbnail.dataset.galleryImage = image.url;
        thumbnail.dataset.alt = image.alt;

        const preview = document.createElement("img");
        preview.src = image.url;
        preview.alt = "";
        preview.loading = "lazy";
        thumbnail.append(preview);
        thumbnails.append(thumbnail);
    });
    videoThumbnails.forEach((thumbnail) => thumbnails.append(thumbnail));

    productThumbnails = [...thumbnails.querySelectorAll(".product-thumbnail")];
    activeProductImage = 0;
    bindProductThumbnails();
}

bindProductThumbnails();

document.querySelector(".product-variation-select")?.addEventListener("change", (event) => {
    const option = event.currentTarget.selectedOptions[0];
    const price = Number(option.dataset.price);
    const regularPrice = Number(option.dataset.regularPrice);
    const discount = Number(option.dataset.discount);
    const formatPrice = (value) => `₹${value.toLocaleString("en-IN", { maximumFractionDigits: 0 })}`;
    const priceElement = document.querySelector("[data-product-price]");
    const oldPriceElement = document.querySelector("[data-product-price-old]");
    const discountElement = document.querySelector("#product-price-discount");
    const stockElement = document.querySelector("[data-product-stock]");
    const addToCartButton = document.querySelector(".product-add-to-cart");
    const buyNowButton = document.querySelector(".product-buy-now");

    if (priceElement) priceElement.textContent = formatPrice(price);
    if (oldPriceElement) {
        oldPriceElement.textContent = formatPrice(regularPrice);
        oldPriceElement.style.display = discount > 0 ? "" : "none";
    }
    if (discountElement) {
        discountElement.textContent = discount > 0 ? `${Math.round(discount)}% OFF` : "";
        discountElement.style.display = discount > 0 ? "" : "none";
    }
    if (stockElement) {
        const inStock = Number(option.dataset.stock) > 0;
        stockElement.dataset.inStock = String(inStock);
        stockElement.lastChild.textContent = inStock ? " In stock — ready to ship" : " Currently out of stock";
        if (addToCartButton) addToCartButton.disabled = !inStock;
        if (buyNowButton) buyNowButton.disabled = !inStock;
    }

    const sku = document.querySelector("[data-product-sku]");
    if (sku) sku.textContent = option.dataset.sku || "";
    const description = document.querySelector("[data-product-description]");
    if (description) description.textContent = option.dataset.description || "";
});

productZoomTrigger?.addEventListener("pointermove", (event) => {
    if (event.pointerType === "touch" || !window.matchMedia("(hover: hover) and (min-width: 701px)").matches) {
        return;
    }

    const bounds = productZoomTrigger.getBoundingClientRect();
    const x = Math.min(1, Math.max(0, (event.clientX - bounds.left) / bounds.width));
    const y = Math.min(1, Math.max(0, (event.clientY - bounds.top) / bounds.height));
    const zoomBounds = productZoomWindow.getBoundingClientRect();
    const imageWidth = bounds.width * 2;
    const imageHeight = bounds.height * 2;
    const lensWidth = Math.min(bounds.width, zoomBounds.width / 3);
    const lensHeight = Math.min(bounds.height, zoomBounds.height / 3);
    const lensLeft = Math.min(bounds.width - lensWidth, Math.max(0, x * bounds.width - lensWidth / 2));
    const lensTop = Math.min(bounds.height - lensHeight, Math.max(0, y * bounds.height - lensHeight / 2));
    const imageLeft = Math.max(zoomBounds.width - imageWidth, Math.min(0, zoomBounds.width / 2 - (lensLeft + lensWidth / 2) * 2));
    const imageTop = Math.max(zoomBounds.height - imageHeight, Math.min(0, zoomBounds.height / 2 - (lensTop + lensHeight / 2) * 2));

    productZoomLens.style.width = `${lensWidth}px`;
    productZoomLens.style.height = `${lensHeight}px`;
    productZoomLens.style.left = `${lensLeft}px`;
    productZoomLens.style.top = `${lensTop}px`;
    productZoomImage.style.width = `${imageWidth}px`;
    productZoomImage.style.height = `${imageHeight}px`;
    productZoomImage.style.transform = `translate(${imageLeft}px, ${imageTop}px)`;
    productMainImage.classList.add("is-zooming");
});
productZoomTrigger?.addEventListener("pointerleave", () => {
    productMainImage.classList.remove("is-zooming");
});
productZoomTrigger?.addEventListener("pointerenter", (event) => {
    if (event.pointerType !== "touch" && window.matchMedia("(hover: hover) and (min-width: 701px)").matches) {
        productMainImage.classList.add("is-zooming");
    }
});

productZoomTrigger?.addEventListener("click", () => {
    productLightbox.showModal();
});
document.querySelector(".product-lightbox-close")?.addEventListener("click", () => productLightbox.close());
document.querySelector(".product-lightbox-prev")?.addEventListener("click", () => {
    selectProductImage(activeProductImage - 1);
});
document.querySelector(".product-lightbox-next")?.addEventListener("click", () => {
    selectProductImage(activeProductImage + 1);
});
productLightbox?.addEventListener("click", (event) => {
    if (event.target === productLightbox) productLightbox.close();
});
productLightbox?.addEventListener("keydown", (event) => {
    if (event.key === "ArrowLeft") selectProductImage(activeProductImage - 1);
    if (event.key === "ArrowRight") selectProductImage(activeProductImage + 1);
    if (event.key === "Escape") {
        event.preventDefault();
        productLightbox.close();
    }
});

const quantityValue = document.querySelector(".quantity-value");
let productQuantity = Number(quantityValue?.value || quantityValue?.textContent || 1);

document.querySelectorAll("[data-quantity-change]").forEach((button) => {
    button.addEventListener("click", () => {
        productQuantity = Math.max(1, productQuantity + Number(button.dataset.quantityChange));
        quantityValue.value = String(productQuantity);
        quantityValue.textContent = String(productQuantity);
    });
});

const productWishlistButton = document.querySelector(".product-gallery-wishlist");
productWishlistButton?.addEventListener("click", async () => {
    productWishlistButton.disabled = true;
    try {
        const response = await fetch(productWishlistButton.dataset.wishlistUrl, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ product_id: Number(productWishlistButton.dataset.productId) }),
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Unable to update your wishlist. Please try again.");
        }

        productWishlistButton.setAttribute("aria-pressed", String(result.in_wishlist));
        productWishlistButton.setAttribute(
            "aria-label",
            result.in_wishlist ? "Remove from wishlist" : "Add to wishlist",
        );
        const icon = productWishlistButton.querySelector(".material-symbols-outlined");
        if (icon) icon.textContent = result.in_wishlist ? "favorite" : "favorite_border";
    } catch (error) {
        console.error("Unable to update the product wishlist.", error);
    } finally {
        productWishlistButton.disabled = false;
    }
});

const productCartStatus = document.querySelector(".product-cart-status");
document.querySelector(".product-add-to-cart")?.addEventListener("click", () => {
    productCartStatus.textContent = `${productQuantity} item${productQuantity === 1 ? "" : "s"} added to your bag.`;
});
document.querySelector(".product-buy-now")?.addEventListener("click", () => {
    productCartStatus.textContent = "Your item is ready for checkout.";
});

const productShareButton = document.querySelector(".product-share-button");
const productShareStatus = document.querySelector(".product-share-status");
productShareButton?.addEventListener("click", async () => {
    const shareData = {
        title: document.querySelector(".product-information h1").textContent.trim(),
        text: "Take a look at this handcrafted silver jewellery.",
        url: window.location.href,
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
            productShareStatus.textContent = "Product shared.";
        } catch (error) {
            if (error.name !== "AbortError") {
                productShareStatus.textContent = "Unable to share this product.";
            }
        }
        return;
    }

    if (navigator.clipboard?.writeText) {
        try {
            await navigator.clipboard.writeText(shareData.url);
            productShareStatus.textContent = "Product link copied to clipboard.";
        } catch {
            productShareStatus.textContent = "Unable to copy the product link.";
        }
        return;
    }

    productShareStatus.textContent = "Sharing is not available in this browser.";
});

// PRODUCT REVIEWS
const reviewSection = document.querySelector(".customer-reviews");
const reviewStorageKey = `jk-silver-product-reviews-${reviewSection?.dataset.productId || "unknown"}`;
const reviewForm = document.querySelector(".review-form");
const reviewFormStatus = document.querySelector(".review-form-status");
const reviewList = document.querySelector(".review-list");

if (reviewSection && reviewForm && reviewFormStatus && reviewList) {
function isValidProductReview(review) {
    return review &&
        typeof review.name === "string" &&
        typeof review.text === "string" &&
        Number.isInteger(review.rating) &&
        review.rating >= 1 &&
        review.rating <= 5 &&
        typeof review.date === "string" &&
        Number.isFinite(Date.parse(review.date));
}

function loadProductReviews() {
    try {
        const savedReviews = JSON.parse(localStorage.getItem(reviewStorageKey) || "[]");
        if (!Array.isArray(savedReviews) || !savedReviews.every(isValidProductReview)) {
            throw new Error("Saved product reviews have an invalid format.");
        }
        return savedReviews;
    } catch (error) {
        reviewFormStatus.textContent = "Saved reviews could not be loaded in this browser.";
        reviewFormStatus.classList.add("is-error");
        console.error("Unable to load product reviews.", error);
        return [];
    }
}

const serverReviews = JSON.parse(reviewSection.dataset.serverReviews || "[]")
    .filter(isValidProductReview);
let browserReviews = loadProductReviews();
let productReviews = [...serverReviews, ...browserReviews];

function renderProductReviews() {
    const reviewCount = productReviews.length;
    const ratingTotal = productReviews.reduce((total, review) => total + review.rating, 0);
    const average = reviewCount ? ratingTotal / reviewCount : 0;
    const roundedAverage = Math.round(average);
    const stars = "★".repeat(roundedAverage) + "☆".repeat(5 - roundedAverage);
    const ratingLabel = reviewCount ? `Rated ${average.toFixed(1)} out of 5` : "No ratings yet";

    document.querySelector("[data-product-rating-stars]").textContent = stars;
    document.querySelector("[data-product-rating-stars]").setAttribute("aria-label", ratingLabel);
    document.querySelector("[data-product-rating-average]").textContent = reviewCount ? average.toFixed(1) : "—";
    document.querySelector(".product-review-count").textContent =
        `(${reviewCount} ${reviewCount === 1 ? "review" : "reviews"})`;
    document.querySelector("[data-reviews-average]").textContent = reviewCount ? average.toFixed(1) : "—";
    document.querySelector("[data-reviews-stars]").textContent = stars;
    document.querySelector("[data-reviews-stars]").setAttribute("aria-label", ratingLabel);
    document.querySelector("[data-reviews-total]").textContent =
        `${reviewCount} ${reviewCount === 1 ? "review" : "reviews"}`;
    document.querySelector("[data-review-list-count]").textContent = `(${reviewCount})`;

    for (let rating = 1; rating <= 5; rating += 1) {
        const count = productReviews.filter((review) => review.rating === rating).length;
        const fill = document.querySelector(`[data-rating-fill="${rating}"]`);
        fill.style.width = reviewCount ? `${(count / reviewCount) * 100}%` : "0";
        document.querySelector(`[data-rating-count="${rating}"]`).textContent = String(count);
    }

    reviewList.replaceChildren();
    if (!reviewCount) {
        const emptyState = document.createElement("p");
        emptyState.className = "reviews-empty-state";
        emptyState.textContent = "No reviews yet. Be the first to share your experience.";
        reviewList.append(emptyState);
        return;
    }

    [...productReviews].reverse().forEach((review) => {
        const card = document.createElement("article");
        card.className = "review-card";

        const header = document.createElement("div");
        header.className = "review-card-header";
        const name = document.createElement("span");
        name.className = "review-card-name";
        name.textContent = review.name;
        const date = document.createElement("time");
        date.className = "review-card-date";
        date.dateTime = new Date(review.date).toISOString();
        date.textContent = new Date(review.date).toLocaleDateString(undefined, {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
        header.append(name, date);

        const starsElement = document.createElement("div");
        starsElement.className = "review-card-stars";
        starsElement.setAttribute("aria-label", `Rated ${review.rating} out of 5`);
        starsElement.textContent = "★".repeat(review.rating) + "☆".repeat(5 - review.rating);

        const text = document.createElement("p");
        text.className = "review-card-text";
        text.textContent = review.text;
        card.append(header, starsElement, text);
        reviewList.append(card);
    });
}

renderProductReviews();

reviewForm.addEventListener("submit", (event) => {
    event.preventDefault();
    reviewFormStatus.classList.remove("is-error");

    const formData = new FormData(reviewForm);
    const name = formData.get("name").trim();
    const text = formData.get("review").trim();
    const rating = Number(formData.get("rating"));

    if (!name || !text || !Number.isInteger(rating) || rating < 1 || rating > 5) {
        reviewFormStatus.textContent = "Please enter your name, select a star rating, and write your review.";
        reviewFormStatus.classList.add("is-error");
        return;
    }

    const review = { name, rating, text, date: new Date().toISOString() };
    const updatedReviews = [...browserReviews, review];
    try {
        localStorage.setItem(reviewStorageKey, JSON.stringify(updatedReviews));
    } catch (error) {
        reviewFormStatus.textContent = "Your review could not be saved. Please check your browser storage and try again.";
        reviewFormStatus.classList.add("is-error");
        console.error("Unable to save product review.", error);
        return;
    }

    browserReviews = updatedReviews;
    productReviews = [...serverReviews, ...browserReviews];
    renderProductReviews();
    reviewForm.reset();
    reviewFormStatus.textContent = "Thank you! Your review has been added.";
    document.querySelector(".customer-reviews").scrollIntoView({ behavior: "smooth", block: "start" });
});
}