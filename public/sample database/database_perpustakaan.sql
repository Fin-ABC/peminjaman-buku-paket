-- ============================================
-- SQL Script untuk Database Perpustakaan Sekolah
-- ============================================

-- Hapus tabel jika sudah ada (urutan terbalik dari pembuatan)
DROP TABLE IF EXISTS detail_peminjaman;
DROP TABLE IF EXISTS peminjaman;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS mata_pelajaran;
DROP TABLE IF EXISTS siswa;
DROP TABLE IF EXISTS kelas;
DROP TABLE IF EXISTS jurusan;
DROP TABLE IF EXISTS tahun_ajaran;
DROP TABLE IF EXISTS akun;

-- ============================================
-- Tabel: akun
-- ============================================
CREATE TABLE akun (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: tahun_ajaran
-- ============================================
CREATE TABLE tahun_ajaran (
    id_tahun INT AUTO_INCREMENT PRIMARY KEY,
    nama_tahun VARCHAR(255) NOT NULL COMMENT '2025/2026',
    aktif BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: jurusan
-- ============================================
CREATE TABLE jurusan (
    id_jurusan INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(255) NOT NULL COMMENT 'RPL',
    nama_jurusan VARCHAR(255) NOT NULL COMMENT 'Rekayasa Perangkat Lunak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: kelas
-- ============================================
CREATE TABLE kelas (
    id_kelas INT AUTO_INCREMENT PRIMARY KEY,
    tingkat ENUM('10', '11', '12') NOT NULL,
    id_jurusan INT NOT NULL,
    id_tahun INT NOT NULL,
    nama_kelas VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_jurusan) REFERENCES jurusan(id_jurusan) ON DELETE CASCADE,
    FOREIGN KEY (id_tahun) REFERENCES tahun_ajaran(id_tahun) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: siswa
-- ============================================
CREATE TABLE siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(255) UNIQUE NOT NULL,
    nama_siswa VARCHAR(255) NOT NULL,
    id_kelas INT NOT NULL,
    id_tahun_masuk INT NOT NULL,
    status ENUM('aktif', 'lulus', 'pindah', 'dropout') DEFAULT 'aktif',
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE,
    FOREIGN KEY (id_tahun_masuk) REFERENCES tahun_ajaran(id_tahun) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: mata_pelajaran
-- ============================================
CREATE TABLE mata_pelajaran (
    id_mapel INT AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(255) NOT NULL,
    nama_mapel VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: buku
-- ============================================
CREATE TABLE buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(255) UNIQUE NOT NULL,
    judul_buku VARCHAR(255) NOT NULL,
    id_mapel INT NOT NULL,
    tingkat ENUM('10', '11', '12') NOT NULL,
    id_jurusan INT NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    stok_total INT DEFAULT 0,
    stok_tersedia INT DEFAULT 0,
    FOREIGN KEY (id_mapel) REFERENCES mata_pelajaran(id_mapel) ON DELETE CASCADE,
    FOREIGN KEY (id_jurusan) REFERENCES jurusan(id_jurusan) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: peminjaman
-- ============================================
CREATE TABLE peminjaman (
    id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
    id_buku INT NOT NULL,
    id_kelas INT NOT NULL,
    id_tahun INT NOT NULL,
    semester ENUM('ganjil', 'genap') NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    status ENUM('belum', 'selesai') DEFAULT 'belum',
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE,
    FOREIGN KEY (id_tahun) REFERENCES tahun_ajaran(id_tahun) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: detail_peminjaman
-- ============================================
CREATE TABLE detail_peminjaman (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_peminjaman INT NOT NULL,
    id_siswa INT NOT NULL,
    status ENUM('dipinjam', 'dikembalikan', 'rusak', 'hilang') DEFAULT 'dipinjam',
    tanggal_kembali DATE NULL,
    catatan TEXT NULL,
    FOREIGN KEY (id_peminjaman) REFERENCES peminjaman(id_peminjaman) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Index tambahan untuk optimasi query
-- ============================================
CREATE INDEX idx_kelas_jurusan ON kelas(id_jurusan);
CREATE INDEX idx_kelas_tahun ON kelas(id_tahun);
CREATE INDEX idx_siswa_kelas ON siswa(id_kelas);
CREATE INDEX idx_siswa_tahun_masuk ON siswa(id_tahun_masuk);
CREATE INDEX idx_siswa_status ON siswa(status);
CREATE INDEX idx_buku_mapel ON buku(id_mapel);
CREATE INDEX idx_buku_jurusan ON buku(id_jurusan);
CREATE INDEX idx_peminjaman_buku ON peminjaman(id_buku);
CREATE INDEX idx_peminjaman_kelas ON peminjaman(id_kelas);
CREATE INDEX idx_peminjaman_tahun ON peminjaman(id_tahun);
CREATE INDEX idx_peminjaman_status ON peminjaman(status);
CREATE INDEX idx_detail_peminjaman ON detail_peminjaman(id_peminjaman);
CREATE INDEX idx_detail_siswa ON detail_peminjaman(id_siswa);
CREATE INDEX idx_detail_status ON detail_peminjaman(status);

-- ============================================
-- Data sample (opsional)
-- ============================================

-- Insert sample tahun ajaran
INSERT INTO tahun_ajaran (nama_tahun, aktif) VALUES 
('2024/2025', TRUE),
('2025/2026', FALSE);

-- Insert sample jurusan
INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES 
('RPL', 'Rekayasa Perangkat Lunak'),
('TKJ', 'Teknik Komputer dan Jaringan'),
('MM', 'Multimedia'),
('TJKT', 'Teknik Jaringan Komputer dan Telekomunikasi');

-- Insert sample mata pelajaran
INSERT INTO mata_pelajaran (kode_mapel, nama_mapel) VALUES 
('MTK', 'Matematika'),
('BIN', 'Bahasa Indonesia'),
('BING', 'Bahasa Inggris'),
('PPKN', 'Pendidikan Pancasila dan Kewarganegaraan'),
('PWPB', 'Pemrograman Web dan Perangkat Bergerak'),
('PPB', 'Pemrograman Berorientasi Objek'),
('BD', 'Basis Data');

-- Insert sample akun admin
INSERT INTO akun (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password di atas adalah hash dari 'password', ganti dengan hash yang sebenarnya

-- ============================================
-- END OF SQL SCRIPT
-- ============================================
