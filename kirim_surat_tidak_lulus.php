<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';
require_once 'surat_template.php';

$db_error = isset($db_connection_error) ? $db_connection_error : "";
$message = "";
$message_type = "";

// ============================================
// Tandai status seleksi (lulus / tidak lulus / pending) + keterangan
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_status') {
    $id = intval($_POST['id']);
    $status = $_POST['status_seleksi'];
    $keterangan = trim($_POST['keterangan_seleksi'] ?? '');

    if (!in_array($status, ['pending', 'lulus', 'tidak_lulus'])) {
        $status = 'pending';
    }

    if (empty($db_error) && $id > 0) {
        // Reset status surat kalau diubah jadi bukan tidak_lulus
        if ($status !== 'tidak_lulus') {
            $stmt = mysqli_prepare($conn, "UPDATE tb_interstudent SET status_seleksi=?, keterangan_seleksi=?, surat_status='belum', surat_terkirim_at=NULL, surat_error=NULL WHERE id=?");
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE tb_interstudent SET status_seleksi=?, keterangan_seleksi=? WHERE id=?");
        }
        mysqli_stmt_bind_param($stmt, "ssi", $status, $keterangan, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: kirim_surat_tidak_lulus.php?updated=1");
        exit;
    }
}

if (isset($_GET['updated'])) {
    $message = "Status peserta berhasil diperbarui.";
    $message_type = "success";
}

