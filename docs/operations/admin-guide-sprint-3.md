# Preview, publikasi, dan pemulihan versi

Admin lokal: `http://127.0.0.1:8000/admin`. Buka **Publikasi & Riwayat** setelah menyimpan perubahan pada editor.

## Publikasikan draft

1. Edit identitas, logo, slideshow, atau portofolio pada halaman masing-masing. Klik **Simpan Draft** pada form pengaturan; tambah/edit proyek dan kategori tersimpan sebagai draft melalui dialognya.
2. Klik **Preview Draft** dari editor atau halaman Publikasi & Riwayat. Preview membuka halaman dengan banner **Preview Draft**, hanya untuk admin yang sedang login. Halaman publik masih menampilkan snapshot sebelumnya.
3. Periksa logo, foto, crop, urutan, dan teks. Preview dapat menampilkan draft yang belum lengkap; preview bukan tanda bahwa draft sudah siap dipublikasikan.
4. Kembali ke **Publikasi & Riwayat**, klik **Publikasikan**, lalu konfirmasi. Semua draft yang sudah tersimpan pada saat publikasi dikompilasi, bukan perubahan form yang belum disimpan. Jika draft diedit setelah preview, buka preview kembali sebelum publikasi.
5. Jika berhasil, halaman menampilkan versi aktif dan waktu publikasi dalam WIB. Buka `/` melalui sesi pengunjung untuk memeriksa hasilnya.

Publikasi ditolak bila Hero atau Tentang tidak memiliki foto aktif, lebih dari 10 foto aktif, interval di luar 3000–10000 ms, konten terstruktur tidak lengkap, URL tidak aman, atau file media lokal hilang. Pesan kesalahan muncul di admin; snapshot dan versi publik sebelumnya tetap utuh.

Foto contoh eksternal tidak diperiksa dengan request jaringan saat publikasi. Ganti dengan foto perusahaan yang diunggah ke Pustaka Media sebelum rilis agar tidak bergantung pada URL contoh.

## Pulihkan versi

Pada tabel riwayat, pilih **Pulihkan** untuk versi yang diinginkan dan konfirmasi. Sistem membuat snapshot baru dengan konten versi tersebut dan mengaktifkannya. Versi sumber tetap utuh. **Draft saat ini tidak ditimpa** sehingga pekerjaan yang sedang dilakukan tetap dapat dilanjutkan.

Jika gambar versi lama hilang dari storage, pemulihan ditolak dan publikasi saat ini tetap aktif. Media yang direferensikan oleh draft atau riwayat versi tidak dapat dihapus lewat admin, termasuk media yang tidak lagi digunakan oleh versi publik terbaru.

## Batas Sprint 3

Riwayat menyimpan nomor versi, waktu pembuatan, pembuat, dan status publik aktif. Snapshot tidak dapat diedit/dihapus. Penghapusan riwayat otomatis, publikasi terjadwal, audit perubahan per field, dan deployment hosting belum tersedia.

Publik membaca snapshot yang ditunjuk database pada setiap request. HTML meminta revalidasi; preview memakai `no-store` dan `noindex`. Jika cache/CDN eksternal dipasang pada deployment nanti, prosedur purge cache harus ditambahkan.
