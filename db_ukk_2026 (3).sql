-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Nov 2026 pada 08.36
-- Versi server: 10.4.25-MariaDB
-- Versi PHP: 8.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '1980010001', 'Aanisah Putri Nur Aini, S.Pd', 'aanisahputrinuraini@gmail.com', 1, 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, '1980020002', 'Ai Nuryaidah, S.Pd', 'ainuryaidah@gmail.com', 1, 2, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, '1980030003', 'Ai Wahidah, S.Ag', 'aiwahidah@gmail.com', 1, 3, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, '1980040004', 'Ansor Muhajir, S.T', 'ansormuhajir@gmail.com', 1, 4, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, '1980050005', 'Ari Sandriana, S.Kom', 'arisandriana@gmail.com', 1, 5, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, '1980060006', 'Arif Kusmawan, S.Pd', 'arifkusmawan@gmail.com', 1, 6, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, '1980070007', 'Asep Bastaman', 'asepbastaman@gmail.com', 1, 7, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, '1980080008', 'Asep Taufiq Hidayat, S.Kom', 'aseptaufiqhidayat@gmail.com', 1, 8, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, '1980090009', 'Chandra Aditya Yusup, S.Pd', 'chandraadityayusup@gmail.com', 1, 9, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, '1980100010', 'Dedi Kardiman, S.Pd', 'dedikardiman@gmail.com', 1, 10, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, '1980110011', 'Dody Jaenudin, S.Pd', 'dodyjaenudin@gmail.com', 1, 11, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, '1980120012', 'Drs. Ahmad Nur Mukhlis', 'ahmadnurmukhlis@gmail.com', 1, 12, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, '1980130013', 'Drs. Heri', 'heri@gmail.com', 1, 13, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, '1980140014', 'Drs. Muhamad Ali', 'muhamadali@gmail.com', 1, 14, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, '1980150015', 'Eli Rosliah, S.Pd', 'elirosliah@gmail.com', 1, 15, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, '1980160016', 'Erna Suryani, S.Pd', 'ernasuryani@gmail.com', 1, 16, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, '1980170017', 'Feri Rahmawan, S.Pd', 'ferirahmawan@gmail.com', 1, 17, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, '1980180018', 'H. Encep Maemun, S.T., M.Pd', 'encepmaemun@gmail.com', 1, 18, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, '1980190019', 'Helmi Ali Akbar, S.Pd.I', 'helmialihakbar@gmail.com', 1, 19, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, '1980200020', 'Ida Farida, S.Pd', 'idafarida@gmail.com', 1, 20, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, '1980210021', 'Idah Mahmudah, S.Pd', 'idahmahmudah@gmail.com', 1, 21, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, '1980220022', 'Isni Nur Fitriani, S.Pd', 'isninurfitriani@gmail.com', 1, 22, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, '1980230023', 'Lilis Rustandy Mawardi, S.Pd', 'lilisrustandymawardi@gmail.com', 1, 23, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, '1980240024', 'Maya Permata Endah, S.Pd', 'mayapermataendah@gmail.com', 1, 24, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, '1980250025', 'Mira Dwi Rahmawati, S.Ag', 'miradwirahmawati@gmail.com', 1, 25, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, '1980260026', 'Ogi Gaosun Gais, S.Pd.I', 'ogigaosungais@gmail.com', 1, 26, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, '1980270027', 'Opik', 'opik@gmail.com', 1, 27, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, '1980280028', 'Rendy Ichsan Kusumah, S.T', 'rendyichsankusumah@gmail.com', 1, 28, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, '1980290029', 'Sandi Mulyana, S.Pd', 'sandimulyana@gmail.com', 1, 29, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, '1980300030', 'Syeni Suswarini, S.Pd', 'syenisuswarini@gmail.com', 1, 30, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, '1980310031', 'Uca Musa Yusenda, S.Pd', 'ucmusayusenda@gmail.com', 1, 31, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, '1980320032', 'Ucu Muhammad Khoer N, S.Pd', 'ucumuhammadkhoern@gmail.com', 1, 32, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, '1980330033', 'Wawan Kurniawan, S.Pd.I', 'wawankurniawan@gmail.com', 1, 33, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, '1980340034', 'Wiwid Yulianti, S.Pd', 'wiwidyulianti@gmail.com', 1, 34, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, '1980350035', 'Yaneu Nurdian, S.Pd, M.Pd', 'yaneunurdian@gmail.com', 1, 35, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, '1980360036', 'Yusrin Noer Sabrina, S.Pd', 'yusrinnoersabrina@gmail.com', 1, 36, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, '1980370037', 'Yusi Luthfiani, S.Sos.I', 'yusiluthfiani@gmail.com', 1, 37, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, '1980380038', 'Nana Sujana', 'guru38@sekolah.sch.id', 1, 38, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, '1980390039', 'Oki Firmansyah', 'guru39@sekolah.sch.id', 1, 39, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, '1980400040', 'Prita Lestari', 'guru40@sekolah.sch.id', 1, 40, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, '1980410041', 'Rudi Hartono', 'guru41@sekolah.sch.id', 1, 41, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, '1980420042', 'Sari Ningsih', 'guru42@sekolah.sch.id', 1, 42, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, '1980430043', 'Tedi Setiawan', 'guru43@sekolah.sch.id', 1, 43, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, '1980440044', 'Umi Rahma', 'guru44@sekolah.sch.id', 1, 44, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, '1980450045', 'Vicky Pratama', 'guru45@sekolah.sch.id', 1, 45, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, '1980460046', 'Widi Astuti', 'guru46@sekolah.sch.id', 1, 46, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, '1980470047', 'Yani Mulyani', 'guru47@sekolah.sch.id', 1, 47, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, '1980480048', 'Yudi Kurniawan', 'guru48@sekolah.sch.id', 1, 48, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, '1980490049', 'Zulfa Aulia', 'guru49@sekolah.sch.id', 1, 49, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, '1980500050', 'Rina Marlina', 'guru50@sekolah.sch.id', 1, 50, '2026-01-01 01:00:00', '2026-09-23 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_kelas`
--

INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '10 RPL 1', '10', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, '11 TKJ 1', '11', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, '12 TKR 1', '12', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, '10 BDPM 1', '10', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, '11 TSM 1', '11', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, '12 RPL 2', '12', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, '10 TKJ 2', '10', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, '11 TKR 2', '11', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, '12 BDPM 2', '12', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, '10 TSM 2', '10', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, '11 RPL 3', '11', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, '12 TKJ 3', '12', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, '10 TKR 3', '10', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, '11 BDPM 3', '11', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, '12 TSM 3', '12', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, '10 RPL 4', '10', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, '11 TKJ 4', '11', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, '12 TKR 4', '12', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, '10 BDPM 4', '10', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, '11 TSM 4', '11', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, '12 RPL 5', '12', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, '10 TKJ 5', '10', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, '11 TKR 5', '11', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, '12 BDPM 5', '12', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, '10 TSM 5', '10', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, '11 RPL 6', '11', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, '12 TKJ 6', '12', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, '10 TKR 6', '10', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, '11 BDPM 6', '11', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, '12 TSM 6', '12', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, '10 RPL 7', '10', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, '11 TKJ 7', '11', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, '12 TKR 7', '12', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, '10 BDPM 7', '10', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, '11 TSM 7', '11', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, '12 RPL 8', '12', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, '10 TKJ 8', '10', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, '11 TKR 8', '11', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, '12 BDPM 8', '12', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, '10 TSM 8', '10', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, '11 RPL 9', '11', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, '12 TKJ 9', '12', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, '10 TKR 9', '10', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, '11 BDPM 9', '11', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, '12 TSM 9', '12', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, '10 RPL 10', '10', 'RPL', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, '11 TKJ 10', '11', 'TKJ', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, '12 TKR 10', '12', 'TKR', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, '10 BDPM 10', '10', 'BDPM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, '11 TSM 10', '11', 'TSM', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(51, '12 RPL 1', '12', 'RPL', 1, '2026-11-10 07:35:29', '2026-11-10 07:35:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(51, 1, 1, 50, '2026-11-10', '2027-11-10', 1, '2026-11-10 06:17:22', '2026-11-10 06:17:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'P001', 'Terlambat Masuk Sekolah', 7, 'Deskripsi pelanggaran nomor 1', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, 2, 'P002', 'Tidak Mengikuti Apel Pagi', 9, 'Deskripsi pelanggaran nomor 2', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, 3, 'P003', 'Tidak Mengikuti Upacara Bendera', 11, 'Deskripsi pelanggaran nomor 3', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, 4, 'P004', 'Meninggalkan Kelas Tanpa Izin', 13, 'Deskripsi pelanggaran nomor 4', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, 5, 'P005', 'Membolos Pelajaran', 15, 'Deskripsi pelanggaran nomor 5', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, 6, 'P006', 'Tidak Hadir Tanpa Keterangan', 17, 'Deskripsi pelanggaran nomor 6', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, 7, 'P007', 'Terlambat Kembali Setelah Istirahat', 19, 'Deskripsi pelanggaran nomor 7', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, 8, 'P008', 'Tidak Membawa Surat Izin', 21, 'Deskripsi pelanggaran nomor 8', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, 9, 'P009', 'Pulang Sebelum Waktunya Tanpa Izin', 23, 'Deskripsi pelanggaran nomor 9', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, 10, 'P010', 'Tidak Mengikuti Kegiatan Sekolah', 25, 'Deskripsi pelanggaran nomor 10', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, 11, 'P011', 'Memakai Seragam Tidak Sesuai Ketentuan', 27, 'Deskripsi pelanggaran nomor 11', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, 12, 'P012', 'Tidak Memakai Atribut Lengkap', 29, 'Deskripsi pelanggaran nomor 12', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, 13, 'P013', 'Rambut Tidak Sesuai Ketentuan', 31, 'Deskripsi pelanggaran nomor 13', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, 14, 'P014', 'Memakai Sepatu Tidak Sesuai Aturan', 33, 'Deskripsi pelanggaran nomor 14', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, 15, 'P015', 'Tidak Memakai Ikat Pinggang', 35, 'Deskripsi pelanggaran nomor 15', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, 16, 'P016', 'Memakai Aksesori Berlebihan', 37, 'Deskripsi pelanggaran nomor 16', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, 17, 'P017', 'Tidak Memakai Kaos Kaki', 39, 'Deskripsi pelanggaran nomor 17', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, 18, 'P018', 'Seragam Tidak Rapi', 41, 'Deskripsi pelanggaran nomor 18', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, 19, 'P019', 'Memakai Jaket Saat Pembelajaran Tanpa Izin', 43, 'Deskripsi pelanggaran nomor 19', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, 20, 'P020', 'Tidak Memakai Seragam Praktik', 45, 'Deskripsi pelanggaran nomor 20', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, 21, 'P021', 'Berbicara Tidak Sopan kepada Guru', 47, 'Deskripsi pelanggaran nomor 21', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, 22, 'P022', 'Mengganggu Teman Saat Pembelajaran', 49, 'Deskripsi pelanggaran nomor 22', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, 23, 'P023', 'Membuat Keributan di Kelas', 51, 'Deskripsi pelanggaran nomor 23', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, 24, 'P024', 'Berkata Kasar kepada Teman', 53, 'Deskripsi pelanggaran nomor 24', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, 25, 'P025', 'Tidak Menghormati Warga Sekolah', 55, 'Deskripsi pelanggaran nomor 25', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, 26, 'P026', 'Bertengkar dengan Teman', 57, 'Deskripsi pelanggaran nomor 26', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, 27, 'P027', 'Melakukan Perundungan', 59, 'Deskripsi pelanggaran nomor 27', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, 28, 'P028', 'Tidak Mengikuti Arahan Guru', 61, 'Deskripsi pelanggaran nomor 28', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, 29, 'P029', 'Menghina Teman', 63, 'Deskripsi pelanggaran nomor 29', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, 30, 'P030', 'Bermain Saat Pembelajaran', 65, 'Deskripsi pelanggaran nomor 30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, 31, 'P031', 'Tidak Mengerjakan Tugas', 67, 'Deskripsi pelanggaran nomor 31', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, 32, 'P032', 'Tidak Membawa Perlengkapan Belajar', 69, 'Deskripsi pelanggaran nomor 32', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, 33, 'P033', 'Menyontek Saat Ujian', 71, 'Deskripsi pelanggaran nomor 33', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, 34, 'P034', 'Mengumpulkan Tugas Terlambat', 73, 'Deskripsi pelanggaran nomor 34', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, 35, 'P035', 'Tidak Mengikuti Ulangan Tanpa Izin', 75, 'Deskripsi pelanggaran nomor 35', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, 36, 'P036', 'Mengganggu Kegiatan Praktikum', 77, 'Deskripsi pelanggaran nomor 36', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, 37, 'P037', 'Melanggar Tata Tertib Laboratorium', 79, 'Deskripsi pelanggaran nomor 37', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, 38, 'P038', 'Tidak Membawa Buku Pelajaran', 81, 'Deskripsi pelanggaran nomor 38', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, 39, 'P039', 'Tidur Saat Pembelajaran', 83, 'Deskripsi pelanggaran nomor 39', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, 40, 'P040', 'Tidak Memperhatikan Penjelasan Guru', 85, 'Deskripsi pelanggaran nomor 40', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, 41, 'P041', 'Membuang Sampah Sembarangan', 87, 'Deskripsi pelanggaran nomor 41', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, 42, 'P042', 'Merusak Fasilitas Sekolah', 89, 'Deskripsi pelanggaran nomor 42', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, 43, 'P043', 'Mencoret Meja atau Dinding', 91, 'Deskripsi pelanggaran nomor 43', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, 44, 'P044', 'Tidak Melaksanakan Piket Kelas', 93, 'Deskripsi pelanggaran nomor 44', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, 45, 'P045', 'Mengotori Ruang Kelas', 95, 'Deskripsi pelanggaran nomor 45', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, 46, 'P046', 'Membawa Rokok ke Sekolah', 97, 'Deskripsi pelanggaran nomor 46', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, 47, 'P047', 'Merokok di Lingkungan Sekolah', 99, 'Deskripsi pelanggaran nomor 47', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, 48, 'P048', 'Menggunakan HP Tanpa Izin Saat Pelajaran', 101, 'Deskripsi pelanggaran nomor 48', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, 49, 'P049', 'Membawa Barang Terlarang', 103, 'Deskripsi pelanggaran nomor 49', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, 50, 'P050', 'Keluar Sekolah Tanpa Izin', 105, 'Deskripsi pelanggaran nomor 50', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_pelanggaran_kategori`
--

INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Keterlambatan', 'Diberikan teguran dan pembinaan oleh wali kelas.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, 'Ketidakhadiran', 'Diberikan teguran dan pembinaan oleh wali kelas.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, 'Membolos', 'Diberikan teguran dan pembinaan oleh wali kelas.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, 'Tidak Mengikuti Upacara', 'Diberikan teguran dan pembinaan oleh wali kelas.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, 'Keluar Tanpa Izin', 'Diberikan teguran dan pembinaan oleh wali kelas.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, 'Atribut Seragam', 'Diberikan teguran dan diminta melengkapi ketentuan sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, 'Kerapian Rambut', 'Diberikan teguran dan diminta melengkapi ketentuan sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, 'Kelengkapan Seragam', 'Diberikan teguran dan diminta melengkapi ketentuan sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, 'Pelanggaran Alas Kaki', 'Diberikan teguran dan diminta melengkapi ketentuan sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, 'Penggunaan Aksesori', 'Diberikan teguran dan diminta melengkapi ketentuan sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, 'Ketidaksopanan', 'Diberikan pembinaan oleh guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, 'Perkataan Tidak Pantas', 'Diberikan pembinaan oleh guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, 'Mengganggu Pembelajaran', 'Diberikan pembinaan oleh guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, 'Perkelahian', 'Diberikan pembinaan oleh guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, 'Perundungan', 'Diberikan pembinaan oleh guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, 'Pelanggaran Tata Tertib', 'Diberikan surat peringatan dan pembinaan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, 'Merokok', 'Diberikan surat peringatan dan pembinaan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, 'Membawa Barang Terlarang', 'Diberikan surat peringatan dan pembinaan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, 'Penggunaan HP', 'Diberikan surat peringatan dan pembinaan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, 'Penyalahgunaan Fasilitas', 'Diberikan surat peringatan dan pembinaan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, 'Tidak Mengerjakan Tugas', 'Diberikan tugas pembinaan oleh guru mata pelajaran.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, 'Menyontek', 'Diberikan tugas pembinaan oleh guru mata pelajaran.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, 'Tidak Membawa Buku', 'Diberikan tugas pembinaan oleh guru mata pelajaran.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, 'Tidak Mengikuti Ulangan', 'Diberikan tugas pembinaan oleh guru mata pelajaran.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, 'Mengganggu Praktikum', 'Diberikan tugas pembinaan oleh guru mata pelajaran.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, 'Pelanggaran Laboratorium', 'Diberikan teguran dan tanggung jawab memperbaiki kerusakan atau kebersihan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, 'Tidak Melaksanakan Piket', 'Diberikan teguran dan tanggung jawab memperbaiki kerusakan atau kebersihan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, 'Membuang Sampah', 'Diberikan teguran dan tanggung jawab memperbaiki kerusakan atau kebersihan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, 'Merusak Fasilitas', 'Diberikan teguran dan tanggung jawab memperbaiki kerusakan atau kebersihan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, 'Mencoret Properti Sekolah', 'Diberikan teguran dan tanggung jawab memperbaiki kerusakan atau kebersihan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, 'Meninggalkan Kelas', 'Diberikan teguran tertulis dan pembinaan kedisiplinan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, 'Tidur Saat Pelajaran', 'Diberikan teguran tertulis dan pembinaan kedisiplinan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, 'Bermain Saat Pelajaran', 'Diberikan teguran tertulis dan pembinaan kedisiplinan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, 'Tidak Memperhatikan Guru', 'Diberikan teguran tertulis dan pembinaan kedisiplinan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, 'Membuat Keributan', 'Diberikan teguran tertulis dan pembinaan kedisiplinan.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, 'Pulang Sebelum Waktunya', 'Dilakukan pembinaan oleh wali kelas dan guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, 'Tidak Mengikuti Kegiatan Sekolah', 'Dilakukan pembinaan oleh wali kelas dan guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, 'Memakai Jaket Tanpa Izin', 'Dilakukan pembinaan oleh wali kelas dan guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, 'Tidak Memakai Seragam Praktik', 'Dilakukan pembinaan oleh wali kelas dan guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, 'Tidak Membawa Perlengkapan Praktik', 'Dilakukan pembinaan oleh wali kelas dan guru BK.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, 'Pelanggaran Keamanan', 'Pemanggilan orang tua dan pembinaan khusus.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, 'Mengambil Barang Orang Lain', 'Pemanggilan orang tua dan pembinaan khusus.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, 'Pemalsuan Surat Izin', 'Pemanggilan orang tua dan pembinaan khusus.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, 'Berbohong kepada Guru', 'Pemanggilan orang tua dan pembinaan khusus.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, 'Menghina Warga Sekolah', 'Pemanggilan orang tua dan pembinaan khusus.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, 'Berkeliaran Saat Pelajaran', 'Diberikan sanksi sesuai tingkat pelanggaran dan tata tertib sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, 'Menggunakan Kendaraan Tidak Tertib', 'Diberikan sanksi sesuai tingkat pelanggaran dan tata tertib sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, 'Parkir Tidak Sesuai Aturan', 'Diberikan sanksi sesuai tingkat pelanggaran dan tata tertib sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, 'Membawa Kendaraan Tanpa Izin', 'Diberikan sanksi sesuai tingkat pelanggaran dan tata tertib sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, 'Pelanggaran Berat', 'Diberikan sanksi sesuai tingkat pelanggaran dan tata tertib sekolah.', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) NOT NULL,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `kelas_id`, `nama_kelas`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Andi Saputra', 1, '10 RPL 1', 1, 'Pelanggaran 1', 1, 1, 'Ahmad Hidayat', '2026-01-01', 'Keterangan pelanggaran siswa 1', 7, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, 1, 2, 'Budi Santoso', 2, '11 TKJ 1', 2, 'Pelanggaran 2', 2, 2, 'Bambang Setiawan', '2026-02-02', 'Keterangan pelanggaran siswa 2', 9, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, 1, 3, 'Citra Lestari', 3, '12 AKL 1', 3, 'Pelanggaran 3', 3, 3, 'Cahya Permana', '2026-03-03', 'Keterangan pelanggaran siswa 3', 11, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, 1, 4, 'Deni Kurniawan', 4, '10 BD 1', 4, 'Pelanggaran 4', 4, 4, 'Dewi Anggraini', '2026-04-04', 'Keterangan pelanggaran siswa 4', 13, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, 1, 5, 'Eka Putri', 5, '11 MPLB 1', 5, 'Pelanggaran 5', 5, 5, 'Eko Pratama', '2026-05-05', 'Keterangan pelanggaran siswa 5', 15, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, 1, 6, 'Fajar Nugraha', 6, '12 RPL 2', 6, 'Pelanggaran 6', 6, 6, 'Fitri Lestari', '2026-06-06', 'Keterangan pelanggaran siswa 6', 17, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, 1, 7, 'Gina Maharani', 7, '10 TKJ 2', 7, 'Pelanggaran 7', 7, 7, 'Gilang Ramadhan', '2026-07-07', 'Keterangan pelanggaran siswa 7', 19, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, 1, 8, 'Hendra Wijaya', 8, '11 AKL 2', 8, 'Pelanggaran 8', 8, 8, 'Hana Sari', '2026-08-08', 'Keterangan pelanggaran siswa 8', 21, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, 1, 9, 'Intan Permata', 9, '12 BD 2', 9, 'Pelanggaran 9', 9, 9, 'Indra Wijaya', '2026-09-09', 'Keterangan pelanggaran siswa 9', 23, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, 1, 10, 'Joko Susanto', 10, '10 MPLB 2', 10, 'Pelanggaran 10', 10, 10, 'Jajang Nurjaman', '2026-10-10', 'Keterangan pelanggaran siswa 10', 25, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, 1, 11, 'Karina Sari', 11, '11 RPL 3', 11, 'Pelanggaran 11', 11, 11, 'Kiki Amelia', '2026-11-11', 'Keterangan pelanggaran siswa 11', 27, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, 1, 12, 'Lukman Hakim', 12, '12 TKJ 3', 12, 'Pelanggaran 12', 12, 12, 'Lilis Kurniasih', '2026-12-12', 'Keterangan pelanggaran siswa 12', 29, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, 1, 13, 'Maya Anggraini', 13, '10 AKL 3', 13, 'Pelanggaran 13', 13, 13, 'Maman Suherman', '2026-01-13', 'Keterangan pelanggaran siswa 13', 31, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, 1, 14, 'Nanda Pratama', 14, '11 BD 3', 14, 'Pelanggaran 14', 14, 14, 'Novi Oktavia', '2026-02-14', 'Keterangan pelanggaran siswa 14', 33, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, 1, 15, 'Oki Ramadhan', 15, '12 MPLB 3', 15, 'Pelanggaran 15', 15, 15, 'Oman Saputra', '2026-03-15', 'Keterangan pelanggaran siswa 15', 35, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, 1, 16, 'Putri Amelia', 16, '10 RPL 4', 16, 'Pelanggaran 16', 16, 16, 'Pipit Maharani', '2026-04-16', 'Keterangan pelanggaran siswa 16', 37, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, 1, 17, 'Qori Aulia', 17, '11 TKJ 4', 17, 'Pelanggaran 17', 17, 17, 'Rizal Fauzi', '2026-05-17', 'Keterangan pelanggaran siswa 17', 39, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, 1, 18, 'Raka Firmansyah', 18, '12 AKL 4', 18, 'Pelanggaran 18', 18, 18, 'Sinta Nirmala', '2026-06-18', 'Keterangan pelanggaran siswa 18', 41, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, 1, 19, 'Salsa Nabila', 19, '10 BD 4', 19, 'Pelanggaran 19', 19, 19, 'Taufik Hidayat', '2026-07-19', 'Keterangan pelanggaran siswa 19', 43, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, 1, 20, 'Tegar Maulana', 20, '11 MPLB 4', 20, 'Pelanggaran 20', 20, 20, 'Ujang Kurnia', '2026-08-20', 'Keterangan pelanggaran siswa 20', 45, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, 1, 21, 'Umi Kalsum', 21, '12 RPL 5', 21, 'Pelanggaran 21', 21, 21, 'Vina Aprilia', '2026-09-21', 'Keterangan pelanggaran siswa 21', 47, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, 1, 22, 'Vino Akbar', 22, '10 TKJ 5', 22, 'Pelanggaran 22', 22, 22, 'Wawan Hermawan', '2026-10-22', 'Keterangan pelanggaran siswa 22', 49, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, 1, 23, 'Wahyu Hidayat', 23, '11 AKL 5', 23, 'Pelanggaran 23', 23, 23, 'Yulia Safitri', '2026-11-23', 'Keterangan pelanggaran siswa 23', 51, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, 1, 24, 'Xenia Oktaviani', 24, '12 BD 5', 24, 'Pelanggaran 24', 24, 24, 'Zainal Abidin', '2026-12-24', 'Keterangan pelanggaran siswa 24', 53, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, 1, 25, 'Yogi Prasetyo', 25, '10 MPLB 5', 25, 'Pelanggaran 25', 25, 25, 'Asep Firmansyah', '2026-01-25', 'Keterangan pelanggaran siswa 25', 55, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, 1, 26, 'Zahra Fadillah', 26, '11 RPL 1', 26, 'Pelanggaran 26', 26, 26, 'Beni Kurnia', '2026-02-26', 'Keterangan pelanggaran siswa 26', 57, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, 1, 27, 'Agus Setiawan', 27, '12 TKJ 1', 27, 'Pelanggaran 27', 27, 27, 'Cici Puspita', '2026-03-27', 'Keterangan pelanggaran siswa 27', 59, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, 1, 28, 'Bella Novitasari', 28, '10 AKL 1', 28, 'Pelanggaran 28', 28, 28, 'Dadan Maulana', '2026-04-01', 'Keterangan pelanggaran siswa 28', 61, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, 1, 29, 'Cahyo Ramadhan', 29, '11 BD 1', 29, 'Pelanggaran 29', 29, 29, 'Euis Kartika', '2026-05-02', 'Keterangan pelanggaran siswa 29', 63, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, 1, 30, 'Dinda Safitri', 30, '12 MPLB 1', 30, 'Pelanggaran 30', 30, 30, 'Feri Irawan', '2026-06-03', 'Keterangan pelanggaran siswa 30', 65, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, 1, 31, 'Erwin Maulana', 31, '10 RPL 2', 31, 'Pelanggaran 31', 31, 31, 'Galih Setiawan', '2026-07-04', 'Keterangan pelanggaran siswa 31', 67, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, 1, 32, 'Fani Azzahra', 32, '11 TKJ 2', 32, 'Pelanggaran 32', 32, 32, 'Hesti Wulandari', '2026-08-05', 'Keterangan pelanggaran siswa 32', 69, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, 1, 33, 'Galih Permana', 33, '12 AKL 2', 33, 'Pelanggaran 33', 33, 33, 'Imam Fauzan', '2026-09-06', 'Keterangan pelanggaran siswa 33', 71, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, 1, 34, 'Hani Rahmawati', 34, '10 BD 2', 34, 'Pelanggaran 34', 34, 34, 'Joko Haryanto', '2026-10-07', 'Keterangan pelanggaran siswa 34', 73, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, 1, 35, 'Ilham Fauzi', 35, '11 MPLB 2', 35, 'Pelanggaran 35', 35, 35, 'Kurnia Sari', '2026-11-08', 'Keterangan pelanggaran siswa 35', 75, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, 1, 36, 'Jihan Nuraini', 36, '12 RPL 3', 36, 'Pelanggaran 36', 36, 36, 'Lukman Hakim', '2026-12-09', 'Keterangan pelanggaran siswa 36', 77, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, 1, 37, 'Kevin Alfarizi', 37, '10 TKJ 3', 37, 'Pelanggaran 37', 37, 37, 'Mira Oktaviani', '2026-01-10', 'Keterangan pelanggaran siswa 37', 79, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, 1, 38, 'Laila Mutiara', 38, '11 AKL 3', 38, 'Pelanggaran 38', 38, 38, 'Nana Sujana', '2026-02-11', 'Keterangan pelanggaran siswa 38', 81, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, 1, 39, 'Miko Aditya', 39, '12 BD 3', 39, 'Pelanggaran 39', 39, 39, 'Oki Firmansyah', '2026-03-12', 'Keterangan pelanggaran siswa 39', 83, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, 1, 40, 'Nia Kartika', 40, '10 MPLB 3', 40, 'Pelanggaran 40', 40, 40, 'Prita Lestari', '2026-04-13', 'Keterangan pelanggaran siswa 40', 85, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, 1, 41, 'Opik Firmansyah', 41, '11 RPL 4', 41, 'Pelanggaran 41', 41, 41, 'Rudi Hartono', '2026-05-14', 'Keterangan pelanggaran siswa 41', 87, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, 1, 42, 'Puspa Sari', 42, '12 TKJ 4', 42, 'Pelanggaran 42', 42, 42, 'Sari Ningsih', '2026-06-15', 'Keterangan pelanggaran siswa 42', 89, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, 1, 43, 'Rian Kurnia', 43, '10 AKL 4', 43, 'Pelanggaran 43', 43, 43, 'Tedi Setiawan', '2026-07-16', 'Keterangan pelanggaran siswa 43', 91, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, 1, 44, 'Siti Aisyah', 44, '11 BD 4', 44, 'Pelanggaran 44', 44, 44, 'Umi Rahma', '2026-08-17', 'Keterangan pelanggaran siswa 44', 93, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, 1, 45, 'Tio Ramadhan', 45, '12 MPLB 4', 45, 'Pelanggaran 45', 45, 45, 'Vicky Pratama', '2026-09-18', 'Keterangan pelanggaran siswa 45', 95, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, 1, 46, 'Ulfa Nurfadilah', 46, '10 RPL 5', 46, 'Pelanggaran 46', 46, 46, 'Widi Astuti', '2026-10-19', 'Keterangan pelanggaran siswa 46', 97, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, 1, 47, 'Vera Anjani', 47, '11 TKJ 5', 47, 'Pelanggaran 47', 47, 47, 'Yani Mulyani', '2026-11-20', 'Keterangan pelanggaran siswa 47', 99, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, 1, 48, 'Wawan Gunawan', 48, '12 AKL 5', 48, 'Pelanggaran 48', 48, 48, 'Yudi Kurniawan', '2026-12-21', 'Keterangan pelanggaran siswa 48', 101, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, 1, 49, 'Yuni Astuti', 49, '10 BD 5', 49, 'Pelanggaran 49', 49, 49, 'Zulfa Aulia', '2026-01-22', 'Keterangan pelanggaran siswa 49', 103, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Proses', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, 1, 50, 'Zaki Mubarak', 50, '11 MPLB 5', 50, 'Pelanggaran 50', 50, 50, 'Rina Marlina', '2026-02-23', 'Keterangan pelanggaran siswa 50', 105, 'Diberikan teguran tertulis dan pembinaan oleh wali kelas serta guru BK.', 'Selesai', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(51, 1, 2, 'Agnia Rosya Noerillah', 49, '10 BDPM 10', 30, 'Bermain Saat Pembelajaran', 30, 1, 'Aanisah Putri Nur Aini, S.Pd', '2026-11-10', 'qqqqqq', 65, 'qqqqq', 'Proses', '2026-11-10 06:57:48', '2026-11-10 06:57:48'),
(52, 1, 4, 'Aradea Kandi', 16, '10 RPL 4', 26, 'Bertengkar dengan Teman', 26, 1, 'Aanisah Putri Nur Aini, S.Pd', '2026-11-10', 'terlambat', 57, 'terlambat', 'Proses', '2026-11-10 06:58:46', '2026-11-10 06:58:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '102410544', '0000000001', 'Adittia Nur Lantip', 'L', '2010-01-15', 'Jl. Pendidikan No. 1, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(2, '102410545', '0000000002', 'Agnia Rosya Noerillah', 'P', '2010-02-20', 'Jl. Pendidikan No. 2, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(3, '102410546', '0000000003', 'Anisa Amirulla', 'P', '2010-03-12', 'Jl. Pendidikan No. 3, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(4, '102410547', '0000000004', 'Aradea Kandi', 'L', '2010-04-25', 'Jl. Pendidikan No. 4, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(5, '102410548', '0000000005', 'Arfa Diesa Okmaleliza', 'P', '2010-05-18', 'Jl. Pendidikan No. 5, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(6, '102410549', '0000000006', 'Fitri Nur Syami', 'P', '2010-06-07', 'Jl. Pendidikan No. 6, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(7, '102410550', '0000000007', 'Junita Putri Nur Rahman', 'P', '2010-07-22', 'Jl. Pendidikan No. 7, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(8, '102410551', '0000000008', 'Mohammad Atalarik Ali Syahbana', 'L', '2010-08-14', 'Jl. Pendidikan No. 8, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(9, '102410552', '0000000009', 'Muhammad Fahril Fahrurozi', 'L', '2010-09-30', 'Jl. Pendidikan No. 9, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(10, '102410750', '0000000010', 'Muhammad Adnan Syaeful Islami', 'L', '2010-10-11', 'Jl. Pendidikan No. 10, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(11, '102410553', '0000000011', 'Muhammad Zahru Aqila Fikri', 'L', '2010-11-05', 'Jl. Pendidikan No. 11, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(12, '102410554', '0000000012', 'Nadia Zulfa Soraya', 'P', '2010-12-19', 'Jl. Pendidikan No. 12, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(13, '102410555', '0000000013', 'Nazril Ilham', 'L', '2011-01-08', 'Jl. Pendidikan No. 13, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(14, '102410556', '0000000014', 'Puspita Dewi', 'P', '2011-02-17', 'Jl. Pendidikan No. 14, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(15, '102410558', '0000000015', 'Rakha Muhammad Naykal', 'L', '2011-03-29', 'Jl. Pendidikan No. 15, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(16, '102410559', '0000000016', 'Rani Melani', 'P', '2011-04-13', 'Jl. Pendidikan No. 16, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(17, '102410560', '0000000017', 'Reihan Muzaki', 'L', '2011-05-21', 'Jl. Pendidikan No. 17, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(18, '102410561', '0000000018', 'Resa Juliansah', 'L', '2011-06-09', 'Jl. Pendidikan No. 18, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(19, '102410562', '0000000019', 'Rizki Permata Sugianto', 'L', '2011-07-27', 'Jl. Pendidikan No. 19, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(20, '102410563', '0000000020', 'Sahira Firaj', 'P', '2011-08-16', 'Jl. Pendidikan No. 20, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(21, '102410564', '0000000021', 'Sendi Mardian', 'L', '2011-09-04', 'Jl. Pendidikan No. 21, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(22, '102410755', '0000000022', 'Shahaidan Nizzar', 'L', '2011-10-23', 'Jl. Pendidikan No. 22, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(23, '102410565', '0000000023', 'Sherly Satriani', 'P', '2011-11-14', 'Jl. Pendidikan No. 23, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(24, '102410566', '0000000024', 'Sofinurjanah', 'P', '2011-12-02', 'Jl. Pendidikan No. 24, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(25, '102410567', '0000000025', 'Suraiya Salsabila Azzahira', 'P', '2010-01-28', 'Jl. Pendidikan No. 25, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(26, '102410568', '0000000026', 'Toni Antonio', 'L', '2010-02-11', 'Jl. Pendidikan No. 26, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-11-10 06:02:22'),
(27, '20260027', '002026000027', 'Agus Setiawan', 'L', '2008-04-01', 'Jl. Pendidikan No. 27', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, '20260028', '002026000028', 'Bella Novitasari', 'P', '2009-05-02', 'Jl. Pendidikan No. 28', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, '20260029', '002026000029', 'Cahyo Ramadhan', 'L', '2010-06-03', 'Jl. Pendidikan No. 29', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, '20260030', '002026000030', 'Dinda Safitri', 'P', '2008-07-04', 'Jl. Pendidikan No. 30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, '20260031', '002026000031', 'Erwin Maulana', 'L', '2009-08-05', 'Jl. Pendidikan No. 31', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, '20260032', '002026000032', 'Fani Azzahra', 'P', '2010-09-06', 'Jl. Pendidikan No. 32', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, '20260033', '002026000033', 'Galih Permana', 'L', '2008-10-07', 'Jl. Pendidikan No. 33', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, '20260034', '002026000034', 'Hani Rahmawati', 'P', '2009-11-08', 'Jl. Pendidikan No. 34', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, '20260035', '002026000035', 'Ilham Fauzi', 'L', '2010-12-09', 'Jl. Pendidikan No. 35', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, '20260036', '002026000036', 'Jihan Nuraini', 'P', '2008-01-10', 'Jl. Pendidikan No. 36', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, '20260037', '002026000037', 'Kevin Alfarizi', 'L', '2009-02-11', 'Jl. Pendidikan No. 37', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, '20260038', '002026000038', 'Laila Mutiara', 'P', '2010-03-12', 'Jl. Pendidikan No. 38', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, '20260039', '002026000039', 'Miko Aditya', 'L', '2008-04-13', 'Jl. Pendidikan No. 39', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, '20260040', '002026000040', 'Nia Kartika', 'P', '2009-05-14', 'Jl. Pendidikan No. 40', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, '20260041', '002026000041', 'Opik Firmansyah', 'L', '2010-06-15', 'Jl. Pendidikan No. 41', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, '20260042', '002026000042', 'Puspa Sari', 'P', '2008-07-16', 'Jl. Pendidikan No. 42', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, '20260043', '002026000043', 'Rian Kurnia', 'L', '2009-08-17', 'Jl. Pendidikan No. 43', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, '20260044', '002026000044', 'Siti Aisyah', 'P', '2010-09-18', 'Jl. Pendidikan No. 44', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, '20260045', '002026000045', 'Tio Ramadhan', 'L', '2008-10-19', 'Jl. Pendidikan No. 45', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, '20260046', '002026000046', 'Ulfa Nurfadilah', 'P', '2009-11-20', 'Jl. Pendidikan No. 46', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, '20260047', '002026000047', 'Vera Anjani', 'L', '2010-12-21', 'Jl. Pendidikan No. 47', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, '20260048', '002026000048', 'Wawan Gunawan', 'P', '2008-01-22', 'Jl. Pendidikan No. 48', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, '20260049', '002026000049', 'Yuni Astuti', 'L', '2009-02-23', 'Jl. Pendidikan No. 49', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, '20260050', '002026000050', 'Zaki Mubarak', 'P', '2010-03-24', 'Jl. Pendidikan No. 50', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, '2045/2046', '2045-07-01', '2046-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, '2046/2047', '2046-07-01', '2047-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, '2047/2048', '2047-07-01', '2048-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, '2048/2049', '2048-07-01', '2049-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, '2049/2050', '2049-07-01', '2050-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, '2050/2051', '2050-07-01', '2051-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, '2051/2052', '2051-07-01', '2052-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, '2052/2053', '2052-07-01', '2053-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, '2053/2054', '2053-07-01', '2054-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, '2054/2055', '2054-07-01', '2055-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, '2055/2056', '2055-07-01', '2056-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, '2056/2057', '2056-07-01', '2057-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, '2057/2058', '2057-07-01', '2058-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, '2058/2059', '2058-07-01', '2059-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, '2059/2060', '2059-07-01', '2060-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, '2060/2061', '2060-07-01', '2061-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, '2061/2062', '2061-07-01', '2062-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, '2062/2063', '2062-07-01', '2063-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, '2063/2064', '2063-07-01', '2064-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, '2064/2065', '2064-07-01', '2065-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, '2065/2066', '2065-07-01', '2066-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, '2066/2067', '2066-07-01', '2067-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, '2067/2068', '2067-07-01', '2068-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, '2068/2069', '2068-07-01', '2069-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, '2069/2070', '2069-07-01', '2070-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, '2070/2071', '2070-07-01', '2071-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, '2071/2072', '2071-07-01', '2072-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, '2072/2073', '2072-07-01', '2073-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, '2073/2074', '2073-07-01', '2074-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, '2074/2075', '2074-07-01', '2075-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, '2075/2076', '2075-07-01', '2076-06-30', 0, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(51, '2021/2022', '2021-07-01', '2022-06-30', 1, '2026-11-10 05:22:59', '2026-11-10 05:22:59'),
(52, '2022/2023', '2022-07-01', '2023-06-30', 1, '2026-11-10 05:22:59', '2026-11-10 05:22:59'),
(53, '2023/2024', '2023-07-01', '2024-06-30', 1, '2026-11-10 05:22:59', '2026-11-10 05:22:59'),
(54, '2024/2025', '2024-07-01', '2025-06-30', 1, '2026-11-10 05:22:59', '2026-11-10 05:22:59'),
(55, '2025/2026', '2025-07-01', '2026-06-30', 1, '2026-11-10 05:22:59', '2026-11-10 05:22:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_tindak_lanjut`
--

CREATE TABLE `t_tindak_lanjut` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL DEFAULT 0,
  `tahun_ajaran_id` int(11) NOT NULL DEFAULT 0,
  `tanggal_tindak_lanjut` date NOT NULL,
  `status` varchar(40) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_tindak_lanjut`
--

INSERT INTO `t_tindak_lanjut` (`id`, `siswa_id`, `kelas_id`, `tahun_ajaran_id`, `tanggal_tindak_lanjut`, `status`, `catatan`, `created_at`) VALUES
(1, 2, 19, 1, '2026-11-10', 'Proses Pembinaan', 'qqqq', '2026-11-10 06:58:02'),
(2, 1, 1, 1, '2026-11-10', 'Orang Tua Hadir', NULL, '2026-11-10 07:15:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT current_timestamp(),
  `password` varchar(225) NOT NULL,
  `remember_token` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Pengguna 1', 'user1@sekolah.sch.id', '2026-01-01 01:00:00', '$2y$10$.rpfZ27kgaqlE30I2wDRe.gUQv2nhmPKwAr4x7QjKCD2dp9pV2v1a', 'token001', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, 'Pengguna 2', 'user2@sekolah.sch.id', '2026-01-01 01:00:00', '$2y$10$wJ5o5JTVZLcZ18d93RlFEOiCGFSWWhKMmvZY0TiLG2PHH6U5K7J1m', 'token002', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, 'Pengguna 3', 'user3@sekolah.sch.id', '2026-01-01 01:00:00', 'password3', 'token003', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, 'Pengguna 4', 'user4@sekolah.sch.id', '2026-01-01 01:00:00', 'password4', 'token004', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, 'Pengguna 5', 'user5@sekolah.sch.id', '2026-01-01 01:00:00', 'password5', 'token005', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, 'Pengguna 6', 'user6@sekolah.sch.id', '2026-01-01 01:00:00', 'password6', 'token006', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, 'Pengguna 7', 'user7@sekolah.sch.id', '2026-01-01 01:00:00', 'password7', 'token007', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, 'Pengguna 8', 'user8@sekolah.sch.id', '2026-01-01 01:00:00', 'password8', 'token008', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, 'Pengguna 9', 'user9@sekolah.sch.id', '2026-01-01 01:00:00', 'password9', 'token009', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, 'Pengguna 10', 'user10@sekolah.sch.id', '2026-01-01 01:00:00', 'password10', 'token010', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, 'Pengguna 11', 'user11@sekolah.sch.id', '2026-01-01 01:00:00', 'password11', 'token011', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, 'Pengguna 12', 'user12@sekolah.sch.id', '2026-01-01 01:00:00', 'password12', 'token012', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, 'Pengguna 13', 'user13@sekolah.sch.id', '2026-01-01 01:00:00', 'password13', 'token013', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, 'Pengguna 14', 'user14@sekolah.sch.id', '2026-01-01 01:00:00', 'password14', 'token014', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, 'Pengguna 15', 'user15@sekolah.sch.id', '2026-01-01 01:00:00', 'password15', 'token015', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, 'Pengguna 16', 'user16@sekolah.sch.id', '2026-01-01 01:00:00', 'password16', 'token016', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, 'Pengguna 17', 'user17@sekolah.sch.id', '2026-01-01 01:00:00', 'password17', 'token017', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, 'Pengguna 18', 'user18@sekolah.sch.id', '2026-01-01 01:00:00', 'password18', 'token018', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, 'Pengguna 19', 'user19@sekolah.sch.id', '2026-01-01 01:00:00', 'password19', 'token019', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, 'Pengguna 20', 'user20@sekolah.sch.id', '2026-01-01 01:00:00', 'password20', 'token020', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, 'Pengguna 21', 'user21@sekolah.sch.id', '2026-01-01 01:00:00', 'password21', 'token021', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, 'Pengguna 22', 'user22@sekolah.sch.id', '2026-01-01 01:00:00', 'password22', 'token022', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, 'Pengguna 23', 'user23@sekolah.sch.id', '2026-01-01 01:00:00', 'password23', 'token023', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, 'Pengguna 24', 'user24@sekolah.sch.id', '2026-01-01 01:00:00', 'password24', 'token024', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, 'Pengguna 25', 'user25@sekolah.sch.id', '2026-01-01 01:00:00', 'password25', 'token025', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, 'Pengguna 26', 'user26@sekolah.sch.id', '2026-01-01 01:00:00', 'password26', 'token026', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, 'Pengguna 27', 'user27@sekolah.sch.id', '2026-01-01 01:00:00', 'password27', 'token027', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, 'Pengguna 28', 'user28@sekolah.sch.id', '2026-01-01 01:00:00', 'password28', 'token028', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, 'Pengguna 29', 'user29@sekolah.sch.id', '2026-01-01 01:00:00', 'password29', 'token029', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, 'Pengguna 30', 'user30@sekolah.sch.id', '2026-01-01 01:00:00', 'password30', 'token030', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, 'Pengguna 31', 'user31@sekolah.sch.id', '2026-01-01 01:00:00', 'password31', 'token031', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, 'Pengguna 32', 'user32@sekolah.sch.id', '2026-01-01 01:00:00', 'password32', 'token032', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, 'Pengguna 33', 'user33@sekolah.sch.id', '2026-01-01 01:00:00', 'password33', 'token033', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, 'Pengguna 34', 'user34@sekolah.sch.id', '2026-01-01 01:00:00', 'password34', 'token034', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, 'Pengguna 35', 'user35@sekolah.sch.id', '2026-01-01 01:00:00', 'password35', 'token035', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, 'Pengguna 36', 'user36@sekolah.sch.id', '2026-01-01 01:00:00', 'password36', 'token036', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, 'Pengguna 37', 'user37@sekolah.sch.id', '2026-01-01 01:00:00', 'password37', 'token037', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, 'Pengguna 38', 'user38@sekolah.sch.id', '2026-01-01 01:00:00', 'password38', 'token038', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, 'Pengguna 39', 'user39@sekolah.sch.id', '2026-01-01 01:00:00', 'password39', 'token039', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, 'Pengguna 40', 'user40@sekolah.sch.id', '2026-01-01 01:00:00', 'password40', 'token040', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, 'Pengguna 41', 'user41@sekolah.sch.id', '2026-01-01 01:00:00', 'password41', 'token041', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, 'Pengguna 42', 'user42@sekolah.sch.id', '2026-01-01 01:00:00', 'password42', 'token042', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, 'Pengguna 43', 'user43@sekolah.sch.id', '2026-01-01 01:00:00', 'password43', 'token043', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, 'Pengguna 44', 'user44@sekolah.sch.id', '2026-01-01 01:00:00', 'password44', 'token044', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, 'Pengguna 45', 'user45@sekolah.sch.id', '2026-01-01 01:00:00', 'password45', 'token045', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, 'Pengguna 46', 'user46@sekolah.sch.id', '2026-01-01 01:00:00', 'password46', 'token046', 'admin', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, 'Pengguna 47', 'user47@sekolah.sch.id', '2026-01-01 01:00:00', 'password47', 'token047', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, 'Pengguna 48', 'user48@sekolah.sch.id', '2026-01-01 01:00:00', 'password48', 'token048', 'wali_kelas', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, 'Pengguna 49', 'user49@sekolah.sch.id', '2026-01-01 01:00:00', 'password49', 'token049', 'siswa', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, 'Pengguna 50', 'user50@sekolah.sch.id', '2026-01-01 01:00:00', 'password50', 'token050', 'petugas', '2026-01-01 01:00:00', '2026-09-23 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `t_wali_kelas`
--

INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(2, 2, 2, 2, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(3, 3, 3, 3, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(4, 4, 4, 4, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(5, 5, 5, 5, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(6, 6, 6, 6, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(7, 7, 7, 7, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(8, 8, 8, 8, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(9, 9, 9, 9, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(10, 10, 10, 10, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(11, 11, 11, 11, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(12, 12, 12, 12, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(13, 13, 13, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(14, 14, 14, 14, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(15, 15, 15, 15, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(16, 16, 16, 16, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(17, 17, 17, 17, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(18, 18, 18, 18, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(19, 19, 19, 19, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(20, 20, 20, 20, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(21, 21, 21, 21, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(22, 22, 22, 22, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(23, 23, 23, 23, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(24, 24, 24, 24, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(25, 25, 25, 25, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(26, 26, 26, 26, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(27, 27, 27, 27, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(28, 28, 28, 28, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(29, 29, 29, 29, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(30, 30, 30, 30, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(31, 31, 31, 31, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(32, 32, 32, 32, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(33, 33, 33, 33, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(34, 34, 34, 34, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(35, 35, 35, 35, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(36, 36, 36, 36, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(37, 37, 37, 37, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(38, 38, 38, 38, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(39, 39, 39, 39, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(40, 40, 40, 40, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(41, 41, 41, 41, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(42, 42, 42, 42, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(43, 43, 43, 43, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(44, 44, 44, 44, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(45, 45, 45, 45, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(46, 46, 46, 46, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(47, 47, 47, 47, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(48, 48, 48, 48, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(49, 49, 49, 49, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
(50, 50, 50, 50, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-09-23 01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`);

--
-- Indeks untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `pelanggaran_id` (`pelanggaran_id`),
  ADD KEY `kelas_id_2` (`kelas_id`),
  ADD KEY `pelanggaran_id_2` (`pelanggaran_id`),
  ADD KEY `pelanggaran_kategori_id` (`pelanggaran_kategori_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_tindak_lanjut`
--
ALTER TABLE `t_tindak_lanjut`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tindak_lanjut_siswa` (`siswa_id`);

--
-- Indeks untuk tabel `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT untuk tabel `t_tindak_lanjut`
--
ALTER TABLE `t_tindak_lanjut`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `t_guru`
--
ALTER TABLE `t_guru`
  ADD CONSTRAINT `t_guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `t_users` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`),
  ADD CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`),
  ADD CONSTRAINT `t_kelas_siswa_ibfk_3` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD CONSTRAINT `t_pelanggaran_ibfk_1` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_4` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`),
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_6` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`),
  ADD CONSTRAINT `t_wali_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`),
  ADD CONSTRAINT `t_wali_kelas_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
