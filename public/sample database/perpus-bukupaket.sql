-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.7 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for peminjaman_buku_paket
CREATE DATABASE IF NOT EXISTS `peminjaman_buku_paket` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `peminjaman_buku_paket`;

-- Dumping structure for table peminjaman_buku_paket.buku
CREATE TABLE IF NOT EXISTS `buku` (
  `id_buku` int NOT NULL AUTO_INCREMENT,
  `kode_buku` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul_buku` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mapel` int NOT NULL,
  `tingkat` enum('10','11','12') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jurusan` int NOT NULL,
  `semester` enum('ganjil','genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok_total` int NOT NULL DEFAULT '0',
  `stok_tersedia` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_buku`),
  UNIQUE KEY `kode_buku` (`kode_buku`),
  KEY `fk_buku_mapel` (`id_mapel`),
  KEY `fk_buku_jurusan` (`id_jurusan`),
  CONSTRAINT `fk_buku_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_buku_mapel` FOREIGN KEY (`id_mapel`) REFERENCES `mata_pelajaran` (`id_mapel`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_buku_stok` CHECK (((`stok_tersedia` >= 0) and (`stok_tersedia` <= `stok_total`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.detail_peminjaman
CREATE TABLE IF NOT EXISTS `detail_peminjaman` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int NOT NULL,
  `id_siswa` int NOT NULL,
  `status` enum('dipinjam','dikembalikan','rusak','hilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dipinjam',
  `tanggal_kembali` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_detail`),
  KEY `fk_detail_peminjaman` (`id_peminjaman`),
  KEY `fk_detail_siswa` (`id_siswa`),
  CONSTRAINT `fk_detail_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.jurusan
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id_jurusan` int NOT NULL AUTO_INCREMENT,
  `kode_jurusan` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jurusan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_jurusan`),
  UNIQUE KEY `kode_jurusan` (`kode_jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int NOT NULL AUTO_INCREMENT,
  `tingkat` enum('10','11','12') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jurusan` int NOT NULL,
  `id_tahun` int DEFAULT NULL,
  `nama_kelas` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `fk_kelas_jurusan` (`id_jurusan`),
  KEY `fk_id_tahun` (`id_tahun`),
  CONSTRAINT `fk_id_tahun` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_kelas_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.mata_pelajaran
CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `id_mapel` int NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_mapel`),
  UNIQUE KEY `kode_mapel` (`kode_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.peminjaman
CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id_peminjaman` int NOT NULL AUTO_INCREMENT,
  `id_buku` int NOT NULL,
  `id_kelas` int NOT NULL,
  `id_tahun` int NOT NULL,
  `semester` enum('ganjil','genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `status` enum('belum','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  PRIMARY KEY (`id_peminjaman`),
  KEY `fk_peminjaman_buku` (`id_buku`),
  KEY `fk_peminjaman_kelas` (`id_kelas`),
  KEY `fk_peminjaman_tahun` (`id_tahun`),
  CONSTRAINT `fk_peminjaman_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_peminjaman_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_peminjaman_tahun` FOREIGN KEY (`id_tahun`) REFERENCES `tahun_ajaran` (`id_tahun`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.siswa
CREATE TABLE IF NOT EXISTS `siswa` (
  `id_siswa` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_siswa` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kelas` int NOT NULL,
  `id_tahun_masuk` int NOT NULL,
  `status` enum('aktif','lulus','pindah','dropout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_siswa`),
  UNIQUE KEY `nis` (`nis`),
  KEY `fk_siswa_kelas` (`id_kelas`),
  KEY `fk_tahun_masuk` (`id_tahun_masuk`),
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_tahun_masuk` FOREIGN KEY (`id_tahun_masuk`) REFERENCES `tahun_ajaran` (`id_tahun`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.tahun_ajaran
CREATE TABLE IF NOT EXISTS `tahun_ajaran` (
  `id_tahun` int NOT NULL AUTO_INCREMENT,
  `nama_tahun` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tahun`),
  UNIQUE KEY `nama_tahun` (`nama_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table peminjaman_buku_paket.users
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
