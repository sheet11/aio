<?php
require_once 'header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>Campus Facilities</h1>
    <p>Explore our premium state-of-the-art infrastructure designed to support a high-quality medical education experience.</p>
</div>

<!-- Facilities Grid -->
<section class="facilities-page-section">
    <div class="facilities-container">
        <div class="facilities-grid">
            <!-- 1. Training & Guidance Center -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-hotel"></i></div>
                    <h3>Training & Guidance Center</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>GTC Ballroom:</strong> A premium, multi-purpose hall with a 250-student capacity, fully equipped with central AC and interactive Videotron display.</li>
                        <li><strong>GTC Guest Rooms:</strong> 54 comfortable rooms (Superior, Deluxe, and Family types) complete with Wi-Fi, AC, TV, and hot water.</li>
                        <li><strong>Migrant Guidance Center:</strong> A specialized prep hub designed to groom graduates for international recruitment and global career deployment.</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Auditoriums -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-people-roof"></i></div>
                    <h3>Grand Auditoriums</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>Campus A Auditorium:</strong> A massive hall hosting up to 1,000 participants, featuring centralized AC, high-res Videotron screens, and VIP waiting lounges.</li>
                        <li><strong>Campus B Auditorium:</strong> A modern auditorium with a 600-participant capacity, fully air-conditioned for academic assemblies and guest lectures.</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Medical Practices & Testing -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-hospital-user"></i></div>
                    <h3>Clinical Practice & Testing</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>Mini Hospital:</strong> A realistic clinical mock ward representing actual hospital settings for students' hands-on medical simulation.</li>
                        <li><strong>OSCE Center:</strong> Professional 12-station standard clinical setup with real-time video monitoring and briefing stations.</li>
                        <li><strong>CBT Center:</strong> LPUK-NAKES certified computer testing rooms (120 units at Campus A, 50 units at Campus B Curup).</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Specialized Laboratories -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-flask-vial"></i></div>
                    <h3>Integrated Health Labs</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>Departmental Labs:</strong> Dedicated labs for Nursing, Midwifery, Nutrition, Medical Lab Technology (MLT), Environmental Health, and Health Promotion.</li>
                        <li><strong>Language Laboratory:</strong> 40-unit multimedia PC workstations equipped with digital audio systems for language training.</li>
                    </ul>
                </div>
            </div>

            <!-- 5. Public Health & Training Services -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-clinic-medical"></i></div>
                    <h3>Health Services & UPK SDMK</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>Hygea Pratama Clinic:</strong> Fully accredited (Paripurna) healthcare clinic providing general medicine, dental, and advanced lab tests for the community.</li>
                        <li><strong>UPK SDMK Center:</strong> Certified training center conducting accredited workshops (Wound Care, BTCLS, teaching methodologies).</li>
                    </ul>
                </div>
            </div>

            <!-- 6. General Campus Infrastructure -->
            <div class="facility-card">
                <div class="facility-header">
                    <div class="facility-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <h3>Amenities & Support</h3>
                </div>
                <div class="facility-body">
                    <ul>
                        <li><strong>Integrated Library:</strong> Grade "A" accredited library cataloging books, clinical journals, and academic thesis collections with quiet reading zones.</li>
                        <li><strong>Mosque & Theater:</strong> Masjid Tarbiyatus Shihah (air-conditioned mosque) and an 80-seat multimedia theater room.</li>
                        <li><strong>Sports Infrastructure:</strong> Outdoor and indoor facilities supporting Futsal, Basketball, Volleyball, Badminton, and Table Tennis.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Facilities Page Specific Styling */
    .facilities-page-section {
        padding: 5rem 8%;
        background-color: var(--secondary);
    }

    .facilities-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
        gap: 2.5rem;
    }

    .facility-card {
        background: var(--white);
        border-radius: var(--border-radius-md);
        padding: 3rem 2.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: var(--transition-all);
        display: flex;
        flex-direction: column;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-light);
    }

    .facility-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 1rem;
    }

    .facility-icon {
        width: 50px;
        height: 50px;
        background-color: var(--primary-light);
        color: var(--primary);
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: var(--transition-all);
    }

    .facility-card:hover .facility-icon {
        background-color: var(--primary);
        color: var(--white);
    }

    .facility-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .facility-body {
        flex-grow: 1;
    }

    .facility-body ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .facility-body ul li {
        position: relative;
        padding-left: 24px;
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .facility-body ul li::before {
        content: "\f058";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 2px;
        color: var(--primary);
        font-size: 0.95rem;
    }

    .facility-body ul li strong {
        color: var(--text-dark);
    }

    @media (max-width: 768px) {
        .facilities-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
require_once 'footer.php';
?>
