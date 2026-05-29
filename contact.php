<?php
require_once 'header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>Contact & Support</h1>
    <p>Get in touch with our international desk team for inquiries about admissions, programs, and visa assistance.</p>
</div>

<!-- Contact Content Section -->
<section class="contact-page-section">
    <div class="contact-container">
        <div class="contact-grid-layout">
            <!-- Left Info Panel -->
            <div class="contact-info-panel">
                <h2>Direct Channels</h2>
                <p>Feel free to reach out via email or direct chat. Our desk is open Monday to Friday, 08:00 to 16:00 (GMT+7).</p>

                <div class="info-details">
                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <h4>Campus Address</h4>
                            <p>Jl. Indragiri No.3, Padang Harapan, Kota Bengkulu, Bengkulu 38225, Indonesia</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <h4>Official Email</h4>
                            <p><a href="mailto:international.admission@poltekkesbengkulu.ac.id">international.admission@poltekkesbengkulu.ac.id</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fa-brands fa-whatsapp"></i>
                        <div>
                            <h4>WhatsApp International Desk</h4>
                            <p><a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-clock"></i>
                        <div>
                            <h4>Office Hours</h4>
                            <p>Monday – Friday: 08:00 AM – 04:00 PM (GMT+7)</p>
                            <p>Saturday – Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Contact Form Panel -->
            <div class="contact-form-panel">
                <h2>Send an Inquiry</h2>
                <p>Have a quick question? Fill out the short form below and we will get back to you within 24 hours.</p>
                
                <form id="inquiryForm" onsubmit="event.preventDefault(); handleInquirySubmit();">
                    <div class="form-group">
                        <label for="inquiryName">Full Name</label>
                        <input type="text" id="inquiryName" class="form-control" placeholder="Your Name" required>
                    </div>

                    <div class="form-group">
                        <label for="inquiryEmail">Email Address</label>
                        <input type="email" id="inquiryEmail" class="form-control" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="inquiryMessage">Your Message</label>
                        <textarea id="inquiryMessage" rows="4" class="form-control" placeholder="What would you like to know?" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit btn-inquiry">Send Message <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i></button>
                </form>
                
                <div id="inquirySuccess" class="alert alert-success" style="display: none; margin-top: 1.5rem; margin-bottom: 0;">
                    <i class="fa-solid fa-circle-check" style="font-size: 1.25rem;"></i>
                    <div>Thank you! Your message has been sent successfully. We will contact you soon.</div>
                </div>
            </div>
        </div>

        <!-- Google Map Section -->
        <div class="map-wrapper">
            <h2>Find Us on Map</h2>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3980.957640656041!2d102.28589781165243!3d-3.818442842918884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e36b0410ffdf15b%3A0xe10839e559e37704!2sPoltekkes%20Kemenkes%20Bengkulu!5e0!3m2!1sen!2sid!4v1716954200000!5m2!1sen!2sid" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<style>
    /* Contact Page Custom CSS */
    .contact-page-section {
        padding: 5rem 8%;
        background-color: var(--white);
    }

    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-grid-layout {
        display: grid;
        grid-template-columns: 1.2fr 1.5fr;
        gap: 5rem;
        margin-bottom: 5rem;
    }

    .contact-info-panel h2,
    .contact-form-panel h2,
    .map-wrapper h2 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: var(--text-dark);
        letter-spacing: -0.5px;
    }

    .contact-info-panel > p,
    .contact-form-panel > p {
        color: var(--text-muted);
        font-size: 1rem;
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }

    .info-details {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .info-item i {
        background-color: var(--primary-light);
        color: var(--primary);
        width: 44px;
        height: 44px;
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .info-item h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.35rem;
    }

    .info-item p,
    .info-item a {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.5;
        text-decoration: none;
        transition: var(--transition-all);
    }

    .info-item a:hover {
        color: var(--primary);
    }

    .contact-form-panel {
        background: var(--secondary);
        padding: 3.5rem 3rem;
        border-radius: var(--border-radius-lg);
        border: 1px solid rgba(0, 128, 128, 0.05);
        box-shadow: var(--shadow-md);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-dark);
    }

    .form-control {
        width: 100%;
        padding: 0.85rem 1.1rem;
        border: 1px solid #cbd5e0;
        border-radius: var(--border-radius-sm);
        font-size: 0.95rem;
        background-color: var(--white);
        color: var(--text-dark);
        transition: var(--transition-all);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.12);
    }

    .btn-inquiry {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 700;
        border-radius: var(--border-radius-sm);
    }

    /* Map Box */
    .map-wrapper {
        border-top: 1px solid #edf2f7;
        padding-top: 4rem;
    }

    .map-wrapper h2 {
        margin-bottom: 2rem;
    }

    .map-container {
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(0, 0, 0, 0.08);
        height: 450px;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    @media (max-width: 968px) {
        .contact-grid-layout {
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        .contact-form-panel {
            padding: 2.5rem;
        }
    }
</style>

<!-- Handling Inquiry Form Submission Demo -->
<script>
    function handleInquirySubmit() {
        const form = document.getElementById('inquiryForm');
        const submitBtn = document.querySelector('.btn-inquiry');
        const successBox = document.getElementById('inquirySuccess');
        
        // Mock loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Sending message... <i class="fa-solid fa-spinner fa-spin" style="margin-left: 8px;"></i>';
        submitBtn.style.opacity = '0.8';

        setTimeout(() => {
            // Hide loading and show success box
            submitBtn.style.display = 'none';
            form.style.display = 'none';
            successBox.style.display = 'flex';
        }, 1200);
    }
</script>

<?php
require_once 'footer.php';
?>
