-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Waktu pembuatan: 14 Agu 2026 pada 23.31
-- Versi server: 11.4.12-MariaDB-ubu2404
-- Versi PHP: 8.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `sim_keanggotaan_imm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nia` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id`, `user_id`, `nia`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telp`, `foto_profil`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 2, '24944679', 'Cahyo Situmorang', 'Probolinggo', '1991-10-28', 'Dk. Ujung No. 487, Pekalongan 69651, Papua', '0679 4415 341', NULL, 1, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(2, 3, '24604812', 'Eli Rina Nurdiyanti', 'Kendari', '1990-03-22', 'Gg. Babakan No. 541, Administrasi Jakarta Pusat 94594, Bali', '0652 4890 005', NULL, 1, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(3, 4, '24882581', 'Tari Safitri', 'Banda Aceh', '1985-01-10', 'Jr. Rumah Sakit No. 205, Yogyakarta 13074, Sulbar', '0229 4627 4512', NULL, 1, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(4, 5, '24177713', 'Silvia Rahmawati', 'Lhokseumawe', '1985-04-05', 'Kpg. Suharso No. 327, Kotamobagu 82519, Jabar', '0817 7241 148', NULL, 1, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(5, 6, '24946769', 'Aris Daliono Prayoga S.Psi', 'Kediri', '1970-09-20', 'Kpg. Padang No. 687, Batam 26249, NTB', '0486 8347 0042', NULL, 1, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(6, 12, NULL, 'Ade Rifqy Aulian', 'Bungo', '2005-05-16', 'Bungo', '085273085692', 'foto_profil/8LsDbfa1ytVOmrHGDdEvX6bAoFShmJcG9JeyGQ4Q.jpg', 1, '2026-07-22 23:24:02', '2026-08-12 08:13:25'),
(7, 13, NULL, 'instruktur@example.com', 'Bungo', '2005-05-16', 'Bungo', '085273085692', NULL, 1, '2026-07-22 23:26:57', '2026-07-22 23:26:57'),
(8, 14, '24260001', 'M. Miftahul Khoiri. S', 'Merangin', '2005-05-16', 'Kerang Berahi', '085288886666', NULL, 1, '2026-07-23 20:49:25', '2026-07-23 20:49:25'),
(9, 15, NULL, 'Dika Aprillas', 'Bungo', '2005-05-16', 'Dusun Manggis', '082377778888', NULL, 1, '2026-07-23 22:16:01', '2026-07-23 22:16:01'),
(10, 17, NULL, 'Amin Saputra', 'Bungo', '2005-05-16', 'Candika', '085398982222', NULL, 1, '2026-07-23 22:19:16', '2026-07-23 22:19:16'),
(11, 19, '24260002', 'Ade Rifqy Aulian', 'Bungo', '2005-05-16', 'Talang Pantai', '085273085692', NULL, 1, '2026-07-23 22:30:14', '2026-07-23 22:33:08'),
(12, 20, NULL, 'ahmad febi', 'Bungo', '2004-05-16', 'Bungo', '085266667878', 'foto_profil/hVZsrLRAlRw1DDFE62vDxdxmXIwiCFXGXinBiTrk.jpg', 1, '2026-08-08 14:27:27', '2026-08-08 14:29:18'),
(13, 21, NULL, 'instruktur Baru', 'Bungo', '2005-05-16', 'Bungo', '085232323260', NULL, 1, '2026-08-08 14:53:06', '2026-08-08 14:53:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip`
--

CREATE TABLE `arsip` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_dokumen` varchar(255) DEFAULT NULL,
  `judul_dokumen` varchar(255) NOT NULL,
  `kategori_arsip` varchar(255) NOT NULL,
  `file_arsip` varchar(255) NOT NULL,
  `tanggal_unggah` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `arsip`
--

INSERT INTO `arsip` (`id`, `anggota_id`, `nomor_dokumen`, `judul_dokumen`, `kategori_arsip`, `file_arsip`, `tanggal_unggah`, `created_at`, `updated_at`) VALUES
(1, 1, 'DOC-5695', 'Proposal Aksi Sosial Peduli Sesama', 'surat_keputusan', 'arsip/dummy.pdf', '2026-07-22', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(2, 2, 'DOC-7631', 'Surat Masuk dari Pimpinan Cabang', 'proposal', 'arsip/dummy.pdf', '2026-07-22', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(3, 3, 'DOC-9048', 'Surat Keterangan Aktif Organisasi', 'surat_keputusan', 'arsip/dummy.pdf', '2026-07-22', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(4, 4, 'DOC-2081', 'Proposal Pelatihan Kepemimpinan Kader', 'lainnya', 'arsip/dummy.pdf', '2026-07-22', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(5, 5, 'DOC-2338', 'Surat Masuk dari Pimpinan Cabang', 'surat_keputusan', 'arsip/dummy.pdf', '2026-07-22', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(6, 11, NULL, 'Proposal Kegiatan', 'proposal', 'arsip/NtBV77I9PkoIes7vGDrwynO3rHdpPw7ODhJJDs1N.pdf', '2026-07-23', '2026-07-23 22:39:02', '2026-07-23 22:39:02'),
(7, 11, NULL, 'Proposal Kegiatan', 'proposal', 'arsip/1Yds6SUKnNoawYDBexKRy1FnMoR6aSbSbn6ekPv4.pdf', '2026-07-23', '2026-07-23 22:39:02', '2026-07-23 22:39:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-amin@example.com|172.18.0.1', 'i:1;', 1784819990),
('laravel-cache-amin@example.com|172.18.0.1:timer', 'i:1784819990;', 1784819990),
('laravel-cache-kegiatan.terbaru', 'a:3:{i:0;a:8:{s:2:\"id\";i:4;s:13:\"nama_kegiatan\";s:10:\"Bedah Buku\";s:9:\"deskripsi\";s:146:\"Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.\";s:13:\"tanggal_waktu\";s:27:\"2026-08-16T13:00:00.000000Z\";s:6:\"lokasi\";s:8:\"Kampus 1\";s:9:\"thumbnail\";N;s:10:\"created_at\";s:27:\"2026-07-23T15:35:46.000000Z\";s:10:\"updated_at\";s:27:\"2026-07-23T15:35:46.000000Z\";}i:1;a:8:{s:2:\"id\";i:5;s:13:\"nama_kegiatan\";s:10:\"Bedah Buku\";s:9:\"deskripsi\";s:146:\"Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.\";s:13:\"tanggal_waktu\";s:27:\"2026-08-16T13:00:00.000000Z\";s:6:\"lokasi\";s:8:\"Kampus 1\";s:9:\"thumbnail\";N;s:10:\"created_at\";s:27:\"2026-07-23T15:35:46.000000Z\";s:10:\"updated_at\";s:27:\"2026-07-23T15:35:46.000000Z\";}i:2;a:8:{s:2:\"id\";i:1;s:13:\"nama_kegiatan\";s:20:\"Mentoring Kader Baru\";s:9:\"deskripsi\";s:76:\"Forum kajian rutin yang membahas isu keislaman, kemahasiswaan, dan keimm-an.\";s:13:\"tanggal_waktu\";s:27:\"2026-07-07T22:56:53.000000Z\";s:6:\"lokasi\";s:20:\"Masjid Kampus, Medan\";s:9:\"thumbnail\";N;s:10:\"created_at\";s:27:\"2026-07-22T15:32:44.000000Z\";s:10:\"updated_at\";s:27:\"2026-07-22T15:32:44.000000Z\";}}', 1785829840),
('sim-imm-cache-instruktur2@gmail.com|172.20.0.1', 'i:1;', 1786175666),
('sim-imm-cache-instruktur2@gmail.com|172.20.0.1:timer', 'i:1786175666;', 1786175666),
('sim-imm-cache-kegiatan.terbaru', 'a:3:{i:0;a:8:{s:2:\"id\";i:6;s:13:\"nama_kegiatan\";s:23:\"Perjalanan seorang nabi\";s:9:\"deskripsi\";N;s:13:\"tanggal_waktu\";s:27:\"2005-05-16T06:30:00.000000Z\";s:6:\"lokasi\";s:13:\"Masjid Ummuba\";s:9:\"thumbnail\";s:64:\"kegiatan_thumbnails/LG14HK4o3lOIHWlN1R128s3OvMK6ce5QmXj02YIH.jpg\";s:10:\"created_at\";s:27:\"2026-08-13T06:30:22.000000Z\";s:10:\"updated_at\";s:27:\"2026-08-13T06:30:22.000000Z\";}i:1;a:8:{s:2:\"id\";i:4;s:13:\"nama_kegiatan\";s:10:\"Bedah Buku\";s:9:\"deskripsi\";s:146:\"Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.\";s:13:\"tanggal_waktu\";s:27:\"2026-08-16T13:00:00.000000Z\";s:6:\"lokasi\";s:8:\"Kampus 1\";s:9:\"thumbnail\";N;s:10:\"created_at\";s:27:\"2026-07-23T15:35:46.000000Z\";s:10:\"updated_at\";s:27:\"2026-07-23T15:35:46.000000Z\";}i:2;a:8:{s:2:\"id\";i:5;s:13:\"nama_kegiatan\";s:10:\"Bedah Buku\";s:9:\"deskripsi\";s:146:\"Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.\";s:13:\"tanggal_waktu\";s:27:\"2026-08-16T13:00:00.000000Z\";s:6:\"lokasi\";s:8:\"Kampus 1\";s:9:\"thumbnail\";N;s:10:\"created_at\";s:27:\"2026-07-23T15:35:46.000000Z\";s:10:\"updated_at\";s:27:\"2026-07-23T15:35:46.000000Z\";}}', 1786615958);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_waktu` datetime NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `nama_kegiatan`, `deskripsi`, `tanggal_waktu`, `lokasi`, `thumbnail`, `created_at`, `updated_at`) VALUES
(1, 'Mentoring Kader Baru', 'Forum kajian rutin yang membahas isu keislaman, kemahasiswaan, dan keimm-an.', '2026-07-08 05:56:53', 'Masjid Kampus, Medan', NULL, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(2, 'Workshop Administrasi Organisasi', 'Forum kajian rutin yang membahas isu keislaman, kemahasiswaan, dan keimm-an.', '2026-07-13 00:48:15', 'Aula Mahasiswa, Payakumbuh', NULL, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(3, 'Musyawarah Komisariat', 'Seminar terbuka mengenai pengembangan organisasi dan peran kader di kampus.', '2026-07-13 06:36:05', 'Aula Mahasiswa, Cirebon', NULL, '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(4, 'Bedah Buku', 'Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.', '2026-08-16 20:00:00', 'Kampus 1', NULL, '2026-07-23 22:35:46', '2026-07-23 22:35:46'),
(5, 'Bedah Buku', 'Buku dengan judul \"My SQL\" merupakan buku yang menjadi panduan bagi para pengembang sistem yang ingin mendalami database dengan konsep relational.', '2026-08-16 20:00:00', 'Kampus 1', NULL, '2026-07-23 22:35:46', '2026-07-23 22:35:46'),
(6, 'Perjalanan seorang nabi', NULL, '2005-05-16 13:30:00', 'Masjid Ummuba', 'kegiatan_thumbnails/LG14HK4o3lOIHWlN1R128s3OvMK6ce5QmXj02YIH.jpg', '2026-08-13 13:30:22', '2026-08-13 13:30:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_kegiatan`
--

CREATE TABLE `laporan_kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `tujuan` text NOT NULL,
  `ringkasan` text NOT NULL,
  `agenda` text NOT NULL,
  `narasumber` text DEFAULT NULL,
  `hasil` text NOT NULL,
  `kendala` text DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `file_lampiran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi_kegiatan`
--

CREATE TABLE `materi_kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `file_materi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `materi_kegiatan`
--

INSERT INTO `materi_kegiatan` (`id`, `kegiatan_id`, `judul`, `deskripsi`, `file_materi`, `created_at`, `updated_at`) VALUES
(1, 6, 'Musa', 'Seorang Nabi', 'materi_kegiatan/XDVU1PiQ2hElMqhmlL1NHQI8VCEVjG6ceDfEgW4W.pdf', '2026-08-13 13:30:53', '2026-08-13 13:30:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi_tersimpan`
--

CREATE TABLE `materi_tersimpan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `materi_kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `materi_tersimpan`
--

INSERT INTO `materi_tersimpan` (`id`, `anggota_id`, `materi_kegiatan_id`, `created_at`, `updated_at`) VALUES
(1, 6, 1, '2026-08-13 13:31:48', '2026-08-13 13:31:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_10_235500_create_anggota_table', 1),
(5, '2026_05_10_235600_create_pendaftaran_table', 1),
(6, '2026_05_10_235700_create_kegiatan_table', 1),
(7, '2026_05_10_235800_create_presensi_table', 1),
(8, '2026_05_10_235900_create_sertifikat_table', 1),
(9, '2026_05_11_000000_create_arsip_table', 1),
(10, '2026_06_04_205438_update_role_column_in_users_table', 1),
(11, '2026_06_04_205444_add_thumbnail_to_kegiatan_table', 1),
(12, '2026_06_04_231334_add_klaim_columns_to_presensi_table', 1),
(13, '2026_07_06_223556_migrate_arsip_files_to_private_disk_and_backfill_categories', 1),
(14, '2026_07_08_123501_add_role_to_pendaftaran_table', 1),
(15, '2026_07_14_000000_add_password_to_pendaftaran_table', 1),
(18, '2026_08_11_000000_add_jenis_dokumen_identitas_to_pendaftaran_table', 2),
(19, '2026_08_11_000001_migrate_pendaftaran_files_to_private_disk', 2),
(20, '2026_08_12_144520_add_kegiatan_anggota_unique_index_to_sertifikat_table', 3),
(21, '2026_08_13_121021_create_materi_kegiatan_table', 4),
(22, '2026_08_13_121022_create_materi_tersimpan_table', 4),
(23, '2026_08_13_143022_create_laporan_kegiatan_table', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('kader','instruktur') NOT NULL DEFAULT 'kader',
  `tempat_lahir` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `file_persyaratan` varchar(255) DEFAULT NULL,
  `jenis_dokumen_identitas` enum('ktp','ktm') DEFAULT NULL,
  `status_validasi` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `user_id`, `nama_lengkap`, `email`, `password`, `role`, `tempat_lahir`, `tanggal_lahir`, `no_telp`, `alamat`, `tanggal_daftar`, `file_persyaratan`, `jenis_dokumen_identitas`, `status_validasi`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Cakrabirawa Wacana', 'cdongoran@example.com', '$2y$12$oluO2Asua/6zzC.F3nlQbelz2ej3SHY74G4rtP/3boiKcnmDy2b5e', 'kader', 'Balikpapan', '1987-06-19', '(+62) 262 6819 948', 'Ki. Cikutra Timur No. 528, Palopo 60718, Kalteng', '2026-07-22', NULL, NULL, 'pending', NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(2, NULL, 'Umay Karna Waluyo', 'melinda36@example.net', '$2y$12$dCkBYvu6YfoSzjgFhr55Ye4ipUV2LFRa0j5o2WgEOnGT4u1I1a37C', 'kader', 'Bandung', '1971-10-27', '(+62) 206 4329 799', 'Ki. Siliwangi No. 804, Cirebon 88330, Sumbar', '2026-07-22', NULL, NULL, 'pending', NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(3, NULL, 'Cakrawala Kamidin Mansur', 'oktaviani.umaya@example.com', '$2y$12$xYWFwau8vdrLsW3mZZVbk.o9jArNfVMPn6ydnGs6YuUC3WjEepFHe', 'kader', 'Denpasar', '1987-01-27', '(+62) 531 8795 3242', 'Ds. Suryo Pranoto No. 202, Yogyakarta 54489, Kalteng', '2026-07-22', NULL, NULL, 'pending', NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(4, 9, 'Tari Purnawati', 'endra82@example.com', '$2y$12$40uLJc05PSjuR70LpN2fbeH4.EuGRi0ldHgunLYXX/CKYNShCXlly', 'kader', 'Surakarta', '1998-04-09', '(+62) 929 9802 8771', 'Jr. Sugiyopranoto No. 840, Denpasar 69511, Sultra', '2026-07-22', NULL, NULL, 'disetujui', 'Pendaftaran disetujui setelah dokumen lengkap dan data valid.', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(5, NULL, 'Yuni Gabriella Hastuti', 'hari.puspita@example.net', '$2y$12$rMAGvamYY53o//RgfW5YKeITEedNA9YPxzkzWmbYKShcJ6Aes3IRa', 'instruktur', 'Langsa', '1970-08-26', '0969 7118 295', 'Ki. Juanda No. 392, Payakumbuh 90852, Kaltim', '2026-07-22', NULL, NULL, 'ditolak', 'Pendaftaran ditolak karena data tidak lengkap atau tidak memenuhi syarat.', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(6, NULL, 'Arwzb6kKLU&94', 'richard.anderson@outlook.com', '$2y$12$WIKrDqPlqrFA.AXXzke0C.ye7l.QridPr0dI7ILBrXO4fJVOw6zJW', 'kader', 'Richard Anderson', '2028-04-17', 'Arwzb6kKLU&94', 'Arwzb6kKLU&94', '2026-07-22', NULL, NULL, 'pending', NULL, '2026-07-22 23:22:45', '2026-07-22 23:22:45'),
(7, 12, 'pma4d3ChN4%43', 'linda.sanchez@testmail.com', '$2y$12$YlDx/qN0Vqx9dXdzz4Kspehh899PBGDFez2F4pG8gkTxfuHVzd1Hm', 'kader', 'Linda Sanchez', '2024-09-21', 'pma4d3ChN4%43', 'pma4d3ChN4%43', '2026-07-22', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-07-22 23:23:46', '2026-07-22 23:24:02'),
(8, 13, 'instruktur@example.com', 'instruktur@example.com', '$2y$12$Ex.h9p6OMtZEgQjvDEI.welx0nsE3qE.yoZYIVx2mK7bUSx6xY3Hu', 'instruktur', 'Bungo', '2005-05-16', '085273085692', 'Bungo', '2026-07-22', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-07-22 23:26:43', '2026-07-22 23:26:57'),
(9, 15, 'Dika Aprillas', 'dika@example.com', '$2y$12$npWm8CDajR3sxic428OuQeyQvD8X9CrKNvkQulLloxpN/B50a3bmG', 'kader', 'Bungo', '2005-05-16', '082377778888', 'Dusun Manggis', '2026-07-23', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-07-23 22:15:41', '2026-07-23 22:16:01'),
(10, NULL, 'Dika Aprillas', 'dika@example.com', NULL, 'kader', 'Bungo', '2005-05-16', '082377778888', 'Dusun Manggis', '2026-07-23', NULL, NULL, 'ditolak', 'sudah ada anggota dika', '2026-07-23 22:15:41', '2026-07-23 22:16:45'),
(11, 17, 'Amin Saputra', 'amin@example.com', '$2y$12$FmOQMFQVYG/d6An/SJRD1ehZeSSsaWEO/pdRSwgWbOv5PW2UjysIS', 'kader', 'Bungo', '2005-05-16', '085398982222', 'Candika', '2026-07-23', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-07-23 22:18:36', '2026-07-23 22:19:16'),
(12, NULL, 'Amin Saputra', 'amin@example.com', NULL, 'kader', 'Bungo', '2005-05-16', '085398982222', 'Candika', '2026-07-23', NULL, NULL, 'ditolak', 'sudah ada', '2026-07-23 22:18:36', '2026-07-23 22:28:25'),
(13, 19, 'Ade Rifqy Aulian', 'adeclouds@example.com', '$2y$12$XCollwAEPwiKSIIc7IrP0eWDENlguq66HLC7JFs2In/DNx21Vw.jG', 'kader', 'Bungo', '2005-05-16', '085273085692', 'Talang Pantai', '2026-07-23', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-07-23 22:29:55', '2026-07-23 22:30:14'),
(14, NULL, 'Ade Rifqy Aulian', 'adeclouds@example.com', '$2y$12$2pWyAaa5eGeOIWnGpiVMsOmBhdX4B4dJArWf5y4wkM3ou.8.GKela', 'kader', 'Bungo', '2005-05-16', '085273085692', 'Talang Pantai', '2026-07-23', NULL, NULL, 'pending', NULL, '2026-07-23 22:29:55', '2026-07-23 22:29:55'),
(15, NULL, 'uuDfMUeY6u@26', 'kevin_robinson@hotmail.com', '$2y$12$1WSjVTQEdGP1xxAlNEEm0OwSsAaDdfgZb/dWa9NFpcwi4zMR1jHT2', 'kader', 'Kevin Robinson', '2028-11-04', 'uuDfMUeY6u@26', 'uuDfMUeY6u@26', '2026-07-25', NULL, NULL, 'pending', NULL, '2026-07-25 13:45:19', '2026-07-25 13:45:19'),
(16, NULL, 'BLkU43p6Nu$93', 'kenneth.martinez@outlook.com', '$2y$12$yPYG1Ve7SkpVPKq1MSOLb.CyWV70H98kDcQDFEgWvdxRWKZWMzFoW', 'kader', 'Kenneth Martinez', '2026-04-14', 'BLkU43p6Nu$93', 'BLkU43p6Nu$93', '2026-07-25', NULL, NULL, 'pending', NULL, '2026-07-25 14:17:07', '2026-07-25 14:17:07'),
(17, NULL, 'mCQ6LGV2jr!26', 'emilygonzalez573@yahoo.com', '$2y$12$j3SgPSGP9LEPCnhz0mfHKe6xQo/vrygdObUVHeJE4NWaShAq2RpSm', 'kader', 'Emily Gonzalez', '2028-12-15', 'mCQ6LGV2jr!26', 'mCQ6LGV2jr!26', '2026-07-25', NULL, NULL, 'pending', NULL, '2026-07-25 14:19:19', '2026-07-25 14:19:19'),
(18, 20, 'ahmad febi', 'ahmd@example.com', '$2y$12$I2/ST0KE.cft2tB0lqD8Pezh55r9cxqTlwO6im5r1rOtNa6NCNoAu', 'kader', 'Bungo', '2004-05-16', '085266667878', 'Bungo', '2026-08-08', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-08-08 14:25:27', '2026-08-08 14:27:27'),
(19, 21, 'instruktur Baru', 'intruktur2@example.com', '$2y$12$J/2NQiht8VZisDjAxQvv..uqqQi21avY28ZguczFldoX7elp/b3o2', 'instruktur', 'Bungo', '2005-05-16', '085232323260', 'Bungo', '2026-08-08', NULL, NULL, 'disetujui', 'Pendaftaran disetujui.', '2026-08-08 14:52:43', '2026-08-08 14:53:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_hadir` datetime DEFAULT NULL,
  `status_kehadiran` enum('hadir','izin','alfa') NOT NULL DEFAULT 'alfa',
  `bukti_kehadiran` varchar(255) DEFAULT NULL,
  `status_klaim` enum('pending','disetujui','ditolak') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`id`, `kegiatan_id`, `anggota_id`, `waktu_hadir`, `status_kehadiran`, `bukti_kehadiran`, `status_klaim`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-23 20:53:14'),
(2, 1, 2, NULL, 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-23 20:53:14'),
(3, 1, 3, NULL, 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-23 20:53:14'),
(4, 1, 4, NULL, 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-23 20:53:14'),
(5, 1, 5, NULL, 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-23 20:53:14'),
(6, 2, 1, '2026-07-22 22:32:45', 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(7, 2, 2, '2026-07-22 22:32:45', 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(8, 2, 3, '2026-07-22 22:32:45', 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(9, 2, 4, '2026-07-22 22:32:45', 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(10, 2, 5, '2026-07-22 22:32:45', 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(11, 3, 1, '2026-07-22 22:32:45', 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(12, 3, 2, '2026-07-22 22:32:45', 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(13, 3, 3, '2026-07-22 22:32:45', 'hadir', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(14, 3, 4, '2026-07-22 22:32:45', 'izin', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(15, 3, 5, '2026-07-22 22:32:45', 'alfa', NULL, NULL, '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(16, 1, 6, '2026-08-08 14:43:39', 'hadir', 'bukti_kehadiran/yIkCMOuRhucAzaG5dznN4XHdNnfarVvEJ8YVsS9T.jpg', 'pending', '2026-07-23 20:53:14', '2026-08-08 14:43:39'),
(17, 1, 7, '2026-08-08 14:43:39', 'hadir', NULL, NULL, '2026-07-23 20:53:14', '2026-08-08 14:43:39'),
(18, 1, 8, '2026-08-08 14:43:39', 'hadir', NULL, NULL, '2026-07-23 20:53:14', '2026-08-08 14:43:39'),
(19, 4, 6, '2026-08-08 14:42:11', 'hadir', 'bukti_kehadiran/LrPlR3EqQrkstOp1QSQKrXAbKzKrI4KZv8IzS6dS.jpg', 'disetujui', '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(21, 4, 11, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(22, 4, 10, NULL, 'izin', NULL, NULL, '2026-07-23 22:36:26', '2026-07-23 22:36:26'),
(24, 4, 5, NULL, 'izin', NULL, NULL, '2026-07-23 22:36:26', '2026-07-23 22:36:26'),
(25, 4, 1, NULL, 'alfa', NULL, NULL, '2026-07-23 22:36:26', '2026-07-23 22:36:26'),
(26, 4, 9, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(27, 4, 2, NULL, 'izin', NULL, NULL, '2026-07-23 22:36:26', '2026-07-23 22:36:26'),
(28, 4, 7, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(29, 4, 8, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(30, 4, 4, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(31, 4, 3, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-07-23 22:36:26', '2026-08-08 14:42:11'),
(32, 4, 12, '2026-08-08 14:42:11', 'hadir', NULL, NULL, '2026-08-08 14:42:11', '2026-08-08 14:42:11'),
(33, 1, 11, '2026-08-08 14:43:39', 'hadir', NULL, NULL, '2026-08-08 14:43:39', '2026-08-08 14:43:39'),
(34, 1, 12, '2026-08-08 14:43:39', 'hadir', 'bukti_kehadiran/y8Z1LUJdNxAX9KjyxVmXhJ6sLDk9A0WCL1JZc0Rt.jpg', 'pending', '2026-08-08 14:43:39', '2026-08-08 14:45:02'),
(35, 1, 10, '2026-08-08 14:43:39', 'hadir', NULL, NULL, '2026-08-08 14:43:39', '2026-08-08 14:43:39'),
(36, 1, 9, '2026-08-08 14:43:39', 'hadir', NULL, NULL, '2026-08-08 14:43:39', '2026-08-08 14:43:39'),
(37, 6, 6, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(38, 6, 11, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(39, 6, 12, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(40, 6, 10, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(41, 6, 5, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(42, 6, 1, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(43, 6, 9, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(44, 6, 2, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(45, 6, 13, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(46, 6, 7, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(47, 6, 8, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(48, 6, 4, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11'),
(49, 6, 3, '2026-08-13 13:31:11', 'hadir', NULL, NULL, '2026-08-13 13:31:11', '2026-08-13 13:31:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sertifikat`
--

CREATE TABLE `sertifikat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `anggota_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_sertifikat` varchar(255) NOT NULL,
  `file_sertifikat` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sertifikat`
--

INSERT INTO `sertifikat` (`id`, `kegiatan_id`, `anggota_id`, `nomor_sertifikat`, `file_sertifikat`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'CERT-5497', 'sertifikat/dummy.pdf', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(2, 1, 2, 'CERT-5429', 'sertifikat/dummy.pdf', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(3, 2, 3, 'CERT-5215', 'sertifikat/dummy.pdf', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(4, 1, 4, 'CERT-0460', 'sertifikat/dummy.pdf', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(5, 1, 5, 'CERT-7564', 'sertifikat/dummy.pdf', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(6, 4, 11, 'CERT-4-11-20260723', 'sertifikat/CERT-4-11-20260723.pdf', '2026-07-23 22:40:50', '2026-07-23 22:40:50'),
(7, 4, 9, 'CERT-4-9-20260723', 'sertifikat/CERT-4-9-20260723.pdf', '2026-07-23 22:40:50', '2026-07-23 22:40:50'),
(8, 4, 10, 'CERT-4-10-20260723', 'sertifikat/CERT-4-10-20260723.pdf', '2026-07-23 22:40:50', '2026-07-23 22:40:50'),
(9, 4, 6, 'CERT-4-6-20260724', 'sertifikat/CERT-4-6-20260724.pdf', '2026-07-24 06:23:08', '2026-07-24 06:23:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vE8GiKkpRdUg1CnZLUzWCXRhKswTEoipL0LOUJzY', 10, '172.20.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5cWtvbU42R2ZPVUphWHFzd2FSV3ZLRWRubkhoWXFqbUIxeVVHN254IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FkbWluXC9rZWdpYXRhbiIsInJvdXRlIjoiYWRtaW4ua2VnaWF0YW4uaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEwfQ==', 1786612781);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kader','instruktur') NOT NULL DEFAULT 'kader',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Cengkal Napitupulu S.Sos', 'sitorus.yuni@example.org', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'admin', 'powqXig5oD', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(2, 'Cahyo Situmorang', 'adinata00@example.com', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'qWsfPoeaKK', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(3, 'Eli Rina Nurdiyanti', 'mahmud.mandasari@example.net', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'o7ArQvau75', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(4, 'Tari Safitri', 'xsaragih@example.net', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'bDRqInU3Pq', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(5, 'Silvia Rahmawati', 'caraka21@example.net', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'NPqVzucKFF', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(6, 'Aris Daliono Prayoga S.Psi', 'dpalastri@example.com', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'uurOFwzvjM', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(7, 'Ida Lestari', 'cecep30@example.org', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'instruktur', 'G9fbkXPjwP', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(8, 'Keisha Suryatmi', 'sirait.nova@example.org', '2026-07-22 22:32:44', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'instruktur', 'fNHzTfroRB', '2026-07-22 22:32:44', '2026-07-22 22:32:44'),
(9, 'Ina Farida', 'rpuspita@example.com', '2026-07-22 22:32:45', '$2y$12$KMpa7NwkpiAagVN3F3YT0urNSqDCwUBh27pL7yP1GLUAbJ9F8/Z3y', 'kader', 'MIyuVCYS49', '2026-07-22 22:32:45', '2026-07-22 22:32:45'),
(10, 'Administrator SIM IMM', 'admin@admin.com', NULL, '$2y$12$yVUDaq4hydNgNwp62DaKDOyAXwS5.Zq5LQA7J.evQskWMyEk1P9/2', 'admin', NULL, '2026-07-22 22:57:28', '2026-07-22 22:57:28'),
(11, 'Ahmad Fauzi', 'kader@example.com', NULL, '$2y$12$EvwaBCKdC06wNQoNJvVoZOQbcPTaB.o0a1GY0QzEFKtPbEXCzjn7S', 'kader', NULL, '2026-07-22 22:57:28', '2026-07-22 22:57:28'),
(12, 'Adeqq', 'aderifqyreg.ti2@gmail.com', NULL, '$2y$12$YlDx/qN0Vqx9dXdzz4Kspehh899PBGDFez2F4pG8gkTxfuHVzd1Hm', 'kader', NULL, '2026-07-22 23:24:02', '2026-07-22 23:25:43'),
(13, 'instruktur@example.com', 'instruktur@example.com', NULL, '$2y$12$Ex.h9p6OMtZEgQjvDEI.welx0nsE3qE.yoZYIVx2mK7bUSx6xY3Hu', 'instruktur', NULL, '2026-07-22 23:26:57', '2026-07-22 23:26:57'),
(14, 'M. Miftahul Khoiri. S', 'khoiri@example.com', NULL, '$2y$12$QTqQW4wbfCL5StZ3y5Jv5e9w3xpKgj9WIEI87O0ufJ0ZHOQTRn0zi', 'kader', NULL, '2026-07-23 20:49:25', '2026-07-23 20:49:25'),
(15, 'Dika Aprillas', 'dika@example.com', NULL, '$2y$12$npWm8CDajR3sxic428OuQeyQvD8X9CrKNvkQulLloxpN/B50a3bmG', 'kader', NULL, '2026-07-23 22:16:01', '2026-07-23 22:16:01'),
(17, 'Amin Saputra', 'amin@example.com', NULL, '$2y$12$FmOQMFQVYG/d6An/SJRD1ehZeSSsaWEO/pdRSwgWbOv5PW2UjysIS', 'kader', NULL, '2026-07-23 22:19:16', '2026-07-23 22:19:16'),
(19, 'Ade Rifqy Aulian', 'adeclouds@example.com', NULL, '$2y$12$XCollwAEPwiKSIIc7IrP0eWDENlguq66HLC7JFs2In/DNx21Vw.jG', 'kader', NULL, '2026-07-23 22:30:14', '2026-07-23 22:30:14'),
(20, 'ahmad febi', 'ahmd@example.com', NULL, '$2y$12$I2/ST0KE.cft2tB0lqD8Pezh55r9cxqTlwO6im5r1rOtNa6NCNoAu', 'kader', NULL, '2026-08-08 14:27:27', '2026-08-08 14:27:27'),
(21, 'instruktur Baru', 'intruktur2@example.com', NULL, '$2y$12$J/2NQiht8VZisDjAxQvv..uqqQi21avY28ZguczFldoX7elp/b3o2', 'instruktur', NULL, '2026-08-08 14:53:06', '2026-08-08 14:53:06');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `anggota_nia_unique` (`nia`),
  ADD KEY `anggota_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `arsip`
--
ALTER TABLE `arsip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arsip_anggota_id_foreign` (`anggota_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_kegiatan`
--
ALTER TABLE `laporan_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laporan_kegiatan_kegiatan_id_unique` (`kegiatan_id`);

--
-- Indeks untuk tabel `materi_kegiatan`
--
ALTER TABLE `materi_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materi_kegiatan_kegiatan_id_foreign` (`kegiatan_id`);

--
-- Indeks untuk tabel `materi_tersimpan`
--
ALTER TABLE `materi_tersimpan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materi_tersimpan_anggota_id_materi_kegiatan_id_unique` (`anggota_id`,`materi_kegiatan_id`),
  ADD KEY `materi_tersimpan_materi_kegiatan_id_foreign` (`materi_kegiatan_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pendaftaran_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `presensi_kegiatan_id_anggota_id_unique` (`kegiatan_id`,`anggota_id`),
  ADD KEY `presensi_anggota_id_foreign` (`anggota_id`);

--
-- Indeks untuk tabel `sertifikat`
--
ALTER TABLE `sertifikat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sertifikat_nomor_sertifikat_unique` (`nomor_sertifikat`),
  ADD UNIQUE KEY `sertifikat_kegiatan_anggota_unique` (`kegiatan_id`,`anggota_id`),
  ADD KEY `sertifikat_anggota_id_foreign` (`anggota_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `arsip`
--
ALTER TABLE `arsip`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `laporan_kegiatan`
--
ALTER TABLE `laporan_kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `materi_kegiatan`
--
ALTER TABLE `materi_kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `materi_tersimpan`
--
ALTER TABLE `materi_tersimpan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `sertifikat`
--
ALTER TABLE `sertifikat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `arsip`
--
ALTER TABLE `arsip`
  ADD CONSTRAINT `arsip_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan_kegiatan`
--
ALTER TABLE `laporan_kegiatan`
  ADD CONSTRAINT `laporan_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `materi_kegiatan`
--
ALTER TABLE `materi_kegiatan`
  ADD CONSTRAINT `materi_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `materi_tersimpan`
--
ALTER TABLE `materi_tersimpan`
  ADD CONSTRAINT `materi_tersimpan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `materi_tersimpan_materi_kegiatan_id_foreign` FOREIGN KEY (`materi_kegiatan_id`) REFERENCES `materi_kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presensi_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sertifikat`
--
ALTER TABLE `sertifikat`
  ADD CONSTRAINT `sertifikat_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sertifikat_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
