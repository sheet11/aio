    </main>

    <!-- Footer CSS Styles -->
    <style>
        footer {
            background-color: #0f172a;
            color: #f8fafc;
            padding: 4.5rem 8% 2rem 8%;
            font-size: 0.95rem;
            margin-top: auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1.5fr;
            gap: 4rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 3rem;
            margin-bottom: 2rem;
        }

        .footer-brand h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-brand h3 i {
            color: var(--primary);
        }

        .footer-brand p {
            color: #94a3b8;
            max-width: 320px;
            line-height: 1.6;
        }

        .footer-links h4,
        .footer-contact h4 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 1.25rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-links h4::after,
        .footer-contact h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        .footer-links ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .footer-links ul a {
            color: #94a3b8;
            text-decoration: none;
            transition: var(--transition-all);
            display: inline-block;
        }

        .footer-links ul a:hover {
            color: var(--primary-light);
            transform: translateX(3px);
        }

        .footer-contact ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-contact ul li {
            color: #94a3b8;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }

        .footer-contact ul li i {
            color: var(--primary);
            font-size: 1.1rem;
            margin-top: 3px;
        }

        .footer-contact ul a {
            color: #94a3b8;
            text-decoration: none;
            transition: var(--transition-all);
        }

        .footer-contact ul a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            color: #64748b;
            font-size: 0.85rem;
        }

        .footer-bottom a {
            color: #64748b;
            text-decoration: none;
            transition: var(--transition-all);
        }

        .footer-bottom a:hover {
            color: #94a3b8;
        }

        @media (max-width: 968px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 3rem;
            }
        }

        @media (max-width: 640px) {
            footer {
                padding: 3rem 6% 1.5rem 6%;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <!-- Footer Section -->
    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <h3><i class="fa-solid fa-graduation-cap"></i> Poltekkes Bengkulu</h3>
                <p>Leading healthcare academy in Bengkulu, Indonesia, certified to produce professional, international-ready medical workers.</p>
            </div>

            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="requirements.php">Requirements</a></li>
                    <li><a href="facilities.php">Campus Facilities</a></li>
                    <li><a href="guidelines.php">Guidelines</a></li>
                    <li><a href="contact.php">Contact & Support</a></li>
                    <li><a href="register.php">Apply Now</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Contact Details</h4>
                <ul>
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Jl. Indragiri No.3, Padang Harapan, Kota Bengkulu, Indonesia</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <a href="mailto:international.admission@poltekkesbengkulu.ac.id">international.admission@poltekkesbengkulu.ac.id</a>
                    </li>
                    <li>
                        <i class="fa-brands fa-whatsapp"></i>
                        <a href="https://wa.me/6281234567890" target="_blank">International Desk: +62 812-3456-7890</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; <?php echo date("Y"); ?> Poltekkes Kemenkes Bengkulu. All Rights Reserved.</div>
            <div>
                <a href="#">Privacy Policy</a> &middot;
                <a href="#">Terms & Conditions</a>
            </div>
        </div>
    </footer>

    <!-- Hamburger Navigation Drawer Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileToggle = document.querySelector('.mobile-toggle');
            const mobileNav = document.querySelector('.mobile-nav');
            const mobileOverlay = document.querySelector('.mobile-overlay');
            const menuIcon = mobileToggle.querySelector('i');

            function toggleMenu() {
                const isOpen = mobileNav.classList.toggle('open');
                mobileOverlay.classList.toggle('open');

                // Toggle between bar and xmark icon
                if (isOpen) {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-xmark');
                    document.body.style.overflow = 'hidden'; // Disable page scrolling
                } else {
                    menuIcon.classList.remove('fa-xmark');
                    menuIcon.classList.add('fa-bars');
                    document.body.style.overflow = ''; // Restore page scrolling
                }
            }

            mobileToggle.addEventListener('click', toggleMenu);
            mobileOverlay.addEventListener('click', toggleMenu);

            // Also close menu when a mobile nav link is clicked
            mobileNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (mobileNav.classList.contains('open')) {
                        toggleMenu();
                    }
                });
            });
        });
    </script>
    </body>

    </html>