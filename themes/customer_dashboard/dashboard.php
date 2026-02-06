<?php defined('BASEPATH') OR exit('No direct script access allowed');
$currency_symbol = (!empty($currency) && isset($currency->symbol)) ? $currency->symbol : (!empty($Settings->default_currency) ? $Settings->default_currency : '€');
$format_amount = function($n) use ($currency_symbol) { return $currency_symbol . ' ' . number_format((float)$n, 2); };
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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
        /* Sidebar-aligned: orange accent (no blue on left / headings) */
        --bg: #E7E8FF;
        --surface: #f8fafc;
        --surface2: #e2e8f0;
        --text: #0f172a;
        --text-muted: #64748b;
        --accent: #F04F23;
        --accent-light: #e04820;
        --accent-dark: #c93d1a;
        --usa-blue: #002868;
        --usa-blue-light: #1e40af;
        --usa-red: #B22234;
        --usa-red-light: #DC2626;
        --success: #16a34a;
        --warning: #d97706;
        --danger: #B22234;
        --radius: 14px;
        --radius-sm: 8px;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    img {
        max-width: 100%;
        height: auto;
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
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
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

    @keyframes underlineSlide {
        from {
            transform: scaleX(0);
            opacity: 0;
        }

        to {
            transform: scaleX(1);
            opacity: 1;
        }
    }

    @keyframes sectionHeaderEnter {
        from {
            opacity: 0;
            transform: translateX(-16px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes sectionHeaderPulse {

        0%,
        100% {
            box-shadow: 0 3px 10px rgba(240, 79, 35, 0.35), 0 1px 0 rgba(255, 255, 255, 0.15) inset;
        }

        50% {
            box-shadow: 0 4px 16px rgba(240, 79, 35, 0.45), 0 1px 0 rgba(255, 255, 255, 0.2) inset;
        }
    }

    @keyframes ambientGlow {

        0%,
        100% {
            opacity: 0.85;
        }

        50% {
            opacity: 1;
        }
    }

    @keyframes headerWelcome {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes codeFadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes codePulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.92;
        }
    }

    @keyframes headerShine {
        0% {
            background-position: -120% 0;
        }

        100% {
            background-position: 220% 0;
        }
    }

    @keyframes headerTitleIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(0.97);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes welcomeBarPulse {

        0%,
        100% {
            opacity: 0.82;
        }

        50% {
            opacity: 1;
        }
    }

    html {
        height: 100%;
        overflow-x: hidden;
    }

    body {
        height: 100%;
        overflow: hidden;
        overflow-x: hidden;
        max-width: 100vw;
    }

    @media (max-width: 992px) {
        html {
            overflow-x: hidden;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            height: auto;
            min-height: 100%;
        }

        body {
            overflow-x: hidden !important;
            overflow-y: scroll !important;
            -webkit-overflow-scrolling: touch;
            height: auto !important;
            min-height: 100vh !important;
            overflow-scrolling: touch;
            touch-action: pan-y;
        }

        /* No fixed height – page grows with content, body scrolls (native full-height scrollbar) */
        .dashboard-wrap {
            position: static;
            flex-direction: column;
            width: 100%;
            height: auto;
            min-height: 100vh;
            max-height: none;
            overflow: visible;
            padding: 0;
        }

        /* Main column must not shrink so total height exceeds viewport and body can scroll */
        .dashboard {
            flex: 0;
            min-height: min-content;
            overflow: visible;
            overflow-x: hidden;
        }

        .dashboard-sidebar {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            margin: 0;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .dashboard {
            margin-top: 1.25rem;
            padding-top: 0.5rem;
        }

        .main-actions-section {
            margin-bottom: 2rem;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        background: var(--bg);
        color: var(--text);
        line-height: 1.4;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse 100% 60% at 20% 30%, rgba(240, 79, 35, 0.12), transparent 50%),
            radial-gradient(ellipse 80% 50% at 80% 20%, rgba(240, 79, 35, 0.08), transparent 45%),
            radial-gradient(ellipse 70% 60% at 70% 80%, rgba(66, 196, 75, 0.1), transparent 50%),
            radial-gradient(ellipse 60% 40% at 10% 70%, rgba(66, 196, 75, 0.06), transparent 45%);
        pointer-events: none;
        z-index: 0;
        animation: ambientGlow 12s ease-in-out infinite;
    }

    .dashboard-wrap {
        display: flex;
        flex-wrap: nowrap;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        margin: 0;
        padding: 0.5rem;
        gap: 0.5rem;
        min-height: 0;
        height: 100vh;
        align-items: stretch;
        box-sizing: border-box;
        background: #E7E8FF;
        z-index: 1;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    }

    /* Equal gap left/right; full height for all three columns */
    .dashboard-sidebar,
    .dashboard,
    .dashboard-right {
        min-height: calc(100vh - 1rem);
        font-size: 1.18rem;
    }

    .dashboard-sidebar {
        width: 23%;
        /*  flex: 0 0 23%; */
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: #E7E8FF;
        border: 1px solid var(--surface2);
        border-radius: 10px;
        margin: 0;
        /*   padding: 0.5rem 0.6rem; */
        position: relative;
        animation: slideInLeft 0.5s ease-out;
        /*  box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2); */
    }

    .dashboard-sidebar .footer-note.sidebar-footer {
        margin-top: 10px;
        flex-shrink: 0;
        margin-bottom: 0;
        padding: 0.75rem 0.6rem 1rem;
        text-align: center;
        font-size: 0.8rem;
        line-height: 1.35;
        background: #ffffff;
        color: #F04F23;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2);
    }

    .dashboard-sidebar .footer-note.sidebar-footer .footer-site {
        color: #F04F23;
        font-weight: 700;
        letter-spacing: 0.06em;
    }

    .dashboard-sidebar .footer-note.sidebar-footer .footer-sep {
        margin: 0 0.4rem;
        color: #F04F23;
    }

    .dashboard-sidebar .footer-note.sidebar-footer .footer-tagline {
        color: #F04F23;
        font-size: 0.75rem;
        font-weight: 500;
    }

    body::after {
        content: '';
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--success);
        z-index: 2;
    }

    .page-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1;
        text-align: center;
        padding: 0.75rem 1.25rem 0.9rem;
        padding-bottom: calc(0.9rem + 3px + env(safe-area-inset-bottom, 0px));
        font-size: 0.95rem;
        line-height: 1.4;
        color: #000000;
        background: linear-gradient(135deg, rgba(255, 248, 246, 0.97) 0%, rgba(231, 232, 255, 0.95) 50%, rgba(255, 248, 246, 0.97) 100%);
        background-size: 200% 200%;
        animation: footerGradientShift 8s ease infinite;
        pointer-events: none;
        overflow: hidden;
        box-shadow: 0 -4px 20px rgba(240, 79, 35, 0.06);
    }

    .page-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent 0%, #F04F23 20%, #c93d1a 50%, #F04F23 80%, transparent 100%);
        background-size: 200% 100%;
        animation: footerLineShine 4s ease-in-out infinite;
    }

    .page-footer-site {
        font-weight: 700;
        letter-spacing: 0.06em;
        color: #000000;
        animation: footerTextIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }

    .page-footer-sep {
        margin: 0 0.2rem;
        color: #000000;
        animation: footerTextIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s backwards;
    }

    .page-footer-tagline {
        font-weight: 500;
        color: #000000;
        animation: footerTextIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s backwards;
    }

    .page-footer-copyright {
        animation: footerTextIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s backwards;
    }

    @keyframes footerGradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    @keyframes footerLineShine {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    @keyframes footerTextIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    .sidebar-card {
        background: #ffffff;
        border: 1px solid var(--surface2);
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2);
        margin-bottom: 0.75rem;
        overflow: hidden;
    }

    .sidebar-card:last-of-type {
        margin-bottom: 0;
    }

    .sidebar-card-brand {
        text-align: center;
        padding: 1rem 0.75rem;
        position: relative;
    }

    .sidebar-card-brand::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #F04F23, #c93d1a);
        border-radius: 3px;
    }

    .sidebar-card-your-details,
    .sidebar-card-your-team,
    .sidebar-card-contact {
        padding: 0rem 0.6rem 1rem;
    }

    .sidebar-card-your-details {
        flex: 0 0 auto;
    }

    .sidebar-card-your-team {
        flex: 0 0 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
        padding-top: 0.5rem;
        padding-bottom: 0.65rem;
    }

    .sidebar-card-your-team .dashboard-sidebar-title {
        margin-bottom: 0.5rem;
        padding: 0.6rem 1rem;
    }

    .sidebar-card-your-details .dashboard-sidebar-title,
    .sidebar-card-your-team .dashboard-sidebar-title,
    .sidebar-card-contact .dashboard-sidebar-title {
        margin-top: 0;
    }

    .sidebar-card-your-team .sidebar-associates {
        margin-top: 0.2rem;
    }

    .sidebar-card-your-team .sidebar-associates .dashboard-sidebar-item {
        margin-bottom: 0.4rem;
        font-size: 0.95rem;
    }

    .sidebar-card-your-team .sidebar-associates .dashboard-sidebar-item:last-child {
        margin-bottom: 0;
    }

    .sidebar-card-your-team .dashboard-sidebar-item .label {
        font-size: 0.82rem;
    }

    .sidebar-card-your-team .dashboard-sidebar-item .val {
        font-size: 0.95rem;
    }

    .sidebar-contact-line {
        margin: 0.35rem 0;
        font-size: 1.08rem;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sidebar-contact-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        min-width: 1.5rem;
        color: #F04F23;
        font-size: 0.9rem;
    }

    .sidebar-card-brand .sidebar-logo {
        max-width: 120px;
        max-height: 48px;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto 0.5rem;
        filter: drop-shadow(0 2px 4px rgba(30, 41, 59, 0.1));
    }

    .sidebar-card-brand .sidebar-site-name {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin: 0;
        background: linear-gradient(135deg, #F04F23 0%, #c93d1a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        display: inline-block;
    }

    .sidebar-card-brand .sidebar-site-name.name-underline-animate::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        border-radius: 1px;
        animation: headerUnderline 2.5s ease-in-out infinite;
    }

    .dashboard-sidebar-title {
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        color: #ffffff;
        margin-bottom: 1.1rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
        padding: 0.9rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, #F04F23 0%, #e04820 50%, #c93d1a 100%);
        border: none;
        /*    border-radius: 0 12px 12px 0; */
        box-shadow: 0 3px 10px rgba(240, 79, 35, 0.35), 0 1px 0 rgba(255, 255, 255, 0.15) inset;
        position: relative;
        text-shadow: 0 1px 2px rgba(30, 41, 59, 0.15);
        animation: sectionHeaderEnter 0.5s ease-out backwards;
        overflow: hidden;
    }

    .dashboard-sidebar-title.sidebar-title-second {
        animation-delay: 0.2s;
    }

    .dashboard-sidebar-title:hover {
        animation: sectionHeaderPulse 1.5s ease-in-out infinite;
    }

    .dashboard-sidebar-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: rgba(30, 41, 59, 0.2);
        border-radius: 0 2px 2px 0;
    }

    .dashboard-sidebar-title i {
        color: #ffffff;
        font-size: 1rem;
        opacity: 0.95;
        filter: drop-shadow(0 1px 1px rgba(30, 41, 59, 0.2));
        display: inline-block;
        animation: headerWave 2s ease-in-out infinite;
    }

    .dashboard-sidebar-title.sidebar-title-second i {
        animation-delay: 0.35s;
    }

    .dashboard-sidebar-item {
        margin-bottom: 0.75rem;
        font-size: 1.05rem;
    }

    .dashboard-sidebar-item:last-child {
        margin-bottom: 0;
    }

    .dashboard-sidebar-item .label {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0.3rem;
    }

    .dashboard-sidebar-item .label i {
        color: #F04F23;
        width: 1.1rem;
        font-size: 0.9rem;
    }

    .dashboard-sidebar-item .val {
        font-size: 1.05rem;
        font-weight: 500;
        color: var(--text);
        word-break: break-word;
    }

    .dashboard-sidebar-item a.val {
        color: var(--usa-blue-light);
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
        flex: 0 0 54%;
        width: 54%;
        min-width: 0;
        min-height: 0;
        align-self: stretch;
        margin: 0;
        padding: 0.5rem 0.6rem;
        padding-bottom: 1rem;
    }

    .dashboard-right {
        /*  flex: 0 0 23%; */
        width: 23%;
        min-width: 0;
        align-self: stretch;
        min-height: calc(100vh - 1rem);
        margin: 0;
        padding: 0.5rem 0.6rem;
        padding-bottom: 1rem;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        animation: fadeInUp 0.5s ease-out 0.1s backwards;
        background: #E7E8FF;
        border: 1px solid var(--surface2);
        border-radius: 10px;
        /*  box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2); */
    }

    .header {
        text-align: center;
        margin-bottom: 0.75rem;
        padding: 0.85rem 1rem;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 248, 246, 0.98) 50%, #ffffff 100%);
        background-size: 200% 200%;
        animation: headerWelcome 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards, headerGradientShift 12s ease infinite;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2);
        position: relative;
        overflow: hidden;
    }

    @keyframes headerGradientShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    .header-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(35px);
        pointer-events: none;
        opacity: 0.35;
        z-index: 0;
    }

    .header-orb-1 {
        top: -30%;
        right: -10%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(240, 79, 35, 0.4) 0%, transparent 70%);
        animation: headerFloat 18s ease-in-out infinite;
    }

    .header-orb-2 {
        bottom: -40%;
        left: -15%;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, rgba(240, 79, 35, 0.3) 0%, transparent 70%);
        animation: headerFloat 14s ease-in-out infinite reverse;
    }

    @keyframes headerFloat {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        33% {
            transform: translate(15px, -10px) scale(1.05);
        }

        66% {
            transform: translate(-10px, 8px) scale(0.98);
        }
    }

    .header-decoration {
        position: absolute;
        top: 12px;
        right: 18px;
        width: 42px;
        height: 42px;
        background: rgba(240, 79, 35, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: var(--accent);
        opacity: 0.7;
        animation: headerRotate 20s linear infinite;
        z-index: 1;
    }

    .header .welcome-greeting,
    .header h1,
    .header p {
        position: relative;
        z-index: 2;
    }

    @keyframes headerRotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .header .welcome-greeting {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
        font-size: 1.4rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--accent);
        animation: codeFadeIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.1s backwards;
    }

    .header .welcome-greeting .welcome-icon {
        font-size: 1rem;
        animation: headerWave 2s ease-in-out infinite;
    }

    @keyframes headerWave {

        0%,
        100% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(15deg);
        }

        75% {
            transform: rotate(-15deg);
        }
    }

    .header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(105deg, transparent 0%, transparent 38%, rgba(255, 255, 255, 0.5) 50%, transparent 62%, transparent 100%);
        background-size: 200% 100%;
        background-position: -120% 0;
        animation: headerShine 1.4s ease-out 0.5s forwards;
        pointer-events: none;
        border-radius: 12px;
    }

    .header::after {
        display: none;
    }

    .header-badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--accent);
        margin-bottom: 0.35rem;
    }

    .header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
        color: var(--text);
        letter-spacing: -0.02em;
        animation: headerTitleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s backwards;
    }

    .header-secondary-name {
        margin: 0 0 0.35rem;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.01em;
        animation: headerTitleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s backwards;
    }

    .header h1 .user-name {
        display: inline-block;
        position: relative;
        padding: 0 4px;
    }

    .header h1 .user-name::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        border-radius: 1px;
        animation: headerUnderline 2.5s ease-in-out infinite;
    }

    @keyframes headerUnderline {

        0%,
        100% {
            transform: scaleX(0);
            opacity: 0;
        }

        50% {
            transform: scaleX(1);
            opacity: 0.9;
        }
    }

    .header p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
    }

    .header-meta {
        margin: 0.6rem 0 0;
        font-size: 0.9rem;
        color: var(--accent);
        animation: codeFadeIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.2s backwards;
    }

    .header-meta-item {
        white-space: nowrap;
    }

    .header-meta-label {
        font-weight: 500;
        color: var(--accent);
    }

    .header-meta-value {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: var(--accent);
    }

    .header-customer-code-value {
        color: var(--accent);
        letter-spacing: 0.03em;
    }

    .header-meta-dot {
        margin: 0 0.5rem;
        opacity: 0.7;
        font-weight: 300;
        color: var(--accent);
    }

    /* License / Support cards – compact so three fit, with gap */
    .licenses-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.7rem;
        margin-bottom: 0.4rem;
        /*   margin-left: auto;
        margin-right: auto; */
        /*   max-width: 680px; */
        flex-shrink: 0;
    }

    .license-card {
        background: #ffffff;
        border: 1px solid var(--surface2);
        border-radius: 10px;
        padding: 0.35rem 0.3rem;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2);
        animation: fadeInUp 0.5s ease-out backwards;
        position: relative;
        overflow: hidden;
    }

    .license-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #F04F23, #c93d1a);
        border-radius: 4px 0 0 4px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .license-card:hover {
        transform: translateY(-3px);
        border-color: rgba(242, 92, 47, 0.25);
        box-shadow: 0 12px 28px rgba(242, 92, 47, 0.12), 0 4px 8px rgba(30, 41, 59, 0.06);
    }

    .license-card:hover::before {
        opacity: 1;
    }

    .licenses-grid .license-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .licenses-grid .license-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .licenses-grid .license-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .license-gauge-top {
        margin-bottom: 0;
    }

    /* Compact progress/status inside service cards */
    .license-card .progress-wrap {
        margin-bottom: 0.15rem;
    }

    .license-card .progress-label-row .label,
    .license-card .progress-label-row .pct {
        font-size: 0.5rem;
    }

    .license-card .progress-bar-outer {
        height: 5px;
    }

    .license-card .license-status {
        font-size: 0.55rem;
        margin-top: 0.08rem;
    }

    .license-card-header {
        padding-top: 0.55rem;
        border-top: 1px solid var(--surface2);
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
        color: var(--accent);
    }

    .progress-bar-outer {
        height: 14px;
        background: rgba(30, 41, 59, 0.25);
        border-radius: 100px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(30, 41, 59, 0.2);
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

    /* Semi-circular gauge */
    .gauge-wrap {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.25rem 1.2rem;
        margin-bottom: 0;
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
        background: linear-gradient(90deg, transparent, rgba(242, 92, 47, 0.15), transparent);
        pointer-events: none;
    }

    .gauge-wrap .gauge-title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text);
        margin-bottom: 0.35rem;
        opacity: 1;
    }

    .gauge-container {
        position: relative;
        width: 100%;
        max-width: 200px;
        margin: 0 auto 0.2rem;
    }

    /* Circular gauge (donut style) */
    .circle-gauge {
        position: relative;
        width: 100%;
        max-width: 110px;
        aspect-ratio: 1 / 1;
        margin: 0.35rem auto 0.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .circle-gauge svg {
        display: block;
        width: 100%;
        height: auto;
    }

    .circle-gauge-track {
        fill: none;
        stroke: #e5e7eb;
        stroke-width: 10;
    }

    .circle-gauge-fill {
        fill: none;
        stroke: #16a34a;
        stroke-width: 10;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .circle-gauge-expired .circle-gauge-fill {
        stroke: #ef4444;
    }

    .circle-gauge-inactive .circle-gauge-fill {
        stroke: #ef4444;
    }

    .circle-gauge-expired {
        position: relative;
    }

    .gauge-center-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 1;
    }

    .gauge-center-icon--expired {
        color: #b91c1c;
        font-size: 1.5rem;
    }

    .gauge-center-icon--active {
        color: #16a34a;
        font-size: 1.5rem;
    }

    .gauge-center-icon--inactive {
        color: #b91c1c;
        font-size: 1.5rem;
    }

    .circle-gauge::after {
        content: '';
        position: absolute;
        inset: 13px;
        border-radius: 50%;
        background: #ffffff;
    }

    .gauge-value-wrap {
        text-align: center;
        margin: 0.15rem 0 0;
    }

    .gauge-value-sublabel {
        font-size: 0.55rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-top: 0.12rem;
        opacity: 0.9;
    }

    .gauge-value-wrap .gauge-value-sublabel {
        color: var(--text-muted);
    }

    .gauge-value-wrap .gauge-value.green+.gauge-value-sublabel {
        color: #16a34a !important;
    }

    .gauge-value-wrap .gauge-value.yellow+.gauge-value-sublabel {
        color: #ca8a04 !important;
    }

    .gauge-value-wrap .gauge-value.red+.gauge-value-sublabel {
        color: #dc2626 !important;
    }

    .gauge-value {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        line-height: 1.1;
        padding: 0.12rem 0.28rem;
        border-radius: 5px;
        background: #ffffff;
        border: 1px solid var(--surface2);
        box-shadow: 0 2px 4px rgba(30, 41, 59, 0.2);
    }

    .gauge-value-dot {
        width: 0.4rem;
        height: 0.4rem;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .gauge-value.green .gauge-value-dot {
        background: var(--success);
    }

    .gauge-value.red .gauge-value-dot {
        background: var(--danger);
    }

    .gauge-value.yellow .gauge-value-dot {
        background: #ca8a04;
    }

    /* Remaining % colour: green (plenty) → yellow (low) → red (critical) */
    .gauge-value.green,
    .gauge-value.green * {
        color: var(--success) !important;
    }

    .gauge-value.green {
        border-color: rgba(66, 196, 75, 0.35);
        box-shadow: 0 2px 10px rgba(66, 196, 75, 0.2), 0 1px 2px rgba(30, 41, 59, 0.06);
    }

    .gauge-value.yellow,
    .gauge-value.yellow * {
        color: #ca8a04 !important;
    }

    .gauge-value.yellow {
        border-color: rgba(202, 138, 4, 0.4);
        box-shadow: 0 2px 10px rgba(202, 138, 4, 0.2), 0 1px 2px rgba(30, 41, 59, 0.06);
    }

    .gauge-value.red,
    .gauge-value.red * {
        color: var(--danger) !important;
    }

    .gauge-value.red {
        border-color: rgba(231, 76, 60, 0.4);
        box-shadow: 0 2px 10px rgba(231, 76, 60, 0.2), 0 1px 2px rgba(30, 41, 59, 0.06);
    }

    .progress-bar-inner.expired {
        background: linear-gradient(90deg, #7f1d1d, #ef4444);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
    }

    .progress-bar-inner.no-expiry {
        background: linear-gradient(90deg, #002868 0%, #1e40af 100%);
        box-shadow: 0 0 10px rgba(0, 40, 104, 0.3);
    }

    .license-status {
        font-size: 0.78rem;
        font-weight: 600;
        margin-top: 0.2rem;
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

    .section {
        background-color: #E7E8FF;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 0;
        box-shadow: 0 1px 2px rgba(30, 41, 59, 0.05);
    }

    .section-head {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.45rem 0.6rem;
        border-bottom: none;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        background: linear-gradient(135deg, rgba(240, 79, 35, 0.12) 0%, rgba(255, 255, 255, 0.7) 100%);
        border-left: 4px solid #F04F23;
        border-radius: 0 8px 0 0;
        box-shadow: 0 1px 2px rgba(240, 79, 35, 0.12);
    }

    .section-head i {
        color: #F04F23;
    }

    .section-head::after {
        content: '';
        flex: 1;
        height: 2px;
        margin-left: 0.5rem;
        background: linear-gradient(90deg, transparent, rgba(242, 92, 47, 0.25));
        border-radius: 2px;
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
        margin-top: 0.5rem;
        padding-top: 0.4rem;
        font-size: 0.6rem;
        color: var(--text-muted);
        border-top: 1px solid var(--surface2);
    }

    .footer-note .footer-site {
        font-weight: 600;
        color: var(--text);
    }

    .footer-note .footer-sep {
        margin: 0 0.4rem;
        color: var(--text-muted);
    }

    .footer-note .footer-tagline {
        color: var(--text-muted);
    }

    /* Right-side support cards (Technical support, Service details, Contact us) */
    .support-cards-section {
        margin: 0;
        flex: 1 1 auto;
    }

    .support-cards-column {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        height: 100%;
    }

    .support-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 2px 12px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        flex: 1 1 1;
        position: relative;
        transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        animation: supportCardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) backwards;
    }

    .support-card:nth-child(1) {
        animation-delay: 0.05s;
    }

    .support-card:nth-child(2) {
        animation-delay: 0.15s;
    }

    .support-card:nth-child(3) {
        animation-delay: 0.25s;
    }

    .support-card:nth-child(4) {
        animation-delay: 0.35s;
    }

    .support-card::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(0, 71, 255, 0.35), rgba(240, 79, 35, 0.25));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .support-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(0, 71, 255, 0.08);
        border-color: rgba(0, 40, 104, 0.15);
    }

    .support-card:hover::before {
        opacity: 1;
    }

    .support-card-technical {
        padding: 0;
        border-left: 4px solid #002868;
    }

    .support-card-technical-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(135deg, #0047ff 0%, #002868 50%, #001a3d 100%);
        background-size: 200% 200%;
        animation: supportHeaderShift 8s ease-in-out infinite;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 0.75rem;
    }

    .support-card-icon-header {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.28rem;
        flex-shrink: 0;
        animation: supportIconIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.2s backwards;
        transition: transform 0.3s ease, background 0.3s ease;
    }

    .support-card:hover .support-card-icon-header {
        animation: supportIconRing 0.6s ease;
    }

    .support-card-icon-header i {
        color: #fff;
    }

    .support-card-technical-header::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
        animation: supportShine 3s ease-in-out infinite;
    }

    .support-card-technical-title {
        font-size: 1.12rem;
        font-weight: 600;
        opacity: 0.95;
        letter-spacing: 0.02em;
        position: relative;
        z-index: 1;
    }

    .support-card-technical-phone {
        margin-top: 0.35rem;
        display: inline-block;
        padding: 0.35rem 0.9rem;
        font-size: 1.26rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        background: rgba(255, 255, 255, 0.95);
        color: #002868;
        border-radius: 999px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        z-index: 1;
    }

    .support-card-technical-phone:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .support-card-technical-body {
        padding: 0.75rem 1rem 0.9rem;
        font-size: 1.08rem;
        color: var(--text-muted);
        line-height: 1.45;
    }

    .support-card-service,
    .support-card-stats,
    .support-card-activity {
        padding: 0.4rem 0.75rem 0.5rem;
        border-left: 4px solid var(--accent);
    }

    .support-card-service .support-card-heading,
    .support-card-stats .support-card-heading,
    .support-card-activity .support-card-heading {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.28rem;
        font-size: 1rem;
    }

    .support-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.9rem;
        height: 1.9rem;
        min-width: 1.9rem;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(0, 71, 255, 0.12), rgba(0, 40, 104, 0.08));
        color: var(--accent);
        font-size: 0.95rem;
        margin-right: 0.4rem;
        animation: supportIconIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) backwards;
        transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease;
    }

    .support-card-service .support-card-icon,
    .support-card-stats .support-card-icon,
    .support-card-activity .support-card-icon {
        width: 1.45rem;
        height: 1.45rem;
        min-width: 1.45rem;
        font-size: 0.95rem;
    }

    .support-card-service .support-card-icon {
        animation-delay: 0.2s;
    }

    .support-card-activity .support-card-icon {
        animation-delay: 0.35s;
    }

    .support-card-contact .support-card-icon {
        animation-delay: 0.3s;
    }

    .support-card:hover .support-card-icon {
        transform: scale(1.1) rotate(-5deg);
        background: linear-gradient(135deg, rgba(0, 71, 255, 0.2), rgba(0, 40, 104, 0.15));
        color: #002868;
    }

    .support-card-heading {
        margin: 0 0 0.6rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        letter-spacing: 0.02em;
    }

    .support-details-list {
        margin: 0;
        padding: 0;
    }

    .support-details-row {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        font-size: 0.98rem;
        padding: 0.06rem 0;
        border-bottom: 1px dashed var(--surface2);
    }

    .support-details-row:last-child {
        border-bottom: none;
    }

    .support-details-row dt {
        margin: 0;
        font-weight: 600;
        color: var(--text-muted);
    }

    .support-details-row dd {
        margin: 0;
        font-weight: 600;
        color: var(--text);
        text-align: right;
    }

    .support-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        font-size: 0.95rem;
        font-weight: 600;
        color: #374151;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .support-status-dot {
        width: 0.4rem;
        height: 0.4rem;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .support-status-badge--active .support-status-dot {
        background: #22c55e;
    }

    .support-status-badge--expired .support-status-dot {
        background: #ef4444;
    }

    .support-status-badge--inactive .support-status-dot {
        background: #9ca3af;
    }

    .support-stats-list {
        margin: 0;
        padding: 0;
    }

    .support-stats-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.12rem 0;
        font-size: 0.98rem;
        border-bottom: 1px dashed var(--surface2);
    }

    .support-stats-row:last-child {
        border-bottom: none;
    }

    .support-stats-label {
        font-weight: 600;
        color: var(--text-muted);
    }

    .support-stats-label i {
        margin-right: 0.35rem;
        color: var(--accent);
    }

    .support-stats-value {
        font-weight: 700;
        color: var(--text);
        font-size: 1.05rem;
    }

    .support-stats-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.75rem;
        height: 1.75rem;
        padding: 0 0.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #F04F23 0%, #e04820 50%, #c93d1a 100%);
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(240, 79, 35, 0.35);
    }

    .support-activity-list {
        margin: 0;
        padding: 0;
    }

    .support-activity-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.35rem 0;
        font-size: 0.98rem;
        font-weight: 500;
        color: var(--text);
    }

    .support-activity-row:first-child {
        padding-top: 0.2rem;
    }

    .support-activity-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .support-activity-row--success .support-activity-dot {
        background: #22c55e;
    }

    .support-activity-text {
        line-height: 1.35;
    }

    @keyframes supportCardIn {
        from {
            opacity: 0;
            transform: translateY(14px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes supportHeaderShift {

        0%,
        100% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }
    }

    @keyframes supportShine {
        0% {
            left: -100%;
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        100% {
            left: 100%;
            opacity: 0;
        }
    }

    @keyframes supportIconIn {
        from {
            opacity: 0;
            transform: scale(0.5) rotate(-12deg);
        }

        to {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
    }

    @keyframes supportIconRing {

        0%,
        100% {
            transform: scale(1) rotate(0deg);
        }

        25% {
            transform: scale(1.15) rotate(-8deg);
        }

        50% {
            transform: scale(1.1) rotate(4deg);
        }

        75% {
            transform: scale(1.15) rotate(-4deg);
        }
    }

    /* Main window: Cases and Book appointment – separate cards */
    .main-actions-section {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
    }

    .main-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
        /* background-color: #E5E4F0; */
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        border-radius: 16px;
        align-items: stretch;
    }

    /* Each action is its own card */
    .action-card {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid var(--surface2);
        border-radius: 14px;
        border-left: 4px solid var(--accent);
        padding: 0;
        box-shadow: 0 2px 10px rgba(30, 41, 59, 0.06), 0 1px 3px rgba(30, 41, 59, 0.04);
        overflow: hidden;
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
    }

    .action-card:hover {
        box-shadow: 0 6px 20px rgba(30, 41, 59, 0.08), 0 2px 6px rgba(240, 79, 35, 0.06);
        border-left-color: var(--accent-light);
    }

    .cases-card {
        border-left-color: var(--accent);
    }

    .appointments-card {
        border-left-color: var(--accent);
    }

    .main-action-block {
        background: #ffffff;
        border: none;
        border-radius: inherit;
        border-left: none;
        padding: 0;
        box-shadow: none;
        overflow: hidden;
        transition: none;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .main-action-block:hover {
        box-shadow: none;
        border-left-color: transparent;
    }

    .action-card .section-head,
    .main-action-block .section-head {
        margin: 0;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text);
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.6) 0%, transparent 100%);
        border-left: none;
        border-bottom: 1px solid var(--accent);
        border-radius: 0;
        box-shadow: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-card .section-head i,
    .main-action-block .section-head i {
        color: var(--accent);
        font-size: 1rem;
        opacity: 1;
        width: 1.1rem;
        text-align: center;
        display: inline-block;
        animation: headerWave 2s ease-in-out infinite;
    }

    .action-card .section-head::after,
    .main-action-block .section-head::after {
        content: '';
        flex: 1;
        height: 2px;
        margin-left: 0.5rem;
        background: linear-gradient(90deg, var(--surface2) 0%, transparent 100%);
        border-radius: 1px;
    }

    .action-card .case-form-wrap,
    .main-action-block .case-form-wrap {
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .appointment-card-body {
        padding: 1.25rem 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }

    .appointment-card-desc {
        font-size: 0.875rem;
        color: var(--text-muted);
        line-height: 1.45;
        margin: 0;
        max-width: 250px;
    }

    .appointments-card .case-form-blocked,
    .main-action-block.appointment-block>.case-form-blocked {
        margin: 0;
    }

    .appointments-card .appointment-btn,
    .main-action-block.appointment-block>.appointment-btn {
        margin: 0;
    }

    .action-card .section-subhead,
    .main-action-block .section-subhead {
        margin-bottom: 0.5rem;
    }

    .section-subhead {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-subhead i {
        color: #F04F23;
    }

    .main-action-block .appointment-btn-wrap {
        margin-top: 0;
        padding: 1.25rem 1.25rem 1.5rem;
        border-top: none;
    }

    .appointment-btn-main {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1.75rem;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        min-width: 200px;
        max-width: 100%;
        border-radius: 10px;
        transition: background-color 0.2s ease, transform 0.15s ease;
    }

    .appointment-btn-main:not(:disabled):hover {
        transform: translateY(-1px);
    }

    .sidebar-associates {
        margin-top: 0.5rem;
    }

    .sidebar-associates .dashboard-sidebar-item {
        margin-bottom: 0.85rem;
    }

    @media (max-width: 900px) {
        .main-actions-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    /* Open Case form – professional */
    .case-form-wrap {
        margin-top: 0;
    }

    .case-form-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 0.85rem;
        line-height: 1.45;
    }

    .case-form-blocked {
        font-size: 0.8rem;
        color: var(--warning);
        margin-bottom: 0;
        padding: 0.75rem 0.9rem;
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.2);
        border-radius: 10px;
        line-height: 1.45;
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
    }

    .case-form-blocked i {
        margin-top: 0.12rem;
        flex-shrink: 0;
    }

    .case-form-group {
        margin-bottom: 0.6rem;
    }

    .case-form-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.3rem;
    }

    .case-form-label .required {
        color: var(--danger);
    }

    .case-form-textarea {
        width: 100%;
        padding: 0.6rem 0.75rem;
        font-family: inherit;
        font-size: 0.875rem;
        color: var(--text);
        background: var(--surface);
        border: 1px solid var(--surface2);
        border-radius: 10px;
        resize: vertical;
        min-height: 3em;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .case-form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(240, 79, 35, 0.12);
    }

    .case-form-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.45rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.02em;
        background: linear-gradient(135deg, #F04F23 0%, #c93d1a 100%);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(240, 79, 35, 0.3), 0 1px 0 rgba(255, 255, 255, 0.15) inset;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .case-form-submit i {
        font-size: 0.95rem;
        opacity: 0.95;
        color: #fff;
    }

    .case-form-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        background: var(--accent-dark);
        box-shadow: 0 4px 14px rgba(242, 92, 47, 0.45), 0 1px 0 rgba(255, 255, 255, 0.2) inset;
    }

    .case-form-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(240, 79, 35, 0.4) inset;
    }

    .case-form-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .case-form-message {
        margin-top: 0.35rem;
        font-size: 0.65rem;
        min-height: 1em;
    }

    .case-form-message.success {
        color: var(--success);
    }

    .case-form-message.error {
        color: var(--danger);
    }

    .case-details-cell {
        max-width: 250px;
        word-break: break-word;
    }

    .status.case-status-open {
        background: rgba(245, 158, 11, 0.2);
        color: var(--warning);
    }

    .status.case-status-in_progress {
        background: rgba(0, 40, 104, 0.15);
        color: var(--usa-blue);
    }

    .status.case-status-closed {
        background: rgba(34, 197, 94, 0.2);
        color: var(--success);
    }

    /* Appointment Button – professional */
    .appointment-btn-wrap {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    .appointment-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        width: 100%;
        padding: 0.55rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.02em;
        background: linear-gradient(135deg, #F04F23 0%, #e04820 50%, #c93d1a 100%);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(240, 79, 35, 0.3), 0 1px 0 rgba(255, 255, 255, 0.15) inset;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .appointment-btn i {
        font-size: 1.05rem;
        opacity: 0.95;
        color: #fff;
    }

    .appointment-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        background: #e04820;
        box-shadow: 0 4px 14px rgba(240, 79, 35, 0.45), 0 1px 0 rgba(255, 255, 255, 0.2) inset;
    }

    .appointment-btn:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(240, 79, 35, 0.4) inset;
    }

    .appointment-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Modal Overlay & Container */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(30, 41, 59, 0.7);
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

    .modal-container {
        background: #ffffff;
        border: 1px solid var(--surface2);
        border-radius: var(--radius);
        max-width: 540px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(30, 41, 59, 0.15);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(40px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid var(--surface2);
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
        color: var(--usa-blue);
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
        background: var(--surface2);
        color: var(--text);
    }

    .modal-body {
        padding: 1.75rem;
    }

    .modal-footer {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid var(--surface2);
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
        border-color: #F04F23;
        background: #ffffff;
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
        padding: 0.8rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #fff;
        letter-spacing: 0.02em;
        background: #F04F23;
        border: none;
        border-radius: 100px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(240, 79, 35, 0.35), 0 1px 0 rgba(255, 255, 255, 0.15) inset;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .appointment-form-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        background: #e04820;
        box-shadow: 0 4px 14px rgba(240, 79, 35, 0.45), 0 1px 0 rgba(255, 255, 255, 0.2) inset;
    }

    .appointment-form-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(240, 79, 35, 0.4) inset;
    }

    .appointment-form-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .appointment-form-cancel {
        padding: 0.8rem 1.35rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-muted);
        background: #ffffff;
        border: 1px solid var(--surface2);
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, color 0.2s;
    }

    .appointment-form-cancel:hover {
        background: var(--surface);
        border-color: #F04F23;
        color: #F04F23;
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
        background: rgba(0, 40, 104, 0.15);
        color: var(--usa-blue);
    }

    .status.appointment-status-completed {
        background: rgba(100, 116, 139, 0.2);
        color: var(--text-muted);
    }

    .status.appointment-status-cancelled {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger);
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
            padding: 0;
            gap: 0;
            height: auto;
            min-height: 100vh;
            overflow: visible;
        }

        .dashboard-sidebar {
            width: 100%;
            position: static;
            order: 1;
            padding: 1rem;
            padding-top: max(1rem, env(safe-area-inset-top, 0px));
        }

        .dashboard {
            order: 2;
            flex: 1 1 auto;
            width: 100%;
            padding: 1rem;
            padding-bottom: calc(2rem + env(safe-area-inset-bottom, 0px));
            overflow: visible;
            min-height: min-content;
        }

        .dashboard-right {
            order: 3;
            flex: 1 1 auto;
            width: 100%;
            margin: 0;
            padding: 1rem;
            padding-top: 0;
        }

        .licenses-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
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

        .dashboard-sidebar,
        .dashboard {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .dashboard-sidebar {
            padding-top: max(0.75rem, env(safe-area-inset-top, 0px));
        }

        .dashboard {
            padding-bottom: calc(2rem + env(safe-area-inset-bottom, 0px));
        }

        .header {
            padding: 1rem 1rem;
            margin-bottom: 1.25rem;
        }

        .header h1 {
            font-size: 1.2rem;
        }

        .header p {
            font-size: 0.78rem;
        }

        .licenses-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .license-card {
            padding: 0.9rem 0.75rem;
        }

        .gauge-value {
            font-size: 1rem;
            padding: 0.35rem 0.65rem;
        }

        .gauge-container {
            max-width: 160px;
        }

        .section-head,
        th,
        td {
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

    @media (max-width: 768px) {
        .main-action-block .section-head {
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .main-action-block .case-form-wrap {
            padding: 1rem 1rem 1.25rem;
        }

        .main-action-block.appointment-block>.case-form-blocked,
        .main-action-block.appointment-block>.appointment-btn {
            margin: 1rem;
        }

        .case-form-textarea {
            min-height: 3em;
        }

        .case-form-submit,
        .appointment-btn {
            min-height: 44px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-wrap {
            padding: 0.5rem;
            padding-bottom: calc(2.5rem + env(safe-area-inset-bottom, 0px));
            gap: 1rem;
        }

        .main-actions-section {
            margin-bottom: 1.5rem;
        }

        .header {
            padding: 0.85rem 0.75rem;
            margin-bottom: 1rem;
        }

        .header h1 {
            font-size: 1.2rem;
        }

        .header-info-chip {
            padding: 0.4rem 0.7rem;
        }

        .header-customer-code .header-customer-code-value {
            font-size: 1.05rem;
        }

        .license-card {
            padding: 0.75rem 0.65rem;
        }

        .licenses-grid {
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .gauge-wrap {
            padding: 0.65rem 0.6rem;
        }

        .gauge-wrap .gauge-title {
            font-size: 0.6rem;
        }

        .gauge-value {
            font-size: 0.6rem;
            padding: 0.25rem 0.4rem;
        }

        .gauge-container {
            max-width: 180px;
        }

        .section-head {
            font-size: 0.8rem;
            padding: 0.7rem 0.9rem;
        }

        .main-action-block .section-head {
            padding: 0.75rem 0.9rem;
            font-size: 0.85rem;
        }

        .main-action-block .case-form-wrap {
            padding: 1rem 0.9rem 1.25rem;
        }

        .main-action-block.appointment-block>.case-form-blocked,
        .main-action-block.appointment-block>.appointment-btn {
            margin: 1rem 0.9rem;
        }

        th,
        td {
            padding: 0.5rem 0.6rem;
            font-size: 0.75rem;
        }

        table {
            min-width: 280px;
        }

        .table-wrap {
            margin: 0 -0.5rem;
            padding: 0 0.5rem;
        }

        .appointment-form-submit-wrap {
            flex-direction: column;
            gap: 0.5rem;
        }

        .appointment-form-submit,
        .appointment-form-cancel {
            width: 100%;
            min-height: 44px;
        }

        .appointment-btn {
            padding: 0.9rem 1rem;
            font-size: 0.9rem;
            min-height: 48px;
        }

        .case-form-submit {
            width: 100%;
            min-height: 44px;
        }

        .sidebar-card-brand,
        .sidebar-card-your-details,
        .sidebar-card-contact {
            padding: 0.85rem 0.65rem;
        }

        .sidebar-card-your-team {
            padding: 0.5rem 0.65rem 0.65rem;
        }

        .sidebar-logo {
            max-width: 110px;
        }

        .sidebar-site-name {
            font-size: 1rem;
        }

        .dashboard-sidebar-title {
            padding: 0.75rem 0.85rem;
            font-size: 0.95rem;
        }

        .dashboard-sidebar .footer-note.sidebar-footer {
            padding: 0.6rem 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        body {
            font-size: 15px;
        }

        .dashboard-wrap {
            padding: 0.4rem;
            padding-bottom: calc(3rem + env(safe-area-inset-bottom, 0px));
        }

        .main-actions-section {
            margin-bottom: 2rem;
        }

        .header {
            padding: 0.7rem 0.6rem;
            margin-bottom: 0.85rem;
        }

        .header h1 {
            font-size: 1.1rem;
        }

        .header-meta {
            font-size: 0.8rem;
        }

        .license-card {
            padding: 0.65rem 0.55rem;
        }

        .license-card-header {
            padding-top: 0.85rem;
        }

        .gauge-wrap {
            padding: 0.55rem 0.5rem;
        }

        .gauge-container {
            max-width: 160px;
        }

        .gauge-value-wrap {
            margin: 0.2rem 0 0;
        }

        .gauge-value {
            font-size: 0.58rem;
        }

        .section-head {
            font-size: 0.75rem;
            padding: 0.6rem 0.75rem;
        }

        .main-actions-grid {
            gap: 1.25rem;
        }

        .main-action-block .case-form-wrap {
            padding: 0.85rem 0.75rem 1rem;
        }

        .main-action-block.appointment-block>.case-form-blocked,
        .main-action-block.appointment-block>.appointment-btn {
            margin: 0.85rem 0.75rem;
        }

        .modal-overlay {
            padding: 0.5rem;
            align-items: flex-end;
        }

        .modal-container {
            max-width: 100%;
            max-height: 92vh;
            margin: 0;
            border-radius: 12px 12px 0 0;
        }

        .modal-header {
            padding: 1rem 1.1rem;
        }

        .modal-header h3 {
            font-size: 1rem;
        }

        .modal-body {
            padding: 1.1rem;
        }

        .modal-footer {
            padding: 1rem 1.1rem;
            flex-wrap: wrap;
        }

        .appointment-form-input,
        .appointment-form-select,
        .appointment-form-textarea {
            min-height: 44px;
            padding: 0.65rem 0.75rem;
        }

        .appointment-form-textarea {
            min-height: 4rem;
        }

        .case-form-textarea {
            min-height: 3.5em;
        }

        th,
        td {
            padding: 0.45rem 0.5rem;
            font-size: 0.7rem;
        }

        table {
            min-width: 260px;
        }
    }

    @media (max-width: 360px) {
        .dashboard-wrap {
            padding: 0.35rem;
        }

        .header h1 {
            font-size: 1rem;
        }

        .sidebar-logo {
            max-width: 100px;
        }

        .sidebar-site-name {
            font-size: 0.95rem;
        }

        .support-cards-section {
            margin-top: 0.5rem;
        }

        .gauge-container {
            max-width: 140px;
        }

        .main-action-block .section-head {
            font-size: 0.8rem;
        }
    }
    </style>
</head>

<body>
    <div class="dashboard-wrap">
        <aside class="dashboard-sidebar">
            <!-- Card 1: Company / Brand -->
            <div class="sidebar-card sidebar-card-brand">
                <?php if (!empty($logo_url)): ?>
                <img src="<?= html_escape($logo_url) ?>" alt="<?= html_escape($site_name) ?>" class="sidebar-logo">
                <?php endif; ?>
                <div class="sidebar-site-name"><?= html_escape($site_name) ?></div>
            </div>

            <!-- Card 2: Your Details -->
            <div class="sidebar-card sidebar-card-your-details">
                <h2 class="dashboard-sidebar-title"><i class="fas fa-user-circle"></i> Your Details</h2>
                <?php $contact_name = trim(($customer->name ?? '') . ' ' . ($customer->last_name ?? '')); if ($contact_name !== ''): ?>
                <div class="dashboard-sidebar-item">
                    <span class="label"><i class="fas fa-user"></i> Name</span>
                    <span class="val"><?= html_escape($contact_name) ?></span>
                </div>
                <?php endif; ?>
                <div class="dashboard-sidebar-item">
                    <span class="label"><i class="fas fa-barcode"></i> Code</span>
                    <span
                        class="val"><?= !empty($customer->vat_no) ? html_escape($customer->vat_no) : (isset($customer_code) ? html_escape($customer_code) : '—') ?></span>
                </div>
            </div>

            <!-- Card 3: Your Team -->
            <div class="sidebar-card sidebar-card-your-team">
                <h2 class="dashboard-sidebar-title"><i class="fas fa-users"></i> Your Team</h2>
                <div class="sidebar-associates">
                    <div class="dashboard-sidebar-item">
                        <span class="label"><i class="fas fa-user-tie"></i> Sales Associate</span>
                        <span
                            class="val"><?= !empty($customer_sales_associate_name) ? html_escape($customer_sales_associate_name) : '—' ?></span>
                    </div>
                    <div class="dashboard-sidebar-item">
                        <span class="label"><i class="fas fa-user-cog"></i> Technical Associate</span>
                        <span
                            class="val"><?= !empty($customer_tech_associate_name) ? html_escape($customer_tech_associate_name) : '—' ?></span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Contact us -->
            <div class="sidebar-card sidebar-card-contact">
                <h2 class="dashboard-sidebar-title"><i class="fas fa-envelope"></i> Contact us</h2>
                <p class="sidebar-contact-line"><span class="sidebar-contact-icon" aria-hidden="true"><i
                            class="fas fa-globe"></i></span> www.geekofstates.com</p>
                <p class="sidebar-contact-line"><span class="sidebar-contact-icon" aria-hidden="true"><i
                            class="fas fa-envelope"></i></span> support@geekofstates.com</p>
            </div>
        </aside>

        <div class="dashboard">
            <header class="header">
                <div class="header-decoration" aria-hidden="true"><i class="fas fa-star"></i></div>
                <div class="header-orb header-orb-1"></div>
                <div class="header-orb header-orb-2"></div>
                <div class="welcome-greeting">
                    <i class="fas fa-hand-sparkles welcome-icon"></i>
                    <span>Welcome</span>
                </div>
                <h1><span class="user-name"><?= html_escape($customer_name) ?></span></h1>
                <?php if (!empty($support_expiry_date_formatted)): ?>
                <p class="header-meta">
                    <span class="header-meta-item" title="Based on last sale date + support duration (days)"><span
                            class="header-meta-label">Expiry date:</span> <span
                            class="header-meta-value header-expiry-date-value"><?= html_escape($support_expiry_date_formatted) ?></span></span>
                </p>
                <?php endif; ?>
            </header>

            <!-- Fixed service gauges -->
            <div class="licenses-grid">
                <?php foreach ($dashboard_services as $svc):
                    $gauge_id = 'gauge-' . preg_replace('/[^a-z0-9]/', '', strtolower($svc->service_name));
                    ?>
                <div class="license-card">
                    <?php if ($svc->status_class === 'no-expiry'): ?>
                    <div class="license-gauge-top">
                        <div class="progress-wrap">
                            <div class="progress-label-row">
                                <span class="label"><?= html_escape($svc->service_name) ?> status</span>
                                <span class="pct no-expiry">Active</span>
                            </div>
                            <div class="progress-bar-outer">
                                <div class="progress-bar-inner no-expiry" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div class="license-status no-expiry">No expiry set</div>
                    </div>
                    <?php elseif ($svc->status_class === 'expired'): ?>
                    <?php $circle_radius_exp = 52; $circle_circ_exp = 2 * M_PI * $circle_radius_exp; ?>
                    <div class="license-gauge-top">
                        <div class="gauge-wrap">
                            <div class="gauge-title"><?= html_escape($svc->service_name) ?></div>
                            <div class="circle-gauge circle-gauge-expired">
                                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                                    <circle class="circle-gauge-track" cx="60" cy="60" r="<?= $circle_radius_exp ?>"></circle>
                                    <circle class="circle-gauge-fill" cx="60" cy="60" r="<?= $circle_radius_exp ?>"
                                        stroke-dasharray="<?= round($circle_circ_exp, 2) ?>"
                                        stroke-dashoffset="0"></circle>
                                </svg>
                                <span class="gauge-center-icon gauge-center-icon--expired" aria-hidden="true"><i class="fas fa-battery-empty"></i></span>
                            </div>
                            <div class="gauge-value-wrap">
                                <div class="gauge-value red"><span class="gauge-value-dot" aria-hidden="true"></span>Expired</div>
                            </div>
                        </div>
                    </div>
                    <?php else:
                    $passed_days = max(0, (int)$svc->support_duration - (int)$svc->remaining_days);
                    $display_pct = min(100, max(0, (float)$svc->percent_remaining));
                    $needle_angle = 90 - ($display_pct / 100) * 180;
                    $is_active = $display_pct > 0;
                    if ($is_active) {
                        if ($display_pct > 75) {
                            $battery_icon = 'fa-battery-full';
                        } elseif ($display_pct > 50) {
                            $battery_icon = 'fa-battery-three-quarters';
                        } elseif ($display_pct > 25) {
                            $battery_icon = 'fa-battery-half';
                        } else {
                            $battery_icon = 'fa-battery-quarter';
                        }
                    } else {
                        $battery_icon = 'fa-battery-empty';
                    }
                    ?>
                    <?php
                    $circle_radius = 52;
                    $circle_circ = 2 * M_PI * $circle_radius;
                    $fill_pct = $is_active ? $display_pct : 100;
                    ?>
                    <div class="license-gauge-top">
                        <div class="gauge-wrap">
                            <div class="gauge-title"><?= html_escape($svc->service_name) ?></div>
                            <div class="circle-gauge<?= $is_active ? '' : ' circle-gauge-inactive' ?>"
                                style="--gauge-pct: <?= (float)$fill_pct ?>; --circle-circ: <?= round($circle_circ, 2) ?>;">
                                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                                    <circle class="circle-gauge-track" cx="60" cy="60" r="<?= $circle_radius ?>">
                                    </circle>
                                    <circle class="circle-gauge-fill" cx="60" cy="60" r="<?= $circle_radius ?>"
                                        stroke-dasharray="<?= round($circle_circ, 2) ?>"
                                        stroke-dashoffset="<?= round($circle_circ - ($fill_pct / 100) * $circle_circ, 2) ?>">
                                    </circle>
                                </svg>
                                <span class="gauge-center-icon gauge-center-icon--<?= $is_active ? 'active' : 'inactive' ?>" aria-hidden="true"><i class="fas <?= $battery_icon ?>"></i></span>
                            </div>
                            <div class="gauge-value-wrap">
                                <div class="gauge-value <?= $is_active ? 'green' : 'red' ?>"><span class="gauge-value-dot" aria-hidden="true"></span><?= $is_active ? 'Active' : 'Inactive' ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cases and Book appointment -->
            <section class="section main-actions-section">
                <div class="main-actions-grid">
                    <div class="action-card cases-card main-action-block case-block">
                        <h2 class="section-head"><i class="fas fa-ticket-alt"></i> Cases</h2>
                        <div class="case-form-wrap">
                            <p class="case-form-hint">Case ID is auto-generated on submit (prefix + your code + date).
                            </p>
                            <form id="case-form" class="case-form" action="" method="post">
                                <input type="hidden" name="customer_code"
                                    value="<?= html_escape($customer_code ?? '') ?>">
                                <div class="case-form-group">
                                    <label for="case-details" class="case-form-label">Details <span
                                            class="required">*</span></label>
                                    <textarea id="case-details" name="details" class="case-form-textarea" rows="2"
                                        placeholder="Describe your issue or request..." required></textarea>
                                </div>
                                <button type="submit" class="case-form-submit" id="case-submit-btn"><i
                                        class="fas fa-paper-plane"></i> Submit Case</button>
                                <div id="case-form-message" class="case-form-message" aria-live="polite"></div>
                            </form>
                        </div>
                    </div>
                    <div class="action-card appointments-card main-action-block appointment-block">
                        <h2 class="section-head"><i class="fas fa-calendar-plus"></i> Book appointment</h2>
                        <div class="appointment-card-body">
                            <p class="appointment-card-desc">Schedule a call or meeting with our team at a time that
                                works for you.</p>
                            <button type="button" class="appointment-btn appointment-btn-main"
                                id="open-appointment-modal">
                                <i class="fas fa-calendar-plus"></i> Book Appointment
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right column: Technical support, Service details, Contact us -->
        <aside class="dashboard-right">
            <?php $svc_base = !empty($dashboard_services) ? $dashboard_services[0] : null; ?>
            <section class="section support-cards-section">
                <div class="support-cards-column">
                    <article class="support-card support-card-technical">
                        <div class="support-card-technical-header">
                            <span class="support-card-icon support-card-icon-header" aria-hidden="true"><i
                                    class="fas fa-headset"></i></span>
                            <div class="support-card-technical-title">Need help? </div><br>
                            <div class="support-card-technical-phone">1-800-755-6599</div>
                        </div>
                        <div class="support-card-technical-body">
                            <p>Need help with your licenses or services? Our technical team is just a call away.</p>
                            <p> <strong><span class="" aria-hidden="true"><i class="fas fa-clock"></i></span>
                                    Mon–Fri 9am–6pm
                                    (local time</strong>)</p>
                        </div>
                    </article>
                    <article class="support-card support-card-service">
                        <h3 class="support-card-heading"><span class="support-card-icon" aria-hidden="true"><i
                                    class="fas fa-clipboard-list"></i></span> Service details</h3>
                        <dl class="support-details-list">
                            <div class="support-details-row">
                                <dt>Service start date</dt>
                                <dd><?= !empty($support_start_date_formatted) ? html_escape($support_start_date_formatted) : '—' ?>
                                </dd>
                            </div>
                            <div class="support-details-row">
                                <dt>Expiry date</dt>
                                <dd><?= !empty($support_expiry_date_formatted) ? html_escape($support_expiry_date_formatted) : '—' ?>
                                </dd>
                            </div>
                            <div class="support-details-row">
                                <dt>Service duration</dt>
                                <dd><?php $duration_days = ($svc_base && !empty($svc_base->support_duration)) ? (int)$svc_base->support_duration : 0; echo $duration_days > 0 ? $duration_days . ' days' : '—'; ?>
                                </dd>
                            </div>
                            <div class="support-details-row">
                                <dt>Status</dt>
                                <dd>
                                    <?php
                                    $status_label = '—';
                                    $status_class = 'inactive';
                                    if ($svc_base) {
                                        switch ($svc_base->status_class) {
                                            case 'expired':
                                                $status_label = 'Expired';
                                                $status_class = 'expired';
                                                break;
                                            case 'no-expiry':
                                                $status_label = 'Active (no expiry)';
                                                $status_class = 'active';
                                                break;
                                            default:
                                                $status_label = 'Active';
                                                $status_class = 'active';
                                                break;
                                        }
                                    }
                                    ?>
                                    <span
                                        class="support-status-badge support-status-badge--<?= html_escape($status_class) ?>">
                                        <span class="support-status-dot" aria-hidden="true"></span>
                                        <span class="support-status-text"><?= html_escape($status_label) ?></span>
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </article>
                    <article class="support-card support-card-stats">
                        <h3 class="support-card-heading"><span class="support-card-icon" aria-hidden="true"><i
                                    class="fas fa-chart-line"></i></span> Support this month</h3>
                        <div class="support-stats-list">
                            <div class="support-stats-row">
                                <span class="support-stats-label"><i class="fas fa-ticket-alt"></i> Open cases</span>
                                <span class="support-stats-badge"><?= (int)($support_open_cases_count ?? 0) ?></span>
                            </div>
                            <div class="support-stats-row">
                                <span class="support-stats-label"><i class="fas fa-calendar-check"></i> Appointments</span>
                                <span class="support-stats-badge"><?= (int)($support_appointments_this_month ?? 0) ?></span>
                            </div>
                        </div>
                    </article>
                    <article class="support-card support-card-activity">
                        <h3 class="support-card-heading"><span class="support-card-icon" aria-hidden="true"><i class="fas fa-bolt"></i></span> Activity</h3>
                        <div class="support-activity-list">
                            <div class="support-activity-row support-activity-row--success">
                                <span class="support-activity-dot" aria-hidden="true"></span>
                                <span class="support-activity-text">Firewall enabled</span>
                            </div>
                            <div class="support-activity-row support-activity-row--success">
                                <span class="support-activity-dot" aria-hidden="true"></span>
                                <span class="support-activity-text">Scan completed • No threats</span>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
        </aside>
    </div>

    <footer class="page-footer">
        <span class="page-footer-site"><?= html_escape($site_name) ?></span><span class="page-footer-sep"> -
        </span><span class="page-footer-tagline"></span>
        <span class="page-footer-copyright"><strong>Copyright &copy; 2026</strong></span>
    </footer>

    <script>
    (function() {
        var form = document.getElementById('case-form');
        if (!form) return;
        var submitUrl =
            <?= json_encode(site_url('customer_dashboard/submit_case/'.(isset($customer_code) ? $customer_code : ''))) ?>;
        var csrfName = <?= json_encode(isset($csrf_token_name) ? $csrf_token_name : '') ?>;
        var csrfHash = <?= json_encode(isset($csrf_hash) ? $csrf_hash : '') ?>;
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
                var res = null;
                try {
                    res = JSON.parse(xhr.responseText || '{}');
                } catch (e) {
                    if (xhr.status === 200 && xhr.responseText) {
                        var start = xhr.responseText.indexOf('{');
                        var end = xhr.responseText.lastIndexOf('}') + 1;
                        if (start !== -1 && end > start) {
                            try {
                                res = JSON.parse(xhr.responseText.substring(start, end));
                            } catch (e2) {}
                        }
                    }
                    if (!res) {
                        setMessage('Request failed. Your case may have been created—please refresh the page to check.', true);
                        return;
                    }
                }
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
                <button type="button" class="modal-close" id="close-appointment-modal"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <div class="appointment-form-group">
                        <label for="appointment-type" class="appointment-form-label">Type <span
                                class="required">*</span></label>
                        <select id="appointment-type" name="appointment_type" class="appointment-form-select" required>
                            <option value="">Select appointment type...</option>
                            <?php foreach ($appointment_types as $key => $label): ?>
                            <option value="<?= html_escape($key) ?>"><?= html_escape($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-subject" class="appointment-form-label">Subject <span
                                class="required">*</span></label>
                        <input type="text" id="appointment-subject" name="subject" class="appointment-form-input"
                            placeholder="Brief description..." required>
                    </div>
                    <div class="appointment-form-row">
                        <div class="appointment-form-group">
                            <label for="appointment-date" class="appointment-form-label">Preferred Date <span
                                    class="required">*</span></label>
                            <input type="date" id="appointment-date" name="preferred_date"
                                class="appointment-form-input" required>
                        </div>
                        <div class="appointment-form-group">
                            <label for="appointment-time" class="appointment-form-label">Preferred Time <span
                                    class="required">*</span></label>
                            <input type="time" id="appointment-time" name="preferred_time"
                                class="appointment-form-input" required>
                        </div>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-duration" class="appointment-form-label">Duration <span
                                class="required">*</span></label>
                        <select id="appointment-duration" name="duration_minutes" class="appointment-form-select"
                            required>
                            <?php foreach ($duration_options as $minutes => $label): ?>
                            <option value="<?= (int)$minutes ?>" <?= $minutes == 30 ? 'selected' : '' ?>>
                                <?= html_escape($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="appointment-form-group">
                        <label for="appointment-description" class="appointment-form-label">Description
                            (optional)</label>
                        <textarea id="appointment-description" name="description" class="appointment-form-textarea"
                            rows="4" placeholder="Additional details about your appointment..."></textarea>
                    </div>
                    <div class="appointment-form-submit-wrap">
                        <button type="submit" class="appointment-form-submit" id="appointment-submit-btn">
                            <i class="fas fa-paper-plane"></i> Request Appointment
                        </button>
                        <button type="button" class="appointment-form-cancel"
                            id="appointment-cancel-btn">Cancel</button>
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
        var submitUrl =
            <?= json_encode(site_url('customer_dashboard/submit_appointment/'.(isset($customer_code) ? $customer_code : ''))) ?>;
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
            // Upcoming Appointments section removed – no DOM update
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
                        var msg = 'Appointment ' + (res.appointment_code || '') +
                            ' requested successfully!';
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