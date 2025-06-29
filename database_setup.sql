-- ====================================================================
-- DIGITAL SIGNATURE SYSTEM - DATABASE SETUP
-- ====================================================================

-- Buat database baru
CREATE DATABASE IF NOT EXISTS digital_signature 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

-- Gunakan database
USE digital_signature;

-- --------------------------------------------------------
-- Tabel: documents
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `filename` varchar(255) NOT NULL COMMENT 'Nama file yang disimpan di server',
    `original_name` varchar(255) NOT NULL COMMENT 'Nama file asli dari user',
    `original_path` varchar(500) NOT NULL COMMENT 'Path file asli',
    `signed_path` varchar(500) DEFAULT NULL COMMENT 'Path file yang sudah ditandatangani',
    `file_size` int(11) NOT NULL COMMENT 'Ukuran file dalam bytes',
    `is_signed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Status tanda tangan (0=belum, 1=sudah)',
    `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal upload',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_is_signed` (`is_signed`),
    KEY `idx_upload_date` (`upload_date`),
    KEY `idx_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabel: signatures
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `signatures` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `document_id` int(11) UNSIGNED NOT NULL COMMENT 'ID dokumen yang ditandatangani',
    `signer_name` varchar(255) NOT NULL COMMENT 'Nama penandatangan',
    `signer_position` varchar(255) NOT NULL COMMENT 'Posisi/jabatan penandatangan',
    `signer_email` varchar(255) NOT NULL COMMENT 'Email penandatangan',
    `verification_code` varchar(100) NOT NULL UNIQUE COMMENT 'Kode verifikasi unik',
    `qr_code_path` varchar(500) NOT NULL COMMENT 'Path file QR code',
    `signature_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal tanda tangan',
    `ip_address` varchar(45) NOT NULL COMMENT 'IP address penandatangan',
    `user_agent` text NOT NULL COMMENT 'User agent browser',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_verification_code` (`verification_code`),
    KEY `idx_document_id` (`document_id`),
    KEY `idx_signer_email` (`signer_email`),
    KEY `idx_signature_date` (`signature_date`),
    CONSTRAINT `fk_signatures_document_id` 
        FOREIGN KEY (`document_id`) 
        REFERENCES `documents` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data untuk testing
INSERT INTO `documents` (`filename`, `original_name`, `original_path`, `file_size`, `is_signed`, `upload_date`) VALUES
('sample_contract_001.pdf', 'Kontrak Kerja - Sample.pdf', 'writable/uploads/documents/sample_contract_001.pdf', 245760, 0, NOW()),
('sample_agreement_002.pdf', 'Perjanjian Kerjasama - Sample.pdf', 'writable/uploads/documents/sample_agreement_002.pdf', 189440, 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Insert sample signature
INSERT INTO `signatures` (`document_id`, `signer_name`, `signer_position`, `signer_email`, `verification_code`, `qr_code_path`, `signature_date`, `ip_address`, `user_agent`) VALUES
(2, 'Ahmad Rizki', 'Manager IT', 'ahmad.rizki@company.com', 'ABCD1234EFGH5678', 'writable/uploads/qrcodes/qr_ABCD1234EFGH5678.png', DATE_SUB(NOW(), INTERVAL 1 DAY), '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

-- Update dokumen yang sudah ditandatangani
UPDATE `documents` SET 
    `signed_path` = 'writable/uploads/signed/signed_sample_agreement_002.pdf',
    `is_signed` = 1 
WHERE `id` = 2;