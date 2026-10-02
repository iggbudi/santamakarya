# Sprint 4 — Editor konten, kontak, SEO, dan dashboard

Plan: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`, Task 6–7.

- [x] Task 6: tes RED, editor konten/kontak/SEO, metadata snapshot, validasi dan layout.
- [x] Task 7: tes RED, statistik draft, profil/password, pintasan dan panduan.
- [x] Verifikasi backend/build/browser, review independen, checklist plan.

Ruling: melanjutkan workspace lokal yang sama; Git tersedia tetapi folder belum menjadi repository. Milestone dicatat tanpa commit/worktree.

Ruling: enam layanan, empat keunggulan, lima tahap alur, dan dua metrik tetap. Foto layanan dan latar CTA baru harus dipilih dari media; URL contoh sebelumnya dipertahankan hanya dari draft server, bukan field URL bebas.

Ruling: kontak menyimpan satu pesan konsultasi default; seluruh CTA memakai default setelah kontak diedit. Snapshot lama tetap mempertahankan pesan semula sampai publikasi baru.

## Implementasi dan bukti

- Editor Konten Landing Page memakai tab/field terstruktur untuk Hero, Tentang, layanan, keunggulan, alur, CTA/lokasi, footer, serta judul portofolio. Batas panjang dan jumlah kartu divalidasi saat simpan maupun publikasi.
- Kontak & Maps menyatukan nomor, pesan WhatsApp yang di-encode, alamat, jam, dan URL Maps. Teks dirender escaped; URL iframe/JavaScript, host tiruan, HTTP, kredensial URL, dan port yang tidak diizinkan ditolak. Tes publikasi memastikan enam tautan WhatsApp dan telepon berubah bersama, sementara draft sebelumnya tidak bocor.
- SEO & Open Graph menghasilkan metadata dari snapshot; foto layanan, latar CTA, dan OG dipilih dari media serta masuk perlindungan referensi draft/riwayat.
- Dasbor menandai hitungan sebagai draft, menunjukkan waktu/nomor versi publik, dan menyediakan pintasan. Profil native Filament mempertahankan verifikasi password saat ini dan password baru minimal 12 karakter. Tes menggunakan akun factory; password akun lokal tidak diubah.
- Panduan lengkap: `docs/operations/admin-guide.md`.

RED sebelum implementasi: ContentSettingsTest gagal karena layanan belum ada; ContentEditorsTest gagal karena halaman belum ada; DashboardTest gagal sebelum widget/profil tersedia. Setelah implementasi, tes terkait lolos. Koreksi ekspektasi tautan WhatsApp dari tujuh menjadi enam mengikuti jumlah yang benar pada markup acuan.

Review independen menemukan satu P2: validasi ID media sebelum lock singleton dapat berlomba dengan penghapusan media. Tes regresi urutan query gagal sebelum perbaikan. Validasi konten/SEO kini berjalan setelah lock dalam transaksi yang sama dengan mekanisme penghapusan; ContentSettingsTest lolos 6 tes/45 assertion. Tidak ada temuan Critical lainnya pada review.

Verifikasi terarah browser: 5 tes lolos. Form konten/kontak/SEO/profil dan dasbor diperiksa pada 390/1440 px; konten mendekati batas serta foto portrait/landscape pada 390/768/1440 px. Tidak ada overflow horizontal/error JavaScript; bingkai Tentang tetap 440 px dan foto memakai cover. Screenshot ada pada `artifacts/browser/sprint4-*.png` (diabaikan Git). Fixture panjang/foto sintetis hanya di-intercept pada tes dan tidak mengubah database publik.

Checkpoint backend lengkap setelah perbaikan: 65 tes/291 assertion lolos. Build produksi berhasil, CSS 24.57 kB dan JS 340.71 kB. Pint dijalankan dengan daftar file eksplisit karena opsi `--dirty` memerlukan repository Git.

## Verifikasi akhir — 1 Oktober 2026

- `scripts/php.ps1 artisan test`: **65 passed / 291 assertions**, exit 0.
- `npm.cmd run build`: **berhasil**, exit 0.
- `npx.cmd playwright test --reporter=list`: **20 passed**, exit 0, tanpa tes dilewati. Termasuk baseline visual 390/768/1440 px, navigasi mobile, editor/media, portofolio, slideshow/reduced motion, serta demo edit → preview → publish → restore pada MySQL.
- Setelah Pint merapikan urutan import tes regresi, ContentSettingsTest dijalankan ulang: **6 passed / 45 assertions**.
- Reviewer memeriksa ulang perbaikan P2 dan mengonfirmasi sudah tertangani; tidak ada masalah tambahan dalam lingkup perbaikan.

Sprint 4 selesai lokal; Task 6–7 pada rencana dicentang sesuai bukti. Demo publikasi browser mengembalikan konten publik semula melalui versi baru dan mempertahankan draft/riwayat. Foto fixture panjang tidak dipublikasikan, dan password akun lokal tidak diubah. Sprint 5 (deployment, backup, pemulihan server, serta UAT produksi) belum dimulai.
