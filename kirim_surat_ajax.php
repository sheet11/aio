<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

header('Content-Type: application/json');
require_once 'koneksi.php';
require_once 'mailer_config.php';
require_once 'surat_template.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID tidak valid']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, first_name, last_name, email, status_seleksi FROM tb_interstudent WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Peserta tidak ditemukan']);
    exit;
}
if ($row['status_seleksi'] !== 'tidak_lulus') {
    echo json_encode(['ok' => false, 'error' => 'Status peserta bukan tidak_lulus']);
    exit;
}
if (empty($row['email'])) {
    echo json_encode(['ok' => false, 'error' => 'Email kosong']);
    exit;
}

$subject = suratSubjectTidakLulus();
$body = buatIsiSuratTidakLulus($row['first_name'], $row['last_name']);
$sendResult = kirimEmailTidakLulus($row['email'], $row['first_name'] . ' ' . $row['last_name'], $subject, $body);

if ($sendResult['ok']) {
    $upd = mysqli_prepare($conn, "UPDATE tb_interstudent SET surat_status='terkirim', surat_terkirim_at=NOW(), surat_error=NULL WHERE id=?");
    mysqli_stmt_bind_param($upd, "i", $id);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);
    echo json_encode(['ok' => true]);
} else {
    $upd = mysqli_prepare($conn, "UPDATE tb_interstudent SET surat_status='gagal', surat_error=? WHERE id=?");
    mysqli_stmt_bind_param($upd, "si", $sendResult['error'], $id);
    mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);
    echo json_encode(['ok' => false, 'error' => $sendResult['error']]);
}
