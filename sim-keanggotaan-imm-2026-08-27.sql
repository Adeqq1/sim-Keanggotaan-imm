/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: sim_keanggotaan_imm
-- ------------------------------------------------------
-- Server version	11.4.13-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `anggota`
--

DROP TABLE IF EXISTS `anggota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `anggota` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nia` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `komisariat_id` varchar(100) DEFAULT NULL,
  `tahun_daftar` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anggota_nia_unique` (`nia`),
  KEY `anggota_user_id_foreign` (`user_id`),
  CONSTRAINT `anggota_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anggota`
--

LOCK TABLES `anggota` WRITE;
/*!40000 ALTER TABLE `anggota` DISABLE KEYS */;
INSERT INTO `anggota` VALUES
(1,4,'24260001','Aisyah Rahmawati','Yogyakarta','2003-02-14','Jl. Kader Muhammadiyah No. 5, Daerah Istimewa Yogyakarta','081234560001','foto_profil/demo/aisyah.png',1,'2025-12-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2026),
(2,5,'24260002','Nabila Putri Ramadhani','Bantul','2004-06-21','Jl. Kader Muhammadiyah No. 6, Daerah Istimewa Yogyakarta','081234560002','foto_profil/demo/nabila.png',1,'2026-01-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2026),
(3,6,'24260003','Fikri Maulana','Sleman','2003-09-08','Jl. Kader Muhammadiyah No. 7, Daerah Istimewa Yogyakarta','081234560003','foto_profil/demo/fikri.png',1,'2026-02-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2026),
(4,7,'24260004','Rafi Pratama','Kulon Progo','2004-01-17','Jl. Kader Muhammadiyah No. 8, Daerah Istimewa Yogyakarta','081234560004',NULL,1,'2026-03-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2026),
(5,8,'24260005','Siti Hanifah','Yogyakarta','2003-12-11','Jl. Kader Muhammadiyah No. 9, Daerah Istimewa Yogyakarta','081234560005',NULL,1,'2026-04-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2026),
(6,9,'24250001','Zahra Amalia','Magelang','2002-07-04','Jl. Kader Muhammadiyah No. 10, Daerah Istimewa Yogyakarta','081234560006',NULL,1,'2025-10-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2025),
(7,10,'24250002','Dimas Saputra','Klaten','2002-03-26','Jl. Kader Muhammadiyah No. 11, Daerah Istimewa Yogyakarta','081234560007',NULL,1,'2025-11-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2025),
(8,11,'24250003','Farhan Akbar','Purworejo','2001-11-19','Jl. Kader Muhammadiyah No. 12, Daerah Istimewa Yogyakarta','081234560008',NULL,1,'2025-12-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2025),
(9,12,'24240001','Salma Nurfadilah','Yogyakarta','2001-05-30','Jl. Kader Muhammadiyah No. 13, Daerah Istimewa Yogyakarta','081234560009',NULL,1,'2025-08-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2024),
(10,13,'24240002','Bagas Ramadhan','Sleman','2002-10-02','Jl. Kader Muhammadiyah No. 14, Daerah Istimewa Yogyakarta','081234560010',NULL,1,'2025-09-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2024),
(11,14,NULL,'Laila Fitriani','Bantul','2004-08-15','Jl. Kader Muhammadiyah No. 15, Daerah Istimewa Yogyakarta','081234560011',NULL,1,'2026-06-27 11:49:30','2026-08-27 11:49:30','ahmad-dahlan',2026),
(12,15,'24230001','Rizky Kurniawan','Wonosari','2001-04-09','Jl. Kader Muhammadiyah No. 16, Daerah Istimewa Yogyakarta','081234560012',NULL,0,'2025-06-27 11:49:30','2026-08-27 11:49:30','buya-hamka',2023);
/*!40000 ALTER TABLE `anggota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arsip`
--

DROP TABLE IF EXISTS `arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `arsip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `nomor_dokumen` varchar(255) DEFAULT NULL,
  `judul_dokumen` varchar(255) NOT NULL,
  `kategori_arsip` varchar(255) NOT NULL,
  `file_arsip` varchar(255) NOT NULL,
  `tanggal_unggah` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `arsip_anggota_id_foreign` (`anggota_id`),
  CONSTRAINT `arsip_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arsip`
--

LOCK TABLES `arsip` WRITE;
/*!40000 ALTER TABLE `arsip` DISABLE KEYS */;
INSERT INTO `arsip` VALUES
(1,1,'IMM/PROP/2026/001','Proposal Darul Arqam Dasar 2026','proposal','arsip/demo/arsip-0.pdf','2026-08-27','2026-08-27 11:45:46','2026-08-27 11:49:30'),
(2,1,'IMM/LPJ/2026/001','LPJ Kajian Rutin Keislaman','lpj','arsip/demo/arsip-1.pdf','2026-08-24','2026-08-27 11:45:46','2026-08-27 11:49:30'),
(3,1,'IMM/SK/2026/001','Surat Keputusan Panitia DAD','surat_keputusan','arsip/demo/arsip-2.pdf','2026-08-21','2026-08-27 11:45:46','2026-08-27 11:49:30'),
(4,2,'IMM/SM/2026/002','Undangan Seminar Kepemimpinan','surat_masuk','arsip/demo/arsip-3.pdf','2026-08-18','2026-08-27 11:45:46','2026-08-27 11:49:30'),
(5,3,'IMM/SKEL/2026/003','Surat Tugas Bakti Sosial','surat_keluar','arsip/demo/arsip-4.pdf','2026-08-15','2026-08-27 11:45:46','2026-08-27 11:49:30'),
(6,6,'IMM/LAIN/2025/004','Notulen Musyawarah Komisariat','lainnya','arsip/demo/arsip-5.pdf','2026-08-12','2026-08-27 11:45:46','2026-08-27 11:49:30');
/*!40000 ALTER TABLE `arsip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kegiatan`
--

DROP TABLE IF EXISTS `kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kegiatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_waktu` datetime NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jenis_pelaksanaan` varchar(32) NOT NULL DEFAULT 'belum_ditetapkan',
  `minimum_sesi_terverifikasi` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kegiatan`
--

LOCK TABLES `kegiatan` WRITE;
/*!40000 ALTER TABLE `kegiatan` DISABLE KEYS */;
INSERT INTO `kegiatan` VALUES
(1,'Kajian Rutin Keislaman','Kegiatan Kajian Rutin Keislaman untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-06-13 09:00:00','Masjid Kampus Utama','kegiatan_thumbnails/demo/kajian.png','2026-05-24 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(2,'Darul Arqam Dasar 2026','Kegiatan Darul Arqam Dasar 2026 untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-07-13 09:00:00','Balai Diklat Muhammadiyah','kegiatan_thumbnails/demo/dad.png','2026-06-23 09:00:00','2026-08-27 11:49:30','multi_sesi',3),
(3,'Workshop Administrasi Organisasi','Kegiatan Workshop Administrasi Organisasi untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-08-07 09:00:00','Aula Ahmad Dahlan','kegiatan_thumbnails/demo/workshop.png','2026-07-18 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(4,'Bakti Sosial Ramadan','Kegiatan Bakti Sosial Ramadan untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-08-17 09:00:00','Desa Binaan IMM','kegiatan_thumbnails/demo/baksos.png','2026-07-28 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(5,'Seminar Kepemimpinan Kader','Kegiatan Seminar Kepemimpinan Kader untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-09-03 09:00:00','Auditorium Universitas','kegiatan_thumbnails/demo/seminar.png','2026-08-14 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(6,'Musyawarah Komisariat','Kegiatan Musyawarah Komisariat untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-09-10 09:00:00','Gedung Dakwah Muhammadiyah','kegiatan_thumbnails/demo/musykom.png','2026-08-21 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(7,'Sekolah Literasi Digital','Kegiatan Sekolah Literasi Digital untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-09-24 09:00:00','Laboratorium Komputer','kegiatan_thumbnails/demo/literasi.png','2026-09-04 09:00:00','2026-08-27 11:49:30','multi_sesi',3),
(8,'Diskusi Publik Gerakan Mahasiswa','Kegiatan Diskusi Publik Gerakan Mahasiswa untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.','2026-02-28 09:00:00','Pendopo Kampus','kegiatan_thumbnails/demo/diskusi.png','2026-02-08 09:00:00','2026-08-27 11:49:30','satu_sesi',1),
(9,'Darul Aqram Dasar','Darul Aqrom Dasar bertujuan membentuk kader yang berakhlak','2026-08-28 08:00:00','UMMUBA','0','2026-08-27 14:24:44','2026-08-27 14:24:44','satu_sesi',1),
(10,'Bedah Buku','Bedah Buku adalah kegiatan yang mengajak kader agar dapat berpikir kritis terhadap ide ide sang penulis buku','2026-08-28 20:00:00','UMMUBA','0','2026-08-27 14:33:01','2026-08-27 14:33:01','satu_sesi',1),
(11,'Aksi Damai','Aksi damai terhadap kebijakan pemerintah yang tidak relevan dengan permasalah masyarakat bungo tahun 2027','2026-08-31 16:30:00','Ummuba','0','2026-08-27 14:34:38','2026-08-27 14:34:38','multi_sesi',3),
(12,'Kegiatan Bakti Sosial','Menyebarkan kebajikan sosial kepada masyarakat sekitar kampus ummuba','2026-09-01 17:00:00','UMMUBA','0','2026-08-27 14:36:33','2026-08-27 14:36:33','satu_sesi',1),
(13,'Pengajian bersama IMM','Pengajian antar kader imm dan kader muhammadiyah','2026-09-03 18:40:00','Masjid Al-Furqan','0','2026-08-27 14:38:58','2026-08-27 14:38:58','satu_sesi',1);
/*!40000 ALTER TABLE `kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kegiatan_tahun_angkatan`
--

DROP TABLE IF EXISTS `kegiatan_tahun_angkatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kegiatan_tahun_angkatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `tahun_daftar` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kegiatan_tahun_angkatan_kegiatan_id_tahun_daftar_unique` (`kegiatan_id`,`tahun_daftar`),
  KEY `kegiatan_tahun_angkatan_tahun_daftar_index` (`tahun_daftar`),
  CONSTRAINT `kegiatan_tahun_angkatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kegiatan_tahun_angkatan`
--

LOCK TABLES `kegiatan_tahun_angkatan` WRITE;
/*!40000 ALTER TABLE `kegiatan_tahun_angkatan` DISABLE KEYS */;
INSERT INTO `kegiatan_tahun_angkatan` VALUES
(1,1,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,1,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,2,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(4,3,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(5,3,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(6,4,2024,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(7,4,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(8,4,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(9,5,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(10,5,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(11,6,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(12,6,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(13,7,2026,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(14,8,2024,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(15,8,2025,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(16,9,2023,'2026-08-27 14:24:44','2026-08-27 14:24:44'),
(17,9,2024,'2026-08-27 14:24:44','2026-08-27 14:24:44'),
(18,9,2025,'2026-08-27 14:24:44','2026-08-27 14:24:44'),
(19,9,2026,'2026-08-27 14:24:44','2026-08-27 14:24:44'),
(20,10,2021,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(21,10,2022,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(22,10,2023,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(23,10,2024,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(24,10,2025,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(25,10,2026,'2026-08-27 14:33:01','2026-08-27 14:33:01'),
(26,11,2016,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(27,11,2017,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(28,11,2018,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(29,11,2019,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(30,11,2020,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(31,11,2021,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(32,11,2022,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(33,11,2023,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(34,11,2024,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(35,11,2025,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(36,11,2026,'2026-08-27 14:34:38','2026-08-27 14:34:38'),
(37,12,2024,'2026-08-27 14:36:33','2026-08-27 14:36:33'),
(38,12,2025,'2026-08-27 14:36:33','2026-08-27 14:36:33'),
(39,12,2026,'2026-08-27 14:36:33','2026-08-27 14:36:33'),
(40,13,2025,'2026-08-27 14:38:58','2026-08-27 14:38:58'),
(41,13,2026,'2026-08-27 14:38:58','2026-08-27 14:38:58');
/*!40000 ALTER TABLE `kegiatan_tahun_angkatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_kegiatan`
--

DROP TABLE IF EXISTS `laporan_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_kegiatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `tujuan` text NOT NULL,
  `ringkasan` text NOT NULL,
  `agenda` text NOT NULL,
  `narasumber` text DEFAULT NULL,
  `hasil` text NOT NULL,
  `kendala` text DEFAULT NULL,
  `tindak_lanjut` text DEFAULT NULL,
  `file_lampiran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laporan_kegiatan_kegiatan_id_unique` (`kegiatan_id`),
  CONSTRAINT `laporan_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_kegiatan`
