/* ══════════════════════════════════════════
   NEXUS STORE — Main JS
══════════════════════════════════════════ */

// ── CART ─────────────────────────────────
const Cart = {
  get() { try { return JSON.parse(localStorage.getItem('nx_cart') || '[]'); } catch { return []; } },
  save(c) { localStorage.setItem('nx_cart', JSON.stringify(c)); Cart.updateUI(); },
  add(item) {
    const c = Cart.get();
    const key = `${item.id}_${item.variant||''}`;
    const idx = c.findIndex(i => `${i.id}_${i.variant||''}` === key);
    if (idx >= 0) c[idx].qty = Math.min(c[idx].qty + (item.qty||1), 99);
    else c.push({ ...item, qty: item.qty||1 });
    Cart.save(c);
    Toast.show(`Đã thêm "${item.name}" vào giỏ hàng`, 'success');
  },
  remove(id, variant) {
    const key = `${id}_${variant||''}`;
    Cart.save(Cart.get().filter(i => `${i.id}_${i.variant||''}` !== key));
  },
  updateQty(id, variant, qty) {
    const c = Cart.get();
    const key = `${id}_${variant||''}`;
    const idx = c.findIndex(i => `${i.id}_${i.variant||''}` === key);
    if (idx >= 0) { if (qty <= 0) c.splice(idx,1); else c[idx].qty = qty; }
    Cart.save(c);
  },
  total() { return Cart.get().reduce((s,i) => s + i.price * i.qty, 0); },
  count() { return Cart.get().reduce((s,i) => s + i.qty, 0); },
  updateUI() {
    const n = Cart.count();
    document.querySelectorAll('.js-cart-count').forEach(el => {
      el.textContent = n;
      el.style.display = n > 0 ? 'flex' : 'none';
    });
  }
};

// ── WISHLIST ──────────────────────────────
const Wishlist = {
  get() { try { return JSON.parse(localStorage.getItem('nx_wish') || '[]'); } catch { return []; } },
  save(w) { localStorage.setItem('nx_wish', JSON.stringify(w)); Wishlist.updateUI(); },
  toggle(id, name) {
    const w = Wishlist.get();
    const idx = w.indexOf(String(id));
    if (idx >= 0) { w.splice(idx,1); Toast.show(`Đã xóa khỏi yêu thích`, 'info'); }
    else { w.push(String(id)); Toast.show(`Đã thêm "${name}" vào yêu thích ♥`, 'success'); }
    Wishlist.save(w);
    return idx < 0;
  },
  has(id) { return Wishlist.get().includes(String(id)); },
  count() { return Wishlist.get().length; },
  updateUI() {
    const n = Wishlist.count();
    document.querySelectorAll('.js-wish-count').forEach(el => {
      el.textContent = n;
      el.style.display = n > 0 ? 'flex' : 'none';
    });
    document.querySelectorAll('[data-wish-id]').forEach(btn => {
      const id = btn.dataset.wishId;
      btn.classList.toggle('active', Wishlist.has(id));
    });
  }
};

// ── TOAST ─────────────────────────────────
const Toast = {
  container: null,
  init() {
    if (!Toast.container) {
      Toast.container = document.createElement('div');
      Toast.container.className = 'toast-wrap';
      document.body.appendChild(Toast.container);
    }
  },
  show(msg, type = 'success', duration = 3200) {
    Toast.init();
    const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠' };
    const el = document.createElement('div');
    el.className = `toast toast--${type}`;
    el.innerHTML = `<span class="toast__icon">${icons[type]||'ℹ'}</span><span class="toast__msg">${msg}</span><button class="toast__close" onclick="this.parentElement.remove()">×</button>`;
    Toast.container.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(110%)'; el.style.transition = '.3s ease'; setTimeout(() => el.remove(), 300); }, duration);
  }
};

// ── FORMAT ────────────────────────────────
function fmtPrice(n) { return n.toLocaleString('vi-VN') + '₫'; }
function fmtDiscount(orig, sale) { return Math.round((1 - sale/orig)*100) + '%'; }

