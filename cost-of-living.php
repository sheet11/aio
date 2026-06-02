<?php require_once 'header.php'; ?>

<!-- Page Hero -->
<div class="page-hero col-hero">
    <span class="col-badge"><i class="fa-solid fa-earth-asia"></i> Cost of Living Guide</span>
    <h1>Living in Bengkulu vs. Other Cities</h1>
    <p>Discover how affordable student life in Bengkulu is compared to major cities across Asia and beyond — all prices
        in <strong>USD per month</strong>.</p>
</div>

<!-- Exchange Rate Note -->
<div class="exchange-banner">
    <div class="exchange-inner">
        <i class="fa-solid fa-circle-info"></i>
        <span>Reference exchange rate: <strong>1 USD ≈ IDR 16,000</strong>. Prices are approximate estimates for a
            student lifestyle.</span>
    </div>
</div>

<!-- =============================================
     Comparison Table Section
============================================= -->
<section class="col-section">
    <div class="col-container">

        <div class="col-intro">
            <h2>Monthly Cost Breakdown</h2>
            <p>A side-by-side comparison of typical monthly student expenses across cities. Bengkulu consistently offers
                one of the most affordable student environments in the region.</p>
        </div>

        <!-- City tabs -->
        <div class="city-tabs" id="cityTabs">
            <button class="tab-btn active" data-tab="bengkulu">🇮🇩 Bengkulu</button>
            <button class="tab-btn" data-tab="jakarta">🇮🇩 Jakarta</button>
            <button class="tab-btn" data-tab="kualalumpur">🇲🇾 Kuala Lumpur</button>
            <button class="tab-btn" data-tab="bangkok">🇹🇭 Bangkok</button>
            <button class="tab-btn" data-tab="singapore">🇸🇬 Singapore</button>
        </div>

        <!-- Comparison Table -->
        <div class="col-table-wrap">
            <table class="col-table">
                <thead>
                    <tr>
                        <th>Expense Category</th>
                        <th class="city-col active" data-city="bengkulu">🇮🇩 Bengkulu</th>
                        <th class="city-col" data-city="jakarta">🇮🇩 Jakarta</th>
                        <th class="city-col" data-city="kualalumpur">🇲🇾 KL</th>
                        <th class="city-col" data-city="bangkok">🇹🇭 Bangkok</th>
                        <th class="city-col" data-city="singapore">🇸🇬 Singapore</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fa-solid fa-house"></i> Rent (shared room)</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$40 – $80</td>
                        <td class="city-col" data-city="jakarta">$120 – $250</td>
                        <td class="city-col" data-city="kualalumpur">$130 – $280</td>
                        <td class="city-col" data-city="bangkok">$150 – $320</td>
                        <td class="city-col" data-city="singapore">$500 – $900</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-utensils"></i> Food (3 meals/day)</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$30 – $60</td>
                        <td class="city-col" data-city="jakarta">$120 – $200</td>
                        <td class="city-col" data-city="kualalumpur">$130 – $220</td>
                        <td class="city-col" data-city="bangkok">$120 – $200</td>
                        <td class="city-col" data-city="singapore">$350 – $600</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-bus"></i> Transportation</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$10 – $20</td>
                        <td class="city-col" data-city="jakarta">$30 – $60</td>
                        <td class="city-col" data-city="kualalumpur">$30 – $55</td>
                        <td class="city-col" data-city="bangkok">$25 – $50</td>
                        <td class="city-col" data-city="singapore">$80 – $130</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-wifi"></i> Internet & Phone</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$5 – $10</td>
                        <td class="city-col" data-city="jakarta">$10 – $20</td>
                        <td class="city-col" data-city="kualalumpur">$12 – $22</td>
                        <td class="city-col" data-city="bangkok">$10 – $18</td>
                        <td class="city-col" data-city="singapore">$25 – $45</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-book"></i> Books & Stationery</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$5 – $10</td>
                        <td class="city-col" data-city="jakarta">$15 – $30</td>
                        <td class="city-col" data-city="kualalumpur">$15 – $30</td>
                        <td class="city-col" data-city="bangkok">$12 – $25</td>
                        <td class="city-col" data-city="singapore">$30 – $60</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-shirt"></i> Clothing & Personal</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$10 – $20</td>
                        <td class="city-col" data-city="jakarta">$25 – $50</td>
                        <td class="city-col" data-city="kualalumpur">$25 – $50</td>
                        <td class="city-col" data-city="bangkok">$20 – $45</td>
                        <td class="city-col" data-city="singapore">$60 – $120</td>
                    </tr>
                    <tr>
                        <td><i class="fa-solid fa-film"></i> Entertainment & Leisure</td>
                        <td class="city-col active highlight-best" data-city="bengkulu">$10 – $20</td>
                        <td class="city-col" data-city="jakarta">$30 – $60</td>
                        <td class="city-col" data-city="kualalumpur">$30 – $60</td>
                        <td class="city-col" data-city="bangkok">$30 – $60</td>
                        <td class="city-col" data-city="singapore">$80 – $150</td>
                    </tr>
                    <tr class="total-row">
                        <td><strong><i class="fa-solid fa-calculator"></i> Estimated Total / Month</strong></td>
                        <td class="city-col active highlight-best" data-city="bengkulu"><strong>$100 – $140</strong>
                        </td>
                        <td class="city-col" data-city="jakarta"><strong>$350 – $670</strong></td>
                        <td class="city-col" data-city="kualalumpur"><strong>$372 – $717</strong></td>
                        <td class="city-col" data-city="bangkok"><strong>$367 – $718</strong></td>
                        <td class="city-col" data-city="singapore"><strong>$1,125 – $2,005</strong></td>
                    </tr>
                    <tr class="tuition-row">
                        <td><i class="fa-solid fa-graduation-cap"></i> Tuition Fee / Semester</td>
                        <td class="city-col active highlight-best" data-city="bengkulu"><span class="free-badge">FREE
                                with Scholarship</span></td>
                        <td class="city-col" data-city="jakarta">$300 – $800</td>
                        <td class="city-col" data-city="kualalumpur">$600 – $2,000</td>
                        <td class="city-col" data-city="bangkok">$500 – $1,500</td>
                        <td class="city-col" data-city="singapore">$3,000 – $8,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Savings highlight -->
        <div class="savings-banner">
            <i class="fa-solid fa-piggy-bank savings-icon"></i>
            <div>
                <h3>Save up to <span>$1,800+</span> every month</h3>
                <p>Compared to studying in Singapore, living in Bengkulu on our scholarship saves you an estimated
                    <strong>$1,800 – $2,200 per month</strong> in combined tuition and living costs.</p>
            </div>
        </div>

    </div>
