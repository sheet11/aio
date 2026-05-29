<?php
require_once 'header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>Admission Requirements</h1>
    <p>Please check the criteria and prepare your files carefully before submitting your online application.</p>
</div>

<!-- Main Requirements Section -->
<section class="requirements-section">
    <div class="requirements-container">
        <div class="grid-3">
            <!-- Academic Criteria -->
            <div class="req-card">
                <div class="req-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <h3>Academic Criteria</h3>
                <ul>
                    <li>High school graduate certificate (SMA/MA/SMK equivalent) or secondary education degree.</li>
                    <li>Minimum GPA or final grade equivalent to 3.00 out of 4.00 scale.</li>
                    <li>Strong foundational knowledge in Natural Sciences (Biology, Chemistry).</li>
                </ul>
            </div>

            <!-- Documents Needed -->
            <div class="req-card">
                <div class="req-icon"><i class="fa-solid fa-passport"></i></div>
                <h3>Documents Needed</h3>
                <ul>
                    <li>Scanned copy of valid passport (minimum 18 months remaining validity).</li>
                    <li>Recent formal passport-size photograph (red background color).</li>
                    <li>Official health certificate & active proof of global health insurance.</li>
                </ul>
            </div>

            <!-- Language Mastery -->
            <div class="req-card">
                <div class="req-icon"><i class="fa-solid fa-language"></i></div>
                <h3>Language Mastery</h3>
                <ul>
                    <li>English Proficiency Certificate: TOEFL score min. 500 or IELTS score min. 5.5.</li>
                    <li>Willingness to enroll in the Basic Indonesian Language Preparatory Course.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Application Workflow -->
<section class="workflow-section">
    <div class="requirements-container">
        <div class="section-header">
            <h2>Application Workflow</h2>
            <p>Our simple 4-step process to get you admitted to Poltekkes Kemenkes Bengkulu.</p>
        </div>
        <div class="workflow-timeline">
            <!-- Step 1 -->
            <div class="timeline-item">
                <div class="timeline-number">1</div>
                <div class="timeline-content">
                    <h4>Online Application</h4>
                    <p>Fill out the application form with your personal details, academic history, and Statement of Purpose (SOP).</p>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="timeline-item">
                <div class="timeline-number">2</div>
                <div class="timeline-content">
                    <h4>Document Review</h4>
                    <p>Our admissions committee will verify your documents, high school transcripts, GPA, and test scores.</p>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="timeline-item">
                <div class="timeline-number">3</div>
                <div class="timeline-content">
                    <h4>Interview & Assessment</h4>
                    <p>Shortlisted candidates will be invited to a digital interview to assess communication and academic alignment.</p>
                </div>
            </div>
            <!-- Step 4 -->
            <div class="timeline-item">
                <div class="timeline-number">4</div>
                <div class="timeline-content">
                    <h4>Acceptance & VISA</h4>
                    <p>Successful applicants receive a Letter of Acceptance (LoA) alongside administrative guidance for VISA setup.</p>
                </div>
            </div>
        </div>
        <div class="workflow-cta">
            <p>Ready to start? Fill out the registration form in just a few minutes.</p>
            <a href="register.php" class="btn-primary">Apply Online Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<style>
    /* Requirements Page Custom CSS */
    .requirements-section {
        padding: 5rem 8%;
        background-color: var(--white);
    }

    .requirements-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
    }

    .req-card {
        background: var(--white);
        padding: 3.5rem 2.5rem;
        border-radius: var(--border-radius-md);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-all);
        position: relative;
        overflow: hidden;
    }

    .req-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background-color: transparent;
        transition: var(--transition-all);
    }

    .req-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-light);
    }

    .req-card:hover::before {
        background-color: var(--primary);
    }

    .req-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-light);
        border-radius: var(--border-radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        transition: var(--transition-all);
    }

    .req-card:hover .req-icon {
        background: var(--primary);
        color: var(--white);
    }

    .req-card h3 {
        margin-bottom: 1.25rem;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .req-card ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .req-card ul li {
        position: relative;
        padding-left: 24px;
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .req-card ul li::before {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 2px;
        color: var(--primary);
        font-size: 0.85rem;
    }

    /* Workflow Section */
    .workflow-section {
        background-color: var(--secondary);
        padding: 6rem 8%;
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-header h2 {
        font-size: 2.25rem;
        color: var(--text-dark);
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .section-header p {
        color: var(--text-light);
        font-size: 1.05rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .workflow-timeline {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        position: relative;
        margin-bottom: 4rem;
    }

    /* Horizontal line running across the timeline on desktop */
    .workflow-timeline::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 50px;
        right: 50px;
        height: 2px;
        background-color: var(--primary-light);
        z-index: 1;
    }

    .timeline-item {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .timeline-number {
        width: 50px;
        height: 50px;
        background: var(--white);
        border: 3px solid var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--primary);
        font-size: 1.2rem;
        margin: 0 auto 1.5rem auto;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-all);
    }

    .timeline-item:hover .timeline-number {
        background: var(--primary);
        color: var(--white);
        transform: scale(1.1);
    }

    .timeline-content h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .timeline-content p {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.6;
        padding: 0 10px;
    }

    .workflow-cta {
        text-align: center;
        background: var(--white);
        border: 1px solid rgba(0, 128, 128, 0.08);
        border-radius: var(--border-radius-md);
        padding: 3rem 2rem;
        box-shadow: var(--shadow-md);
    }

    .workflow-cta p {
        font-size: 1.1rem;
        color: var(--text-dark);
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 968px) {
        .workflow-timeline {
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        .workflow-timeline::before {
            display: none; /* Hide line on mobile */
        }

        .timeline-item {
            display: flex;
            text-align: left;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .timeline-number {
            margin: 0;
            flex-shrink: 0;
        }

        .timeline-content p {
            padding: 0;
        }
    }
</style>

<?php
require_once 'footer.php';
?>
