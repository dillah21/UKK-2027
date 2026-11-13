-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Nov 2026 pada 17.47
-- Versi server: 10.11.16-MariaDB
-- Versi PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_ukk_2026`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, '1980070007', 'ARI SANDRIANA', 'asepbastaman@gmail.com', 1, 7, '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(2, 2, 1, 2, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(3, 3, 1, 3, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(4, 4, 1, 4, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(5, 5, 1, 5, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(6, 6, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(7, 7, 1, 7, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(8, 8, 1, 8, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(9, 9, 1, 9, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(10, 10, 1, 10, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(11, 11, 1, 11, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(12, 12, 1, 12, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(13, 13, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(14, 14, 1, 14, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(15, 15, 1, 15, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(16, 16, 1, 16, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(17, 17, 1, 17, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(18, 18, 1, 18, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(19, 19, 1, 19, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(20, 20, 1, 20, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(21, 21, 1, 21, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(22, 22, 1, 22, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(23, 23, 1, 23, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(24, 24, 1, 24, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(25, 25, 1, 25, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(26, 26, 1, 26, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(27, 27, 1, 27, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(28, 28, 1, 28, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(29, 29, 1, 29, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(30, 30, 1, 30, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(31, 31, 1, 31, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(32, 32, 1, 32, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(33, 33, 1, 33, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(34, 34, 1, 34, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(35, 35, 1, 35, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(36, 36, 1, 36, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(37, 37, 1, 37, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(38, 38, 1, 38, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(39, 39, 1, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(40, 40, 1, 40, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(41, 41, 1, 41, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(42, 42, 1, 42, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(43, 43, 1, 43, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(44, 44, 1, 44, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(45, 45, 1, 45, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(46, 46, 1, 46, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(47, 47, 1, 47, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(48, 48, 1, 48, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(49, 49, 1, 49, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(50, 50, 1, 50, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(51, 51, 1, 51, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(52, 52, 1, 1, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(53, 53, 1, 2, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(54, 54, 1, 3, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(55, 55, 1, 4, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(56, 56, 1, 5, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(57, 57, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(58, 58, 1, 7, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(59, 59, 1, 8, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(60, 60, 1, 9, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(61, 61, 1, 10, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(62, 62, 1, 11, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(63, 63, 1, 12, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(64, 64, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(65, 65, 1, 14, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(66, 66, 1, 15, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(67, 67, 1, 16, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(68, 68, 1, 17, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(69, 69, 1, 18, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(70, 70, 1, 19, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(71, 71, 1, 20, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(72, 72, 1, 21, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(73, 73, 1, 22, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(74, 74, 1, 23, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(75, 75, 1, 24, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(76, 76, 1, 25, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(77, 77, 1, 26, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(78, 78, 1, 27, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(79, 79, 1, 28, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(80, 80, 1, 29, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(81, 81, 1, 30, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(82, 82, 1, 31, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(83, 83, 1, 32, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(84, 84, 1, 33, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(85, 85, 1, 34, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(86, 86, 1, 35, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(87, 87, 1, 36, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(88, 88, 1, 37, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(89, 89, 1, 38, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(90, 90, 1, 39, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(91, 91, 1, 40, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(92, 92, 1, 41, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(93, 93, 1, 42, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(94, 94, 1, 43, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(95, 95, 1, 44, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(96, 96, 1, 45, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(97, 97, 1, 46, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(98, 98, 1, 47, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(99, 99, 1, 48, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(100, 100, 1, 49, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(101, 101, 1, 50, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(102, 102, 1, 51, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(103, 103, 1, 1, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(104, 104, 1, 2, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(105, 105, 1, 3, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(106, 106, 1, 4, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(107, 107, 1, 5, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(108, 108, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(109, 109, 1, 7, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(110, 110, 1, 8, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12'),
(111, 111, 1, 9, '2026-07-01', '2027-06-30', 1, '2026-11-10 16:45:12', '2026-11-10 16:45:12');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `kelas_id`, `nama_kelas`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Adittia Nur Lantip', 1, '10 RPL 1', 1, 'Terlambat Masuk Sekolah', 1, 1, 'Aanisah Putri Nur Aini, S.Pd', '2026-11-01', 'Terlambat datang ke gerbang sekolah', 7, 'Diberikan teguran dan pembinaan oleh wali kelas.', 'Proses', '2026-11-10 16:46:42', '2026-11-10 16:46:42'),
(2, 1, 2, 'Agnia Rosya Noerillah', 4, '10 BDPM 1', 2, 'Tidak Mengikuti Apel Pagi', 2, 2, 'Ai Nuryaidah, S.Pd', '2026-11-02', 'Tidak ikut baris saat apel', 9, 'Diberikan teguran dan pembinaan oleh wali kelas.', 'Selesai', '2026-11-10 16:46:42', '2026-11-10 16:46:42'),
(3, 1, 3, 'Anisa Amirulla', 11, '11 RPL 3', 3, 'Tidak Mengikuti Upacara Bendera', 3, 4, 'Ansor Muhajir, S.T', '2026-11-03', 'Membolos saat upacara', 11, 'Diberikan pembinaan oleh guru BK.', 'Proses', '2026-11-10 16:46:42', '2026-11-10 16:46:42'),
(4, 1, 4, 'Aradea Kandi', 16, '10 RPL 4', 4, 'Meninggalkan Kelas Tanpa Izin', 4, 5, 'Ari Sandriana, S.Kom', '2026-11-04', 'Keluar kelas ketika jam pelajaran berlangsung', 13, 'Diberikan teguran tertulis.', 'Selesai', '2026-11-10 16:46:42', '2026-11-10 16:46:42'),
(5, 1, 5, 'Arfa Diesa Okmaleliza', 19, '10 BDPM 4', 5, 'Membolos Pelajaran', 5, 6, 'Arif Kusmawan, S.Pd', '2026-11-05', 'Tidak masuk di jam pelajaran terakhir', 15, 'Pemanggilan orang tua.', 'Proses', '2026-11-10 16:46:42', '2026-11-10 16:46:42');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '102410544', '0000000001', 'Adittia Nur Lantip', 'L', '2010-01-15', 'Jl. Pendidikan No. 1, Tasikmalaya', 1, '2025-12-31 18:00:00', '2026-11-09 17:59:48'),
(2, '102410545', '0000000002', 'Agnia Rosya Noerillah', 'P', '2010-02-20', 'Jl. Pendidikan No. 2, Tasikmalaya', 1, '2026-02-01 18:00:00', '2026-02-01 18:00:00'),
(3, '102410546', '0000000003', 'Anisa Amirulla', 'P', '2010-03-12', 'Jl. Pendidikan No. 3, Tasikmalaya', 1, '2026-03-02 18:00:00', '2026-03-02 18:00:00'),
(4, '102410547', '0000000004', 'Aradea Kandi', 'L', '2010-04-25', 'Jl. Pendidikan No. 4, Tasikmalaya', 1, '2026-04-03 18:00:00', '2026-04-03 18:00:00'),
(5, '102410548', '0000000005', 'Arfa Diesa Okmaleliza', 'P', '2010-05-18', 'Jl. Pendidikan No. 5, Tasikmalaya', 1, '2026-05-04 18:00:00', '2026-05-04 18:00:00'),
(6, '102410549', '0000000006', 'Fitri Nur Syami', 'P', '2010-06-07', 'Jl. Pendidikan No. 6, Tasikmalaya', 1, '2026-06-05 18:00:00', '2026-06-05 18:00:00'),
(7, '102410550', '0000000007', 'Junita Putri Nur Rahman', 'P', '2010-07-22', 'Jl. Pendidikan No. 7, Tasikmalaya', 1, '2026-07-06 18:00:00', '2026-07-06 18:00:00'),
(8, '102410551', '0000000008', 'Mohammad Atalarik Ali Syahbana', 'L', '2010-08-14', 'Jl. Pendidikan No. 8, Tasikmalaya', 1, '2026-08-07 18:00:00', '2026-08-07 18:00:00'),
(9, '102410552', '0000000009', 'Muhammad Fahril Fahrurozi', 'L', '2010-09-30', 'Jl. Pendidikan No. 9, Tasikmalaya', 1, '2026-09-08 18:00:00', '2026-09-08 18:00:00'),
(10, '102410750', '0000000010', 'Muhammad Adnan Syaeful Islami', 'L', '2010-10-11', 'Jl. Pendidikan No. 10, Tasikmalaya', 1, '2026-10-09 18:00:00', '2026-10-09 18:00:00'),
(11, '102410553', '0000000011', 'Muhammad Zahru Aqila Fikri', 'L', '2010-11-05', 'Jl. Pendidikan No. 11, Tasikmalaya', 1, '2026-11-10 18:00:00', '2026-11-10 18:00:00'),
(12, '102410554', '0000000012', 'Nadia Zulfa Soraya', 'P', '2010-12-19', 'Jl. Pendidikan No. 12, Tasikmalaya', 1, '2026-12-11 18:00:00', '2026-12-11 18:00:00'),
(13, '102410555', '0000000013', 'Nazril Ilham', 'L', '2011-01-08', 'Jl. Pendidikan No. 13, Tasikmalaya', 1, '2026-01-12 18:00:00', '2026-01-12 18:00:00'),
(14, '102410556', '0000000014', 'Puspita Dewi', 'P', '2011-02-17', 'Jl. Pendidikan No. 14, Tasikmalaya', 1, '2026-02-13 18:00:00', '2026-02-13 18:00:00'),
(15, '102410558', '0000000015', 'Rakha Muhammad Naykal', 'L', '2011-03-29', 'Jl. Pendidikan No. 15, Tasikmalaya', 1, '2026-03-14 18:00:00', '2026-03-14 18:00:00'),
(16, '102410559', '0000000016', 'Rani Melani', 'P', '2011-04-13', 'Jl. Pendidikan No. 16, Tasikmalaya', 1, '2026-04-15 18:00:00', '2026-04-15 18:00:00'),
(17, '102410560', '0000000017', 'Reihan Muzaki', 'L', '2011-05-21', 'Jl. Pendidikan No. 17, Tasikmalaya', 1, '2026-05-16 18:00:00', '2026-05-16 18:00:00'),
(18, '102410561', '0000000018', 'Resa Juliansah', 'L', '2011-06-09', 'Jl. Pendidikan No. 18, Tasikmalaya', 1, '2026-06-17 18:00:00', '2026-06-17 18:00:00'),
(19, '102410562', '0000000019', 'Rizki Permata Sugianto', 'L', '2011-07-27', 'Jl. Pendidikan No. 19, Tasikmalaya', 1, '2026-07-18 18:00:00', '2026-07-18 18:00:00'),
(20, '102410563', '0000000020', 'Sahira Firaj', 'P', '2011-08-16', 'Jl. Pendidikan No. 20, Tasikmalaya', 1, '2026-08-19 18:00:00', '2026-08-19 18:00:00'),
(21, '102410564', '0000000021', 'Sendi Mardian', 'L', '2011-09-04', 'Jl. Pendidikan No. 21, Tasikmalaya', 1, '2026-09-20 18:00:00', '2026-11-09 18:00:00'),
(22, '102410755', '0000000022', 'Shahaidan Nizzar', 'L', '2011-10-23', 'Jl. Pendidikan No. 22, Tasikmalaya', 1, '2026-10-21 18:00:00', '2026-10-21 18:00:00'),
(23, '102410565', '0000000023', 'Sherly Satriani', 'P', '2011-11-14', 'Jl. Pendidikan No. 23, Tasikmalaya', 1, '2026-11-22 18:00:00', '2026-11-22 18:00:00'),
(24, '102410566', '0000000024', 'Sofinurjanah', 'P', '2011-12-02', 'Jl. Pendidikan No. 24, Tasikmalaya', 1, '2026-12-23 18:00:00', '2026-12-23 18:00:00'),
(25, '102410567', '0000000025', 'Suraiya Salsabila Azzahira', 'P', '2010-01-28', 'Jl. Pendidikan No. 25, Tasikmalaya', 1, '2026-01-24 18:00:00', '2026-01-24 18:00:00'),
(26, '102410568', '0000000026', 'Toni Antonio', 'L', '2010-02-11', 'Jl. Pendidikan No. 26, Tasikmalaya', 1, '2026-02-25 18:00:00', '2026-02-25 18:00:00'),
(27, '102611149', '0000000027', 'Abdul Malik Fajar', 'L', '2009-01-12', 'Jl. Contoh Melati No. 1, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(28, '102611150', '0000000028', 'Agin Ginanjar Saputra', 'L', '2009-03-21', 'Jl. Contoh Mawar No. 2, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(29, '102611151', '0000000029', 'Algi Athari', 'L', '2009-05-14', 'Jl. Contoh Anggrek No. 3, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(30, '102611152', '0000000030', 'Arystya Apriliandi Franki', 'L', '2009-07-09', 'Jl. Contoh Kenanga No. 4, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(31, '102611153', '0000000031', 'Billah Abdillah', 'L', '2009-02-18', 'Jl. Contoh Dahlia No. 5, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(32, '102611154', '0000000032', 'Dhimas Dwi Ramadhan', 'L', '2009-04-25', 'Jl. Contoh Teratai No. 6, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(33, '102611155', '0000000033', 'Fadil Ramdhani', 'L', '2009-06-11', 'Jl. Contoh Flamboyan No. 7, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(34, '102611478', '0000000034', 'Faraz Fachri Muharram', 'L', '2009-08-17', 'Jl. Contoh Cempaka No. 8, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(35, '102611157', '0000000035', 'Haikal Ilham Suhanda', 'L', '2009-09-23', 'Jl. Contoh Kamboja No. 9, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(36, '102611158', '0000000036', 'Laodra Roba Ahfiana', 'L', '2009-10-05', 'Jl. Contoh Cemara No. 10, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(37, '102611159', '0000000037', 'Lintang Febrian Maulana', 'L', '2009-11-19', 'Jl. Contoh Pinus No. 11, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(38, '102611160', '0000000038', 'Lisan Dzakir Ramadhan', 'L', '2009-12-08', 'Jl. Contoh Merpati No. 12, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(39, '102611161', '0000000039', 'Muhamad Abylah Azam', 'L', '2010-01-16', 'Jl. Contoh Rajawali No. 13, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(40, '102611162', '0000000040', 'Muhamad Algiansyah', 'L', '2010-02-22', 'Jl. Contoh Garuda No. 14, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(41, '102611163', '0000000041', 'Muhamad Azril Yasin', 'L', '2010-03-13', 'Jl. Contoh Merak No. 15, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(42, '102611164', '0000000042', 'Muhamad Itshar', 'L', '2010-04-27', 'Jl. Contoh Elang No. 16, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(43, '102611165', '0000000043', 'Muhamad Nasabandy', 'L', '2010-05-09', 'Jl. Contoh Rajin No. 17, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(44, '102611166', '0000000044', 'Muhammad Adam Efathurrohman', 'L', '2010-06-15', 'Jl. Contoh Sukamaju No. 18, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(45, '102611167', '0000000045', 'Muhammad Dzikri Abdul Jalil', 'L', '2010-07-20', 'Jl. Contoh Sukaraja No. 19, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(46, '102611476', '0000000046', 'Muhammad Rafa Firmansyah', 'L', '2010-08-11', 'Jl. Contoh Sukasari No. 20, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(47, '102611169', '0000000047', 'Muhammad Rizky Hidayatullah', 'L', '2010-09-24', 'Jl. Contoh Cibereum No. 21, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(48, '102611170', '0000000048', 'Nasta Pratama Supriatna', 'L', '2010-10-06', 'Jl. Contoh Cihideung No. 22, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(49, '102611171', '0000000049', 'Raditia Purnama', 'L', '2010-11-18', 'Jl. Contoh Indihiang No. 23, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(50, '102611172', '0000000050', 'Rahmat Suparman', 'L', '2010-12-12', 'Jl. Contoh Kawalu No. 24, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(51, '102611173', '0000000051', 'Rama Manramsyah Gustian Pratama', 'L', '2009-02-05', 'Jl. Contoh Mangkubumi No. 25, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(52, '102611174', '0000000052', 'Rehan Nadi Saputra', 'L', '2009-04-19', 'Jl. Contoh Tamansari No. 26, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(53, '102611175', '0000000053', 'Rof Rof Al Arof Subul Amin', 'L', '2009-06-23', 'Jl. Contoh Singaparna No. 27, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(54, '102611176', '0000000054', 'Sandi Permana', 'L', '2009-08-08', 'Jl. Contoh Manonjaya No. 28, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(55, '102611177', '0000000055', 'Tubagus Rizqi', 'L', '2009-10-14', 'Jl. Contoh Rajapolah No. 29, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(56, '102611178', '0000000056', 'Wildan Nurjaman', 'L', '2009-12-20', 'Jl. Contoh Cisayong No. 30, Tasikmalaya', 1, '2026-11-06 01:18:37', '2026-11-06 01:18:37'),
(57, '102410341', '0000000057', 'Adi Bayu Maulana Putra', 'L', '2008-01-12', 'Jl. Melati, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(58, '102410343', '0000000058', 'Agan Basyri Sa\'ady', 'L', '2008-02-15', 'Jl. Mawar, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(59, '102410344', '0000000059', 'Ali Rahmat Mutaqin', 'L', '2008-03-21', 'Jl. Anggrek, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(60, '102410345', '0000000060', 'Alzi Kusnendi', 'L', '2008-04-09', 'Jl. Kenanga, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(61, '102410346', '0000000061', 'Andika Nugraha', 'L', '2008-05-17', 'Jl. Dahlia, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(62, '102410347', '0000000062', 'Andri Mabrur', 'L', '2008-06-11', 'Jl. Teratai, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(63, '102410349', '0000000063', 'Dafa Ezar Ramadan', 'L', '2008-07-23', 'Jl. Cempaka, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(64, '102410350', '0000000064', 'Defri Muhamad Ramadhan', 'L', '2008-08-14', 'Jl. Cemara, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(65, '102410351', '0000000065', 'Deva Al Fauzan', 'L', '2008-09-05', 'Jl. Merpati, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(66, '102410352', '0000000066', 'Dika Ardiansyah Putra', 'L', '2008-10-19', 'Jl. Rajawali, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(67, '102410353', '0000000067', 'Fadli Muhammad Ramdhan', 'L', '2008-11-08', 'Jl. Garuda, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(68, '102410354', '0000000068', 'Febi Danil Gunawan', 'L', '2008-12-16', 'Jl. Merak, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(69, '102410355', '0000000069', 'Gabriel Alhazmi Adiyasa', 'L', '2008-01-25', 'Jl. Elang, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(70, '102410356', '0000000070', 'Gisna Sufi Ginanjar', 'L', '2008-02-27', 'Jl. Pinus, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(71, '102410358', '0000000071', 'Lutfi Indriawan', 'L', '2008-03-13', 'Jl. Flamboyan, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(72, '102410359', '0000000072', 'Moch Alif Sayyandra Abipraya', 'L', '2008-04-22', 'Jl. Sukamaju, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(73, '102410360', '0000000073', 'Muhamad Al Mu\'min Akbar', 'L', '2008-05-06', 'Jl. Sukaraja, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(74, '102410361', '0000000074', 'Muhamad Rafi Ramdani', 'L', '2008-06-18', 'Jl. Sukasari, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(75, '102410362', '0000000075', 'Muhamad Zulpi Zulkipli', 'L', '2008-07-29', 'Jl. Cibeureum, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(76, '102410364', '0000000076', 'Muhammad Faisal Arrizal', 'L', '2008-08-12', 'Jl. Cihideung, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(77, '102410366', '0000000077', 'Muhammad Rafliyatu Rizqi Nur Rohman', 'L', '2008-09-24', 'Jl. Indihiang, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(78, '102410367', '0000000078', 'Muhammad Ridho Qushoiri', 'L', '2008-10-07', 'Jl. Kawalu, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(79, '102410368', '0000000079', 'Raditia Nugraha', 'L', '2008-11-15', 'Jl. Tamansari, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(80, '102410369', '0000000080', 'Raeval Legya Rachman', 'L', '2008-12-03', 'Jl. Mangkubumi, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(81, '102410370', '0000000081', 'Rafa Azkia', 'L', '2008-01-19', 'Jl. Singaparna, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(82, '102410371', '0000000082', 'Refan Sultansah', 'L', '2008-02-08', 'Jl. Rajapolah, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(83, '102410372', '0000000083', 'Reihan Al Azhar', 'L', '2008-03-26', 'Jl. Cisayong, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(84, '112510757', '0000000084', 'Sani Achmad Fikri', 'L', '2008-04-16', 'Jl. Leuwisari, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(85, '102410373', '0000000085', 'Sendy Ramdhani', 'L', '2008-05-28', 'Jl. Manonjaya, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(86, '102410374', '0000000086', 'Surahman Wahdan Andika', 'L', '2008-06-09', 'Jl. Salawu, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(87, '102410375', '0000000087', 'Tahmid Hidayatulloh', 'L', '2008-07-17', 'Jl. Sukaratu, Tasikmalaya', 1, '2026-11-06 01:30:14', '2026-11-06 01:30:14'),
(88, '102410520', '0091000001', 'Alfin Rahadian', 'L', '2008-01-12', 'Jl. Pendidikan No. 1', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(89, '102410521', '0091000002', 'Alisa Ramadhani', 'P', '2008-02-15', 'Jl. Pendidikan No. 2', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(90, '102410522', '0091000003', 'Alma Aura Livia', 'P', '2008-03-20', 'Jl. Pendidikan No. 3', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(91, '102410523', '0091000004', 'Anis Khoerunnisa Nur Ramadani', 'P', '2008-04-18', 'Jl. Pendidikan No. 4', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(92, '102410524', '0091000005', 'Azmil Amira', 'P', '2008-05-22', 'Jl. Pendidikan No. 5', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(93, '102410525', '0091000006', 'Azril Akbar Fawji Sedillah', 'L', '2008-06-10', 'Jl. Pendidikan No. 6', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(94, '102410527', '0091000007', 'Erlangga Saputra Hidayat', 'L', '2008-07-14', 'Jl. Pendidikan No. 7', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(95, '102410528', '0091000008', 'Hilma Humma Tunnajah', 'P', '2008-08-19', 'Jl. Pendidikan No. 8', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(96, '102410529', '0091000009', 'Ilma Najya', 'P', '2008-09-12', 'Jl. Pendidikan No. 9', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(97, '102410531', '0091000010', 'Julianda Dharma Pangestu', 'L', '2008-10-16', 'Jl. Pendidikan No. 10', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(98, '102410532', '0091000011', 'Kayla Nur Fadilah', 'P', '2008-11-21', 'Jl. Pendidikan No. 11', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(99, '102410533', '0091000012', 'Moza Dina Nabillah', 'P', '2008-12-09', 'Jl. Pendidikan No. 12', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(100, '102410534', '0091000013', 'Muhamad Alfarizi', 'L', '2008-01-25', 'Jl. Pendidikan No. 13', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(101, '102410535', '0091000014', 'Muhamad Fahrin Adam', 'L', '2008-02-27', 'Jl. Pendidikan No. 14', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(102, '102410536', '0091000015', 'Muhamad Zidane Maulidan', 'L', '2008-03-16', 'Jl. Pendidikan No. 15', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(103, '102410537', '0091000016', 'Muhamad Zulfan Nuralfansyah', 'L', '2008-04-24', 'Jl. Pendidikan No. 16', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(104, '102410538', '0091000017', 'Natiya Nur Azkiya', 'P', '2008-05-13', 'Jl. Pendidikan No. 17', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(105, '102410539', '0091000018', 'Novita Puspitasari', 'P', '2008-06-29', 'Jl. Pendidikan No. 18', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(106, '102410540', '0091000019', 'Nucky Andreas Febrian', 'L', '2008-07-08', 'Jl. Pendidikan No. 19', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(107, '102410543', '0091000020', 'Raditya Jeti Prasetya', 'L', '2008-08-26', 'Jl. Pendidikan No. 20', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(108, '102410741', '0091000021', 'Rangga Ramadiyanto Nurhakim', 'L', '2008-09-17', 'Jl. Pendidikan No. 21', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(109, '102410541', '0091000022', 'Risqi Muhamad Ihwani', 'L', '2008-10-23', 'Jl. Pendidikan No. 22', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(110, '102410542', '0091000023', 'Salwa Sabilatul Barakah', 'P', '2008-11-11', 'Jl. Pendidikan No. 23', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08'),
(111, '102410753', '0091000024', 'Tazkia Zahra', 'P', '2008-12-20', 'Jl. Pendidikan No. 24', 1, '2026-11-06 01:57:08', '2026-11-06 01:57:08');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 'ARI SANDRIANA', 'user7@sekolah.sch.id', '2026-01-01 01:00:00', 'password7', 'token007', 'guru', '2026-01-01 01:00:00', '2026-09-23 01:00:00'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indeks untuk tabel yang dibuang
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT untuk tabel `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT untuk tabel `t_tindak_lanjut`
--
ALTER TABLE `t_tindak_lanjut`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
