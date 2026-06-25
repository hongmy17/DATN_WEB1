@extends('layouts.app')

@section('title', 'MacBook Pro 14" M3 Pro — Nexus Store')

@push('styles')
    <style>
        /* ── LAYOUT ─────────────────────────────── */
        .pdp-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .pdp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: start;
            padding: 40px 0 64px;
        }

        /* ── GALLERY ────────────────────────────── */
        .gallery {
            position: sticky;
            top: 88px;
        }

        .gallery__main {
            position: relative;
            background: #F8F8F7;
            border: 1px solid #E8E6E1;
            border-radius: 16px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .gallery__main-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: #B0ADA6;
        }

        .gallery__main-placeholder svg {
            opacity: .35;
        }

        .gallery__main-placeholder span {
            font-size: 13px;
        }

        .gallery__badges {
            position: absolute;
            top: 14px;
            left: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .badge-discount {
            background: #E53E3E;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .badge-new {
            background: #1A1A1A;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 99px;
        }

        .gallery__wish {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #E8E6E1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s;
        }

        .gallery__wish:hover {
            background: #FFF0EE;
        }

        .gallery__wish svg {
            transition: fill .15s;
        }

        .gallery__wish.active svg {
            fill: #E53E3E;
            stroke: #E53E3E;
        }

        .gallery__thumbs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .gallery__thumb {
            aspect-ratio: 1;
            background: #F8F8F7;
            border: 1.5px solid #E8E6E1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color .15s;
            color: #9A9790;
        }

        .gallery__thumb:hover {
            border-color: #9A9790;
        }

        .gallery__thumb.active {
            border-color: #1A1A1A;
            background: #fff;
        }

        /* ── PRODUCT INFO ───────────────────────── */
        .pdp-info {}

        .pdp-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #9A9790;
            margin-bottom: 8px;
        }

        .pdp-breadcrumb a {
            color: #9A9790;
            text-decoration: none;
        }

        .pdp-breadcrumb a:hover {
            color: #1A1A1A;
        }

        .pdp-breadcrumb svg {
            flex-shrink: 0;
        }

        .pdp-brand {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .pdp-name {
            font-family: var(--font-display);
            font-size: clamp(22px, 3vw, 30px);
            font-weight: 700;
            color: #1A1A1A;
            line-height: 1.2;
            letter-spacing: -.3px;
            margin-bottom: 16px;
        }

        .pdp-rating {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid #EDEBE6;
            margin-bottom: 20px;
        }

        .stars {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .stars svg {
            color: #F59E0B;
        }

        .pdp-rating__score {
            font-size: 14px;
            font-weight: 600;
            color: #1A1A1A;
        }

        .pdp-rating__count {
            font-size: 13px;
            color: #9A9790;
        }

        .pdp-rating__sep {
            width: 1px;
            height: 14px;
            background: #EDEBE6;
        }

        .pdp-stock {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #16A34A;
            font-weight: 500;
        }

        /* ── PRICE ──────────────────────────────── */
        .price-block {
            background: #FAFAF9;
            border: 1px solid #EDEBE6;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }

        .price-block__row {
            display: flex;
            align-items: baseline;
            gap: 12px;
            flex-wrap: wrap;
        }

        .price-block__current {
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
            color: #1A1A1A;
            letter-spacing: -.5px;
        }

        .price-block__old {
            font-size: 16px;
            color: #B0ADA6;
            text-decoration: line-through;
        }

        .price-block__save {
            background: #FEF2F2;
            color: #DC2626;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .price-block__installment {
            margin-top: 8px;
            font-size: 12px;
            color: #6B6965;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── OPTIONS ────────────────────────────── */
        .option-section {
            margin-bottom: 20px;
        }

        .option-header {
            font-size: 12px;
            font-weight: 600;
            color: #6B6965;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .option-header__value {
            color: #1A1A1A;
            text-transform: none;
            letter-spacing: 0;
        }

        .color-swatches {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .color-swatch {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            position: relative;
        }

        .color-swatch::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color .15s;
        }

        .color-swatch.active::after {
            border-color: #1A1A1A;
        }

        .color-swatch:hover {
            transform: scale(1.08);
        }

        .size-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-chip {
            min-width: 64px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1.5px solid #EDEBE6;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s, color .15s;
            background: #fff;
            color: #1A1A1A;
        }

        .size-chip:hover {
            border-color: #9A9790;
        }

        .size-chip.active {
            background: #1A1A1A;
            border-color: #1A1A1A;
            color: #fff;
        }

        .size-chip.out {
            opacity: .4;
            pointer-events: none;
            text-decoration: line-through;
        }

        /* ── QTY ────────────────────────────────── */
        .qty-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .qty-ctrl {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #EDEBE6;
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-ctrl__btn {
            width: 38px;
            height: 40px;
            background: #F8F8F7;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            border: none;
            color: #1A1A1A;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .qty-ctrl__btn:hover {
            background: #EDEBE6;
        }

        .qty-ctrl__input {
            width: 48px;
            height: 40px;
            text-align: center;
            border: none;
            border-left: 1.5px solid #EDEBE6;
            border-right: 1.5px solid #EDEBE6;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-body);
            background: #fff;
            color: #1A1A1A;
        }

        .qty-stock-note {
            font-size: 12px;
            color: #9A9790;
        }

        /* ── CTA BUTTONS ────────────────────────── */
        .cta-row {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .btn-cart {
            flex: 1;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #1A1A1A;
            color: #fff;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background .15s;
            font-family: var(--font-body);
        }

        .btn-cart:hover {
            background: #333;
        }

        .btn-buy {
            flex: 1;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: #fff;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity .15s;
            font-family: var(--font-body);
        }

        .btn-buy:hover {
            opacity: .88;
        }

        /* ── TRUST BADGES ───────────────────────── */
        .trust-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            padding-top: 20px;
            border-top: 1px solid #EDEBE6;
        }

        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 8px;
            background: #FAFAF9;
            border-radius: 10px;
            text-align: center;
        }

        .trust-item__icon {
            color: #6B6965;
        }

        .trust-item__text {
            font-size: 11px;
            font-weight: 500;
            color: #6B6965;
            line-height: 1.3;
        }

        /* ── DELIVERY INFO ──────────────────────── */
        .delivery-box {
            margin-top: 16px;
            border: 1px solid #EDEBE6;
            border-radius: 10px;
            overflow: hidden;
        }

        .delivery-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid #EDEBE6;
            font-size: 13px;
        }

        .delivery-row:last-child {
            border-bottom: none;
        }

        .delivery-row__icon {
            color: #6B6965;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .delivery-row__label {
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 2px;
        }

        .delivery-row__sub {
            color: #9A9790;
            font-size: 12px;
        }

        /* ── TABS ───────────────────────────────── */
        .pdp-tabs {
            margin-top: 64px;
            border-top: 1px solid #EDEBE6;
        }

        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 1px solid #EDEBE6;
            overflow-x: auto;
        }

        .tab-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 14px 20px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            font-weight: 500;
            color: #9A9790;
            cursor: pointer;
            white-space: nowrap;
            transition: color .15s;
            font-family: var(--font-body);
        }

        .tab-btn:hover {
            color: #1A1A1A;
        }

        .tab-btn.active {
            color: #1A1A1A;
            border-bottom-color: #1A1A1A;
            font-weight: 600;
        }

        .tab-btn svg {
            flex-shrink: 0;
        }

        .tab-panel {
            display: none;
            padding: 32px 0;
        }

        .tab-panel.active {
            display: block;
        }

        /* ── SPECS TABLE ────────────────────────── */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .specs-table tr {
            border-bottom: 1px solid #EDEBE6;
        }

        .specs-table tr:last-child {
            border-bottom: none;
        }

        .specs-table td {
            padding: 13px 16px;
            vertical-align: top;
            line-height: 1.5;
        }

        .specs-table td:first-child {
            width: 190px;
            font-weight: 500;
            color: #6B6965;
            background: #FAFAF9;
        }

        .specs-table td:last-child {
            color: #1A1A1A;
        }

        .specs-table tr:nth-child(even) td:first-child {
            background: #F5F3EE;
        }

        /* ── DESCRIPTION ────────────────────────── */
        .desc-content {
            font-size: 14px;
            line-height: 1.8;
            color: #4B4A47;
        }

        .desc-content p {
            margin-bottom: 16px;
        }

        .desc-content ul {
            margin: 0 0 16px 20px;
        }

        .desc-content li {
            margin-bottom: 6px;
        }

        .desc-content h3 {
            font-family: var(--font-display);
            font-size: 17px;
            font-weight: 600;
            color: #1A1A1A;
            margin: 24px 0 10px;
        }

        .desc-highlight {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .desc-hi-item {
            background: #FAFAF9;
            border: 1px solid #EDEBE6;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .desc-hi-item__num {
            font-family: var(--font-display);
            font-size: 24px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 4px;
        }

        .desc-hi-item__label {
            font-size: 12px;
            color: #9A9790;
        }

        /* ── REVIEWS ────────────────────────────── */
        .review-overview {
            display: flex;
            gap: 40px;
            align-items: center;
            background: #FAFAF9;
            border: 1px solid #EDEBE6;
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 28px;
        }

        .review-score__num {
            font-family: var(--font-display);
            font-size: 52px;
            font-weight: 700;
            color: #1A1A1A;
            line-height: 1;
        }

        .review-score__stars {
            display: flex;
            gap: 3px;
            margin: 6px 0 4px;
        }

        .review-score__label {
            font-size: 12px;
            color: #9A9790;
        }

        .review-bars {
            flex: 1;
        }

        .review-bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .review-bar-row__label {
            font-size: 12px;
            color: #6B6965;
            width: 14px;
            text-align: right;
            flex-shrink: 0;
        }

        .review-bar-track {
            flex: 1;
            height: 6px;
            background: #EDEBE6;
            border-radius: 99px;
            overflow: hidden;
        }

        .review-bar-fill {
            height: 100%;
            background: #F59E0B;
            border-radius: 99px;
        }

        .review-bar-row__count {
            font-size: 12px;
            color: #9A9790;
            width: 14px;
        }

        .review-list {
            border-top: 1px solid #EDEBE6;
        }

        .review-item {
            display: flex;
            gap: 14px;
            padding: 20px 0;
            border-bottom: 1px solid #EDEBE6;
        }

        .review-item:last-of-type {
            border-bottom: none;
        }

        .review-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1A1A1A;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .review-body {
            flex: 1;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .review-name {
            font-size: 14px;
            font-weight: 600;
            color: #1A1A1A;
        }

        .review-date {
            font-size: 12px;
            color: #B0ADA6;
        }

        .review-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 6px;
        }

        .review-text {
            font-size: 13px;
            color: #6B6965;
            line-height: 1.6;
            margin: 0;
        }

        .review-verified {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #16A34A;
            font-weight: 500;
            margin-left: 8px;
        }

        /* ── REVIEW FORM ────────────────────────── */
        .review-form-wrap {
            background: #FAFAF9;
            border: 1px solid #EDEBE6;
            border-radius: 14px;
            padding: 24px;
            margin-top: 28px;
        }

        .review-form-wrap h4 {
            font-size: 15px;
            font-weight: 600;
            color: #1A1A1A;
            margin-bottom: 16px;
        }

        .star-picker {
            display: flex;
            gap: 4px;
            margin-bottom: 14px;
            cursor: pointer;
        }

        .star-picker svg {
            transition: color .1s;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 500;
            color: #6B6965;
            display: block;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #EDEBE6;
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--font-body);
            color: #1A1A1A;
            background: #fff;
            transition: border-color .15s;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #9A9790;
            outline: none;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 88px;
        }

        .btn-submit-review {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 40px;
            padding: 0 20px;
            background: #1A1A1A;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: var(--font-body);
            transition: background .15s;
        }

        .btn-submit-review:hover {
            background: #333;
        }

        /* ── RELATED ────────────────────────────── */
        .related-section {
            margin-top: 64px;
            padding-top: 40px;
            border-top: 1px solid #EDEBE6;
        }

        .section-header {
            margin-bottom: 24px;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 4px;
        }

        .section-sub {
            font-size: 13px;
            color: #9A9790;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .related-card {
            background: #FAFAF9;
            border: 1px solid #EDEBE6;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: box-shadow .2s, transform .2s;
            text-decoration: none;
        }

        .related-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .related-card__img {
            aspect-ratio: 1;
            background: #F0EEE9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #C5C3BC;
        }

        .related-card__badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 99px;
            margin-bottom: 6px;
        }

        .related-card__badge--new {
            background: #1A1A1A;
            color: #fff;
        }

        .related-card__badge--hot {
            background: #FEF2F2;
            color: #DC2626;
        }

        .related-card__name {
            font-size: 13px;
            font-weight: 500;
            color: #1A1A1A;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .related-card__price {
            font-size: 15px;
            font-weight: 700;
            color: #1A1A1A;
            font-family: var(--font-display);
        }

        .related-card__price-old {
            font-size: 12px;
            color: #B0ADA6;
            text-decoration: line-through;
            margin-left: 6px;
        }

        /* ── RESPONSIVE ─────────────────────────── */
        @media (max-width: 1024px) {
            .pdp-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .gallery {
                position: static;
            }

            .related-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .desc-highlight {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 640px) {
            .pdp-grid {
                padding: 24px 0 40px;
            }

            .trust-grid {
                grid-template-columns: 1fr 1fr;
            }

            .desc-highlight {
                grid-template-columns: 1fr 1fr;
            }

            .review-overview {
                flex-direction: column;
                gap: 20px;
            }

            .related-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <div class="pdp-wrap">

        {{-- ── PRODUCT GRID ── --}}
        <div class="pdp-grid">

            {{-- GALLERY --}}
            <div class="gallery">
                <div class="gallery__main">

                    <div class="gallery__badges">
                        <span class="badge-discount">-11%</span>
                    </div>

                    <button class="gallery__wish" id="wishBtn" data-wish-id="1" data-wish-name="MacBook Pro 14&quot; M3 Pro"
                        data-wish-price="42990000" aria-label="Yêu thích">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </button>

                    {{-- Placeholder hình sản phẩm (thay bằng <img> khi có ảnh thật) --}}
                    <div class="gallery__main-placeholder" id="mainImg">
                        <svg width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                        <span>Ảnh sản phẩm</span>
                    </div>
                </div>

                <div class="gallery__thumbs">
                    @php
                        $thumbIcons = [
                            [
                                'label' => 'Chính diện',
                                'icon' =>
                                    '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
                            ],
                            [
                                'label' => 'Bên trái',
                                'icon' => '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M22 10H2"/>',
                            ],
                            [
                                'label' => 'Hộp',
                                'icon' =>
                                    '<polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/>',
                            ],
                            [
                                'label' => 'Phụ kiện',
                                'icon' =>
                                    '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
                            ],
                        ];
                    @endphp
                    @foreach ($thumbIcons as $i => $t)
                        <div class="gallery__thumb {{ $i === 0 ? 'active' : '' }}" onclick="selectThumb(this)"
                            title="{{ $t['label'] }}">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                {!! $t['icon'] !!}
                            </svg>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- INFO --}}
            <div class="pdp-info">

                <div class="pdp-brand">Apple</div>

                <h1 class="pdp-name">MacBook Pro 14" M3 Pro</h1>

                <div class="pdp-rating">
                    <div class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= 5 ? '#F59E0B' : 'none' }}"
                                stroke="#F59E0B" stroke-width="1.5">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        @endfor
                    </div>
                    <span class="pdp-rating__score">4.9</span>
                    <span class="pdp-rating__count">6 đánh giá</span>
                    <div class="pdp-rating__sep"></div>
                    <div class="pdp-stock">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Còn hàng
                    </div>
                </div>

                {{-- PRICE --}}
                <div class="price-block">
                    <div class="price-block__row">
                        <span class="price-block__current" id="currentPrice">42.990.000₫</span>
                        <span class="price-block__old">48.490.000₫</span>
                        <span class="price-block__save">Tiết kiệm 5.500.000₫</span>
                    </div>
                    <div class="price-block__installment">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" />
                            <line x1="1" y1="10" x2="23" y2="10" />
                        </svg>
                        Trả góp 0% lãi suất từ 3.582.000₫/tháng qua thẻ tín dụng
                    </div>
                </div>

                {{-- MÀU SẮC --}}
                <div class="option-section">
                    <div class="option-header">
                        Màu sắc:
                        <span class="option-header__value" id="selectedColor">Space Black</span>
                    </div>
                    <div class="color-swatches">
                        <div class="color-swatch active"
                            style="background:#1C1C1C; box-shadow: inset 0 0 0 1px rgba(255,255,255,.15)"
                            onclick="selectColor(this,'Space Black')" title="Space Black"></div>
                        <div class="color-swatch" style="background:#C8C8CA; box-shadow: inset 0 0 0 1px rgba(0,0,0,.08)"
                            onclick="selectColor(this,'Silver')" title="Silver"></div>
                        <div class="color-swatch" style="background:#F0EDE6; box-shadow: inset 0 0 0 1px rgba(0,0,0,.08)"
                            onclick="selectColor(this,'Starlight')" title="Starlight"></div>
                    </div>
                </div>

                {{-- DUNG LƯỢNG --}}
                <div class="option-section">
                    <div class="option-header">
                        Bộ nhớ trong:
                        <span class="option-header__value" id="selectedStorage">512GB</span>
                    </div>
                    <div class="size-chips">
                        <div class="size-chip active" onclick="selectChip(this,'selectedStorage','512GB','42.990.000₫')">
                            512GB</div>
                        <div class="size-chip" onclick="selectChip(this,'selectedStorage','1TB','52.990.000₫')">1TB</div>
                        <div class="size-chip" onclick="selectChip(this,'selectedStorage','2TB','65.990.000₫')">2TB</div>
                    </div>
                </div>

                {{-- RAM --}}
                <div class="option-section">
                    <div class="option-header">
                        Bộ nhớ RAM:
                        <span class="option-header__value" id="selectedRAM">18GB</span>
                    </div>
                    <div class="size-chips">
                        <div class="size-chip active" onclick="selectChip(this,'selectedRAM','18GB',null)">18GB</div>
                        <div class="size-chip" onclick="selectChip(this,'selectedRAM','36GB',null)">36GB</div>
                    </div>
                </div>

                {{-- SỐ LƯỢNG --}}
                <div class="qty-row">
                    <div class="qty-ctrl">
                        <button class="qty-ctrl__btn" onclick="changeQty(-1)" aria-label="Giảm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                        <input type="number" class="qty-ctrl__input" id="qtyInput" value="1" min="1"
                            max="10">
                        <button class="qty-ctrl__btn" onclick="changeQty(1)" aria-label="Tăng">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                    </div>
                    <span class="qty-stock-note">Còn 12 sản phẩm</span>
                </div>

                {{-- CTA --}}
                <div class="cta-row">
                    <button class="btn-cart"
                        onclick="Cart.add({id:1,name:'MacBook Pro 14 M3 Pro',price:42990000,img:''})">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1" />
                            <circle cx="20" cy="21" r="1" />
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                        </svg>
                        Thêm vào giỏ
                    </button>
                    <button class="btn-buy" onclick="Toast.show('Đang chuyển đến thanh toán...','info')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        Mua ngay
                    </button>
                </div>

                {{-- TRUST BADGES --}}
                <div class="trust-grid">
                    <div class="trust-item">
                        <div class="trust-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <div class="trust-item__text">Bảo hành<br>12 tháng</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10" />
                                <polyline points="23 20 23 14 17 14" />
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" />
                            </svg>
                        </div>
                        <div class="trust-item__text">Đổi mới<br>30 ngày</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-item__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13" rx="1" />
                                <path d="M16 8h4l3 3v4h-7V8z" />
                                <circle cx="5.5" cy="18.5" r="2.5" />
                                <circle cx="18.5" cy="18.5" r="2.5" />
                            </svg>
                        </div>
                        <div class="trust-item__text">Miễn phí<br>vận chuyển</div>
                    </div>
                </div>

                {{-- DELIVERY INFO --}}
                <div class="delivery-box">
                    <div class="delivery-row">
                        <div class="delivery-row__icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div>
                            <div class="delivery-row__label">Giao hàng nhanh</div>
                            <div class="delivery-row__sub">Dự kiến nhận hàng trong 2–3 ngày làm việc</div>
                        </div>
                    </div>
                    <div class="delivery-row">
                        <div class="delivery-row__icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div>
                            <div class="delivery-row__label">Nhận tại cửa hàng</div>
                            <div class="delivery-row__sub">Sẵn sàng để lấy hàng hôm nay tại 12 chi nhánh</div>
                        </div>
                    </div>
                </div>

            </div>{{-- end pdp-info --}}
        </div>{{-- end pdp-grid --}}


        {{-- ── TABS ── --}}
        <div class="pdp-tabs">
            <div class="tab-nav">
                <button class="tab-btn active" onclick="switchTab('specs',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                    Thông số kỹ thuật
                </button>
                <button class="tab-btn" onclick="switchTab('desc',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                        <polyline points="10 9 9 9 8 9" />
                    </svg>
                    Mô tả sản phẩm
                </button>
                <button class="tab-btn" onclick="switchTab('reviews',this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                    Đánh giá
                    <span
                        style="background:#EDEBE6;color:#6B6965;font-size:11px;padding:1px 6px;border-radius:99px;margin-left:2px">6</span>
                </button>
            </div>

            {{-- SPECS --}}
            <div class="tab-panel active" id="tab-specs">
                <table class="specs-table">
                    <tr>
                        <td>CPU / Chip</td>
                        <td>Apple M3 Pro — 12-core CPU, 18-core GPU, 16-core Neural Engine</td>
                    </tr>
                    <tr>
                        <td>RAM</td>
                        <td>18GB Unified Memory</td>
                    </tr>
                    <tr>
                        <td>Bộ nhớ trong</td>
                        <td>512GB SSD NVMe (đọc đến 7.4 GB/s)</td>
                    </tr>
                    <tr>
                        <td>Màn hình</td>
                        <td>14.2" Liquid Retina XDR · 3024 × 1964px · ProMotion 120Hz · 1000 nits (fullscreen) · True Tone ·
                            P3</td>
                    </tr>
                    <tr>
                        <td>Camera</td>
                        <td>1080p FaceTime HD · tắt đèn hardware khi đóng nắp</td>
                    </tr>
                    <tr>
                        <td>Âm thanh</td>
                        <td>Hệ thống 6 loa · Spatial Audio · 3 micro chất lượng studio</td>
                    </tr>
                    <tr>
                        <td>Kết nối</td>
                        <td>3× Thunderbolt 4 (USB-C) · HDMI 2.1 · SD Card · MagSafe 3 · Wi-Fi 6E · Bluetooth 5.3</td>
                    </tr>
                    <tr>
                        <td>Pin</td>
                        <td>70Wh · Lên đến 18 giờ phát video · Sạc MagSafe 96W</td>
                    </tr>
                    <tr>
                        <td>Hệ điều hành</td>
                        <td>macOS Sonoma (nâng cấp lên macOS Sequoia)</td>
                    </tr>
                    <tr>
                        <td>Kích thước</td>
                        <td>31.26 × 22.12 × 1.55 cm</td>
                    </tr>
                    <tr>
                        <td>Trọng lượng</td>
                        <td>1.61 kg</td>
                    </tr>
                    <tr>
                        <td>Màu sắc</td>
                        <td>Space Black · Silver · Starlight</td>
                    </tr>
                </table>
            </div>

            {{-- DESCRIPTION --}}
            <div class="tab-panel" id="tab-desc">
                <div class="desc-content">

                    <div class="desc-highlight">
                        <div class="desc-hi-item">
                            <div class="desc-hi-item__num">12‑core</div>
                            <div class="desc-hi-item__label">CPU mạnh nhất từ trước đến nay</div>
                        </div>
                        <div class="desc-hi-item">
                            <div class="desc-hi-item__num">18 giờ</div>
                            <div class="desc-hi-item__label">Thời lượng pin vượt trội</div>
                        </div>
                        <div class="desc-hi-item">
                            <div class="desc-hi-item__num">120Hz</div>
                            <div class="desc-hi-item__label">Màn hình Liquid Retina XDR</div>
                        </div>
                    </div>

                    <h3>Hiệu năng đột phá với chip M3 Pro</h3>
                    <p>MacBook Pro 14 inch được trang bị chip Apple M3 Pro — bộ xử lý tiên tiến nhất được Apple thiết kế
                        riêng cho dòng máy chuyên nghiệp. Với CPU 12-core và GPU 18-core, M3 Pro mang lại hiệu suất vượt
                        trội so với thế hệ trước lên đến 40%, trong khi vẫn duy trì mức tiêu thụ điện năng cực thấp.</p>

                    <h3>Màn hình Liquid Retina XDR đẳng cấp</h3>
                    <p>Tấm nền 14.2 inch với độ phân giải 3024 × 1964px và công nghệ ProMotion tự động điều chỉnh tần số
                        quét từ 24Hz đến 120Hz mang lại trải nghiệm hình ảnh mượt mà và tiết kiệm pin. Độ sáng đỉnh lên đến
                        1600 nits khi xem nội dung HDR, cùng dải màu P3 rộng đảm bảo màu sắc trung thực tuyệt đối.</p>

                    <h3>Thiết kế tinh tế, bền vững</h3>
                    <ul>
                        <li>Vỏ nhôm nguyên khối tái chế 100% — giảm thiểu tác động môi trường</li>
                        <li>Bàn phím Magic Keyboard với Touch ID tích hợp</li>
                        <li>Trackpad Force Touch rộng nhất trong dòng MacBook Pro</li>
                        <li>Cổng MagSafe 3 sạc nhanh, an toàn — không lo vấp dây</li>
                    </ul>

                </div>
            </div>

            {{-- REVIEWS --}}
            <div class="tab-panel" id="tab-reviews">

                {{-- Tổng quan --}}
                <div class="review-overview">
                    <div class="review-score" style="text-align:center;flex-shrink:0">
                        <div class="review-score__num">4.9</div>
                        <div class="review-score__stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B"
                                    stroke-width="1">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            @endfor
                        </div>
                        <div class="review-score__label">6 đánh giá</div>
                    </div>

                    <div class="review-bars">
                        @foreach ([5 => 5, 4 => 1, 3 => 0, 2 => 0, 1 => 0] as $star => $count)
                            @php $pct = $count > 0 ? round($count / 6 * 100) : 0; @endphp
                            <div class="review-bar-row">
                                <span class="review-bar-row__label">{{ $star }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="#F59E0B"
                                    style="flex-shrink:0">
                                    <polygon
                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                <div class="review-bar-track">
                                    <div class="review-bar-fill" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="review-bar-row__count">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Danh sách review --}}
                @php
                    $reviews = [
                        [
                            'name' => 'Nguyễn Văn An',
                            'stars' => 5,
                            'date' => '20/06/2026',
                            'content' =>
                                'Máy cực kỳ mạnh mẽ, màn hình đẹp xuất sắc, pin trâu hơn mong đợi. Rất đáng tiền!',
                            'verified' => true,
                        ],
                        [
                            'name' => 'Trần Thị Bích',
                            'stars' => 5,
                            'date' => '18/06/2026',
                            'content' =>
                                'Seal hộp nguyên vẹn, giao hàng nhanh, sản phẩm chính hãng. Mình rất hài lòng.',
                            'verified' => true,
                        ],
                        [
                            'name' => 'Lê Minh Quân',
                            'stars' => 5,
                            'date' => '15/06/2026',
                            'content' => 'Hiệu năng vượt trội, làm việc nặng không lag. Thiết kế sang trọng, mỏng nhẹ.',
                            'verified' => true,
                        ],
                        [
                            'name' => 'Phạm Thu Hà',
                            'stars' => 5,
                            'date' => '12/06/2026',
                            'content' => 'Dùng cho công việc đồ họa rất mượt. Chip M3 Pro thực sự ấn tượng!',
                            'verified' => true,
                        ],
                        [
                            'name' => 'Hoàng Đức Long',
                            'stars' => 4,
                            'date' => '10/06/2026',
                            'content' =>
                                'Máy rất tốt, chỉ tiếc giá hơi cao. Nhưng chất lượng xứng đáng với số tiền bỏ ra.',
                            'verified' => false,
                        ],
                        [
                            'name' => 'Vũ Thanh Tùng',
                            'stars' => 5,
                            'date' => '08/06/2026',
                            'content' => 'Mua lần 2 rồi vẫn thấy hài lòng. Shop uy tín, bảo hành tốt.',
                            'verified' => true,
                        ],
                    ];
                @endphp

                <div class="review-list">
                    @foreach ($reviews as $r)
                        <div class="review-item">
                            <div class="review-avatar">{{ mb_substr($r['name'], 0, 1) }}</div>
                            <div class="review-body">
                                <div class="review-header">
                                    <div style="display:flex;align-items:center;gap:4px">
                                        <span class="review-name">{{ $r['name'] }}</span>
                                        @if ($r['verified'])
                                            <span class="review-verified">
                                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                Đã mua
                                            </span>
                                        @endif
                                    </div>
                                    <span class="review-date">{{ $r['date'] }}</span>
                                </div>
                                <div class="review-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg width="13" height="13" viewBox="0 0 24 24"
                                            fill="{{ $i <= $r['stars'] ? '#F59E0B' : '#E5E3DE' }}"
                                            stroke="{{ $i <= $r['stars'] ? '#F59E0B' : '#E5E3DE' }}" stroke-width="1">
                                            <polygon
                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="review-text">{{ $r['content'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Form đánh giá --}}
                <div class="review-form-wrap">
                    <h4>Viết đánh giá của bạn</h4>

                    <div class="star-picker" id="starPicker">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#E5E3DE" stroke="#E5E3DE"
                                stroke-width="1" style="cursor:pointer" onclick="selectStar({{ $i }})"
                                data-star="{{ $i }}">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        @endfor
                    </div>

                    <div class="form-group">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-input" placeholder="Tên của bạn">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nội dung đánh giá</label>
                        <textarea class="form-input" placeholder="Chia sẻ trải nghiệm sử dụng sản phẩm..."></textarea>
                    </div>

                    <button class="btn-submit-review" onclick="submitReview()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        Gửi đánh giá
                    </button>
                </div>
            </div>
        </div>

        {{-- ── RELATED PRODUCTS ── --}}
        <div class="related-section">
            <div class="section-header">
                <div class="section-title">Sản phẩm liên quan</div>
                <div class="section-sub">Có thể bạn cũng thích</div>
            </div>
            <div class="related-grid">
                @php
                    $related = [
                        [
                            'name' => 'MacBook Air 13" M3',
                            'price' => '28.990.000₫',
                            'old' => '32.490.000₫',
                            'badge' => '',
                            'badge_type' => '',
                        ],
                        [
                            'name' => 'iPad Pro 13" M4',
                            'price' => '32.990.000₫',
                            'old' => '',
                            'badge' => 'Mới',
                            'badge_type' => 'new',
                        ],
                        [
                            'name' => 'Magic Keyboard TouchID',
                            'price' => '3.490.000₫',
                            'old' => '',
                            'badge' => '',
                            'badge_type' => '',
                        ],
                        [
                            'name' => 'AirPods Pro 2',
                            'price' => '6.990.000₫',
                            'old' => '7.990.000₫',
                            'badge' => 'Hot',
                            'badge_type' => 'hot',
                        ],
                    ];
                @endphp
                @foreach ($related as $item)
                    <a class="related-card" href="{{ url('san-pham') }}">
                        @if ($item['badge'])
                            <div class="related-card__badge related-card__badge--{{ $item['badge_type'] }}">
                                {{ $item['badge'] }}</div>
                        @endif
                        <div class="related-card__img">
                            <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1" stroke-linecap="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" />
                                <line x1="8" y1="21" x2="16" y2="21" />
                                <line x1="12" y1="17" x2="12" y2="21" />
                            </svg>
                        </div>
                        <div class="related-card__name">{{ $item['name'] }}</div>
                        <div>
                            <span class="related-card__price">{{ $item['price'] }}</span>
                            @if ($item['old'])
                                <span class="related-card__price-old">{{ $item['old'] }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    <script>
        function selectColor(el, color) {
            document.querySelectorAll('.color-swatch').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('selectedColor').textContent = color;
        }

        function selectChip(el, labelId, value, price) {
            el.closest('.size-chips').querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById(labelId).textContent = value;
            if (price) document.getElementById('currentPrice').textContent = price;
        }

        function changeQty(delta) {
            let input = document.getElementById('qtyInput');
            let val = Math.min(10, Math.max(1, (parseInt(input.value) || 1) + delta));
            input.value = val;
        }

        function selectThumb(el) {
            document.querySelectorAll('.gallery__thumb').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }

        function toggleWish() {
            const btn = document.getElementById('wishBtn');
            const id = btn.dataset.wishId || '1';
            const name = btn.dataset.wishName || 'Sản phẩm';
            const price = parseInt(btn.dataset.wishPrice || '0');
            Wishlist.toggle(id, name, price, '');
        }

        function switchTab(id, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + id).classList.add('active');
        }

        let selectedStar = 0;

        function selectStar(n) {
            selectedStar = n;
            document.querySelectorAll('#starPicker svg').forEach((s, i) => {
                const active = i < n;
                s.setAttribute('fill', active ? '#F59E0B' : '#E5E3DE');
                s.setAttribute('stroke', active ? '#F59E0B' : '#E5E3DE');
            });
        }

        function submitReview() {
            if (!selectedStar) {
                Toast.show('Vui lòng chọn số sao!', 'error');
                return;
            }
            Toast.show('Đánh giá của bạn đã được gửi, cảm ơn bạn!', 'success');
            selectStar(0);
        }

        Object.assign(window, {
            selectColor,
            selectChip,
            changeQty,
            selectThumb,
            toggleWish,
            switchTab,
            selectStar,
            submitReview
        });
    </script>
@endsection
