/* ══════════════════════════════════════════
   NEXUS STORE — Main JS v3
══════════════════════════════════════════ */

/* ── CART ─────────────────────────────── */
const Cart = {
    get() {
        try {
            return JSON.parse(localStorage.getItem("nx_cart") || "[]");
        } catch {
            return [];
        }
    },
    save(c) {
        localStorage.setItem("nx_cart", JSON.stringify(c));
        Cart.updateUI();
    },
    add(item) {
        const c = Cart.get();
        const key = `${item.id}_${item.variant || ""}`;
        const idx = c.findIndex((i) => `${i.id}_${i.variant || ""}` === key);
        if (idx >= 0) c[idx].qty = Math.min(c[idx].qty + (item.qty || 1), 99);
        else c.push({ ...item, qty: item.qty || 1 });
        Cart.save(c);
        Toast.show(`Đã thêm "${item.name}" vào giỏ hàng`, "success");
    },
    remove(id, variant) {
        const key = `${id}_${variant || ""}`;
        Cart.save(
            Cart.get().filter((i) => `${i.id}_${i.variant || ""}` !== key),
        );
    },
    updateQty(id, variant, qty) {
        const c = Cart.get();
        const key = `${id}_${variant || ""}`;
        const idx = c.findIndex((i) => `${i.id}_${i.variant || ""}` === key);
        if (idx >= 0) {
            if (qty <= 0) c.splice(idx, 1);
            else c[idx].qty = qty;
        }
        Cart.save(c);
    },
    total() {
        return Cart.get().reduce((s, i) => s + i.price * i.qty, 0);
    },
    count() {
        return Cart.get().reduce((s, i) => s + i.qty, 0);
    },
    updateUI() {
        const n = Cart.count();
        document.querySelectorAll(".js-cart-count").forEach((el) => {
            el.textContent = n;
            el.style.display = n > 0 ? "flex" : "none";
        });
    },
};

/* ── WISHLIST ────────────────────────── */
const Wishlist = {
    get() {
        try {
            return JSON.parse(localStorage.getItem("nx_wish") || "[]");
        } catch {
            return [];
        }
    },
    save(w) {
        localStorage.setItem("nx_wish", JSON.stringify(w));
        Wishlist.updateUI();
    },
    toggle(id, name, price, img, slug) {
        const w = Wishlist.get();
        const sid = String(id);
        const idx = w.findIndex((i) => String(i.id) === sid);
        if (idx >= 0) {
            w.splice(idx, 1);
            Toast.show(`Đã xoá khỏi yêu thích`, "info");
        } else {
            w.push({
                id: sid,
                name: name || "Sản phẩm",
                price: price || 0,
                img: img || "",
                slug: slug || "",
            });
            Toast.show(`Đã thêm vào yêu thích`, "success");
        }
        Wishlist.save(w);
    },
    has(id) {
        return Wishlist.get().some((i) => String(i.id) === String(id));
    },
    count() {
        return Wishlist.get().length;
    },
    updateUI() {
        const n = Wishlist.count();
        document.querySelectorAll(".js-wish-count").forEach((el) => {
            el.textContent = n;
            el.style.display = n > 0 ? "flex" : "none";
        });
        document.querySelectorAll("[data-wish-id]").forEach((btn) => {
            btn.classList.toggle("active", Wishlist.has(btn.dataset.wishId));
        });
    },
};

/* ── TOAST ───────────────────────────── */
const Toast = {
    container: null,
    icons: {
        success:
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>',
        error: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
        info: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
        warning:
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    },
    init() {
        if (!Toast.container) {
            Toast.container = document.createElement("div");
            Toast.container.className = "toast-wrap";
            document.body.appendChild(Toast.container);
        }
    },
    show(msg, type = "success", duration = 3200) {
        Toast.init();
        const el = document.createElement("div");
        el.className = `toast toast--${type}`;
        el.innerHTML = `
      <span class="toast__icon">${Toast.icons[type] || Toast.icons.info}</span>
      <span class="toast__msg">${msg}</span>
      <button class="toast__close" onclick="this.parentElement.remove()" aria-label="Đóng">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>`;
        Toast.container.appendChild(el);
        setTimeout(() => {
            el.style.opacity = "0";
            el.style.transform = "translateX(10px)";
            el.style.transition = ".25s ease";
            setTimeout(() => el.remove(), 260);
        }, duration);
    },
};

/* ── EVENT DELEGATION — WISHLIST ─────── */
function initWishlistButtons() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest("[data-wish-id]");
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const id = btn.dataset.wishId;
        const name = btn.dataset.wishName || "Sản phẩm";
        const price = parseInt(btn.dataset.wishPrice || "0");
        const img = btn.dataset.wishImg || "";
        const slug = btn.dataset.wishSlug || ""; // ← THÊM
        Wishlist.toggle(id, name, price, img, slug);
    });
}

/* ── NAVBAR ──────────────────────────── */
function initNavbar() {
    const nav = document.querySelector(".navbar");
    if (!nav) return;
    window.addEventListener(
        "scroll",
        () => {
            nav.classList.toggle("scrolled", scrollY > 10);
        },
        { passive: true },
    );
    Cart.updateUI();
    Wishlist.updateUI();
    const path = location.pathname;
    document.querySelectorAll(".navbar__nav-link").forEach((a) => {
        try {
            if (new URL(a.href).pathname === path) a.classList.add("active");
        } catch (e) {}
    });
}

/* ── REVEAL ANIMATION ────────────────── */
function initReveal() {
    if (!("IntersectionObserver" in window)) {
        document
            .querySelectorAll(".reveal")
            .forEach((el) => el.classList.add("visible"));
        return;
    }
    const obs = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add("visible");
                    obs.unobserve(e.target);
                }
            });
        },
        { threshold: 0.08 },
    );
    document.querySelectorAll(".reveal").forEach((el) => obs.observe(el));
}

/* ── FORMAT HELPERS ──────────────────── */
function fmtPrice(n) {
    return n.toLocaleString("vi-VN") + "₫";
}

/* ── INIT ────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
    initNavbar();
    initReveal();
    initWishlistButtons();
});

/* ── EXPORTS ─────────────────────────── */
window.Cart = Cart;
window.Wishlist = Wishlist;
window.Toast = Toast;
window.fmtPrice = fmtPrice;
