# Sprint 1 — Catatan pengerjaan

Plan: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`

## Batas pengerjaan

Sprint 1 / Task 1 saja. Pengelolaan logo, slideshow Tentang, editor konten, preview draft, dan tombol publikasi belum termasuk sprint ini.

## Keputusan

- Ruling: Kerjakan di workspace sekarang tanpa worktree karena belum ada repository Git. Referensi `landingpage.html` dan `logo.jpeg` dipertahankan. Biaya bila salah: riwayat perubahan belum tersedia sampai Git diinisialisasi.
- Ruling: Gunakan PHP Laragon 8.3.30 melalui path khusus proyek karena PHP di PATH memiliki konfigurasi ekstensi yang rusak. Tidak mengubah instalasi PHP global. Biaya bila salah: developer perlu memilih runtime yang didokumentasikan.
- Ruling: Gunakan Laravel 12 dan Filament 5 dengan pasangan versi yang diselesaikan Composer; PHP lokal kompatibel dengan kebutuhan keduanya. Hosting belum ditentukan sehingga kompatibilitas hosting belum diverifikasi.
- Ruling: MySQL 8.4.3 lokal menggunakan data directory proyek dan port khusus 3307, bukan instance database global. Biaya bila salah: konfigurasi deployment tetap perlu disesuaikan.
- Pre-flight: Sprint 1 menghasilkan model/payload untuk Sprint 2–4; kontrak nama model dan snapshot mengikuti rencana. Tidak mengimplementasikan layanan publikasi sebelum Sprint 3.

## Progres

- [x] Runtime PHP/Node/Composer/MySQL ditemukan.
- [x] Git belum diinisialisasi; tidak ada baseline test aplikasi sebelumnya.
- [x] Screenshot acuan tiga viewport.
- [x] Scaffold dan dependency Laravel/Filament.
- [x] Tes akses admin dan isolasi draft RED → GREEN.
- [x] Schema, seed snapshot, Blade, dan build aset.
- [x] Database lokal dan akun admin.
- [x] Pengujian backend dan browser.
- [x] Review akhir dan pembaruan rencana sprint.

## Bukti verifikasi

Tanggal verifikasi: 1 Oktober 2026.

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test --filter=AdminAccessTest` sebelum wiring panel | RED: route/panel belum tersedia atau akses admin ditolak |
| `php artisan test --filter=LandingPageTest` sebelum controller snapshot | RED: 5 gagal, 1 lolos setelah seed fixture diperbaiki |
| `php artisan test --filter=CreateAdminTest` sebelum command admin | RED: command belum tersedia |
| `scripts/php.ps1 artisan test` setelah review | GREEN: 13 tes, 40 assertion |
| `npm.cmd run build` | Berhasil; CSS publik 23,91 kB, JS 340,17 kB sebelum gzip |
| `php artisan migrate --seed` pada MySQL 8.4.3 | Berhasil, migration CMS dan snapshot seed tersimpan |
| `scripts/database.ps1 Status` | `mysqld is alive` |
| `npx.cmd playwright test tests/browser/landing-baseline.spec.ts` | 6 tes lolos; 3 comparison visual dan 3 tes interaksi/akses guest |
| Login akun admin nyata melalui browser | Berhasil, dashboard `/admin` terbuka |

Screenshot HTML asli: `artifacts/browser/baseline/{390,768,1440}.png`. Hasil migrasi: `artifacts/browser/migrated/{390,768,1440}.png`. Dashboard login: `artifacts/browser/admin-dashboard.png`. Log pengujian dan credential berada dalam `.runtime/`, diabaikan Git.

Comparison visual menggunakan HTML asli dengan penghapusan kartu/filter yang diminta saja. Snapshot Maps dimask, interval autoplay dibekukan pada kedua halaman, dan foto eksternal memakai respons cache yang sama. Toleransi maksimum pixel diff 1%; ketiga viewport lolos. Font/CDN referensi masih memerlukan jaringan.

## Review independen dan penyelesaian

- Review independen tidak menemukan kebocoran draft atau celah akses admin pada lingkup Sprint 1.
- Important: tes scaffold `ExampleTest` meminta halaman tanpa menyiapkan database. Full suite mengonfirmasi 1 gagal/14 lolos; tes contoh redundan dihapus karena kontrak landing sudah dicakup `LandingPageTest`. Full suite final: 13/13 lolos.
- Minor: klik dot pertama pada screenshot memulai ulang timer. Interval sekarang dibekukan pada kedua halaman selama comparison; browser suite final: 6/6 lolos.
- Tidak ada temuan review yang ditunda.

## Keputusan tambahan dan batas aktual

- Ruling: Menambah kolom nullable `image_url` pada slide/proyek dan `caption` pada proyek untuk mengakomodasi URL contoh dari HTML tanpa menjadikannya upload media. Upload baru pada Sprint 2 tetap wajib memakai pustaka media. Biaya bila salah: migrasi normalisasi sumber foto perlu ditambah.
- Ruling: Akun development memakai identitas sementara `Santama Admin` / `admin@santamakarya.test` karena identitas belum ditentukan. Password acak dibuat melalui command interaktif dan disimpan di `.runtime/admin-login.txt`. Biaya bila salah: akun perlu diganti melalui command lokal.
- Ruling: Browser test memakai Chrome yang sudah terpasang, karena versi browser Playwright package terbaru belum tersedia. Biaya bila salah: mesin lain harus memasang Chrome atau Chromium Playwright sesuai panduan README.
- File referensi awal tetap dipertahankan. Tidak ada commit karena repository Git belum tersedia.
- Foto Tentang dari HTML awal `photo-1541888946425-d0fbb186a5b3` dikonfirmasi HTTP 404. Source tetap dipertahankan pada Sprint 1 agar tidak mengganti konten di luar migrasi; ganti foto melalui pekerjaan slideshow Sprint 2. Dalam comparison, gambar remote yang gagal menggunakan placeholder konsisten pada kedua halaman.
- Target hosting belum ditentukan, sehingga verifikasi kompatibilitas hosting/deployment belum dilakukan. Aplikasi berjalan lokal.
- Dashboard admin saat ini hanya fondasi login/akun. Editor konten, logo resmi, slideshow Tentang, dan publikasi UI belum diimplementasikan.
