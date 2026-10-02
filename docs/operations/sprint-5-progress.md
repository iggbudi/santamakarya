# Sprint 5 — Kandidat rilis lokal dan pemulihan

Plan: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`, Task 8.

- [x] Verifikasi backend/build/browser dan pemeriksaan aksesibilitas/performa gambar.
- [x] Konfigurasi rilis, panduan deployment lokal/hosting, checklist UAT.
- [x] Backup MySQL + media, checksum, uji pemulihan ke database/storage terpisah.
- [x] Review independen dan verifikasi akhir.
- [ ] Deployment hosting, backup off-site terjadwal, UAT/persetujuan produksi.

Ruling: pengguna menyatakan belum ada hosting/domain/tujuan backup dan meminta sementara berjalan lokal pada komputer dengan Laragon. Kerjakan kandidat rilis serta restore drill lokal; jangan mengklaim HTTPS/deployment/off-site/UAT produksi selesai. Biaya bila berubah: sesuaikan konfigurasi/prosedur pada provider yang dipilih.

Ruling: Git tersedia tetapi folder belum menjadi repository; lanjut workspace yang sama dan catat milestone tanpa commit/worktree. Baseline awal 65 tes backend/291 assertion lolos.

Ruling: backup lokal tidak otomatis dijadwalkan dan tidak menghapus backup lama. Retensi minimal 14 hari dan salinan di lokasi berbeda didokumentasikan; penjadwalan tujuan off-site menunggu keputusan pengguna. Restore drill hanya membuat database baru bernama `santama_restore_*` dan media privat pada `.runtime`, tidak menimpa database/storage aktif.

## Implementasi dan RED/GREEN

- ReleaseReadinessTest awal gagal pada header admin dan loading gambar. Middleware global kini memasang noindex/nofollow + no-store/private pada route admin/preview, foto bawah fold memakai lazy/async, dan Hero pertama mendapat preload. Tes lolos 3/53; throttling login bawaan Filament diuji dengan enam percobaan gagal.
- Tes browser keyboard awal gagal pada status menu. Menu kini mempunyai aria-controls/expanded, fokus terlihat saat keyboard, dan Escape menutup menu serta mengembalikan fokus. Ketiga tes release terarah lolos, termasuk kontak/alt dan bingkai foto gagal + reduced motion.
- `cms:backup` awal belum tersedia (exit 1), kemudian membuat SQL/ZIP/manifest privat dengan checksum. BackupArchiveTest gagal pada kelas belum tersedia setelah runtime zip diperbaiki, kemudian lolos 2/4; corrupt SQL dan traversal ZIP ditolak.
- `artisan test` menjalankan subprocess PHP yang tidak mewarisi flag `-d extension=zip`; helper kini memakai INI tambahan khusus proses. Pemeriksaan PowerShell 5 awal kehilangan quote pada PHP `-r` (zip tidak aktif, exit 1), lalu lolos setelah memakai quote PHP yang kompatibel (exit 0, Zip enabled). Tidak mengubah php.ini global.
- Pint mengubah alias Kernel pada script restore yang sebelumnya ditempatkan sebelum import. Bootstrap gagal; import dipindahkan sebelum semua statement bootstrap, lalu restore normal lolos kembali.
- Suite browser awal mengalami timeout pada tiga tes baseline: fixture menunggu decode gambar lazy di luar viewport tanpa meminta gambarnya. Stabilizer screenshot kini membuat gambar eager hanya pada tes perbandingan full-page; perilaku lazy produksi tetap diuji terpisah. Run tersebut belum dianggap verifikasi akhir.

## Backup dan review

Backup lokal: `.runtime/backups/backup-20261001-082503-535603be-1cca-40b7-81f3-94647479f404`; berisi 5 berkas storage (termasuk thumbnail/file bawaan), SQL, dan manifest. Tidak ada `.env`/APP_KEY dalam bundle; checksum bukan autentikasi dan backup belum dienkripsi/off-site.

Review independen menemukan P1: import root dapat menjalankan SQL berkualifikasi ke database lain. Root kini hanya membuat database dan akun sementara; import/check memakai hak pada database pemulihan saja, lalu akun dihapus di finally. Regresi dengan skema probe disposable gagal sebelum perbaikan dan lolos setelahnya: write lintas skema ditolak saat import, probe utuh. Reviewer memeriksa ulang dan menyatakan P1 tertangani, tanpa temuan Critical/Important tambahan.

Restore normal setelah perbaikan: database `santama_restore_20261001_083708_3894c93f`, storage privat pada `.runtime/restore-drills/.../media`, **9 pemeriksaan true**: tabel CMS cocok dengan backup, pointer versi, HTML publik, checksum media, Hero/Tentang aktif, portofolio, logo draft, hash/hak akses admin, dan sumber tidak berubah. Report pada direktori drill. Ini verifikasi data/render server, bukan login HTTP ke server restore tersendiri.

Panduan baru: `deployment.md`, `backup-restore.md`, `release-checklist.md`. Konfigurasi lokal HTTP dipertahankan; contoh produksi mengharuskan HTTPS, debug false, cookie secure, web root public, storage persisten dan APP_KEY privat.

Verifikasi backend terbaru: **70 passed / 348 assertions**, build berhasil (CSS 24.66 kB, JS 340.88 kB). Perbandingan visual tiga viewport kembali lolos sesudah stabilizer meminta gambar lazy pada screenshot saja.

Run browser berikutnya masih menemukan timeout notifikasi 5 detik pada editor identitas/SEO dan publikasi; hasil 21 passed/2 failed tidak dianggap selesai. Penantian save kini memeriksa respons POST Livewire yang benar (`/livewire-<hash>/update`) sebelum notifikasi; batas assertion disesuaikan menjadi 15 detik untuk server lokal. Cleanup demo memeriksa nomor versi aktif agar publikasi yang berhasil sebelum notifikasi tidak meninggalkan konten demo. Marker yang tertinggal pada satu run gagal dipulihkan ke snapshot nyata; draft tidak ditimpa.

Smoke HTTP lokal: `/` dan `/admin/login` merespons 200. Login memakai `Cache-Control: no-store, private` dan `X-Robots-Tag: noindex, nofollow`. Pemeriksaan HTML publik sesudah cleanup tidak menemukan marker demo. Warna tetap: kontras putih/oranye acuan sekitar 3.45:1; keterbatasan teks kecil untuk target WCAG AA dicatat pada checklist, tanpa perubahan warna di luar instruksi pengguna.

## Hasil akhir kandidat lokal — 1 Oktober 2026

- Backend lengkap: **70 passed / 348 assertions**, exit 0.
- Build produksi: **berhasil**, exit 0 (CSS 24.66 kB; JS 340.88 kB).
- Browser lengkap setelah seluruh perbaikan: **23 passed**, exit 0, tanpa tes dilewati. Tiga baseline visual, form mobile/desktop, media, slideshow, reduced motion, keyboard, kontak, serta alur publikasi/pemulihan lolos dalam satu run.
- Helper cleanup setelah run mengonfirmasi konten publik sudah tidak memuat marker demo; password lokal tidak diubah.
- Checkpoint backup akhir: `.runtime/backups/backup-20261001-094008-8721c718-f651-47c4-b38d-5e42d8891395`.
- Restore checkpoint akhir: `santama_restore_20261001_094106_7ae9a1fb`, 5 berkas media, 9 pemeriksaan true, exit 0. Report pada `.runtime/restore-drills/santama_restore_20261001_094106_7ae9a1fb/report.json`; sumber tetap utuh.

Sprint 5 **untuk lingkungan lokal** sudah menghasilkan kandidat rilis, backup dan prosedur pemulihan, serta dokumentasi operasi. Checklist hosting/HTTPS, backup off-site terjadwal, smoke HTTP server restore/staging, dan persetujuan UAT produksi tetap terbuka sesuai keputusan pengguna menjalankan sementara di Laragon. Tidak ada deployment/backup off-site atau commit Git yang diklaim selesai.
