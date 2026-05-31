<?php
// Get current page name to set active class in navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>International Admissions | Poltekkes Kemenkes Bengkulu</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #008080;
            /* Teal brand color */
            --primary-dark: #006666;
            --primary-light: #e6f2f2;
            --primary-rgb: 0, 128, 128;
            --secondary: #f4fbfb;
            --text-dark: #1a202c;
            --text-muted: #4a5568;
            --text-light: #718096;
            --white: #ffffff;
            --success: #38a169;
            --success-light: #f0fff4;
            --error: #e53e3e;
            --error-light: #fff5f5;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
            --transition-all: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 20px;
            --border-radius-xl: 30px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            color: var(--text-dark);
            background-color: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f7fafc;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 128, 128, 0.08);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 8%;
            transition: var(--transition-all);
        }

        .logo {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.5px;
        }

        .logo i {
            font-size: 1.6rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2.2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            transition: var(--transition-all);
            font-size: 0.95rem;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
        }

        /* Underline transition for active page */
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition-all);
            border-radius: 2px;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .btn-nav {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white !important;
            padding: 0.75rem 1.6rem;
            border-radius: var(--border-radius-xl);
            box-shadow: 0 4px 15px rgba(0, 128, 128, 0.15);
            font-weight: 700 !important;
        }

        .btn-nav::after {
            display: none !important;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 128, 128, 0.25);
            color: white !important;
        }

        /* Mobile Menu Hamburger Button */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-dark);
            cursor: pointer;
            z-index: 1001;
            transition: var(--transition-all);
            padding: 5px;
        }

        /* Mobile Navigation Overlay */
        .mobile-nav {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 400px;
            height: 100vh;
            background: var(--white);
            box-shadow: var(--shadow-xl);
            z-index: 999;
            display: flex;
            flex-direction: column;
            padding: 7rem 3rem 3rem 3rem;
            gap: 2rem;
            transition: cubic-bezier(0.77, 0, 0.175, 1) 0.5s;
        }

        .mobile-nav.open {
            right: 0;
        }

        .mobile-nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1.25rem;
            transition: var(--transition-all);
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .mobile-nav a.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .mobile-nav .btn-nav-mobile {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius-md);
            text-align: center;
            font-weight: 700;
            margin-top: 1.5rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 128, 128, 0.15);
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(5px);
            z-index: 998;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition-all);
        }

        .mobile-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Page Layout Adjustment for Fixed Header */
        main {
            flex: 1 0 auto;
            margin-top: 90px;
            /* Offset for the fixed navigation height */
        }

        /* Custom Animations */
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

        /* Common Page Header Utility */
        .page-hero {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--white) 100%);
            padding: 5rem 8% 4rem 8%;
            text-align: center;
            border-bottom: 1px solid rgba(0, 128, 128, 0.05);
        }

        .page-hero h1 {
            font-size: 2.8rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-weight: 800;
            letter-spacing: -1px;
            animation: fadeInUp 0.6s ease-out;
        }

        .page-hero p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive Layouts */
        @media (max-width: 968px) {
            nav {
                padding: 1.2rem 5%;
            }

            .nav-links {
                display: none;
            }

            .mobile-toggle {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation Header -->
    <nav>
        <a href="index.php" class="logo">
            <img src="logo-ptk.png" alt="Poltekkes Kemenkes Bengkulu Logo" width="100%" height="80">
        </a>

        <!-- Desktop Nav Links -->
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="requirements.php"
                    class="<?php echo $current_page == 'requirements.php' ? 'active' : ''; ?>">Requirements</a></li>
            <li><a href="facilities.php" class="<?php echo $current_page == 'facilities.php' ? 'active' : ''; ?>">Campus
                    Facilities</a></li>
            <li><a href="cost-of-living.php"
                    class="<?php echo $current_page == 'cost-of-living.php' ? 'active' : ''; ?>">Cost of Living</a></li>
            <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
            </li>
            <li><a href="guidelines.php" class="<?php echo $current_page == 'guidelines.php' ? 'active' : ''; ?>">Guidelines</a></li>
            <li><a href="register.php"
                    class="btn-nav <?php echo $current_page == 'register.php' ? 'active' : ''; ?>">Apply Now</a></li>
        </ul>

        <!-- Mobile Hamburg Button -->
        <button class="mobile-toggle" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </nav>

    <!-- Mobile Drawer Nav -->
    <div class="mobile-overlay"></div>
    <div class="mobile-nav">
        <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
        <a href="requirements.php"
            class="<?php echo $current_page == 'requirements.php' ? 'active' : ''; ?>">Requirements</a>
        <a href="facilities.php" class="<?php echo $current_page == 'facilities.php' ? 'active' : ''; ?>">Campus
            Facilities</a>
        <a href="cost-of-living.php" class="<?php echo $current_page == 'cost-of-living.php' ? 'active' : ''; ?>">Cost
            of Living</a>
        <a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
        <a href="guidelines.php" class="<?php echo $current_page == 'guidelines.php' ? 'active' : ''; ?>">Guidelines</a>
        <a href="register.php" class="btn-nav-mobile">Apply Now</a>
    </div>

    <!-- Main Container -->
    <main>