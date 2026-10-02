# Checklist kandidat rilis

Lingkungan: Windows/Laragon lokal, `http://127.0.0.1:8000`. Hosting/domain belum dipilih; checklist ini tidak menyatakan aplikasi sudah dirilis ke produksi.

## Pemeriksaan otomatis dan lokal

- [x] Backend lengkap dan build produksi exit 0; jumlah tes dicatat pada laporan Sprint 5.
- [x] Browser lengkap exit 0 tanpa tes dilewati: 23 tes lolos pada run akhir.
- [x] Perbandingan visual dengan `landingpage.html` pada 390×844, 768×1024, 1440×900 lolos; hanya revisi yang disepakati diterima.
- [x] Input panjang dan rasio foto portrait/landscape diperiksa pada tiga viewport pada Sprint 4.
- [x] Tes kontak publik memeriksa nomor WhatsApp terpusat, tautan telepon/Maps aman, dan alt gambar nonkosong.
- [x] Navigasi mobile bisa dibuka dengan keyboard dan ditutup Escape; indikator fokus terlihat dan status menu dapat dibaca teknologi bantu.
- [x] Reduced motion mempertahankan foto pertama statis; foto gagal dimuat tidak meruntuhkan bingkai Tentang atau menimbulkan error JS.
- [x] Foto Tentang/layanan/portofolio memakai lazy loading/async decoding; latar Hero pertama mendapat preload prioritas tinggi.
- [x] Admin/preview memakai noindex/nofollow dan no-store/private, termasuk redirect login; percobaan login berulang dibatasi.
- [x] Backup MySQL/media telah dipulihkan ke database dan storage terpisah; sumber tidak ditimpa. Report mencatat fingerprint data, pointer versi, HTML, gambar, logo, slideshow, portofolio, dan hash/hak akses admin.
- [x] Credential dan backup hanya berada pada lokasi privat yang diabaikan Git; panduan tidak memuat password.

Screenshot/perbandingan dan browser tests merupakan bukti lokal, bukan sertifikasi aksesibilitas penuh. Kontras putih terhadap oranye acuan `#D96B27` adalah sekitar **3.45:1**, di bawah 4.5:1 untuk teks kecil pada target WCAG AA; keputusan visual diperlukan bila target tersebut dipilih. Warna tidak diubah karena pengguna meminta desain tetap. Foto/font contoh eksternal masih membutuhkan jaringan; contoh foto Tentang pada snapshot publik lama dapat gagal dimuat. Ganti foto contoh melalui admin dan publikasikan setelah pemeriksaan.

## UAT pengguna (belum disetujui)

- [ ] Periksa penghapusan **portofolio** Rumah Dinas & Fasilitas; layanan Rumah Dinas tetap tersedia.
- [ ] Pilih logo resmi terang/gelap dan favicon, periksa tiga lokasi serta ketajaman.
- [ ] Unggah foto perusahaan, atur crop/urutan Tentang dan Hero, periksa mobile/desktop.
- [ ] Edit satu proyek, kategori, dan status Aktif/Arsipkan; periksa filter.
- [ ] Edit teks/kontak/SEO, Simpan Draft, pastikan halaman pengunjung belum berubah, lalu Preview.
- [ ] Publikasikan setelah preview; periksa nomor/link dan gambar sebagai pengunjung.
- [ ] Pulihkan versi terdahulu; pastikan draft tetap tersimpan.
- [ ] Uji ganti password dan logout dengan akun yang dipakai pengguna.
- [ ] Catat nama pemeriksa, tanggal, feedback, dan persetujuan UAT. Tes otomatis tidak menggantikan persetujuan pengguna.

## Sebelum produksi di hosting

- [ ] Provider/domain dan versi PHP/MySQL disetujui; web root `/public`, storage persisten, symlink dan permission diuji.
- [ ] HTTPS valid, `APP_DEBUG=false`, APP_KEY privat, cookie secure/HTTP-only/SameSite dan proxy tepercaya diperiksa pada browser.
- [ ] Akun produksi dibuat terpisah; credential lokal tidak dipakai.
- [ ] Backup harian ke lokasi berbeda, retensi minimal 14 hari, enkripsi, verifikasi checksum serta pemberitahuan kegagalan dikonfigurasi dan diuji.
- [ ] Restore dan smoke test HTTP staging berhasil; waktu pemulihan dicatat.
- [ ] UAT dan persetujuan deployment produksi diperoleh.
- [ ] Artefak rollback kode dan versi konten dicatat; smoke test pascadeploy serta prosedur purge CDN diperiksa.

Lihat [deployment](deployment.md), [backup/pemulihan](backup-restore.md), dan [panduan admin](admin-guide.md).