// ============================================
// Ambil daftar peserta TIDAK LULUS
// ============================================
$peserta = [];
if (empty($db_error)) {
    $res = mysqli_query($conn, "SELECT id, first_name, last_name, email, keterangan_seleksi, surat_status, surat_terkirim_at, surat_error
                                 FROM tb_interstudent
                                 WHERE status_seleksi = 'tidak_lulus'
                                 ORDER BY surat_status ASC, last_name ASC");
    while ($row = mysqli_fetch_assoc($res)) {
        $peserta[] = $row;
    }
}

$total_tidak_lulus = count($peserta);
$total_belum_kirim = count(array_filter($peserta, fn($p) => $p['surat_status'] === 'belum'));
$total_terkirim = count(array_filter($peserta, fn($p) => $p['surat_status'] === 'terkirim'));
$total_gagal = count(array_filter($peserta, fn($p) => $p['surat_status'] === 'gagal'));

// Semua peserta (untuk form "tandai tidak lulus")
$semua_peserta = [];
if (empty($db_error)) {
    $res2 = mysqli_query($conn, "SELECT id, first_name, last_name, email, status_seleksi FROM tb_interstudent ORDER BY last_name ASC");
    while ($row = mysqli_fetch_assoc($res2)) {
        $semua_peserta[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kirim Surat Tidak Lulus - Poltekkes Bengkulu OIA Portal</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    :root { --primary:#2563eb; --border-radius-md:10px; --text-light:#64748b; }
    body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; }
    .admin-nav { display:flex; align-items:center; justify-content:space-between; padding:1rem 2rem; background:#fff; border-bottom:1px solid #e5e7eb; }
    .admin-nav .brand { font-weight:700; color:var(--primary); text-decoration:none; }
    .dashboard-container { max-width:1200px; margin:2rem auto; padding:0 1.5rem; }
    .menu-tabs { display:flex; gap:.5rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .menu-tabs a { padding:.6rem 1.1rem; border-radius:8px; text-decoration:none; color:#334155; background:#fff; border:1px solid #e2e8f0; font-size:.9rem; font-weight:600; }
    .menu-tabs a.active { background:var(--primary); color:#fff; border-color:var(--primary); }
    .card { background:#fff; border-radius:var(--border-radius-md); padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    table { width:100%; border-collapse:collapse; font-size:.88rem; }
    th, td { padding:.6rem .7rem; border-bottom:1px solid #eef1f5; text-align:left; vertical-align:top; }
    th { background:#f8fafc; font-weight:700; color:#475569; }
    .badge { padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:700; }
    .badge-belum { background:#fef3c7; color:#92400e; }
    .badge-terkirim { background:#dcfce7; color:#166534; }
    .badge-gagal { background:#fee2e2; color:#991b1b; }
    .btn { padding:.45rem .9rem; border-radius:7px; border:none; font-weight:700; font-size:.82rem; cursor:pointer; }
    .btn-primary { background:var(--primary); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#334155; }
    .stat-row { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .stat-box { flex:1; min-width:140px; background:#fff; border-radius:10px; padding:1rem; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .stat-box h2 { margin:0; font-size:1.6rem; }
    .stat-box p { margin:.2rem 0 0; color:var(--text-light); font-size:.8rem; }
    select, textarea, input[type=text] { padding:6px 8px; border:1px solid #cbd5e0; border-radius:6px; font-size:.85rem; }
    #log-kirim { font-family:monospace; font-size:.8rem; background:#0f172a; color:#e2e8f0; padding:1rem; border-radius:8px; max-height:260px; overflow-y:auto; }
</style>
</head>
<body>

<nav class="admin-nav">
    <a href="admin_dashboard.php" class="brand"><i class="fa-solid fa-gauge-high"></i> Poltekkes Bengkulu <span>OIA Portal</span></a>
    <div><a href="logout.php" style="text-decoration:none;color:#334155;font-weight:600;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></div>
</nav>

<div class="dashboard-container">

    <div class="menu-tabs">
        <a href="admin_dashboard.php"><i class="fa-solid fa-table-list"></i> Dashboard</a>
        <a href="kirim_surat_tidak_lulus.php" class="active"><i class="fa-solid fa-envelope"></i> Kirim Surat Tidak Lulus</a>
    </div>

    <?php if (!empty($message)): ?>
    <div class="card" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-weight:600;">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="stat-row">
        <div class="stat-box"><h2><?php echo $total_tidak_lulus; ?></h2><p>Total Tidak Lulus</p></div>
        <div class="stat-box"><h2 style="color:#92400e;"><?php echo $total_belum_kirim; ?></h2><p>Belum Dikirim</p></div>
        <div class="stat-box"><h2 style="color:#166534;"><?php echo $total_terkirim; ?></h2><p>Terkirim</p></div>
        <div class="stat-box"><h2 style="color:#991b1b;"><?php echo $total_gagal; ?></h2><p>Gagal</p></div>
    </div>

    <!-- TANDAI PESERTA TIDAK LULUS -->
    <div class="card">
        <h3 style="margin-top:0;">Tandai Status Seleksi Peserta</h3>
        <form method="POST" style="display:flex; gap:.75rem; flex-wrap:wrap; align-items:end;">
            <input type="hidden" name="action" value="set_status">
            <div>
                <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:4px;">Peserta</label>
                <select name="id" required style="min-width:260px;">
                    <option value="">-- Pilih Peserta --</option>
                    <?php foreach ($semua_peserta as $p): ?>
                    <option value="<?php echo $p['id']; ?>">
                        <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name'] . ' (' . $p['email'] . ')'); ?>
                        <?php if ($p['status_seleksi'] !== 'pending') echo ' - ' . strtoupper($p['status_seleksi']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:4px;">Status</label>
                <select name="status_seleksi">
                    <option value="tidak_lulus">Tidak Lulus</option>
                    <option value="lulus">Lulus</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div style="flex:1; min-width:220px;">
                <label style="display:block;font-size:.78rem;font-weight:600;margin-bottom:4px;">Keterangan (opsional)</label>
                <input type="text" name="keterangan_seleksi" placeholder="Misal: Passport habis 2027" style="width:100%;">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>

    <!-- DAFTAR TIDAK LULUS + KIRIM -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:.5rem;">
            <h3 style="margin:0;">Daftar Peserta Tidak Lulus (<?php echo $total_tidak_lulus; ?>)</h3>
            <button id="btn-kirim-semua" class="btn btn-primary" <?php echo $total_belum_kirim === 0 ? 'disabled' : ''; ?>>
                <i class="fa-solid fa-paper-plane"></i> Kirim ke Semua yang Belum Terkirim (<?php echo $total_belum_kirim; ?>)
            </button>
        </div>

        <table>
            <thead>
                <tr><th>Nama</th><th>Email</th><th>Keterangan</th><th>Status Surat</th><th>Aksi</th></tr>
            </thead>
            <tbody id="tbody-peserta">
                <?php foreach ($peserta as $p): ?>
                <tr data-id="<?php echo $p['id']; ?>" data-status="<?php echo $p['surat_status']; ?>">
                    <td><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td><?php echo htmlspecialchars($p['keterangan_seleksi'] ?? ''); ?></td>
                    <td class="cell-status">
                        <?php if ($p['surat_status'] === 'terkirim'): ?>
                            <span class="badge badge-terkirim">Terkirim<?php echo $p['surat_terkirim_at'] ? ' - ' . date('d/m/Y H:i', strtotime($p['surat_terkirim_at'])) : ''; ?></span>
                        <?php elseif ($p['surat_status'] === 'gagal'): ?>
                            <span class="badge badge-gagal" title="<?php echo htmlspecialchars($p['surat_error'] ?? ''); ?>">Gagal</span>
                        <?php else: ?>
                            <span class="badge badge-belum">Belum Dikirim</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-secondary btn-kirim-satu" data-id="<?php echo $p['id']; ?>">Kirim</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($peserta)): ?>
                <tr><td colspan="5" style="text-align:center;color:#94a3b8;">Belum ada peserta dengan status Tidak Lulus.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card" id="log-card" style="display:none;">
        <h3 style="margin-top:0;">Log Pengiriman</h3>
        <div id="log-kirim"></div>
    </div>

</div>

<script>
function addLog(text) {
    const log = document.getElementById('log-kirim');
    document.getElementById('log-card').style.display = 'block';
    log.innerHTML += text + '<br>';
    log.scrollTop = log.scrollHeight;
}

async function kirimSatu(id, row) {
    try {
        const resp = await fetch('kirim_surat_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        });
        const data = await resp.json();
        if (data.ok) {
            if (row) row.querySelector('.cell-status').innerHTML = '<span class="badge badge-terkirim">Terkirim</span>';
            addLog('✅ ID ' + id + ' terkirim.');
            return true;
        } else {
            if (row) row.querySelector('.cell-status').innerHTML = '<span class="badge badge-gagal">Gagal</span>';
            addLog('❌ ID ' + id + ' gagal: ' + data.error);
            return false;
        }
    } catch (e) {
        addLog('❌ ID ' + id + ' error koneksi: ' + e.message);
        return false;
    }
}

document.querySelectorAll('.btn-kirim-satu').forEach(btn => {
    btn.addEventListener('click', async () => {
        btn.disabled = true;
        const row = btn.closest('tr');
        await kirimSatu(btn.dataset.id, row);
        btn.disabled = false;
    });
});

document.getElementById('btn-kirim-semua').addEventListener('click', async function() {
    this.disabled = true;
    const rows = Array.from(document.querySelectorAll('#tbody-peserta tr')).filter(r => r.dataset.status !== 'terkirim');
    addLog('Mulai mengirim ' + rows.length + ' surat...');
    let sukses = 0, gagal = 0;
    for (const row of rows) {
        const id = row.dataset.id;
        const ok = await kirimSatu(id, row);
        ok ? sukses++ : gagal++;
        await new Promise(r => setTimeout(r, 1500)); // jeda 1.5 detik antar email
    }
    addLog('Selesai. Sukses: ' + sukses + ', Gagal: ' + gagal);
    this.disabled = false;
    this.textContent = 'Selesai - Sukses ' + sukses + ', Gagal ' + gagal;
});
</script>

</body>
</html>
