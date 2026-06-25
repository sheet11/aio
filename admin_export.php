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

// Configure headers for download
$format = isset($_GET['format']) && strtolower($_GET['format']) === 'csv' ? 'csv' : 'xls';
$filename = 'registrants_report_' . date('Y-m-d_His') . '.' . $format;
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
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
        'Passport File',
        'English Cert File',
        'Diploma File',
        'Transcript File',
        'Photo File',
        'CV File',
        'Letter Rec File',
        'Statement File',
        'Registered Date'
    ]);
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
            '+' . $row['phone'],
            $row['current_location'],
            $row['education_level'],
            $row['gpa'],
            $row['previous_school'],
            ($row['program1'] == 'Bachelor Promosi Kesehatan') ? 'Bachelor of Health Promotion' : $row['program1'],
            $row['english_proficiency'],
            $row['sop'],
            $row['referral'],
            $row['passport_file'] ? getUploadUrl($row['passport_file']) : '',
            $row['english_cert_file'] ? getUploadUrl($row['english_cert_file']) : '',
            $row['diploma_file'] ? getUploadUrl($row['diploma_file']) : '',
            $row['transcript_file'] ? getUploadUrl($row['transcript_file']) : '',
            $row['photo_file'] ? getUploadUrl($row['photo_file']) : '',
            $row['cv_file'] ? getUploadUrl($row['cv_file']) : '',
            $row['letter_rec_file'] ? getUploadUrl($row['letter_rec_file']) : '',
            $row['health_cert_file'] ? getUploadUrl($row['health_cert_file']) : '',
            $row['sponsor_statement_file'] ? getUploadUrl($row['sponsor_statement_file']) : '',
            $row['statement_file'] ? getUploadUrl($row['statement_file']) : '',
            $row['created_at']
        ]);
    }
    fclose($output);
    exit;
}

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
echo "\xEF\xBB\xBF";

function exportValue($value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getUploadUrl(string $path): string
{
    if ($path === '') {
        return '';
    }

    if (parse_url($path, PHP_URL_SCHEME) !== null) {
        return $path;
    }

    $baseUrl = 'https://aio.poltekkesbengkulu.ac.id/';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function exportFileLink(?string $path, string $label): string
{
    if (trim((string) $path) === '') {
        return '';
    }

    $url = getUploadUrl((string) $path);
    return '<a href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" target="_blank">' . htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
}

$headerLabels = [
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
    'Passport File',
    'English Cert File',
    'Diploma File',
    'Transcript File',
    'Photo File',
    'CV File',
    'Letter Rec File',
    'Health Cert File',
    'Sponsor Statement File',
    'Statement File',
    'Registered Date'
];

echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /></head><body>";
echo "<table border=1 cellspacing=0 cellpadding=5>";
echo "<tr style=\"background-color:#f2f2f2;font-weight:bold;\">";
foreach ($headerLabels as $label) {
    echo "<th>" . exportValue($label) . "</th>";
}
echo "</tr>";

foreach ($applicants as $row) {
    echo "<tr>";
    echo "<td>" . exportValue($row['id']) . "</td>";
    echo "<td>" . exportValue($row['first_name']) . "</td>";
    echo "<td>" . exportValue($row['last_name']) . "</td>";
    echo "<td>" . exportValue($row['dob']) . "</td>";
    echo "<td>" . exportValue($row['gender']) . "</td>";
    echo "<td>" . exportValue($row['nationality']) . "</td>";
    echo "<td>" . exportValue($row['passport']) . "</td>";
    echo "<td>" . exportValue($row['email']) . "</td>";
    echo "<td>" . exportValue($row['phone']) . "</td>";
    echo "<td>" . exportValue($row['current_location']) . "</td>";
    echo "<td>" . exportValue($row['education_level']) . "</td>";
    echo "<td>" . exportValue($row['gpa']) . "</td>";
    echo "<td>" . exportValue($row['previous_school']) . "</td>";
    echo "<td>" . exportValue(($row['program1'] == 'Bachelor Promosi Kesehatan') ? 'Bachelor of Health Promotion' : $row['program1']) . "</td>";
    echo "<td>" . exportValue($row['english_proficiency']) . "</td>";
    echo "<td style=\"white-space:nowrap;\">" . exportValue($row['sop']) . "</td>";
    echo "<td>" . exportValue($row['referral']) . "</td>";
    echo "<td>" . exportFileLink($row['passport_file'], 'Passport') . "</td>";
    echo "<td>" . exportFileLink($row['english_cert_file'], 'English Cert') . "</td>";
    echo "<td>" . exportFileLink($row['diploma_file'], 'Diploma') . "</td>";
    echo "<td>" . exportFileLink($row['transcript_file'], 'Transcript') . "</td>";
    echo "<td>" . exportFileLink($row['photo_file'], 'Photo') . "</td>";
    echo "<td>" . exportFileLink($row['cv_file'], 'CV') . "</td>";
    echo "<td>" . exportFileLink($row['letter_rec_file'], 'Letter Rec') . "</td>";
    echo "<td>" . exportFileLink($row['health_cert_file'], 'Health Cert') . "</td>";
    echo "<td>" . exportFileLink($row['sponsor_statement_file'], 'Sponsor Statement') . "</td>";
    echo "<td>" . exportFileLink($row['statement_file'], 'Statement') . "</td>";
    echo "<td>" . exportValue($row['created_at']) . "</td>";
    echo "</tr>";
}

echo "</table></body></html>";
exit;