// ── NAVBAR ────────────────────────────────
function initNavbar() {
  const nav = document.querySelector('.navbar');
  if (!nav) return;
  window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 10), { passive: true });
  document.querySelector('.navbar__hamburger')?.addEventListener('click', () => document.body.classList.toggle('nav-open'));
  Cart.updateUI();
  Wishlist.updateUI();
  // Mark active link
  const path = location.pathname.split('/').pop();
  nav.querySelectorAll('.navbar__nav-link').forEach(a => {
    if (a.getAttribute('href') && a.getAttribute('href').split('/').pop() === path) a.classList.add('active');
  });
}

// ── REVEAL ON SCROLL ──────────────────────
function initReveal() {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), i * 60);
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  els.forEach(el => io.observe(el));
}

// ── QTY CONTROL ───────────────────────────
function initQtyControls() {
  document.querySelectorAll('.qty-ctrl').forEach(wrap => {
    const inp = wrap.querySelector('.qty-ctrl__input');
    if (!inp) return;
    wrap.querySelector('.qty-ctrl__btn--minus')?.addEventListener('click', () => {
      const v = Math.max(1, parseInt(inp.value||1) - 1);
      inp.value = v; inp.dispatchEvent(new Event('change'));
    });
    wrap.querySelector('.qty-ctrl__btn--plus')?.addEventListener('click', () => {
      const max = parseInt(inp.max||99);
      const v = Math.min(max, parseInt(inp.value||1) + 1);
      inp.value = v; inp.dispatchEvent(new Event('change'));
    });
  });
}

// ── WISHLIST BUTTONS ──────────────────────
function initWishBtns() {
  document.querySelectorAll('[data-wish-id]').forEach(btn => {
    if (btn.dataset.wishInited) return;
    btn.dataset.wishInited = '1';
    btn.classList.toggle('active', Wishlist.has(btn.dataset.wishId));
    btn.addEventListener('click', (e) => {
      e.preventDefault(); e.stopPropagation();
      const added = Wishlist.toggle(btn.dataset.wishId, btn.dataset.wishName||'Sản phẩm');
      btn.classList.toggle('active', added);
    });
  });
}

// ── MODAL ─────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('open'); document.body.style.overflow = '';
  }
});

// ── COUNTDOWN ─────────────────────────────
function initCountdown(targetDate, elId) {
  const el = document.getElementById(elId);
  if (!el) return;
  function tick() {
    const d = new Date(targetDate) - Date.now();
    if (d <= 0) { el.textContent = 'Đã kết thúc'; return; }
    const h = String(Math.floor(d/3.6e6)).padStart(2,'0');
    const m = String(Math.floor(d%3.6e6/6e4)).padStart(2,'0');
    const s = String(Math.floor(d%6e4/1e3)).padStart(2,'0');
    el.innerHTML = `<span class="cd__unit"><b>${h}</b><small>Giờ</small></span><span class="cd__sep">:</span><span class="cd__unit"><b>${m}</b><small>Phút</small></span><span class="cd__sep">:</span><span class="cd__unit"><b>${s}</b><small>Giây</small></span>`;
  }
  tick(); setInterval(tick, 1000);
}

// ── SEARCH ────────────────────────────────
function initSearch() {
  document.querySelectorAll('.navbar__search-input').forEach(inp => {
    inp.addEventListener('keydown', e => {
      if (e.key === 'Enter' && inp.value.trim()) {
        location.href = `san-pham.html?q=${encodeURIComponent(inp.value.trim())}`;
      }
    });
  });
}

// ── TABS ──────────────────────────────────
function initTabs(containerSelector) {
  document.querySelectorAll(containerSelector || '[data-tabs]').forEach(container => {
    const btns = container.querySelectorAll('[data-tab]');
    btns.forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        btns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        container.querySelectorAll('[data-tab-panel]').forEach(p => {
          p.style.display = p.dataset.tabPanel === target ? '' : 'none';
        });
      });
    });
  });
}

// ── INIT ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initReveal();
  initQtyControls();
  initWishBtns();
  initSearch();
  initTabs();
});

