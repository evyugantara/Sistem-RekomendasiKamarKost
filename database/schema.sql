-- --------------------------------------------------------
-- SQL DDL untuk ERD Skripsi
-- Sistem Rekomendasi Kost Mahasiswa Berbasis Web
-- Menggunakan Metode Content-Based Filtering
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabel users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mahasiswa','pengelola') NOT NULL DEFAULT 'mahasiswa',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel profil_mahasiswa
-- --------------------------------------------------------
DROP TABLE IF EXISTS `profil_mahasiswa`;
CREATE TABLE `profil_mahasiswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nim` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_mahasiswa_user_id_foreign` (`user_id`),
  CONSTRAINT `profil_mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel profil_pengelola
-- --------------------------------------------------------
DROP TABLE IF EXISTS `profil_pengelola`;
CREATE TABLE `profil_pengelola` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ktp_number` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_pengelola_user_id_foreign` (`user_id`),
  CONSTRAINT `profil_pengelola_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel kampus
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kampus`;
CREATE TABLE `kampus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel kriteria
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kriteria`;
CREATE TABLE `kriteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('select','checkbox') NOT NULL DEFAULT 'select',
  `category` enum('umum','pribadi','bersama') NOT NULL DEFAULT 'umum',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Tabel opsi_kriteria
-- --------------------------------------------------------
DROP TABLE IF EXISTS `opsi_kriteria`;
CREATE TABLE `opsi_kriteria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kriteria_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `opsi_kriteria_kriteria_id_foreign` (`kriteria_id`),
  CONSTRAINT `opsi_kriteria_kriteria_id_foreign` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Tabel kost
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kost`;
CREATE TABLE `kost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `kampus_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kost_user_id_foreign` (`user_id`),
  KEY `kost_kampus_id_foreign` (`kampus_id`),
  CONSTRAINT `kost_kampus_id_foreign` FOREIGN KEY (`kampus_id`) REFERENCES `kampus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kost_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Tabel foto_kost
-- --------------------------------------------------------
DROP TABLE IF EXISTS `foto_kost`;
CREATE TABLE `foto_kost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kost_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foto_kost_kost_id_foreign` (`kost_id`),
  CONSTRAINT `foto_kost_kost_id_foreign` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Tabel atribut_kost
-- --------------------------------------------------------
DROP TABLE IF EXISTS `atribut_kost`;
CREATE TABLE `atribut_kost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kost_id` bigint(20) unsigned NOT NULL,
  `kriteria_id` bigint(20) unsigned NOT NULL,
  `opsi_kriteria_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atribut_kost_kost_id_foreign` (`kost_id`),
  KEY `atribut_kost_kriteria_id_foreign` (`kriteria_id`),
  KEY `atribut_kost_opsi_kriteria_id_foreign` (`opsi_kriteria_id`),
  CONSTRAINT `atribut_kost_kost_id_foreign` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atribut_kost_kriteria_id_foreign` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atribut_kost_opsi_kriteria_id_foreign` FOREIGN KEY (`opsi_kriteria_id`) REFERENCES `opsi_kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Tabel preferensi_mahasiswa
-- --------------------------------------------------------
DROP TABLE IF EXISTS `preferensi_mahasiswa`;
CREATE TABLE `preferensi_mahasiswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `kriteria_id` bigint(20) unsigned NOT NULL,
  `opsi_kriteria_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preferensi_mahasiswa_user_id_foreign` (`user_id`),
  KEY `preferensi_mahasiswa_kriteria_id_foreign` (`kriteria_id`),
  KEY `preferensi_mahasiswa_opsi_kriteria_id_foreign` (`opsi_kriteria_id`),
  CONSTRAINT `preferensi_mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `preferensi_mahasiswa_kriteria_id_foreign` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `preferensi_mahasiswa_opsi_kriteria_id_foreign` FOREIGN KEY (`opsi_kriteria_id`) REFERENCES `opsi_kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Tabel kamar
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kamar`;
CREATE TABLE `kamar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kost_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('tersedia','terisi') NOT NULL DEFAULT 'tersedia',
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kamar_kost_id_foreign` (`kost_id`),
  CONSTRAINT `kamar_kost_id_foreign` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. Tabel atribut_kamar
-- --------------------------------------------------------
DROP TABLE IF EXISTS `atribut_kamar`;
CREATE TABLE `atribut_kamar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kamar_id` bigint(20) unsigned NOT NULL,
  `kriteria_id` bigint(20) unsigned NOT NULL,
  `opsi_kriteria_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atribut_kamar_kamar_id_foreign` (`kamar_id`),
  KEY `atribut_kamar_kriteria_id_foreign` (`kriteria_id`),
  KEY `atribut_kamar_opsi_kriteria_id_foreign` (`opsi_kriteria_id`),
  CONSTRAINT `atribut_kamar_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atribut_kamar_kriteria_id_foreign` FOREIGN KEY (`kriteria_id`) REFERENCES `kriteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `atribut_kamar_opsi_kriteria_id_foreign` FOREIGN KEY (`opsi_kriteria_id`) REFERENCES `opsi_kriteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. Tabel kamar_favorit
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kamar_favorit`;
CREATE TABLE `kamar_favorit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `kamar_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kamar_favorit_user_id_foreign` (`user_id`),
  KEY `kamar_favorit_kamar_id_foreign` (`kamar_id`),
  CONSTRAINT `kamar_favorit_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kamar_favorit_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 14. Tabel log_kontak
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_kontak`;
CREATE TABLE `log_kontak` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `kamar_id` bigint(20) unsigned NOT NULL,
  `contact_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_kontak_user_id_foreign` (`user_id`),
  KEY `log_kontak_kamar_id_foreign` (`kamar_id`),
  CONSTRAINT `log_kontak_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `log_kontak_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 15. Tabel log_rekomendasi
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_rekomendasi`;
CREATE TABLE `log_rekomendasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `preference_summary` text NOT NULL,
  `results_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_rekomendasi_user_id_foreign` (`user_id`),
  CONSTRAINT `log_rekomendasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
