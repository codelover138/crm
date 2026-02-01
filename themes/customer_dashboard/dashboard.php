<?php defined('BASEPATH') OR exit('No direct script access allowed');
$currency_symbol = (!empty($currency) && isset($currency->symbol)) ? $currency->symbol : (!empty($Settings->default_currency) ? $Settings->default_currency : '€');
$format_amount = function($n) use ($currency_symbol) { return $currency_symbol . ' ' . number_format((float)$n, 2); };
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($customer_name) ?> - Licenses &amp; Support | <?= html_escape($site_name) ?></title>
    <?php if (!empty($logo_url)): ?>
    <link rel="icon" type="image/png" href="<?= html_escape($logo_url) ?>">
    <link rel="apple-touch-icon" href="<?= html_escape($logo_url) ?>">
    <?php endif; ?>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
    :root {
        /* Professional dark slate – easy on the eyes, corporate-friendly */
        --bg: #0f172a;
        --surface: #1e293b;
        --surface2: #334155;
        --text: #f1f5f9;
        --text-muted: #94a3b8;
        --accent: #6366f1;
        --accent-light: #818cf8;
        --success: #22c55e;
        --warning: #f59e0b;
        --danger: #ef4444;
        --radius: 14px;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    @keyframes titleGlow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    @keyframes underlineSlide {
        from { transform: scaleX(0); opacity: 0; }
        to { transform: scaleX(1); opacity: 1; }
    }

    body {
        font-family: 'DM Sans', -apple-system, sans-serif;
        background: linear-gradient(160deg, #0f172a 0%, #0c1222 35%, #0d1324 60%, #0f172a 100%);
        background-attachment: fixed;
        color: var(--text);
        min-height: 100vh;
        line-height: 1.5;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99, 102, 241, 0.15), transparent),
                    radial-gradient(ellipse 60% 40% at 100% 50%, rgba(139, 92, 246, 0.06), transparent),
                    radial-gradient(ellipse 50% 30% at 0% 80%, rgba(59, 130, 246, 0.05), transparent);
        pointer-events: none;
        z-index: 0;
    }

    .dashboard-wrap {
        display: flex;
        flex-wrap: wrap;
        max-width: 1320px;
        margin: 0 auto;
        padding: 1.5rem;
        gap: 1.5rem;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .dashboard-sidebar {
        width: 260px;
        flex-shrink: 0;
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius);
        padding: 1.35rem;
        position: sticky;
        top: 1.5rem;
        animation: slideInLeft 0.5s ease-out;
    }

    .sidebar-logo-wrap {
        text-align: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .sidebar-logo {
        max-width: 140px;
        max-height: 60px;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto 0.5rem;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
    }

    .sidebar-site-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.01em;
        margin-top: 0.5rem;
    }

    .dashboard-sidebar-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .dashboard-sidebar-title i {
        color: var(--accent);
    }

    .dashboard-sidebar-item {
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }

    .dashboard-sidebar-item:last-child {
        margin-bottom: 0;
    }

    .dashboard-sidebar-item .label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.25rem;
    }

    .dashboard-sidebar-item .label i {
        color: var(--accent);
        width: 0.9rem;
        font-size: 0.7rem;
    }

    .dashboard-sidebar-item .val {
        color: var(--text);
        word-break: break-word;
    }

    .dashboard-sidebar-item a.val {
        color: var(--accent-light);
        text-decoration: none;
    }

    .dashboard-sidebar-item a.val:hover {
        text-decoration: underline;
    }

    @media (max-width: 900px) {
        .dashboard-sidebar {
            width: 100%;
            position: static;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .dashboard-sidebar-title {
            grid-column: 1 / -1;
        }
    }

    .dashboard {
        flex: 1;
        min-width: 0;
        max-width: 1100px;
        padding: 0;
        animation: fadeInUp 0.5s ease-out 0.1s backwards;
    }

    .header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 2rem 1.5rem;
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius);
        border-left: 4px solid var(--accent);
        animation: fadeInUp 0.5s ease-out;
    }

    .header-badge {
        display: inline-block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--accent-light);
        margin-bottom: 0.5rem;
    }

    .header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
    }

    .header p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    .header-logo-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .header-logo {
        max-height: 48px;
        max-width: 160px;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }

    .header-logo-site {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.02em;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text);
    }

    .section-title i {
        color: var(--accent);
    }

    /* My active Products section – highlighted title */
    .section-title--products {
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin-bottom: 1.25rem;
        position: relative;
        padding-bottom: 0.75rem;
        animation: fadeInUp 0.6s ease-out 0.15s backwards;
    }

    .section-title--products i {
        color: var(--accent-light);
        font-size: 1.1rem;
        animation: titleGlow 3s ease-in-out infinite;
    }

    .section-title--products .section-title-text {
        background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 50%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-title--products::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), #8b5cf6);
        border-radius: 2px;
        animation: underlineSlide 0.6s ease-out 0.4s backwards;
    }

    /* License / Support cards */
    .licenses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .license-card {
        background: linear-gradient(165deg, var(--surface) 0%, rgba(18, 18, 26, 0.98) 100%);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 1.5rem;
        transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .licenses-grid .license-card:nth-child(1) { animation-delay: 0.1s; }
    .licenses-grid .license-card:nth-child(2) { animation-delay: 0.2s; }
    .licenses-grid .license-card:nth-child(3) { animation-delay: 0.3s; }

    .license-card:hover {
        transform: translateY(-4px);
        border-color: rgba(99, 102, 241, 0.2);
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3);
    }

    .license-gauge-top {
        margin-bottom: 1.25rem;
    }
    .license-card-header {
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .license-card .product-name {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .license-card .product-name .license-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--accent-light);
        background: rgba(99, 102, 241, 0.12);
        border: 1px solid rgba(99, 102, 241, 0.25);
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
    }

    .license-card .product-name .license-badge i {
        font-size: 0.6rem;
        opacity: 0.9;
    }

    .license-card .product-name .product-name-text {
        flex: 1;
        min-width: 0;
    }

    .license-info-item-inline {
        display: inline-flex;
        max-width: 240px;
    }

    .license-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.85rem;
    }

    .license-info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding: 0.65rem 0.85rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        min-width: 0;
    }

    .license-info-item .icon-wrap {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.8rem;
        color: var(--accent-light);
        background: rgba(99, 102, 241, 0.12);
    }

    .license-info-item .icon-wrap.calendar {
        color: var(--success);
        background: rgba(34, 197, 94, 0.12);
    }

    .license-info-item .icon-wrap.support {
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.12);
    }

    .license-info-item .content {
        min-width: 0;
    }

    .license-info-item .label {
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .license-info-item .val {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text);
        word-break: break-all;
    }

    .license-card .meta {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 1.1rem;
        line-height: 1.5;
    }

    .license-card .meta span {
        margin-right: 0.75rem;
    }

    /* Progress bar — professional, attractive */
    .progress-wrap {
        margin-bottom: 0.75rem;
    }

    .progress-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .progress-label-row .label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }

    .progress-label-row .pct {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .progress-label-row .pct.green {
        color: var(--success);
    }

    .progress-label-row .pct.yellow {
        color: var(--warning);
    }

    .progress-label-row .pct.red {
        color: var(--danger);
    }

    .progress-label-row .pct.expired {
        color: var(--danger);
    }

    .progress-label-row .pct.no-expiry {
        color: var(--accent-light);
    }

    .progress-bar-outer {
        height: 14px;
        background: rgba(0, 0, 0, 0.25);
        border-radius: 100px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .progress-bar-inner {
        height: 100%;
        border-radius: 100px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        min-width: 4px;
    }

    .progress-bar-inner::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 50%;
        border-radius: 100px 100px 0 0;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.2), transparent);
        pointer-events: none;
    }

    /* Chart-style: passed (red) | remaining (green); full bar width */
    .progress-chart {
        background: rgba(0, 0, 0, 0.15);
        border-radius: var(--radius-sm);
        padding: 1rem 1.1rem;
        border: 1px solid rgba(255, 255, 255, 0.04);
        margin-bottom: 0.75rem;
    }

    .progress-chart-legend {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.65rem;
        flex-wrap: wrap;
    }

    .progress-chart-legend span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .progress-chart-legend .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        box-shadow: 0 0 8px currentColor;
    }

    .progress-chart-legend .dot.red {
        background: #ef4444;
        color: rgba(239, 68, 68, 0.5);
    }

    .progress-chart-legend .dot.green {
        background: #22c55e;
        color: rgba(34, 197, 94, 0.5);
    }

    .progress-chart-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.4rem;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
    }

    .progress-bar-outer.chart-bar {
        height: 18px;
        border-radius: 10px;
        position: relative;
        overflow: visible;
    }

    .progress-bar-inner.passed-remaining {
        width: 100% !important;
        box-shadow: 0 0 14px rgba(34, 197, 94, 0.25);
        border-radius: 10px;
    }

    .progress-bar-split {
        position: absolute;
        top: -2px;
        bottom: -2px;
        width: 3px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 2px;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.4);
        pointer-events: none;
    }

    .progress-chart-days {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .progress-chart-days .passed {
        color: #f87171;
    }

    .progress-chart-days .remaining {
        color: #4ade80;
    }

    /* Semi-circular gauge – professional & beautiful */
    .gauge-wrap {
        background: linear-gradient(168deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.99) 100%);
        border-radius: 16px;
        padding: 1.75rem 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.07);
        margin-bottom: 0.75rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), 0 1px 0 rgba(255, 255, 255, 0.04) inset;
        position: relative;
        overflow: hidden;
    }

    .gauge-wrap::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
        pointer-events: none;
    }

    .gauge-wrap .gauge-title {
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: var(--text-muted);
        margin-bottom: 1.1rem;
        opacity: 0.92;
    }

    .gauge-container {
        position: relative;
        width: 100%;
        max-width: 240px;
        margin: 0 auto 0.6rem;
    }

    .gauge-container svg {
        display: block;
        width: 100%;
        height: auto;
        filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.25));
    }

    .gauge-value-wrap {
        text-align: center;
        margin: 1rem 0 1.1rem;
    }

    .gauge-value-sublabel {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-top: 0.35rem;
        opacity: 0.9;
    }

    .gauge-value-wrap .gauge-value-sublabel { color: var(--text-muted); }
    .gauge-value-wrap .gauge-value.green + .gauge-value-sublabel { color: #34d399; }
    .gauge-value-wrap .gauge-value.yellow + .gauge-value-sublabel { color: #fbbf24; }
    .gauge-value-wrap .gauge-value.red + .gauge-value-sublabel { color: #f87171; }

    .gauge-value {
        display: inline-block;
        font-size: 2.4rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.1;
        padding: 0.5rem 1.25rem;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.07);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .gauge-value.green,
    .gauge-value.green * {
        color: #34d399 !important;
    }
    .gauge-value.green {
        box-shadow: 0 0 28px rgba(52, 211, 153, 0.2), 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .gauge-value.yellow,
    .gauge-value.yellow * {
        color: #fbbf24 !important;
    }
    .gauge-value.yellow {
        box-shadow: 0 0 28px rgba(251, 191, 36, 0.2), 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .gauge-value.red,
    .gauge-value.red * {
        color: #f87171 !important;
    }
    .gauge-value.red {
        box-shadow: 0 0 28px rgba(248, 113, 113, 0.2), 0 2px 12px rgba(0, 0, 0, 0.15);
    }

    .gauge-days {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .gauge-days .passed,
    .gauge-days .remaining {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.85rem 0.6rem;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 600;
        text-align: center;
        letter-spacing: 0.02em;
    }

    .gauge-days .passed {
        background: linear-gradient(145deg, rgba(239, 68, 68, 0.1) 0%, rgba(185, 28, 28, 0.06) 100%);
        border: 1px solid rgba(239, 68, 68, 0.22);
        color: #fca5a5 !important;
    }

    .gauge-days .passed i {
        display: block;
        font-size: 1.15rem;
        margin-bottom: 0.4rem;
        color: #ef4444 !important;
        opacity: 0.95;
    }

    .gauge-days .remaining {
        background: linear-gradient(145deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.06) 100%);
        border: 1px solid rgba(34, 197, 94, 0.22);
        color: #6ee7b7 !important;
    }

    .gauge-days .remaining i {
        display: block;
        font-size: 1.15rem;
        margin-bottom: 0.4rem;
        color: #22c55e !important;
        opacity: 0.95;
    }

    .gauge-days .label {
        font-size: 0.625rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    .gauge-expires {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6ee7b7;
        padding: 0.55rem 0.9rem;
        position: center;
        border-radius: 10px;
        background: linear-gradient(145deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.06) 100%);
        border: 1px solid rgba(34, 197, 94, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .gauge-expires::before {
        content: '\f073';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 0.75rem;
        opacity: 0.95;
    }

    .progress-bar-inner.expired {
        background: linear-gradient(90deg, #7f1d1d, #ef4444);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
    }

    .progress-bar-inner.no-expiry {
        background: linear-gradient(90deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
        box-shadow: 0 0 14px rgba(99, 102, 241, 0.35);
    }

    .license-status {
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.35rem;
    }

    .license-status.green {
        color: var(--success);
    }

    .license-status.yellow {
        color: var(--warning);
    }

    .license-status.red {
        color: var(--danger);
    }

    .license-status.expired {
        color: var(--danger);
    }

    .license-status.no-expiry {
        color: var(--accent-light);
    }

    .license-empty {
        background: var(--surface);
        border: 1px dashed rgba(255, 255, 255, 0.1);
        border-radius: var(--radius);
        padding: 2rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .license-empty i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    /* Stats strip */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius-sm);
        padding: 1rem;
        text-align: center;
        animation: fadeInUp 0.4s ease-out backwards;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stats-strip .stat-box:nth-child(1) { animation-delay: 0.05s; }
    .stats-strip .stat-box:nth-child(2) { animation-delay: 0.1s; }
    .stats-strip .stat-box:nth-child(3) { animation-delay: 0.15s; }
    .stats-strip .stat-box:nth-child(4) { animation-delay: 0.2s; }
    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-box .label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .stat-box .value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .stat-box .value.accent {
        color: var(--accent-light);
    }

    .stat-box .value.green {
        color: var(--success);
    }

    .stat-box .value.amber {
        color: var(--warning);
    }

    .section {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .section-head {
        font-size: 0.95rem;
        font-weight: 600;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-head i {
        color: var(--accent);
    }

    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 0.8rem 1.25rem;
        text-align: left;
    }

    th {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    tr:last-child {
        border-bottom: 0;
    }

    td {
        font-size: 0.88rem;
    }

    .status {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .status.completed {
        background: rgba(34, 197, 94, 0.2);
        color: var(--success);
    }

    .status.pending {
        background: rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    .status.draft {
        background: rgba(139, 139, 154, 0.2);
        color: var(--text-muted);
    }

    .empty-state {
        padding: 2rem;
        text-align: center;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .empty-state i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .footer-note {
        text-align: center;
        color: var(--text-muted);
        font-size: 0.78rem;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    /* Your team – Sales & Technical Associate */
    .associates-section {
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius);
        padding: 1.5rem;
    }

    .associates-section .section-title {
        margin-bottom: 0.75rem;
    }

    .associates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .associate-card {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius-sm);
        padding: 1.25rem 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        animation: fadeInUp 0.4s ease-out backwards;
    }
    .associates-grid .associate-card:nth-child(1) { animation-delay: 0.1s; }
    .associates-grid .associate-card:nth-child(2) { animation-delay: 0.2s; }

    .associate-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.2);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .associate-card .icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    .associate-card.sales .icon-wrap {
        background: rgba(99, 102, 241, 0.15);
        color: var(--accent-light);
    }

    .associate-card.tech .icon-wrap {
        background: rgba(34, 197, 94, 0.15);
        color: var(--success);
    }

    .associate-card .role {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .associate-card .name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
    }

    /* Open Case form (sidebar) */
    .case-form-wrap {
        margin-top: 0.5rem;
    }

    .case-form-hint {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .case-form-blocked {
        font-size: 0.8rem;
        color: var(--warning);
        margin-bottom: 0.75rem;
        padding: 0.6rem 0.75rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.25);
        border-radius: var(--radius-sm);
        line-height: 1.4;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .case-form-blocked i {
        margin-top: 0.1rem;
        flex-shrink: 0;
    }

    .case-form-group {
        margin-bottom: 0.75rem;
    }

    .case-form-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .case-form-label .required {
        color: var(--danger);
    }

    .case-form-textarea {
        width: 100%;
        padding: 0.6rem 0.75rem;
        font-family: inherit;
        font-size: 0.85rem;
        color: var(--text);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-sm);
        resize: vertical;
        min-height: 80px;
    }

    .case-form-textarea:focus {
        outline: none;
        border-color: var(--accent);
    }

    .case-form-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, var(--accent), #8b5cf6);
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .case-form-submit:hover {
        opacity: 0.9;
    }

    .case-form-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .case-form-message {
        margin-top: 0.75rem;
        font-size: 0.8rem;
        min-height: 1.2em;
    }

    .case-form-message.success {
        color: var(--success);
    }

    .case-form-message.error {
        color: var(--danger);
    }

    .case-details-cell {
        max-width: 280px;
        word-break: break-word;
    }

    .status.case-status-open {
        background: rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    .status.case-status-in_progress {
        background: rgba(99, 102, 241, 0.2);
        color: var(--accent-light);
    }

    .status.case-status-closed {
        background: rgba(34, 197, 94, 0.2);
        color: var(--success);
    }

    /* Appointment Button & Modal */
    .appointment-btn-wrap {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
    .appointment-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s;
    }
    .appointment-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    .appointment-btn i {
        font-size: 1rem;
    }

    /* Modal Overlay & Container */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
        animation: fadeIn 0.2s ease;
    }
    .modal-overlay.active {
        display: flex;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .modal-container {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius);
        max-width: 540px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .modal-header h3 i {
        color: var(--accent);
    }
    .modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.3rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s, color 0.2s;
    }
    .modal-close:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text);
    }
    .modal-body {
        padding: 1.75rem;
    }
    .modal-footer {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    /* Appointment Form in Modal */
    .appointment-form-group {
        margin-bottom: 1.25rem;
    }
    .appointment-form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }
    .appointment-form-label .required {
        color: var(--danger);
    }
    .appointment-form-input,
    .appointment-form-select,
    .appointment-form-textarea {
        width: 100%;
        padding: 0.7rem 0.85rem;
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--text);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-sm);
    }
    .appointment-form-input:focus,
    .appointment-form-select:focus,
    .appointment-form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.07);
    }
    .appointment-form-textarea {
        resize: vertical;
        min-height: 80px;
    }
    .appointment-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 600px) {
        .appointment-form-row {
            grid-template-columns: 1fr;
        }
    }
    .appointment-form-submit-wrap {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .appointment-form-submit {
        flex: 1;
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, var(--accent), #8b5cf6);
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .appointment-form-submit:hover {
        opacity: 0.9;
    }
    .appointment-form-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .appointment-form-cancel {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background 0.2s;
    }
    .appointment-form-cancel:hover {
        background: rgba(255, 255, 255, 0.08);
    }
    .appointment-form-message {
        margin-top: 1rem;
        font-size: 0.85rem;
        padding: 0.75rem;
        border-radius: var(--radius-sm);
    }
    .appointment-form-message.success {
        color: var(--success);
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
    }
    .appointment-form-message.error {
        color: var(--danger);
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    /* Appointment Status Colors */
    .status.appointment-status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }
    .status.appointment-status-confirmed {
        background: rgba(34, 197, 94, 0.2);
        color: var(--success);
    }
    .status.appointment-status-rescheduled {
        background: rgba(99, 102, 241, 0.2);
        color: var(--accent-light);
    }
    .status.appointment-status-completed {
        background: rgba(100, 116, 139, 0.2);
        color: var(--text-muted);
    }
    .status.appointment-status-cancelled {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger);
    }

    /* Charts Section */
    .charts-section {
        margin-bottom: 2rem;
    }
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
    .chart-card {
        background: var(--surface);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: var(--radius);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: fadeInUp 0.4s ease-out backwards;
    }
    .charts-grid .chart-card:nth-child(1) { animation-delay: 0.1s; }
    .charts-grid .chart-card:nth-child(2) { animation-delay: 0.2s; }
    .chart-card-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chart-card-title i {
        color: var(--accent);
    }
    .chart-container {
        position: relative;
        width: 100%;
        max-width: 200px;
        height: 200px;
    }
    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .chart-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .chart-legend-value {
        font-weight: 600;
        color: var(--text);
    }
    .chart-empty {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .chart-empty i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
        display: block;
    }

    /* ========== Full responsive ========== */
    @media (max-width: 1200px) {
        .dashboard-wrap {
            max-width: 100%;
            padding: 1.25rem;
        }
        .dashboard {
            max-width: 100%;
        }
    }

    @media (max-width: 992px) {
        .dashboard-wrap {
            flex-direction: column;
            padding: 1rem;
            gap: 1.25rem;
        }
        .dashboard-sidebar {
            width: 100%;
            position: static;
            order: 1;
        }
        .dashboard {
            order: 2;
            width: 100%;
        }
        .licenses-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .associates-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
    }

    @media (max-width: 900px) {
        .dashboard-sidebar {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .dashboard-sidebar-title {
            margin-top: 1rem;
        }
        .dashboard-sidebar-title:first-child {
            margin-top: 0;
        }
        .appointment-btn-wrap {
            margin-top: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .dashboard-wrap {
            padding: 0.75rem;
        }
        .header {
            padding: 1.5rem 1rem;
            margin-bottom: 1.5rem;
        }
        .header h1 {
            font-size: 1.35rem;
        }
        .header p {
            font-size: 0.85rem;
        }
        .section-title {
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }
        .section-title--products {
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }
        .licenses-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .license-card {
            padding: 1.25rem;
        }
        .license-card .product-name {
            font-size: 1.05rem;
        }
        .license-info {
            grid-template-columns: 1fr;
        }
        .gauge-value {
            font-size: 2rem;
            padding: 0.4rem 1rem;
        }
        .gauge-container {
            max-width: 200px;
        }
        .gauge-days .passed,
        .gauge-days .remaining {
            padding: 0.65rem 0.5rem;
            font-size: 0.75rem;
        }
        .stats-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .stat-box {
            padding: 0.85rem;
        }
        .stat-box .value {
            font-size: 1.1rem;
        }
        .section-head,
        th, td {
            padding: 0.65rem 0.85rem;
            font-size: 0.8rem;
        }
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -0.75rem;
            padding: 0 0.75rem;
        }
        table {
            min-width: 400px;
        }
        .associates-section {
            padding: 1.25rem;
        }
        .modal-container {
            max-width: 100%;
            margin: 0.5rem;
            max-height: 85vh;
        }
        .modal-body {
            padding: 1.25rem;
        }
        .modal-header {
            padding: 1.25rem 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .dashboard-wrap {
            padding: 0.5rem;
        }
        .header {
            padding: 1.25rem 0.75rem;
        }
        .header h1 {
            font-size: 1.2rem;
        }
        .section-title--products::after {
            width: 40px;
        }
        .license-card {
            padding: 1rem;
        }
        .gauge-value {
            font-size: 1.75rem;
        }
        .gauge-container {
            max-width: 180px;
        }
        .gauge-days {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        .stats-strip {
            grid-template-columns: 1fr;
        }
        .section-head {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }
        th, td {
            padding: 0.5rem 0.65rem;
            font-size: 0.75rem;
        }
        table {
            min-width: 320px;
        }
        .chart-container {
            max-width: 180px;
            height: 180px;
        }
        .chart-legend {
            gap: 0.5rem;
        }
        .chart-legend-item {
            font-size: 0.7rem;
        }
        .appointment-form-submit-wrap {
            flex-direction: column;
        }
        .appointment-form-submit,
        .appointment-form-cancel {
            width: 100%;
        }
        .appointment-btn {
            padding: 0.85rem 1rem;
            font-size: 0.9rem;
        }
        .case-form-submit {
            width: 100%;
        }
        .sidebar-logo {
            max-width: 120px;
        }
        .sidebar-site-name {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 480px) {
        body {
            font-size: 15px;
        }
        .header h1 {
            font-size: 1.1rem;
        }
        .license-card-header {
            padding-top: 1rem;
        }
        .gauge-value-wrap {
            margin: 0.75rem 0;
        }
        .gauge-value {
            font-size: 1.6rem;
        }
        .section-title--products {
            font-size: 1rem;
        }
        .associate-card {
            padding: 1rem;
        }
        .associate-card .icon-wrap {
            width: 38px;
            height: 38px;
        }
    }
    </style>
</head>

<body>
    <div class="dashboard-wrap">
        <aside class="dashboard-sidebar">
            <?php if (!empty($logo_url)): ?>
            <div class="sidebar-logo-wrap">
                <img src="<?= html_escape($logo_url) ?>" alt="<?= html_escape($site_name) ?>" class="sidebar-logo">
                <div class="sidebar-site-name"><?= html_escape($site_name) ?></div>
            </div>
            <?php endif; ?>
            <h2 class="dashboard-sidebar-title"><i class="fas fa-user-circle"></i> Your details</h2>
            <?php $contact_name = trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? '')); if ($contact_name !== ''): ?>
            <div class="dashboard-sidebar-item">
                <span class="label"><i class="fas fa-user"></i> Name</span>
                <span class="val"><?= html_escape($contact_name) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($customer->email)): ?>
            <div class="dashboard-sidebar-item">
                <span class="label"><i class="fas fa-envelope"></i> Email</span>
                <a class="val"
                    href="mailto:<?= html_escape($customer->email) ?>"><?= html_escape($customer->email) ?></a>
            </div>
            <?php endif; ?>
            <?php if (!empty($customer->phone)): ?>
            <div class="dashboard-sidebar-item">
                <span class="label"><i class="fas fa-phone"></i> Phone</span>
                <a class="val"
                    href="tel:<?= html_escape(preg_replace('/\s+/', '', $customer->phone)) ?>"><?= html_escape($customer->phone) ?></a>
            </div>
            <?php endif; ?>
            <?php
            $addr_parts = array_filter(array(
                isset($customer->address) ? trim(strip_tags($customer->address)) : '',
                isset($customer->city) ? trim(strip_tags($customer->city)) : '',
                isset($customer->state) ? trim(strip_tags($customer->state)) : '',
                isset($customer->postal_code) ? trim(strip_tags($customer->postal_code)) : '',
                isset($customer->country) ? trim(strip_tags($customer->country)) : '',
            ));
            if (!empty($addr_parts)):
            ?>
            <div class="dashboard-sidebar-item">
                <span class="label"><i class="fas fa-map-marker-alt"></i> Address</span>
                <span class="val"><?= html_escape(implode(', ', $addr_parts)) ?></span>
            </div>
            <?php endif; ?>

            <h2 class="dashboard-sidebar-title" style="margin-top: 1.5rem;"><i class="fas fa-ticket-alt"></i> Open Case
            </h2>
            <div class="case-form-wrap">
                <?php if (empty($can_open_case)): ?>
                <p class="case-form-blocked"><i class="fas fa-lock"></i> Resolve your current case before opening a new
                    one.</p>
                <form id="case-form" class="case-form" action="" method="post" aria-disabled="true">
                    <input type="hidden" name="customer_code" value="<?= html_escape($customer_code ?? '') ?>">
                    <div class="case-form-group">
                        <label for="case-details" class="case-form-label">Details <span
                                class="required">*</span></label>
                        <textarea id="case-details" name="details" class="case-form-textarea" rows="4"
                            placeholder="Describe your issue or request..." disabled></textarea>
                    </div>
                    <button type="button" class="case-form-submit" id="case-submit-btn" disabled>
                        <i class="fas fa-paper-plane"></i> Submit Case
                    </button>
                    <div id="case-form-message" class="case-form-message" aria-live="polite"></div>
                </form>
                <?php else: ?>
                <p class="case-form-hint">Case ID is auto-generated on submit (prefix + your code + date).</p>
                <form id="case-form" class="case-form" action="" method="post">
                    <input type="hidden" name="customer_code" value="<?= html_escape($customer_code ?? '') ?>">
                    <div class="case-form-group">
                        <label for="case-details" class="case-form-label">Details <span
                                class="required">*</span></label>
                        <textarea id="case-details" name="details" class="case-form-textarea" rows="4"
                            placeholder="Describe your issue or request..." required></textarea>
                    </div>
                    <button type="submit" class="case-form-submit" id="case-submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Case
                    </button>
                    <div id="case-form-message" class="case-form-message" aria-live="polite"></div>
                </form>
                <?php endif; ?>
            </div>

            <!-- Book Appointment Button -->
            <div class="appointment-btn-wrap">
                <?php if (empty($can_book_appointment)): ?>
                <p class="case-form-blocked"><i class="fas fa-lock"></i> Complete your current appointment before booking a new one.</p>
                <button type="button" class="appointment-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </button>
                <?php else: ?>
                <button type="button" class="appointment-btn" id="open-appointment-modal">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </button>
                <?php endif; ?>
            </div>
        </aside>

        <div class="dashboard">
            <header class="header">
                <span class="header-badge">Licenses &amp; Support</span>
                <h1>Welcome, <?= html_escape($customer_name) ?></h1>
                <p>Your active licenses, support status and invoices at a glance.</p>
            </header>

            <!-- My licenses / support (last 3 products with feature=1, support duration progress) -->
            <h2 class="section-title section-title--products"><i class="fas fa-shield-alt"></i> <span class="section-title-text">My active Products, licenses &amp; support</span></h2>
            <?php if (!empty($dashboard_products)): ?>
            <div class="licenses-grid">
                <?php foreach ($dashboard_products as $lic): ?>
                <div class="license-card">
                    <?php /* 1. GAUGE / STATUS FIRST */ ?>
                    <?php if ($lic->status_class === 'no-expiry'): ?>
                    <div class="license-gauge-top">
                        <div class="progress-wrap">
                            <div class="progress-label-row">
                                <span class="label">Support status</span>
                                <span class="pct no-expiry">Active</span>
                            </div>
                            <div class="progress-bar-outer">
                                <div class="progress-bar-inner no-expiry" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div class="license-status no-expiry">No expiry set</div>
                    </div>
                    <?php elseif ($lic->status_class === 'expired'): ?>
                    <div class="license-gauge-top">
                        <div class="progress-wrap">
                            <div class="progress-label-row">
                                <span class="label">Support status</span>
                                <span class="pct expired">Expired</span>
                            </div>
                            <div class="progress-bar-outer">
                                <div class="progress-bar-inner expired" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div class="license-status expired">Expired <?= (int)abs($lic->remaining_days) ?> days ago</div>
                    </div>
                    <?php else:
                    $passed_days = max(0, (int)$lic->support_duration - (int)$lic->remaining_days);
                    $display_pct = min(100, max(0, (float)$lic->percent_remaining));
                    $passed_pct = 100 - $display_pct;
                    $needle_angle = ($passed_pct / 100) * 180 - 90;
                    ?>
                    <div class="license-gauge-top">
                        <div class="gauge-wrap">
                            <div class="gauge-title"><?= html_escape($lic->product_name) ?></div>
                            <div class="gauge-container">
                                <svg viewBox="0 0 200 120" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="gaugeGrad-<?= $lic->sale_id ?>" x1="0%" y1="0%" x2="100%"
                                            y2="0%">
                                            <stop offset="0%" stop-color="#22c55e" />
                                            <stop offset="35%" stop-color="#eab308" />
                                            <stop offset="65%" stop-color="#f97316" />
                                            <stop offset="100%" stop-color="#ef4444" />
                                        </linearGradient>
                                        <filter id="gaugeShadow-<?= $lic->sale_id ?>" x="-20%" y="-20%" width="140%"
                                            height="140%">
                                            <feDropShadow dx="0" dy="1" stdDeviation="1" flood-color="#000"
                                                flood-opacity="0.25" />
                                        </filter>
                                    </defs>
                                    <path d="M 35 95 A 65 65 0 0 1 165 95" fill="none" stroke="#1e293b" stroke-width="16"
                                        stroke-linecap="round" />
                                    <path d="M 35 95 A 65 65 0 0 1 165 95" fill="none"
                                        stroke="url(#gaugeGrad-<?= $lic->sale_id ?>)" stroke-width="11"
                                        stroke-linecap="round" filter="url(#gaugeShadow-<?= $lic->sale_id ?>)" />
                                    <circle cx="100" cy="98" r="4" fill="#fff" stroke="rgba(0,0,0,0.15)"
                                        stroke-width="0.5" />
                                    <g class="gauge-needle" transform="rotate(<?= $needle_angle ?> 100 98)">
                                        <line x1="100" y1="98" x2="100" y2="34" stroke="#fff" stroke-width="1.8"
                                            stroke-linecap="round" />
                                        <circle cx="100" cy="34" r="2.5" fill="#fff" />
                                    </g>
                                    <text x="36" y="111" fill="rgba(255,255,255,0.65)" font-size="9"
                                        font-weight="600">0%</text>
                                    <text x="164" y="111" fill="rgba(255,255,255,0.65)" font-size="9" font-weight="600"
                                        text-anchor="end">100%</text>
                                </svg>
                            </div>
                            <div class="gauge-value-wrap">
                                <div class="gauge-value <?= $lic->status_class ?>"><?= number_format($display_pct, 1) ?>%
                                </div>
                                <div class="gauge-value-sublabel">remaining</div>
                            </div>
                            <div class="gauge-days">
                                <span class="passed"><i class="fas fa-clock"></i> <?= $passed_days ?>
                                    day<?= $passed_days != 1 ? 's' : '' ?> passed</span>
                                <span class="remaining"><i class="fas fa-hourglass-half"></i>
                                    <?= (int)$lic->remaining_days ?> day<?= (int)$lic->remaining_days != 1 ? 's' : '' ?>
                                    remaining</span>
                            </div>
                            <?php if (!empty($lic->end_date)): ?>
                            <div class="gauge-expires-wrap">
                                <div class="gauge-expires">Expires <?= date($date_format, strtotime($lic->end_date)) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php /* 2. PRODUCT DETAIL SECTION */ ?>
                    <div class="license-card-header">
                        <div class="product-name">
                            <span class="license-badge"><i class="fas fa-id-card"></i> </span>
                            <span class="product-name-text"><?= html_escape($lic->product_name) ?></span>
                        </div>
                        <div class="license-info">
                            <div class="license-info-item">
                                <div class="icon-wrap"><i class="fas fa-file-invoice"></i></div>
                                <div class="content">
                                    <div class="label">Reference</div>
                                    <div class="val"><?= html_escape($lic->reference_no) ?></div>
                                </div>
                            </div>
                            <div class="license-info-item">
                                <div class="icon-wrap calendar"><i class="fas fa-calendar-check"></i></div>
                                <div class="content">
                                    <div class="label">Sale date</div>
                                    <div class="val"><?= date($date_format, strtotime($lic->sale_date)) ?></div>
                                </div>
                            </div>
                            <?php if (!empty($lic->end_date)): ?>
                            <div class="license-info-item">
                                <div class="icon-wrap support"><i class="fas fa-calendar-alt"></i></div>
                                <div class="content">
                                    <div class="label">Support ends</div>
                                    <div class="val"><?= date($date_format, strtotime($lic->end_date)) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($lic->status_class === 'expired' && !empty($lic->end_date)): ?>
                        <div class="license-info-item license-info-item-inline" style="margin-top: 0.5rem;">
                            <div class="icon-wrap support"><i class="fas fa-calendar-alt"></i></div>
                            <div class="content">
                                <div class="label">End date</div>
                                <div class="val"><?= date($date_format, strtotime($lic->end_date)) ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="license-empty">
                <i class="fas fa-box-open"></i><br>
                No active licenses with support duration to show. Products sold to you that are marked “Show on customer
                dashboard” will appear here with support remaining.
            </div>
            <?php endif; ?>

            <!-- Your team section -->
            <section class="associates-section">
                <h2 class="section-title"><i class="fas fa-users"></i> Your team</h2>
                <div class="associates-grid">
                    <div class="associate-card sales">
                        <div class="icon-wrap"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <div class="role">Sales Associate</div>
                            <div class="name">
                                <?= !empty($customer_sales_associate_name) ? html_escape($customer_sales_associate_name) : '—' ?>
                            </div>
                        </div>
                    </div>
                    <div class="associate-card tech">
                        <div class="icon-wrap"><i class="fas fa-user-cog"></i></div>
                        <div>
                            <div class="role">Technical Associate</div>
                            <div class="name">
                                <?= !empty($customer_tech_associate_name) ? html_escape($customer_tech_associate_name) : '—' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upcoming Appointments -->
            <section class="section">
                <h2 class="section-head"><i class="fas fa-calendar-check"></i> Upcoming Appointments</h2>
                <div class="table-wrap">
                    <table id="upcoming-appointments-table"<?= empty($upcoming_appointments) ? ' style="display:none"' : '' ?>>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="upcoming-appointments-tbody">
                            <?php $apt_count = 0; foreach (isset($upcoming_appointments) ? $upcoming_appointments : array() as $apt): if ($apt_count >= 5) break; $apt_count++; ?>
                            <tr>
                                <td><?= html_escape($apt->appointment_code) ?></td>
                                <td><?= html_escape(isset($appointment_types[$apt->appointment_type]) ? $appointment_types[$apt->appointment_type] : $apt->appointment_type) ?></td>
                                <td><?= date($date_format, strtotime($apt->preferred_date)) ?></td>
                                <td><?= date('h:i A', strtotime($apt->preferred_time)) ?></td>
                                <td><span class="status appointment-status-<?= html_escape(str_replace(' ', '_', strtolower($apt->status))) ?>"><?= html_escape($apt->status) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="empty-state" id="upcoming-appointments-empty"<?= !empty($upcoming_appointments) ? ' style="display:none"' : '' ?>><i class="fas fa-calendar"></i><br>No upcoming appointments. Click "Book Appointment" to schedule one.</div>
                </div>
            </section>

            <p class="footer-note"><?= html_escape($site_name) ?> · Licenses &amp; support dashboard</p>
        </div>
    </div>

    <script>
    (function() {
        var form = document.getElementById('case-form');
        if (!form) return;
        var submitUrl =
            <?= json_encode(site_url('customer_dashboard/submit_case/'.(isset($customer_code) ? $customer_code : ''))) ?>;
        var csrfName = <?= json_encode(isset($csrf_token_name) ? $csrf_token_name : '') ?>;
        var csrfHash = <?= json_encode(isset($csrf_hash) ? $csrf_hash : '') ?>;
        var tbody = document.getElementById('case-history-tbody');
        var table = document.getElementById('case-history-table');
        var emptyEl = document.getElementById('case-history-empty');
        var messageEl = document.getElementById('case-form-message');
        var submitBtn = document.getElementById('case-submit-btn');
        var detailsInput = document.getElementById('case-details');

        function setMessage(text, isError) {
            messageEl.textContent = text || '';
            messageEl.className = 'case-form-message' + (text ? (isError ? ' error' : ' success') : '');
        }

        function escapeHtml(s) {
            var div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }

        function addRowToTable(data) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(data.case_code) + '</td>' +
                '<td>' + escapeHtml(data.date_formatted) + '</td>' +
                '<td><span class="status case-status-open">' + escapeHtml(data.status || 'open') + '</span></td>';
            if (tbody) tbody.insertBefore(tr, tbody.firstChild);
            if (table) table.style.display = '';
            if (emptyEl) emptyEl.style.display = 'none';
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var details = (detailsInput && detailsInput.value) ? detailsInput.value.trim() : '';
            if (!details) {
                setMessage('Please enter details.', true);
                return;
            }
            setMessage('');
            if (submitBtn) submitBtn.disabled = true;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', submitUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                if (submitBtn) submitBtn.disabled = false;
                try {
                    var res = JSON.parse(xhr.responseText || '{}');
                    if (res.success) {
                        var msg = 'Case ' + (res.case_code || '') + ' submitted.';
                        if (res.email_sent) {
                            if (res.email_sent.staff_ok && res.email_sent.customer_ok) {
                                msg += ' Emails sent to support and to you.';
                            } else if (res.email_sent.staff_ok) {
                                msg += ' Email sent to support.';
                                if (res.email_sent.customer_error) {
                                    msg += ' Your confirmation email failed: ' + (res.email_sent
                                        .customer_error || '').substring(0, 150);
                                }
                            } else if (res.email_sent.customer_ok) {
                                msg += ' Confirmation sent to you. Support email failed: ' + (res
                                    .email_sent.staff_error || '').substring(0, 150);
                            } else {
                                msg += ' Emails could not be sent.';
                                if (res.email_sent.protocol_used) msg += ' (Protocol: ' + res.email_sent
                                    .protocol_used + '.)';
                                if (res.email_sent.staff_error) msg += ' Error: ' + (res.email_sent
                                    .staff_error || '').substring(0, 200);
                            }
                        } else {
                            msg += ' A confirmation email has been sent to you and to support.';
                        }
                        setMessage(msg);
                        if (detailsInput) detailsInput.value = '';
                        addRowToTable({
                            case_code: res.case_code || '',
                            date_formatted: res.date_formatted || '',
                            status: res.status || 'open',
                            details: details
                        });
                    } else {
                        setMessage(res.message || 'Failed to submit case.', true);
                    }
                } catch (err) {
                    setMessage('Request failed. Please try again.', true);
                }
            };
            var postData = 'details=' + encodeURIComponent(details);
            if (csrfName && csrfHash) {
                postData += '&' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
            }
            xhr.send(postData);
        });
    })();
    </script>

    <!-- Appointment Modal -->
    <div class="modal-overlay" id="appointment-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-plus"></i> Book Appointment</h3>
                <button type="button" class="modal-close" id="close-appointment-modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <div class="appointment-form-group">
                        <label for="appointment-type" class="appointment-form-label">Type <span class="required">*</span></label>
                        <select id="appointment-type" name="appointment_type" class="appointment-form-select" required>
                            <option value="">Select appointment type...</option>
                            <?php foreach ($appointment_types as $key => $label): ?>
                            <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-subject" class="appointment-form-label">Subject <span class="required">*</span></label>
                        <input type="text" id="appointment-subject" name="subject" class="appointment-form-input" placeholder="Brief description..." required>
                    </div>
                    <div class="appointment-form-row">
                        <div class="appointment-form-group">
                            <label for="appointment-date" class="appointment-form-label">Preferred Date <span class="required">*</span></label>
                            <input type="date" id="appointment-date" name="preferred_date" class="appointment-form-input" required>
                        </div>
                        <div class="appointment-form-group">
                            <label for="appointment-time" class="appointment-form-label">Preferred Time <span class="required">*</span></label>
                            <input type="time" id="appointment-time" name="preferred_time" class="appointment-form-input" required>
                        </div>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-duration" class="appointment-form-label">Duration <span class="required">*</span></label>
                        <select id="appointment-duration" name="duration_minutes" class="appointment-form-select" required>
                            <?php foreach ($duration_options as $minutes => $label): ?>
                            <option value="<?= (int)$minutes ?>" <?= $minutes == 30 ? 'selected' : '' ?>><?= html_escape($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-description" class="appointment-form-label">Description (optional)</label>
                        <textarea id="appointment-description" name="description" class="appointment-form-textarea" rows="4" placeholder="Additional details about your appointment..."></textarea>
                    </div>
                    <div class="appointment-form-submit-wrap">
                        <button type="submit" class="appointment-form-submit" id="appointment-submit-btn">
                            <i class="fas fa-paper-plane"></i> Request Appointment
                        </button>
                        <button type="button" class="appointment-form-cancel" id="appointment-cancel-btn">Cancel</button>
                    </div>
                    <div id="appointment-form-message" class="appointment-form-message" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Appointment Modal Script
    (function() {
        var modal = document.getElementById('appointment-modal');
        var openBtn = document.getElementById('open-appointment-modal');
        var closeBtn = document.getElementById('close-appointment-modal');
        var cancelBtn = document.getElementById('appointment-cancel-btn');
        var form = document.getElementById('appointment-form');
        var messageEl = document.getElementById('appointment-form-message');
        var submitBtn = document.getElementById('appointment-submit-btn');
        var upcomingTbody = document.getElementById('upcoming-appointments-tbody');
        var upcomingTable = document.getElementById('upcoming-appointments-table');
        var upcomingEmpty = document.getElementById('upcoming-appointments-empty');

        var submitUrl = <?= json_encode(site_url('customer_dashboard/submit_appointment/'.(isset($customer_code) ? $customer_code : ''))) ?>;
        var csrfName = <?= json_encode(isset($csrf_token_name) ? $csrf_token_name : '') ?>;
        var csrfHash = <?= json_encode(isset($csrf_hash) ? $csrf_hash : '') ?>;

        // Set minimum date to today
        var dateInput = document.getElementById('appointment-date');
        if (dateInput) {
            var today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            form.reset();
            messageEl.style.display = 'none';
            messageEl.textContent = '';
        }

        function setMessage(text, isError) {
            messageEl.textContent = text || '';
            messageEl.className = 'appointment-form-message' + (text ? (isError ? ' error' : ' success') : '');
            messageEl.style.display = text ? 'block' : 'none';
        }

        function escapeHtml(s) {
            var div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }

        function addAppointmentToTable(data) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(data.appointment_code) + '</td>' +
                '<td>' + escapeHtml(data.type_label || '') + '</td>' +
                '<td>' + escapeHtml(data.date_formatted || '') + '</td>' +
                '<td>' + escapeHtml(data.time_formatted || '') + '</td>' +
                '<td><span class="status appointment-status-pending">pending</span></td>';
            if (upcomingTbody) upcomingTbody.insertBefore(tr, upcomingTbody.firstChild);
            if (upcomingTable) upcomingTable.style.display = '';
            if (upcomingEmpty) upcomingEmpty.style.display = 'none';
        }

        if (openBtn) {
            openBtn.addEventListener('click', openModal);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            setMessage('');
            submitBtn.disabled = true;

            var formData = new FormData(form);
            var postData = '';
            formData.forEach(function(value, key) {
                if (postData) postData += '&';
                postData += encodeURIComponent(key) + '=' + encodeURIComponent(value);
            });
            if (csrfName && csrfHash) {
                postData += '&' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', submitUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                submitBtn.disabled = false;
                try {
                    var res = JSON.parse(xhr.responseText || '{}');
                    if (res.success) {
                        var msg = 'Appointment ' + (res.appointment_code || '') + ' requested successfully!';
                        if (res.email_sent) {
                            if (res.email_sent.staff_ok && res.email_sent.customer_ok) {
                                msg += ' Confirmation emails sent.';
                            } else if (res.email_sent.staff_ok) {
                                msg += ' Support team notified.';
                            }
                        }
                        setMessage(msg);
                        
                        // Get appointment type label
                        var typeSelect = document.getElementById('appointment-type');
                        var typeLabel = typeSelect.options[typeSelect.selectedIndex].text;
                        
                        // Format time
                        var timeInput = document.getElementById('appointment-time');
                        var timeValue = timeInput.value;
                        var timeFormatted = '';
                        if (timeValue) {
                            var timeParts = timeValue.split(':');
                            var hour = parseInt(timeParts[0]);
                            var min = timeParts[1];
                            var ampm = hour >= 12 ? 'PM' : 'AM';
                            hour = hour % 12;
                            hour = hour ? hour : 12;
                            timeFormatted = hour + ':' + min + ' ' + ampm;
                        }
                        
                        addAppointmentToTable({
                            appointment_code: res.appointment_code || '',
                            type_label: typeLabel,
                            date_formatted: res.preferred_date_formatted || '',
                            time_formatted: timeFormatted,
                            status: 'pending'
                        });
                        
                        setTimeout(function() {
                            closeModal();
                        }, 2500);
                    } else {
                        setMessage(res.message || 'Failed to submit appointment.', true);
                    }
                } catch (err) {
                    setMessage('Request failed. Please try again.', true);
                }
            };
            xhr.send(postData);
        });
    })();
    </script>

</body>

</html>