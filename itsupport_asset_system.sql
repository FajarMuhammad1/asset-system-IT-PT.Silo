-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 03, 2026 at 12:02 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itsupport_asset_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Tiket Helpdesk telah di-created', 'App\\Models\\Ticket', 'created', 1, 'App\\Models\\User', 5, '{\"attributes\": {\"status\": \"Open\", \"prioritas\": \"Normal\", \"teknisi_id\": null, \"solusi_teknisi\": null, \"alasan_penolakan\": null}}', NULL, '2025-12-20 08:02:05', '2025-12-20 08:02:05'),
(2, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 1, 'App\\Models\\User', 2, '{\"old\": {\"teknisi_id\": null}, \"attributes\": {\"teknisi_id\": 15}}', NULL, '2025-12-20 08:02:30', '2025-12-20 08:02:30'),
(3, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 1, 'App\\Models\\User', 15, '{\"old\": {\"status\": \"Open\"}, \"attributes\": {\"status\": \"Progres\"}}', NULL, '2025-12-20 08:02:56', '2025-12-20 08:02:56'),
(4, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 1, 'App\\Models\\User', 15, '{\"old\": {\"status\": \"Progres\", \"solusi_teknisi\": null}, \"attributes\": {\"status\": \"Closed\", \"solusi_teknisi\": \"kajjksakjskja\"}}', NULL, '2025-12-20 08:03:07', '2025-12-20 08:03:07'),
(5, 'default', 'PPI (Request) telah di-created', 'App\\Models\\Ppi', 'created', 71, 'App\\Models\\User', 5, '{\"attributes\": {\"no_ppi\": \"0001.PPI-SFP.XII.2025\", \"status\": \"pending\", \"tanggal\": \"2025-12-22\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"radio rig Motorola XiR M3688\", \"keterangan\": \"hsajgd\", \"ba_kerusakan\": \"hadjhajhda\"}}', NULL, '2025-12-22 00:13:18', '2025-12-22 00:13:18'),
(6, 'default', 'PPI (Request) telah di-updated', 'App\\Models\\Ppi', 'updated', 71, 'App\\Models\\User', 2, '{\"old\": {\"no_ppi\": \"0001.PPI-SFP.XII.2025\", \"status\": \"pending\", \"tanggal\": \"2025-12-22\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"radio rig Motorola XiR M3688\", \"keterangan\": \"hsajgd\", \"ba_kerusakan\": \"hadjhajhda\"}, \"attributes\": {\"no_ppi\": \"0001.PPI-SFP.XII.2025\", \"status\": \"disetujui\", \"tanggal\": \"2025-12-22\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"radio rig Motorola XiR M3688\", \"keterangan\": \"hsajgd\", \"ba_kerusakan\": \"hadjhajhda\"}}', NULL, '2025-12-22 00:14:05', '2025-12-22 00:14:05'),
(7, 'default', 'PPI (Request) telah di-updated', 'App\\Models\\Ppi', 'updated', 71, 'App\\Models\\User', 2, '{\"old\": {\"no_ppi\": \"0001.PPI-SFP.XII.2025\", \"status\": \"disetujui\", \"tanggal\": \"2025-12-22\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"radio rig Motorola XiR M3688\", \"keterangan\": \"hsajgd\", \"ba_kerusakan\": \"hadjhajhda\"}, \"attributes\": {\"no_ppi\": \"0001.PPI-SFP.XII.2025\", \"status\": \"selesai\", \"tanggal\": \"2025-12-22\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"radio rig Motorola XiR M3688\", \"keterangan\": \"hsajgd\", \"ba_kerusakan\": \"hadjhajhda\"}}', NULL, '2025-12-22 02:47:05', '2025-12-22 02:47:05'),
(8, 'default', 'Tiket Helpdesk telah di-created', 'App\\Models\\Ticket', 'created', 2, 'App\\Models\\User', 5, '{\"attributes\": {\"status\": \"Open\", \"prioritas\": \"Normal\", \"teknisi_id\": null, \"solusi_teknisi\": null, \"alasan_penolakan\": null}}', NULL, '2025-12-27 00:10:12', '2025-12-27 00:10:12'),
(9, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 2, 'App\\Models\\User', 2, '{\"old\": {\"teknisi_id\": null}, \"attributes\": {\"teknisi_id\": 15}}', NULL, '2025-12-27 00:11:42', '2025-12-27 00:11:42'),
(10, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 2, 'App\\Models\\User', 15, '{\"old\": {\"status\": \"Open\"}, \"attributes\": {\"status\": \"Progres\"}}', NULL, '2025-12-27 00:12:08', '2025-12-27 00:12:08'),
(11, 'default', 'Tiket Helpdesk telah di-updated', 'App\\Models\\Ticket', 'updated', 2, 'App\\Models\\User', 15, '{\"old\": {\"status\": \"Progres\", \"solusi_teknisi\": null}, \"attributes\": {\"status\": \"Closed\", \"solusi_teknisi\": \"psu harus di ganti\"}}', NULL, '2025-12-27 00:13:52', '2025-12-27 00:13:52'),
(12, 'default', 'PPI (Request) telah di-created', 'App\\Models\\Ppi', 'created', 72, 'App\\Models\\User', 5, '{\"attributes\": {\"no_ppi\": \"0002.PPI-SFP.XII.2025\", \"status\": \"pending\", \"tanggal\": \"2025-12-27\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"Ssd 512 gb sata\", \"keterangan\": null, \"ba_kerusakan\": \"Ingin mengganti ram dari hdd\"}}', NULL, '2025-12-27 06:16:22', '2025-12-27 06:16:22'),
(13, 'default', 'PPI (Request) telah di-updated', 'App\\Models\\Ppi', 'updated', 12, 'App\\Models\\User', 2, '{\"old\": {\"no_ppi\": \"PPI/2025/XII/012\", \"status\": \"disetujui\", \"tanggal\": \"2025-12-28\", \"user_id\": 8, \"file_ppi\": null, \"perangkat\": \"Keyboard Logitech\", \"keterangan\": \"Tombol keyboard macet\", \"ba_kerusakan\": \"Perbaikan\"}, \"attributes\": {\"no_ppi\": \"PPI/2025/XII/012\", \"status\": \"selesai\", \"tanggal\": \"2025-12-28\", \"user_id\": 8, \"file_ppi\": null, \"perangkat\": \"Keyboard Logitech\", \"keterangan\": \"Tombol keyboard macet\", \"ba_kerusakan\": \"Perbaikan\"}}', NULL, '2025-12-27 06:16:37', '2025-12-27 06:16:37'),
(14, 'default', 'PPI (Request) telah di-updated', 'App\\Models\\Ppi', 'updated', 72, 'App\\Models\\User', 2, '{\"old\": {\"no_ppi\": \"0002.PPI-SFP.XII.2025\", \"status\": \"pending\", \"tanggal\": \"2025-12-27\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"Ssd 512 gb sata\", \"keterangan\": null, \"ba_kerusakan\": \"Ingin mengganti ram dari hdd\"}, \"attributes\": {\"no_ppi\": \"0002.PPI-SFP.XII.2025\", \"status\": \"disetujui\", \"tanggal\": \"2025-12-27\", \"user_id\": 5, \"file_ppi\": null, \"perangkat\": \"Ssd 512 gb sata\", \"keterangan\": null, \"ba_kerusakan\": \"Ingin mengganti ram dari hdd\"}}', NULL, '2025-12-27 06:16:50', '2025-12-27 06:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluars`
--

CREATE TABLE `barang_keluars` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` bigint UNSIGNED NOT NULL,
  `surat_jalan_id` bigint UNSIGNED DEFAULT NULL,
  `master_barang_id` bigint UNSIGNED DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `kode_asset` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Stok',
  `user_pemegang_id` bigint UNSIGNED DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `surat_jalan_id`, `master_barang_id`, `serial_number`, `kode_asset`, `status`, `user_pemegang_id`, `tanggal_masuk`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 3, NULL, 'LPT-00001', 'Dipakai', 5, '2025-12-18', 'kondisi baru dengan box sampai di warehouse logistik', '2025-12-18 02:33:07', '2025-12-18 02:37:32'),
(2, 2, 16, NULL, 'RAD-00001', 'Dipakai', 5, '2025-12-22', NULL, '2025-12-22 00:17:21', '2025-12-22 00:17:52'),
(3, 3, 8, NULL, 'AKS-00001', 'Dipakai', 8, '2025-12-27', NULL, '2025-12-27 00:22:03', '2025-12-27 00:30:50'),
(4, 4, 17, '1212121', 'SSD-00001', 'Dipakai', 5, '2025-12-27', NULL, '2025-12-27 06:21:45', '2025-12-27 06:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuks`
--

CREATE TABLE `barang_masuks` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `no_sj` varchar(255) DEFAULT NULL,
  `no_ppi` varchar(255) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_serah_terima`
--

CREATE TABLE `log_serah_terima` (
  `id` bigint UNSIGNED NOT NULL,
  `status` enum('draft','menunggu_ttd_user','menunggu_ttd_admin','selesai') NOT NULL DEFAULT 'draft',
  `id_surat_jalan` bigint UNSIGNED DEFAULT NULL,
  `barang_masuk_id` bigint UNSIGNED NOT NULL,
  `user_pemegang_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `tanggal_serah_terima` date NOT NULL,
  `keterangan` text,
  `kondisi_saat_serah` varchar(50) NOT NULL DEFAULT 'Baik',
  `ttd_penerima` text,
  `ttd_petugas` text,
  `file_bast` varchar(255) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_serah_terima`
--

INSERT INTO `log_serah_terima` (`id`, `status`, `id_surat_jalan`, `barang_masuk_id`, `user_pemegang_id`, `created_by`, `admin_id`, `tanggal_serah_terima`, `keterangan`, `kondisi_saat_serah`, `ttd_penerima`, `ttd_petugas`, `file_bast`, `foto_bukti`, `file`, `created_at`, `updated_at`) VALUES
(1, 'selesai', NULL, 1, 5, NULL, 2, '2025-12-18', 'lengkap tersedia di tabuan', 'Baik', 'data:image/png;base64,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', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAXcAAAC7CAYAAACend6FAAAP/0lEQVR4AezdPY4sVxmH8cEiJCRwgATeB5JhCQghkdnsgIyAAMjIECsA74EcLAgIkVgAJvIy7PO/4+Nb09PdU91V1XXOqZ9VZ7q7Ps7H844ev3qrpu8HT/5DAAEEEBiOALkPF1ILQgABBJ6eyN1vAQIILCPg6iYJkHuTYTEpBBBAYBkBcl/Gz9UIIIBAkwTIvcmwmNR5AvYigMBcAuQ+l5TzEEAAgY4IkHtHwTJVBBBAYC4Bcj9Pyl4EEECgawLk3nX4TB4BBBA4T4Dcz3OxFwEEEFhGYOeryX3nABgeAQQQ2IIAuW9BVZ8IIIDAzgTIfecAGB6B5QT0gMBrAuT+mok9CCCAQPcEyL37EFoAAggg8JoAub9mYs9lAo4ggEAnBMi9k0CZJgIIIHALAXK/hZZzEUAAgU4INCv3TviZJgIIINAkAXJvMiwmhQACCCwjQO7L+LkaAQSaJXDsiZH7seNv9QggMCgBch80sJaFAALHJkDux46/1a9DQC8INEeA3JsLiQkhgAACywmQ+3KGekAAAQSaI0DuzYXk+oQcRQABBOYQIPc5lJyDAAIIdEaA3DsLmOkigAACcwhclvucq52DAAIIINAkAXJvMiwmhQACCCwjQO7L+LkaAQQuE3BkRwLkviN8QyOAAAJbESD3rciu3++PS5f/KO3XpdkQQACBqwTI/Sqepg7+vczm49L+VBrBFwjTzXsEEHhJgNxf8mj1U7L2704m99vJe28RQACBVwTI/RWSJnf8q8zqP6XV7fvlzf9K+0lpNgQQQOAVAXJ/heSNHfsd/tnJ0D8qn1OqSR0+78tHGwIIIPBMgNyfOfTw84syyV+VdrqlDv/fspPgCwQbAgg8EyD3Zw69/Pxrmei/Szvdvld2JIsn+ALChkDjBB4yPXJ/COZVB/n8Qm8RO8FfgGM3AkcjQO79RfxvV6ZcBe9G6xVIDiFwBALk3l+U8+TMT69MO4L/Szn++9JsBydg+cclQO59xj5PyHx0ZeoR/CflOMEXCDYEjkiA3PuNep6eeUvwvyvLI/gCwYbA0QiQe98Rr4LP66WVPEbwl0a3HwEEdiFA7rtgX3XQiD01+Lxe6jiCTx3+0nH7EUBgMALkPkZAI/YI/g9XlvNpOfZlaanHlxcbAgiMTKBDuY8cjkVri+BTX08dPjdcz3X2Ydn5z9IIvkCwITAyAXIfL7qRfLL4fFVB3p+u8AdlR/7YKf8jKG9tCCAwIgFyHzGqz2vKVxVE8n9+/vjiZzL31OEJ/gUWH45C4AjrJPexo5zMPf+wR0o1eX+62gg+Xx0c2Z8e8xkBBDomQO4dB++GqUfsyeLP3XCN2JVpboDpVAR6IEDuPURpnTlG8CnDXBJ8svgcX2c0vTwT8BOBnQiQ+07gdxw2Alem2TEAhkbgEQTI/RGU2xsjWbwyTXtxMSMEViNA7quh3Lujm8eP4JPFXyvTuNl6M1YXINAGAXJvIw57ziKCv1SmcbN1z8gYG4EFBMh9AbyBLk0Wf61M42brQMG2lGMQuEfuxyBzvFVG8Ney+Ag+ZRr/ytPxfjesuEMC5N5h0DaeciR/LYvPt0vmfwIbT0P3CCCwhAC5L6E37rURfAR+7vtpUoeXxY8b+8eszCibEyD3zRF3PUD9fppLT9TUv2yN8LteqMkjMBoBch8touuvp2bxl56oSRZfJb/+6HpEAIG7CJD7XdgOeVEkf60WH8nnhmtjWfwhY2XRCDyRu1+CWwhE8KnFJ4s/9w+CROyy+FuIOheBjQiQ+0ZgB+82kk8W74br4IG2vH4JkPt6sTtiT9MbrhH+lIEsfkrDewQeTIDcHwx8wOEi9ZRqksmfe6qm1uJzzoDLtyQE2iRA7m3GpcdZVcmnHn8q+WTxkXzq8Xnf4/rMGYHtCaw4ArmvCFNX7whck3y+uiCCl8W/Q+UHAtsRIPft2B6956nkU5uvPJK5J4v32GQl4hWBDQiQ+wZQdfmCQCSfp2pSrpk+PhnJy+JfoHrkB2ONToDcR49wO+uL5HPTdSr5CL5m8Z+2M1UzQaB/AuTefwx7W0GVfLL5vM/8q+TV4kNDQ2AFAuS+AkRdXCVw6WDq8Mnkq+Sr4FOLz43XS9fZjwACMwiQ+wxITtmMQDL3Kvn6+GQkn1p8vjd+s4F1jMDoBMh99Aj3sb5IPiWZ1OOr5FOD/7JMP/vLiw0BBG4hMJTcb1m4c5skMJX8Z2WGH5bmhmuBYEPgVgLkfisx5z+CQCSfzD2ZfMo2KdWkTJNyjXr8IyJgjO4JkHv3IRx6AZF8brim5Rn5iD2Cj+jzfujFW9weBMYZk9zHieXIK0n2Pn2yJll9lXyy+pHXbm0I3EWA3O/C5qKdCETyKdXkpmuyepLfKRCGbZ8AubcfIzN8TSBP0CSTj+RztEo++3vJ5DNvDYHNCJD7Zmh1vDGBZO6ReZV8pJ4na1Kuyf583ngKukegXQLk3m5szGwegSr5Wq6J1KeSn9eLsxAYjAC5DxbQc8s5yL5Lks/XGSSTPwgGy0TgmQC5P3PwcxwCJD9OLK1kAQFyXwDPpU0TIPmmw2NyWxNYV+5bz1b/CNxOgORvZ+aKAQiQ+wBBtIRZBEh+FiYnjUKA3EeJpHXMJUDyc0ntc55RVyJA7iuB1E13BN6SfB6p7G5RJoxAJUDulYTXoxK4JPn8MZQvKDvqb8UA6yb3AYJoCfcROLnqVPI5XL/WIM/K+xbKENG6IUDu3YTKRB9EoEo+X2tQv2o4JZpk8pG8P4h6UCAMs4wAuS/j5+pxCUTy+RbKSD5fbZD3kXy+2iCSV7IZN/ZDrIzcHxFGY/ROIKJPFh/Jn34TZUSf8k3vazT/wQiQ+2ABtZxNCUTyKctE8snoazafLD6Sz6va/KYh0PlcAuQ+l5TzEHhJIP/s3zSbj/iTwavNv+Tk0zoEbu6F3G9G5gIEXhCI1JPNJ5NPRl+zebX5F5h8eDQBcn80ceONTCCir9l8XvP5NJvPTdmRGVhbIwTIvZFAmMZQBCL1ZPA1m89N2OxLNp+yTdO1+aEiceDFkPuBg2/pDyEQqU/LNp+VUXPTNZJ3E7bAsG1DgNy34apXBM4RmIo+WX1uyp6Wbc5dZx8CNxMg95uRuWA1AsftKJKP2FOXz03YvGZfyjY1m4/0j0vIyhcTIPfFCHWAwCICkfq0Pp+yTW66pi4f0f+x9J4yTnmxITCfALnPZ+VMBLYmENGf1ud/Uwat9XnZfIFhm0fgIHKfB8NZCDREoIo+ZZtan6/ZfF5l8w0Fq8WpkHuLUTEnBN4TiOSn9flatqnZfDL9lHHeX+EdAoUAuRcINgQ6IRDRR+bJ5NMi/dyEjeiTzSvbbBjI3rom994iZr4IPBOI2POUTco2+SOpZO8RfG7C5lXZ5pnTYX+S+2FDb+GDEEg2f/q0TcSebD6iT6Y/yFIt4xYC5H4LLeci8AgC948R0UfmyeanZZtIXjZ/P9curyT3LsNm0gi8SWBatjl3EzbZ/ZudOKFfAuTeb+zMHIE5BGo2n0w+GX1EX2/CJqNPpj+nH+d0RoDcOwvYutPV28EIVNFH8rkZm+w+oo/kU7bxtM1AvxDkPlAwLQWBmQQi+dyEjeAjek/bzATX02nk3lO0zBWB9QlU0U/LNnmssj5tk4xefX597pv3+Ci5b74QAyCAwGICEX1q8FPRR+xV9DmWz4sH0sH2BMh9e8ZGQKBHAlX0Kduk1fp8Fb2MvvGoknvjATI9BBogENHX+nyy+og+N18fK/oGQPQ0BXLvKVrmisC+BCL5iD2i/06ZSjL6PFqZUs1U9BF/OWzbkwC570nf2Aj0TSCyTx0+kk+L6HMzNiWbPF6ZluORf98r7XD25N5h0Ex5awL6v4NAFX3KNhF9Hq9Mlp/n6GX1dwBdegm5LyXoegQQOCUQ0dfn6FO+ifBl9aeUNv5M7hsD1j0CCDwlg095JpKfZvUfPz09nWb1SjgFyhobua9B8f4+XInA0QhMs/qp7MMhtfpT2aeGn2PajQTI/UZgTkcAgVUJTGWfEk7N7DNIZJ+bsml5L6sPlZmN3GeCchoCCDyEwDXZJ6v/qsyC7AuEb7cLb8j9Ahi7EUCgCQJT2SerT8uTOJlcZB/R5zU1fZl9qHzTyP0bEF4QQKB5AhF9Wn0SJ6LPH1TlSZzpzdnIPn9IdWjZk3vzv88miEArBJqbR0SfJ3Ei+3pzNrL/vMz0nOwPdXOW3MtvgQ0BBIYgUGWfEk0kn8w+JZzI/pOywpRw0nJz9pfl89CyJ/cSYRsCCAxJILJPVh/Z18w+r/8vq/15aRF9bRF+2jClHHIvEbY1R8CEENiCQGSfFtn/ogyQRy8j+2T55eO7LYI/FX6X9XtyfxdPPxBA4KAEIvvU7SP4tJRyqvBTzgmW0+/Hyf8AIvymyzrkntBpCCCAwHsCVfgp6VThR/p5X4U/reEn06/Cb6asc3i5v4+ndwgggMBFAqfCT3Yf4ec1N21zYYSfxzAj+7ym/JMMP8ce3sj94cgNiAACgxCI8NNqhh/RR/jJ8Ouz9xH+9K9qI/yHZPfkPshvmWUggMBeBF6MG9mnhh/hR/ZpEX6y+zylU5+/nwo/2f3qwif3F3HxAQEEEFidQIQf2Sdrj+zzlE4VfgZLdp8yzlT4OTfSz/G7Grnfhc1FCCCAwCICVfgp4UT4kX1aMvx0nAw/T+lU4aeOP71p+2amT+7BqCHQBwGzHJdAZJ+WDP9U+PlcpV+z/Gmmn/cRf83234mf3Mf9ZbEyBBDon0CEX2v4kXyy/JR10vI++3LzNiv9YflR5f8VuRcaNgQQQKBDApF+Ws32I/oIP+L/iNw7jOgjpmwMBBDomsAX5N51/EweAQQQOE+A3M9zsRcBBBDomsD+cu8an8kjgAACbRIg9zbjYlYIIIDAIgLkvgifixFAoAECpnCGALmfgWIXAggg0DsBcu89guaPAAIInCFA7meg2IXAJQL2I9ALAXLvJVLmiQACCNxAgNxvgOVUBBBAoBcC5N5qpMwLAQQQWECA3BfAcykCCCDQKgFybzUy5oUAAggsIPDB09OCq12KAAIIINAkAZl7k2ExKQQQQGAZAXJfxs/VCCDw9PQEQnsEyL29mJgRAgggsJgAuS9GqAMEEECgPQLk3l5MzOgaAccQQGAWAXKfhclJCCCAQF8EyL2veJktAgggMIsAuV/E5AACCCDQLwFy7zd2Zo4AAghcJEDuF9E4gAACCCwjsOfV5L4nfWMjgAACGxEg943A6hYBBBDYkwC570nf2AisRUA/CJwQIPcTID4igAACIxAg9xGiaA0IIIDACQFyPwHi41sEHEcAgR4IkHsPUTJHBBBA4EYC5H4jMKcjgAACPRBoWe498DNHBBBAoEkC5N5kWEwKAQQQWEaA3JfxczUCCLRM4MBzI/cDB9/SEUBgXALkPm5srQwBBA5MgNwPHHxLX5OAvhBoiwC5txUPs0EAAQRWIfA1AAAA//9U4+btAAAABklEQVQDAO/l+YZW8lSqAAAAAElFTkSuQmCC', NULL, NULL, NULL, '2025-12-18 02:34:52', '2025-12-18 02:37:32'),
(2, 'selesai', NULL, 2, 5, NULL, 2, '2025-12-22', NULL, 'Baik', 'data:image/png;base64,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', 'data:image/png;base64,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', NULL, NULL, NULL, '2025-12-22 00:17:52', '2025-12-22 00:17:52'),
(3, 'selesai', NULL, 3, 8, NULL, 2, '2025-12-27', NULL, 'Baik', 'data:image/png;base64,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', 'data:image/png;base64,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', NULL, NULL, NULL, '2025-12-27 00:22:42', '2025-12-27 00:30:50');
INSERT INTO `log_serah_terima` (`id`, `status`, `id_surat_jalan`, `barang_masuk_id`, `user_pemegang_id`, `created_by`, `admin_id`, `tanggal_serah_terima`, `keterangan`, `kondisi_saat_serah`, `ttd_penerima`, `ttd_petugas`, `file_bast`, `foto_bukti`, `file`, `created_at`, `updated_at`) VALUES
(4, 'selesai', NULL, 4, 5, NULL, 2, '2025-12-27', NULL, 'Baik', 'data:image/png;base64,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', 'data:image/png;base64,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', NULL, NULL, NULL, '2025-12-27 06:21:58', '2025-12-27 06:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `master_barang`
--

CREATE TABLE `master_barang` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `spesifikasi` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_barang`
--

INSERT INTO `master_barang` (`id`, `nama_barang`, `kategori`, `merk`, `spesifikasi`, `created_at`, `updated_at`) VALUES
(1, 'Laptop Lenovo Thinkpad', 'Laptop', 'Lenovo', 'Core i5 Gen 11, RAM 8GB, SSD 512GB', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(2, 'PC Desktop Dell', 'PC Desktop', 'Dell', 'Optiplex 3080, Core i7, RAM 16GB', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(3, 'Laptop Dell Latitude', 'Laptop', 'Dell', 'Series 5000, Core i5, RAM 16GB', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(4, 'Printer Epson L3110', 'Printer', 'Epson', 'EcoTank All-in-One Ink Tank Printer', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(5, 'Printer Dot Matrix LX-310', 'Printer', 'Epson', '9 Pin, Impact Dot Matrix', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(6, 'Scanner Canon', 'Scanner', 'Canon', 'Lide 400 Flatbed Scanner', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(7, 'Mouse Wireless', 'Aksesoris', 'Logitech', 'M170 Wireless 2.4Ghz', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(8, 'Keyboard Mechanical', 'Aksesoris', 'Logitech', 'K845 Mechanical Illuminated', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(9, 'Monitor Samsung 24\"', 'Monitor', 'Samsung', 'LED IPS Panel 75Hz', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(10, 'UPS Prolink', 'UPS', 'Prolink', '1200VA Line Interactive', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(11, 'Kabel LAN Belden', 'Network', 'Belden', 'Cat 6 Original (Per Box)', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(12, 'Radio Rig / HT', 'Network', 'Icom', 'IC-2300H VHF FM Transceiver', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(13, 'CCTV Outdoor', 'CCTV', 'Hikvision', '2MP Bullet Camera IP67', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(14, 'Pita Printer LX-310', 'Consumable', 'Epson', 'Ribbon Cartridge Original', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(15, 'Tinta Epson 003 Black', 'Consumable', 'Epson', 'Botol 65ml', '2025-12-18 02:30:30', '2025-12-18 02:30:30'),
(16, 'radio rig Motorola XiR M3688', 'radio rig', 'motorola', 'Tipe Radio: Analog / Digital (DMR – Digital Mobile Radio)\r\nFrekuensi: VHF 136-174 MHz, UHF 403-470 MHz (beberapa penjual mencantumkan UHF 403-438 MHz) \r\n2\r\nDaya Output:\r\nVHF: 1–25 Watt atau 25–45 Watt\r\nUHF: 1–25 Watt atau 25–40 Watt \r\n2\r\nKapasitas Saluran: 160 saluran\r\nBobot: 1,3 kg\r\nDimensi: 44 x 169 x 134 mm (Tinggi x Lebar x Kedalaman)\r\nLayar: Alfanumerik kontras tinggi untuk menampilkan ID penelepon dan pengaturan radio secara jelas\r\nStandar Proteksi: IP54 (perlindungan debu dan air), dan dibuat tahan uji militer MIL-STD 810 C/D/E/F/G', '2025-12-22 00:15:48', '2025-12-22 00:15:48'),
(17, 'ROM SSD 512 gb', 'ssd eksternal', 'vgen', 'djkahhdkj', '2025-12-27 06:18:32', '2025-12-27 06:18:32');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_07_062556_create_surat_jalan_table', 1),
(5, '2025_11_08_091621_add_file_to_surat_jalan_table', 1),
(6, '2025_11_08_134726_add_departemen_to_users_table', 1),
(7, '2025_11_08_200712_create_barang_masuk_table', 1),
(8, '2025_11_08_200719_create_barang_keluar_table', 1),
(9, '2025_11_08_200851_create_barang_masuks_table', 1),
(10, '2025_11_08_200859_create_barang_keluars_table', 1),
(11, '2025_11_10_075330_add_fields_to_barang_masuk_table', 1),
(12, '2025_11_10_082257_add_no_fields_to_barang_masuk_table', 1),
(13, '2025_11_10_131135_add_perusahaan_to_users_table', 1),
(14, '2025_11_11_084202_create_master_barang_table', 1),
(15, '2025_11_11_091639_update_surat_jalan_table_to_header', 1),
(16, '2025_11_11_091802_create_surat_jalan_details_table', 1),
(17, '2025_11_11_091940_update_barang_masuk_table_to_assets', 1),
(18, '2025_11_11_102618_modify_bast_column_in_surat_jalan_table', 1),
(19, '2025_11_11_113155_create_log_serah_terima_table', 1),
(20, '2025_11_11_114204_add_bast_fields_to_log_serah_terima_table', 1),
(21, '2025_11_12_055951_add_id_suratjalan_to_surat_jalan_table', 1),
(22, '2025_11_14_061225_create_ppis_table', 1),
(23, '2025_11_18_065030_create_activity_log_table', 1),
(24, '2025_11_18_065031_add_event_column_to_activity_log_table', 1),
(25, '2025_11_18_065032_add_batch_uuid_column_to_activity_log_table', 1),
(26, '2025_12_03_071156_create_tickets_table', 1),
(27, '2025_12_03_122455_add_started_at_to_tickets_table', 1),
(28, '2025_12_08_073318_create_task_reports_table', 1),
(29, '2025_12_11_065759_add_status_and_sj_to_log_serah_terima_table', 1),
(30, '2025_12_11_082720_update_log_serah_terima_structure', 1),
(31, '2025_12_11_093950_update_log_serah_terima_table', 1),
(32, '2025_12_11_120320_add_kondisi_to_log_serah_terima_table', 1),
(33, '2025_12_13_090856_make_sn_and_code_nullable', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ppis`
--

CREATE TABLE `ppis` (
  `id` bigint UNSIGNED NOT NULL,
  `no_ppi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `perangkat` varchar(255) NOT NULL,
  `ba_kerusakan` text NOT NULL,
  `keterangan` text,
  `file_ppi` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','selesai','ditolak') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ppis`
--

INSERT INTO `ppis` (`id`, `no_ppi`, `tanggal`, `user_id`, `perangkat`, `ba_kerusakan`, `keterangan`, `file_ppi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PPI/2025/X/001', '2025-10-23', 5, 'Jaringan WiFi', 'Penambahan', 'Tidak bisa connect internet', NULL, 'selesai', '2025-10-23 02:39:00', '2025-10-24 13:39:00'),
(2, 'PPI/2025/II/002', '2025-02-08', 5, 'Printer Epson L3110', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'pending', '2025-02-08 07:51:00', '2025-02-10 00:51:00'),
(3, 'PPI/2025/X/003', '2025-10-15', 12, 'Printer Epson L3110', 'Penambahan', 'Hasil print putus-putus', NULL, 'selesai', '2025-10-15 05:24:00', '2025-10-15 13:24:00'),
(4, 'PPI/2025/II/004', '2025-02-09', 13, 'Laptop Dell Latitude', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-02-09 01:45:00', '2025-02-09 06:45:00'),
(5, 'PPI/2025/IX/005', '2025-09-14', 7, 'Jaringan WiFi', 'Penambahan', 'PC sangat lambat saat booting', NULL, 'disetujui', '2025-09-14 07:25:00', '2025-09-15 15:25:00'),
(6, 'PPI/2025/VI/006', '2025-06-02', 5, 'Printer Epson L3110', 'Perbaikan', 'Lampu indikator kedap-kedip', NULL, 'selesai', '2025-06-02 03:00:00', '2025-06-02 20:00:00'),
(7, 'PPI/2025/IX/007', '2025-09-13', 5, 'Mouse Wireless', 'Perbaikan', 'Baterai drop cepat habis', NULL, 'ditolak', '2025-09-13 00:30:00', '2025-09-13 04:30:00'),
(8, 'PPI/2025/II/008', '2025-02-09', 5, 'Jaringan WiFi', 'Perbaikan', 'Tombol keyboard macet', NULL, 'selesai', '2025-02-09 00:18:00', '2025-02-10 21:18:00'),
(9, 'PPI/2025/II/009', '2025-02-18', 11, 'PC Desktop Lenovo', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'disetujui', '2025-02-18 00:59:00', '2025-02-18 04:59:00'),
(10, 'PPI/2025/VII/010', '2025-07-13', 12, 'Jaringan WiFi', 'Penambahan', 'Lampu indikator kedap-kedip', NULL, 'selesai', '2025-07-13 00:41:00', '2025-07-13 06:41:00'),
(11, 'PPI/2025/V/011', '2025-05-17', 5, 'Monitor Samsung 24\"', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-05-17 00:03:00', '2025-05-17 07:03:00'),
(12, 'PPI/2025/XII/012', '2025-12-28', 8, 'Keyboard Logitech', 'Perbaikan', 'Tombol keyboard macet', NULL, 'selesai', '2025-12-28 08:45:00', '2025-12-27 06:16:37'),
(13, 'PPI/2025/III/013', '2025-03-17', 7, 'Jaringan WiFi', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'selesai', '2025-03-17 05:06:00', '2025-03-18 03:06:00'),
(14, 'PPI/2025/II/014', '2025-02-09', 5, 'Monitor Samsung 24\"', 'Perbaikan', 'Hasil print putus-putus', NULL, 'ditolak', '2025-02-09 06:08:00', '2025-02-11 04:08:00'),
(15, 'PPI/2025/VI/015', '2025-06-25', 5, 'Printer Epson L3110', 'Penambahan', 'PC sangat lambat saat booting', NULL, 'selesai', '2025-06-25 05:40:00', '2025-06-27 02:40:00'),
(16, 'PPI/2025/V/016', '2025-05-01', 5, 'Kabel LAN', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'pending', '2025-05-01 02:43:00', '2025-05-01 12:43:00'),
(17, 'PPI/2025/V/017', '2025-05-12', 5, 'Laptop Dell Latitude', 'Perbaikan', 'Baterai drop cepat habis', NULL, 'ditolak', '2025-05-12 03:05:00', '2025-05-13 17:05:00'),
(18, 'PPI/2025/IX/018', '2025-09-23', 5, 'PC Desktop Lenovo', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'selesai', '2025-09-23 01:04:00', '2025-09-23 15:04:00'),
(19, 'PPI/2025/IX/019', '2025-09-20', 5, 'Jaringan WiFi', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'disetujui', '2025-09-20 02:50:00', '2025-09-20 15:50:00'),
(20, 'PPI/2025/VIII/020', '2025-08-13', 12, 'UPS Prolink', 'Perbaikan', 'Baterai drop cepat habis', NULL, 'selesai', '2025-08-13 05:20:00', '2025-08-14 10:20:00'),
(21, 'PPI/2025/X/021', '2025-10-17', 11, 'Jaringan WiFi', 'Perbaikan', 'Tombol keyboard macet', NULL, 'disetujui', '2025-10-17 00:56:00', '2025-10-17 10:56:00'),
(22, 'PPI/2025/VI/022', '2025-06-28', 13, 'Keyboard Logitech', 'Penambahan', 'Hasil print putus-putus', NULL, 'pending', '2025-06-28 03:27:00', '2025-06-28 21:27:00'),
(23, 'PPI/2025/X/023', '2025-10-27', 5, 'Printer Epson L3110', 'Perbaikan', 'Layar blue screen tiba-tiba', NULL, 'ditolak', '2025-10-27 05:14:00', '2025-10-27 21:14:00'),
(24, 'PPI/2025/II/024', '2025-02-10', 12, 'Kabel LAN', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-02-10 04:09:00', '2025-02-11 09:09:00'),
(25, 'PPI/2025/XI/025', '2025-11-02', 14, 'Scanner Canon', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'pending', '2025-11-02 06:58:00', '2025-11-03 18:58:00'),
(26, 'PPI/2025/XII/026', '2025-12-11', 11, 'Jaringan WiFi', 'Perbaikan', 'Layar blue screen tiba-tiba', NULL, 'selesai', '2025-12-11 05:17:00', '2025-12-12 14:17:00'),
(27, 'PPI/2025/VI/027', '2025-06-01', 5, 'Jaringan WiFi', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'disetujui', '2025-06-01 06:45:00', '2025-06-01 19:45:00'),
(28, 'PPI/2025/XII/028', '2025-12-13', 5, 'Laptop Dell Latitude', 'Perbaikan', 'Lampu indikator kedap-kedip', NULL, 'selesai', '2025-12-13 08:44:00', '2025-12-13 11:44:00'),
(29, 'PPI/2025/VI/029', '2025-06-05', 5, 'Laptop Dell Latitude', 'Penambahan', 'Kabel digigit tikus', NULL, 'selesai', '2025-06-05 07:10:00', '2025-06-07 03:10:00'),
(30, 'PPI/2025/II/030', '2025-02-07', 5, 'UPS Prolink', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'ditolak', '2025-02-07 07:14:00', '2025-02-09 04:14:00'),
(31, 'PPI/2025/VIII/031', '2025-08-11', 11, 'Laptop Dell Latitude', 'Perbaikan', 'Tombol keyboard macet', NULL, 'selesai', '2025-08-11 05:10:00', '2025-08-12 00:10:00'),
(32, 'PPI/2025/IV/032', '2025-04-19', 6, 'Printer Epson L3110', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'ditolak', '2025-04-19 07:45:00', '2025-04-19 09:45:00'),
(33, 'PPI/2025/IV/033', '2025-04-25', 8, 'UPS Prolink', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'ditolak', '2025-04-25 00:11:00', '2025-04-25 20:11:00'),
(34, 'PPI/2025/VII/034', '2025-07-01', 9, 'Printer Epson L3110', 'Perbaikan', 'Layar blue screen tiba-tiba', NULL, 'pending', '2025-07-01 06:47:00', '2025-07-02 01:47:00'),
(35, 'PPI/2025/IV/035', '2025-04-10', 5, 'PC Desktop Lenovo', 'Penambahan', 'Minta install Microsoft Visio', NULL, 'selesai', '2025-04-10 07:42:00', '2025-04-12 01:42:00'),
(36, 'PPI/2025/III/036', '2025-03-20', 7, 'PC Desktop Lenovo', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'pending', '2025-03-20 05:27:00', '2025-03-22 03:27:00'),
(37, 'PPI/2025/VI/037', '2025-06-23', 5, 'Printer Epson L3110', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'selesai', '2025-06-23 02:18:00', '2025-06-24 20:18:00'),
(38, 'PPI/2025/IX/038', '2025-09-06', 5, 'Kabel LAN', 'Perbaikan', 'Lampu indikator kedap-kedip', NULL, 'ditolak', '2025-09-06 03:04:00', '2025-09-06 09:04:00'),
(39, 'PPI/2025/III/039', '2025-03-01', 5, 'Jaringan WiFi', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-03-01 04:03:00', '2025-03-01 20:03:00'),
(40, 'PPI/2025/IV/040', '2025-04-16', 13, 'UPS Prolink', 'Perbaikan', 'Hasil print putus-putus', NULL, 'ditolak', '2025-04-16 04:10:00', '2025-04-17 04:10:00'),
(41, 'PPI/2025/I/041', '2025-01-28', 7, 'Mouse Wireless', 'Perbaikan', 'Hasil print putus-putus', NULL, 'selesai', '2025-01-28 05:01:00', '2025-01-28 23:01:00'),
(42, 'PPI/2025/IV/042', '2025-04-07', 9, 'Keyboard Logitech', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-04-07 01:48:00', '2025-04-08 17:48:00'),
(43, 'PPI/2025/VII/043', '2025-07-03', 13, 'PC Desktop Lenovo', 'Perbaikan', 'Lampu indikator kedap-kedip', NULL, 'pending', '2025-07-03 04:54:00', '2025-07-04 01:54:00'),
(44, 'PPI/2025/IV/044', '2025-04-16', 5, 'PC Desktop Lenovo', 'Perbaikan', 'Hasil print putus-putus', NULL, 'selesai', '2025-04-16 02:36:00', '2025-04-16 20:36:00'),
(45, 'PPI/2025/V/045', '2025-05-24', 13, 'Laptop Dell Latitude', 'Perbaikan', 'Tombol keyboard macet', NULL, 'disetujui', '2025-05-24 01:14:00', '2025-05-25 15:14:00'),
(46, 'PPI/2025/X/046', '2025-10-27', 5, 'UPS Prolink', 'Perbaikan', 'Lampu indikator kedap-kedip', NULL, 'selesai', '2025-10-27 01:12:00', '2025-10-28 01:12:00'),
(47, 'PPI/2025/IV/047', '2025-04-04', 9, 'Jaringan WiFi', 'Penambahan', 'Minta install Microsoft Visio', NULL, 'ditolak', '2025-04-04 06:20:00', '2025-04-05 14:20:00'),
(48, 'PPI/2025/II/048', '2025-02-27', 5, 'Scanner Canon', 'Penambahan', 'Suara kipas PC berisik', NULL, 'selesai', '2025-02-27 08:03:00', '2025-03-01 01:03:00'),
(49, 'PPI/2025/IV/049', '2025-04-08', 5, 'UPS Prolink', 'Perbaikan', 'Layar blue screen tiba-tiba', NULL, 'disetujui', '2025-04-08 04:03:00', '2025-04-08 15:03:00'),
(50, 'PPI/2025/VII/050', '2025-07-26', 5, 'PC Desktop Lenovo', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'selesai', '2025-07-26 08:27:00', '2025-07-26 14:27:00'),
(51, 'PPI/2025/II/051', '2025-02-26', 7, 'Jaringan WiFi', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'disetujui', '2025-02-26 00:18:00', '2025-02-26 23:18:00'),
(52, 'PPI/2025/XI/052', '2025-11-23', 5, 'Kabel LAN', 'Penambahan', 'PC sangat lambat saat booting', NULL, 'selesai', '2025-11-23 07:38:00', '2025-11-25 01:38:00'),
(53, 'PPI/2025/IV/053', '2025-04-08', 5, 'Mouse Wireless', 'Penambahan', 'Layar blue screen tiba-tiba', NULL, 'disetujui', '2025-04-08 00:50:00', '2025-04-08 17:50:00'),
(54, 'PPI/2025/III/054', '2025-03-12', 11, 'Scanner Canon', 'Perbaikan', 'Tombol keyboard macet', NULL, 'ditolak', '2025-03-12 06:28:00', '2025-03-13 19:28:00'),
(55, 'PPI/2025/VIII/055', '2025-08-19', 13, 'UPS Prolink', 'Perbaikan', 'Baterai drop cepat habis', NULL, 'disetujui', '2025-08-19 03:25:00', '2025-08-20 16:25:00'),
(56, 'PPI/2025/X/056', '2025-10-22', 5, 'Scanner Canon', 'Penambahan', 'PC sangat lambat saat booting', NULL, 'selesai', '2025-10-22 08:10:00', '2025-10-23 16:10:00'),
(57, 'PPI/2025/X/057', '2025-10-14', 13, 'Kabel LAN', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'pending', '2025-10-14 05:01:00', '2025-10-14 16:01:00'),
(58, 'PPI/2025/I/058', '2025-01-23', 5, 'Jaringan WiFi', 'Penambahan', 'Baterai drop cepat habis', NULL, 'selesai', '2025-01-23 03:49:00', '2025-01-24 16:49:00'),
(59, 'PPI/2025/XI/059', '2025-11-16', 5, 'Laptop Dell Latitude', 'Penambahan', 'Tidak bisa connect internet', NULL, 'selesai', '2025-11-16 07:12:00', '2025-11-17 07:12:00'),
(60, 'PPI/2025/IV/060', '2025-04-26', 5, 'Kabel LAN', 'Perbaikan', 'Hasil print putus-putus', NULL, 'ditolak', '2025-04-26 08:24:00', '2025-04-26 22:24:00'),
(61, 'PPI/2025/II/061', '2025-02-28', 5, 'Mouse Wireless', 'Perbaikan', 'Kabel digigit tikus', NULL, 'selesai', '2025-02-28 07:43:00', '2025-02-28 09:43:00'),
(62, 'PPI/2025/XII/062', '2025-12-01', 9, 'Printer Epson L3110', 'Penambahan', 'Kabel digigit tikus', NULL, 'selesai', '2025-12-01 03:01:00', '2025-12-01 14:01:00'),
(63, 'PPI/2025/II/063', '2025-02-19', 5, 'Mouse Wireless', 'Penambahan', 'Hasil print putus-putus', NULL, 'selesai', '2025-02-19 06:12:00', '2025-02-20 05:12:00'),
(64, 'PPI/2025/V/064', '2025-05-27', 5, 'UPS Prolink', 'Perbaikan', 'Tombol keyboard macet', NULL, 'disetujui', '2025-05-27 02:58:00', '2025-05-28 06:58:00'),
(65, 'PPI/2025/XII/065', '2025-12-12', 12, 'Jaringan WiFi', 'Penambahan', 'Kabel digigit tikus', NULL, 'selesai', '2025-12-12 04:07:00', '2025-12-12 10:07:00'),
(66, 'PPI/2025/I/066', '2025-01-22', 12, 'Kabel LAN', 'Perbaikan', 'Suara kipas PC berisik', NULL, 'ditolak', '2025-01-22 01:33:00', '2025-01-23 18:33:00'),
(67, 'PPI/2025/VIII/067', '2025-08-13', 5, 'Monitor Samsung 24\"', 'Perbaikan', 'Tidak bisa connect internet', NULL, 'selesai', '2025-08-13 02:59:00', '2025-08-13 05:59:00'),
(68, 'PPI/2025/III/068', '2025-03-15', 5, 'Laptop Dell Latitude', 'Perbaikan', 'Hasil print putus-putus', NULL, 'selesai', '2025-03-15 07:52:00', '2025-03-15 16:52:00'),
(69, 'PPI/2025/XII/069', '2025-12-24', 10, 'Mouse Wireless', 'Perbaikan', 'Kabel digigit tikus', NULL, 'disetujui', '2025-12-24 00:17:00', '2025-12-24 21:17:00'),
(70, 'PPI/2025/VII/070', '2025-07-27', 13, 'Printer Epson L3110', 'Perbaikan', 'PC sangat lambat saat booting', NULL, 'ditolak', '2025-07-27 00:00:00', '2025-07-28 09:00:00'),
(71, '0001.PPI-SFP.XII.2025', '2025-12-22', 5, 'radio rig Motorola XiR M3688', 'hadjhajhda', 'hsajgd', NULL, 'selesai', '2025-12-22 00:13:18', '2025-12-22 02:47:05'),
(72, '0002.PPI-SFP.XII.2025', '2025-12-27', 5, 'Ssd 512 gb sata', 'Ingin mengganti ram dari hdd', NULL, NULL, 'disetujui', '2025-12-27 06:16:22', '2025-12-27 06:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('JYoZI15090pTJlHmVgDxHsdML5iWClNP54CzD7CC', 5, '192.168.0.158', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic1hQZ3Q1ZkphZGpId3luWXBsWHZrWEJHeDRhanBHNUNoVDlOYm5MdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xOTIuMTY4LjAuMTc3OjgwMDAvUGVuZ2d1bmEvaGVscGRlc2svMiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==', 1767136390),
('MjVkXbcCcktmxe5fBvnD61nA9Lt5qxfRIYZMgE13', 2, '192.168.0.177', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRkJEOVRGUXhtNTRGQzcxY0xaeHVsaDRCd3REaEJrNWJGRTVwTmhtbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xOTIuMTY4LjAuMTc3OjgwMDAvYmFyYW5nbWFzdWsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1767136880),
('yyrUT9lUOh1d1JHiKAq4F7v9VU6o0IfgDceWZZ6G', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0x1RDlWaHg2eGhKcjI3MjYzdXZtUjRuQ092RHRQQmNaSmtxM0treCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9pdHN1cHBvcnRfYXNzZXQudGVzdC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767398518),
('Z9S9gYrNGvswU4sck7jv69unRDq9uUhd5nbtS4Bc', 2, '192.168.0.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3FtVXNLYm16R3hpalZhc2tUN0dkM0xZaTdEZ1VXSUVnblNUR3hKNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xOTIuMTY4LjAuMTc3OjgwMDAvdGFzay1yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1767136283);

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id_sj` bigint UNSIGNED NOT NULL,
  `id_suratjalan` varchar(255) DEFAULT NULL,
  `no_sj` varchar(255) NOT NULL,
  `no_ppi` varchar(255) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `jenis_surat_jalan` varchar(255) DEFAULT NULL,
  `is_bast_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_input` date DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `surat_jalan`
--

INSERT INTO `surat_jalan` (`id_sj`, `id_suratjalan`, `no_sj`, `no_ppi`, `no_po`, `keterangan`, `jenis_surat_jalan`, `is_bast_submitted`, `tanggal_input`, `file`, `created_at`, `updated_at`) VALUES
(1, 'SJ001', 'SJ-OPERATION-DESEMBER-2025-SFP-1111 SILO', 'PPI/2025/XII/028', '0001', NULL, 'baru', 1, '2025-12-18', NULL, '2025-12-18 02:32:20', '2025-12-18 02:37:32'),
(2, 'SJ002', 'SJ-OPERATION-DESEMBER-2025-SFP-1112 SILO', '0001.PPI-SFP.XII.2025', '0002-desember-2025', 'shjahjhsajh', 'baru', 1, '2025-12-22', NULL, '2025-12-22 00:16:46', '2025-12-22 00:17:52'),
(3, 'SJ003', 'SJ-OPERATION-DESEMBER-2025-SFP-1113 SILO', 'PPI/2025/XII/012', '0002-desember-2025', NULL, 'baru', 1, '2025-12-27', NULL, '2025-12-27 00:21:25', '2025-12-27 00:30:50'),
(4, 'SJ004', 'SJ-SFP-Oktober-2025-OPERATION-1009 SILO', '0002.PPI-SFP.XII.2025', '00348/PO-HO/SILO/2025', NULL, 'Penambahan', 1, '2025-12-27', NULL, '2025-12-27 06:20:31', '2025-12-27 06:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan_details`
--

CREATE TABLE `surat_jalan_details` (
  `id` bigint UNSIGNED NOT NULL,
  `surat_jalan_id` bigint UNSIGNED NOT NULL,
  `master_barang_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `surat_jalan_details`
--

INSERT INTO `surat_jalan_details` (`id`, `surat_jalan_id`, `master_barang_id`, `qty`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, '2025-12-18 02:32:20', '2025-12-18 02:32:20'),
(2, 2, 16, 1, '2025-12-22 00:16:46', '2025-12-22 00:16:46'),
(3, 3, 8, 1, '2025-12-27 00:21:25', '2025-12-27 00:21:25'),
(4, 4, 17, 1, '2025-12-27 06:20:31', '2025-12-27 06:20:31');

-- --------------------------------------------------------

--
-- Table structure for table `task_reports`
--

CREATE TABLE `task_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `ticket_id` bigint UNSIGNED DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `hasil` text,
  `lampiran` varchar(255) DEFAULT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `task_reports`
--

INSERT INTO `task_reports` (`id`, `staff_id`, `ticket_id`, `judul`, `deskripsi`, `hasil`, `lampiran`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(1, 15, 1, 'service pc GA', 'jkajjhajdha', 'asbnashgash', 'task_reports/ra3jiGHrp1BE6gzGeNUE44hGzVEUXARTTw34Q77S.jpg', '2025-12-20 17:03:00', '2025-12-20 18:03:00', '2025-12-20 08:04:01', '2025-12-20 08:04:01'),
(2, 15, 2, 'pc mati total', '-', 'ganti psu', NULL, '2025-12-27 09:15:00', '2025-12-27 10:14:00', '2025-12-27 00:15:06', '2025-12-27 00:15:06');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `no_tiket` varchar(255) NOT NULL,
  `pelapor_id` bigint UNSIGNED NOT NULL,
  `judul_masalah` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_masalah` varchar(255) DEFAULT NULL,
  `prioritas` varchar(255) NOT NULL DEFAULT 'Normal',
  `teknisi_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('Open','Progres','Closed','Ditolak') NOT NULL DEFAULT 'Open',
  `tgl_selesai` datetime DEFAULT NULL,
  `solusi_teknisi` text,
  `alasan_penolakan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `started_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `no_tiket`, `pelapor_id`, `judul_masalah`, `deskripsi`, `foto_masalah`, `prioritas`, `teknisi_id`, `status`, `tgl_selesai`, `solusi_teknisi`, `alasan_penolakan`, `created_at`, `updated_at`, `started_at`) VALUES
(1, 'TIK-202512-001', 5, 'service pc GA', 'clone data', 'helpdesk_uploads/3Mjx2kfAxCeRV8m8PXg1xfCZwPtAbwkcLIr8pXtP.jpg', 'Normal', 15, 'Closed', '2025-12-20 16:03:07', 'kajjksakjskja', NULL, '2025-12-20 08:02:05', '2025-12-20 08:03:07', '2025-12-20 16:02:56'),
(2, 'TIK-202512-002', 5, 'pc mati total', '-', NULL, 'Normal', 15, 'Closed', '2025-12-27 08:13:52', 'psu harus di ganti', NULL, '2025-12-27 00:10:12', '2025-12-27 00:13:52', '2025-12-27 08:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `departemen` varchar(255) DEFAULT NULL,
  `perusahaan` varchar(255) DEFAULT NULL,
  `role` enum('SuperAdmin','Admin','Staff','Pengguna') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Staff',
  `status` enum('Aktif','Off','Izin','Sakit','Cuti','Resign','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nik`, `nama`, `email`, `jabatan`, `departemen`, `perusahaan`, `role`, `status`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super Admin', 'superadmin@example.com', 'Head IT', NULL, NULL, 'SuperAdmin', 'Aktif', '$2y$12$ZKyOgwyZmF.NJCHzKIEY5u/GPXXI50S0csSb1KzJNnVbx/WIZrCC.', NULL, '2025-12-18 01:54:23', '2025-12-18 01:54:23'),
(2, NULL, 'Admin IT', 'admin@example.com', 'IT Support', NULL, NULL, 'Admin', 'Aktif', '$2y$12$JQPLxY5b0xzFvRuia7b6VeybwjTn0QyVjD5HzTcAymJpeVuzp/ZW2', NULL, '2025-12-18 01:54:23', '2025-12-18 22:25:57'),
(3, NULL, 'Teknisi Staff', 'staff@example.com', 'Teknisi', NULL, NULL, 'Staff', 'Aktif', '$2y$12$xjCQ2Ym8G1sUIBrM/3KUa.jvdKTAQH.zNre0PaJDYbhFkOuMG6h/a', NULL, '2025-12-18 01:54:24', '2025-12-18 01:54:24'),
(5, 'KRY2025001', 'Budi Santoso', 'budi@mail.com', 'Staff Admin', 'Operation', 'PT.SFP', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(6, 'KRY2025002', 'Siti Aminah', 'siti@mail.com', 'Staff Finance', 'Finance', 'PT.BCI', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(7, 'KRY2025003', 'Doni Pratama', 'doni@mail.com', 'Operator', 'Produksi', 'PT.SILO', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(8, 'KRY2025004', 'Rina Kartika', 'rina@mail.com', 'Staff HRD', 'HRD', 'PT.Banjar Asri', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(9, 'KRY2025005', 'Eko Wahyudi', 'eko@mail.com', 'Supervisor', 'Warehouse', 'PT.SFP', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(10, 'KRY2025006', 'Maya Suryani', 'maya@mail.com', 'Receptionist', 'General Affair', 'PT.BCI', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(11, 'KRY2025007', 'Agus Setiawan', 'agus@mail.com', 'Staff Logistik', 'Logistik', 'PT.SILO', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(12, 'KRY2025008', 'Dewi Lestari', 'dewi@mail.com', 'Staff Purchasing', 'Purchasing', 'PT.SFP', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(13, 'KRY2025009', 'Reza Rahardian', 'reza@mail.com', 'Surveyor', 'Mine Survey', 'PT.BCI', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(14, 'KRY2025010', 'Fajar Nugroho', 'fajar@mail.com', 'Safety Officer', 'HSE', 'PT.Banjar Asri', 'Pengguna', 'Aktif', '$2y$12$pqa/YBib90bAVVBa4r7tTeKZRL2zu82lYqYPkFw0ETh4amvl91l4K', NULL, '2025-12-18 02:02:47', '2025-12-18 02:02:47'),
(15, 'k12900912918', 'fahrin', 'fahrin@gmail.com', 'Teknisi', NULL, NULL, 'Staff', 'Aktif', '$2y$12$760Jr1wcN59LpAiDEn5apORbJQHiV..CFUCKcNJn5S7wVgpVtLDjy', NULL, '2025-12-18 05:09:59', '2025-12-18 05:09:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barang_keluars`
--
ALTER TABLE `barang_keluars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barang_masuk_serial_number_unique` (`serial_number`),
  ADD UNIQUE KEY `barang_masuk_kode_asset_unique` (`kode_asset`),
  ADD KEY `barang_masuk_surat_jalan_id_foreign` (`surat_jalan_id`),
  ADD KEY `barang_masuk_master_barang_id_foreign` (`master_barang_id`),
  ADD KEY `barang_masuk_user_pemegang_id_foreign` (`user_pemegang_id`);

--
-- Indexes for table `barang_masuks`
--
ALTER TABLE `barang_masuks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `log_serah_terima`
--
ALTER TABLE `log_serah_terima`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_serah_terima_barang_masuk_id_foreign` (`barang_masuk_id`),
  ADD KEY `log_serah_terima_user_pemegang_id_foreign` (`user_pemegang_id`),
  ADD KEY `log_serah_terima_admin_id_foreign` (`admin_id`),
  ADD KEY `log_serah_terima_created_by_foreign` (`created_by`);

--
-- Indexes for table `master_barang`
--
ALTER TABLE `master_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `ppis`
--
ALTER TABLE `ppis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ppis_no_ppi_unique` (`no_ppi`),
  ADD KEY `ppis_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id_sj`),
  ADD UNIQUE KEY `surat_jalan_no_sj_unique` (`no_sj`),
  ADD UNIQUE KEY `surat_jalan_id_suratjalan_unique` (`id_suratjalan`);

--
-- Indexes for table `surat_jalan_details`
--
ALTER TABLE `surat_jalan_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_jalan_details_surat_jalan_id_foreign` (`surat_jalan_id`),
  ADD KEY `surat_jalan_details_master_barang_id_foreign` (`master_barang_id`);

--
-- Indexes for table `task_reports`
--
ALTER TABLE `task_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_reports_staff_id_foreign` (`staff_id`),
  ADD KEY `task_reports_ticket_id_foreign` (`ticket_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_no_tiket_unique` (`no_tiket`),
  ADD KEY `tickets_pelapor_id_foreign` (`pelapor_id`),
  ADD KEY `tickets_teknisi_id_foreign` (`teknisi_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_keluars`
--
ALTER TABLE `barang_keluars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `barang_masuks`
--
ALTER TABLE `barang_masuks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_serah_terima`
--
ALTER TABLE `log_serah_terima`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_barang`
--
ALTER TABLE `master_barang`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `ppis`
--
ALTER TABLE `ppis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id_sj` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `surat_jalan_details`
--
ALTER TABLE `surat_jalan_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `task_reports`
--
ALTER TABLE `task_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_master_barang_id_foreign` FOREIGN KEY (`master_barang_id`) REFERENCES `master_barang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `barang_masuk_surat_jalan_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id_sj`) ON DELETE SET NULL,
  ADD CONSTRAINT `barang_masuk_user_pemegang_id_foreign` FOREIGN KEY (`user_pemegang_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `log_serah_terima`
--
ALTER TABLE `log_serah_terima`
  ADD CONSTRAINT `log_serah_terima_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `log_serah_terima_barang_masuk_id_foreign` FOREIGN KEY (`barang_masuk_id`) REFERENCES `barang_masuk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_serah_terima_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `log_serah_terima_user_pemegang_id_foreign` FOREIGN KEY (`user_pemegang_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ppis`
--
ALTER TABLE `ppis`
  ADD CONSTRAINT `ppis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat_jalan_details`
--
ALTER TABLE `surat_jalan_details`
  ADD CONSTRAINT `surat_jalan_details_master_barang_id_foreign` FOREIGN KEY (`master_barang_id`) REFERENCES `master_barang` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `surat_jalan_details_surat_jalan_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id_sj`) ON DELETE CASCADE;

--
-- Constraints for table `task_reports`
--
ALTER TABLE `task_reports`
  ADD CONSTRAINT `task_reports_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_reports_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_pelapor_id_foreign` FOREIGN KEY (`pelapor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_teknisi_id_foreign` FOREIGN KEY (`teknisi_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
