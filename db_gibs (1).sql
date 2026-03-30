-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 16, 2026 at 04:34 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_gibs`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_bulanan`
--

CREATE TABLE `catatan_bulanan` (
  `id_catatan_bulanan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL COMMENT 'FK ke siswa.id_siswa (Siswa yang dicatat)',
  `id_kelas` int(11) NOT NULL COMMENT 'FK ke kelas.id_kelas (Kelas tempat siswa berada)',
  `id_guru` int(11) NOT NULL COMMENT 'FK ke guru.id_guru (Wali kelas yang membuat catatan)',
  `id_tahun_ajar` int(11) NOT NULL COMMENT 'FK ke tahun_ajar.id_tahun_ajar',
  `periode` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Periode catatan (MM-YYYY)',
  `isi_catatan_bulanan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_semester`
--

CREATE TABLE `catatan_semester` (
  `id_catatan_semester` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL COMMENT 'FK ke siswa.id_siswa (Siswa yang dicatat)',
  `id_kelas` int(11) NOT NULL COMMENT 'FK ke kelas.id_kelas (Kelas tempat siswa berada)',
  `id_guru` int(11) NOT NULL COMMENT 'FK ke guru.id_guru (Wali kelas yang membuat catatan)',
  `id_tahun_ajar` int(11) NOT NULL COMMENT 'FK ke tahun_ajar.id_tahun_ajar',
  `isi_catatan_semester` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_jadwal`
--

CREATE TABLE `config_jadwal` (
  `id_config` int(11) NOT NULL,
  `jam_mulai_default` time DEFAULT '07:00:00',
  `durasi_jp_default` int(11) DEFAULT '45' COMMENT 'Durasi dalam menit',
  `jp_per_hari_default` int(11) DEFAULT '8',
  `hari_libur` text COMMENT 'JSON array hari libur',
  `tahun_ajaran_aktif` varchar(20) DEFAULT NULL,
  `semester_aktif` enum('Ganjil','Genap') DEFAULT 'Ganjil',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id_guru` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_guru` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_guru` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_hrt` tinyint(1) DEFAULT '0',
  `id_kelas` int(11) DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru_mapel`
--

CREATE TABLE `guru_mapel` (
  `id_guru` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hode`
--

CREATE TABLE `hode` (
  `id` int(20) NOT NULL,
  `id_guru` int(20) NOT NULL,
  `nama_divisi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `jenjang`
--

CREATE TABLE `jenjang` (
  `id_jenjang` int(11) NOT NULL,
  `nama_jenjang` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_bulanan`
--

CREATE TABLE `kehadiran_bulanan` (
  `id_kehadiran_bulanan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_tahun_ajar` int(11) NOT NULL,
  `periode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hadir` int(11) DEFAULT '0',
  `sakit` int(11) DEFAULT '0',
  `izin` int(11) DEFAULT '0',
  `tanpa_keterangan` int(11) DEFAULT '0',
  `id_guru` int(11) NOT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_lock` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_harian`
--

CREATE TABLE `kehadiran_harian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_tahun_ajar` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('H','S','I','A','L') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'H' COMMENT 'H=Hadir, S=Sakit, I=Izin, A=Alpha, L=Libur/Guru Berhalangan',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_hrt`
--

CREATE TABLE `kehadiran_hrt` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_tahun_ajar` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('H','S','I','A','L') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'H' COMMENT 'H=Hadir, S=Sakit, I=Izin, A=Alpha, L=Libur',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Alasan jika sakit/izin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_semester`
--

CREATE TABLE `kehadiran_semester` (
  `id_kehadiran_semester` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_tahun_ajar` int(11) NOT NULL,
  `hadir` int(11) DEFAULT '0',
  `sakit` int(11) DEFAULT '0',
  `izin` int(11) DEFAULT '0',
  `tanpa_keterangan` int(11) DEFAULT '0',
  `id_guru_hrt` int(11) NOT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `id_jenjang` int(11) NOT NULL,
  `nama_kelas` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fase` enum('D','E','F') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `komponen_penilaian`
--

CREATE TABLE `komponen_penilaian` (
  `id_komponen` int(11) NOT NULL,
  `id_tugas` int(11) NOT NULL,
  `periode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `komponen_json` text COLLATE utf8mb4_unicode_ci COMMENT 'Stores JSON array of component names',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_generate_jadwal`
--

CREATE TABLE `log_generate_jadwal` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tanggal_generate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kelas_generated` text COMMENT 'JSON array id_kelas',
  `total_jadwal` int(11) DEFAULT '0',
  `status` enum('success','failed','partial') DEFAULT 'success',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `major`
--

CREATE TABLE `major` (
  `id_major` int(11) NOT NULL,
  `nama_major` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mapel`
--

CREATE TABLE `mapel` (
  `id_mapel` int(11) NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Common','Major','Mulok') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mapel_major`
--

CREATE TABLE `mapel_major` (
  `id_mapel` int(11) NOT NULL,
  `id_major` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mengajar`
--

CREATE TABLE `mengajar` (
  `id_mengajar` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_major` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_bulanan`
--

CREATE TABLE `nilai_bulanan` (
  `id_nilai_bulanan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_tugas` int(11) NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `periode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `is_lock` tinyint(1) NOT NULL DEFAULT '0',
  `nilai_komponen_json` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_komponen_detail`
--

CREATE TABLE `nilai_komponen_detail` (
  `id_nilai_detail` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL COMMENT 'FK ke siswa.id_siswa',
  `id_tugas` int(11) NOT NULL COMMENT 'FK ke tugas_mengajar.id_tugas',
  `periode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Format: MM-YYYY (contoh: 01-2026)',
  `nama_komponen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama komponen penilaian (Tugas, UTS, Praktik, dll)',
  `nilai` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke users.id_user (guru yang input)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel detail nilai per komponen penilaian';

-- --------------------------------------------------------

--
-- Table structure for table `nilai_semester`
--

CREATE TABLE `nilai_semester` (
  `id_nilai_semester` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_major` int(11) DEFAULT NULL,
  `id_tahun_ajar` int(11) NOT NULL,
  `nilai_uas` decimal(5,2) NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL,
  `is_lock` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `catatan_plus` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_minus` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kelas` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fase` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_tka`
--

CREATE TABLE `nilai_tka` (
  `id_nilai_tka` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nomor_peserta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Format: T3-25-15-05-0039-0001-8 0084013548',
  `nama_peserta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ttl` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tempat dan Tanggal Lahir (contoh: Jombang, 28 Juni 2008)',
  `bahasa_indonesia` decimal(5,2) NOT NULL,
  `kategori_bindo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matematika` decimal(5,2) NOT NULL,
  `kategori_mtk` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bahasa_inggris` decimal(5,2) NOT NULL,
  `kategori_bing` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mapel_pilihan_1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_1` decimal(5,2) DEFAULT NULL,
  `kategori_1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mapel_pilihan_2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_2` decimal(5,2) DEFAULT NULL,
  `kategori_2` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket_mapel`
--

CREATE TABLE `paket_mapel` (
  `id_paket_mapel` int(11) NOT NULL,
  `nama_paket` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id_report` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_pengguna` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_masalah` enum('Bug','Permintaan Fitur','Saran UI/UX','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bug',
  `prioritas` enum('Rendah','Sedang','Tinggi','Kritis') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sedang',
  `halaman_terkait` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail_masalah` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `langkah_replikasi` text COLLATE utf8mb4_unicode_ci,
  `lampiran_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Baru','Diproses','Selesai','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Baru',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sakit_siswa`
--

CREATE TABLE `sakit_siswa` (
  `id_sakit` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status_akhir` enum('Masih Sakit','Kembali ke Kelas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Masih Sakit',
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nisn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_siswa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jk` enum('Laki-Laki','Perempuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_major` int(11) DEFAULT NULL,
  `tpt_lahir` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `angkatan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajar`
--

CREATE TABLE `tahun_ajar` (
  `id_tahun_ajar` int(11) NOT NULL,
  `semester` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_pelajaran` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tugas_mengajar`
--

CREATE TABLE `tugas_mengajar` (
  `id_tugas` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_major` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sso_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','guru','siswa','klinik') COLLATE utf8mb4_unicode_ci DEFAULT 'siswa',
  `profile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile.png',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `catatan_bulanan`
--
ALTER TABLE `catatan_bulanan`
  ADD PRIMARY KEY (`id_catatan_bulanan`),
  ADD UNIQUE KEY `uk_siswa_periode` (`id_siswa`,`periode`),
  ADD KEY `fk_catatan_kelas` (`id_kelas`),
  ADD KEY `fk_catatan_guru` (`id_guru`),
  ADD KEY `fk_catatan_tahun` (`id_tahun_ajar`);

--
-- Indexes for table `config_jadwal`
--
ALTER TABLE `config_jadwal`
  ADD PRIMARY KEY (`id_config`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  ADD PRIMARY KEY (`id_guru`,`id_mapel`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `hode`
--
ALTER TABLE `hode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `idx_hari_jam` (`hari`,`jam_mulai`);

--
-- Indexes for table `jenjang`
--
ALTER TABLE `jenjang`
  ADD PRIMARY KEY (`id_jenjang`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kehadiran_bulanan`
--
ALTER TABLE `kehadiran_bulanan`
  ADD PRIMARY KEY (`id_kehadiran_bulanan`),
  ADD UNIQUE KEY `unique_bulanan` (`id_siswa`,`id_mapel`,`periode`),
  ADD KEY `fk_kehadiran_bulanan_kelas` (`id_kelas`),
  ADD KEY `fk_kehadiran_bulanan_mapel` (`id_mapel`),
  ADD KEY `fk_kehadiran_bulanan_tahun` (`id_tahun_ajar`),
  ADD KEY `fk_kehadiran_bulanan_guru` (`id_guru`);

--
-- Indexes for table `kehadiran_harian`
--
ALTER TABLE `kehadiran_harian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kehadiran_harian_id_siswa_index` (`id_siswa`),
  ADD KEY `kehadiran_harian_id_kelas_index` (`id_kelas`),
  ADD KEY `kehadiran_harian_id_mapel_index` (`id_mapel`),
  ADD KEY `kehadiran_harian_id_guru_index` (`id_guru`),
  ADD KEY `kehadiran_harian_id_tahun_ajar_index` (`id_tahun_ajar`),
  ADD KEY `idx_guru_tanggal` (`id_guru`,`tanggal`),
  ADD KEY `idx_kelas_mapel_tanggal` (`id_kelas`,`id_mapel`,`tanggal`),
  ADD KEY `idx_siswa_mapel_tanggal` (`id_siswa`,`id_mapel`,`tanggal`);

--
-- Indexes for table `kehadiran_hrt`
--
ALTER TABLE `kehadiran_hrt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kehadiran_hrt_id_siswa_foreign` (`id_siswa`),
  ADD KEY `kehadiran_hrt_id_kelas_foreign` (`id_kelas`),
  ADD KEY `kehadiran_hrt_id_guru_foreign` (`id_guru`),
  ADD KEY `kehadiran_hrt_id_tahun_ajar_foreign` (`id_tahun_ajar`);

--
-- Indexes for table `kehadiran_semester`
--
ALTER TABLE `kehadiran_semester`
  ADD PRIMARY KEY (`id_kehadiran_semester`),
  ADD UNIQUE KEY `unique_semester` (`id_siswa`,`id_tahun_ajar`),
  ADD KEY `fk_kehadiran_semester_kelas` (`id_kelas`),
  ADD KEY `fk_kehadiran_semester_tahun` (`id_tahun_ajar`),
  ADD KEY `fk_kehadiran_semester_guru` (`id_guru_hrt`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD KEY `id_jenjang` (`id_jenjang`);

--
-- Indexes for table `komponen_penilaian`
--
ALTER TABLE `komponen_penilaian`
  ADD PRIMARY KEY (`id_komponen`),
  ADD UNIQUE KEY `unique_tugas_periode` (`id_tugas`,`periode`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `log_generate_jadwal`
--
ALTER TABLE `log_generate_jadwal`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `major`
--
ALTER TABLE `major`
  ADD PRIMARY KEY (`id_major`);

--
-- Indexes for table `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id_mapel`);

--
-- Indexes for table `mapel_major`
--
ALTER TABLE `mapel_major`
  ADD PRIMARY KEY (`id_mapel`,`id_major`),
  ADD KEY `id_major` (`id_major`);

--
-- Indexes for table `mengajar`
--
ALTER TABLE `mengajar`
  ADD PRIMARY KEY (`id_mengajar`),
  ADD KEY `fk_mengajar_guru` (`id_guru`),
  ADD KEY `fk_mengajar_mapel` (`id_mapel`),
  ADD KEY `fk_mengajar_kelas` (`id_kelas`),
  ADD KEY `fk_mengajar_major` (`id_major`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilai_bulanan`
--
ALTER TABLE `nilai_bulanan`
  ADD PRIMARY KEY (`id_nilai_bulanan`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_tugas` (`id_tugas`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `nilai_komponen_detail`
--
ALTER TABLE `nilai_komponen_detail`
  ADD PRIMARY KEY (`id_nilai_detail`),
  ADD UNIQUE KEY `unique_siswa_tugas_periode_komponen` (`id_siswa`,`id_tugas`,`periode`,`nama_komponen`),
  ADD KEY `idx_siswa` (`id_siswa`),
  ADD KEY `idx_tugas` (`id_tugas`),
  ADD KEY `idx_periode` (`periode`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `nilai_tka`
--
ALTER TABLE `nilai_tka`
  ADD PRIMARY KEY (`id_nilai_tka`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `paket_mapel`
--
ALTER TABLE `paket_mapel`
  ADD PRIMARY KEY (`id_paket_mapel`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id_report`);

--
-- Indexes for table `sakit_siswa`
--
ALTER TABLE `sakit_siswa`
  ADD PRIMARY KEY (`id_sakit`),
  ADD KEY `sakit_siswa_id_siswa_index` (`id_siswa`),
  ADD KEY `sakit_siswa_created_by_index` (`created_by`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_major` (`id_major`);

--
-- Indexes for table `tahun_ajar`
--
ALTER TABLE `tahun_ajar`
  ADD PRIMARY KEY (`id_tahun_ajar`);

--
-- Indexes for table `tugas_mengajar`
--
ALTER TABLE `tugas_mengajar`
  ADD PRIMARY KEY (`id_tugas`),
  ADD KEY `fk_tugas_guru` (`id_guru`),
  ADD KEY `fk_tugas_mapel` (`id_mapel`),
  ADD KEY `fk_tugas_kelas` (`id_kelas`),
  ADD KEY `fk_tugas_major` (`id_major`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catatan_bulanan`
--
ALTER TABLE `catatan_bulanan`
  MODIFY `id_catatan_bulanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_jadwal`
--
ALTER TABLE `config_jadwal`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id_guru` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hode`
--
ALTER TABLE `hode`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenjang`
--
ALTER TABLE `jenjang`
  MODIFY `id_jenjang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadiran_bulanan`
--
ALTER TABLE `kehadiran_bulanan`
  MODIFY `id_kehadiran_bulanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadiran_harian`
--
ALTER TABLE `kehadiran_harian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadiran_hrt`
--
ALTER TABLE `kehadiran_hrt`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadiran_semester`
--
ALTER TABLE `kehadiran_semester`
  MODIFY `id_kehadiran_semester` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `komponen_penilaian`
--
ALTER TABLE `komponen_penilaian`
  MODIFY `id_komponen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_generate_jadwal`
--
ALTER TABLE `log_generate_jadwal`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `major`
--
ALTER TABLE `major`
  MODIFY `id_major` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id_mapel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mengajar`
--
ALTER TABLE `mengajar`
  MODIFY `id_mengajar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_bulanan`
--
ALTER TABLE `nilai_bulanan`
  MODIFY `id_nilai_bulanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_komponen_detail`
--
ALTER TABLE `nilai_komponen_detail`
  MODIFY `id_nilai_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_tka`
--
ALTER TABLE `nilai_tka`
  MODIFY `id_nilai_tka` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket_mapel`
--
ALTER TABLE `paket_mapel`
  MODIFY `id_paket_mapel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id_report` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sakit_siswa`
--
ALTER TABLE `sakit_siswa`
  MODIFY `id_sakit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tahun_ajar`
--
ALTER TABLE `tahun_ajar`
  MODIFY `id_tahun_ajar` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tugas_mengajar`
--
ALTER TABLE `tugas_mengajar`
  MODIFY `id_tugas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `catatan_bulanan`
--
ALTER TABLE `catatan_bulanan`
  ADD CONSTRAINT `fk_catatan_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_catatan_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_catatan_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_catatan_tahun` FOREIGN KEY (`id_tahun_ajar`) REFERENCES `tahun_ajar` (`id_tahun_ajar`) ON DELETE CASCADE;

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `guru_mapel`
--
ALTER TABLE `guru_mapel`
  ADD CONSTRAINT `guru_mapel_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `guru_mapel_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE;

--
-- Constraints for table `kehadiran_bulanan`
--
ALTER TABLE `kehadiran_bulanan`
  ADD CONSTRAINT `fk_kehadiran_bulanan_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_bulanan_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_bulanan_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_bulanan_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_bulanan_tahun` FOREIGN KEY (`id_tahun_ajar`) REFERENCES `tahun_ajar` (`id_tahun_ajar`) ON DELETE CASCADE;

--
-- Constraints for table `kehadiran_hrt`
--
ALTER TABLE `kehadiran_hrt`
  ADD CONSTRAINT `kehadiran_hrt_id_guru_foreign` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `kehadiran_hrt_id_kelas_foreign` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `kehadiran_hrt_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `kehadiran_hrt_id_tahun_ajar_foreign` FOREIGN KEY (`id_tahun_ajar`) REFERENCES `tahun_ajar` (`id_tahun_ajar`) ON DELETE CASCADE;

--
-- Constraints for table `kehadiran_semester`
--
ALTER TABLE `kehadiran_semester`
  ADD CONSTRAINT `fk_kehadiran_semester_guru` FOREIGN KEY (`id_guru_hrt`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_semester_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_semester_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kehadiran_semester_tahun` FOREIGN KEY (`id_tahun_ajar`) REFERENCES `tahun_ajar` (`id_tahun_ajar`) ON DELETE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_jenjang`) REFERENCES `jenjang` (`id_jenjang`) ON DELETE CASCADE;

--
-- Constraints for table `komponen_penilaian`
--
ALTER TABLE `komponen_penilaian`
  ADD CONSTRAINT `komponen_penilaian_ibfk_1` FOREIGN KEY (`id_tugas`) REFERENCES `tugas_mengajar` (`id_tugas`) ON DELETE CASCADE,
  ADD CONSTRAINT `komponen_penilaian_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `mapel_major`
--
ALTER TABLE `mapel_major`
  ADD CONSTRAINT `mapel_major_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mapel_major_ibfk_2` FOREIGN KEY (`id_major`) REFERENCES `major` (`id_major`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mengajar`
--
ALTER TABLE `mengajar`
  ADD CONSTRAINT `fk_mengajar_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mengajar_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mengajar_major` FOREIGN KEY (`id_major`) REFERENCES `major` (`id_major`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mengajar_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE;

--
-- Constraints for table `nilai_bulanan`
--
ALTER TABLE `nilai_bulanan`
  ADD CONSTRAINT `nilai_bulanan_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_bulanan_ibfk_2` FOREIGN KEY (`id_tugas`) REFERENCES `tugas_mengajar` (`id_tugas`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_bulanan_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `nilai_komponen_detail`
--
ALTER TABLE `nilai_komponen_detail`
  ADD CONSTRAINT `fk_nilai_detail_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nilai_detail_tugas` FOREIGN KEY (`id_tugas`) REFERENCES `tugas_mengajar` (`id_tugas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nilai_detail_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `nilai_tka`
--
ALTER TABLE `nilai_tka`
  ADD CONSTRAINT `nilai_tka_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE;

--
-- Constraints for table `paket_mapel`
--
ALTER TABLE `paket_mapel`
  ADD CONSTRAINT `paket_mapel_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE;

--
-- Constraints for table `sakit_siswa`
--
ALTER TABLE `sakit_siswa`
  ADD CONSTRAINT `sakit_siswa_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_ibfk_3` FOREIGN KEY (`id_major`) REFERENCES `major` (`id_major`) ON DELETE SET NULL;

--
-- Constraints for table `tugas_mengajar`
--
ALTER TABLE `tugas_mengajar`
  ADD CONSTRAINT `fk_tugas_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_major` FOREIGN KEY (`id_major`) REFERENCES `major` (`id_major`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mapel` (`id_mapel`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
