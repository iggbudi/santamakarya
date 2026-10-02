# Menjalankan kandidat rilis dan deployment

Status: kandidat rilis lokal pada Laragon; hosting/domain belum dipilih. Tidak ada deployment staging/produksi atau HTTPS hosting yang sudah diverifikasi.

## Lokal pada komputer ini

Workspace: `C:/dev/santamakarya`. Helper memilih PHP 8.3.30 dan MySQL 8.4.3 Laragon. Database proyek memakai port **3307**, terpisah dari database Laragon standar. Hindari mengganti konfigurasi database aktif atau melakukan seed ulang untuk memindahkan server.

```powershell
.\scripts\database.ps1 Start
.\scripts\database.ps1 Status
.\scripts\php.ps1 artisan storage:link
npm.cmd run build
.\scripts\serve.ps1
```

Buka `http://127.0.0.1:8000/` dan `/admin`. Credential lokal berada pada `.runtime/admin-login.txt`; jangan menyalinnya ke dokumentasi/publikasi. `serve.ps1` memakai runtime Laragon, tanpa perubahan konfigurasi global. Helper PHP menambahkan konfigurasi zip khusus proses di `.runtime/php-conf` agar subprocess `artisan test` menerima ekstensi yang sama.

Jika ingin memakai Apache/Nginx Laragon melalui virtual host, arahkan **DocumentRoot ke `C:/dev/santamakarya/public`**, bukan root proyek. Gunakan PHP/ekstensi yang sama, izinkan aturan routing Laravel, dan sesuaikan `APP_URL` dengan URL virtual host. Virtual host ini belum dikonfigurasi atau diuji; server port 8000 adalah jalur lokal yang sudah diuji. Setelah mengubah URL, periksa juga URL media yang tersimpan pada snapshot lama melalui preview/publikasi baru.

## Konfigurasi saat hosting dipilih

Kebutuhan: PHP 8.3 kompatibel dengan lockfile beserta `intl`, `mbstring`, `pdo_mysql`, `gd`, `zip`, `exif`, dan ekstensi framework; MySQL; Composer; Node untuk build atau artefak build dari mesin rilis; storage persisten; scheduler/backup; akses konfigurasi web root `/public`; sertifikat HTTPS. Cocokkan versi provider sebelum deployment.

Contoh nilai produksi (isi credential hanya melalui konfigurasi privat provider):

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-yang-dipilih
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
LOG_LEVEL=warning
```

Simpan `APP_KEY` unik dengan aman dan pertahankan saat upgrade/restore. Jangan menjalankan `key:generate` pada instalasi aktif. Akun produksi dibuat terpisah melalui `php artisan admin:create`; tidak ada akun default produksi. Jika TLS berhenti pada reverse proxy, konfigurasikan hanya proxy yang tepercaya sesuai provider dan periksa cookie/URL yang dihasilkan. Jangan mengaktifkan secure cookie pada server lokal HTTP karena sesi login tidak akan terkirim.

Snapshot historis mempertahankan URL media saat publikasi. Sebelum pindah dari localhost ke domain hosting, periksa URL seluruh media, gunakan APP_URL final, lalu preview/publikasikan draft pada URL final. Jangan memulihkan snapshot development ber-URL localhost pada produksi sebelum strategi migrasi snapshot/media diuji. Penanganan perpindahan domain dan riwayat lintas domain belum diverifikasi pada kandidat lokal ini.

Urutan deployment yang perlu dijalankan pada staging dahulu:

1. Backup database dan media; catat commit/artefak kode serta nomor versi konten aktif. Letakkan kode di direktori rilis baru dan hubungkan storage persisten serta konfigurasi privat.
2. Instal dependency sesuai lockfile: `composer install --no-dev --prefer-dist --optimize-autoloader`, `npm ci`, `npm run build`; atau gunakan build identik yang sudah diuji. Jangan upload `node_modules`, `.runtime`, `.env` lokal, backup, dan credential.
3. Dalam jendela maintenance, jalankan `php artisan migrate --force`, `php artisan filament:assets`, dan `php artisan storage:link`. Jangan menjalankan seed pada database produksi yang sudah berisi konten.
4. Jalankan `php artisan optimize`; pastikan `storage` dan `bootstrap/cache` dapat ditulis akun web server. Aktifkan web root `/public` dan HTTPS. Uji konfigurasi provider sebelum mengalihkan trafik.
5. Lakukan smoke test: `/` dan `/up`, gambar/logo, login/logout, preview berbanner, save draft tanpa perubahan publik, publikasi dan pemulihan konten uji. Pastikan header `X-Robots-Tag: noindex, nofollow` dan `Cache-Control: no-store, private` pada admin/preview. Login memakai throttling Filament bawaan (lima percobaan per jendela limiter); suite lokal menguji penolakan percobaan berulang.
6. Konfigurasikan backup harian/off-site, monitoring kegagalan, serta purge cache/CDN jika provider menambahkan cache. Jangan cache admin/preview. Jalankan UAT dan catat persetujuan sebelum produksi.

## Rollback

Untuk kode, alihkan ke direktori/artefak rilis sebelumnya yang sudah disimpan, gunakan konfigurasi/storage persisten yang sama, lalu bangun ulang cache dan lakukan smoke test. Jangan otomatis melakukan `migrate:rollback`: perubahan skema dapat merusak data; gunakan strategi migrasi kompatibel atau restore backup dalam maintenance setelah menilai perubahan sejak backup.

Untuk konten, **Publikasi & Riwayat → Pulihkan** membuat snapshot baru tanpa menimpa draft. Untuk kehilangan database/media, ikuti [backup dan pemulihan](backup-restore.md), pertahankan APP_KEY, dan uji di lingkungan terpisah sebelum menyentuh instalasi aktif.
