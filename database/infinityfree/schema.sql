-- Skema untuk hosting yang menonaktifkan stored procedure, trigger, dan foreign key
-- (InfinityFree). Diimpor lewat phpMyAdmin karena server tidak punya `php artisan migrate`.
--
-- Sama dengan hasil migration di database/migrations, dengan dua perbedaan:
--   1. Tanpa FOREIGN KEY. Kepemilikan dijaga kode (EbookService); riwayat status
--      dihapus manual saat ebook dihapus (EbookService::destroy).
--   2. Tanpa procedure, trigger, dan function. Gantinya: EBOOK_SQL_OBJECTS=false di .env.
--
-- Index unik (user_id, file_hash) tetap ada: itu pengaman terakhir pencegah duplikat
-- (dokumen 03 bagian 12) dan bukan foreign key, jadi seharusnya diizinkan.

SET NAMES utf8mb4;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ebooks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_hash` char(64) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ebooks_user_id_index` (`user_id`),
  UNIQUE KEY `ebooks_user_id_file_hash_unique` (`user_id`, `file_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ebook_status_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ebook_id` bigint unsigned NOT NULL,
  `old_status` tinyint(1) NOT NULL,
  `new_status` tinyint(1) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ebook_status_history_ebook_id_index` (`ebook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dipakai Laravel Sanctum (login API untuk Android). Boleh dilewati bila hanya Web.
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
