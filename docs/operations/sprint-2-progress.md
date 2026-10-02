# Sprint 2 — Catatan pengerjaan

Plan: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`

## Cakupan dan kontrak

Task 2 (media/identitas), Task 3 (slideshow), dan Task 4 (portofolio). Semua perubahan merupakan draft; pointer publikasi tetap sama. Preview route dan publikasi UI tetap Sprint 3.

Baseline: 13 tes backend / 40 assertion lolos; MySQL lokal aktif. Workspace belum memiliki Git, sehingga milestone dicatat di sini tanpa commit/worktree.

Pre-flight: Task 2 menghasilkan MediaService/MediaAsset untuk pilihan gambar Task 3–4. Task 3 menghasilkan slide draft dengan lokasi/crop/urutan. Task 4 menghasilkan CmsDraftService::build() yang digunakan Sprint 3 untuk snapshot publikasi. Payload build tidak membaca konten snapshot sebagai draft.

## Progres

- [x] Task 2: media, identitas, dan aset logo.
- [x] Task 3: slideshow hero dan Tentang.
- [x] Task 4: portofolio dan kategori.
- [x] Full suite, build, browser, dan review independen.

## Keputusan dan bukti



- Media: tes RED mengonfirmasi layanan belum ada; GREEN 8 tes media (valid MIME/5MB/nama acak/thumbnail/transparansi/referensi historis/deletion). Ukuran file diperiksa sebelum membaca isi; gambar dibatasi 25 MP. Migration thumbnail dan storage symlink diterapkan pada MySQL lokal.
- Identitas: pilihan media tervalidasi dan preview latar terang/gelap. `cms:prepare-brand` idempoten, menyiapkan dua PNG transparan dan memilihnya pada draft yang kosong. Aset SVG ditelusuri dari JPEG (bukan file master dan bukan logo generatif); nama perusahaan tetap elemen teks acuan. Varian dark menggunakan gear putih. Ketiga lokasi logo dan favicon memakai URL snapshot hasil kompilasi, tanpa membaca model mutable saat rendering publik.
- Slideshow: tes RED layanan belum ada, lalu GREEN mencakup lokasi, interval 3000–10000, batas 10 aktif, crop, urutan, isolasi lokasi, foto baru wajib media, dan draft kosong. Form server mempertahankan URL seed dari record asli, bukan menerima URL tersembunyi dari client. Halaman Hero/Tentang terpisah, dengan preview crop dan Simpan Draft. Modul JS menghasilkan cleanup, slider independen, kondisi 0/1 foto, dan reduced motion.
- Portofolio: tambah/edit/arsip/hapus draft, sampul wajib untuk proyek baru, kategori unik dan perlindungan kategori terpakai termasuk arsip. `CmsDraftService::build()` deterministik, hanya proyek/slide aktif, kategori terisi, dan metadata media yang direferensikan. Galeri kosong memiliki pesan tanpa filter. Layanan Rumah Dinas tidak dihapus.
- Foto contoh Tentang yang HTTP 404 diganti **hanya pada draft MySQL lokal** dengan URL foto contoh Hero pertama. Snapshot lama dan file HTML referensi tidak diubah. Foto contoh eksternal tetap bergantung pada sumber/jaringan; ganti dengan foto perusahaan melalui upload sebelum rilis. Fresh seed mempertahankan sumber asli; admin dapat menggantinya melalui editor.
- Regression Livewire menemukan alt/caption proyek kosong menghasilkan null; form kini menormalisasi menjadi string kosong. Review independen menemukan kasus identik pada edit alt media; RED direproduksi, perbaikan diterapkan, GREEN melalui tes regresi. Tidak ada temuan penting yang ditunda.
- Browser acuan menstabilkan Tailwind/Lucide melalui paket lokal karena CDN gagal dapat menghentikan inline JS lama. CSS acuan dikompilasi dari kelas HTML asli. Foto memakai shared cache/placeholder konsisten bila sumber gagal, iframe dimask, interval dibekukan. Perbandingan 390/768/1440 lolos dalam ambang 1% yang sama dengan Sprint 1.
- Fixture draft browser dirender dari builder ke `.runtime`, lalu diintercept oleh Playwright. Tidak menambah route preview produksi atau mengubah pointer publikasi. Preview/publish UI tetap Sprint 3.
- Panduan: `docs/operations/admin-guide-sprint-2.md`. Akun dan password development tetap di file lokal yang diabaikan; tidak dimasukkan ke dokumentasi ini.

## Verifikasi final

- `scripts/php.ps1 artisan test`: **35 passed, 125 assertions**.
- `npm.cmd run build`: sukses; CSS 24.43 KB, JS 340.71 KB (sebelum gzip).
- `npx.cmd playwright test --reporter=list`: **14 passed**, tanpa skip pada mesin ini.
- Browser mencakup 6 editor admin, simpan identitas dengan public HTML identik, upload/hapus aktual, baseline 3 viewport, filter portofolio, menu mobile, slideshow independen, 0/1 foto, cleanup, reduced motion, bingkai Tentang 440 px, tiga logo, galeri kosong.
- Pint merapikan file PHP yang disentuh; full backend suite dijalankan setelah formatting/perbaikan. `--dirty` tidak tersedia karena workspace belum memiliki Git.
- Screenshot: `artifacts/browser/sprint2-admin-*.png`, `sprint2-draft-390.png`, `sprint2-draft-1440.png`, dan `migrated/*.png`. Logo navbar/footer diperiksa secara visual. Sumber foto eksternal dapat gagal saat pengambilan screenshot; pengujian layout memakai fallback konsisten dan bukan bukti keaslian foto perusahaan.

Sprint 2 selesai lokal. Publik tetap membaca snapshot Sprint 1; penerapan edit melalui preview/publish adalah Sprint 3. Tidak ada commit/deployment pada sprint ini.
