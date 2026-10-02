# Sprint 3 — Preview, publikasi, dan pemulihan versi

Plan: `docs/superpowers/plans/2026-10-01-santama-cms-sprint-plan.md`, Task 5.

- [x] Tes RED akses preview dan publikasi/pemulihan.
- [x] Validasi payload, transaksi snapshot, otorisasi, perlindungan aset historis.
- [x] Preview admin no-store/noindex dan banner; halaman publikasi/riwayat versi.
- [x] Demo browser edit → preview → publish → restore dengan sesi guest.
- [x] Full suite/build, review independen, panduan dan checklist plan.

Baseline: 35 backend tests /125 assertions lolos. Kompilasi draft sudah tersedia dari Sprint 2. Pekerjaan dilanjutkan pada workspace lokal sesuai dua sprint sebelumnya. Pemeriksaan akhir menemukan Git sudah tersedia, tetapi workspace belum merupakan repository (`git status`: not a git repository); milestone dicatat tanpa commit/worktree.

Ruling: tidak menambahkan cache HTML publik baru — controller membaca pointer snapshot pada tiap request, sehingga tidak ada cache aplikasi yang perlu dihapus; respons publik akan meminta revalidasi agar publikasi/pemulihan terlihat pada request berikutnya. Bila cache CDN ditambahkan nanti, integrasi purge diperlukan.

Ruling: publikasi hanya membuat versi konten pada aplikasi lokal. Demo browser akan memulihkan konten publik awal dan mengembalikan edit demo, tanpa deployment eksternal.

- RED: 10 tes akses preview/publikasi gagal karena route/layanan belum ada; 3 tes halaman publikasi gagal karena page belum ada. GREEN awal 13 tes/55 assertion setelah implementasi.
- Snapshot baru dan pointer disimpan dalam transaksi yang mengunci singleton SiteSetting. Restore membaca payload versi tersimpan dan membuat versi baru, tidak menimpa draft. Actor diverifikasi terhadap role admin terkini di database.
- Preview menyusun draft, tidak menjalankan validasi publikasi agar draft kosong dapat diperiksa; akses guest diarahkan ke login dan non-admin 403. Header no-store/private dan noindex/nofollow serta banner hanya ada pada preview.
- Semua editor prioritas menyediakan Preview Draft; publikasi dan riwayat terpusat pada halaman admin baru. Konfirmasi publish/restore tersedia sebagai bagian UI. Tabel tidak mempunyai edit/delete snapshot.
- Delete media memperoleh lock yang sama dengan publish/restore sebelum pemeriksaan penggunaan, untuk mencegah race snapshot terhadap penghapusan aset. File dihapus setelah transaksi record selesai.
- Tes trigger database menolak update pointer dan membuktikan snapshot baru ikut rollback. Tes kehilangan aset membuktikan publish dan restore ditolak tanpa perubahan publikasi/draft. Media dari snapshot historis tetap terlindungi setelah draft diganti.
- Ruling: jumlah kartu services (6), advantages (4), workflow (5), dan metrics (2) tetap schema v1 untuk mempertahankan struktur acuan. Semua elemen diperiksa dengan wildcard. Penambahan/penghapusan jumlah kartu di luar struktur ini memerlukan perubahan schema/validator pada pekerjaan berikutnya.
- Review independen: Important/P2 pada validasi elemen tambahan berhasil direproduksi (RED: malformed extra card was published). Perbaikan wildcard + batas jumlah diterapkan; GREEN scoped 17 tes/68 assertion. Tidak ada temuan Important lain.
- Browser demo awal gagal pada selector label wajib yang memiliki asterisk, sebelum melakukan perubahan draft. Selector diperbaiki menjadi role textbox dengan nama prefix; demo dijalankan ulang.
- Demo browser berhasil pada MySQL lokal: edit nama brand → simpan → preview privat → publish → cek sesi guest → restore → pastikan draft tetap → kembalikan nama brand semula. HTML publik setelah restore sama persis dengan sebelum demo. History tetap menyimpan versi demo dan pemulihan sebagai bukti, sesuai kontrak snapshot immutable.
- Dialog konfirmasi Filament menggunakan role alertdialog; selector browser diperbaiki berdasarkan snapshot DOM. Default timeout locator 15 detik membantu mendeteksi salah selector sebelum timeout seluruh demo. Sisa draft dari percobaan gagal dibersihkan sebelum demo final.
- Pesan validasi publikasi menggunakan bahasa Indonesia dan riwayat diberi label Versi. Header HTML publik no-cache/must-revalidate; tidak ada cache HTML aplikasi tambahan.


## Verifikasi final — 1 Oktober 2026

- `scripts/php.ps1 artisan test`: **52 passed /193 assertions**, SQLite in-memory. Seluruh tes lama ikut dijalankan.
- `npm.cmd run build`: sukses; CSS 24.57 KB, JS 340.71 KB sebelum gzip.
- `npx.cmd playwright test --reporter=list`: **15 passed**, tanpa skip pada mesin ini. Demo publikasi/pemulihan berjalan terhadap MySQL lokal dan menggunakan sesi admin serta guest terpisah.
- Perbandingan baseline tetap lolos pada 390/768/1440 px. Preview banner hanya muncul untuk admin pada route preview; public HTML tidak memuat banner atau draft.
- Full suite dijalankan setelah perbaikan review, formatting Pint, label halaman dan pesan validasi. Tidak ada temuan Important/Critical yang ditunda.
- Screenshot halaman publikasi: `artifacts/browser/sprint3-publication-history.png`, diperiksa visual. Screenshot baseline/editor tetap tersedia dari suite browser.
- Konten publik dan nama brand draft dikembalikan setelah demo. Versi demo/restored tetap berada di riwayat sebagai snapshot immutable; tidak dilakukan penghapusan riwayat atau deployment.
- Cache aplikasi HTML tidak diperkenalkan: controller selalu membaca pointer database dan respons meminta revalidasi. Purge CDN harus diintegrasikan jika digunakan nanti.
- Validator tidak mengecek HTTP gambar contoh eksternal; yang diperiksa adalah format URL aman, kelengkapan konten, batas slideshow, dan keberadaan file media lokal. Penggunaan foto perusahaan asli serta staging/hosting tetap pekerjaan UAT/produksi berikutnya.

Task 5 complete. Sprint 3 selesai lokal, MVP pengelolaan tiga revisi utama dapat dipreview, dipublikasikan, dan dipulihkan dari admin. Panduan: `docs/operations/admin-guide-sprint-3.md`. Sprint 4 belum dimulai.
