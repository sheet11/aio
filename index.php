<?php
// Include database connection
require_once 'koneksi.php';

$message = "";
$message_type = ""; // "success" or "error"

// Check if database connection encountered an error
if (isset($db_connection_error)) {
    $message = $db_connection_error;
    $message_type = "error";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($db_connection_error)) {
    // Collect and sanitize/validate required fields
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
    $passport = isset($_POST['passport']) ? trim($_POST['passport']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $educationLevel = isset($_POST['educationLevel']) ? trim($_POST['educationLevel']) : '';
    $previousSchool = isset($_POST['previousSchool']) ? trim($_POST['previousSchool']) : '';
    $program1 = isset($_POST['program1']) ? trim($_POST['program1']) : '';
    $sop = isset($_POST['sop']) ? trim($_POST['sop']) : '';

    // Handle optional fields: convert empty strings to NULL to avoid database corruption or wrong inputs
    $currentLocation = (isset($_POST['currentLocation']) && trim($_POST['currentLocation']) !== '') ? trim($_POST['currentLocation']) : null;
    $gpa = (isset($_POST['gpa']) && trim($_POST['gpa']) !== '') ? trim($_POST['gpa']) : null;
    $englishProficiency = (isset($_POST['englishProficiency']) && trim($_POST['englishProficiency']) !== '') ? trim($_POST['englishProficiency']) : null;
    $referral = (isset($_POST['referral']) && trim($_POST['referral']) !== '') ? trim($_POST['referral']) : null;

    // Check mandatory fields
    if (empty($firstName) || empty($lastName) || empty($dob) || empty($gender) || empty($nationality) || 
        empty($passport) || empty($email) || empty($phone) || empty($educationLevel) || 
        empty($previousSchool) || empty($program1) || empty($sop)) {
        
        $message = "Please fill in all required fields marked with an asterisk (*).";
        $message_type = "error";
    } else {
        // Insert into database using Secure Prepared Statement targeting the live table 'tb_interstudent'
        $sql = "INSERT INTO tb_interstudent (
                    first_name, last_name, dob, gender, nationality, passport, email, phone, 
                    current_location, education_level, gpa, previous_school, program1, 
                    english_proficiency, sop, referral
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssssssssssss", 
                $firstName, $lastName, $dob, $gender, $nationality, $passport, $email, $phone, 
                $currentLocation, $educationLevel, $gpa, $previousSchool, $program1, 
                $englishProficiency, $sop, $referral
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Your registration has been submitted successfully!";
                $message_type = "success";
            } else {
                $message = "Oops! Something went wrong while saving your data: " . mysqli_stmt_error($stmt);
                $message_type = "error";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $message = "Database prepared statement failed: " . mysqli_error($conn);
            $message_type = "error";
        }
    }
}
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #008080;
            /* Teal brand color */
            --primary-dark: #006666;
            --secondary: #f4fbfb;
            --text-dark: #2d3748;
            --text-light: #718096;
            --white: #ffffff;
            --success: #38a169;
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
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 8%;
        }

        .logo {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s;
            font-size: 0.95rem;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-nav {
            background: var(--primary);
            color: white !important;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            transition: all 0.3s !important;
        }

        .btn-nav:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.2);
        }

        /* Hero Section */
        .hero {
            padding: 10rem 8% 6rem 8%;
            background: linear-gradient(135deg, #e6f2f2 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 85vh;
        }

        .hero-content {
            max-width: 55%;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1a202c;
        }

        .hero-content h1 span {
            color: var(--primary);
        }

        .hero-content p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
        }

        .hero-image {
            max-width: 40%;
            position: relative;
        }

        .hero-image img {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Section Global Settings */
        section {
            padding: 6rem 8%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .section-title p {
            color: var(--text-light);
        }

        /* Requirements Section */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
            border-color: var(--primary);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: #e6f2f2;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .card ul {
            list-style-position: inside;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .card ul li {
            margin-bottom: 0.5rem;
        }

        /* Scholarship Section */
        #scholarship {
            background-color: var(--secondary);
        }

        .scholarship-box {
            background: var(--white);
            border-radius: 24px;
            padding: 3.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            display: flex;
            gap: 3rem;
            align-items: center;
        }

        .scholarship-info {
            flex: 1;
        }

        .scholarship-info h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .benefit-item i {
            color: var(--success);
        }

        /* Form Registration Section */
        .form-container {
            max-width: 850px !important;
            /* Increased width to perfectly host multi-column rows */
            margin: 0 auto;
            background: var(--white);
            padding: 3.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .form-section {
            margin-bottom: 2.5rem;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 1.5rem;
        }

        .form-section:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        /* Contact Section */
        #contact {
            background: #1a202c;
            color: var(--white);
            padding: 4rem 8%;
        }

        .contact-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .contact-info h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .contact-info p {
            color: #a0aec0;
        }

        .contact-links {
            display: flex;
            gap: 2rem;
        }

        .contact-links a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }

        .contact-links a:hover {
            color: var(--primary);
        }

        /* Alert Styles */
        .alert {
            padding: 1.25rem 1.75rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease-out;
        }

        .alert-success {
            background-color: #e6fffa;
            border: 1px solid #b2f5ea;
            color: #008080;
        }

        .alert-error {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            color: #c53030;
        }

        <blade keyframes|%20slideDown%20%7B%0D>from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
        }

        /* Responsive Design */
        <blade media|%20(max-width%3A%20968px)%20%7B%0D>nav {
            padding: 1.2rem 5%;
        }

        .nav-links {
            gap: 1.5rem;
        }

        .hero {
            flex-direction: column;
            text-align: center;
            padding-top: 8rem;
        }

        .hero-content {
            max-width: 100%;
            margin-bottom: 3rem;
        }

        .hero-image {
            max-width: 80%;
        }

        .scholarship-box {
            flex-direction: column;
            padding: 2rem;
        }

        .benefits-grid {
            grid-template-columns: 1fr;
        }

        .form-container {
            padding: 2rem;
        }
        }

        <blade media|%20(max-width%3A%20768px)%20%7B%0D>.form-row {
            grid-template-columns: 1fr;
            /* Switch to single column on mobile views */
            gap: 0;
        }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav>
        <a href="#" class="logo">
            <i class="fa-solid fa-graduation-cap"></i> Poltekkes Bengkulu
        </a>
        <ul class="nav-links">
            <li><a href="#requirements">Requirements</a></li>
            <li><a href="#facilities">Campus Facilities</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#register" class="btn-nav">Apply Now</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Start Your Healthcare Career Journey in <span>Indonesia</span></h1>
            <p>Poltekkes Kemenkes Bengkulu offers world-class health education modules tailored for global environments.
                Join our diverse international community today.</p>
            <a href="#register" class="btn-nav"
                style="padding: 1rem 2.5rem; font-size: 1rem; text-align:center; display:inline-block;">Online
                Application Form</a>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=600&q=80"
                alt="Medical Students">
        </div>
    </section>

    <!-- Requirements Section -->
    <section id="requirements">
        <div class="section-title">
            <h2>Admission Requirements</h2>
            <p>Please check the criteria below before submitting your application files</p>
        </div>
        <div class="grid-3">
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-user-graduate"></i></div>
                <h3>Academic Criteria</h3>
                <ul>
                    <li>High school graduate certificate or equivalent</li>
                    <li>Minimum GPA equivalent to 3.00 out of 4.00</li>
                    <li>Strong foundation in Natural Sciences (Biology, Chemistry)</li>
                </ul>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-passport"></i></div>
                <h3>Documents Needed</h3>
                <ul>
                    <li>Scanned copy of valid passport (min. 18 months validity)</li>
                    <li>Recent formal photograph (red background)</li>
                    <li>Health certificate & proof of health insurance</li>
                </ul>
            </div>
            <div class="card">
                <div class="card-icon"><i class="fa-solid fa-language"></i></div>
                <h3>Language Mastery</h3>
                <ul>
                    <li>English Proficiency Certificate (TOEFL min. 500 / IELTS min. 5.5)</li>
                    <li>Willingness to join Basic Indonesian Language Course</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Campus Facilities Section -->
    <section id="facilities">
        <div class="section-title">
            <h2>Campus Facilities</h2>
            <p>Experience a conducive learning environment with our modern facilities</p>
        </div>
        <div class="scholarship-box">
            <div class="scholarship-info">
                <h3>World-Class Learning Infrastructure</h3>
                <p>We provide state-of-the-art facilities to ensure our students get the best practical and theoretical learning experience.</p>
                <div class="benefits-grid">
                    <div class="benefit-item"><i class="fa-solid fa-flask"></i> Modern Health Laboratories</div>
                    <div class="benefit-item"><i class="fa-solid fa-book-open"></i> Comprehensive Library</div>
                    <div class="benefit-item"><i class="fa-solid fa-wifi"></i> High-Speed Campus Wi-Fi</div>
                    <div class="benefit-item"><i class="fa-solid fa-bed"></i> Comfortable Student Dormitories</div>
                    <div class="benefit-item"><i class="fa-solid fa-dumbbell"></i> Sports & Recreation Center</div>
                    <div class="benefit-item"><i class="fa-solid fa-utensils"></i> Student Cafeteria</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form Section -->
    <section id="register">
        <div class="section-title">
            <h2>Registration Form</h2>
            <p>Please fill out the form completely. Fields marked with an asterisk (*) are required.</p>
        </div>
        <div class="form-container">

            <!-- Dynamic Alert Box for PHP Response -->
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php if ($message_type == 'success'): ?>
                <i class="fa-solid fa-circle-check" style="font-size: 1.25rem;"></i>
                <?php else: ?>
                <i class="fa-solid fa-circle-xmark" style="font-size: 1.25rem;"></i>
                <?php endif; ?>
                <div><?php echo htmlspecialchars($message); ?></div>
            </div>
            <?php endif; ?>

            <form id="admissionForm" action="index.php#register" method="POST">

                <!-- SECTION 1: Personal Information -->
                <div class="form-section">
                    <h3 class="section-subtitle"><i class="fa-solid fa-user"></i> Personal Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="firstName" class="form-control" placeholder="John"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Doe"
                                required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dob">Date of Birth *</label>
                            <input type="date" id="dob" name="dob" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="" disabled selected>— Select —</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nationality">Nationality / Country of Origin *</label>
                            <input type="text" id="nationality" name="nationality" class="form-control"
                                placeholder="e.g., Malaysia, Timor Leste" required>
                        </div>
                        <div class="form-group">
                            <label for="passport">Passport Number *</label>
                            <input type="text" id="passport" name="passport" class="form-control" placeholder="A1234567"
                                required>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Contact Details -->
                <div class="form-section">
                    <h3 class="section-subtitle"><i class="fa-solid fa-address-book"></i> Contact Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="johndoe@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">WhatsApp / Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                placeholder="+62 812-3456-7890" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="currentLocation">Current City & Country</label>
                        <input type="text" id="currentLocation" name="currentLocation" class="form-control"
                            placeholder="Kuala Lumpur, Malaysia">
                    </div>
                </div>


                <!-- SECTION 4: Program & Requirements -->
                <div class="form-section">
                    <h3 class="section-subtitle"><i class="fa-solid fa-book-medical"></i> Program Selection &
                        Preferences</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="program1">Study Program *</label>
                            <select id="program1" name="program1" class="form-control" required>
                                <option value="" disabled selected>— Select Program —</option>
                                <option value="Bachelor Promosi Kesehatan">Bachelor Promosi Kesehatan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">

                        <div class="form-group">
                            <label for="englishProficiency">English Proficiency Level</label>
                            <select id="englishProficiency" name="englishProficiency" class="form-control">
                                <option value="" disabled selected>— Select —</option>
                                <option value="IELTS 5.0–5.5">IELTS 5.0–5.5</option>
                                <option value="IELTS 6.0+">IELTS 6.0+</option>
                                <option value="TOEFL ITP 500–549">TOEFL ITP 500–549</option>
                                <option value="TOEFL ITP 550+">TOEFL ITP 550+</option>
                                <option value="Other Certificate">Other Certificate</option>
                                <option value="No Certificate (applying for waiver)">No Certificate (applying for
                                    waiver)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sop">Statement of Purpose *</label>
                        <textarea id="sop" name="sop" rows="4" class="form-control"
                            placeholder="Briefly describe your motivation to study at Poltekkes Bengkulu..." required
                            style="resize: vertical; min-height: 100px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="referral">How did you hear about us?</label>
                        <input type="text" id="referral" name="referral" class="form-control"
                            placeholder="e.g., Social Media, Embassy, Friends">
                    </div>
                </div>



                <button type="submit" class="btn-submit">Submit Registration</button>
            </form>
        </div>
    </section>

    <!-- Contact Person / Footer Section -->
    <footer id="contact">
        <div class="contact-grid">
            <div class="contact-info">
                <h3>Poltekkes Kemenkes Bengkulu</h3>
                <p>Jl. Indragiri No.3, Padang Harapan, Kota Bengkulu, Indonesia</p>
            </div>
            <div class="contact-links">
                <a href="mailto:international.admission@poltekkesbengkulu.ac.id"><i class="fa-solid fa-envelope"></i>
                    Email Us</a>
                <a href="https://wa.me/6281234567890" target="_blank"><i class="fa-brands fa-whatsapp"></i>
                    International Desk (WhatsApp)</a>
            </div>
        </div>
        <div
            style="text-align: center; margin-top: 3rem; color: #718096; font-size: 0.85rem; border-top: 1px solid #2d3748; padding-top: 1.5rem;">
            &copy; 2026 Poltekkes Kemenkes Bengkulu. All Rights Reserved.
        </div>
    </footer>

    <!-- Handling Form Submission cleanly with Premium UX Loader -->
    <script>
        const form = document.getElementById('admissionForm');
        const submitBtn = form.querySelector('.btn-submit');
        form.addEventListener('submit', function (e) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting your application...';
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
        });
    </script>
</body>

</html>