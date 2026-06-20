<?php
/**
 * cek_status.php
 * Helper untuk mengecek status buka/tutup pendaftaran.
 * Wajib di-include SETELAH koneksi.php (membutuhkan variabel $conn).
 *
 * Menyediakan variabel:
 *  - $status_manual      : 'buka' atau 'tutup' (diset manual oleh admin)
 *  - $waktu_tutup_str    : string waktu tutup otomatis, format 'Y-m-d H:i:s'
 *  - $pendaftaran_tutup  : boolean, TRUE jika pendaftaran harus dianggap tutup
 *  - $selisih_detik      : jumlah detik tersisa sampai waktu_tutup (bisa negatif)
 */

function getSettingValue($conn, $nama_setting, $default = null)
{
    $nama_setting_esc = mysqli_real_escape_string($conn, $nama_setting);
    $result = mysqli_query($conn, "SELECT nilai FROM tb_settings WHERE nama_setting = '$nama_setting_esc' LIMIT 1");
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['nilai'];
    }
    return $default;
}

function updateSettingValue($conn, $nama_setting, $nilai_baru)
{
    $nama_setting_esc = mysqli_real_escape_string($conn, $nama_setting);
    $nilai_baru_esc   = mysqli_real_escape_string($conn, $nilai_baru);
    return mysqli_query($conn, "UPDATE tb_settings SET nilai = '$nilai_baru_esc' WHERE nama_setting = '$nama_setting_esc'");
}

$status_manual   = getSettingValue($conn, 'status_pendaftaran', 'buka'); // default aman: buka
$waktu_tutup_str  = getSettingValue($conn, 'waktu_tutup', null);

$pendaftaran_tutup = false;
$selisih_detik      = null;

if ($status_manual === 'tutup') {
    // Admin menutup manual -> langsung tutup, tidak peduli jam
    $pendaftaran_tutup = true;
} elseif (!empty($waktu_tutup_str)) {
    try {
        $sekarang    = new DateTime('now');
        $batas_tutup = new DateTime($waktu_tutup_str);
        $selisih_detik = $batas_tutup->getTimestamp() - $sekarang->getTimestamp();

        if ($selisih_detik <= 0) {
            $pendaftaran_tutup = true;
        }
    } catch (Exception $e) {
        // Jika format tanggal tidak valid, anggap tidak ada batas waktu (tetap mengikuti status manual)
        $pendaftaran_tutup = false;
    }
}
?>
