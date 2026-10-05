# Panduan pengelolaan landing page Santama Karya

Masuk melalui `/admin` menggunakan akun admin. Semua perubahan konten disimpan sebagai **draft**; halaman pengunjung berubah setelah **Publikasikan**. Dasbor menunjukkan jumlah proyek dan foto aktif dalam draft, serta waktu versi publik terakhir dalam WIB.

## Konten, kontak, dan SEO

1. Buka **Konten Landing Page**, pilih tab Hero, Profil Tentang, Layanan, Keunggulan, Alur Kerja, CTA & Lokasi, Footer, atau Portofolio. Edit teks, lalu klik **Simpan Draft**. Jumlah kartu tetap: enam layanan, empat keunggulan, lima tahap alur, dan dua metrik Tentang.
2. Foto layanan dan latar CTA dipilih dari **Pustaka Media**. Unggah gambar dahulu jika belum tersedia. Pilihan kosong mempertahankan foto contoh bawaan bila ada; untuk mengganti foto yang sudah diunggah, pilih aset baru. Isi deskripsi foto layanan agar gambarnya bisa dipahami pembaca layar. URL gambar bebas dan HTML tidak tersedia pada editor.
3. Buka **Kontak & Maps** untuk nama + nomor WhatsApp/telepon 1 (wajib), nama + nomor WhatsApp/telepon 2 (opsional), pesan konsultasi, alamat, jam operasional, tautan Maps, dan URL embed. Nomor memakai angka internasional tanpa `+`, spasi, atau nol lokal; contoh `6281234567890`. Nama ditampilkan di depan nomor, contoh `Dian (+62 821-5661-5866)` dan contoh input nama `Dian`. Nomor kedua hanya tampil di footer dan kartu lokasi bila diisi; bila diisi, nama dan nomornya wajib diisi bersamaan. Setelah kontak disimpan dan dipublikasikan, semua tombol WhatsApp memakai nomor utama dan pesan default tersebut; tautan telepon dan alamat footer ikut diperbarui.
4. Maps hanya menerima URL HTTPS Google Maps. Untuk embed, pilih **Share → Embed a map** di Google Maps dan salin nilai URL `src` saja, bukan seluruh HTML iframe. URL embed memakai `https://www.google.com/maps/embed…`. Tautan kunjungan dapat memakai Google Maps atau tautan pendek Maps yang valid.
5. Buka **SEO & Open Graph** untuk judul, deskripsi, dan gambar pratinjau berbagi. Metadata publik berasal dari versi yang dipublikasikan. Pratinjau aplikasi pesan dapat tetap memakai cache milik layanan tersebut setelah publikasi.

| Field | Batas karakter |
|---|---:|
| Judul section | 100 |
| Judul kartu | 80 |
| Deskripsi kartu | 300 |
| Paragraf utama | 800 |
| SEO title | 70 |
| SEO description | 160 |

Gunakan teks ringkas untuk judul dan label tombol. Form menolak input melebihi batas; layout tetap mengikuti landing page awal.

## Logo, foto, slideshow, dan portofolio

1. **Pustaka Media → Unggah Gambar** menerima JPEG, PNG, atau WebP sampai 5 MB dan 25 megapixel. Upload menghasilkan gambar maksimal sisi terpanjang 2400 px dan thumbnail 480 px. Transparansi PNG dipertahankan. Kolom **Digunakan di** menjelaskan referensi draft/riwayat yang melindungi gambar dari penghapusan.
2. **Identitas Perusahaan** mengatur nama perusahaan, tagline, logo untuk latar terang/gelap, dan favicon. Pilih aset sesuai latarnya, periksa preview, lalu **Simpan Draft**.
3. **Slideshow Hero** dan **Slideshow Tentang** mengatur foto secara terpisah. Pilih media, isi deskripsi, seret untuk mengurutkan, dan atur posisi crop horizontal/vertikal 0–100%. Gunakan preview crop untuk memastikan objek tetap terlihat pada mobile. Nonaktifkan **Aktif** untuk mengarsipkan foto.
4. Atur jeda 3000–10000 ms; maksimum 10 foto aktif per lokasi. Satu foto tampil statis. Tentang mempertahankan bingkai 440 px. Draft boleh kosong sementara, tetapi publikasi membutuhkan minimal satu foto aktif di setiap lokasi.
5. **Proyek Portofolio** mengatur judul, kategori, sampul, deskripsi gambar, keterangan, urutan, dan **Aktif**. Matikan Aktif untuk mengarsipkan proyek. Proyek baru memerlukan sampul dari media. **Kategori Portofolio** mengatur nama/slug/urutan; pindahkan semua proyek yang menggunakannya, termasuk arsip, sebelum menghapus kategori. Filter publik hanya muncul untuk kategori dengan proyek aktif.

Media dalam versi historis tetap dilindungi walaupun sudah diganti pada draft. Detail pengelolaan foto tersedia pada [panduan media dan slideshow](admin-guide-sprint-2.md).

## Preview, publikasi, dan pemulihan

1. Simpan seluruh perubahan pada editor masing-masing, lalu klik **Preview Draft**. Preview hanya dapat dibuka admin yang sedang login dan menampilkan banner draft.
2. Periksa teks, nomor/link, logo, foto, crop, serta urutan pada mobile dan desktop. Bila draft berubah lagi setelah preview, periksa kembali.
3. Buka **Publikasi & Riwayat → Publikasikan**, lalu konfirmasi. Sistem memublikasikan seluruh draft tersimpan sebagai versi baru. Form yang belum disimpan tidak ikut dipublikasikan.
4. Jika validasi gagal, perbaiki field yang disebutkan dan ulangi; versi publik sebelumnya tetap aktif. Buka `/` sebagai pengunjung untuk memeriksa versi baru.
5. Untuk kembali ke versi lama, pilih **Pulihkan** pada riwayat lalu konfirmasi. Sistem membuat versi publik baru dari snapshot tersebut; draft yang sedang dikerjakan tetap tersimpan. Publikasi kembali draft akan menggantikan hasil pemulihan.

Detail tersedia pada [panduan publikasi dan pemulihan](admin-guide-sprint-3.md).

## Profil, password, dan logout

Buka **Profil & Password** dari dasbor atau menu pengguna. Untuk mengganti password, isi password baru minimal 12 karakter, konfirmasi, dan password saat ini; lalu simpan. Perubahan email juga memerlukan password saat ini. Jangan menyimpan password dalam konten website.

Untuk keluar, buka menu pengguna di kanan atas lalu pilih **Sign out**. Pada mobile, buka navigasi melalui ikon menu; menu pengguna tetap tersedia di panel admin. Setelah keluar, akses admin kembali meminta login.

Sprint 1–4 tersedia lokal, dilengkapi kandidat rilis dan backup/restore drill lokal pada Sprint 5. Deployment hosting, backup off-site, serta UAT/persetujuan produksi menunggu keputusan dan akses. Lihat [cara menjalankan](deployment.md) dan [backup/pemulihan](backup-restore.md).
