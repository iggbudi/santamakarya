# Santama Karya CMS

Landing page CV Santama Karya Indonesia menggunakan Laravel dan Filament. Sprint 1 menyediakan fondasi aplikasi, login admin, database CMS, snapshot konten publik, serta migrasi visual dari HTML referensi.

## Status

- Halaman `/` membaca snapshot yang dipilih melalui `site_settings.published_version_id`; draft tidak dibaca halaman publik.
- Admin `/admin` hanya menerima akun dengan `is_admin = true`; tidak ada registrasi publik.
- Portofolio Rumah Dinas & Fasilitas serta filter Lainnya dihapus. Layanan Rumah Dinas tetap ada.
- Logo, slideshow Hero/Tentang, portofolio, dan media dapat dikelola sebagai draft.
- Preview, publikasi, dan pemulihan snapshot tersedia melalui Publikasi & Riwayat.
- Sprint 4 menyediakan editor konten terstruktur, Kontak & Maps, SEO & Open Graph, dasbor statistik draft, serta Profil & Password.
- Sprint 5 menyiapkan kandidat rilis lokal, backup MySQL/media dan restore drill terisolasi, serta panduan deployment dan checklist UAT.
- `landingpage.html` dan `logo.jpeg` dipertahankan; repository Git belum diinisialisasi.

## Menjalankan workspace Windows yang sudah disiapkan

```powershell
# Terminal database: instance proyek pada port 3307
.\scripts\database.ps1 Start
.\scripts\database.ps1 Status

# Terminal web server: port 8000
.\scripts\serve.ps1
```

Landing: `http://127.0.0.1:8000/`. Admin: `http://127.0.0.1:8000/admin`.

Credential akun development berada di `.runtime/admin-login.txt` dan diabaikan Git. Password dibuat acak. Buat akun produksi tersendiri; jangan menyalin credential development.

Helper Windows memilih PHP Laragon yang diketahui berfungsi, atau PHP pada PATH bila Laragon tidak ada. Tidak ada perubahan PATH/config PHP global. Detail runtime ada di `docs/operations/runtime.md`.

## Setup pada mesin lain

Siapkan PHP 8.3 dengan ekstensi Laravel/Filament termasuk `intl`, `mbstring`, `pdo_mysql`, `pdo_sqlite`, `gd`, dan `zip`; Composer; Node; serta MySQL. Buat database khusus dan sesuaikan `.env`.

```powershell
composer install
Copy-Item .env.example .env
# Isi DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD untuk mesin ini.
php artisan key:generate
php artisan migrate --seed
php artisan filament:assets
npm.cmd ci
npm.cmd run build
php artisan admin:create
php artisan serve --host=127.0.0.1 --port=8000
```

`admin:create` meminta nama, email, dan password tersembunyi dengan panjang minimum 12 karakter. Tidak ada akun/password produksi default dalam seed. Seed ulang tidak menimpa draft/publikasi yang sudah ada.

Untuk runtime Laragon workspace ini, gunakan wrapper yang mengaktifkan zip per proses:

```powershell
.\scripts\php.ps1 artisan admin:create
.\scripts\php.ps1 artisan test
```

## Build dan pengujian

```powershell
.\scripts\php.ps1 artisan test
npm.cmd run build
npm.cmd run test:browser
```

Backend test menggunakan SQLite in-memory, bukan database development. Migration dan seed juga diverifikasi pada MySQL lokal. Browser test menggunakan Chrome yang terpasang; untuk Chromium Playwright, jalankan `npx.cmd playwright install chromium`, lalu atur `$env:PLAYWRIGHT_CHANNEL = 'chromium'`.

Tes visual membandingkan aplikasi dengan HTML awal pada 390x844, 768x1024, dan 1440x900. Referensi comparison hanya menghapus kartu/filter sesuai revisi. Autoplay dibekukan, iframe Maps dimask, dan foto eksternal dibagikan melalui cache test. Tailwind/Lucide acuan menggunakan paket lokal; font/foto eksternal masih membutuhkan jaringan. Screenshot tersimpan di `artifacts/browser/` dan laporan di `playwright-report/`; keduanya diabaikan Git.

Sprint 2 menambahkan media, identitas, slideshow Hero/Tentang, proyek, dan kategori di admin. Jalankan `scripts/php.ps1 artisan storage:link` dan `scripts/php.ps1 artisan cms:prepare-brand` setelah migration/seed untuk aset logo draft. Semua edit tersimpan sebagai draft sampai dipublikasikan melalui halaman Publikasi & Riwayat (Sprint 3). Preview admin tersedia di `/admin/preview`. Tes browser editor/upload memakai akun lokal di `.runtime/admin-account.json`; bila akun belum ada, tes editor/upload/publikasi yang memerlukannya dilewati. Tes draft galeri merender fixture lokal melalui `tests/fixtures/render-draft.php` dan intercept browser saja, tanpa route preview produksi.

## Dokumen

- Rencana sprint: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`
- Progres dan bukti: `docs/operations/sprint-1-progress.md`
- Runtime: `docs/operations/runtime.md`
- Progres Sprint 2: `docs/operations/sprint-2-progress.md`
- Panduan admin Sprint 2: `docs/operations/admin-guide-sprint-2.md`
- Progres Sprint 3: `docs/operations/sprint-3-progress.md`
- Panduan preview/publikasi/pemulihan: `docs/operations/admin-guide-sprint-3.md`
- Panduan admin lengkap: `docs/operations/admin-guide.md`
- Progres Sprint 4: `docs/operations/sprint-4-progress.md`
- Progres Sprint 5: `docs/operations/sprint-5-progress.md`
- Menjalankan lokal/deployment: `docs/operations/deployment.md`
- Backup dan restore drill: `docs/operations/backup-restore.md`
- Checklist rilis/UAT: `docs/operations/release-checklist.md`

Target hosting belum ditentukan; aplikasi sementara berjalan lokal menggunakan runtime Laragon. Deployment hosting, backup off-site harian, dan UAT/persetujuan produksi belum dilakukan. Backup lokal dan restore drill tersedia; jangan menyamakan backup pada komputer yang sama dengan salinan off-site.

