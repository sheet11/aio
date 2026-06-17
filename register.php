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

// -------------------------------------------------------
// File Upload Helper Function
// -------------------------------------------------------
function handleFileUpload($fileKey, $fieldLabel)
{
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => null, 'error' => null];
    }

    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => "Upload error for $fieldLabel. Please try again."];
    }

    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['path' => null, 'error' => "$fieldLabel exceeds the 5MB file size limit."];
    }

    // Validate MIME type
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['path' => null, 'error' => "$fieldLabel must be a PDF, JPG, or PNG file."];
    }

    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid($fileKey . '_', true) . '.' . strtolower($ext);
    $uploadDir = __DIR__ . '/uploads/';
    $destPath = $uploadDir . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['path' => null, 'error' => "Failed to save $fieldLabel. Check server permissions."];
    }

    return ['path' => 'uploads/' . $uniqueName, 'error' => null];
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

    // -------------------------------------------------------
    // Handle file uploads
    // -------------------------------------------------------
    $passportUpload            = handleFileUpload('passport_file', 'Passport Scan');
    $englishCertUpload         = handleFileUpload('english_cert_file', 'English Certificate');
    $diplomaUpload             = handleFileUpload('diploma_file', 'Diploma / Certificate');
    $transcriptUpload          = handleFileUpload('transcript_file', 'Transcript');
    $photoUpload               = handleFileUpload('photo_file', 'Passport Photo');
    $cvUpload                  = handleFileUpload('cv_file', 'Curriculum Vitae (CV)');
    $letterRecUpload           = handleFileUpload('letter_rec_file', 'Letter of Recommendation');
    $healthCertUpload          = handleFileUpload('health_cert_file', 'Health Certificate');
    $sponsorStatementUpload    = handleFileUpload('sponsor_statement_file', 'Parent/Guardian/Sponsor Statement');
    $statementUpload           = handleFileUpload('statement_file', 'Statement of Legal Commitment');

    // Consolidate upload errors (take the first non-null error)
    $uploadError = null;
    foreach ([$passportUpload, $englishCertUpload, $diplomaUpload, $transcriptUpload, $photoUpload, $cvUpload, $letterRecUpload, $healthCertUpload, $sponsorStatementUpload, $statementUpload] as $u) {
        if (!empty($u['error'])) {
            $uploadError = $u['error'];
            break;
        }
    }

    $passportFilePath           = $passportUpload['path'];
    $englishCertFilePath        = $englishCertUpload['path'];
    $diplomaFilePath            = $diplomaUpload['path'];
    $transcriptFilePath         = $transcriptUpload['path'];
    $photoFilePath              = $photoUpload['path'];
    $cvFilePath                 = $cvUpload['path'];
    $letterRecFilePath          = $letterRecUpload['path'];
    $healthCertFilePath         = $healthCertUpload['path'];
    $sponsorStatementFilePath   = $sponsorStatementUpload['path'];
    $statementFilePath          = $statementUpload['path'];

    // Check mandatory fields
    if (
        empty($firstName) || empty($lastName) || empty($dob) || empty($gender) || empty($nationality) ||
        empty($passport) || empty($email) || empty($phone) || empty($educationLevel) ||
        empty($previousSchool) || empty($program1) || empty($sop)
    ) {

        $message = "Please fill in all required fields marked with an asterisk (*).";
        $message_type = "error";
    } elseif ($uploadError) {
        $message = $uploadError;
        $message_type = "error";
    } else {
        // Insert into database using Secure Prepared Statement targeting the live table 'tb_interstudent'
        $sql = "INSERT INTO tb_interstudent (
                    first_name, last_name, dob, gender, nationality, passport, email, phone, 
                    current_location, education_level, gpa, previous_school, program1, 
                    english_proficiency, sop, referral,
                    passport_file, english_cert_file, diploma_file, transcript_file, photo_file, cv_file, letter_rec_file, health_cert_file, sponsor_statement_file, statement_file
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssssssssssssssssss",
                $firstName,
                $lastName,
                $dob,
                $gender,
                $nationality,
                $passport,
                $email,
                $phone,
                $currentLocation,
                $educationLevel,
                $gpa,
                $previousSchool,
                $program1,
                $englishProficiency,
                $sop,
                $referral,
                $passportFilePath,
                $englishCertFilePath,
                $diplomaFilePath,
                $transcriptFilePath,
                $photoFilePath,
                $cvFilePath,
                $letterRecFilePath,
                $healthCertFilePath,
                $sponsorStatementFilePath,
                $statementFilePath
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Your registration has been submitted successfully! We will contact you via email within 3–5 business days.";
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

        <form id="admissionForm" action="register.php" method="POST" enctype="multipart/form-data">

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
                        <input type="text" id="nationality" name="nationality" class="form-control"
                            placeholder="e.g., Malaysia, Timor Leste" required>
                    </div>
                    <div class="form-group">
                        <label for="passport">Passport Number *</label>
                        <input type="text" id="passport" name="passport" class="form-control"
                            placeholder="e.g., A1234567" required>
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
                            placeholder="e.g., +62 812-3456-7890" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="currentLocation">Current City & Country</label>
                    <input type="text" id="currentLocation" name="currentLocation" class="form-control"
                        placeholder="e.g., Kuala Lumpur, Malaysia">
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
                    <input type="text" id="previousSchool" name="previousSchool" class="form-control"
                        placeholder="e.g., Global Health Academy" required>
                </div>
            </div>

            <!-- SECTION 4: Program & Preferences -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-book-medical"></i> Program Selection & Preferences
                </h3>
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
                            <option value="No Certificate (applying for waiver)">No Certificate (applying for waiver)
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sop">Statement of Purpose *</label>
                    <textarea id="sop" name="sop" rows="5" class="form-control"
                        placeholder="Briefly describe your academic motivation and goals at Poltekkes Bengkulu..."
                        required style="resize: vertical; min-height: 120px;"></textarea>
                </div>
                <div class="form-group">
                    <label for="referral">How did you hear about us?</label>
                    <input type="text" id="referral" name="referral" class="form-control"
                        placeholder="e.g., Social Media, Embassy, Friends">
                </div>
            </div>

            <!-- SECTION 5: Document Uploads -->
            <div class="form-section">
                <h3 class="section-subtitle"><i class="fa-solid fa-file-arrow-up"></i> Supporting Documents</h3>
                <p class="upload-note">Upload scanned copies or photos of your documents. Accepted formats: <strong>PDF,
                        JPG, PNG</strong>. Max size: <strong>5MB</strong> per file.</p>

                <div class="upload-grid">
                    <!-- Passport Scan -->
                    <div class="upload-item">
                        <label class="upload-label" for="passport_file">
                            <div class="upload-icon"><i class="fa-solid fa-passport"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Passport Scan</span>
                                <span class="upload-sub">Photo page of your valid passport</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_passport"
                            onclick="document.getElementById('passport_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_passport"></div>
                        </div>
                        <input type="file" id="passport_file" name="passport_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_passport" data-preview="preview_passport">
                    </div>

                    <!-- English Certificate -->
                    <div class="upload-item">
                        <label class="upload-label" for="english_cert_file">
                            <div class="upload-icon"><i class="fa-solid fa-language"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">English Certificate</span>
                                <span class="upload-sub">IELTS / TOEFL or equivalent</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_english"
                            onclick="document.getElementById('english_cert_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_english"></div>
                        </div>
                        <input type="file" id="english_cert_file" name="english_cert_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_english" data-preview="preview_english">
                    </div>

                    <!-- Diploma / Transcript -->
                    <div class="upload-item">
                        <label class="upload-label" for="diploma_file">
                            <div class="upload-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Diploma</span>
                                <span class="upload-sub">Most recent academic certificate</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_diploma"
                            onclick="document.getElementById('diploma_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_diploma"></div>
                        </div>
                        <input type="file" id="diploma_file" name="diploma_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_diploma" data-preview="preview_diploma">
                    </div>

                    <!-- Transcript (additional) -->
                    <div class="upload-item">
                        <label class="upload-label" for="transcript_file">
                            <div class="upload-icon"><i class="fa-solid fa-file-lines"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Transcript</span>
                                <span class="upload-sub">Official academic transcript (detailed grades)</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_transcript"
                            onclick="document.getElementById('transcript_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_transcript"></div>
                        </div>
                        <input type="file" id="transcript_file" name="transcript_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_transcript" data-preview="preview_transcript">
                    </div>

                    <!-- Passport Photo -->
                    <div class="upload-item">
                        <label class="upload-label" for="photo_file">
                            <div class="upload-icon"><i class="fa-solid fa-image"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Passport Photo</span>
                                <span class="upload-sub">Recent passport-style photograph (4x6 preferred)</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_photo"
                            onclick="document.getElementById('photo_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_photo"></div>
                        </div>
                        <input type="file" id="photo_file" name="photo_file" accept=".jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_photo" data-preview="preview_photo">
                    </div>

                    <!-- CV -->
                    <div class="upload-item">
                        <label class="upload-label" for="cv_file">
                            <div class="upload-icon"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Curriculum Vitae (CV)</span>
                                <span class="upload-sub">CV / Resume</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_cv" onclick="document.getElementById('cv_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_cv"></div>
                        </div>
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_cv" data-preview="preview_cv">
                    </div>

                    <!-- Letter of Recommendation -->
                    <div class="upload-item">
                        <label class="upload-label" for="letter_rec_file">
                            <div class="upload-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Letter of Recommendation</span>
                                <span class="upload-sub">From teacher, employer, or headmaster</span>
                            </div>
                        </label>
                        <div class="sample-actions">
                            <a href="downloads/letter of recomendation.docx" class="sample-download" download>Download
                                Sample Recommendation Letter</a>
                        </div>
                        <div class="upload-drop-zone" id="zone_letter_rec"
                            onclick="document.getElementById('letter_rec_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_letter_rec"></div>
                        </div>
                        <input type="file" id="letter_rec_file" name="letter_rec_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_letter_rec" data-preview="preview_letter_rec">
                    </div>

                    <!-- Health Certificate -->
                    <div class="upload-item">
                        <label class="upload-label" for="health_cert_file">
                            <div class="upload-icon"><i class="fa-solid fa-file-medical"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Health Certificate</span>
                                <span class="upload-sub">General physical and mental health certificate from a
                                    government hospital in your country of origin</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_health_cert"
                            onclick="document.getElementById('health_cert_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_health_cert"></div>
                        </div>
                        <input type="file" id="health_cert_file" name="health_cert_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_health_cert" data-preview="preview_health_cert">
                    </div>

                    <!-- Parent / Guardian / Sponsor Statement -->
                    <div class="upload-item">
                        <label class="upload-label" for="sponsor_statement_file">
                            <div class="upload-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Parent / Guardian / Sponsor Statement</span>
                                <span class="upload-sub">Statement of willingness to cover costs not covered by the
                                    scholarship</span>
                            </div>
                        </label>
                        <div class="upload-drop-zone" id="zone_sponsor_statement"
                            onclick="document.getElementById('sponsor_statement_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_sponsor_statement"></div>
                        </div>
                        <input type="file" id="sponsor_statement_file" name="sponsor_statement_file"
                            accept=".pdf,.jpg,.jpeg,.png" class="upload-input" data-zone="zone_sponsor_statement"
                            data-preview="preview_sponsor_statement">
                    </div>

                    <!-- Statement of Legal Commitment -->
                    <div class="upload-item">
                        <label class="upload-label" for="statement_file">
                            <div class="upload-icon"><i class="fa-solid fa-file-contract"></i></div>
                            <div class="upload-info">
                                <span class="upload-title">Statement of Legal Commitment</span>
                                <span class="upload-sub">Signed statement agreeing to regulations</span>
                            </div>
                        </label>
                        <div class="sample-actions">
                            <a href="downloads/statement of legal compliment.docx" class="sample-download"
                                download>Download Sample Statement</a>
                        </div>
                        <div class="upload-drop-zone" id="zone_statement"
                            onclick="document.getElementById('statement_file').click()">
                            <i class="fa-solid fa-cloud-arrow-up upload-drop-icon"></i>
                            <p class="upload-drop-text">Click to upload or drag & drop</p>
                            <p class="upload-drop-hint">PDF, JPG, PNG &mdash; max 5MB</p>
                            <div class="upload-preview" id="preview_statement"></div>
                        </div>
                        <input type="file" id="statement_file" name="statement_file" accept=".pdf,.jpg,.jpeg,.png"
                            class="upload-input" data-zone="zone_statement" data-preview="preview_statement">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit Registration <i class="fa-solid fa-paper-plane"
                    style="margin-left: 8px;"></i></button>
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

    <blade keyframes|%20slideDown%20%7B>from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
    }

    /* ── Upload Section ── */
    .upload-note {
        font-size: 0.88rem;
        color: var(--text-light);
        margin-bottom: 1.75rem;
        background: var(--primary-light);
        padding: 0.75rem 1rem;
        border-radius: var(--border-radius-sm);
        border-left: 3px solid var(--primary);
    }

    .upload-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
    }

    .upload-item {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .upload-label {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: default;
    }

    .upload-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--border-radius-sm);
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .upload-info {
        display: flex;
        flex-direction: column;
    }

    .upload-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .upload-sub {
        font-size: 0.78rem;
        color: var(--text-light);
    }

    .sample-actions {
        margin-top: 6px;
    }

    .sample-download {
        display: inline-block;
        font-size: 0.82rem;
        color: var(--primary-dark);
        background: rgba(0, 0, 0, 0.03);
        padding: 6px 10px;
        border-radius: 8px;
        text-decoration: none;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .sample-download:hover {
        background: var(--primary-light);
        color: var(--primary);
        transform: translateY(-2px);
    }

    .upload-drop-zone {
        border: 2px dashed #cbd5e0;
        border-radius: var(--border-radius-sm);
        padding: 1.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: var(--transition-all);
        background: #f7fafc;
        position: relative;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .upload-drop-zone:hover,
    .upload-drop-zone.drag-over {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .upload-drop-zone.has-file {
        border-color: #48bb78;
        background: #f0fff4;
    }

    .upload-drop-icon {
        font-size: 1.6rem;
        color: #a0aec0;
        transition: var(--transition-all);
    }

    .upload-drop-zone:hover .upload-drop-icon,
    .upload-drop-zone.drag-over .upload-drop-icon {
        color: var(--primary);
        transform: translateY(-3px);
    }

    .upload-drop-zone.has-file .upload-drop-icon {
        color: #48bb78;
    }

    .upload-drop-text {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .upload-drop-hint {
        font-size: 0.75rem;
        color: var(--text-light);
        margin: 0;
    }

    .upload-preview {
        font-size: 0.78rem;
        color: #276749;
        font-weight: 600;
        margin-top: 4px;
        word-break: break-all;
    }

    .upload-input {
        display: none;
    }

    <blade media|%20(max-width%3A%20768px)%20%7B>.form-container {
        padding: 2.5rem;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .upload-grid {
        grid-template-columns: 1fr;
    }
    }
</style>

<!-- Form submission loading states + File upload previews -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ── Submit loading state ──
        const form = document.getElementById('admissionForm');
        if (form) {
            const submitBtn = form.querySelector('.btn-submit');
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    'Submitting your application... <i class="fa-solid fa-spinner fa-spin" style="margin-left: 8px;"></i>';
                submitBtn.style.opacity = '0.8';
                submitBtn.style.cursor = 'not-allowed';
            });
        }

        // ── File Upload: preview & drag-drop ──
        document.querySelectorAll('.upload-input').forEach(input => {
            const zoneId = input.getAttribute('data-zone');
            const previewId = input.getAttribute('data-preview');
            const zone = document.getElementById(zoneId);
            const preview = document.getElementById(previewId);

            // Click to upload handled by zone onclick → triggers input.click()
            input.addEventListener('change', () => updatePreview(input, zone, preview));

            // Drag & Drop
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.classList.add('drag-over');
            });
            zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length) {
                    // Transfer dropped file to input
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    input.files = dt.files;
                    updatePreview(input, zone, preview);
                }
            });
        });

        function updatePreview(input, zone, preview) {
            const file = input.files[0];
            if (!file) return;

            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert(`File "${file.name}" exceeds the 5MB limit.`);
                input.value = '';
                return;
            }

            zone.classList.add('has-file');
            const icon = zone.querySelector('.upload-drop-icon');
            if (icon) icon.className = 'fa-solid fa-circle-check upload-drop-icon';

            const textEl = zone.querySelector('.upload-drop-text');
            if (textEl) textEl.textContent = 'File selected';

            preview.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        }
    });
</script>

<?php
require_once 'footer.php';
?>