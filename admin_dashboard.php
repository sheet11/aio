<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

// Check connection
$db_error = "";
if (isset($db_connection_error)) {
    $db_error = $db_connection_error;
}

// ============================================
// Handle DELETE request
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $applicant_id = intval($_POST['id']);

    if (empty($db_error) && $applicant_id > 0) {
        $delete_sql = "DELETE FROM tb_interstudent WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $delete_sql)) {
            mysqli_stmt_bind_param($stmt, "i", $applicant_id);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to refresh the page and show success
                header("Location: admin_dashboard.php");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ============================================
// Handle UPDATE request
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $applicant_id = intval($_POST['id']);

    // Collect all updateable fields
    $update_fields = [];
    $update_params = [];
    $types = "";

    $updateable_fields = [
        'first_name',
        'last_name',
        'dob',
        'gender',
        'nationality',
        'passport',
        'email',
        'phone',
        'current_location',
        'education_level',
        'gpa',
        'previous_school',
        'program1',
        'english_proficiency',
        'sop',
        'referral'
    ];

    foreach ($updateable_fields as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            $update_fields[] = "`$field` = ?";
            $update_params[] = $_POST[$field];
            $types .= "s";
        }
    }

    if (!empty($update_fields) && empty($db_error) && $applicant_id > 0) {
        $update_sql = "UPDATE tb_interstudent SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $update_params[] = $applicant_id;
        $types .= "i";

        if ($stmt = mysqli_prepare($conn, $update_sql)) {
            mysqli_stmt_bind_param($stmt, $types, ...$update_params);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: admin_dashboard.php");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Initialize variables for filtering & search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_nationality = isset($_GET['nationality']) ? trim($_GET['nationality']) : '';
$filter_program = isset($_GET['program']) ? trim($_GET['program']) : '';
$filter_education = isset($_GET['education']) ? trim($_GET['education']) : '';

// 1. Fetch statistics (independent of search/filter)
$total_applicants = 0;
$total_countries = 0;
$today_registrations = 0;
$popular_program = "N/A";

if (empty($db_error)) {
    // Total applicants
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_interstudent");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_applicants = $row['cnt'];
    }

    // Total countries represented
    $res = mysqli_query($conn, "SELECT COUNT(DISTINCT nationality) as cnt FROM tb_interstudent");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_countries = $row['cnt'];
    }

    // Today's registrations
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_interstudent WHERE DATE(created_at) = CURDATE()");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $today_registrations = $row['cnt'];
    }

    // Popular program
    $res = mysqli_query($conn, "SELECT program1, COUNT(*) as cnt FROM tb_interstudent GROUP BY program1 ORDER BY cnt DESC LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $popular_program = ($row['program1'] == 'Bachelor Promosi Kesehatan') ? 'Bachelor Health Promo' : $row['program1'];
    }

    // Dynamic lists for filter dropdowns
    $nationalities_list = [];
    $res = mysqli_query($conn, "SELECT DISTINCT nationality FROM tb_interstudent ORDER BY nationality ASC");
    while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['nationality'])) $nationalities_list[] = $row['nationality'];
    }

    $programs_list = [];
    $res = mysqli_query($conn, "SELECT DISTINCT program1 FROM tb_interstudent ORDER BY program1 ASC");
    while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['program1'])) $programs_list[] = $row['program1'];
    }

    // 2. Fetch country breakdown analytics
    $country_breakdown = [];
    $res = mysqli_query($conn, "SELECT nationality, COUNT(*) as cnt FROM tb_interstudent GROUP BY nationality ORDER BY cnt DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        $country_breakdown[] = $row;
    }
}

// 3. Build query based on filters & search
$where_clauses = [];
$params = [];
$types = "";