</section>

<!-- =============================================
     City Profile Cards
============================================= -->
<section class="city-profiles-section">
    <div class="col-container">
        <div class="col-intro">
            <h2>City Snapshot</h2>
            <p>Quick facts on student life in each city to help you make an informed decision.</p>
        </div>

        <div class="city-cards-grid">

            <div class="city-card featured">
                <div class="city-card-header">
                    <span class="city-flag">🇮🇩</span>
                    <div>
                        <h3>Bengkulu</h3>
                        <span class="city-tag best-tag">Most Affordable</span>
                    </div>
                </div>
                <ul class="city-facts">
                    <li><i class="fa-solid fa-check"></i> Avg. monthly cost: <strong>$140 – $260</strong></li>
                    <li><i class="fa-solid fa-check"></i> Tuition: <strong>Free</strong> (full scholarship)</li>
                    <li><i class="fa-solid fa-check"></i> Beach city — relaxed coastal lifestyle</li>
                    <li><i class="fa-solid fa-check"></i> Low crime rate, welcoming community</li>
                    <li><i class="fa-solid fa-check"></i> Tropical climate, year-round warmth</li>
                </ul>
            </div>

            <div class="city-card">
                <div class="city-card-header">
                    <span class="city-flag">🇮🇩</span>
                    <div>
                        <h3>Jakarta</h3>
                        <span class="city-tag">Capital City</span>
                    </div>
                </div>
                <ul class="city-facts">
                    <li><i class="fa-solid fa-minus"></i> Avg. monthly cost: <strong>$350 – $670</strong></li>
                    <li><i class="fa-solid fa-minus"></i> Tuition: $300 – $800/semester</li>
                    <li><i class="fa-solid fa-minus"></i> Heavy traffic & urban density</li>
                    <li><i class="fa-solid fa-check"></i> Huge job market post-graduation</li>
                    <li><i class="fa-solid fa-check"></i> Rich culture & nightlife</li>
                </ul>
            </div>

            <div class="city-card">
                <div class="city-card-header">
                    <span class="city-flag">🇲🇾</span>
                    <div>
                        <h3>Kuala Lumpur</h3>
                        <span class="city-tag">SEA Hub</span>
                    </div>
                </div>
                <ul class="city-facts">
                    <li><i class="fa-solid fa-minus"></i> Avg. monthly cost: <strong>$372 – $717</strong></li>
                    <li><i class="fa-solid fa-minus"></i> Tuition: $600 – $2,000/semester</li>
                    <li><i class="fa-solid fa-check"></i> Multicultural environment</li>
                    <li><i class="fa-solid fa-check"></i> English widely spoken</li>
                    <li><i class="fa-solid fa-minus"></i> Higher visa requirements for some nationals</li>
                </ul>
            </div>

            <div class="city-card">
                <div class="city-card-header">
                    <span class="city-flag">🇹🇭</span>
                    <div>
                        <h3>Bangkok</h3>
                        <span class="city-tag">SEA Capital</span>
                    </div>
                </div>
                <ul class="city-facts">
                    <li><i class="fa-solid fa-minus"></i> Avg. monthly cost: <strong>$367 – $718</strong></li>
                    <li><i class="fa-solid fa-minus"></i> Tuition: $500 – $1,500/semester</li>
                    <li><i class="fa-solid fa-check"></i> Vibrant student city</li>
                    <li><i class="fa-solid fa-minus"></i> Language barrier (Thai)</li>
                    <li><i class="fa-solid fa-check"></i> Great food & tourism scene</li>
                </ul>
            </div>

            <div class="city-card">
                <div class="city-card-header">
                    <span class="city-flag">🇸🇬</span>
                    <div>
                        <h3>Singapore</h3>
                        <span class="city-tag">Premium City</span>
                    </div>
                </div>
                <ul class="city-facts">
                    <li><i class="fa-solid fa-xmark"></i> Avg. monthly cost: <strong>$1,125 – $2,005</strong></li>
                    <li><i class="fa-solid fa-xmark"></i> Tuition: $3,000 – $8,000/semester</li>
                    <li><i class="fa-solid fa-check"></i> World-class education system</li>
                    <li><i class="fa-solid fa-check"></i> English as official language</li>
                    <li><i class="fa-solid fa-xmark"></i> Very high cost of living</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- CTA -->
