<?php
session_start();
require_once 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$message = "";
$message_type = "";

// Fetch student data
$sql = "SELECT * FROM tb_interstudent WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_type'])) {
    $upload_type = $_POST['upload_type']; // passport, english, or diploma
    
    // Check if file was uploaded without errors
    if (isset($_FILES["document"]) && $_FILES["document"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "pdf" => "application/pdf");
        $filename = $_FILES["document"]["name"];
        $filetype = $_FILES["document"]["type"];
        $filesize = $_FILES["document"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $message = "Error: Please select a valid file format (JPG, PNG, or PDF).";
            $message_type = "error";
        } else {
            // Verify file size - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) {
                $message = "Error: File size is larger than the allowed limit (5MB).";
                $message_type = "error";
            } else {
                // Check whether file exists before uploading it
                $new_filename = $upload_type . "_" . $student_id . "." . $ext;
                $upload_path = "uploads/" . $new_filename;
                
                if (move_uploaded_file($_FILES["document"]["tmp_name"], $upload_path)) {
                    // Update database
                    $db_column = "";
                    if ($upload_type == "passport") $db_column = "passport_file";
                    else if ($upload_type == "english") $db_column = "english_cert_file";
                    else if ($upload_type == "diploma") $db_column = "diploma_file";
                    
                    if ($db_column != "") {
                        $update_sql = "UPDATE tb_interstudent SET $db_column = ? WHERE id = ?";
                        if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                            mysqli_stmt_bind_param($update_stmt, "si", $new_filename, $student_id);
                            mysqli_stmt_execute($update_stmt);
                            mysqli_stmt_close($update_stmt);
                            
                            $message = "Your file was uploaded successfully.";
                            $message_type = "success";
                            
                            // Refresh student data
                            $student[$db_column] = $new_filename;
                        }
                    }
                } else {
                    $message = "Error: There was a problem uploading your file. Please try again.";
                    $message_type = "error";
                }
            }
        }
    } else {
        $message = "Error: " . $_FILES["document"]["error"];
        $message_type = "error";
    }
}

// Calculate progress
$completed_steps = 0;
if (!empty($student['passport_file'])) $completed_steps++;
if (!empty($student['english_cert_file'])) $completed_steps++;
if (!empty($student['diploma_file'])) $completed_steps++;

