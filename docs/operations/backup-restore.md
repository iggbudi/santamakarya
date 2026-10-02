# Backup dan pemulihan

Status: satu backup MySQL + media dan restore drill lokal telah diuji. Salinan off-site, penjadwalan harian, dan pemulihan server hosting belum tersedia karena tujuan/provider belum dipilih. Backup pada komputer yang sama belum melindungi dari kehilangan komputer/disk.

## Membuat backup lokal

Hentikan pengeditan/upload selama capture. Untuk instalasi yang dipakai beberapa admin, gunakan jendela maintenance (`php artisan down`, backup, kemudian `php artisan up` bahkan bila backup gagal) supaya tabel akun/media tidak berubah selama dump. Penguncian CMS melindungi referensi publikasi dan penghapusan media; maintenance melengkapi konsistensi perubahan lain.

```powershell
.\scripts\php.ps1 artisan cms:backup --destination=.runtime/backups
```

Perintah membuat direktori unik `backup-TANGGAL-UUID` berisi:

- `database.sql`: dump MySQL dengan snapshot transaksi; mencakup akun/hash password, draft, snapshot, pointer versi, dan tabel aplikasi lainnya.
- `media.zip`: seluruh storage public lokal, termasuk gambar utama dan thumbnail.
- `manifest.json`: waktu, database sumber, pointer versi, fingerprint tabel CMS, serta SHA-256 dump, arsip, dan berkas media.

Password koneksi tidak dimasukkan ke argumen CLI atau bundle; file opsi client sementara dihapus setelah proses. Bundle **tidak berisi `.env` atau APP_KEY**. Simpan APP_KEY dan konfigurasi privat secara terpisah dalam penyimpanan rahasia yang aman; kehilangan kunci dapat merusak data/sesi terenkripsi. Backup sendiri berisi data privat dan belum dienkripsi: batasi akses, enkripsi salinan off-site, dan jangan letakkan di `public`/web root. Manifest/checksum memeriksa kerusakan, bukan autentisitas; restore hanya backup buatan sendiri dari lokasi tepercaya.

Jika client MySQL berbeda, isi `CMS_MYSQLDUMP`/`CMS_MYSQL` pada konfigurasi privat. Kegagalan tidak menghasilkan konfirmasi selesai; direktori parsial mungkin tertinggal dan tidak boleh dianggap backup valid. Backup lama tidak dihapus otomatis.

## Uji pemulihan terisolasi di Laragon

```powershell
.\scripts\php.ps1 scripts/restore-drill.php .runtime/backups/backup-TANGGAL-UUID
```

Gunakan path direktori yang dicetak oleh perintah backup. Drill hanya berjalan pada `APP_ENV=local` dan memakai credential root instance proyek yang sudah ada di `.runtime/database-credentials.json`, tanpa mencetaknya. Jangan memakai drill ini pada database produksi.

Drill membuat database baru `santama_restore_*` dan storage baru di `.runtime/restore-drills/`; tidak menimpa/drop database aktif. Root hanya membuat database dan akun sementara. Import dan verifikasi memakai akun dengan hak pada database pemulihan itu saja; akun sementara dihapus saat selesai/gagal. Checksum dan path ZIP diperiksa sebelum ekstraksi/import. Report JSON mencatat kesamaan seluruh tabel CMS, pointer publikasi, HTML publik, checksum media, slideshow aktif, portofolio, logo draft, serta hash password/hak akses akun admin lokal. Setelahnya, drill memeriksa database sumber tetap sama. Ini uji pemulihan data dan render server; login melalui HTTP pada server restore terpisah bukan bagian drill ini.

Regresi isolasi dapat dijalankan dengan `scripts/php.ps1 tests/fixtures/restore-isolation-check.php DIREKTORI_BACKUP`. Fixture membuat skema probe baru dan backup salinan dengan perintah ke skema probe tersebut; restore harus menolak perintahnya dan menjaga probe utuh. Fixture tidak menulis ke data aplikasi aktif.

Database dan direktori hasil drill dipertahankan untuk inspeksi. Bila kelak ingin membersihkan, cocokkan nama lengkap `santama_restore_*` dengan report dan pastikan bukan database aktif; tidak ada pembersihan otomatis. Jangan mengimpor dump langsung ke database yang sedang digunakan.

## Backup harian dan retensi hosting

Target awal: **backup database dan media setiap hari, simpan minimal 14 hari, pada lokasi berbeda dari server aplikasi**. Pilih storage/provider yang mendukung akses terbatas, enkripsi, riwayat versi, dan pemeriksaan hasil upload. Simpan set SQL/ZIP/manifest bersama; simpan APP_KEY terpisah. Scheduler provider menjalankan backup dalam jendela yang ditentukan, mengunggah bundle, memverifikasi checksum salinan, dan mengirim pemberitahuan hanya jika gagal. Setelah salinan terverifikasi, retensi dapat menghapus set yang lebih lama sesuai kebijakan; jangan menghapus satu-satunya backup valid.

Dokumentasikan zona waktu, jam backup, akun scheduler, lokasi off-site, siapa yang menerima kegagalan, dan cara membaca kunci. Uji restore berkala ke database/storage terpisah dengan kode/lockfile kompatibel, lalu smoke test HTTP untuk landing, media, admin/login, draft/preview/publish/restore. Verifikasi cadangan sesudah migrasi besar. RPO awal maksimal satu hari; waktu pemulihan aktual hosting belum diukur.

Untuk pemulihan instalasi aktif: aktifkan maintenance, ambil salinan kondisi sekarang, verifikasi backup, restore ke target baru, atur konfigurasi DB/storage/APP_KEY, jalankan migration yang diperlukan dan smoke test, baru alihkan aplikasi. Jangan menjadikan script drill lokal sebagai pemulihan destruktif produksi.
