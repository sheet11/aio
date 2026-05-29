<?php
session_start();

// Verify session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied.");
}

require_once 'koneksi.php';

if (isset($db_connection_error)) {
    exit("Database connection error: " . $db_connection_error);
}

// Read filters matching the dashboard
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_nationality = isset($_GET['nationality']) ? trim($_GET['nationality']) : '';
$filter_program = isset($_GET['program']) ? trim($_GET['program']) : '';
$filter_education = isset($_GET['education']) ? trim($_GET['education']) : '';

// Build dynamic query
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

// Configure CSV headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=registrants_report_' . date('Y-m-d_His') . '.csv');

// Open the output stream
$output = fopen('php://output', 'w');

// Output UTF-8 BOM to make Excel read unicode accents properly
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV header row
fputcsv($output, [
    'ID',
    'First Name',
    'Last Name',
    'Date of Birth',
    'Gender',
    'Nationality',
    'Passport Number',
    'Email Address',
    'Phone / WhatsApp',
    'Current Location',
    'Education Level',
    'GPA',
    'Previous School',
    'Study Program',
    'English Proficiency',
    'Statement of Purpose',
    'Referral Channel',
    'Registered Date'
]);

// Write applicant records
foreach ($applicants as $row) {
    fputcsv($output, [
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['dob'],
        $row['gender'],
        $row['nationality'],
        $row['passport'],
        $row['email'],
        $row['phone'],
        $row['current_location'],
        $row['education_level'],
        $row['gpa'],
        $row['previous_school'],
        ($row['program1'] == 'Bachelor Promosi Kesehatan') ? 'Bachelor of Health Promotion' : $row['program1'],
        $row['english_proficiency'],
        $row['sop'],
        $row['referral'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
?>
