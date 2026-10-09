-- Jalankan pada database db_ukk_2026 yang sudah digunakan aplikasi.
-- t_kelas diasumsikan sudah tersedia dengan kolom id, nama, status_aktif.
-- Tambahkan indeks unik jika belum ada dan data lama tidak memiliki nama duplikat:
-- ALTER TABLE t_kelas ADD UNIQUE KEY uq_t_kelas_nama (nama);

CREATE TABLE IF NOT EXISTS t_tahun_ajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(30) NOT NULL UNIQUE,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    status_aktif TINYINT(1) NOT NULL DEFAULT 0,
    CHECK (tanggal_mulai <= tanggal_selesai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Jika belum ada tahun ajaran, contoh data awal (ubah tanggal sesuai kebutuhan):
INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif)
SELECT '2026/2027', '2026-07-01', '2027-06-30', 1
WHERE NOT EXISTS (SELECT 1 FROM t_tahun_ajaran);

-- Opsional: pastikan kolom relasi tahun ajaran tersedia pada tabel catatan pelanggaran.
-- Jangan jalankan jika kolom ini sudah ada.
-- ALTER TABLE t_pelanggaran_siswa ADD COLUMN tahun_ajaran_id INT NULL;


-- Tabel penugasan wali kelas: satu wali kelas untuk setiap kelas.
CREATE TABLE IF NOT EXISTS t_wali_kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kelas_id INT NOT NULL UNIQUE,
    nama_wali VARCHAR(150) NOT NULL,
    nip VARCHAR(30) NULL,
    CONSTRAINT fk_wali_kelas_kelas FOREIGN KEY (kelas_id) REFERENCES t_kelas(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