if ($search !== '') {
    $where_clauses[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR passport LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

if ($filter_nationality !== '') {
    $where_clauses[] = "nationality = ?";
    $params[] = $filter_nationality;
    $types .= "s";
}

if ($filter_program !== '') {
    $where_clauses[] = "program1 = ?";
    $params[] = $filter_program;
    $types .= "s";
}

if ($filter_education !== '') {
    $where_clauses[] = "education_level = ?";
    $params[] = $filter_education;
    $types .= "s";
}

$sql = "SELECT * FROM tb_interstudent";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY created_at DESC";

$applicants = [];
if (empty($db_error)) {
    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (count($params) > 0) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $applicants[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Poltekkes Bengkulu</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #008080;
            --primary-dark: #006666;
            --primary-light: #e6f2f2;
            --secondary: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --text-light: #94a3b8;
            --white: #ffffff;
            --success: #10b981;
            --border-color: #e2e8f0;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            --transition-all: all 0.25s ease;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Admin Navbar */
        .admin-nav {
            background-color: #0f172a;
            color: var(--white);
            padding: 1rem 4%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-nav .brand {
            font-size: 1.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--white);
            text-decoration: none;
        }

        .admin-nav .brand i {
            color: #38bdf8;
        }

        .admin-nav .brand span {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 600;
            border-left: 1px solid #334155;
            padding-left: 10px;
            margin-left: 5px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .admin-info-text {
            text-align: right;
        }

        .admin-info-text h4 {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .admin-info-text p {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .btn-logout {
            background-color: #e2e8f0;
            color: #0f172a;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background-color: #f1f5f9;
            color: #e11d48;
        }

        /* Main Dashboard Panel */
        .dashboard-container {
            padding: 2.5rem 4%;
            flex-grow: 1;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
        }

        /* DB Connection Error Box */
        .db-error-alert {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 1.25rem;
            border-radius: var(--border-radius-md);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Metrics Statistics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .metric-card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition-all);
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .metric-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .metric-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-blue {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background-color: #dcfce7;
            color: #15803d;
        }

        .icon-purple {
            background-color: #f3e8ff;
            color: #7e22ce;
        }

        .icon-orange {
            background-color: #ffedd5;
            color: #c2410c;
        }

        /* Dashboard Split Layout */
        .dashboard-split {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 2rem;
        }

        <blade media|%20(max-width%3A%201024px)%20%7B>.dashboard-split {
            grid-template-columns: 1fr;
        }
        }

        /* Country Breakdown Card */
        .breakdown-card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            height: fit-content;
        }

        .breakdown-card h3 {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .breakdown-card h3 i {
            color: var(--primary);
        }

        .country-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .country-item {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .country-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .country-name {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dark);
        }

        .country-count {
            color: var(--primary-dark);
            background-color: var(--primary-light);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .country-bar-bg {
            background-color: #f1f5f9;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
        }

        .country-bar-fill {
            background-color: var(--primary);
            height: 100%;
            border-radius: 3px;
            transition: var(--transition-all);
        }

        /* Main Data Card with Filter and Table */
        .data-card {
            background-color: var(--white);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Control Panel: Filters & Search */
        .control-panel {
            background-color: #fafbfc;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: var(--border-radius-md);
        }

        .filter-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        <blade media|%20(max-width%3A%20900px)%20%7B>.filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .filter-form .form-group-btn {
            grid-column: span 2;
        }
        }

        <blade media|%20(max-width%3A%20500px)%20%7B>.filter-form {
            grid-template-columns: 1fr;
        }

        .filter-form .form-group-btn {
            grid-column: span 1;
        }
        }

        .filter-form .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .filter-form label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .filter-form .form-control {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: var(--white);
        }

        .filter-form .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-filter-action {
            display: flex;
            gap: 0.5rem;
        }

        .btn-submit-filter {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-filter:hover {
            background-color: var(--primary-dark);
        }

        .btn-reset-filter {
            background-color: #e2e8f0;
            color: var(--text-dark);
            border: none;
            padding: 0.65rem 1rem;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-reset-filter:hover {
            background-color: #cbd5e1;
        }

        /* Top Actions Bar */
        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .table-title p {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 0.15rem;
        }

        .btn-export-csv {
            background-color: #10b981;
            color: var(--white);
            text-decoration: none;
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 700;
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);
        }

        .btn-export-csv:hover {
            background-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        /* Table Responsive Wrapper */
        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            padding: 1rem;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 1.1rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #fafbfc;
        }

        /* Custom cell styles */
        .cell-bold {
            color: var(--text-dark);
            font-weight: 700;
        }

        .cell-avatar {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
        }

        .badge-program {
            background-color: #f0fdf4;
            color: #166534;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #bbf7d0;
            display: inline-block;
        }

        .badge-country {
            background-color: #eff6ff;
            color: #1e40af;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #bfdbfe;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-details {
            background: none;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 0.4rem 0.85rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 700;
            transition: var(--transition-all);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view-details:hover {
            background-color: var(--primary);
            color: var(--white);
        }

        .empty-table-state {
            text-align: center;
            padding: 3rem !important;
            color: var(--text-light);
            font-size: 1rem;
        }

        .empty-table-state i {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        /* Modal Overlay & Drawer */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition-all);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background-color: var(--white);
            border-radius: var(--border-radius-lg);
            width: 90%;
            max-width: 750px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
            transform: scale(0.95);
            transition: cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.open .modal-card {
            transform: scale(1);
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background-color: var(--white);
            z-index: 10;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-header h3 i {
            color: var(--primary);
        }

        .btn-modal-close {
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition-all);
        }

        .btn-modal-close:hover {
            color: #ef4444;
        }

        .modal-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .modal-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .modal-section-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3px solid var(--primary);
            padding-left: 10px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        <blade media|%20(max-width%3A%20600px)%20%7B>.modal-grid {
            grid-template-columns: 1fr;
        }
        }

        .modal-field h5 {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-light);
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }

        .modal-field p {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .sop-blockquote {
            background-color: var(--secondary);
            border-left: 4px solid var(--primary);
            padding: 1.25rem;
            border-radius: 0 8px 8px 0;
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            font-style: italic;
            white-space: pre-line;
        }
    </style>
</head>

<body>

    <!-- Admin Top Nav -->
    <nav class="admin-nav">
        <a href="admin_dashboard.php" class="brand">
            <i class="fa-solid fa-gauge-high"></i> Poltekkes Bengkulu <span>OIA Portal</span>
        </a>
        <div class="admin-profile">
            <div class="admin-info-text">
                <h4>Administrator</h4>
                <p>OIA Admissions Desk</p>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="dashboard-container">

        <!-- DB Error Display -->
        <?php if (!empty($db_error)): ?>
        <div class="db-error-alert">
            <i class="fa-solid fa-circle-exclaim" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Database Connection Error!</strong><br>
                <?php echo htmlspecialchars($db_error); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Panel -->
        <div class="metrics-grid">
            <!-- Metric 1: Total Applicants -->
            <div class="metric-card">
                <div class="metric-info">
                    <h3><?php echo $total_applicants; ?></h3>
                    <p>Total Applications</p>
                </div>
                <div class="metric-icon icon-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <!-- Metric 2: Total Countries -->
            <div class="metric-card">
                <div class="metric-info">
                    <h3><?php echo $total_countries; ?></h3>
                    <p>Countries Represented</p>
                </div>
                <div class="metric-icon icon-green">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
            </div>

            <!-- Metric 3: Today's Registrants -->
            <div class="metric-card">
                <div class="metric-info">
                    <h3><?php echo $today_registrations; ?></h3>
                    <p>New Today</p>
                </div>
                <div class="metric-icon icon-purple">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </div>

            <!-- Metric 4: Popular Program -->
            <div class="metric-card">
                <div class="metric-info">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-top: 5px; color: var(--text-dark); max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                        title="<?php echo htmlspecialchars($popular_program); ?>">
                        <?php echo htmlspecialchars($popular_program); ?>
                    </h3>
                    <p style="margin-top: 5px;">Top Study Program</p>
                </div>
                <div class="metric-icon icon-orange">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
        </div>

        <!-- Dashboard Split Layout -->
        <div class="dashboard-split">

            <!-- LEFT Panel: Country Breakdown -->
            <div class="breakdown-card">
                <h3><i class="fa-solid fa-chart-pie"></i> Applicants by Country</h3>
                <div class="country-list">
                    <?php if (count($country_breakdown) > 0): ?>
                    <?php
                        foreach ($country_breakdown as $country_item):
                            $percentage = ($total_applicants > 0) ? round(($country_item['cnt'] / $total_applicants) * 100) : 0;
                            $flag_icon = "fa-globe"; // fallback
                        ?>
                    <div class="country-item">
                        <div class="country-info-row">
                            <span class="country-name">
                                <i class="fa-solid fa-map-pin" style="color: var(--primary); font-size: 0.8rem;"></i>
                                <?php echo htmlspecialchars($country_item['nationality']); ?>
                            </span>
                            <span class="country-count"><?php echo $country_item['cnt']; ?></span>
                        </div>
                        <div class="country-bar-bg" title="<?php echo $percentage; ?>%">
                            <div class="country-bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div style="color: var(--text-light); text-align: center; padding: 1.5rem 0; font-size: 0.85rem;">
                        No country data.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT Panel: Registrant Database List -->
            <div class="data-card">

                <!-- Filters Control -->
                <div class="control-panel">
                    <form action="admin_dashboard.php" method="GET" class="filter-form">

                        <!-- Search term -->
                        <div class="form-group">
                            <label for="search">Keyword Search</label>
                            <input type="text" id="search" name="search" class="form-control"
                                placeholder="Name, Email, Passport..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- Nationality filter -->
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <select id="nationality" name="nationality" class="form-control">
                                <option value="">— All —</option>
                                <?php foreach ($nationalities_list as $nat): ?>
                                <option value="<?php echo htmlspecialchars($nat); ?>"
                                    <?php echo ($filter_nationality === $nat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($nat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Program filter -->
                        <div class="form-group">
                            <label for="program">Program</label>
                            <select id="program" name="program" class="form-control">
                                <option value="">— All —</option>
                                <?php foreach ($programs_list as $prog): ?>
                                <option value="<?php echo htmlspecialchars($prog); ?>"
                                    <?php echo ($filter_program === $prog) ? 'selected' : ''; ?>>
                                    <?php echo ($prog == 'Bachelor Promosi Kesehatan') ? 'Bachelor Health Promotion' : htmlspecialchars($prog); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Education filter -->
                        <div class="form-group">
                            <label for="education">Education</label>
                            <select id="education" name="education" class="form-control">
                                <option value="">— All —</option>
                                <option value="High School / Senior Secondary"
                                    <?php echo ($filter_education === 'High School / Senior Secondary') ? 'selected' : ''; ?>>
                                    High School</option>
                                <option value="Diploma (D-I / D-II / D-III)"
                                    <?php echo ($filter_education === 'Diploma (D-I / D-II / D-III)') ? 'selected' : ''; ?>>
                                    Diploma</option>
                                <option value="Bachelor's Degree (S-1 / D-IV)"
                                    <?php echo ($filter_education === "Bachelor's Degree (S-1 / D-IV)") ? 'selected' : ''; ?>>
                                    Bachelor</option>
                                <option value="Master's Degree (S-2)"
                                    <?php echo ($filter_education === "Master's Degree (S-2)") ? 'selected' : ''; ?>>
                                    Master</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="btn-filter-action form-group-btn">
                            <button type="submit" class="btn-submit-filter">
                                <i class="fa-solid fa-magnifying-glass"></i> Filter
                            </button>
                            <?php if ($search !== '' || $filter_nationality !== '' || $filter_program !== '' || $filter_education !== ''): ?>
                            <a href="admin_dashboard.php" class="btn-reset-filter" title="Clear Filters">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Table Headers / Export -->
                <div class="table-header-row">
                    <div class="table-title">
                        <h2>Registrant Listing</h2>
                        <p>Found <?php echo count($applicants); ?> applicant records</p>
                    </div>
                    <?php if (count($applicants) > 0): ?>
                    <a href="admin_export.php?format=xls&search=<?php echo urlencode($search); ?>&nationality=<?php echo urlencode($filter_nationality); ?>&program=<?php echo urlencode($filter_program); ?>&education=<?php echo urlencode($filter_education); ?>"
                        class="btn-export-csv" title="Export current list to XLS file">
                        <i class="fa-solid fa-file-excel"></i> Export Report (XLS)
                    </a>
                    <a href="admin_export.php?format=xls" class="btn-export-csv"
                        title="Export all applicants to XLS file" style="margin-left:0.75rem;">
                        <i class="fa-solid fa-file-excel"></i> Export All (XLS)
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Table Wrapper -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Nationality</th>
                                <th>Study Program</th>
                                <th>GPA</th>
                                <th>Passport No.</th>
                                <th>Registration Date</th>
                                <th>Documents</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($applicants) > 0): ?>
                            <?php foreach ($applicants as $applicant):
                                    $first_initial = strtoupper(substr($applicant['first_name'], 0, 1));
                                    $last_initial = strtoupper(substr($applicant['last_name'], 0, 1));
                                    $initials = $first_initial . $last_initial;
                                ?>
                            <tr>
                                <td>
                                    <div class="cell-avatar">
                                        <div class="avatar-circle"><?php echo $initials; ?></div>
                                        <div>
                                            <span class="cell-bold">
                                                <?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']); ?>
                                            </span>
                                            <div style="font-size:0.75rem;color:var(--text-light);">
                                                <?php echo htmlspecialchars($applicant['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-country">
                                        <i class="fa-solid fa-globe"></i>
                                        <?php echo htmlspecialchars($applicant['nationality']); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge-program">
                                        <?php echo ($applicant['program1'] == 'Bachelor Promosi Kesehatan')
                                                    ? 'Bachelor Health Promotion'
                                                    : htmlspecialchars($applicant['program1']); ?>
                                    </span>
                                </td>

                                <td class="cell-bold">
                                    <?php echo !empty($applicant['gpa']) ? htmlspecialchars($applicant['gpa']) : '—'; ?>
                                </td>

                                <td>
                                    <code><?php echo htmlspecialchars($applicant['passport']); ?></code>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($applicant['created_at'])); ?>
                                </td>

                                <!-- KOLOM DOKUMEN -->
                                <td>
                                    <div style="display:flex;gap:4px;flex-wrap:wrap;">

                                        <?php if (!empty($applicant['passport_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['passport_file']); ?>"
                                            target="_blank" title="Passport" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-passport"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['english_cert_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['english_cert_file']); ?>"
                                            target="_blank" title="English Certificate" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-language"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['diploma_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['diploma_file']); ?>"
                                            target="_blank" title="Diploma" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['transcript_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['transcript_file']); ?>"
                                            target="_blank" title="Transcript" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['photo_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['photo_file']); ?>"
                                            target="_blank" title="Photo" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-image"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['cv_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['cv_file']); ?>" target="_blank"
                                            title="CV" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-file-signature"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['letter_rec_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['letter_rec_file']); ?>"
                                            target="_blank" title="Letter of Rec." class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-envelope-open-text"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (!empty($applicant['statement_file'])): ?>
                                        <a href="<?php echo htmlspecialchars($applicant['statement_file']); ?>"
                                            target="_blank" title="Statement" class="btn-view-details"
                                            style="padding:4px 8px;font-size:0.75rem;">
                                            <i class="fa-solid fa-file-contract"></i>
                                        </a>
                                        <?php endif; ?>

                                    </div>
                                </td>

                                <!-- KOLOM AKSI -->
                                <td style="text-align:center;">
                                    <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                                        <button type="button" class="btn-view-details"
                                            onclick="openDetails(<?php echo $applicant['id']; ?>)" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn-view-details"
                                            style="border-color:#f59e0b;color:#f59e0b;"
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($applicant), ENT_QUOTES, 'UTF-8'); ?>)"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" class="btn-view-details"
                                            style="border-color:#ef4444;color:#ef4444;"
                                            onclick="deleteApplicant(<?php echo $applicant['id']; ?>)" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-table-state">
                                    <i class="fa-solid fa-folder-open"></i>
                                    No applicant records found matching your filters.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- Applicant Detail Modal (Blur Backdrop Popup) -->
    <div class="modal-overlay" id="detailModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-id-card"></i> Candidate Application Dossier</h3>
                <button class="btn-modal-close" onclick="closeDetails()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">

                <!-- Section 1: Personal Details -->
                <div class="modal-section">
                    <div class="modal-section-title">Personal Details</div>
                    <div class="modal-grid">
                        <div class="modal-field">
                            <h5>Full Name</h5>
                            <p id="modal_full_name">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Date of Birth</h5>
                            <p id="modal_dob">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Gender</h5>
                            <p id="modal_gender">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Nationality & Origin</h5>
                            <p id="modal_nationality">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Passport Number</h5>
                            <p><code id="modal_passport">—</code></p>
                        </div>
                        <div class="modal-field">
                            <h5>Current Residence</h5>
                            <p id="modal_current_location">—</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact Details -->
                <div class="modal-section">
                    <div class="modal-section-title">Contact Information</div>
                    <div class="modal-grid">
                        <div class="modal-field">
                            <h5>Email Address</h5>
                            <p id="modal_email">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>WhatsApp / Phone</h5>
                            <p id="modal_phone">—</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Academic History -->
                <div class="modal-section">
                    <div class="modal-section-title">Academic Credentials</div>
                    <div class="modal-grid">
                        <div class="modal-field">
                            <h5>Previous School / University</h5>
                            <p id="modal_previous_school">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Highest Education</h5>
                            <p id="modal_education_level">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Cumulative GPA / Grade</h5>
                            <p id="modal_gpa">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>English Proficiency Level</h5>
                            <p id="modal_english">—</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Study Choice -->
                <div class="modal-section">
                    <div class="modal-section-title">Chosen Program & Referrals</div>
                    <div class="modal-grid">
                        <div class="modal-field">
                            <h5>Chosen Course of Study</h5>
                            <p id="modal_program">—</p>
                        </div>
                        <div class="modal-field">
                            <h5>Referral Channel</h5>
                            <p id="modal_referral">—</p>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Statement of Purpose -->
                <div class="modal-section">
                    <div class="modal-section-title">Statement of Purpose</div>
                    <blockquote class="sop-blockquote" id="modal_sop">—</blockquote>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Applicant Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-pen-to-square"></i> Edit Applicant Record</h3>
                <button class="btn-modal-close" type="button" onclick="closeEditModal()"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="editForm" method="POST" style="display:flex;flex-direction:column;height:100%;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_applicant_id">

                <div class="modal-body" style="flex-grow:1;overflow-y:auto;">
                    <!-- Personal Details -->
                    <div class="modal-section">
                        <div class="modal-section-title">Personal Details</div>
                        <div class="modal-grid">
                            <div class="modal-field">
                                <h5>First Name</h5>
                                <input type="text" name="first_name" id="edit_first_name" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field">
                                <h5>Last Name</h5>
                                <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field">
                                <h5>Date of Birth</h5>
                                <input type="date" name="dob" id="edit_dob" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field">
                                <h5>Gender</h5>
                                <select name="gender" id="edit_gender" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                                    <option value="">— Select —</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                            <div class="modal-field">
                                <h5>Nationality</h5>
                                <input type="text" name="nationality" id="edit_nationality" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field">
                                <h5>Passport Number</h5>
                                <input type="text" name="passport" id="edit_passport" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="modal-section">
                        <div class="modal-section-title">Contact Information</div>
                        <div class="modal-grid">
                            <div class="modal-field">
                                <h5>Email Address</h5>
                                <input type="email" name="email" id="edit_email" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field">
                                <h5>WhatsApp / Phone</h5>
                                <input type="tel" name="phone" id="edit_phone" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field" style="grid-column:span 2;">
                                <h5>Current Location</h5>
                                <input type="text" name="current_location" id="edit_current_location"
                                    class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- Academic -->
                    <div class="modal-section">
                        <div class="modal-section-title">Academic Credentials</div>
                        <div class="modal-grid">
                            <div class="modal-field">
                                <h5>Education Level</h5>
                                <select name="education_level" id="edit_education_level" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                                    <option value="">— Select —</option>
                                    <option value="High School / Senior Secondary">High School</option>
                                    <option value="Diploma (D-I / D-II / D-III)">Diploma</option>
                                    <option value="Bachelor's Degree (S-1 / D-IV)">Bachelor</option>
                                    <option value="Master's Degree (S-2)">Master</option>
                                </select>
                            </div>
                            <div class="modal-field">
                                <h5>GPA / Final Grade</h5>
                                <input type="text" name="gpa" id="edit_gpa" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                            <div class="modal-field" style="grid-column:span 2;">
                                <h5>Previous School / University</h5>
                                <input type="text" name="previous_school" id="edit_previous_school" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- Program & Language -->
                    <div class="modal-section">
                        <div class="modal-section-title">Program & Language</div>
                        <div class="modal-grid">
                            <div class="modal-field">
                                <h5>Study Program</h5>
                                <select name="program1" id="edit_program1" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                                    <option value="Bachelor Promosi Kesehatan">Bachelor of Health Promotion</option>
                                </select>
                            </div>
                            <div class="modal-field">
                                <h5>English Proficiency</h5>
                                <select name="english_proficiency" id="edit_english_proficiency" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                                    <option value="">— Select —</option>
                                    <option value="IELTS 5.0–5.5">IELTS 5.0–5.5</option>
                                    <option value="IELTS 6.0+">IELTS 6.0+</option>
                                    <option value="TOEFL ITP 500–549">TOEFL ITP 500–549</option>
                                    <option value="TOEFL ITP 550+">TOEFL ITP 550+</option>
                                    <option value="Other Certificate">Other Certificate</option>
                                    <option value="No Certificate (applying for waiver)">No Certificate</option>
                                </select>
                            </div>
                            <div class="modal-field" style="grid-column:span 2;">
                                <h5>How did they hear about us?</h5>
                                <input type="text" name="referral" id="edit_referral" class="form-control"
                                    style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;width:100%;">
                            </div>
                        </div>
                    </div>

                    <!-- Statement of Purpose -->
                    <div class="modal-section">
                        <div class="modal-section-title">Statement of Purpose</div>
                        <textarea name="sop" id="edit_sop" class="form-control"
                            style="padding:8px;border:1px solid #cbd5e1;border-radius:6px;min-height:120px;resize:vertical;width:100%;"></textarea>
                    </div>
                </div>

                <div
                    style="padding:2rem;border-top:1px solid var(--border-color);display:flex;gap:1rem;justify-content:flex-end;">
                    <button type="button" class="btn-reset-filter" onclick="closeEditModal()" style="margin:0;">
                        <i class="fa-solid fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-submit-filter" style="background-color:var(--primary);margin:0;">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Client-side applicant database for instant details loader -->
    <script>
        // Convert PHP Array of applicants to client JSON
        const applicantsData = < ? php echo json_encode(array_column($applicants, null, 'id'), JSON_HEX_TAG |
            JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ? > ;

        const modal = document.getElementById('detailModal');

        function openDetails(id) {
            const data = applicantsData[id];
            if (!data) return;

            // Populate personal details
            document.getElementById('modal_full_name').innerText = data.first_name + ' ' + data.last_name;
            document.getElementById('modal_dob').innerText = formatDate(data.dob);
            document.getElementById('modal_gender').innerText = data.gender;
            document.getElementById('modal_nationality').innerText = data.nationality;
            document.getElementById('modal_passport').innerText = data.passport;
            document.getElementById('modal_current_location').innerText = data.current_location ? data
                .current_location : '—';

            // Populate contact details
            document.getElementById('modal_email').innerText = data.email;
            document.getElementById('modal_phone').innerText = data.phone;

            // Populate academic
            document.getElementById('modal_previous_school').innerText = data.previous_school;
            document.getElementById('modal_education_level').innerText = data.education_level;
            document.getElementById('modal_gpa').innerText = data.gpa ? data.gpa : '—';
            document.getElementById('modal_english').innerText = data.english_proficiency ? data.english_proficiency :
                '—';

            // Populate choices
            document.getElementById('modal_program').innerText = data.program1 === 'Bachelor Promosi Kesehatan' ?
                'Bachelor of Health Promotion' : data.program1;
            document.getElementById('modal_referral').innerText = data.referral ? data.referral : '—';

            // Populate SOP
            document.getElementById('modal_sop').innerText = data.sop;

            // Show Modal
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDetails() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        // Helper Date Formatter
        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateStr).toLocaleDateString('en-US', options);
        }

        // Close modal when clicking on overlay background
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeDetails();
            }
        });

        // ============================================
        // EDIT FUNCTIONALITY
        // ============================================
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');

        function openEditModal(data) {
            // Populate all form fields
            document.getElementById('edit_applicant_id').value = data.id;
            document.getElementById('edit_first_name').value = data.first_name || '';
            document.getElementById('edit_last_name').value = data.last_name || '';
            document.getElementById('edit_dob').value = data.dob || '';
            document.getElementById('edit_gender').value = data.gender || '';
            document.getElementById('edit_nationality').value = data.nationality || '';
            document.getElementById('edit_passport').value = data.passport || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_current_location').value = data.current_location || '';
            document.getElementById('edit_education_level').value = data.education_level || '';
            document.getElementById('edit_gpa').value = data.gpa || '';
            document.getElementById('edit_previous_school').value = data.previous_school || '';
            document.getElementById('edit_program1').value = data.program1 || 'Bachelor Promosi Kesehatan';
            document.getElementById('edit_english_proficiency').value = data.english_proficiency || '';
            document.getElementById('edit_referral').value = data.referral || '';
            document.getElementById('edit_sop').value = data.sop || '';

            // Show edit modal
            editModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            editModal.classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close edit modal when clicking on overlay background
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) {
                closeEditModal();
            }
        });

        // Handle form submission
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            editForm.submit();
        });

        // ============================================
        // DELETE FUNCTIONALITY
        // ============================================
        function deleteApplicant(id) {
            if (confirm('Are you sure you want to delete this applicant? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;

                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>