$progress_percentage = ($completed_steps / 3) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Poltekkes Kemenkes Bengkulu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #008080;
            --primary-dark: #006666;
            --secondary: #f4fbfb;
            --text-dark: #2d3748;
            --text-light: #718096;
            --white: #ffffff;
            --success: #38a169;
            --warning: #dd6b20;
            --bg-color: #f7fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Top Navigation */
        .topbar {
            background: var(--white);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-greeting {
            font-weight: 600;
        }

        .btn-logout {
            color: #e53e3e;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s;
        }

        .btn-logout:hover { color: #c53030; }

        /* Dashboard Container */
        .dashboard-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 2.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 128, 128, 0.2);
        }

        .welcome-banner h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .welcome-banner p { opacity: 0.9; font-size: 1.1rem; }

        /* Progress Bar */
        .progress-container {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .progress-bar-bg {
            background: #e2e8f0;
            height: 12px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: var(--success);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        /* Upload Steps Grid */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .step-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
            position: relative;
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 20px;
            background: var(--primary);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,128,128,0.3);
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 1rem;
            margin-top: 0.5rem;
        }

        .step-icon {
            font-size: 2rem;
            color: var(--primary);
        }

        .step-title h3 { font-size: 1.2rem; }
        .step-desc { color: var(--text-light); font-size: 0.9rem; margin-bottom: 1.5rem; }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .status-pending { background: #feebc8; color: #c05621; }
        .status-completed { background: #c6f6d5; color: #276749; }

        /* Upload Form */
        .upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: var(--secondary);
        }

        .file-input { display: none; }
        .upload-label { cursor: pointer; display: block; }
        .upload-icon { font-size: 1.5rem; color: var(--text-light); margin-bottom: 0.5rem; }
        
        .btn-upload {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 1rem;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-upload:hover { background: var(--primary-dark); }

        .file-view-link {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .file-view-link:hover { text-decoration: underline; }

        /* Print Card Section */
        .print-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 3rem;
            border: 2px solid var(--primary);
        }

        .print-section.locked {
            border-color: #cbd5e0;
            opacity: 0.7;
        }

        .btn-print {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn-print:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,128,128,0.2);
        }

        .btn-print:disabled {
            background: #a0aec0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Alert styling */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert-success { background: #c6f6d5; color: #276749; border: 1px solid #9ae6b4; }
        .alert-error { background: #fed7d7; color: #c53030; border: 1px solid #feb2b2; }

        /* --- PRINT STYLES --- */
        /* The physical admission card layout that only appears on paper */
        #registration-card {
            display: none;
        }

        @media print {
            body { background: white; }
            .topbar, .dashboard-container > *:not(#registration-card) { display: none !important; }
            
            #registration-card {
                display: block;
                width: 100%;
                max-width: 800px;
                margin: 0 auto;
                border: 2px solid #2d3748;
                padding: 2rem;
                page-break-inside: avoid;
            }

            .rc-header {
                display: flex;
                align-items: center;
                border-bottom: 2px solid #2d3748;
                padding-bottom: 1rem;
                margin-bottom: 2rem;
            }
            .rc-header img { width: 60px; height: auto; margin-right: 1.5rem; }
            .rc-header h2 { color: #2d3748; margin: 0; font-size: 1.5rem; }
            .rc-header p { margin: 0; color: #4a5568; font-size: 0.9rem; }

            .rc-body { display: flex; gap: 2rem; }
            .rc-photo {
                width: 120px;
                height: 160px;
                border: 1px solid #cbd5e0;
                background: #f7fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #a0aec0;
                font-size: 0.8rem;
                text-align: center;
            }

            .rc-details { flex: 1; }
            .rc-row { display: flex; margin-bottom: 0.8rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.2rem; }
            .rc-label { width: 150px; font-weight: bold; color: #4a5568; }
            .rc-value { flex: 1; font-weight: 500; color: #1a202c; }

            .rc-footer {
                margin-top: 2rem;
                text-align: center;
                font-size: 0.85rem;
                color: #718096;
                padding-top: 1rem;
                border-top: 1px solid #e2e8f0;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <div class="topbar">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-graduation-cap"></i> Poltekkes OIA
        </a>
        <div class="user-menu">
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['student_name']); ?>!</span>
            <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <!-- Main Dashboard -->
    <div class="dashboard-container">
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="welcome-banner">
            <h1>Welcome to your portal!</h1>
            <p>Please complete all document upload steps below to finalize your registration and print your admission card.</p>
        </div>

        <div class="progress-container">
            <div class="progress-header">
                <span>Document Upload Progress</span>
                <span><?php echo round($progress_percentage); ?>% Completed</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?php echo $progress_percentage; ?>%;"></div>
            </div>
        </div>

        <div class="steps-grid">
            <!-- Step 1: Passport -->
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-header">
                    <div class="step-icon"><i class="fa-solid fa-passport"></i></div>
                    <div class="step-title">
                        <h3>Passport Scan</h3>
                    </div>
                </div>
                
                <?php if (!empty($student['passport_file'])): ?>
                    <div class="status-badge status-completed"><i class="fa-solid fa-check"></i> Uploaded</div>
                    <p class="step-desc">Your passport copy has been received.</p>
                    <a href="uploads/<?php echo htmlspecialchars($student['passport_file']); ?>" target="_blank" class="file-view-link"><i class="fa-solid fa-eye"></i> View Uploaded File</a>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem; border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.5rem;">Need to update it?</p>
                        <input type="hidden" name="upload_type" value="passport">
                        <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" required style="font-size: 0.85rem; width: 100%;">
                        <button type="submit" class="btn-upload" style="padding: 0.4rem; font-size: 0.9rem;">Replace File</button>
                    </form>
                <?php else: ?>
                    <div class="status-badge status-pending">Pending Upload</div>
                    <p class="step-desc">Please upload a clear scan of your valid passport (bio-data page).</p>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="upload_type" value="passport">
                        <div class="upload-area">
                            <label class="upload-label">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                                <span style="display: block; font-size: 0.9rem;">Click to select file (PDF, JPG, PNG)</span>
                                <input type="file" name="document" class="file-input" accept=".jpg,.jpeg,.png,.pdf" required onchange="this.form.submitBtn.disabled = false; this.nextElementSibling.innerText = this.files[0].name;">
                                <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-top: 10px; font-weight: 600;"></span>
                            </label>
                        </div>
                        <button type="submit" name="submitBtn" class="btn-upload" disabled>Upload Passport</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Step 2: English Certificate -->
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-header">
                    <div class="step-icon"><i class="fa-solid fa-language"></i></div>
                    <div class="step-title">
                        <h3>English Certificate</h3>
                    </div>
                </div>
                
                <?php if (!empty($student['english_cert_file'])): ?>
                    <div class="status-badge status-completed"><i class="fa-solid fa-check"></i> Uploaded</div>
                    <p class="step-desc">Your English proficiency certificate is received.</p>
                    <a href="uploads/<?php echo htmlspecialchars($student['english_cert_file']); ?>" target="_blank" class="file-view-link"><i class="fa-solid fa-eye"></i> View Uploaded File</a>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem; border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.5rem;">Need to update it?</p>
                        <input type="hidden" name="upload_type" value="english">
                        <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" required style="font-size: 0.85rem; width: 100%;">
                        <button type="submit" class="btn-upload" style="padding: 0.4rem; font-size: 0.9rem;">Replace File</button>
                    </form>
                <?php else: ?>
                    <div class="status-badge status-pending">Pending Upload</div>
                    <p class="step-desc">Upload your TOEFL, IELTS, TOEIC, or equivalent certificate.</p>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="upload_type" value="english">
                        <div class="upload-area">
                            <label class="upload-label">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                                <span style="display: block; font-size: 0.9rem;">Click to select file (PDF, JPG, PNG)</span>
                                <input type="file" name="document" class="file-input" accept=".jpg,.jpeg,.png,.pdf" required onchange="this.form.submitBtn.disabled = false; this.nextElementSibling.innerText = this.files[0].name;">
                                <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-top: 10px; font-weight: 600;"></span>
                            </label>
                        </div>
                        <button type="submit" name="submitBtn" class="btn-upload" disabled>Upload Certificate</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Step 3: Diploma -->
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-header">
                    <div class="step-icon"><i class="fa-solid fa-certificate"></i></div>
                    <div class="step-title">
                        <h3>Academic Diploma</h3>
                    </div>
                </div>
                
                <?php if (!empty($student['diploma_file'])): ?>
                    <div class="status-badge status-completed"><i class="fa-solid fa-check"></i> Uploaded</div>
                    <p class="step-desc">Your High School/Degree diploma is received.</p>
                    <a href="uploads/<?php echo htmlspecialchars($student['diploma_file']); ?>" target="_blank" class="file-view-link"><i class="fa-solid fa-eye"></i> View Uploaded File</a>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem; border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                        <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.5rem;">Need to update it?</p>
                        <input type="hidden" name="upload_type" value="diploma">
                        <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" required style="font-size: 0.85rem; width: 100%;">
                        <button type="submit" class="btn-upload" style="padding: 0.4rem; font-size: 0.9rem;">Replace File</button>
                    </form>
                <?php else: ?>
                    <div class="status-badge status-pending">Pending Upload</div>
                    <p class="step-desc">Upload your latest graduation certificate or diploma.</p>
                    
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="upload_type" value="diploma">
                        <div class="upload-area">
                            <label class="upload-label">
                                <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
                                <span style="display: block; font-size: 0.9rem;">Click to select file (PDF, JPG, PNG)</span>
                                <input type="file" name="document" class="file-input" accept=".jpg,.jpeg,.png,.pdf" required onchange="this.form.submitBtn.disabled = false; this.nextElementSibling.innerText = this.files[0].name;">
                                <span style="display: block; font-size: 0.85rem; color: var(--primary); margin-top: 10px; font-weight: 600;"></span>
                            </label>
                        </div>
                        <button type="submit" name="submitBtn" class="btn-upload" disabled>Upload Diploma</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Step 4: Print Card -->
        <div class="print-section <?php echo ($completed_steps < 3) ? 'locked' : ''; ?>">
            <h2><i class="fa-solid fa-id-card"></i> Step 4: Admission Card</h2>
            <p style="margin-top: 0.5rem; color: var(--text-light);">
                <?php if ($completed_steps < 3): ?>
                    You must complete all document uploads (100%) before you can generate and print your registration card.
                <?php else: ?>
                    Your documents are complete! You can now print your official admission card. Please bring this card during your selection process.
                <?php endif; ?>
            </p>
            <button onclick="window.print()" class="btn-print" <?php echo ($completed_steps < 3) ? 'disabled' : ''; ?>>
                <i class="fa-solid fa-print"></i> Print Registration Card
            </button>
        </div>

        <!-- HIDDEN PRINTABLE CARD (Only visible when printing) -->
        <?php if ($completed_steps == 3): ?>
        <div id="registration-card">
            <div class="rc-header">
                <!-- Fallback to a FontAwesome Icon for logo if real logo image isn't available locally -->
                <i class="fa-solid fa-graduation-cap" style="font-size: 3rem; color: #008080; margin-right: 1.5rem;"></i>
                <div>
                    <h2>POLTEKKES KEMENKES BENGKULU</h2>
                    <p>International Admissions - Official Registration Card</p>
                </div>
            </div>
            
            <div class="rc-body">
                <div class="rc-photo">
                    Attach 3x4<br>Photo Here
                </div>
                <div class="rc-details">
                    <div class="rc-row">
                        <div class="rc-label">Registration ID</div>
                        <div class="rc-value">OIA-<?php echo date('Y'); ?>-<?php echo str_pad($student['id'], 4, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div class="rc-row">
                        <div class="rc-label">Full Name</div>
                        <div class="rc-value" style="text-transform: uppercase;"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></div>
                    </div>
                    <div class="rc-row">
                        <div class="rc-label">Passport No.</div>
                        <div class="rc-value"><?php echo htmlspecialchars($student['passport']); ?></div>
                    </div>
                    <div class="rc-row">
                        <div class="rc-label">Nationality</div>
                        <div class="rc-value"><?php echo htmlspecialchars($student['nationality']); ?></div>
                    </div>
                    <div class="rc-row">
                        <div class="rc-label">Study Program</div>
                        <div class="rc-value"><?php echo htmlspecialchars($student['program1']); ?></div>
                    </div>
                    <div class="rc-row">
                        <div class="rc-label">Registration Date</div>
                        <div class="rc-value"><?php echo date('d F Y', strtotime($student['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="rc-footer">
                <p>This card is automatically generated by the Poltekkes Bengkulu OIA System.</p>
                <p>Please print this card and present it along with your original passport when requested.</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
