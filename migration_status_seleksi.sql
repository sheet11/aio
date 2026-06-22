-- Jalankan sekali saja di database (phpMyAdmin / mysql CLI)

ALTER TABLE tb_interstudent
  ADD COLUMN status_seleksi ENUM('pending','lulus','tidak_lulus') NOT NULL DEFAULT 'pending',
  ADD COLUMN keterangan_seleksi TEXT NULL,
  ADD COLUMN surat_terkirim_at DATETIME NULL,
  ADD COLUMN surat_status ENUM('belum','terkirim','gagal') NOT NULL DEFAULT 'belum',
  ADD COLUMN surat_error TEXT NULL;