--

LOCK TABLES `laporan_kegiatan` WRITE;
/*!40000 ALTER TABLE `laporan_kegiatan` DISABLE KEYS */;
INSERT INTO `laporan_kegiatan` VALUES
(1,2,'Memperkuat pemahaman ideologi IMM dan kemampuan kepemimpinan kader.','Darul Arqam Dasar 2026 berjalan tertib, partisipatif, dan sesuai agenda.','Pembukaan, penyampaian materi, diskusi kelompok, evaluasi, dan penutup.','Fajar Hidayat dan Rahma Nuraini','Peserta memahami materi dan menyusun rencana tindak lanjut di komisariat.','Penyesuaian waktu pada sesi diskusi karena antusiasme peserta.','Mentoring dua pekan dan evaluasi pelaksanaan program komisariat.','laporan_kegiatan/demo/dad.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,3,'Memperkuat pemahaman ideologi IMM dan kemampuan kepemimpinan kader.','Workshop Administrasi Organisasi berjalan tertib, partisipatif, dan sesuai agenda.','Pembukaan, penyampaian materi, diskusi kelompok, evaluasi, dan penutup.','Fajar Hidayat dan Rahma Nuraini','Peserta memahami materi dan menyusun rencana tindak lanjut di komisariat.','Penyesuaian waktu pada sesi diskusi karena antusiasme peserta.','Mentoring dua pekan dan evaluasi pelaksanaan program komisariat.','laporan_kegiatan/demo/workshop.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,8,'Memperkuat pemahaman ideologi IMM dan kemampuan kepemimpinan kader.','Diskusi Publik Gerakan Mahasiswa berjalan tertib, partisipatif, dan sesuai agenda.','Pembukaan, penyampaian materi, diskusi kelompok, evaluasi, dan penutup.','Fajar Hidayat dan Rahma Nuraini','Peserta memahami materi dan menyusun rencana tindak lanjut di komisariat.','Penyesuaian waktu pada sesi diskusi karena antusiasme peserta.','Mentoring dua pekan dan evaluasi pelaksanaan program komisariat.','laporan_kegiatan/demo/diskusi.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `laporan_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materi_kegiatan`
--

DROP TABLE IF EXISTS `materi_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `materi_kegiatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `file_materi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materi_kegiatan_kegiatan_id_foreign` (`kegiatan_id`),
  CONSTRAINT `materi_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materi_kegiatan`
--

LOCK TABLES `materi_kegiatan` WRITE;
/*!40000 ALTER TABLE `materi_kegiatan` DISABLE KEYS */;
INSERT INTO `materi_kegiatan` VALUES
(1,1,'Modul Kajian Keislaman','Pokok bahasan kajian dan referensi diskusi.','materi_kegiatan/demo/kajian-modul.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,2,'Modul Darul Arqam Dasar','Modul utama perkaderan dasar IMM.','materi_kegiatan/demo/dad-modul.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,2,'Panduan Rencana Tindak Lanjut','Panduan menyusun tindak lanjut peserta.','materi_kegiatan/demo/dad-rtl.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(4,3,'Template Administrasi Komisariat','Contoh surat dan administrasi organisasi.','materi_kegiatan/demo/administrasi.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(5,8,'Ringkasan Diskusi Gerakan','Ringkasan materi dan rekomendasi diskusi.','materi_kegiatan/demo/diskusi.pdf','2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `materi_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materi_tersimpan`
--

DROP TABLE IF EXISTS `materi_tersimpan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `materi_tersimpan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `materi_kegiatan_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materi_tersimpan_anggota_id_materi_kegiatan_id_unique` (`anggota_id`,`materi_kegiatan_id`),
  KEY `materi_tersimpan_materi_kegiatan_id_foreign` (`materi_kegiatan_id`),
  CONSTRAINT `materi_tersimpan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `materi_tersimpan_materi_kegiatan_id_foreign` FOREIGN KEY (`materi_kegiatan_id`) REFERENCES `materi_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materi_tersimpan`
--

LOCK TABLES `materi_tersimpan` WRITE;
/*!40000 ALTER TABLE `materi_tersimpan` DISABLE KEYS */;
INSERT INTO `materi_tersimpan` VALUES
(1,1,2,'2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `materi_tersimpan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_05_10_235500_create_anggota_table',1),
(5,'2026_05_10_235600_create_pendaftaran_table',1),
(6,'2026_05_10_235700_create_kegiatan_table',1),
(7,'2026_05_10_235800_create_presensi_table',1),
(8,'2026_05_10_235900_create_sertifikat_table',1),
(9,'2026_05_11_000000_create_arsip_table',1),
(10,'2026_06_04_205438_update_role_column_in_users_table',1),
(11,'2026_06_04_205444_add_thumbnail_to_kegiatan_table',1),
(12,'2026_06_04_231334_add_klaim_columns_to_presensi_table',1),
(13,'2026_07_06_223556_migrate_arsip_files_to_private_disk_and_backfill_categories',1),
(14,'2026_07_08_123501_add_role_to_pendaftaran_table',1),
(15,'2026_07_14_000000_add_password_to_pendaftaran_table',1),
(16,'2026_08_11_000000_add_jenis_dokumen_identitas_to_pendaftaran_table',1),
(17,'2026_08_11_000001_migrate_pendaftaran_files_to_private_disk',1),
(18,'2026_08_12_144520_add_kegiatan_anggota_unique_index_to_sertifikat_table',1),
(19,'2026_08_13_121021_create_materi_kegiatan_table',1),
(20,'2026_08_13_121022_create_materi_tersimpan_table',1),
(21,'2026_08_13_143022_create_laporan_kegiatan_table',1),
(22,'2026_08_17_020609_add_komisariat_and_tahun_daftar_to_pendaftaran_and_anggota_tables',1),
(23,'2026_08_24_000001_add_attendance_policy_to_kegiatan_table',1),
(24,'2026_08_24_000002_create_sesi_kegiatan_table',1),
(25,'2026_08_24_000003_add_session_verification_to_presensi_table',1),
(26,'2026_08_24_000004_backfill_sesi_kegiatan_for_existing_presensi',1),
(27,'2026_08_24_000005_replace_presensi_activity_member_unique_index',1),
(28,'2026_08_24_000006_create_penilaian_kegiatan_table',1),
(29,'2026_08_24_000007_backfill_legacy_attendance_policy',1),
(30,'2026_08_25_000001_add_certificate_snapshots_to_sertifikat_table',1),
(31,'2026_08_25_000001_create_kegiatan_tahun_angkatan_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pendaftaran`
--

DROP TABLE IF EXISTS `pendaftaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pendaftaran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  `komisariat_id` varchar(100) DEFAULT NULL,
  `tahun_daftar` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pendaftaran_user_id_foreign` (`user_id`),
  CONSTRAINT `pendaftaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pendaftaran`
--

LOCK TABLES `pendaftaran` WRITE;
/*!40000 ALTER TABLE `pendaftaran` DISABLE KEYS */;
INSERT INTO `pendaftaran` VALUES
(1,NULL,'Calon Kader Ahmad Dahlan','calon.ahmad@example.com','password','kader','Yogyakarta','2003-01-12','08210000001','Daerah Istimewa Yogyakarta','2026-08-22','pendaftaran/demo/pendaftaran-0.pdf','ktm','pending',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30','ahmad-dahlan',2026),
(2,NULL,'Calon Kader Buya Hamka','calon.buya@example.com','password','kader','Yogyakarta','2003-02-12','08210000002','Daerah Istimewa Yogyakarta','2026-08-23','pendaftaran/demo/pendaftaran-1.png','ktp','pending',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30','buya-hamka',2026),
(3,NULL,'Calon Instruktur IMM','calon.instruktur@example.com','password','instruktur','Yogyakarta','2003-03-12','08210000003','Daerah Istimewa Yogyakarta','2026-08-24','pendaftaran/demo/pendaftaran-2.pdf','ktp','pending',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30',NULL,2026),
(4,5,'Nabila Putri Ramadhani','nabila@example.com',NULL,'kader','Yogyakarta','2003-04-12','08210000004','Daerah Istimewa Yogyakarta','2026-08-25','pendaftaran/demo/pendaftaran-3.pdf','ktm','disetujui','Data dan dokumen telah diverifikasi.','2026-08-27 11:45:46','2026-08-27 11:49:30','ahmad-dahlan',2026),
(5,NULL,'Pendaftar Dokumen Buram','ditolak@example.com',NULL,'kader','Yogyakarta','2003-05-12','08210000005','Daerah Istimewa Yogyakarta','2026-08-26','pendaftaran/demo/pendaftaran-4.pdf','ktm','ditolak','Dokumen KTM tidak terbaca. Silakan daftar ulang.','2026-08-27 11:45:46','2026-08-27 11:49:30','buya-hamka',2026);
/*!40000 ALTER TABLE `pendaftaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penilaian_kegiatan`
--

DROP TABLE IF EXISTS `penilaian_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penilaian_kegiatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `nilai` enum('A','B','C','D') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penilaian_kegiatan_kegiatan_anggota_unique` (`kegiatan_id`,`anggota_id`),
  KEY `penilaian_kegiatan_anggota_id_foreign` (`anggota_id`),
  CONSTRAINT `penilaian_kegiatan_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penilaian_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penilaian_kegiatan`
--

LOCK TABLES `penilaian_kegiatan` WRITE;
/*!40000 ALTER TABLE `penilaian_kegiatan` DISABLE KEYS */;
INSERT INTO `penilaian_kegiatan` VALUES
(1,2,1,'A','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,2,2,'B','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,2,5,'C','2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `penilaian_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presensi`
--

DROP TABLE IF EXISTS `presensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `presensi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `sesi_kegiatan_id` bigint(20) unsigned NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `waktu_hadir` datetime DEFAULT NULL,
  `status_kehadiran` enum('hadir','izin','alfa') NOT NULL DEFAULT 'alfa',
  `status_verifikasi` varchar(32) NOT NULL DEFAULT 'pending',
  `pemeriksa_id` bigint(20) unsigned DEFAULT NULL,
  `diperiksa_pada` datetime DEFAULT NULL,
  `bukti_kehadiran` varchar(255) DEFAULT NULL,
  `status_klaim` enum('pending','disetujui','ditolak') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `presensi_sesi_anggota_unique` (`sesi_kegiatan_id`,`anggota_id`),
  KEY `presensi_anggota_id_foreign` (`anggota_id`),
  KEY `presensi_pemeriksa_id_foreign` (`pemeriksa_id`),
  KEY `presensi_kegiatan_id_index` (`kegiatan_id`),
  KEY `presensi_verified_count_index` (`kegiatan_id`,`anggota_id`,`status_kehadiran`,`status_verifikasi`,`sesi_kegiatan_id`),
  CONSTRAINT `presensi_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensi_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `presensi_pemeriksa_id_foreign` FOREIGN KEY (`pemeriksa_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `presensi_sesi_kegiatan_id_foreign` FOREIGN KEY (`sesi_kegiatan_id`) REFERENCES `sesi_kegiatan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presensi`
--

LOCK TABLES `presensi` WRITE;
/*!40000 ALTER TABLE `presensi` DISABLE KEYS */;
INSERT INTO `presensi` VALUES
(1,1,1,1,'2026-06-13 09:05:00','hadir','terverifikasi',2,'2026-06-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,1,1,2,'2026-06-13 09:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,1,1,3,'2026-06-13 09:05:00','hadir','ditolak',2,'2026-06-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(4,1,1,4,NULL,'izin','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(5,1,1,5,NULL,'alfa','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(6,1,1,6,'2026-06-13 09:05:00','hadir','terverifikasi',2,'2026-06-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(7,1,1,7,'2026-06-13 09:05:00','hadir','terverifikasi',2,'2026-06-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(8,2,2,1,'2026-07-13 09:05:00','hadir','terverifikasi',2,'2026-07-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(9,2,3,1,'2026-07-13 12:05:00','hadir','terverifikasi',2,'2026-07-13 18:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(10,2,4,1,'2026-07-13 15:05:00','hadir','terverifikasi',2,'2026-07-13 21:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(11,2,5,1,'2026-07-13 18:05:00','hadir','terverifikasi',2,'2026-07-14 00:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(12,2,2,2,'2026-07-13 09:05:00','hadir','terverifikasi',2,'2026-07-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(13,2,3,2,'2026-07-13 12:05:00','hadir','terverifikasi',2,'2026-07-13 18:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(14,2,4,2,'2026-07-13 15:05:00','hadir','terverifikasi',2,'2026-07-13 21:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(15,2,5,2,'2026-07-13 18:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(16,2,2,3,'2026-07-13 09:05:00','hadir','terverifikasi',2,'2026-07-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(17,2,3,3,'2026-07-13 12:05:00','hadir','terverifikasi',2,'2026-07-13 18:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(18,2,4,3,'2026-07-13 15:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(19,2,5,3,'2026-07-13 18:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(20,2,2,4,'2026-07-13 09:05:00','hadir','terverifikasi',2,'2026-07-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(21,2,3,4,'2026-07-13 12:05:00','hadir','terverifikasi',2,'2026-07-13 18:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(22,2,4,4,'2026-07-13 15:05:00','hadir','ditolak',2,'2026-07-13 21:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(23,2,5,4,'2026-07-13 18:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(24,2,2,5,'2026-07-13 09:05:00','hadir','terverifikasi',2,'2026-07-13 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(25,2,3,5,'2026-07-13 12:05:00','hadir','terverifikasi',2,'2026-07-13 18:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(26,2,4,5,'2026-07-13 15:05:00','hadir','terverifikasi',2,'2026-07-13 21:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(27,2,5,5,'2026-07-13 18:05:00','hadir','pending',NULL,NULL,NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(28,3,6,1,'2026-08-07 09:05:00','hadir','terverifikasi',2,'2026-08-07 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(29,3,6,2,'2026-08-07 09:05:00','hadir','terverifikasi',2,'2026-08-07 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(30,3,6,6,'2026-08-07 09:05:00','hadir','terverifikasi',2,'2026-08-07 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(31,3,6,7,'2026-08-07 09:05:00','hadir','terverifikasi',2,'2026-08-07 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(32,3,6,8,'2026-08-07 09:05:00','hadir','terverifikasi',2,'2026-08-07 15:00:00',NULL,NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `presensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sertifikat`
--

DROP TABLE IF EXISTS `sertifikat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sertifikat` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `nomor_sertifikat` varchar(255) NOT NULL,
  `file_sertifikat` varchar(255) NOT NULL,
  `tipe_sertifikat` varchar(32) DEFAULT NULL,
  `nilai_snapshot` enum('A','B','C','D') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sertifikat_nomor_sertifikat_unique` (`nomor_sertifikat`),
  UNIQUE KEY `sertifikat_kegiatan_anggota_unique` (`kegiatan_id`,`anggota_id`),
  KEY `sertifikat_anggota_id_foreign` (`anggota_id`),
  CONSTRAINT `sertifikat_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sertifikat_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sertifikat`
--

LOCK TABLES `sertifikat` WRITE;
/*!40000 ALTER TABLE `sertifikat` DISABLE KEYS */;
INSERT INTO `sertifikat` VALUES
(1,1,1,'IMM/KJ/2026/001','sertifikat/demo/kajian-aisyah.pdf','satu_sesi',NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,2,1,'IMM/DAD/2026/001','sertifikat/demo/dad-aisyah.pdf','multi_sesi','A','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,2,2,'IMM/DAD/2026/002','sertifikat/demo/dad-nabila.pdf','multi_sesi','B','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(4,3,1,'IMM/ADM/2026/001','sertifikat/demo/workshop-aisyah.pdf','satu_sesi',NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(5,3,6,'IMM/ADM/2026/002','sertifikat/demo/workshop-zahra.pdf','satu_sesi',NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46'),
(6,3,7,'IMM/ADM/2026/003','sertifikat/demo/workshop-dimas.pdf','satu_sesi',NULL,'2026-08-27 11:45:46','2026-08-27 11:45:46');
/*!40000 ALTER TABLE `sertifikat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesi_kegiatan`
--

DROP TABLE IF EXISTS `sesi_kegiatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sesi_kegiatan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kegiatan_id` bigint(20) unsigned NOT NULL,
  `urutan` smallint(5) unsigned NOT NULL,
  `nama_sesi` varchar(255) NOT NULL,
  `mulai_pada` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sesi_kegiatan_kegiatan_id_urutan_unique` (`kegiatan_id`,`urutan`),
  UNIQUE KEY `sesi_kegiatan_kegiatan_id_nama_sesi_mulai_pada_unique` (`kegiatan_id`,`nama_sesi`,`mulai_pada`),
  KEY `sesi_kegiatan_kegiatan_id_mulai_pada_index` (`kegiatan_id`,`mulai_pada`),
  CONSTRAINT `sesi_kegiatan_kegiatan_id_foreign` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesi_kegiatan`
--

LOCK TABLES `sesi_kegiatan` WRITE;
/*!40000 ALTER TABLE `sesi_kegiatan` DISABLE KEYS */;
INSERT INTO `sesi_kegiatan` VALUES
(1,1,1,'Kajian Rutin Keislaman','2026-06-13 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(2,2,1,'Keislaman','2026-07-13 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(3,2,2,'Ke-IMM-an','2026-07-13 12:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(4,2,3,'Kepemimpinan','2026-07-13 15:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(5,2,4,'Evaluasi dan Rencana Tindak Lanjut','2026-07-13 18:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(6,3,1,'Workshop Administrasi Organisasi','2026-08-07 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(7,4,1,'Bakti Sosial Ramadan','2026-08-17 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(8,5,1,'Seminar Kepemimpinan Kader','2026-09-03 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(9,6,1,'Musyawarah Komisariat','2026-09-10 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(10,7,1,'Keislaman','2026-09-24 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(11,7,2,'Ke-IMM-an','2026-09-24 12:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(12,7,3,'Kepemimpinan','2026-09-24 15:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(13,7,4,'Evaluasi dan Rencana Tindak Lanjut','2026-09-24 18:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(14,8,1,'Diskusi Publik Gerakan Mahasiswa','2026-02-28 09:00:00','2026-08-27 11:45:46','2026-08-27 11:45:46'),
(15,9,1,'Sesi 1','2026-08-28 08:00:00','2026-08-27 14:24:44','2026-08-27 14:24:44'),
(16,10,1,'Sesi 1','2026-08-28 20:00:00','2026-08-27 14:33:01','2026-08-27 14:33:01'),
(17,11,1,'Sesi 1','2026-08-31 16:30:00','2026-08-27 14:34:38','2026-08-27 14:34:38'),
(18,12,1,'Sesi 1','2026-09-01 17:00:00','2026-08-27 14:36:33','2026-08-27 14:36:33'),
(19,13,1,'Sesi 1','2026-09-03 18:40:00','2026-08-27 14:38:58','2026-08-27 14:38:58');
/*!40000 ALTER TABLE `sesi_kegiatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('B3r7Gxc1kioUjEloxIO3nniE9L1n3CnSib3j2QU2',1,'172.71.124.201','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJKd25WYWJhbHZhVUs0eUl2Q2Joa3BuODFaRWhZUFB1aXpuenNHYVRlIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2ltbS1idW5nby53ZWIuaWRcL2FkbWluXC9rZWdpYXRhbj9zb3J0PW5hbWEiLCJyb3V0ZSI6ImFkbWluLmtlZ2lhdGFuLmluZGV4In0sInVybCI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==',1787816398),
('C9rNdEIJkbKTl4AtMCgNXJ02dVNTQuAZZ589juMT',NULL,'37.120.201.58','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIwZElOSXg1TTdQWkpmNG1jUHpmbEhOTmVtU1dDTEdKbG1HNE9odDU2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC8xNi43OC4xMTEuMjA0Iiwicm91dGUiOiJsYW5kaW5nIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1787814589),
('lB3Yzxc9O0nIOEL8dSGM4O9Er7ibbazY2UaEu9iA',NULL,'162.158.108.162','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4240.193 Safari/537.36','eyJfdG9rZW4iOiJ6SGpIOTJJd3RRMTF4YTVMbXBTdXpLWGRXNUJOc3lORHMyY3MwRGl1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9pbW0tYnVuZ28ud2ViLmlkIiwicm91dGUiOiJsYW5kaW5nIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1787811855),
('nrcbBvdDuei8Wmp2M7SuXI3D23tRWsz3AayKPuw6',NULL,'104.22.24.114','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36','eyJfdG9rZW4iOiIybnZaTE9STVRJS1JMNXo5eEdQR0dYcnprUUpGOTJOcVlCYzVLN1FqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9pbW0tYnVuZ28ud2ViLmlkIiwicm91dGUiOiJsYW5kaW5nIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1787816182),
('qsD4gD0WrgQueoUbsQHodOXEyRPULsGn9dsFlHLX',NULL,'45.156.129.191','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.6312.86 Safari/537.36','eyJfdG9rZW4iOiJ5bUYxdlRjR2JHR1BXcHE0TGQ2dUtTemhxdTZYZlUyOVpJbXJYQ245IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC8xNi43OC4xMTEuMjA0Iiwicm91dGUiOiJsYW5kaW5nIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1787811039),
('TuiFdL19eC37Iy2HYZ7YKDgeOfuhGkLt17d2Fmqa',NULL,'162.159.106.191','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.5938.132 Safari/537.36','eyJfdG9rZW4iOiJQWUFQaXBka29pMjJ5d0s4Q2JhekNtOEl0a3BweXBoenhwSnZoRG5IIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9pbW0tYnVuZ28ud2ViLmlkIiwicm91dGUiOiJsYW5kaW5nIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1787811955),
('Z6gP0F1pIkWWaY1EKM4tOuTbwOktqCx2TmtYAeXa',NULL,'172.69.109.44','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJjUDl4SXZ2QmxwZE5zNDZCRXJTSFpMUGFVVjVZejFyWFNZVHVkTHhOIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9pbW0tYnVuZ28ud2ViLmlkXC9pbmRleC5waHAiLCJyb3V0ZSI6ImxhbmRpbmcifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1787814761);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kader','instruktur') NOT NULL DEFAULT 'kader',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Admin SIM IMM','admin@admin.com','2026-08-27 11:49:26','$2y$12$Am/pKT.rqU2Vz2zIqXDd4e47KB4hu8StDdqau.aLl7kd3IRanSmoS','admin',NULL,'2026-08-27 06:59:03','2026-08-27 11:49:27'),
(2,'Fajar Hidayat','instruktur@example.com','2026-08-27 11:49:27','$2y$12$FAx5iFWh4xyE4zgeKpNfX.W9mIOxvo89KyrvRXv6cpH88UZ3UxkjO','instruktur',NULL,'2026-08-27 11:45:43','2026-08-27 11:49:27'),
(3,'Rahma Nuraini','rahma.instruktur@example.com','2026-08-27 11:49:27','$2y$12$U9De9TxqDSeNcUSBkDNTB.WLAK7nKhTi3fbx7lxF/2sXG.DWkTQPy','instruktur',NULL,'2026-08-27 11:45:43','2026-08-27 11:49:27'),
(4,'Aisyah Rahmawati','kader@example.com','2026-08-27 11:49:27','$2y$12$GnMMnt5b1xzScyFSdLCmZuIKb3eSCsCKOj3zOnCcBB7VzKOZB.TDS','kader',NULL,'2026-08-27 11:45:43','2026-08-27 11:49:27'),
(5,'Nabila Putri Ramadhani','nabila@example.com','2026-08-27 11:49:27','$2y$12$RPIokw2WZKfkLD1gP6XCgO4YzGRtxIz.Z5eez8mL1PUFMMdAC6slG','kader',NULL,'2026-08-27 11:45:44','2026-08-27 11:49:28'),
(6,'Fikri Maulana','fikri@example.com','2026-08-27 11:49:28','$2y$12$rpzgB.G1.pML2QsMRxcx5ucqicLnryF4DyWSDtx76r2RmMoQJ5h4O','kader',NULL,'2026-08-27 11:45:44','2026-08-27 11:49:28'),
(7,'Rafi Pratama','rafi@example.com','2026-08-27 11:49:28','$2y$12$Mw9ReZFSCqKe9QpMtZzH4uB0TP3G0H9EfMKbwfOk1lscoC6hmgTka','kader',NULL,'2026-08-27 11:45:44','2026-08-27 11:49:28'),
(8,'Siti Hanifah','siti@example.com','2026-08-27 11:49:28','$2y$12$uxmoE.7L9FSqK./2c0wlLe7dxelcoiSQHDCB0LIY1e7YOfarzH672','kader',NULL,'2026-08-27 11:45:44','2026-08-27 11:49:29'),
(9,'Zahra Amalia','zahra@example.com','2026-08-27 11:49:29','$2y$12$fTNjwhnlnwyxlwYbmHQsV.BwE77dv8H2ql0I66OjO2eFKtCMesbv6','kader',NULL,'2026-08-27 11:45:45','2026-08-27 11:49:29'),
(10,'Dimas Saputra','dimas@example.com','2026-08-27 11:49:29','$2y$12$EXHBtD5E1GiUsYiZS/X7EubNKhVPej4usKJwQABhcX4J2.k0aDHGa','kader',NULL,'2026-08-27 11:45:45','2026-08-27 11:49:29'),
(11,'Farhan Akbar','farhan@example.com','2026-08-27 11:49:29','$2y$12$1Ivw873gPmOLstOgGlmIU.Jzvi4ZHnV9MuHTU1fDFH3yAPyZQXheC','kader',NULL,'2026-08-27 11:45:45','2026-08-27 11:49:29'),
(12,'Salma Nurfadilah','salma@example.com','2026-08-27 11:49:29','$2y$12$ZVGKVXuSAnRZKPbCyQeM/up2qtmapIWVrAJ7ey3ErsM.vNgtONDge','kader',NULL,'2026-08-27 11:45:45','2026-08-27 11:49:30'),
(13,'Bagas Ramadhan','bagas@example.com','2026-08-27 11:49:30','$2y$12$mrHIitA/lOnA0TDRK1z9sOBjDD212xMSoWUJi.wjdUSvz54XV2Lrq','kader',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30'),
(14,'Laila Fitriani','laila@example.com','2026-08-27 11:49:30','$2y$12$0ZcA6ePtzWWml5qcSVO13O4lUXm8sb3J6y/3wRzQPBl9aa6FqsePy','kader',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30'),
(15,'Rizky Kurniawan','rizky.nonaktif@example.com','2026-08-27 11:49:30','$2y$12$mD7p76Wg4qhZS8AxsPkVxOQQUWp.mnJ.YiFIeId3gfwRVgDH4.xDS','kader',NULL,'2026-08-27 11:45:46','2026-08-27 11:49:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'sim_keanggotaan_imm'
--

--
-- Dumping routines for database 'sim_keanggotaan_imm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-08-27  7:42:14
