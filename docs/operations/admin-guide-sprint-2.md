# Panduan admin — Sprint 2

Admin lokal: `http://127.0.0.1:8000/admin`. Gunakan akun development yang telah dibuat pada Sprint 1. Semua perubahan saat ini merupakan **draft**. Alur preview dan publikasi diselesaikan pada Sprint 3.

## Media dan logo

1. Buka **Pustaka Media → Unggah Gambar**. JPEG, PNG, dan WebP diterima sampai 5 MB dan 25 megapixel. Tunggu upload selesai, lalu klik **Unggah**.
2. Isi deskripsi gambar bila diperlukan. Foto besar diperkecil hingga sisi terpanjang 2400 px; thumbnail hingga 480 px. PNG transparan tetap transparan.
3. Buka **Identitas Perusahaan**, pilih logo untuk latar terang, logo untuk latar gelap, dan favicon. Preview menunjukkan pilihan masing-masing. Klik **Simpan Draft**.
4. Aset yang terpakai di draft, slideshow/proyek arsip, atau versi publikasi historis tidak bisa dihapus. Kolom **Digunakan di** menunjukkan referensinya. Unggah file baru untuk mengganti gambar, lalu ubah pilihan kontennya.

Aset simbol transparan tersedia dari rekonstruksi vektor `logo.jpeg`. File master resmi perusahaan dapat menggantikannya melalui admin. `php artisan cms:prepare-brand` dapat menambahkan aset bawaan secara idempoten dan mengisi pilihan draft yang masih kosong.

## Slideshow

Buka **Slideshow Hero** atau **Slideshow Tentang**. Pilih gambar yang sudah diunggah; foto bawaan tetap digunakan selama belum diganti. Seret item untuk mengatur urutan, isi deskripsi gambar, atur posisi horizontal/vertikal 0–100%, dan lihat preview crop. Nonaktifkan foto untuk mengarsipkannya atau hapus item dari daftar draft.

Jeda pergantian 3000–10000 ms, maksimal 10 foto aktif per lokasi. Klik **Simpan Draft**. Satu foto tampil statis; Tentang menggunakan bingkai 440 px tanpa tombol baru. Pengunjung dengan reduced motion melihat foto pertama statis. Draft boleh sementara kosong; validasi publikasi akan ditambahkan pada Sprint 3.

## Portofolio

Kategori dikelola lewat **Kategori Portofolio**. Gunakan slug huruf kecil/angka/tanda hubung yang unik. Kategori yang masih dipakai proyek, termasuk arsip, harus dikosongkan sebelum dihapus.

Pada **Proyek Portofolio**, tambah/edit judul, kategori, sampul, deskripsi gambar, keterangan, dan urutan. Proyek baru wajib memiliki sampul dari Pustaka Media. Matikan **Aktif** untuk mengarsipkan proyek. Proyek nonaktif dan kategori tanpa proyek aktif tidak masuk kompilasi draft. Penghapusan draft tidak mengubah snapshot publik/historis.

Layanan **Rumah Dinas** tetap tersedia; kartu portofolio **Rumah Dinas & Fasilitas** sudah dihapus sesuai permintaan.
