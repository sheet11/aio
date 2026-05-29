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

require_once 'header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <h1>Online Application Form</h1>
    <p>Complete the form below to register as an international student candidate for the 2026 intake.</p>
</div>

<!-- Form Registration Section -->
<section class="form-page-section">
    <div class="form-container">

        <!-- Dynamic Alert Box for PHP Response -->
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php if ($message_type == 'success'): ?>
            <i class="fa-solid fa-circle-check" style="font-size: 1.4rem;"></i>
            <?php else: ?>
            <i class="fa-solid fa-circle-xmark" style="font-size: 1.4rem;"></i>
            <?php endif; ?>
            <div><?php echo htmlspecialchars($message); ?></div>
        </div>
        <?php endif; ?>

        <form id="admissionForm" action="register.php" method="POST">

            <!-- SECTION 1: Personal Information -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-user"></i> Personal Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" class="form-control" placeholder="John" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" class="form-control" placeholder="Doe" required>
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
                            <option value="" disabled selected>— Select Gender —</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nationality">Nationality / Country of Origin *</label>
                        <input type="text" id="nationality" name="nationality" class="form-control" placeholder="e.g., Malaysia, Timor Leste" required>
                    </div>
                    <div class="form-group">
                        <label for="passport">Passport Number *</label>
                        <input type="text" id="passport" name="passport" class="form-control" placeholder="e.g., A1234567" required>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Contact Details -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-address-book"></i> Contact Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="johndoe@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">WhatsApp / Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="e.g., +62 812-3456-7890" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="currentLocation">Current City & Country</label>
                    <input type="text" id="currentLocation" name="currentLocation" class="form-control" placeholder="e.g., Kuala Lumpur, Malaysia">
                </div>
            </div>

            <!-- SECTION 3: Academic Background -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-graduation-cap"></i> Academic Background</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="educationLevel">Highest Education Level *</label>
                        <select id="educationLevel" name="educationLevel" class="form-control" required>
                            <option value="" disabled selected>— Select Level —</option>
                            <option value="High School / Senior Secondary">High School / Senior Secondary</option>
                            <option value="Diploma (D-I / D-II / D-III)">Diploma (D-I / D-II / D-III)</option>
                            <option value="Bachelor's Degree (S-1 / D-IV)">Bachelor's Degree (S-1 / D-IV)</option>
                            <option value="Master's Degree (S-2)">Master's Degree (S-2)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="gpa">GPA / Final Grade</label>
                        <input type="text" id="gpa" name="gpa" class="form-control" placeholder="e.g., 3.75 or 85%">
                    </div>
                </div>
                <div class="form-group">
                    <label for="previousSchool">Name of Last School / University *</label>
                    <input type="text" id="previousSchool" name="previousSchool" class="form-control" placeholder="e.g., Global Health Academy" required>
                </div>
            </div>

            <!-- SECTION 4: Program & Preferences -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-book-medical"></i> Program Selection & Preferences</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="program1">Study Program *</label>
                        <select id="program1" name="program1" class="form-control" required>
                            <option value="" disabled selected>— Select Program —</option>
                            <option value="Bachelor Promosi Kesehatan">Bachelor of Health Promotion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="englishProficiency">English Proficiency Level</label>
                        <select id="englishProficiency" name="englishProficiency" class="form-control">
                            <option value="" disabled selected>— Select Level —</option>
                            <option value="IELTS 5.0–5.5">IELTS 5.0–5.5</option>
                            <option value="IELTS 6.0+">IELTS 6.0+</option>
                            <option value="TOEFL ITP 500–549">TOEFL ITP 500–549</option>
                            <option value="TOEFL ITP 550+">TOEFL ITP 550+</option>
                            <option value="Other Certificate">Other Certificate</option>
                            <option value="No Certificate (applying for waiver)">No Certificate (applying for waiver)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sop">Statement of Purpose *</label>
                    <textarea id="sop" name="sop" rows="5" class="form-control" placeholder="Briefly describe your academic motivation and goals at Poltekkes Bengkulu..." required style="resize: vertical; min-height: 120px;"></textarea>
                </div>
                <div class="form-group">
                    <label for="referral">How did you hear about us?</label>
                    <input type="text" id="referral" name="referral" class="form-control" placeholder="e.g., Social Media, Embassy, Friends">
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit Registration <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i></button>
        </form>
    </div>
</section>

<style>
    /* Registration Page Custom CSS */
    .form-page-section {
        padding: 5rem 8%;
        background-color: var(--secondary);
    }

    .form-container {
        max-width: 850px;
        margin: 0 auto;
        background: var(--white);
        padding: 4rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(0, 128, 128, 0.05);
    }

    .form-section {
        margin-bottom: 3rem;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 2rem;
    }

    .form-section:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 2rem;
    }

    .section-subtitle {
        font-size: 1.25rem;
        color: var(--primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
    }

    .section-subtitle i {
        background-color: var(--primary-light);
        width: 36px;
        height: 36px;
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
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
        transition: var(--transition-all);
        background: #f7fafc;
        color: var(--text-dark);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        background: var(--white);
        box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.12);
    }

    /* Premium submit button */
    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 1.1rem;
        border-radius: var(--border-radius-sm);
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-all);
        box-shadow: 0 4px 15px rgba(0, 128, 128, 0.2);
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 128, 128, 0.3);
    }

    /* Alerts */
    .alert {
        padding: 1.25rem 1.75rem;
        margin-bottom: 2.5rem;
        border-radius: var(--border-radius-md);
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 15px;
        animation: slideDown 0.4s ease-out;
    }

    .alert-success {
        background-color: var(--success-light);
        border: 1px solid #c6f6d5;
        color: #22543d;
    }

    .alert-error {
        background-color: var(--error-light);
        border: 1px solid #fed7d7;
        color: #742a2a;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 2.5rem;
        }
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<!-- Form submission loading states -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('admissionForm');
        if (form) {
            const submitBtn = form.querySelector('.btn-submit');
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Submitting your application... <i class="fa-solid fa-spinner fa-spin" style="margin-left: 8px;"></i>';
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            });
        }
    });
</script>

<?php
require_once 'footer.php';
?>
