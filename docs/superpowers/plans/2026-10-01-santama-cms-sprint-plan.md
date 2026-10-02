# Santama Karya CMS — Implementation Plan per Sprint

> **For agentic workers:** REQUIRED SUB-SKILL: Gunakan `superpowers:executing-plans` untuk implementasi langsung, atau `superpowers:subagent-driven-development` jika pengguna memilih eksekusi dengan subagent. Kerjakan task berurutan dan gunakan checkbox untuk melacak hasil. Dokumen ini adalah rencana, bukan perintah memulai implementasi.

**Goal:** Membuat landing page Santama Karya dengan konten yang bisa dikelola melalui admin, menerapkan tiga revisi yang diminta, dan mempertahankan desain landing page.

**Architecture:** Satu aplikasi Laravel melayani landing page melalui Blade dan admin melalui Filament di `/admin`. Konten yang diedit disimpan sebagai draft di MySQL; halaman publik membaca snapshot publikasi sehingga perubahan yang belum dipublikasikan tidak bocor. Foto disimpan di storage persisten dan direferensikan oleh konten.

**Tech Stack:** Laravel, Filament, MySQL, Blade, Tailwind CSS, Vite, JavaScript, PHPUnit, dan Playwright untuk pemeriksaan browser.

**Spec:** Bagian [Spesifikasi yang menjadi acuan](#spesifikasi-yang-menjadi-acuan) dalam dokumen ini, berdasarkan usulan yang disetujui pengguna pada 1 Oktober 2026.

## Spesifikasi yang menjadi acuan

### Kondisi awal

- Workspace saat ditinjau hanya berisi `landingpage.html` dan `logo.jpeg`; belum ada aplikasi, database, atau panel admin.
- HTML menggunakan Tailwind CDN, Lucide CDN, Google Fonts, gambar Unsplash, dan JavaScript langsung di halaman.
- Hero memiliki empat slide dengan interval 5.000 ms; bagian Tentang memiliki satu gambar dengan tinggi 440 px.
- Portofolio memiliki enam kartu; kartu `Rumah Dinas & Fasilitas` adalah satu-satunya item kategori `lainnya`.
- Bagian Layanan memiliki kartu `Rumah Dinas` yang berbeda dari kartu portofolio tersebut.
- Logo pada navbar, lokasi, dan footer berupa SVG di HTML; `logo.jpeg` belum digunakan.
- Kontak menggunakan WhatsApp, telepon, Google Maps, dan alamat; tidak ada formulir calon pelanggan.

### Hasil yang dituju

1. Hapus kartu portofolio `Rumah Dinas & Fasilitas`. Filter kategori hanya muncul jika memiliki proyek yang dipublikasikan.
2. Ubah foto Tentang menjadi slideshow yang bisa dikelola dari admin, dalam bingkai yang sekarang.
3. Gunakan logo perusahaan yang sesuai referensi, dapat diganti dari admin, dan cocok untuk latar terang maupun gelap.
4. Admin dapat mengelola hero, portofolio, identitas, kontak, konten section, media, serta SEO dasar.
5. Admin dapat menyimpan draft, melihat preview, dan memublikasikan perubahan secara eksplisit.
6. Situs produksi memiliki backup database dan gambar serta prosedur pemulihan yang teruji.

### Global Constraints

- Layout, warna, font, breakpoint, bentuk kartu, overlay, dan gaya landing page mengikuti `landingpage.html`.
- Perubahan tampilan hanya mencakup penghapusan kartu/filter kosong, slideshow Tentang, serta penggunaan logo resmi.
- Layanan `Rumah Dinas` tetap ada; permintaan penghapusan hanya berlaku pada portofolio.
- Admin mengedit konten melalui formulir terstruktur; tidak tersedia editor HTML, pengaturan CSS, atau page builder bebas.
- Hero tetap memiliki interval awal 5.000 ms; slideshow Tentang menggunakan interval awal 5.000 ms juga.
- Interval slideshow dapat diatur antara 3.000–10.000 ms; maksimum 10 slide aktif per lokasi.
- Satu slide aktif tampil sebagai gambar biasa; nol slide aktif ditolak ketika publikasi.
- Warna logo resmi tidak diubah hanya untuk menyamakan warna aksen website.
- Logo transparan/varian gelap dibuat dari aset resmi; hasilnya perlu pemeriksaan visual sebelum digunakan.
- Upload foto menerima JPEG, PNG, atau WebP, maksimum 5 MB per berkas; SVG upload tidak didukung pada tahap awal.
- Gunakan satu peran admin pada versi pertama, tanpa registrasi publik. Pengelolaan banyak peran di luar lingkup.
- Semua tautan WhatsApp, telepon, alamat, dan Maps berasal dari pengaturan terpusat.
- Tidak ada formulir konsultasi, CRM, blog, testimonial baru, pembayaran, atau integrasi pesan pada versi pertama.
- Versi Laravel/PHP/Filament dipilih berdasarkan dokumentasi resmi dan kemampuan hosting sebelum scaffolding; catat dan kunci dalam lockfile.
- Build CSS landing page dipisahkan dari tema admin. Hindari upgrade mayor Tailwind landing page yang mengubah tampilannya selama migrasi.
- Database, storage, dan backup berada pada layanan persisten; jangan menyimpan upload hanya pada filesystem deployment sementara.

### Review Focus

1. Slide kosong atau tinggal satu: publikasi tidak boleh menghasilkan area kosong atau error timer. Diuji pada Task 3 dan 5.
2. Perubahan draft dan preview: pengunjung biasa hanya melihat publikasi terakhir. Diuji pada Task 1 dan 5.
3. Gambar yang sedang digunakan: penghapusan media tidak boleh merusak draft atau snapshot publikasi. Diuji pada Task 2 dan 5.
4. Kategori kosong setelah proyek dihapus/dinonaktifkan: filter kosong tidak muncul. Diuji pada Task 4.
5. Teks panjang dan foto dengan rasio berbeda: layout tetap terbaca pada mobile dan desktop. Diuji pada Task 6 dan 8.

## Pembagian sprint dan estimasi

Estimasi berikut adalah perkiraan awal untuk satu developer, bukan komitmen tanggal. Satu hari berarti hari kerja efektif. Estimasi mencakup implementasi dan verifikasi, tetapi tidak mencakup menunggu aset, akses hosting, atau feedback pengguna.

| Sprint | Fokus | Estimasi | Hasil yang bisa diperiksa |
|---|---|---|---|
| 1 | Fondasi dan migrasi landing page | 3–4 hari | Laravel berjalan, login admin, landing page tetap menyerupai acuan |
| 2 | Revisi utama dan pengelolaan konten prioritas | 4–5 hari | Logo, slideshow, dan portofolio bisa diedit sebagai draft |
| 3 | Preview dan publikasi | 2–3 hari | Draft aman, preview admin, publikasi dan pemulihan versi |
| 4 | Konten lainnya, kontak, SEO, dashboard | 3–4 hari | Seluruh konten yang disepakati bisa dikelola |
| 5 | Pengujian menyeluruh dan persiapan produksi | 3–4 hari | Kandidat rilis tervalidasi, backup/pemulihan teruji |

**Total perkiraan:** 15–20 hari kerja efektif. Sprint 1–3 membentuk MVP fungsional untuk tiga revisi utama. Sprint 4–5 melengkapi pengelolaan dan kesiapan produksi.

Semua sprint berurutan. Task dalam sprint dapat ditinjau sendiri, tetapi jangan memulai task yang bergantung pada kontrak yang belum selesai.

## Peta file dan kontrak data

Path di bawah relatif terhadap root aplikasi `C:/dev/santamakarya`. Nama direktori resource Filament mengikuti generator versi yang dipilih; sesuaikan path hasil generator di rencana ini sebelum mengerjakan resource terkait.

| File/direktori | Tanggung jawab |
|---|---|
| `landingpage.html`, `logo.jpeg` | Referensi awal; jangan ditimpa |
| `resources/views/landing.blade.php` | Susunan section landing page |
| `resources/views/landing/*.blade.php` | Partial navbar, hero, tentang, layanan, keunggulan, alur, portfolio, kontak, lokasi, footer |
| `resources/css/landing.css` | Style dan token visual publik |
| `resources/js/landing.js` | Menu mobile, filter portofolio, inisialisasi slider |
| `resources/js/slideshow.js` | Slider yang bekerja per container, tanpa state global bersama |
| `app/Http/Controllers/LandingPageController.php` | Halaman publik dari snapshot terakhir |
| `app/Http/Controllers/PreviewPageController.php` | Preview draft khusus admin |
| `app/Models/{SiteSetting,MediaAsset,Slide,PortfolioCategory,PortfolioProject,PageVersion}.php` | Data draft, media, dan versi publikasi |
| `app/Services/CmsDraftService.php` | Menghasilkan payload draft untuk preview/publikasi |
| `app/Services/PublicationService.php` | Validasi, publikasi atomik, dan pemulihan versi |
| `app/Services/MediaService.php` | Upload, optimasi, pengecekan penggunaan, dan penghapusan media |
| `app/Providers/Filament/AdminPanelProvider.php` | Konfigurasi panel admin |
| `app/Filament/Pages/*.php` | Form pengaturan, slideshow, konten section, dashboard, dan publikasi |
| `app/Filament/Resources/*` | Tabel dan form portofolio, kategori, serta media |
| `database/migrations/*`, `database/seeders/*` | Skema dan migrasi konten awal |
| `tests/Feature/Cms/*`, `tests/Feature/Public/*` | Pengujian akses, konten, upload, dan publikasi |
| `tests/browser/*` | Pemeriksaan visual dan interaksi browser |
| `docs/operations/*.md` | Panduan admin, deployment, backup, dan pemulihan |

### Model dan payload

- `SiteSetting`: singleton dengan `draft_payload` JSON dan `published_version_id` nullable. Draft mencakup `identity`, `hero`, `about`, `services`, `advantages`, `workflow`, `contact`, `footer`, dan `seo`.
- `MediaAsset`: `path`, `mime_type`, `size_bytes`, `width`, `height`, `alt_text`, dan `original_name`.
- `Slide`: `location` bernilai `hero` atau `about`, `media_asset_id`, `alt_text`, `position_x`, `position_y`, `sort_order`, dan `is_active`.
- `PortfolioCategory`: `name`, `slug`, dan `sort_order`.
- `PortfolioProject`: `title`, `category_id`, `cover_media_asset_id`, `alt_text`, `sort_order`, dan `is_active`.
- `PageVersion`: snapshot `payload` JSON yang immutable, `created_by` nullable untuk seed, dan `created_at`.
- Payload hasil kompilasi: `schema_version: 1`, `settings`, `slides: {hero: [], about: []}`, `portfolio: {categories: [], projects: []}`, dan `media: []`.
- Referensi aset snapshot menyimpan ID dan path, bukan mengambil detail terbaru dari draft ketika dirender. Kategori snapshot hanya mencakup kategori dengan proyek aktif.
- Konten awal dari Unsplash boleh tetap menjadi URL eksternal dalam seed. Upload baru wajib melalui pustaka media; penggantian dengan foto proyek asli dilakukan melalui admin.
- `CmsDraftService::build(): array` menghasilkan payload dengan urutan deterministik.
- `PublicationService::validate(array $payload): void` menolak payload tidak valid melalui validation exception.
- `PublicationService::publish(User $actor): PageVersion` membuat snapshot dan mengganti pointer publikasi dalam satu transaksi database.
- `PublicationService::restore(PageVersion $version, User $actor): PageVersion` membuat snapshot baru dari versi lama dan mengaktifkannya; tidak mengubah draft yang sedang dikerjakan.
- `MediaService::upload(UploadedFile $file): MediaAsset`, `isReferenced(MediaAsset $asset): bool`, dan `delete(MediaAsset $asset): void`.
- Penghapusan media ditolak jika direferensikan draft atau salah satu snapshot tersimpan. Cleanup versi lama merupakan pekerjaan lanjutan, bukan proses otomatis versi pertama.
- Route publik `GET /`; preview `GET /admin/preview` menggunakan autentikasi dan otorisasi admin serta header `Cache-Control: no-store` dan `X-Robots-Tag: noindex`.

## Sprint 1 — Fondasi dan migrasi tampilan

### Task 1: Aplikasi, akses admin, dan landing page berbasis snapshot

**Files:** Buat konfigurasi Laravel/Vite, model dan migration di peta file, controller publik, seed konten, partial Blade, CSS/JS publik, `tests/Feature/Public/LandingPageTest.php`, `tests/Feature/Cms/AdminAccessTest.php`, dan `tests/browser/landing-baseline.spec.ts`.

**Interfaces:** Menghasilkan skema data di atas, route `/`, panel `/admin`, dan snapshot seed yang menjadi sumber konten publik. Login menggunakan session Laravel; hanya user yang diotorisasi bisa mengakses panel.

- [x] Inventarisasi runtime; versi kompatibel lokal dan batas hosting yang belum ditentukan dicatat di `docs/operations/runtime.md`.
- [x] Ambil screenshot acuan HTML pada viewport 390×844, 768×1024, dan 1440×900; hentikan slider pada frame yang sama dan tunggu font/gambar sebelum menangkap screenshot.
- [x] Scaffold Laravel tanpa menimpa kedua file referensi; pasang Filament, pipeline aset publik, MySQL, dan PHPUnit.
- [x] Tulis `guest_cannot_access_admin`, `authorized_admin_can_access_panel`, dan `draft_changes_do_not_affect_public_page`; assertions: guest diarahkan ke login, admin mendapat respons 200, teks draft baru tidak ada di respons `/`.
- [x] Jalankan `php artisan test --filter=AdminAccessTest` dan `php artisan test --filter=LandingPageTest`; pastikan tes perilaku belum lolos sebelum wiring akses/sumber konten selesai.
- [x] Implementasikan model/migration, seed dari HTML, controller snapshot, dan partial Blade dengan class serta style acuan. Seed awal mengecualikan kartu portofolio yang diminta dihapus dan filter `lainnya` yang kosong.
- [x] Buat akun admin melalui perintah interaktif; jangan memasukkan password default produksi ke source code atau dokumentasi.
- [x] Jalankan tes ulang, `npm run build`, dan `npx playwright test tests/browser/landing-baseline.spec.ts`; tinjau perbedaan screenshot. Gambar Tentang masih satu foto pada sprint ini; logo resmi selesai di Sprint 2.
- [x] Git belum tersedia; milestone dan bukti disimpan di `docs/operations/sprint-1-progress.md` tanpa commit.

**Kriteria selesai:** Situs publik menampilkan snapshot seed, desain baseline terjaga selain penghapusan yang diminta, admin hanya bisa diakses setelah login, dan build produksi berhasil.

**Status 1 Oktober 2026:** Sprint 1 selesai lokal. Verifikasi: 13 tes backend/40 assertion, 6 tes browser, build aset, migration/seed MySQL, dan login admin nyata. Detail, keputusan, serta sumber foto Tentang awal yang HTTP 404 tercatat di `docs/operations/sprint-1-progress.md`.

## Sprint 2 — Logo, slideshow, media, dan portofolio

### Task 2: Pustaka media dan identitas perusahaan

**Files:** `app/Services/MediaService.php`, model media, halaman identitas, resource media, partial navbar/lokasi/footer, dan `tests/Feature/Cms/MediaManagementTest.php`.

**Interfaces:** Menghasilkan kontrak `MediaService` di atas. Identitas draft memiliki `company_name`, `tagline`, `logo_light_media_id`, `logo_dark_media_id`, dan `favicon_media_id`.

- [x] Tulis `rejects_invalid_or_oversized_upload`, `stores_valid_image`, dan `cannot_delete_referenced_media`; assertions: file non-gambar/lebih dari 5 MB ditolak, gambar valid tersimpan, aset seed yang dipakai snapshot tidak dapat dihapus.
- [x] Jalankan `php artisan test --filter=MediaManagementTest` untuk mengonfirmasi tes gagal pada layanan yang belum diimplementasikan.
- [x] Implementasikan pemeriksaan isi file/MIME, nama file acak, thumbnail, serta penyimpanan persisten. Optimalkan foto tanpa mengubah logo atau menghilangkan transparansi.
- [x] Buat form identitas dan pustaka media dengan preview; tampilkan lokasi penggunaan media sehingga admin memahami alasan penghapusan ditolak.
- [x] Siapkan logo resmi transparan dengan padding rapi dan varian footer; periksa bentuk terhadap `logo.jpeg`. Jangan mengganti dengan logo generatif yang hanya menyerupai referensi.
- [x] Jalankan tes ulang dan periksa preview logo pada tiga lokasi serta favicon; commit milestone setelah verifikasi.

**Kriteria selesai:** Media bisa diunggah/dipilih, logo dapat diganti sebagai draft, dan media yang masih digunakan terlindungi dari penghapusan.

### Task 3: Slideshow hero dan Tentang yang dapat diedit

**Files:** Halaman admin slideshow, `resources/js/slideshow.js`, partial hero/Tentang, `tests/Feature/Cms/SlideshowManagementTest.php`, dan `tests/browser/slideshow.spec.ts`.

**Interfaces:** `initSlideshow(container: HTMLElement): () => void` menginisialisasi satu slider dan mengembalikan fungsi cleanup. Lokasi, interval, dan foto berasal dari payload; state hero dan Tentang independen.

- [x] Tulis `validates_slide_location_interval_and_count` untuk lokasi `hero/about`, interval 3.000–10.000 ms, dan batas 10 slide aktif. Tulis tes browser `one_slide_has_no_timer_controls` serta `hero_and_about_advance_independently`.
- [x] Jalankan `php artisan test --filter=SlideshowManagementTest`; catat kegagalan kontrak sebelum implementasi.
- [x] Implementasikan unggah/pilih foto, urutan, aktif/nonaktif, alt text, posisi crop, serta interval pada admin. Draft boleh sementara kosong; validasi publikasi menangani kondisi itu.
- [x] Implementasikan slider Tentang dalam bingkai 440 px yang ada, mempertahankan radius, gradient, badge, dan aksen. Gunakan fade; jangan menambah kontrol visual baru pada Tentang. Dots hero tetap mengikuti jumlah slide.
- [x] Tangani satu slide sebagai foto statis, zero slide tanpa error JS pada preview, serta `prefers-reduced-motion` dengan foto pertama statis.
- [x] Jalankan tes backend, `npm run build`, dan `npx playwright test tests/browser/slideshow.spec.ts`; periksa crop mobile/desktop dan commit hasil.

**Kriteria selesai:** Kedua slideshow bisa dikelola terpisah, foto Tentang berganti dalam bingkai lama, dan satu slide tidak menghasilkan timer/kontrol yang tidak diperlukan.

### Task 4: Portofolio dan kategori

**Files:** Resource kategori/proyek, `CmsDraftService`, partial portfolio, `tests/Feature/Cms/PortfolioManagementTest.php`, dan `tests/browser/portfolio.spec.ts`.

**Interfaces:** Portofolio draft menggunakan model yang ditetapkan; hasil kompilasi berisi proyek aktif berurutan serta kategori yang masih memiliki proyek aktif.

- [x] Tulis `removed_project_is_not_seeded`, `empty_category_is_not_compiled`, dan `inactive_project_is_not_compiled`; assertions: `Rumah Dinas & Fasilitas` tidak ada, kategori `lainnya` tidak tampil, layanan `Rumah Dinas` tetap tersedia.
- [x] Jalankan `php artisan test --filter=PortfolioManagementTest` sebelum melengkapi pengelolaan dan kompilasi portofolio.
- [x] Implementasikan tambah/edit/arsipkan proyek, pilih foto sampul, kategori, alt text, dan urutan. Hapus kategori ditolak bila masih direferensikan proyek draft; tampilkan cara memindahkan proyeknya.
- [x] Implementasikan grid/filter memakai markup acuan; proyek nonaktif tidak masuk payload. Bila tidak ada proyek aktif, tampilkan pesan sederhana di area galeri tanpa tombol kategori kosong.
- [x] Jalankan tes ulang dan `npx playwright test tests/browser/portfolio.spec.ts`; pastikan filter Semua/Rumah/Renovasi/Commercial bekerja dan commit hasil.

**Kriteria selesai:** Portofolio bisa dikelola sebagai draft, kategori kosong tidak muncul, dan tidak ada penghapusan layanan di luar permintaan.

**Status 1 Oktober 2026:** Sprint 2 selesai lokal: 35 tes backend/125 assertion, 14 tes browser, build produksi, upload/hapus melalui browser, serta review independen. Semua edit tetap draft. Git belum tersedia, sehingga milestone dicatat tanpa commit. Lihat docs/operations/sprint-2-progress.md.

**Demo sprint:** Tunjukkan pengeditan logo, foto Tentang, hero, dan satu proyek pada admin. Data masih draft; alur preview/publikasi lengkap diberikan pada Sprint 3.

## Sprint 3 — Preview, publikasi, dan pemulihan versi

### Task 5: Alur konten draft hingga publikasi

**Files:** `CmsDraftService.php`, `PublicationService.php`, `PreviewPageController.php`, halaman publikasi admin, route preview, `tests/Feature/Cms/PublicationTest.php`, dan `tests/Feature/Cms/PreviewAccessTest.php`.

**Interfaces:** Menghasilkan seluruh kontrak publikasi pada peta data. Halaman publik membaca snapshot saja, preview membaca payload draft, dan publish/restore hanya tersedia untuk admin.

- [x] Tulis tes `guest_cannot_preview`, `preview_shows_draft_only_to_admin`, `publish_changes_public_snapshot`, dan `restore_creates_new_version_without_overwriting_draft`.
- [x] Tambahkan `publish_rejects_empty_slideshow`, `failed_publish_keeps_previous_version`, dan `historical_media_cannot_be_deleted`; assertions: validasi gagal tidak mengubah pointer publikasi dan aset versi lama tetap aman.
- [x] Jalankan `php artisan test --filter=PublicationTest` dan `php artisan test --filter=PreviewAccessTest` sebelum implementasi layanan.
- [x] Implementasikan kompilasi payload, validasi semua section, snapshot immutable, transaksi publikasi, serta restore sebagai snapshot baru. Untuk publikasi bersamaan, kunci singleton SiteSetting dalam transaksi sebelum membaca draft/mengganti pointer.
- [x] Implementasikan preview khusus admin dengan `no-store/noindex`; tampilkan banner preview yang hanya ada pada route ini.
- [x] Tambahkan tombol Simpan Draft, Preview, Publikasikan, waktu publikasi terakhir, serta riwayat versi pada admin. Invalidate cache publik setelah transaksi berhasil.
- [x] Jalankan tes ulang dan demo: edit → simpan → preview → publish → restore. Periksa melalui sesi browser guest bahwa draft tidak bocor dan catat milestone (workspace belum menjadi repository Git).

**Kriteria selesai:** Tiga revisi utama sudah dapat dipublikasikan dari admin, snapshot terakhir tetap aman bila publikasi gagal, dan admin bisa memulihkan versi sebelumnya.

**Status 1 Oktober 2026:** Sprint 3 selesai lokal: 52 tes backend/193 assertion, 15 tes browser, build produksi, demo MySQL edit → preview → publish → restore, dan review independen. Konten publik dikembalikan setelah demo; riwayat versi tetap tersimpan. Detail: `docs/operations/sprint-3-progress.md`.

**Milestone MVP:** Landing page, logo, slideshow, portofolio, media, login, preview, dan publikasi berfungsi. MVP dapat diuji di staging; belum merupakan rilis produksi final.

## Sprint 4 — Pengelolaan konten lengkap

### Task 6: Konten section, kontak terpusat, dan SEO

**Files:** Halaman admin konten/kontak/SEO, partial section terkait, `CmsDraftService.php`, `tests/Feature/Cms/ContentSettingsTest.php`, dan `tests/browser/content-layout.spec.ts`.

**Interfaces:** Memperluas `draft_payload` dengan field terstruktur. Kontak memiliki `whatsapp_number` dalam format internasional angka saja, `consultation_message`, `address`, `opening_hours`, `maps_url`, dan `maps_embed_url`.

- [x] Tulis `contact_update_reaches_all_contact_links`, `escapes_admin_text`, dan `validates_external_links`; assertions: navbar, hero, CTA, footer, dan tombol mengambang memakai nomor terpusat, teks tidak dieksekusi sebagai HTML, URL berbahaya ditolak.
- [x] Tetapkan batas form: judul section 100 karakter, judul kartu 80, deskripsi kartu 300, paragraf utama 800, SEO title 70, SEO description 160. Tulis tes penolakan melebihi batas serta tes layout dengan teks mendekati batas.
- [x] Jalankan `php artisan test --filter=ContentSettingsTest` sebelum implementasi form/validasi.
- [x] Buat editor teks/gambar layanan, profil Tentang, keunggulan, alur kerja, hero, CTA, dan footer; pertahankan jumlah enam layanan, empat keunggulan, dan lima tahap alur agar desain tetap sama.
- [x] Buat form kontak terpusat. Terapkan pesan konsultasi default ke CTA WhatsApp; admin tidak perlu mengubah URL secara manual. Gunakan URL encoding untuk pesan.
- [x] Buat form SEO title, description, dan gambar Open Graph; hasilkan metadata dari snapshot publikasi. Maps embed hanya menerima URL HTTPS Google Maps yang divalidasi, bukan HTML iframe bebas.
- [x] Jalankan tes ulang, `npm run build`, dan `npx playwright test tests/browser/content-layout.spec.ts`; periksa teks panjang/foto portrait-landscape pada tiga viewport dan catat milestone (workspace belum menjadi repository Git).

**Kriteria selesai:** Seluruh teks/gambar yang disepakati bisa diedit, semua kontak konsisten, dan input admin tidak dapat mengubah HTML/CSS halaman secara bebas.

### Task 7: Dashboard dan penggunaan admin

**Files:** Dashboard admin, halaman profil/password, `tests/Feature/Cms/DashboardTest.php`, dan `docs/operations/admin-guide.md`.

**Interfaces:** Dashboard menampilkan jumlah draft proyek aktif, jumlah slide aktif per lokasi, waktu publikasi terakhir, serta pintasan edit dan preview. Label harus menjelaskan bahwa hitungan konten berasal dari draft.

- [x] Tulis `dashboard_counts_draft_content_correctly` dan `password_change_requires_current_password`; pastikan perubahan password tidak dapat dilakukan tanpa password saat ini.
- [x] Jalankan `php artisan test --filter=DashboardTest` sebelum wiring widget dan profil.
- [x] Implementasikan dashboard dan penggantian password. Gunakan istilah Indonesia yang konsisten: Simpan Draft, Preview, Publikasikan, Aktif, dan Arsipkan.
- [x] Buat panduan singkat penggantian logo, upload/crop foto, urutan slide, pengelolaan proyek, publish, restore, dan logout.
- [x] Jalankan tes ulang dan uji alur melalui form admin pada desktop/mobile; catat milestone (workspace belum menjadi repository Git).

**Kriteria selesai:** Admin dapat menemukan menu utama dan menyelesaikan perubahan konten dengan panduan yang tersedia.

**Status 1 Oktober 2026:** Sprint 4 selesai lokal: 65 tes backend/291 assertion, 20 tes browser, build produksi, pemeriksaan visual tiga viewport, serta review independen dan perbaikan penguncian media. Panduan lengkap tersedia di `docs/operations/admin-guide.md`; bukti di `docs/operations/sprint-4-progress.md`. Git tersedia tetapi workspace belum menjadi repository, sehingga milestone dicatat tanpa commit.

## Sprint 5 — Verifikasi dan kesiapan produksi

### Task 8: Kandidat rilis, backup, dan deployment

**Files:** `tests/browser/release.spec.ts`, konfigurasi deployment sesuai hosting, `.env.example`, `docs/operations/{deployment,backup-restore,release-checklist}.md`.

**Interfaces:** Mengonsumsi aplikasi lengkap dari Sprint 1–4. Menghasilkan build rilis dan prosedur deployment/rollback, bukan otomatis memublikasikan ke hosting tanpa cakupan yang disepakati.

- [x] Jalankan `php artisan test` dan `npm run build`; kedua perintah harus keluar dengan exit code 0. Perbaiki kegagalan sebelum melanjutkan.
- [x] Jalankan `npx playwright test`; periksa menu mobile, kedua slideshow, semua filter portfolio, WhatsApp/telepon/Maps, login, preview, publish, dan restore.
- [x] Bandingkan screenshot pada 390×844, 768×1024, dan 1440×900 dengan baseline. Catat setiap perbedaan yang disengaja; perbedaan di luar tiga revisi harus diperbaiki.
- [x] Periksa keyboard navigation, fokus, alt text, contrast, reduced motion, gambar gagal dimuat, serta layout teks panjang. Pastikan gambar bawah fold memakai lazy loading dan hero pertama tidak tertunda olehnya. Keterbatasan kontras warna acuan dicatat, tidak diubah di luar instruksi pengguna.
- [ ] Konfigurasikan HTTPS, `APP_DEBUG=false`, session aman, rate limit login, storage symlink/akses media, dan web root `/public`; verifikasi admin serta preview tidak masuk indeks pencarian.
- [x] Dokumentasikan backup harian MySQL dan storage dengan retensi awal 14 hari pada lokasi berbeda dari server aplikasi. Prosedur tersedia; konfigurasi/dukungan provider menunggu tujuan hosting dan off-site.
- [x] Pulihkan satu backup ke lingkungan lokal terpisah; verifikasi akun admin (hash/hak akses), pointer versi, render HTML, logo, slideshow, dan portofolio setelah pemulihan. Smoke HTTP server restore/staging tetap masuk checklist hosting.
- [x] Siapkan kandidat/prosedur deployment dan checklist UAT karena pengguna memilih sementara berjalan lokal dengan Laragon; hosting/staging belum tersedia dan deployment belum terverifikasi.
- [ ] Catat persetujuan hasil UAT dan target hosting sebelum deployment produksi. Lakukan smoke test sesudah deployment serta siapkan rollback kode dan snapshot konten.
- [x] Catat hasil verifikasi dan dokumentasi berdasarkan bukti aktual, tanpa commit karena workspace belum menjadi repository Git.

**Kriteria selesai:** Semua pengujian utama lolos, visual sesuai acuan, backup berhasil dipulihkan, dan deployment yang dilakukan memiliki hasil smoke test. Jangan mengklaim produksi selesai jika akses hosting atau UAT belum tersedia.

**Status 1 Oktober 2026:** Kandidat Sprint 5 lokal selesai sesuai keputusan pengguna: 70 tes backend/348 assertion, 23 tes browser dalam satu run, build produksi, backup SQL/media, restore ke database/storage terpisah dengan 9 pemeriksaan true, serta review independen dan perbaikan isolasi import. HTTP lokal landing/login merespons 200. Hosting/HTTPS, backup off-site terjadwal, smoke HTTP staging/restore server, dan persetujuan UAT produksi belum selesai. Bukti: `docs/operations/sprint-5-progress.md`; panduan: `deployment.md`, `backup-restore.md`, `release-checklist.md` pada direktori yang sama.

## Definition of Done untuk setiap sprint

- [ ] Deliverable sprint bisa diperiksa pada aplikasi berjalan.
- [ ] Kriteria task terkait terpenuhi dan bukti pengujian dicatat.
- [ ] Tidak ada regresi pada layout desktop/mobile atau konten di luar lingkup revisi.
- [ ] Tidak ada credential dalam source code atau dokumentasi.
- [ ] Perubahan tidak membocorkan draft ke pengunjung.
- [ ] Checkbox diperbarui sesuai hasil aktual, bukan hanya karena kode sudah ditulis.

## Dependensi eksternal dan keputusan sebelum pengerjaan

| Kebutuhan | Kapan diperlukan | Sikap bila belum tersedia |
|---|---|---|
| Runtime lokal PHP/Composer/Node/MySQL | Awal Sprint 1 | Inventarisasi dahulu; gunakan runtime yang tersedia atau catat kebutuhan setup |
| Target hosting dan dukungan versinya | Pemilihan versi Sprint 1, deployment Sprint 5 | Kerjakan lokal dengan pasangan versi stabil yang terdokumentasi; jangan menjanjikan kompatibilitas hosting tanpa pemeriksaan |
| Aset logo resmi yang lebih bersih/transparan | Sprint 2 | Gunakan `logo.jpeg` sebagai referensi dan evaluasi kualitas hasil persiapan aset |
| Foto perusahaan/proyek asli | Sprint 2 dan UAT | Pertahankan URL contoh sebagai seed; tandai foto contoh untuk diganti melalui admin sebelum rilis bila diperlukan |
| Akses domain/hosting serta tujuan backup | Sprint 5 | Selesaikan pengujian lokal dan dokumentasi; deployment tetap berstatus belum dilakukan |

## Di luar lingkup dan pengembangan berikutnya

Multi-role admin, formulir konsultasi/CRM, analytics pengunjung, blog, galeri detail per proyek, penjadwalan publikasi, serta object storage eksternal dapat menjadi sprint lanjutan setelah kebutuhan nyata muncul. Tidak perlu menambahkan fitur tersebut untuk menyelesaikan tiga revisi sekarang.

## Status perencanaan

- [x] Struktur HTML dan aset logo ditinjau.
- [x] Usulan stack dan fitur dasar disetujui pengguna.
- [x] Rencana sprint dan kriteria verifikasi ditulis.
- [x] Pengguna menginstruksikan implementasi Sprint 1.
- [x] Sprint 1–4 selesai lokal.
- [x] Sprint 5 menghasilkan kandidat rilis dan backup/restore lokal untuk Laragon.
- [ ] Hosting, backup off-site terjadwal, UAT/persetujuan dan deployment produksi.

## Referensi teknis

- [Laravel Blade](https://laravel.com/framework/docs/13.x/blade)
- [Filament overview](https://filamentphp.com/docs/5.x/introduction/overview)
- [Filament installation](https://filamentphp.com/docs/5.x/introduction/installation)
- [Laravel file storage](https://laravel.com/framework/docs/13.x/filesystem)
- [Tailwind Play CDN — untuk pengembangan](https://tailwindcss.com/docs/installation/play-cdn)

Referensi versi merupakan titik awal pemeriksaan, bukan jaminan pasangan versi aplikasi. Cocokkan persyaratan saat Sprint 1 dan rekam versi yang benar-benar dipilih.


