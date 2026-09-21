# Sistem Absensi QR — Rebuild

Versi ini dibuat ulang dengan satu bahasa visual, struktur PHP Native + MySQL, QR personal mahasiswa, QR sesi dosen, validasi GPS, radius lokasi, serta dashboard terpisah untuk mahasiswa, dosen, dan admin.

## Struktur
- `index.php` — landing + pintu scanner
- `login.php` — login tiga role
- `mahasiswa/` — dashboard, scanner, kartu QR, riwayat, izin/sakit, profil
- `dosen/` — dashboard, pembuat sesi/QR, absensi kelas, laporan, pengajuan
- `admin/` — dashboard dan master data
- `api/` — endpoint lokasi, sesi, identifikasi QR, penyimpanan absensi
- `config/` — database, auth, helper
- `assets/` — CSS dan JS bersama
- `database/database.sql` — database baru + 30 data mahasiswa contoh

## Instalasi Laragon/XAMPP
1. Ekstrak folder `absen_qr` ke `C:\laragon\www\absen_qr` atau `htdocs`.
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin lalu import `database/database.sql`.
4. Pastikan `config/database.php` sesuai username/password MySQL.
5. Buka `http://localhost/absen_qr/`.

## Akun demo
- Admin: `admin` / `123456`
- Dosen: `NIDN-JS-001` / `123456`
- Mahasiswa: `24105111049` / `123456`

Semua password demo memakai `password_hash` dan diverifikasi dengan `password_verify`.

## Alur
1. Admin mengatur mahasiswa, dosen, mata kuliah, kelas, jadwal, dan lokasi.
2. Dosen membuat sesi. Sistem membuat QR sesi unik.
3. Mahasiswa membuka scanner, memakai kamera atau gambar dari galeri.
4. QR personal mahasiswa dapat dibaca untuk identifikasi. QR sesi juga dapat dibaca untuk memilih sesi.
5. Mahasiswa memilih lokasi aktif dan mengirim GPS.
6. Server memeriksa identitas, kelas, tanggal, jam sesi, lokasi, dan radius.
7. Catatan masuk ke tabel `absensi` dan langsung terlihat di halaman dosen.

## Catatan penting
- Kolom `face_verified_at` disiapkan untuk pengembangan verifikasi wajah sekali. Versi rebuild ini sengaja tidak berpura-pura melakukan face matching; integrasi biometrik perlu model/libraries dan kebijakan privasi yang jelas.

- Browser harus mengizinkan kamera dan lokasi. Pada banyak browser, kamera/GPS paling aman melalui HTTPS atau localhost.
- Koordinat awal hanya data contoh. Ganti melalui menu Admin → Lokasi sesuai titik kampus yang benar.
- QR personal hanya identitas mahasiswa. Untuk alur kelas yang lebih ketat, gunakan QR sesi dosen sebagai QR yang dibagikan ke grup.
- Library scanner dan generator QR dipanggil dari CDN. Jika komputer tanpa internet, simpan library tersebut lokal di `assets/vendor/`.
# absensiqr
