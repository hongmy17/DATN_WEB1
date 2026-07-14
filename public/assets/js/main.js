/* ══════════════════════════════════════════
   NEXUS STORE — Main JS v5
   Cart: localStorage (guest) + DB sync (auth)
   Key: variant_id (số nguyên) — không bao giờ nhầm
══════════════════════════════════════════ */

/* ── API helper ──────────────────────── */
function cartApi(method, path, body) {
    const opts = {
        method,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN":
                document.querySelector('meta[name="csrf-token"]')?.content ??
                "",
        },
    };
    if (body) opts.body = JSON.stringify(body);
    return fetch("/api/cart" + path, opts)
        .then((r) => r.json())
        .catch(() => ({ success: false, message: "Lỗi kết nối" }));
}

/* ── CART ─────────────────────────────── */
const Cart = {
    get() {
        try {
            return JSON.parse(localStorage.getItem("nx_cart") || "[]");
        } catch {
            return [];
        }
    },
    _save(c) {
        localStorage.setItem("nx_cart", JSON.stringify(c));
        Cart.updateUI();
    },
    _idx(variantId) {
        return Cart.get().findIndex(
            (i) => String(i.variant_id) === String(variantId),
        );
    },

    /* ── THÊM ── */
    async add(item) {
        if (!item.variant_id) {
            Toast.show("Không thể thêm: thiếu thông tin biến thể", "error");
            return;
        }
        const qty = item.qty || 1;

        if (window.__authUser) {
            const res = await cartApi("POST", "", {
                variant_id: item.variant_id,
                quantity: qty,
            });
            if (!res.success) {
                Toast.show(res.message ?? "Thêm vào giỏ thất bại", "error");
                return;
            }
            const c = Cart.get();
            const idx = Cart._idx(item.variant_id);
            if (idx >= 0) {
                c[idx].qty = res.data.qty;
                c[idx].cart_item_id = res.data.cart_item_id;
                c[idx].price = res.data.price; // FIX: luôn lấy giá thật từ server, không tin giá client truyền vào
            } else {
                c.push({ ...item, ...res.data }); // FIX: res.data đè lên item, đảm bảo price/qty/cart_item_id đều là dữ liệu server trả về
            }
            Cart._save(c);
        } else {
            // Khách chưa đăng nhập: giỏ hàng lưu localStorage, không có API để lấy giá chuẩn
            // → phần này vẫn phụ thuộc vào "price" client truyền vào, xem lưu ý bên dưới
            const c = Cart.get();
            const idx = Cart._idx(item.variant_id);
            if (idx >= 0) c[idx].qty = Math.min(c[idx].qty + qty, 99);
            else c.push({ ...item, qty });
            Cart._save(c);
        }
        Toast.show(`Đã thêm "${item.name}" vào giỏ hàng`, "success");
    },

    /* ── XOÁ 1 ── */
    async removeByVariantId(variantId) {
        const c = Cart.get();
        const idx = Cart._idx(variantId);
        if (idx < 0) return;
        const item = c[idx];
        if (window.__authUser && item.cart_item_id) {
            const res = await cartApi("DELETE", `/${item.cart_item_id}`);
            if (!res.success) {
                Toast.show(
                    res.message || "Xoá thất bại, vui lòng thử lại.",
                    "error",
                );
                return;
            }
        }
        c.splice(idx, 1);
        Cart._save(c);
    },

    /* ── CẬP NHẬT SỐ LƯỢNG ── */
    async updateQty(variantId, qty) {
        if (qty <= 0) {
            await Cart.removeByVariantId(variantId);
            return;
        }
        const c = Cart.get();
        const idx = Cart._idx(variantId);
        if (idx < 0) return;
        if (window.__authUser && c[idx].cart_item_id) {
            const res = await cartApi("PATCH", `/${c[idx].cart_item_id}`, {
                quantity: qty,
            });
            // FIX: trước đây bỏ qua res.message, luôn hiện cứng "Cập nhật thất bại"
            // chung chung — khách không biết vì sao. Backend đã trả sẵn lý do rõ ràng
            // (VD "Chỉ còn 3 sản phẩm trong kho."), giờ ưu tiên hiện đúng message đó.
            if (!res.success) {
                Toast.show(
                    res.message || "Cập nhật thất bại, vui lòng thử lại.",
                    "error",
                );
                return;
            }
        }
        c[idx].qty = qty;
        Cart._save(c);
    },

    /* ── XOÁ TẤT CẢ ── */
    async clearAll() {
        if (window.__authUser) {
            await cartApi("DELETE", "");
        }
        localStorage.removeItem("nx_cart");
        Cart.updateUI();
    },

    /* ── XOÁ LOCAL (khi đăng xuất) ── */
    clearLocal() {
        localStorage.removeItem("nx_cart");
        Cart.updateUI();
    },

    /* ── SYNC lên server sau khi đăng nhập ── */
    async syncToServer() {
        const local = Cart.get();
        if (!local.length) {
            await Cart.loadFromServer();
            return;
        }
        const res = await cartApi("POST", "/sync", {
            items: local.map((i) => ({
                variant_id: i.variant_id,
                quantity: i.qty,
            })),
        });
        if (res.success) Cart._save(Cart._mapServerItems(res.data));
    },

    /* ── LOAD từ server (local trống) ── */
    async loadFromServer() {
        const res = await cartApi("GET", "");
        if (res.success) Cart._save(Cart._mapServerItems(res.data));
    },

    /* ── Map data server → format localStorage ── */
    _mapServerItems(data) {
        return data.map((d) => ({
            cart_item_id: d.cart_item_id,
            variant_id: d.variant_id,
            id: d.id,
            name: d.name,
            variant: d.variant,
            price: d.price,
            qty: d.qty,
            img: d.img,
        }));
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
            Toast.show("Đã xoá khỏi yêu thích", "info");
        } else {
            w.push({
                id: sid,
                name: name || "Sản phẩm",
                price: price || 0,
                img: img || "",
                slug: slug || "",
            });
            Toast.show("Đã thêm vào yêu thích", "success");
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

/* ── WISHLIST BUTTONS ────────────────── */
function initWishlistButtons() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest("[data-wish-id]");
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        Wishlist.toggle(
            btn.dataset.wishId,
            btn.dataset.wishName || "Sản phẩm",
            parseInt(btn.dataset.wishPrice || "0"),
            btn.dataset.wishImg || "",
            btn.dataset.wishSlug || "",
        );
    });
}

/* ── NAVBAR ──────────────────────────── */
function initNavbar() {
    const nav = document.querySelector(".navbar");
    if (!nav) return;
    window.addEventListener(
        "scroll",
        () => nav.classList.toggle("scrolled", scrollY > 10),
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

/* ── REVEAL ──────────────────────────── */
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

function fmtPrice(n) {
    return n.toLocaleString("vi-VN") + "₫";
}

/* ── INIT ────────────────────────────── */
document.addEventListener("DOMContentLoaded", async () => {
    initNavbar();
    initReveal();
    initWishlistButtons();
    if (window.__authUser) {
        await Cart.syncToServer();
        Cart.updateUI();
    }
});

window.Cart = Cart;
window.Wishlist = Wishlist;
window.Toast = Toast;
window.fmtPrice = fmtPrice;
