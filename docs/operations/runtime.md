# Runtime pengembangan

Pemeriksaan: 1 Oktober 2026.

| Komponen | Runtime yang ditemukan |
|---|---|
| PHP | 8.3.30, `C:/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe` |
| Composer | 2.8.3, `C:/ProgramData/ComposerSetup/bin/composer.phar` |
| Node | 24.14.0 |
| npm | 11.18.0, jalankan `npm.cmd` dari PowerShell |
| MySQL | 8.4.3, `C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/` |

PHP di PATH berasal dari XAMPP dan gagal startup karena path konfigurasi lama. Gunakan PHP Laragon secara eksplisit atau tambahkan direktori PHP Laragon pada PATH session terminal. Tidak perlu mengedit konfigurasi PHP global.

Laravel 12 dipilih untuk PHP lokal yang tersedia, bersama Filament 5. Versi tepat hasil instalasi direkam dalam `composer.lock`; aset publik tetap menggunakan Tailwind 3 agar mengikuti HTML acuan. Tema bawaan Filament disajikan terpisah.

Versi yang terpasang: Laravel 12.69.3, Filament 5.9.0, Livewire 4.4.7, PHPUnit 11.5.56, Tailwind 3.4.19, Vite 7.3.6, dan Playwright 1.63.0. Lockfile menjadi acuan instalasi ulang.

Ekstensi `zip` tersedia tetapi belum aktif pada konfigurasi PHP Laragon global. Helper `scripts/php.ps1` dan `scripts/serve.ps1` mengaktifkannya dengan `-d extension=zip`. Composer juga dapat dijalankan dengan flag tersebut; tidak mengubah `php.ini` global.

Resolver curl PHP mengalami timeout pada instalasi awal. Arsip dependency resmi sesuai lockfile diunduh ke cache proyek melalui curl Windows; Composer menyelesaikan instalasi dari cache. Tidak menggunakan mirror pihak ketiga atau mengubah DNS global.

MySQL development memakai port 3307, bind `127.0.0.1`, dan data directory `.runtime/mysql-data`. Akun aplikasi hanya untuk database proyek. Pengujian otomatis menggunakan SQLite in-memory untuk isolasi, lalu migration/seed diperiksa juga pada MySQL lokal.

Target hosting belum ditentukan. Sebelum produksi, cocokkan versi PHP, ekstensi, web root `/public`, akses Composer/build, dan persistent storage terhadap [Laravel deployment](https://laravel.com/framework/docs/12.x/deployment) serta [Filament installation](https://filamentphp.com/docs/5.x/introduction/installation).
