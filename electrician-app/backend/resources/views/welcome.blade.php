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
            padding: 64px 0 48px;
            background: radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.08) 0%, transparent 60%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 48px;
            align-items: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 48px;
            line-height: 1.15;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1.2px;
            margin-bottom: 18px;
        }

        .hero-title span {
            color: var(--accent);
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 32px;
            max-width: 540px;
        }

        /* Search Card in Hero */
        .search-box-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 24px;
        }

        .search-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .input-group label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            transition: border-color 0.2s ease;
        }

        .input-wrapper:focus-within {
            border-color: var(--accent);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input-wrapper svg {
            color: var(--text-muted);
            flex-shrink: 0;
            width: 18px;
            height: 18px;
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            font-family: inherit;
        }

        .hero-trust-badges {
            display: flex;
            align-items: center;
            gap: 24px;
            padding-top: 12px;
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
            width: 16px;
            height: 16px;
        }

        /* Hero Graphic Illustration */
        .hero-visual {
            position: relative;
        }

        .visual-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: var(--radius-lg);
            padding: 36px 30px;
            color: #ffffff;
            box-shadow: var(--shadow-lg), 0 20px 40px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }

        .visual-card::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .visual-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            padding: 6px 12px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .visual-header {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .visual-sub {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 28px;
        }

        .quick-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .metric-box h4 {
            font-size: 22px;
            font-weight: 800;
            color: #f59e0b;
        }

        .metric-box p {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Floating notification on hero */
        .floating-notification {
            position: absolute;
            bottom: -20px;
            left: -20px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-lg);
            color: var(--text-dark);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .floating-icon {
            width: 36px;
            height: 36px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 36px;
            }
            .map-layout {
                grid-template-columns: 1fr;
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
        }

        @media (max-width: 640px) {
            .nav-links {
                display: none;
            }
            .hero-title {
                font-size: 34px;
            }
            .search-form-row {
                grid-template-columns: 1fr;
            }
            .services-grid {
                grid-template-columns: 1fr;
            }
            .steps-grid {
                grid-template-columns: 1fr;
            }
            .emergency-content {
                flex-direction: column;
                text-align: center;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .map-wrapper {
                height: 350px;
            }
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
                <span>ElectroLKO</span>
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
                <a href="#nearby-map" class="btn btn-primary">Find Electrician</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <div class="hero-tag">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                    Certified & Background-Checked Electricians
                </div>

                <h1 class="hero-title">
                    Find a trusted <span>electrician</span> near you
                </h1>

                <p class="hero-subtitle">
                    Get immediate help for short circuits, fan repairs, socket replacements, and full-home wiring with verified local pros.
                </p>

                <!-- Location & Service Search Box -->
                <div class="search-box-card">
                    <form id="heroSearchForm" onsubmit="event.preventDefault(); handleHeroSearch();">
                        <div class="search-form-row">
                            <div class="input-group">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <label for="locationInput" style="margin: 0;">Where You Live (Your Home Area)</label>
                                    <button type="button" onclick="detectBrowserLocationAndArea(true)" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px;" title="Detect your location">
                                        <span>📍 Detect My Location</span>
                                    </button>
                                </div>
                                <div class="input-wrapper" style="position: relative;">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <input type="text" id="locationInput" placeholder="Enter your area in Lucknow (e.g. Gomti Nagar, Aliganj)..." value="Gomti Nagar, Lucknow">
                                    <button type="button" onclick="searchLocationFromInput()" class="btn-detect-loc" title="Set your home location and find nearest electricians">
                                        <span>Set Home 🏠</span>
                                    </button>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="serviceSelect">Needed Service</label>
                                <div class="input-wrapper">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <select id="serviceSelect">
                                        <option value="all">All Electrical Services</option>
                                        <option value="fan">Fan Repair & Installation</option>
                                        <option value="socket">Switch & Socket Fix</option>
                                        <option value="lighting">Light Installation</option>
                                        <option value="wiring">Full House Wiring</option>
                                        <option value="appliance">Appliance Hookup</option>
                                        <option value="emergency">Emergency Short Circuit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group" style="justify-content: flex-end;">
                                <label style="visibility: hidden;">Search</label>
                                <button type="submit" class="btn btn-primary" style="height: 44px;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Find Electrician
                                </button>
                            </div>
                        </div>
                    </form>
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

            <!-- Visual Card Graphic -->
            <div class="hero-visual">
                <div class="visual-card">
                    <div class="visual-badge">
                        <span>⚡ 24/7 Rapid Response</span>
                    </div>
                    <h3 class="visual-header">Live Technician Dispatch</h3>
                    <p class="visual-sub">Real-time GPS dispatch coordinates the nearest certified electrician directly to your doorstep.</p>

                    <div style="background: rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 13px; font-weight: 600; color: #cbd5e1;">Available In Your Area</span>
                            <span style="font-size: 12px; color: #10b981; font-weight: 700;">● 4 Pros Online</span>
                        </div>
                        <div style="font-size: 12px; color: #94a3b8;">Average arrival time: <strong style="color: #ffffff;">18 minutes</strong></div>
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

                <!-- Floating Live Badge -->
                <div class="floating-notification">
                    <div class="floating-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 700;">Rajesh S. Completed Job</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Switchboard Repair • 8 mins ago</div>
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

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            // Fetch directly from browser coordinates on page load
            detectBrowserLocationAndArea(false);
        });
    </script>
</body>
</html>