<section class="col-cta-section">
    <div class="col-cta-inner">
        <h2>Study in Bengkulu — For Free</h2>
        <p>With our full scholarship, you'll enjoy zero tuition costs, a monthly allowance, free dormitory, and all the
            benefits of living in one of Indonesia's most affordable cities.</p>
        <div class="col-cta-btns">
            <a href="register.php" class="col-btn-primary"><i class="fa-solid fa-user-plus"></i> Apply for
                Scholarship</a>
            <a href="requirements.php" class="col-btn-secondary"><i class="fa-solid fa-list-check"></i> Check
                Requirements</a>
        </div>
    </div>
</section>

<style>
    /* =============================================
       Cost of Living Page — Styles
    ============================================= */

    .col-hero {
        background: linear-gradient(135deg, #003d3d 0%, #006666 60%, #e6f2f2 100%);
        color: white;
        padding: 5.5rem 8% 4rem;
    }

    .col-hero h1 {
        color: #fff;
    }

    .col-hero p {
        color: rgba(255, 255, 255, 0.82);
    }

    .col-hero p strong {
        color: #a8f0e0;
    }

    .col-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #a8f0e0;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        margin-bottom: 1.25rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    /* Exchange banner */
    .exchange-banner {
        background: #fffbeb;
        border-bottom: 1px solid #fde68a;
        padding: 0.85rem 8%;
    }

    .exchange-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
        color: #92400e;
    }

    .exchange-inner i {
        color: #d97706;
        font-size: 1rem;
    }

    .exchange-inner strong {
        color: #78350f;
    }

    /* Main sections */
    .col-section {
        padding: 5rem 8%;
        background: var(--white);
    }

    .city-profiles-section {
        padding: 5rem 8%;
        background: var(--secondary);
    }

    .col-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .col-intro {
        text-align: center;
        margin-bottom: 3rem;
    }

    .col-intro h2 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
        letter-spacing: -0.5px;
    }

    .col-intro p {
        color: var(--text-light);
        font-size: 1rem;
        max-width: 620px;
        margin: 0 auto;
    }

    /* ── City Tabs ── */
    .city-tabs {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .tab-btn {
        padding: 0.55rem 1.1rem;
        border: 1px solid #cbd5e0;
        border-radius: 999px;
        background: #f7fafc;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-all);
        color: var(--text-dark);
    }

    .tab-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .tab-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ── Table ── */
    .col-table-wrap {
        overflow-x: auto;
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-md);
        border: 1px solid #e2e8f0;
    }

    .col-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        min-width: 700px;
    }

    .col-table thead tr {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        color: white;
    }

    .col-table th {
        padding: 1rem 1.2rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .col-table th.city-col:not(.active) {
        opacity: 0.65;
    }

    .col-table tbody tr {
        border-bottom: 1px solid #edf2f7;
        transition: background 0.2s;
    }

    .col-table tbody tr:hover {
        background: #f7fafc;
    }

    .col-table td {
        padding: 0.95rem 1.2rem;
        color: var(--text-dark);
        vertical-align: middle;
    }

    .col-table td:first-child {
        font-weight: 600;
        color: var(--text-dark);
        white-space: nowrap;
    }

    .col-table td i {
        color: var(--primary);
        margin-right: 6px;
    }

    /* Active city column highlight */
    .col-table .city-col.active {
        background: rgba(0, 128, 128, 0.04);
    }

    .col-table th.city-col.active {
        background: rgba(0, 0, 0, 0.15);
        opacity: 1;
    }

    /* Highlight best price */
    .highlight-best {
        color: #276749 !important;
        font-weight: 700;
    }

    /* Total row */
    .total-row {
        background: #f0fff4 !important;
    }

    .total-row td {
        font-size: 0.95rem;
    }

    .total-row .highlight-best {
        font-size: 1rem;
        color: #22543d !important;
    }

    /* Tuition row */
    .tuition-row {
        background: #fffbeb;
    }

    .tuition-row td {
        font-size: 0.88rem;
    }

    .free-badge {
        background: linear-gradient(135deg, #00d2b4, #009e87);
        color: white;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ── Savings Banner ── */
    .savings-banner {
        margin-top: 2.5rem;
        background: linear-gradient(135deg, #003d3d, #006666);
        border-radius: var(--border-radius-md);
        padding: 2rem 2.5rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        color: white;
    }

    .savings-icon {
        font-size: 3rem;
        color: #ffd96e;
        flex-shrink: 0;
    }

    .savings-banner h3 {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 0.4rem;
    }

    .savings-banner h3 span {
        color: #a8f0e0;
    }

    .savings-banner p {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.78);
        margin: 0;
        line-height: 1.6;
    }

    .savings-banner p strong {
        color: #a8f0e0;
    }

    /* ── City Profile Cards ── */
    .city-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .city-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: var(--border-radius-md);
        padding: 1.75rem;
        transition: var(--transition-all);
        box-shadow: var(--shadow-sm);
    }

    .city-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary);
    }

    .city-card.featured {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(0, 128, 128, 0.15), var(--shadow-lg);
        background: linear-gradient(180deg, #f0fafa, #fff);
    }

    .city-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.25rem;
    }

    .city-flag {
        font-size: 2.2rem;
        line-height: 1;
    }

    .city-card-header h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 3px;
    }

    .city-tag {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: var(--primary-light);
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .best-tag {
        background: linear-gradient(135deg, #00d2b4, #009e87);
        color: white;
    }

    .city-facts {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .city-facts li {
        font-size: 0.875rem;
        color: var(--text-muted);
        display: flex;
        align-items: flex-start;
        gap: 8px;
        line-height: 1.4;
    }

    .city-facts li i.fa-check {
        color: #38a169;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .city-facts li i.fa-minus {
        color: #d69e2e;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .city-facts li i.fa-xmark {
        color: #e53e3e;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .city-facts li strong {
        color: var(--text-dark);
    }

    /* ── CTA Section ── */
    .col-cta-section {
        padding: 5rem 8%;
        background: linear-gradient(160deg, #003d3d 0%, #007a7a 100%);
        text-align: center;
        color: white;
    }

    .col-cta-inner {
        max-width: 700px;
        margin: 0 auto;
    }

    .col-cta-section h2 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }

    .col-cta-section p {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 2.5rem;
        line-height: 1.7;
    }

    .col-cta-btns {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .col-btn-primary {
        background: linear-gradient(135deg, #00d2b4, #009e87);
        color: white;
        text-decoration: none;
        padding: 1rem 2.2rem;
        border-radius: var(--border-radius-xl);
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 20px rgba(0, 210, 180, 0.35);
        transition: var(--transition-all);
    }

    .col-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 210, 180, 0.5);
    }

    .col-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
        padding: 1rem 2.2rem;
        border-radius: var(--border-radius-xl);
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: var(--transition-all);
    }

    .col-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.18);
        transform: translateY(-3px);
    }

    /* ── Responsive ── */
    <blade media|%20(max-width%3A%20768px)%20%7B>.savings-banner {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .city-cards-grid {
        grid-template-columns: 1fr;
    }

    .col-cta-section h2 {
        font-size: 1.7rem;
    }
    }
</style>

<script>
    // City tab switcher — highlights selected city column
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const city = btn.getAttribute('data-tab');

            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Update table columns
            document.querySelectorAll('.city-col').forEach(col => {
                col.classList.toggle('active', col.getAttribute('data-city') === city);
            });
        });
    });
</script>

<?php require_once 'footer.php'; ?>