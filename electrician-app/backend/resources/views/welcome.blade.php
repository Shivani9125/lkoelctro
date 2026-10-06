<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lucknow ElectroFix — Find a Trusted Electrician Near You</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        :root {
            --primary: #0f172a;
            --primary-light: #1e293b;
            --accent: #2563eb;
            --accent-hover: #1d4ed8;
            --accent-light: #eff6ff;
            --electric-yellow: #f59e0b;
            --electric-glow: rgba(245, 158, 11, 0.25);
            --success: #10b981;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.04);
            --shadow-lg: 0 12px 24px -4px rgba(15,23,42,0.08), 0 4px 8px -2px rgba(15,23,42,0.04);
            --shadow-glow: 0 0 25px rgba(37,99,235,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px var(--electric-glow);
        }

        .brand-icon svg {
            width: 22px;
            height: 22px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 15px;
            font-weight: 500;
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .emergency-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background-color: #fef2f2;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-full);
            border: 1px solid #fecaca;
        }

        .emergency-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.3); }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--accent);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        /* Hero Section */
        .hero {
            padding: 72px 0 56px;
            position: relative;
            background: radial-gradient(circle at 45% 10%, rgba(37, 99, 235, 0.09) 0%, transparent 65%),
                        radial-gradient(circle at 80% 40%, rgba(245, 158, 11, 0.06) 0%, transparent 55%);
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.14fr 0.86fr;
            gap: 48px;
            align-items: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.12);
        }

        .pulse-spark {
            font-size: 14px;
            animation: spark-pulse 1.8s infinite;
        }

        @keyframes spark-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
        }

        .hero-title {
            font-size: 48px;
            line-height: 1.15;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1.2px;
            margin-bottom: 18px;
        }

        .hero-title .gradient-accent {
            background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 17px;
            color: var(--text-muted);
            margin-bottom: 28px;
            max-width: 540px;
            line-height: 1.6;
        }

        /* Modern Elevated Search Card in Hero */
        .search-box-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 20px;
            padding: 22px 24px 20px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(241, 245, 249, 0.9);
            margin-bottom: 24px;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .search-box-card:focus-within {
            box-shadow: 0 24px 50px -12px rgba(37, 99, 235, 0.12), 0 0 0 1px rgba(37, 99, 235, 0.25);
            border-color: rgba(191, 219, 254, 0.9);
        }

        .search-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .search-live-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.2px;
        }

        .status-live-beacon {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            position: relative;
        }

        .status-live-beacon::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background-color: rgba(16, 185, 129, 0.4);
            animation: ping 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        @keyframes ping {
            75%, 100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .search-eta-pill {
            font-size: 11px;
            font-weight: 700;
            color: #b45309;
            background: #fffbeb;
            padding: 3px 10px;
            border-radius: 9999px;
            border: 1px solid #fef3c7;
        }

        .search-form-row {
            display: grid;
            grid-template-columns: 1.15fr 1fr auto;
            gap: 14px;
            align-items: flex-end;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-group label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 14px;
            transition: all 0.2s ease;
        }

        .input-wrapper:focus-within {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-icon {
            color: #64748b;
            flex-shrink: 0;
            width: 18px;
            height: 18px;
            transition: color 0.2s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: #2563eb;
        }

        .input-wrapper input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            font-family: inherit;
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .input-wrapper select {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            font-family: inherit;
            cursor: pointer;
        }

        .btn-hero-search {
            height: 46px;
            padding: 0 24px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .btn-hero-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.45);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .btn-hero-search svg {
            transition: transform 0.2s ease;
        }

        .btn-hero-search:hover svg {
            transform: translateX(3px);
        }

        /* Quick Areas Bar Inside Search Card */
        .hero-quick-areas {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #e2e8f0;
            flex-wrap: wrap;
        }

        .quick-areas-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .quick-areas-pills {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .hero-quick-pill {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            padding: 4px 11px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .hero-quick-pill:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }

        .hero-quick-pill.active {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        .hero-trust-badges {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-top: 8px;
            flex-wrap: wrap;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .trust-item svg {
            color: var(--success);
            width: 17px;
            height: 17px;
        }

        /* Hero Interactive Slider Visual Showcase */
        .hero-visual {
            position: relative;
        }

        .hero-ambient-glow {
            position: absolute;
            top: -40px;
            right: -40px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(245, 158, 11, 0.1) 50%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
            animation: ambient-drift 8s ease-in-out infinite alternate;
        }

        @keyframes ambient-drift {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-20px, 20px) scale(1.1); }
        }

        .hero-slider-card {
            background: linear-gradient(150deg, #0b1329 0%, #111e38 50%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 26px 24px 20px;
            color: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 40px rgba(37, 99, 235, 0.15);
            position: relative;
            z-index: 1;
            overflow: hidden;
            min-height: 485px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .slider-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            z-index: 2;
        }

        .slider-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #93c5fd;
            backdrop-filter: blur(8px);
        }

        .beacon-dot {
            width: 7px;
            height: 7px;
            background: #3b82f6;
            border-radius: 50%;
            box-shadow: 0 0 8px #60a5fa;
            animation: beacon-pulse 1.6s infinite;
        }

        @keyframes beacon-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.5; }
        }

        .slider-nav-arrows {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .slider-nav-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .slider-nav-btn:hover {
            background: rgba(37, 99, 235, 0.6);
            border-color: #60a5fa;
            transform: scale(1.08);
        }

        .slider-slides-container {
            position: relative;
            flex: 1;
            display: flex;
            align-items: stretch;
        }

        .hero-slide {
            display: none;
            width: 100%;
            animation: slide-fade-in 0.4s ease-out forwards;
        }

        .hero-slide.active {
            display: block;
        }

        @keyframes slide-fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .visual-header {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.4px;
            margin-bottom: 6px;
            color: #ffffff;
        }

        .visual-sub {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        /* Slide 1: Animated Radar */
        .radar-scope-wrapper {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 12px 10px;
            margin-bottom: 14px;
            display: flex;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .radar-scope {
            width: 220px;
            height: 135px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radar-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px dashed rgba(59, 130, 246, 0.35);
            pointer-events: none;
        }

        .radar-ring.ring-1 { width: 56px; height: 56px; border-style: solid; border-color: rgba(59, 130, 246, 0.4); }
        .radar-ring.ring-2 { width: 115px; height: 115px; }
        .radar-ring.ring-3 { width: 180px; height: 180px; border-color: rgba(59, 130, 246, 0.2); }

        .radar-sweep-beam {
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient(from 0deg at 50% 50%, rgba(37, 99, 235, 0.35) 0deg, transparent 60deg, transparent 360deg);
            animation: radar-sweep 4s linear infinite;
            pointer-events: none;
        }

        @keyframes radar-sweep {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .radar-home-beacon {
            position: absolute;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .radar-home-beacon .home-icon {
            font-size: 18px;
            filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.6));
        }

        .pulse-wave {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 32px;
            height: 32px;
            margin-top: -16px;
            margin-left: -16px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.5);
            animation: beacon-wave 2s infinite;
            pointer-events: none;
        }

        @keyframes beacon-wave {
            0% { transform: scale(0.5); opacity: 1; }
            100% { transform: scale(2.4); opacity: 0; }
        }

        .home-tag {
            font-size: 9px;
            font-weight: 800;
            color: #93c5fd;
            background: rgba(15, 23, 42, 0.85);
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid rgba(59, 130, 246, 0.4);
            white-space: nowrap;
            margin-top: 2px;
        }

        .radar-pro-blip {
            position: absolute;
            z-index: 6;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .radar-pro-blip:hover {
            transform: scale(1.2);
        }

        .radar-pro-blip.blip-1 { top: 10px; left: 34px; }
        .radar-pro-blip.blip-2 { bottom: 14px; right: 38px; }
        .radar-pro-blip.blip-3 { top: 18px; right: 28px; }

        .blip-core {
            font-size: 13px;
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 50%;
            padding: 2px;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.8);
        }

        .blip-ping {
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.4);
            animation: blip-pulse 2s infinite;
        }

        @keyframes blip-pulse {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2.6); opacity: 0; }
        }

        .blip-label {
            font-size: 9px;
            font-weight: 700;
            color: #cbd5e1;
            background: rgba(15, 23, 42, 0.9);
            padding: 1px 5px;
            border-radius: 4px;
            white-space: nowrap;
            margin-top: 1px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .radar-live-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 9px 13px;
            margin-bottom: 14px;
            font-size: 12px;
            color: #cbd5e1;
        }

        .radar-live-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dot-online {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
        }

        /* Slide 2: Pro Spotlight Card */
        .pro-spotlight-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            backdrop-filter: blur(8px);
        }

        .pro-spotlight-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .pro-avatar-badge {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            color: #ffffff;
            position: relative;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            flex-shrink: 0;
        }

        .pro-check-dot {
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 15px;
            height: 15px;
            background: #10b981;
            border: 2px solid #0f172a;
            border-radius: 50%;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #ffffff;
        }

        .pro-info-col {
            flex: 1;
        }

        .pro-name-line {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2px;
        }

        .pro-name-line h4 {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .pro-verified-tag {
            font-size: 10px;
            font-weight: 700;
            color: #10b981;
            background: rgba(16, 185, 129, 0.15);
            padding: 2px 7px;
            border-radius: 9999px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .pro-rating-line {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
        }

        .pro-stars { color: #f59e0b; }
        .pro-score { font-weight: 800; color: #ffffff; }
        .pro-count { color: #94a3b8; font-size: 10px; }

        .pro-loc-line {
            font-size: 11px;
            color: #cbd5e1;
            margin-top: 2px;
        }

        .pro-tags-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .pro-tag {
            font-size: 10px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            padding: 2px 7px;
            border-radius: 6px;
        }

        .pro-status-dispatch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .pro-dispatch-avail {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #94a3b8;
        }

        .pulse-active-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
        }

        .btn-book-spotlight {
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-book-spotlight:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .mini-tech-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 11px;
            color: #cbd5e1;
        }

        .mini-tech-avatar {
            width: 22px;
            height: 22px;
            background: #334155;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 10px;
            color: #ffffff;
        }

        .mini-tech-status {
            color: #10b981;
            font-weight: 700;
            margin-left: auto;
        }

        /* Slide 3: Guarantee Grid */
        .guarantee-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .guarantee-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 11px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: background 0.2s ease;
        }

        .guarantee-card:hover {
            background: rgba(255, 255, 255, 0.09);
        }

        .guarantee-icon {
            font-size: 18px;
            line-height: 1;
            flex-shrink: 0;
        }

        .guarantee-text h5 {
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .guarantee-text p {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.35;
            margin: 0;
        }

        .guarantee-subtext {
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
            text-align: center;
            padding: 7px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Shared Metrics */
        .quick-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .metric-box h4 {
            font-size: 19px;
            font-weight: 800;
            color: #f59e0b;
            margin-bottom: 2px;
        }

        .metric-box p {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Slider Progress Bar */
        .slider-progress-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .progress-segment {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 6px 8px;
            cursor: pointer;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .progress-segment:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .prog-title {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            position: relative;
            z-index: 2;
            transition: color 0.2s ease;
        }

        .progress-segment.active .prog-title {
            color: #ffffff;
        }

        .prog-fill {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0%;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.6), rgba(59, 130, 246, 0.8));
            border-radius: 7px;
            transition: width 0.3s ease;
            z-index: 1;
        }

        .progress-segment.active .prog-fill {
            width: 100%;
        }

        /* Floating Live Notification Ticker */
        .floating-notification-ticker {
            position: absolute;
            bottom: -22px;
            left: -18px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12), 0 4px 10px rgba(15, 23, 42, 0.06);
            color: var(--text-dark);
            z-index: 10;
            animation: ticker-float 4s ease-in-out infinite;
            max-width: 380px;
        }

        @keyframes ticker-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .ticker-avatar-circle {
            width: 34px;
            height: 34px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ticker-content {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .ticker-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .ticker-sub {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Section Headings */
        .section-header {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 48px;
        }

        .section-tag {
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 34px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--text-muted);
        }

        /* Interactive Map Section */
        .map-section {
            padding: 60px 0;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .map-search-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            margin-bottom: 24px;
            gap: 16px;
        }

        .map-search-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .map-search-left input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 15px;
            font-family: inherit;
            width: 100%;
            color: var(--text-dark);
        }

        .location-status {
            font-size: 13px;
            font-weight: 600;
            color: var(--success);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .area-switcher-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 0 14px;
            margin-bottom: 8px;
        }

        .area-pill {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        .area-pill:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #f8fafc;
        }

        .area-pill.active {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 2px 6px rgba(15,23,42,0.25);
        }

        .btn-detect-loc {
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s ease;
        }

        .btn-detect-loc:hover {
            background: #1d4ed8;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Custom 'You Live Here' Home Marker */
        .custom-user-marker-icon {
            background: transparent !important;
            border: none !important;
        }

        .user-home-marker {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            cursor: grab;
            user-select: none;
        }

        .user-home-marker:active {
            cursor: grabbing;
        }

        .home-pulse-ring {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.45);
            animation: ring-pulse 1.8s infinite;
            pointer-events: none;
        }

        .home-pin-bubble {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45);
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        .home-pin-pointer {
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid #2563eb;
            margin-top: -1px;
        }

        @keyframes ring-pulse {
            0% { transform: translateX(-50%) scale(0.6); opacity: 1; }
            100% { transform: translateX(-50%) scale(2.4); opacity: 0; }
        }

        /* Electrician Marker Pin */
        .custom-elec-marker-icon {
            background: transparent !important;
            border: none !important;
        }

        .elec-map-pin {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .elec-map-pin:hover {
            transform: scale(1.2);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.65);
        }

        .map-layout {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .map-wrapper {
            position: relative;
            height: 480px;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
        }

        #electricianMap {
            width: 100%;
            height: 100%;
        }

        .map-legend {
            position: absolute;
            bottom: 16px;
            left: 16px;
            z-index: 500;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            gap: 14px;
            box-shadow: var(--shadow-sm);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Electrician Cards List */
        .electricians-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-height: 480px;
            overflow-y: auto;
            padding-right: 4px;
        }

        /* Custom Scrollbar */
        .electricians-list::-webkit-scrollbar {
            width: 6px;
        }
        .electricians-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .electricians-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .electrician-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .electrician-card:hover, .electrician-card.active {
            border-color: var(--accent);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.12);
            transform: translateY(-2px);
        }

        .electrician-card.active {
            background-color: #fafcff;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .electrician-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .electrician-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            font-size: 15px;
            border: 2px solid #ffffff;
            box-shadow: var(--shadow-sm);
        }

        .electrician-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary);
        }

        .electrician-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #d97706;
            font-weight: 600;
        }

        .distance-badge {
            background: var(--accent-light);
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .specialty-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 10px 0 12px;
        }

        .specialty-tag {
            font-size: 11px;
            background: #f1f5f9;
            color: var(--text-muted);
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px dashed var(--border-color);
            font-size: 13px;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .status-indicator.available {
            color: var(--success);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }

        .btn-select-electrician {
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            background: var(--primary);
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-select-electrician:hover {
            background: var(--accent);
        }

        /* Services Section */
        .services-section {
            padding: 80px 0;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .service-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 28px 24px;
            transition: all 0.25s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }

        .service-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-sm);
            background: var(--accent-light);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }

        .service-card:hover .service-icon {
            background: var(--accent);
            color: #ffffff;
            transform: scale(1.05);
        }

        .service-icon svg {
            width: 26px;
            height: 26px;
        }

        .service-title {
            font-size: 19px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .service-desc {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .service-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .service-price {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .service-price span {
            font-size: 15px;
            color: var(--accent);
            font-weight: 700;
        }

        .service-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        /* How It Works Section */
        .how-section {
            padding: 80px 0;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            position: relative;
        }

        .step-card {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 28px 20px;
            text-align: center;
            position: relative;
            transition: all 0.2s ease;
        }

        .step-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--accent);
        }

        .step-number {
            width: 44px;
            height: 44px;
            margin: 0 auto 18px;
            background: var(--accent);
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .step-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .step-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Emergency CTA Banner */
        .emergency-banner {
            padding: 48px 0;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #ffffff;
            position: relative;
        }

        .emergency-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }

        .emergency-text h3 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .emergency-text p {
            color: #94a3b8;
            font-size: 15px;
        }

        .emergency-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-emergency-call {
            background: #ef4444;
            color: #ffffff;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 16px;
            border-radius: var(--radius-sm);
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
        }

        .btn-emergency-call:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* Footer */
        .footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 60px 0 24px;
            border-top: 1px solid #1e293b;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .footer-brand .brand-logo {
            color: #ffffff;
        }

        .footer-brand p {
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-col h5 {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-col ul a {
            font-size: 14px;
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .footer-col ul a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
        }

        /* Custom Leaflet Marker Styling */
        .custom-div-icon {
            background: transparent;
            border: none;
        }

        .marker-pin-user {
            width: 32px;
            height: 32px;
            border-radius: 50% 50% 50% 0;
            background: #2563eb;
            position: absolute;
            transform: rotate(-45deg);
            left: 50%;
            top: 50%;
            margin: -20px 0 0 -20px;
            animation: bounce 2s infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
        }

        .marker-pin-user::after {
            content: '';
            width: 14px;
            height: 14px;
            margin: 8px 0 0 8px;
            background: #ffffff;
            position: absolute;
            border-radius: 50%;
        }

        .marker-pin-electrician {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f59e0b;
            border: 2px solid #ffffff;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.5);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .marker-pin-electrician:hover {
            transform: scale(1.18);
        }

        /* Responsive Breakpoints & Mobile Optimization */
        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 36px;
            }
            .map-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .steps-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .map-search-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .location-status {
                flex-wrap: wrap;
                justify-content: space-between;
                white-space: normal;
            }
        }

        @media (max-width: 640px) {
            body {
                padding-bottom: 74px; /* Room for sticky mobile bottom action bar */
                overflow-x: hidden;
            }
            .container {
                padding: 0 16px;
            }
            .navbar {
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            }
            .navbar-content {
                height: 58px;
            }
            .brand-logo {
                font-size: 18px;
                gap: 8px;
            }
            .brand-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }
            .brand-icon svg {
                width: 18px;
                height: 18px;
            }
            .nav-links {
                display: none;
            }
            .emergency-badge {
                display: none; /* Hide on small mobile to give room to call/find button */
            }
            .nav-actions .btn {
                padding: 6px 12px;
                font-size: 12px;
                border-radius: 6px;
            }
            .hero {
                padding: 24px 0 20px;
            }
            .hero-tag {
                font-size: 11px;
                padding: 5px 10px;
                margin-bottom: 12px;
                display: inline-flex;
            }
            .hero-title {
                font-size: 26px;
                line-height: 1.22;
                margin-bottom: 10px;
            }
            .hero-subtitle {
                font-size: 14px;
                line-height: 1.5;
                margin-bottom: 18px;
            }
            .trust-badges {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                margin-top: 14px;
            }
            .trust-item {
                font-size: 12px;
            }
            .search-box-card {
                padding: 16px;
                border-radius: 16px;
            }
            .search-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
            .search-form-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .btn-hero-search {
                width: 100%;
                height: 48px;
                font-size: 15px;
            }
            .hero-quick-areas {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .hero-slider-card {
                padding: 20px 16px;
                border-radius: 18px;
                min-height: auto;
            }
            .guarantee-grid {
                grid-template-columns: 1fr;
            }
            .floating-notification-ticker {
                position: static;
                margin-top: 14px;
                width: 100%;
                box-sizing: border-box;
                animation: none;
                max-width: 100%;
            }
            .map-section {
                padding: 36px 0;
            }
            .section-header {
                margin-bottom: 20px;
            }
            .section-title {
                font-size: 22px;
                line-height: 1.25;
            }
            .section-subtitle {
                font-size: 13px;
                line-height: 1.5;
            }
            .map-search-bar {
                padding: 12px;
                border-radius: 12px;
                margin-bottom: 14px;
            }
            .map-search-left input {
                font-size: 14px;
            }
            .location-status {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
            }
            .location-status button {
                width: 100%;
                justify-content: center;
                padding: 10px;
                font-size: 13px;
                border-radius: 8px;
            }
            .area-switcher-bar {
                padding: 4px 0 10px;
                margin-bottom: 12px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .area-switcher-bar::-webkit-scrollbar {
                display: none;
            }
            .area-pill {
                padding: 6px 12px;
                font-size: 12px;
            }
            .map-wrapper {
                height: 310px;
                border-radius: 12px;
            }
            .map-legend {
                bottom: 8px;
                left: 8px;
                right: 8px;
                padding: 6px 8px;
                font-size: 10px;
                gap: 6px;
                flex-wrap: wrap;
                border-radius: 6px;
            }
            .electricians-list {
                max-height: 480px;
                gap: 12px;
            }
            .electrician-card {
                padding: 14px;
                border-radius: 12px;
            }
            .card-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .distance-badge {
                align-self: flex-start;
                font-size: 11px;
                padding: 4px 8px;
            }
            .card-bottom {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .btn-select-electrician {
                width: 100%;
                min-height: 42px;
                justify-content: center;
                text-align: center;
                font-size: 13px;
                padding: 10px 14px;
                border-radius: 8px;
            }
            .services-section, .steps-section {
                padding: 40px 0;
            }
            .services-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            .service-card {
                padding: 18px;
            }
            .steps-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .step-card {
                padding: 20px 16px;
            }
            .emergency-section {
                padding: 36px 0;
            }
            .emergency-content {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            .emergency-text h3 {
                font-size: 22px;
            }
            .emergency-text p {
                font-size: 14px;
            }
            .emergency-actions {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .btn-emergency-call {
                width: 100%;
                justify-content: center;
                min-height: 48px;
                font-size: 15px;
            }
            .emergency-actions .btn-outline {
                width: 100%;
                justify-content: center;
                min-height: 44px;
            }
            .footer {
                padding: 40px 0 24px;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        /* Small Phones (iPhone SE, Galaxy A, 320px - 380px) */
        @media (max-width: 380px) {
            .container {
                padding: 0 12px;
            }
            .hero-title {
                font-size: 23px;
            }
            .brand-logo {
                font-size: 17px;
            }
            .brand-icon {
                width: 28px;
                height: 28px;
            }
            .map-wrapper {
                height: 280px;
            }
            .btn-search {
                font-size: 14px;
            }
        }

        /* Mobile Sticky Quick Action Bar */
        .mobile-bottom-bar {
            display: none;
        }

        @media (max-width: 640px) {
            .mobile-bottom-bar {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                background: rgba(15, 23, 42, 0.96);
                backdrop-filter: blur(12px);
                padding: 10px 14px;
                gap: 10px;
                border-top: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.25);
            }
            .mobile-bar-btn {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 11px 12px;
                font-size: 13px;
                font-weight: 700;
                border-radius: 8px;
                text-decoration: none;
                border: none;
                cursor: pointer;
                transition: transform 0.15s ease;
            }
            .mobile-bar-btn:active {
                transform: scale(0.97);
            }
            .mobile-bar-btn.btn-detect {
                background: #2563eb;
                color: #ffffff;
                box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
            }
            .mobile-bar-btn.btn-call {
                background: #ef4444;
                color: #ffffff;
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            }
        }

        /* =====================================================================
           Bijli Guru (बिजली गुरु) AI Assistant - Warm, Bright & Welcoming UI
           ===================================================================== */
        .btn-ai-agent-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            color: #c2410c;
            border: 1.5px solid #fdba74;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(249, 115, 22, 0.15);
            transition: all 0.25s ease;
            font-family: inherit;
        }

        .btn-ai-agent-trigger:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            border-color: #ea580c;
            box-shadow: 0 4px 16px rgba(234, 88, 12, 0.3);
            color: #ffffff;
        }

        .navbar-guru-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1.5px solid #f59e0b;
            object-fit: cover;
            display: inline-block;
        }

        /* Floating Bijli Guru Launcher Pill */
        .electrofix-robot-widget {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .robot-launcher-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #fff7ed 100%);
            color: #1e293b;
            border: 2px solid #fdba74;
            border-radius: 9999px;
            padding: 8px 18px 8px 8px;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(234, 88, 12, 0.25), 0 0 20px rgba(245, 158, 11, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .robot-launcher-btn:hover {
            transform: translateY(-3px) scale(1.03);
            border-color: #ea580c;
            box-shadow: 0 14px 30px -5px rgba(234, 88, 12, 0.35), 0 0 25px rgba(245, 158, 11, 0.3);
        }

        .launcher-guru-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid #f59e0b;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.4);
            overflow: visible;
        }

        .launcher-guru-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .launcher-halo-badge {
            position: absolute;
            top: -6px;
            right: -4px;
            font-size: 13px;
            filter: drop-shadow(0 0 4px #f59e0b);
            animation: halo-float 2s infinite ease-in-out;
        }

        @keyframes halo-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .launcher-text-col {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .launcher-badge {
            font-size: 9.5px;
            font-weight: 800;
            color: #ea580c;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .launcher-title {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.2px;
        }

        .launcher-live-pulse {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
            position: relative;
        }

        .launcher-live-pulse::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.4);
            animation: ping 2s infinite;
        }

        /* Bijli Guru Console Card Window */
        .robot-console-card {
            position: absolute;
            bottom: 60px;
            right: 0;
            width: 420px;
            max-width: calc(100vw - 32px);
            background: #ffffff;
            border: 2px solid #fed7aa;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(154, 52, 18, 0.16), 0 0 35px rgba(245, 158, 11, 0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px) scale(0.96);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(16px);
        }

        .robot-console-card.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }

        /* Header Bar - Warm & Welcoming */
        .robot-console-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-bottom: 1.5px solid #fed7aa;
        }

        .console-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .console-bot-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid #f59e0b;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
        }

        .console-title {
            font-size: 14.5px;
            font-weight: 800;
            color: #9a3412;
            margin: 0;
            line-height: 1.2;
        }

        .console-subtitle {
            font-size: 11px;
            color: #c2410c;
            font-weight: 600;
        }

        .console-actions-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .console-btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .console-btn-icon:hover {
            background: #ffedd5;
            color: #7c2d12;
            border-color: #f97316;
        }

        /* Sadhu Mahatma Avatar Chamber */
        .robot-chamber {
            padding: 16px 18px 12px;
            background: radial-gradient(circle at 50% 25%, #fff1e6 0%, #fffbf5 60%, #ffffff 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid #ffedd5;
            position: relative;
            transition: background 0.3s ease;
        }

        .robot-chamber.emergency-mode {
            background: radial-gradient(circle at 50% 25%, #fee2e2 0%, #fff1f2 60%, #ffffff 100%);
            animation: emergency-alarm-flash 1.5s infinite;
        }

        .guru-avatar-wrapper {
            position: relative;
            margin-bottom: 10px;
            animation: guru-float 4s ease-in-out infinite;
        }

        @keyframes guru-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .guru-portrait-ring {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #f59e0b, #ea580c, #fbbf24);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.4), 0 4px 10px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .robot-chamber.emergency-mode .guru-portrait-ring {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.6);
        }

        .guru-halo-aura {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.35) 0%, transparent 70%);
            animation: halo-radiate 3s infinite alternate;
            pointer-events: none;
        }

        .robot-chamber.speaking-mode .guru-halo-aura {
            animation: halo-radiate-speak 1s infinite alternate;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.6) 0%, transparent 75%);
        }

        .robot-chamber.listening-mode .guru-halo-aura {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.5) 0%, transparent 70%);
        }

        .robot-chamber.emergency-mode .guru-halo-aura {
            background: radial-gradient(circle, rgba(239, 68, 68, 0.5) 0%, transparent 70%);
        }

        @keyframes halo-radiate {
            0% { transform: scale(0.95); opacity: 0.6; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        @keyframes halo-radiate-speak {
            0% { transform: scale(1); opacity: 0.7; }
            100% { transform: scale(1.25); opacity: 1; }
        }

        .guru-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #ffffff;
            border: 2px solid #ffffff;
            display: block;
        }

        .guru-aura-badge {
            position: absolute;
            bottom: -4px;
            right: -2px;
            background: #ffffff;
            border: 1.5px solid #f59e0b;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* State Badge */
        .robot-state-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            color: #9a3412;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .state-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #f59e0b;
            box-shadow: 0 0 6px #f59e0b;
        }

        /* Soundwave Equalizer visualizer */
        .robot-audio-visualizer {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 3px;
            height: 14px;
            width: 140px;
        }

        .eq-bar {
            width: 4px;
            height: 3px;
            background: #fdba74;
            border-radius: 2px;
            transition: height 0.2s ease, background-color 0.2s ease;
        }

        .robot-chamber.speaking-mode .eq-bar,
        .robot-chamber.listening-mode .eq-bar {
            background: #ea580c;
            animation: visualizer-eq 0.5s ease-in-out infinite alternate;
        }
        .robot-chamber.listening-mode .eq-bar { background: #10b981; }
        .robot-chamber.emergency-mode .eq-bar { background: #ef4444; }

        .eq-bar:nth-child(2) { animation-delay: 0.1s; }
        .eq-bar:nth-child(3) { animation-delay: 0.25s; }
        .eq-bar:nth-child(4) { animation-delay: 0.15s; }
        .eq-bar:nth-child(5) { animation-delay: 0.35s; }
        .eq-bar:nth-child(6) { animation-delay: 0.2s; }
        .eq-bar:nth-child(7) { animation-delay: 0.4s; }
        .eq-bar:nth-child(8) { animation-delay: 0.18s; }
        .eq-bar:nth-child(9) { animation-delay: 0.3s; }
        .eq-bar:nth-child(10) { animation-delay: 0.12s; }
        .eq-bar:nth-child(11) { animation-delay: 0.28s; }
        .eq-bar:nth-child(12) { animation-delay: 0.22s; }

        @keyframes visualizer-eq {
            0% { height: 3px; }
            100% { height: 14px; }
        }

        /* Tool Banner */
        .robot-tool-banner {
            background: #fffbeb;
            border-top: 1px solid #fde68a;
            border-bottom: 1px solid #fde68a;
            padding: 7px 14px;
            font-size: 11.5px;
            font-weight: 700;
            color: #b45309;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tool-spinner {
            animation: spark-pulse 1s infinite;
        }

        /* Messages Thread - Bright & Super Clean */
        .robot-messages-box {
            height: 250px;
            overflow-y: auto;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #fafaf9;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .agent-msg-bubble {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            max-width: 92%;
            align-self: flex-start;
        }

        .agent-msg-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            border: 1.5px solid #f59e0b;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .agent-msg-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .agent-msg-text {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            color: #1e293b;
            padding: 10px 14px;
            border-radius: 4px 16px 16px 16px;
            font-size: 13px;
            line-height: 1.5;
            word-break: break-word;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .user-msg-bubble {
            align-self: flex-end;
            max-width: 85%;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: #ffffff;
            padding: 9px 14px;
            border-radius: 16px 16px 4px 16px;
            font-size: 13px;
            font-weight: 500;
            line-height: 1.45;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.22);
        }

        /* Electrician Suggestion Card inside chat */
        .robot-pro-card {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            padding: 10px 12px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .robot-pro-name {
            font-size: 13px;
            font-weight: 700;
            color: #9a3412;
            margin-bottom: 2px;
        }

        .robot-pro-meta {
            font-size: 11px;
            color: #78716c;
        }

        .btn-confirm-robot-booking {
            background: #ea580c;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-confirm-robot-booking:hover {
            background: #c2410c;
            transform: scale(1.04);
        }

        /* Booking Confirmed Card */
        .robot-booking-success-card {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            padding: 12px;
            margin-top: 8px;
            color: #065f46;
            font-size: 12px;
        }

        /* Quick Prompt Chips */
        .robot-quick-chips {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            padding: 8px 14px;
            background: #ffffff;
            border-top: 1px solid #fed7aa;
            scrollbar-width: none;
        }

        .robot-quick-chips::-webkit-scrollbar {
            display: none;
        }

        .quick-chip {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 9999px;
            color: #9a3412;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .quick-chip:hover {
            background: #ffedd5;
            color: #7c2d12;
            border-color: #ea580c;
        }

        /* Input Bar */
        .robot-input-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px 12px;
            background: #ffffff;
            border-top: 1px solid #f5f5f4;
        }

        .robot-mic-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff7ed;
            border: 1.5px solid #fdba74;
            color: #ea580c;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .robot-mic-btn:hover {
            background: #ea580c;
            color: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 0 12px rgba(234, 88, 12, 0.4);
        }

        .robot-mic-btn.recording {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            box-shadow: 0 0 18px rgba(239, 68, 68, 0.6);
        }

        .robot-mic-btn.recording .mic-pulse-ring {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid #ef4444;
            animation: sonic-pulse 1.2s infinite;
        }

        .robot-input-bar input {
            flex: 1;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 9px 12px;
            color: #0f172a;
            font-size: 13px;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s ease;
        }

        .robot-input-bar input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.15);
            background: #ffffff;
        }

        .robot-send-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #ea580c;
            border: none;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.2s ease;
        }

        .robot-send-btn:hover {
            background: #c2410c;
        }
    </style>
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="navbar">
        <div class="container navbar-content">
            <a href="/" class="brand-logo">
                <div class="brand-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span> </span>
            </a>

            <ul class="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="#nearby-map">Nearby Electricians</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#contact">Emergency 24/7</a></li>
            </ul>

            <div class="nav-actions">
                <div class="emergency-badge">
                    <span class="emergency-dot"></span>
                    <span>24/7 Dispatch</span>
                </div>
                <button type="button" onclick="toggleRobotAssistant()" class="btn-ai-agent-trigger" id="navbarAiAgentBtn">
                    <span>🤖</span>
                    <span>Ask ElectroFix AI</span>
                </button>
                <a href="#nearby-map" class="btn btn-primary">Find Electrician</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <div class="hero-tag">
                    <span class="pulse-spark">⚡</span>
                    <span>Certified & Background-Checked Electricians in Lucknow</span>
                </div>

                <h1 class="hero-title">
                    Find a trusted <span class="gradient-accent">electrician</span> near you
                </h1>

                <p class="hero-subtitle">
                    Immediate doorstep assistance for short circuits, fan repairs, switchboards, and full-home wiring with verified Lucknow pros.
                </p>

                <div class="search-box-card">
                    <div class="search-card-header">
                        <div class="search-live-status">
                            <span class="status-live-beacon"></span>
                            <span>Live Proximity Dispatch Active</span>
                        </div>
                        <span class="search-eta-pill">⚡ Avg. Arrival: 15-20 Mins</span>
                    </div>

                    <form id="heroSearchForm" onsubmit="event.preventDefault(); handleHeroSearch();">
                        <div class="search-form-row">
                            <div class="input-group">
                                <label for="locationInput">Your Area in Lucknow</label>
                                <div class="input-wrapper">
                                    <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <input type="text" id="locationInput" placeholder="Enter locality or area (e.g. Gomti Nagar, Aliganj)..." value="Gomti Nagar, Lucknow">
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="serviceSelect">Needed Service</label>
                                <div class="input-wrapper">
                                    <svg class="input-icong" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <select id="serviceSelect">
                                        <option value="all">⚡ All Electrical Services</option>
                                        <option value="fan">🌀 Fan Repair & Installation</option>
                                        <option value="socket">🔌 Switch & Socket Fix</option>
                                        <option value="lighting">💡 Light Installation</option>
                                        <option value="wiring">🏠 Full House Wiring</option>
                                        <option value="appliance">⚙️ Appliance Hookup</option>
                                        <option value="emergency">🚨 Emergency Short Circuit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group">
                                <label style="visibility: hidden;">Search</label>
                                <button type="submit" class="btn-hero-search">
                                    <span>Find Electrician</span>
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Popular Area Quick Select Pills -->
                    <div class="hero-quick-areas">
                        <span class="quick-areas-label">Popular Localities:</span>
                        <div class="quick-areas-pills">
                            <button type="button" class="hero-quick-pill active" id="quick-pill-gomti_nagar" onclick="selectHeroQuickArea('Gomti Nagar', 'gomti_nagar')">Gomti Nagar</button>
                            <button type="button" class="hero-quick-pill" id="quick-pill-indira_nagar" onclick="selectHeroQuickArea('Indira Nagar', 'indira_nagar')">Indira Nagar</button>
                            <button type="button" class="hero-quick-pill" id="quick-pill-hazratganj" onclick="selectHeroQuickArea('Hazratganj', 'hazratganj')">Hazratganj</button>
                            <button type="button" class="hero-quick-pill" id="quick-pill-aliganj" onclick="selectHeroQuickArea('Aliganj', 'aliganj')">Aliganj</button>
                            <button type="button" class="hero-quick-pill" id="quick-pill-alambagh" onclick="selectHeroQuickArea('Alambagh', 'alambagh')">Alambagh</button>
                            <button type="button" class="hero-quick-pill" id="quick-pill-chowk" onclick="selectHeroQuickArea('Chowk', 'chowk')">Chowk</button>
                        </div>
                    </div>
                </div>

                <div class="hero-trust-badges">
                    <div class="trust-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Verified Electricians</span>
                    </div>
                    <div class="trust-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>30-Min Fast Response</span>
                    </div>
                    <div class="trust-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Safe & Insured Work</span>
                    </div>
                </div>
            </div>

            <!-- Hero Interactive Slider Showcase (Right Column) -->
            <div class="hero-visual">
                <div class="hero-ambient-glow"></div>

                <div class="hero-slider-card" id="heroSliderCard">
                    <!-- Slider Top Header -->
                    <div class="slider-header-bar">
                        <div class="slider-status-badge">
                            <span class="beacon-dot"></span>
                            <span id="sliderCategoryBadge">⚡ LIVE RADAR DISPATCH</span>
                        </div>
                        <div class="slider-nav-arrows">
                            <button type="button" class="slider-nav-btn prev" onclick="prevHeroSlide()" aria-label="Previous Slide">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" class="slider-nav-btn next" onclick="nextHeroSlide()" aria-label="Next Slide">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Slide Track -->
                    <div class="slider-slides-container">
                        <!-- Slide 0: Live Radar Screen -->
                        <div class="hero-slide active" id="heroSlide0">
                            <div class="slide-inner">
                                <h3 class="visual-header">Live GPS Dispatch</h3>
                                <p class="visual-sub">Intelligent coordinates match the nearest certified technician directly to your doorstep in Lucknow.</p>

                                <!-- Interactive Animated Radar -->
                                <div class="radar-scope-wrapper">
                                    <div class="radar-scope">
                                        <div class="radar-sweep-beam"></div>
                                        <div class="radar-ring ring-1"></div>
                                        <div class="radar-ring ring-2"></div>
                                        <div class="radar-ring ring-3"></div>
                                        
                                        <!-- Central You / Home Beacon -->
                                        <div class="radar-home-beacon">
                                            <span class="pulse-wave"></span>
                                            <span class="home-icon">🏠</span>
                                            <div class="home-tag">Your Home</div>
                                        </div>

                                        <!-- Blip 1 -->
                                        <div class="radar-pro-blip blip-1" title="Rajesh S. • 0.8 km">
                                            <span class="blip-ping"></span>
                                            <span class="blip-core">⚡</span>
                                            <div class="blip-label">Rajesh (0.8km)</div>
                                        </div>

                                        <!-- Blip 2 -->
                                        <div class="radar-pro-blip blip-2" title="Mohd Imran • 1.4 km">
                                            <span class="blip-ping"></span>
                                            <span class="blip-core">⚡</span>
                                            <div class="blip-label">Imran (1.4km)</div>
                                        </div>

                                        <!-- Blip 3 -->
                                        <div class="radar-pro-blip blip-3" title="Sunil K. • 1.9 km">
                                            <span class="blip-ping"></span>
                                            <span class="blip-core">⚡</span>
                                            <div class="blip-label">Sunil (1.9km)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="radar-live-bar">
                                    <div class="radar-live-item">
                                        <span class="dot-online"></span>
                                        <span><strong>4 Pros Online</strong> in Area</span>
                                    </div>
                                    <div class="radar-live-item">
                                        <span style="color: #f59e0b;">⏱</span>
                                        <span>Average Arrival: <strong style="color: #ffffff;">14 Mins</strong></span>
                                    </div>
                                </div>

                                <div class="quick-metrics">
                                    <div class="metric-box">
                                        <h4>4.9 ★</h4>
                                        <p>Average Rating</p>
                                    </div>
                                    <div class="metric-box">
                                        <h4>15k+</h4>
                                        <p>Jobs Done</p>
                                    </div>
                                    <div class="metric-box">
                                        <h4>100%</h4>
                                        <p>Safety Assured</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 1: Verified Pros Spotlight -->
                        <div class="hero-slide" id="heroSlide1">
                            <div class="slide-inner">
                                <h3 class="visual-header">Verified Master Electricians</h3>
                                <p class="visual-sub">Police-checked, licensed pros background-vetted for quality and safety.</p>

                                <div class="pro-spotlight-card">
                                    <div class="pro-spotlight-header">
                                        <div class="pro-avatar-badge">
                                            <span>RS</span>
                                            <div class="pro-check-dot">✓</div>
                                        </div>
                                        <div class="pro-info-col">
                                            <div class="pro-name-line">
                                                <h4>Rajesh Sharma</h4>
                                                <span class="pro-verified-tag">Master Certified</span>
                                            </div>
                                            <div class="pro-rating-line">
                                                <span class="pro-stars">★★★★★</span>
                                                <span class="pro-score">4.96</span>
                                                <span class="pro-count">(320+ jobs in Lucknow)</span>
                                            </div>
                                            <div class="pro-loc-line">📍 Gomti Nagar & Hazratganj • 8+ Yrs Exp</div>
                                        </div>
                                    </div>

                                    <div class="pro-tags-row">
                                        <span class="pro-tag">⚡ Short Circuits</span>
                                        <span class="pro-tag">🔧 3-Phase Panels</span>
                                        <span class="pro-tag">💡 Inverter Setup</span>
                                    </div>

                                    <div class="pro-status-dispatch-row">
                                        <div class="pro-dispatch-avail">
                                            <span class="pulse-active-dot"></span>
                                            <span>Ready for dispatch • ~12m away</span>
                                        </div>
                                        <button type="button" class="btn-book-spotlight" onclick="selectElectricianFromHero('Rajesh')">
                                            View & Book →
                                        </button>
                                    </div>
                                </div>

                                <div class="mini-tech-preview">
                                    <div class="mini-tech-avatar">IA</div>
                                    <div class="mini-tech-details">
                                        <strong>Mohd. Imran</strong> • 4.92 ★ (210+ jobs) • Aliganj & Indira Nagar
                                    </div>
                                    <span class="mini-tech-status">● Online</span>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Guarantee & Assurance -->
                        <div class="hero-slide" id="heroSlide2">
                            <div class="slide-inner">
                                <h3 class="visual-header">100% Quality & Safety Guarantee</h3>
                                <p class="visual-sub">Total peace of mind with insured work, certified tools, and transparent pricing.</p>

                                <div class="guarantee-grid">
                                    <div class="guarantee-card">
                                        <div class="guarantee-icon">🛡</div>
                                        <div class="guarantee-text">
                                            <h5>₹10,000 Protection</h5>
                                            <p>Insured property damage cover on all jobs.</p>
                                        </div>
                                    </div>
                                    <div class="guarantee-card">
                                        <div class="guarantee-icon">⚡</div>
                                        <div class="guarantee-text">
                                            <h5>30-Day Free Rework</h5>
                                            <p>Free resolution if the problem recurs in 30 days.</p>
                                        </div>
                                    </div>
                                    <div class="guarantee-card">
                                        <div class="guarantee-icon">🏷</div>
                                        <div class="guarantee-text">
                                            <h5>Fixed Upfront Pricing</h5>
                                            <p>Pre-approved rates. No unexpected fees.</p>
                                        </div>
                                    </div>
                                    <div class="guarantee-card">
                                        <div class="guarantee-icon">⏱</div>
                                        <div class="guarantee-text">
                                            <h5>30-Min Fast Dispatch</h5>
                                            <p>Nearest electrician routed immediately.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guarantee-subtext">
                                    <span>✓ Verified identity & background check for every electrician</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide Progress Indicators (Segments) -->
                    <div class="slider-progress-bar">
                        <button type="button" class="progress-segment active" id="progSeg0" onclick="goToHeroSlide(0)">
                            <span class="prog-fill"></span>
                            <span class="prog-title">Live Radar</span>
                        </button>
                        <button type="button" class="progress-segment" id="progSeg1" onclick="goToHeroSlide(1)">
                            <span class="prog-fill"></span>
                            <span class="prog-title">Top Pros</span>
                        </button>
                        <button type="button" class="progress-segment" id="progSeg2" onclick="goToHeroSlide(2)">
                            <span class="prog-fill"></span>
                            <span class="prog-title">Guarantee</span>
                        </button>
                    </div>
                </div>

                <!-- Floating Live Notification Ticker (Dynamic Real-Time Rotation) -->
                <div class="floating-notification-ticker" id="heroLiveTicker">
                    <div class="ticker-avatar-circle">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ticker-content" id="heroTickerContent">
                        <div class="ticker-title" id="tickerTitle">Rajesh S. Completed Job</div>
                        <div class="ticker-sub" id="tickerSubtitle">Switchboard Repair in Gomti Nagar • 4 mins ago</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Map & Nearby Electricians Section -->
    <section class="map-section" id="nearby-map">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Live Proximity Map</span>
                <h2 class="section-title">Electricians near where you live</h2>
                <p class="section-subtitle">Showing certified electricians closest to your home in <strong id="sectionHomeArea" style="color: var(--accent);">Gomti Nagar, Lucknow</strong>. Drag the <span style="color: #2563eb; font-weight: 700;">🏠 You Live Here</span> pin to your building.</p>
            </div>

            <!-- Search Filter Bar for Map -->
            <div class="map-search-bar">
                <div class="map-search-left">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="filterElectricianInput" placeholder="Filter technician by name or specialty (e.g. Wiring, Fan, MCB)..." oninput="filterElectricians()">
                </div>
                <div class="location-status">
                    <div id="locationBadge" style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 15px;">🏠</span>
                        <span style="color: #0f172a; font-weight: 700;">You live in Gomti Nagar</span>
                    </div>
                    <button type="button" onclick="detectBrowserLocationAndArea(false)" style="background: #2563eb; color: #ffffff; border: none; border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37,99,235,0.25);" title="Detect your location">
                        <span>📍 Detect My Location</span>
                    </button>
                </div>
            </div>

            <!-- Lucknow Area Quick Switcher Pills -->
            <div class="area-switcher-bar">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); white-space: nowrap;">Pick Your Home Area:</span>
                <button type="button" class="area-pill active" id="pill-gomti_nagar" onclick="switchArea('gomti_nagar')">🏠 Gomti Nagar</button>
                <button type="button" class="area-pill" id="pill-indira_nagar" onclick="switchArea('indira_nagar')">🏠 Indira Nagar</button>
                <button type="button" class="area-pill" id="pill-hazratganj" onclick="switchArea('hazratganj')">🏠 Hazratganj</button>
                <button type="button" class="area-pill" id="pill-aliganj" onclick="switchArea('aliganj')">🏠 Aliganj</button>
                <button type="button" class="area-pill" id="pill-alambagh" onclick="switchArea('alambagh')">🏠 Alambagh</button>
                <button type="button" class="area-pill" id="pill-rajajipuram" onclick="switchArea('rajajipuram')">🏠 Rajajipuram</button>
                <button type="button" class="area-pill" id="pill-chowk" onclick="switchArea('chowk')">🏠 Chowk</button>
                <button type="button" class="area-pill" id="pill-mahanagar" onclick="switchArea('mahanagar')">🏠 Mahanagar</button>
                <button type="button" class="area-pill" id="pill-ashiyana" onclick="switchArea('ashiyana')">🏠 Ashiyana</button>
                <button type="button" class="area-pill" id="pill-vikas_nagar" onclick="switchArea('vikas_nagar')">🏠 Vikas Nagar</button>
            </div>

            <div class="map-layout">
                <!-- Leaflet Interactive Map Container -->
                <div class="map-wrapper">
                    <div id="electricianMap"></div>
                    <div class="map-legend">
                        <div class="legend-item">
                            <span style="font-size: 14px;">🏠</span>
                            <span style="color: #2563eb; font-weight: 800;">You Live Here (Draggable)</span>
                        </div>
                        <div class="legend-item">
                            <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#f59e0b;"></span>
                            <span>⚡ Electrician</span>
                        </div>
                        <div class="legend-item" style="color: #475569; font-weight: 600; font-size: 11px;">
                            <span>💡 Click map to set home</span>
                        </div>
                    </div>
                </div>

                <!-- Electrician Cards List -->
                <div class="electricians-list" id="electriciansList">
                    <!-- Cards will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Quality Electrical Services</span>
                <h2 class="section-title">Our professional services</h2>
                <p class="section-subtitle">From minor repairs to complex residential rewiring, our certified electricians have you covered.</p>
            </div>

            <div class="services-grid">
                <!-- 1. Fan Repair -->
                <div class="service-card">
                    <div>
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Fan Repair & Fitting</h3>
                        <p class="service-desc">Ceiling fan noise fix, capacitor replacement, regulator adjustments, and new fan installs.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹149</span></div>
                        <div class="service-link" onclick="quickSelectService('fan')">Select Service &rarr;</div>
                    </div>
                </div>

                <!-- 2. Switch & Socket -->
                <div class="service-card">
                    <div>
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Switch & Socket Fix</h3>
                        <p class="service-desc">Burnt socket replacements, modular switchboard upgrades, and MCB breaker troubleshooting.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹99</span></div>
                        <div class="service-link" onclick="quickSelectService('socket')">Select Service &rarr;</div>
                    </div>
                </div>

                <!-- 3. Light Installation -->
                <div class="service-card">
                    <div>
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Light Installation</h3>
                        <p class="service-desc">Chandelier setup, false ceiling LED strips, smart lighting, tube lights, and wall sconces.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹199</span></div>
                        <div class="service-link" onclick="quickSelectService('lighting')">Select Service &rarr;</div>
                    </div>
                </div>

                <!-- 4. Wiring -->
                <div class="service-card">
                    <div>
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Full House Wiring</h3>
                        <p class="service-desc">Concealed copper wiring, short circuit isolation, grounding/earthing check, and safety audits.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹499</span></div>
                        <div class="service-link" onclick="quickSelectService('wiring')">Select Service &rarr;</div>
                    </div>
                </div>

                <!-- 5. Appliance Repair -->
                <div class="service-card">
                    <div>
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Appliance Repair</h3>
                        <p class="service-desc">Geysers, AC stabilizer connections, inverter battery lines, water heater element replacement.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹299</span></div>
                        <div class="service-link" onclick="quickSelectService('appliance')">Select Service &rarr;</div>
                    </div>
                </div>

                <!-- 6. Emergency Work -->
                <div class="service-card" style="border-color: #fecaca; background: #fffdfd;">
                    <div>
                        <div class="service-icon" style="background: #fee2e2; color: #dc2626;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="service-title">Emergency Electrical Work</h3>
                        <p class="service-desc">Sparking wires, sudden blackouts, smoking switchboards, and breaker trips requiring immediate attention.</p>
                    </div>
                    <div class="service-footer">
                        <div class="service-price">From <span>₹399</span></div>
                        <div class="service-link" style="color: #dc2626;" onclick="quickSelectService('emergency')">Get Fast Help &rarr;</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-section" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Simple Process</span>
                <h2 class="section-title">How ElectroFix works</h2>
                <p class="section-subtitle">Get professional electrical service in four simple and transparent steps.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Choose a service</h3>
                    <p class="step-desc">Select the electrical service you require from our transparent service catalog.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Find nearby pro</h3>
                    <p class="step-desc">Browse nearby verified electricians on the live map and check ratings & distance.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Select a time</h3>
                    <p class="step-desc">Pick an instant emergency arrival (within 30 mins) or schedule a convenient slot.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3 class="step-title">Get work done</h3>
                    <p class="step-desc">A certified pro arrives equipped with proper tools and completes the job safely.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Dispatch CTA Banner -->
    <section class="emergency-banner" id="contact">
        <div class="container emergency-content">
            <div class="emergency-text">
                <h3>Facing an electrical emergency right now?</h3>
                <p>Sparking wires, burning smells, or sudden complete blackouts. Our 24/7 team is on standby.</p>
            </div>
            <div class="emergency-actions">
                <a href="tel:1800353287" class="btn btn-emergency-call">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Call: 1800-ELECTRO
                </a>
                <a href="#nearby-map" class="btn btn-outline" style="color: #ffffff; border-color: rgba(255,255,255,0.25);">Find Nearest Pro</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="brand-logo">
                        <div class="brand-icon">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span>ElectroFix</span>
                    </a>
                    <p>On-demand certified electrician booking platform. Connecting homeowners and businesses with background-verified electrical pros.</p>
                </div>

                <div class="footer-col">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#services">All Services</a></li>
                        <li><a href="#nearby-map">Live Map</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#contact">Emergency 24/7</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Top Services</h5>
                    <ul>
                        <li><a href="#services">Fan Repair & Fitting</a></li>
                        <li><a href="#services">Switchboard Upgrade</a></li>
                        <li><a href="#services">LED Light Installation</a></li>
                        <li><a href="#services">Full House Wiring</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Safety Guarantee</h5>
                    <p style="font-size: 13px; color: #94a3b8; margin-bottom: 12px;">All technicians carry verified government IDs, verified electrical licenses, and follow standardized safety protocols.</p>
                    <div style="font-size: 13px; color: #10b981; font-weight: 600;">✓ 30-Day Service Guarantee</div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>© 2026 ElectroFix Technologies Inc. All rights reserved.</div>
                <div>Designed for prompt & safe electrical service in Lucknow.</div>
            </div>
        </div>
    </footer>

    <!-- Mobile Sticky Quick Action Bar -->
    <div class="mobile-bottom-bar">
        <button type="button" onclick="detectBrowserLocationAndArea(true)" class="mobile-bar-btn btn-detect" title="Detect your location">
            <span>📍 Near Me</span>
        </button>
        <a href="#nearby-map" class="mobile-bar-btn" style="background: #0f172a; color: #ffffff;" title="View Live Map">
            <span>🗺️ Map</span>
        </a>
        <a href="tel:1800353287" class="mobile-bar-btn btn-call" title="Emergency 24/7 Helpline">
            <span>📞 Call 24/7</span>
        </a>
    </div>

    <!-- =====================================================================
         Bijli Guru (बिजली गुरु) AI Assistant Widget (Friendly Mahatma Guide)
         ===================================================================== -->
    <div class="electrofix-robot-widget" id="electrofixRobotWidget">
        <!-- Floating Launcher Pill -->
        <button type="button" class="robot-launcher-btn" id="robotLauncherBtn" onclick="toggleRobotAssistant()" aria-label="Open Bijli Guru AI Assistant">
            <div class="launcher-guru-avatar">
                <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" class="launcher-guru-img">
                <span class="launcher-halo-badge">🌸</span>
            </div>
            <div class="launcher-text-col">
                <span class="launcher-badge">AI GUIDE • लखनऊ</span>
                <span class="launcher-title">बिजली गुरु (Ask AI)</span>
            </div>
            <span class="launcher-live-pulse" title="Bijli Guru Active"></span>
        </button>

        <!-- Bijli Guru Console Card (Chat & Consultation Window) -->
        <div class="robot-console-card" id="robotConsoleCard">
            <!-- Header Bar -->
            <div class="robot-console-header">
                <div class="console-title-group">
                    <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" class="console-bot-icon">
                    <div>
                        <h4 class="console-title">Bijli Guru (बिजली गुरु)</h4>
                        <span class="console-subtitle" id="robotHeaderSub">Friendly & Calm Electrical Guide • ElectroLKO</span>
                    </div>
                </div>
                <div class="console-actions-group">
                    <button type="button" class="console-btn-icon" id="robotSpeechToggleBtn" onclick="toggleSpeechOutput()" title="Toggle Voice Output (TTS)">🔊</button>
                    <button type="button" class="console-btn-icon" onclick="resetRobotChat()" title="Restart Conversation">↺</button>
                    <button type="button" class="console-btn-icon" onclick="toggleRobotAssistant()" title="Minimize Console">✕</button>
                </div>
            </div>

            <!-- Sadhu Mahatma Avatar Chamber (Animated Avatar & Aura Glow) -->
            <div class="robot-chamber" id="robotChamber">
                <div class="guru-avatar-wrapper">
                    <div class="guru-portrait-ring" id="robotHead">
                        <div class="guru-halo-aura"></div>
                        <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" class="guru-avatar-img">
                        <span class="guru-aura-badge">🙏</span>
                    </div>
                </div>

                <!-- Live State Badge -->
                <div class="robot-state-badge" id="robotStateBadge">
                    <span class="state-dot" id="robotStateDot"></span>
                    <span id="robotStateText">🧘 TAIYAR • SAHAYTA READY</span>
                </div>

                <!-- Equalizer Visualizer -->
                <div class="robot-audio-visualizer" id="robotVisualizer">
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                    <span class="eq-bar"></span>
                </div>
            </div>

            <!-- Tool Banner (appears dynamically when executing tools) -->
            <div class="robot-tool-banner" id="robotToolBanner" style="display: none;">
                <span class="tool-spinner">⚡</span>
                <span id="robotToolBannerText">Finding nearby electricians in Lucknow...</span>
            </div>

            <!-- Chat Messages Thread -->
            <div class="robot-messages-box" id="robotChatMessages">
                <!-- Initial Welcome Message from Bijli Guru -->
                <div class="agent-msg-bubble">
                    <div class="agent-msg-avatar">
                        <img src="/images/sadhu_guru.jpg" alt="Bijli Guru">
                    </div>
                    <div class="agent-msg-text">
                        🌸 <strong>Pranam! Main hoon Bijli Guru (बिजली गुरु)</strong> — ElectroLKO ka aapka shant aur anubhavi electrical guide.
                        <br><br>
                        Aapke ghar me koi fan aawaz kar raha hai, switchboard se spark aa rahi hai, ya verified electrician chahiye?
                        <br><br>
                        Mujhe likhkar ya <strong>🎤 Mic</strong> dabakar <em>Hindi, Hinglish ya English</em> me batayein. Main samasya ki jaanch aur saste sahi mistri provide karta hoon!
                    </div>
                </div>
            </div>

            <!-- Quick Prompt Chips -->
            <div class="robot-quick-chips" id="robotQuickChips">
                <button type="button" class="quick-chip" onclick="handleQuickChipClick('Fan kharab hai, humming sound aa rahi hai')">🌀 Fan Humming</button>
                <button type="button" class="quick-chip" onclick="handleQuickChipClick('Switchboard se spark aa raha hai, burning smell hai')">⚠️ Spark & Smoke</button>
                <button type="button" class="quick-chip" onclick="handleQuickChipClick('Gomti Nagar me verified electrician bhej do')">⚡ Electrician Near Me</button>
                <button type="button" class="quick-chip" onclick="handleQuickChipClick('MCB baar baar trip ho rahi hai')">🔌 MCB Tripping</button>
                <button type="button" class="quick-chip" onclick="handleQuickChipClick('New light fitting aur chandelier lagwana hai')">💡 Light Fitting</button>
            </div>

            <!-- Input Bar -->
            <div class="robot-input-bar">
                <button type="button" class="robot-mic-btn" id="robotMicBtn" onclick="toggleRobotVoiceInput()" title="Voice Input (Hindi/English)">
                    <span class="mic-pulse-ring"></span>
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                        <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                    </svg>
                </button>
                <input type="text" id="robotInputText" placeholder="Bijli Guru se poochhein (Hindi/English)..." onkeydown="handleRobotInputKey(event)" autocomplete="off">
                <button type="button" class="robot-send-btn" id="robotSendBtn" onclick="handleRobotSendClick()" title="Send">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Leaflet JS for Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Predefined Lucknow Localities & Coordinates
        const LUCKNOW_AREAS = {
            gomti_nagar: { name: 'Gomti Nagar', lat: 26.8530, lng: 80.9980 },
            indira_nagar: { name: 'Indira Nagar', lat: 26.8780, lng: 80.9850 },
            hazratganj: { name: 'Hazratganj', lat: 26.8467, lng: 80.9462 },
            aliganj: { name: 'Aliganj', lat: 26.8920, lng: 80.9380 },
            alambagh: { name: 'Alambagh', lat: 26.8150, lng: 80.9100 },
            rajajipuram: { name: 'Rajajipuram', lat: 26.8520, lng: 80.8850 },
            chowk: { name: 'Chowk', lat: 26.8680, lng: 80.9020 },
            mahanagar: { name: 'Mahanagar', lat: 26.8720, lng: 80.9520 },
            ashiyana: { name: 'Ashiyana', lat: 26.7900, lng: 80.9050 },
            charbagh: { name: 'Charbagh', lat: 26.8320, lng: 80.9220 },
            vikas_nagar: { name: 'Vikas Nagar', lat: 26.8950, lng: 80.9600 },
            jankipuram: { name: 'Jankipuram', lat: 26.9200, lng: 80.9450 },
            desktop: { name: 'Network IP Location', lat: 26.8373, lng: 80.9165 }
        };

        // Current User Location ("Where You Live")
        let userLocation = {
            name: "Gomti Nagar, Lucknow",
            lat: 26.8530,
            lng: 80.9980
        };

        let electriciansData = [];
        let map = null;
        let userMarker = null;
        let markers = {};
        let activeElectricianId = null;

        // Haversine distance in km between two lat/lng coordinates
        function calculateDistanceKm(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // Find closest known Lucknow area from coordinates
        function findClosestLucknowArea(lat, lng) {
            let closestKey = 'gomti_nagar';
            let closestName = 'Gomti Nagar';
            let minDistance = Infinity;

            for (const [key, area] of Object.entries(LUCKNOW_AREAS)) {
                if (key === 'desktop') continue;
                const dist = calculateDistanceKm(lat, lng, area.lat, area.lng);
                if (dist < minDistance) {
                    minDistance = dist;
                    closestKey = key;
                    closestName = area.name;
                }
            }
            return { name: closestName, key: closestKey, distanceKm: minDistance.toFixed(1) };
        }

        // 1. Initialize Leaflet Map
        function initMap() {
            map = L.map('electricianMap', {
                center: [userLocation.lat, userLocation.lng],
                zoom: 13,
                zoomControl: true,
                scrollWheelZoom: false,
                attributionControl: false
            });

            // OpenStreetMap tile layer
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // Create the custom "🏠 You Live Here" marker
            updateUserMarker(userLocation.lat, userLocation.lng, userLocation.name);

            // Listen for user clicking directly on the map to set their home
            map.on('click', function (e) {
                const { lat, lng } = e.latlng;
                fetchAreaFromCoordinates(lat, lng);
            });
        }

        // 2. Create or Update the custom "🏠 You Live Here" Marker
        function updateUserMarker(lat, lng, label) {
            if (!map) return;

            const homeIcon = L.divIcon({
                className: 'custom-user-marker-icon',
                html: `
                    <div class="user-home-marker">
                        <div class="home-pulse-ring"></div>
                        <div class="home-pin-bubble">
                            <span style="font-size: 14px;">🏠</span>
                            <span>You Live Here</span>
                        </div>
                        <div class="home-pin-pointer"></div>
                    </div>
                `,
                iconSize: [130, 48],
                iconAnchor: [65, 48],
                popupAnchor: [0, -48]
            });

            if (!userMarker) {
                userMarker = L.marker([lat, lng], {
                    icon: homeIcon,
                    draggable: true,
                    zIndexOffset: 1000
                }).addTo(map);

                // Allow dragging the home pin
                userMarker.on('dragend', function (e) {
                    const pos = e.target.getLatLng();
                    fetchAreaFromCoordinates(pos.lat, pos.lng);
                });
            } else {
                userMarker.setIcon(homeIcon);
                userMarker.setLatLng([lat, lng]);
            }

            userMarker.bindPopup(`
                <div style="font-family: inherit; font-size: 13px; text-align: center; padding: 4px; min-width: 200px;">
                    <div style="font-size: 20px; margin-bottom: 2px;">🏠</div>
                    <strong style="color: #2563eb; font-size: 14px;">You Live Here</strong><br>
                    <span style="color: #0f172a; font-weight: 700;">${label}</span><br>
                    <div style="margin-top: 8px; font-size: 11px; color: #64748b; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px dashed #cbd5e1; line-height: 1.4;">
                        ⚡ All electricians below are calculated based on where you live.<br>
                        <em>(Drag this pin to any street or click map to move)</em>
                    </div>
                </div>
            `);

            map.setView([lat, lng], 13);
        }

        // 3. Central Function to Set Home Location & Recalculate Distances
        function setHomeLocation(lat, lng, label, areaKey, saveStorage = true) {
            userLocation.lat = lat;
            userLocation.lng = lng;
            userLocation.name = label;

            if (saveStorage) {
                try {
                    localStorage.setItem('electrofix_user_home', JSON.stringify({
                        lat, lng, label, areaKey
                    }));
                } catch (e) {}
            }

            // Update Hero Input
            const input = document.getElementById('locationInput');
            if (input) input.value = label;

            // Update Map Subtitle
            const sectionHome = document.getElementById('sectionHomeArea');
            if (sectionHome) sectionHome.innerText = label;

            // Update Location Badge
            const badge = document.getElementById('locationBadge');
            if (badge) {
                badge.innerHTML = `
                    <span style="font-size: 15px;">🏠</span>
                    <span style="color: #0f172a; font-weight: 700;">You live in ${label}</span>
                `;
            }

            updateUserMarker(lat, lng, label);
            setActiveAreaPill(areaKey);
            fetchElectriciansFromApi(lat, lng);
        }

        // 4. Intelligent Geocoding: Resolves coordinates into recognized, friendly Lucknow area name
        function fetchAreaFromCoordinates(lat, lng, callback) {
            const badge = document.getElementById('locationBadge');
            if (badge) {
                badge.innerHTML = `<span>🔍 Identifying your area in Lucknow...</span>`;
            }

            // Call intelligent backend geocoding layer
            fetch(`/api/geocode?latitude=${lat}&longitude=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.success) {
                        const areaName = data.formatted || (data.area + ', Lucknow');
                        setHomeLocation(lat, lng, areaName, data.area_key);
                        if (callback) callback();
                    } else {
                        const closest = findClosestLucknowArea(lat, lng);
                        setHomeLocation(lat, lng, `${closest.name}, Lucknow`, closest.key);
                        if (callback) callback();
                    }
                })
                .catch(err => {
                    console.warn("Geocoding API failed, falling back to closest Lucknow locality:", err);
                    const closest = findClosestLucknowArea(lat, lng);
                    setHomeLocation(lat, lng, `${closest.name}, Lucknow`, closest.key);
                    if (callback) callback();
                });
        }

        // 5. Fetch directly from Browser Coordinates (lat/lng), then reverse-geocode to tell exact area!
        function detectBrowserLocationAndArea(scrollIntoView = false) {
            const badge = document.getElementById('locationBadge');
            const input = document.getElementById('locationInput');
            
            if (badge) {
                badge.innerHTML = `
                    <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="#2563eb"></path>
                    </svg>
                    <span>Detecting your location...</span>
                `;
            }
            if (input) {
                input.value = "Detecting your location...";
            }

            if (!navigator.geolocation) {
                if (badge) badge.innerHTML = `<span>⚠️ Geolocation not supported by your browser.</span>`;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log("Successfully fetched location:", lat, lng);

                    // Reverse geocode the browser's exact coordinates to announce the area
                    fetchAreaFromCoordinates(lat, lng, () => {
                        if (scrollIntoView) {
                            const mapSec = document.getElementById('nearby-map');
                            if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                },
                (error) => {
                    console.warn("Browser geolocation permission denied or error:", error);
                    let msg = "Could not fetch location.";
                    if (error.code === 1) {
                        msg = "Please allow location access in your browser.";
                    } else if (error.code === 2) {
                        msg = "Location unavailable from device.";
                    } else if (error.code === 3) {
                        msg = "Location request timed out.";
                    }
                    if (badge) {
                        badge.innerHTML = `<span>⚠️ ${msg}</span>`;
                    }
                    if (input) {
                        input.value = userLocation.name;
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
        window.detectLocationFromBrowser = detectBrowserLocationAndArea;

        // 6. Desktop Network Detection via Laravel /api/my-location
        function detectDesktopLocation(scrollIntoView = false) {
            const badge = document.getElementById('locationBadge');
            const input = document.getElementById('locationInput');
            if (badge) {
                badge.innerHTML = `
                    <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="#2563eb"></path>
                    </svg>
                    <span>Fetching your location from network...</span>
                `;
            }
            if (input) {
                input.value = "Fetching network location...";
            }

            fetch('/api/my-location')
                .then(res => res.json())
                .then(data => {
                    if (data && data.success) {
                        LUCKNOW_AREAS.desktop.lat = data.latitude;
                        LUCKNOW_AREAS.desktop.lng = data.longitude;
                        LUCKNOW_AREAS.desktop.name = `${data.city} (${data.zip})`;

                        fetchAreaFromCoordinates(data.latitude, data.longitude, () => {
                            if (scrollIntoView) {
                                const mapSec = document.getElementById('nearby-map');
                                if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error("Failed to detect desktop network location:", err);
                    setHomeLocation(26.8530, 80.9980, "Gomti Nagar, Lucknow", 'gomti_nagar');
                });
        }

        // 7. Quick Area Switcher
        function switchArea(areaKey) {
            if (areaKey === 'desktop') {
                detectDesktopLocation(false);
                return;
            }

            const area = LUCKNOW_AREAS[areaKey];
            if (!area) return;

            setHomeLocation(area.lat, area.lng, `${area.name}, Lucknow`, areaKey);
        }

        function setActiveAreaPill(areaKey) {
            document.querySelectorAll('.area-pill').forEach(pill => pill.classList.remove('active'));
            if (areaKey) {
                const pill = document.getElementById(`pill-${areaKey}`);
                if (pill) pill.classList.add('active');
            }
        }

        // 8. Search Area / Colony from Hero Input
        function searchLocationFromInput() {
            const query = document.getElementById('locationInput').value.trim();
            if (!query) return;

            searchLocationOrArea(query, () => {
                const mapSec = document.getElementById('nearby-map');
                if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
            });
        }

        function searchLocationOrArea(query, callback) {
            const cleanQuery = query.toLowerCase().trim();

            // Check predefined Lucknow areas first
            for (const [key, area] of Object.entries(LUCKNOW_AREAS)) {
                if (cleanQuery.includes(key) || cleanQuery.includes(area.name.toLowerCase())) {
                    setHomeLocation(area.lat, area.lng, `${area.name}, Lucknow`, key);
                    if (callback) callback();
                    return;
                }
            }

            // Query OpenStreetMap Nominatim for live geocoding of any Lucknow street/colony
            const badge = document.getElementById('locationBadge');
            if (badge) {
                badge.innerHTML = `<span>🔍 Locating "${query}" in Lucknow...</span>`;
            }

            const targetUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Lucknow, Uttar Pradesh')}&limit=1`;
            fetch(targetUrl)
                .then(res => res.json())
                .then(results => {
                    if (results && results.length > 0) {
                        const lat = parseFloat(results[0].lat);
                        const lng = parseFloat(results[0].lon);
                        fetchAreaFromCoordinates(lat, lng, callback);
                    } else {
                        // Fallback to Gomti Nagar if geocoder returned nothing
                        setHomeLocation(userLocation.lat, userLocation.lng, query, null);
                        if (callback) callback();
                    }
                })
                .catch(err => {
                    console.warn("Geocoding failed, keeping current coordinates:", err);
                    setHomeLocation(userLocation.lat, userLocation.lng, query, null);
                    if (callback) callback();
                });
        }

        // 9. Fetch from Laravel API: GET /api/electricians/nearby?latitude=...&longitude=...
        function fetchElectriciansFromApi(lat, lng, service = '') {
            let url = `/api/electricians/nearby?latitude=${lat}&longitude=${lng}`;
            if (service && service !== 'all') {
                url += `&service=${encodeURIComponent(service)}`;
            }

            const listContainer = document.getElementById('electriciansList');
            if (listContainer) {
                listContainer.style.opacity = '0.5';
            }

            fetch(url)
                .then(res => res.json())
                .then(response => {
                    if (response && response.success && response.data) {
                        electriciansData = response.data;
                        updateElectricianMapMarkers(electriciansData);
                        renderElectricianCards(electriciansData);
                    }
                })
                .catch(err => {
                    console.error("Failed to load electricians from API:", err);
                })
                .finally(() => {
                    if (listContainer) {
                        listContainer.style.opacity = '1';
                    }
                });
        }

        // 10. Update Electrician markers on Leaflet Map
        function updateElectricianMapMarkers(list) {
            // Remove previous electrician markers
            Object.values(markers).forEach(m => map.removeLayer(m));
            markers = {};

            list.forEach(elec => {
                const lat = parseFloat(elec.latitude || elec.lat);
                const lng = parseFloat(elec.longitude || elec.lng);
                if (!lat || !lng) return;

                const elecIcon = L.divIcon({
                    className: 'custom-elec-marker-icon',
                    html: `<div class="elec-map-pin" id="pin-${elec.id}" title="${elec.name}">⚡</div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -20]
                });

                const marker = L.marker([lat, lng], { icon: elecIcon }).addTo(map);

                const popupContent = `
                    <div style="font-family: inherit; min-width: 200px; padding: 4px;">
                        <div style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 2px;">⚡ ${elec.name}</div>
                        <div style="font-size: 12px; color: #d97706; font-weight: 700; margin-bottom: 2px;">📍 ${elec.area || 'Lucknow'}</div>
                        <div style="font-size: 11px; color: #64748b; margin-bottom: 6px;">${elec.address}</div>
                        <div style="font-size: 13px; color: #2563eb; font-weight: 800; margin-bottom: 6px; background: #eff6ff; padding: 4px 8px; border-radius: 4px;">
                            📍 ${elec.distance} from your home
                        </div>
                        <div style="font-size: 12px; color: #10b981; font-weight: 600; margin-bottom: 10px;">📞 ${elec.phone}</div>
                        <a href="tel:${elec.phone}" style="display: block; text-align: center; background: #0f172a; color: #ffffff; text-decoration: none; padding: 7px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                            📞 Call Electrician Now
                        </a>
                    </div>
                `;

                marker.bindPopup(popupContent);
                marker.on('click', () => {
                    selectElectrician(elec.id, false);
                });

                markers[elec.id] = marker;
            });
        }

        // 11. Render Electrician Cards list
        function renderElectricianCards(list) {
            const container = document.getElementById('electriciansList');
            container.innerHTML = '';

            if (list.length === 0) {
                container.innerHTML = `
                    <div style="background: #ffffff; border: 1px dashed var(--border-color); border-radius: 12px; padding: 30px; text-align: center; color: var(--text-muted);">
                        <p style="font-size: 15px; font-weight: 600;">No electricians found near this location.</p>
                        <p style="font-size: 13px; margin-top: 4px;">Try selecting another area in Lucknow above.</p>
                    </div>
                `;
                return;
            }

            list.forEach(elec => {
                const card = document.createElement('div');
                card.className = `electrician-card ${activeElectricianId === elec.id ? 'active' : ''}`;
                card.id = `card-${elec.id}`;
                card.onclick = () => selectElectrician(elec.id, true);

                card.innerHTML = `
                    <div class="card-top">
                        <div class="electrician-info">
                            <div class="electrician-avatar">${elec.initials || '⚡'}</div>
                            <div>
                                <div class="electrician-name">${elec.name}</div>
                                <div class="electrician-rating">★ ${elec.rating || '4.8'} <span style="color: var(--text-muted); font-weight: 400;">(${elec.reviews || '48'})</span></div>
                                <div style="font-size: 11px; color: #d97706; font-weight: 700; margin-top: 2px;">📍 ${elec.area || 'Lucknow'}</div>
                            </div>
                        </div>
                        <div class="distance-badge" title="Distance from where you live">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            ${elec.distance} from home
                        </div>
                    </div>

                    <div style="font-size: 12px; color: #64748b; margin: 6px 0 10px; line-height: 1.4;">
                        ${elec.address}
                    </div>

                    <div class="card-bottom">
                        <div class="status-indicator ${elec.status_class || 'available'}">
                            <span class="status-dot"></span>
                            <span>${elec.status || 'Available Now'}</span>
                        </div>
                        <a href="tel:${elec.phone}" class="btn-select-electrician" onclick="event.stopPropagation();" style="text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            📞 Call: ${elec.phone}
                        </a>
                    </div>
                `;

                container.appendChild(card);
            });
        }

        // 12. Select Electrician (pans map & opens popup)
        function selectElectrician(id, panMap = true) {
            activeElectricianId = id;
            const elec = electriciansData.find(e => e.id === id);
            if (!elec) return;

            // Highlight card
            document.querySelectorAll('.electrician-card').forEach(c => c.classList.remove('active'));
            const activeCard = document.getElementById(`card-${id}`);
            if (activeCard) {
                activeCard.classList.add('active');
                activeCard.scrollIntoView({ behavior: 'smooth' });
            }

            // Pan and open marker popup
            if (markers[id]) {
                const lat = parseFloat(elec.latitude || elec.lat);
                const lng = parseFloat(elec.longitude || elec.lng);
                if (panMap && lat && lng) {
                    map.flyTo([lat, lng], 15, { duration: 0.8 });
                }
                markers[id].openPopup();
            }
        }

        // 13. Live text filter for electricians
        function filterElectricians() {
            const query = document.getElementById('filterElectricianInput').value.toLowerCase().trim();
            const filtered = electriciansData.filter(e => {
                const matchName = e.name && e.name.toLowerCase().includes(query);
                const matchArea = e.area && e.area.toLowerCase().includes(query);
                const matchAddress = e.address && e.address.toLowerCase().includes(query);
                return matchName || matchArea || matchAddress;
            });
            renderElectricianCards(filtered);
        }

        // 14. Quick select from services section
        function quickSelectService(serviceKey) {
            const dropdown = document.getElementById('serviceSelect');
            if (dropdown) {
                dropdown.value = serviceKey;
            }
            const mapSec = document.getElementById('nearby-map');
            if (mapSec) {
                mapSec.scrollIntoView({ behavior: 'smooth' });
            }
            fetchElectriciansFromApi(userLocation.lat, userLocation.lng, serviceKey);
        }

        // 15. Hero search submit
        function handleHeroSearch() {
            const locQuery = document.getElementById('locationInput').value.trim();
            const serv = document.getElementById('serviceSelect').value;

            if (locQuery) {
                searchLocationOrArea(locQuery, () => {
                    const mapSec = document.getElementById('nearby-map');
                    if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
                    fetchElectriciansFromApi(userLocation.lat, userLocation.lng, serv === 'all' ? '' : serv);
                });
            } else {
                const mapSec = document.getElementById('nearby-map');
                if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
                fetchElectriciansFromApi(userLocation.lat, userLocation.lng, serv === 'all' ? '' : serv);
            }
        }

        // 16. Hero Slider Controller
        let currentHeroSlide = 0;
        const totalHeroSlides = 3;
        let heroSliderInterval = null;
        const heroSlideCategories = [
            "⚡ LIVE RADAR DISPATCH",
            "🛡 TOP PROS SPOTLIGHT",
            "✨ 100% SAFETY GUARANTEE"
        ];

        function goToHeroSlide(index) {
            currentHeroSlide = (index + totalHeroSlides) % totalHeroSlides;
            for (let i = 0; i < totalHeroSlides; i++) {
                const slide = document.getElementById(`heroSlide${i}`);
                const seg = document.getElementById(`progSeg${i}`);
                if (slide) slide.classList.toggle('active', i === currentHeroSlide);
                if (seg) seg.classList.toggle('active', i === currentHeroSlide);
            }
            const catBadge = document.getElementById('sliderCategoryBadge');
            if (catBadge) {
                catBadge.innerText = heroSlideCategories[currentHeroSlide];
            }
        }

        function nextHeroSlide() {
            goToHeroSlide(currentHeroSlide + 1);
        }

        function prevHeroSlide() {
            goToHeroSlide(currentHeroSlide - 1);
        }

        function startHeroSliderAutoplay() {
            stopHeroSliderAutoplay();
            heroSliderInterval = setInterval(() => {
                nextHeroSlide();
            }, 5500);
        }

        function stopHeroSliderAutoplay() {
            if (heroSliderInterval) {
                clearInterval(heroSliderInterval);
                heroSliderInterval = null;
            }
        }

        // 17. Floating Live Activity Ticker
        const tickerActivities = [
            { title: "Rajesh S. Completed Job", sub: "Switchboard Repair in Gomti Nagar • 4 mins ago" },
            { title: "Amit K. Dispatched Now", sub: "Emergency MCB Tripping in Indira Nagar • 8 mins ago" },
            { title: "Sunil V. Arrived on Site", sub: "Fan Installation in Aliganj • 14 mins ago" },
            { title: "Mohd Imran Completed Job", sub: "Full Home Wiring in Hazratganj • 21 mins ago" },
            { title: "Dinesh K. Dispatched Now", sub: "Power Socket Replacement in Alambagh • 29 mins ago" }
        ];
        let currentTickerIndex = 0;

        function cycleLiveTicker() {
            const content = document.getElementById('heroTickerContent');
            const titleEl = document.getElementById('tickerTitle');
            const subEl = document.getElementById('tickerSubtitle');
            if (!content || !titleEl || !subEl) return;

            content.style.opacity = '0';
            content.style.transform = 'translateY(6px)';

            setTimeout(() => {
                currentTickerIndex = (currentTickerIndex + 1) % tickerActivities.length;
                const item = tickerActivities[currentTickerIndex];
                titleEl.innerText = item.title;
                subEl.innerText = item.sub;
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';
            }, 250);
        }

        // 18. Quick Select Area from Hero Pills
        function selectHeroQuickArea(areaName, areaKey) {
            const input = document.getElementById('locationInput');
            if (input) input.value = areaName + ', Lucknow';

            document.querySelectorAll('.hero-quick-pill').forEach(p => p.classList.remove('active'));
            const clicked = document.getElementById(`quick-pill-${areaKey}`);
            if (clicked) clicked.classList.add('active');

            if (typeof switchArea === 'function' && areaKey) {
                switchArea(areaKey);
                const mapSec = document.getElementById('nearby-map');
                if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });
            } else {
                handleHeroSearch();
            }
        }

        // 19. Spotlight Book CTA
        function selectElectricianFromHero(nameQuery) {
            const mapSec = document.getElementById('nearby-map');
            if (mapSec) mapSec.scrollIntoView({ behavior: 'smooth' });

            if (typeof electriciansData !== 'undefined' && electriciansData && electriciansData.length > 0) {
                const found = electriciansData.find(e => e.name && e.name.toLowerCase().includes(nameQuery.toLowerCase()));
                if (found) {
                    selectElectrician(found.id, true);
                    return;
                }
            }
            handleHeroSearch();
        }

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            // Fetch directly from browser coordinates on page load
            detectBrowserLocationAndArea(false);

            // Initialize Hero Slider & Live Ticker
            startHeroSliderAutoplay();
            const sliderCard = document.getElementById('heroSliderCard');
            if (sliderCard) {
                sliderCard.addEventListener('mouseenter', stopHeroSliderAutoplay);
                sliderCard.addEventListener('mouseleave', startHeroSliderAutoplay);
            }
            setInterval(cycleLiveTicker, 4000);
        });

        /* =====================================================================
           ElectroFix AI Agent - Client Controller & Voice Engine
           ===================================================================== */
        let robotConversationHistory = [];
        let robotSpeechEnabled = true;
        let robotRecognition = null;
        let isRobotListening = false;
        let currentRobotState = 'IDLE';

        // Toggle open/close of Robot Widget console
        function toggleRobotAssistant() {
            const card = document.getElementById('robotConsoleCard');
            if (!card) return;
            const isOpen = card.classList.toggle('open');
            if (isOpen) {
                scrollRobotChatToBottom();
                const input = document.getElementById('robotInputText');
                if (input) setTimeout(() => input.focus(), 250);
            } else {
                if (isRobotListening && robotRecognition) {
                    robotRecognition.stop();
                }
                if (window.speechSynthesis) {
                    window.speechSynthesis.cancel();
                }
            }
        }

        // Update Robot State & Visual Animations
        function updateRobotState(state, toolName = null) {
            currentRobotState = state;
            const chamber = document.getElementById('robotChamber');
            const stateDot = document.getElementById('robotStateDot');
            const stateText = document.getElementById('robotStateText');
            const toolBanner = document.getElementById('robotToolBanner');
            const toolBannerText = document.getElementById('robotToolBannerText');

            if (!chamber || !stateDot || !stateText) return;

            // Reset mode classes
            chamber.classList.remove('speaking-mode', 'listening-mode', 'emergency-mode');
            if (toolBanner) toolBanner.style.display = 'none';

            switch (state) {
                case 'LISTENING':
                    chamber.classList.add('listening-mode');
                    stateDot.style.background = '#10b981';
                    stateDot.style.boxShadow = '0 0 8px #10b981';
                    stateText.innerText = '🎤 SUN RAHE HAIN (LISTENING)...';
                    break;

                case 'THINKING':
                    stateDot.style.background = '#a855f7';
                    stateDot.style.boxShadow = '0 0 8px #a855f7';
                    stateText.innerText = '💡 SOCH RAHE HAIN (THINKING)...';
                    break;

                case 'USING TOOL':
                    stateDot.style.background = '#f59e0b';
                    stateDot.style.boxShadow = '0 0 8px #f59e0b';
                    stateText.innerText = `⚡ ${toolName ? toolName.toUpperCase() : 'JAANCH'}...`;
                    if (toolBanner && toolBannerText) {
                        toolBanner.style.display = 'flex';
                        toolBannerText.innerText = `Executing: ${toolName || 'Application Tool'} in Lucknow DB...`;
                    }
                    break;

                case 'SPEAKING':
                    chamber.classList.add('speaking-mode');
                    stateDot.style.background = '#ea580c';
                    stateDot.style.boxShadow = '0 0 8px #ea580c';
                    stateText.innerText = '🗣️ SAMJHA RAHE HAIN...';
                    break;

                case 'EMERGENCY':
                    chamber.classList.add('emergency-mode');
                    stateDot.style.background = '#ef4444';
                    stateDot.style.boxShadow = '0 0 10px #ef4444';
                    stateText.innerText = '⚠️ SAVDHANI • EMERGENCY ALERT';
                    break;

                case 'IDLE':
                default:
                    stateDot.style.background = '#f59e0b';
                    stateDot.style.boxShadow = '0 0 6px #f59e0b';
                    stateText.innerText = '🧘 BIJLI GURU • READY';
                    break;
            }
        }

        // Toggle Voice Recording (Speech to Text)
        function toggleRobotVoiceInput() {
            const micBtn = document.getElementById('robotMicBtn');
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                alert('Speech Recognition is not supported by this browser. Please use Chrome, Edge, or type your message.');
                return;
            }

            if (isRobotListening) {
                if (robotRecognition) robotRecognition.stop();
                return;
            }

            try {
                robotRecognition = new SpeechRecognition();
                robotRecognition.lang = 'hi-IN'; // Recognizes Hindi and Indian English seamlessly
                robotRecognition.interimResults = false;
                robotRecognition.continuous = false;

                robotRecognition.onstart = () => {
                    isRobotListening = true;
                    if (micBtn) micBtn.classList.add('recording');
                    updateRobotState('LISTENING');
                };

                robotRecognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    const input = document.getElementById('robotInputText');
                    if (input) input.value = transcript;
                    // Auto send spoken query to agent
                    setTimeout(() => {
                        handleRobotSendClick();
                    }, 400);
                };

                robotRecognition.onerror = (err) => {
                    console.warn('Speech recognition error:', err);
                    isRobotListening = false;
                    if (micBtn) micBtn.classList.remove('recording');
                    updateRobotState('IDLE');
                };

                robotRecognition.onend = () => {
                    isRobotListening = false;
                    if (micBtn) micBtn.classList.remove('recording');
                    if (currentRobotState === 'LISTENING') {
                        updateRobotState('IDLE');
                    }
                };

                robotRecognition.start();
            } catch (err) {
                console.error('Speech recognition exception:', err);
                isRobotListening = false;
                if (micBtn) micBtn.classList.remove('recording');
                updateRobotState('IDLE');
            }
        }

        // Text to Speech (Natural Voice Synthesis)
        function speakAgentText(text) {
            if (!robotSpeechEnabled || !window.speechSynthesis) return;

            // Stop any ongoing speech
            window.speechSynthesis.cancel();

            // Clean markdown syntax & emojis for clear speech audio
            const cleanText = text
                .replace(/[*#_`>]/g, '')
                .replace(/[\u{1F300}-\u{1F9FF}]/gu, '')
                .trim();

            if (!cleanText) return;

            const utterance = new SpeechSynthesisUtterance(cleanText);
            utterance.rate = 1.0;
            utterance.pitch = 1.02;

            // Try to match a pleasant Indian or Hindi voice if available
            const voices = window.speechSynthesis.getVoices();
            const preferredVoice = voices.find(v => v.lang.includes('hi') || v.lang.includes('en-IN')) || voices[0];
            if (preferredVoice) utterance.voice = preferredVoice;

            utterance.onstart = () => {
                updateRobotState('SPEAKING');
            };

            utterance.onend = () => {
                if (currentRobotState === 'EMERGENCY') {
                    updateRobotState('EMERGENCY');
                } else {
                    updateRobotState('IDLE');
                }
            };

            utterance.onerror = () => {
                updateRobotState('IDLE');
            };

            window.speechSynthesis.speak(utterance);
        }

        // Toggle Voice Output Mute
        function toggleSpeechOutput() {
            robotSpeechEnabled = !robotSpeechEnabled;
            const btn = document.getElementById('robotSpeechToggleBtn');
            if (btn) {
                btn.innerText = robotSpeechEnabled ? '🔊' : '🔇';
                btn.title = robotSpeechEnabled ? 'Voice Output ON' : 'Voice Output Muted';
            }
            if (!robotSpeechEnabled && window.speechSynthesis) {
                window.speechSynthesis.cancel();
                if (currentRobotState === 'SPEAKING') updateRobotState('IDLE');
            }
        }

        // Reset & Clear Chat
        function resetRobotChat() {
            robotConversationHistory = [];
            const chatBox = document.getElementById('robotChatMessages');
            if (chatBox) {
                chatBox.innerHTML = `
                    <div class="agent-msg-bubble">
                        <div class="agent-msg-avatar">
                            <img src="/images/sadhu_guru.jpg" alt="Bijli Guru">
                        </div>
                        <div class="agent-msg-text">
                            🌸 Pranam! Main hoon <strong>Bijli Guru (बिजली गुरु)</strong> — ElectroLKO ka aapka guide. 
                            Aapke ghar me kya electrical pareshani hai? Batayiye, main turant madad karta hoon!
                        </div>
                    </div>
                `;
            }
            updateRobotState('IDLE');
        }

        // Handle Quick Chip click
        function handleQuickChipClick(text) {
            const input = document.getElementById('robotInputText');
            if (input) input.value = text;
            handleRobotSendClick();
        }

        // Handle Enter key
        function handleRobotInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleRobotSendClick();
            }
        }

        // Send Message Handler
        async function handleRobotSendClick() {
            const input = document.getElementById('robotInputText');
            if (!input) return;
            const text = input.value.trim();
            if (!text) return;

            // Clear input
            input.value = '';

            // Add user bubble
            appendUserMessageBubble(text);
            scrollRobotChatToBottom();

            // Set thinking state
            updateRobotState('THINKING');

            // Collect active context
            const activeArea = (typeof currentAreaKey !== 'undefined' && lucknowAreas[currentAreaKey]) 
                ? lucknowAreas[currentAreaKey].name 
                : 'Gomti Nagar, Lucknow';

            const activeCoords = (typeof userHomeMarker !== 'undefined' && userHomeMarker)
                ? [userHomeMarker.getLatLng().lat, userHomeMarker.getLatLng().lng]
                : [26.8500, 80.9990];

            try {
                // Call Laravel AI Agent API
                const response = await fetch('/api/ai-agent/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        message: text,
                        conversation_history: robotConversationHistory,
                        user_location: {
                            area: activeArea,
                            lat: activeCoords[0],
                            lng: activeCoords[1]
                        }
                    })
                });

                if (!response.ok) {
                    throw new Error(`Server returned status ${response.status}`);
                }

                const result = await response.json();

                // Check tool used and reflect in UI
                if (result.tool_used) {
                    updateRobotState('USING TOOL', result.tool_used);
                }

                // Check emergency status
                if (result.state === 'EMERGENCY' || result.data?.emergency) {
                    updateRobotState('EMERGENCY');
                }

                // Update memory history
                if (result.history) {
                    robotConversationHistory = result.history;
                } else {
                    robotConversationHistory.push({ role: 'user', content: text });
                    robotConversationHistory.push({ role: 'assistant', content: result.message || '' });
                }

                // Render agent response in chat
                appendAgentResponseBubble(result);
                scrollRobotChatToBottom();

                // Speak response aloud
                if (result.message) {
                    speakAgentText(result.message);
                }

                // If not emergency and not speaking, return to IDLE after a moment
                if (result.state !== 'EMERGENCY' && !robotSpeechEnabled) {
                    setTimeout(() => updateRobotState('IDLE'), 1200);
                }

            } catch (err) {
                console.error('Agent chat error:', err);
                appendAgentResponseBubble({
                    message: "Sorry, I had trouble connecting to the server. Please check your internet or retry.",
                    state: 'IDLE'
                });
                updateRobotState('IDLE');
                scrollRobotChatToBottom();
            }
        }

        // Render User Message Bubble
        function appendUserMessageBubble(text) {
            const chatBox = document.getElementById('robotChatMessages');
            if (!chatBox) return;
            const bubble = document.createElement('div');
            bubble.className = 'user-msg-bubble';
            bubble.innerText = text;
            chatBox.appendChild(bubble);
        }

        // Render Agent Response Bubble with Rich Content
        function appendAgentResponseBubble(res) {
            const chatBox = document.getElementById('robotChatMessages');
            if (!chatBox) return;

            const bubbleWrap = document.createElement('div');
            bubbleWrap.className = 'agent-msg-bubble';

            let bubbleHtml = `
                <div class="agent-msg-avatar">
                    ${res.state === 'EMERGENCY' ? '⚠️' : '<img src="/images/sadhu_guru.jpg" alt="Bijli Guru">'}
                </div>
                <div class="agent-msg-text">
            `;

            // Emergency Warning Banner inside bubble
            if (res.state === 'EMERGENCY') {
                bubbleHtml += `
                    <div style="background: rgba(239,68,68,0.2); border: 1px solid #ef4444; border-radius: 8px; padding: 8px 10px; margin-bottom: 8px; color: #fca5a5; font-weight: 700;">
                        🚨 SAFETY WARNING: Do NOT touch exposed wires or sparking switches. Main power turn off karein agar safe ho!
                    </div>
                `;
            }

            // Main Text Message formatted
            let formattedMessage = (res.message || '')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code>$1</code>')
                .replace(/\n\n/g, '<br><br>')
                .replace(/\n/g, '<br>');
            bubbleHtml += `<div>${formattedMessage}</div>`;

            // If Electricians List is attached in response
            if (res.data && res.data.electricians && res.data.electricians.length > 0) {
                bubbleHtml += `<div style="margin-top: 10px; display: flex; flex-direction: column; gap: 6px;">`;
                res.data.electricians.forEach(pro => {
                    const safeName = (pro.name || 'Electrician').replace(/'/g, "\\'");
                    const safeArea = pro.area || 'Lucknow';
                    const rating = pro.rating || '4.8';
                    const dist = pro.distance_km ? `${pro.distance_km} km` : pro.distance || '';

                    bubbleHtml += `
                        <div class="robot-pro-card">
                            <div>
                                <div class="robot-pro-name">⚡ ${pro.name} <span style="color: #f59e0b; font-size: 11px;">⭐ ${rating}</span></div>
                                <div class="robot-pro-meta">📍 ${safeArea} ${dist ? '• ' + dist : ''} • ~₹${pro.hourly_rate || '199'}/hr</div>
                            </div>
                            <button type="button" class="btn-confirm-robot-booking" onclick="confirmBookingFromAgent('${safeName}')">
                                Book ${pro.name.split(' ')[0]}
                            </button>
                        </div>
                    `;
                });
                bubbleHtml += `</div>`;
            }

            // If Booking Confirmation Details are attached
            if (res.data && res.data.booking) {
                const b = res.data.booking;
                bubbleHtml += `
                    <div class="robot-booking-success-card">
                        <div style="font-weight: 800; font-size: 13px; margin-bottom: 4px;">✅ Booking Confirmed!</div>
                        <div><strong>Booking ID:</strong> ${b.booking_reference || b.id}</div>
                        <div><strong>Electrician:</strong> ${b.electrician_name || 'Assigned Pro'}</div>
                        <div><strong>Service:</strong> ${b.service_name || 'Electrical Repair'}</div>
                        <div><strong>Scheduled Time:</strong> ${b.scheduled_time || 'Within 30 mins'}</div>
                        <div style="margin-top: 4px; font-size: 11px; color: #cbd5e1;">Technician is dispatched to ${b.customer_address || 'your address in Lucknow'}.</div>
                    </div>
                `;
            }

            bubbleHtml += `</div>`;
            bubbleWrap.innerHTML = bubbleHtml;
            chatBox.appendChild(bubbleWrap);
        }

        // 1-Click Booking Confirmation Trigger
        function confirmBookingFromAgent(proName) {
            const input = document.getElementById('robotInputText');
            if (input) {
                input.value = `Yes, please book ${proName}`;
                handleRobotSendClick();
            }
        }

        // Scroll chat to bottom helper
        function scrollRobotChatToBottom() {
            const chatBox = document.getElementById('robotChatMessages');
            if (chatBox) {
                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 50);
            }
        }
    </script>
</body>
</html>
