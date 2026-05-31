<?php
require_once 'header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>Admission Guidelines</h1>
    <p>Open the official guide directly in your browser with our built-in PDF viewer.</p>
</div>

<section class="guidelines-section">
    <div class="guidelines-container">
        <div class="section-header">
            <h2>Official Admission Guide</h2>
            <p>Read the full foreign student admission guidelines from Poltekkes Kemenkes Bengkulu.</p>
        </div>

        <div class="pdf-viewer-wrap">
            <iframe src="GUIDLINES%20selection%20of%20foreign%20student%20admissions%20Poltekkes%20Bengkulu%202026.pdf" frameborder="0" allowfullscreen></iframe>
        </div>

        <div class="guide-note">
            <p>If the PDF does not load, you can download it directly:</p>
            <a href="GUIDLINES%20selection%20of%20foreign%20student%20admissions%20Poltekkes%20Bengkulu%202026.pdf" class="btn-primary" download>Download Guidelines PDF</a>
        </div>
    </div>
</section>

<style>
    .guidelines-section {
        padding: 5rem 8%;
        background-color: var(--secondary);
    }

    .guidelines-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .pdf-viewer-wrap {
        width: 100%;
        min-height: 820px;
        background: #fff;
        border: 1px solid rgba(0, 128, 128, 0.12);
        border-radius: var(--border-radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .pdf-viewer-wrap iframe {
        width: 100%;
        height: 820px;
        border: none;
    }

    .guide-note {
        text-align: center;
        margin-top: 2rem;
    }

    .guide-note p {
        margin-bottom: 1rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    .guide-note .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .guidelines-section {
            padding: 3rem 5%;
        }

        .pdf-viewer-wrap {
            min-height: 640px;
        }
    }
</style>