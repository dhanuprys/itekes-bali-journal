# Konfigurasi PHP untuk Upload File Berukuran Besar

Secara _default_, PHP membatasi ukuran maksimal file yang diunggah ke _server_ sebesar **2MB**. Jika sistem membutuhkan ukuran _upload_ file yang lebih besar (misalnya 6MB untuk unggahan ZIP, 10MB untuk laporan akhir, dll.), Anda harus mengubah beberapa parameter di dalam file konfigurasi PHP (`php.ini`).

## Langkah-Langkah

1. Buka file `php.ini` yang digunakan oleh _server_ PHP Anda.
    - Pada XAMPP (Windows): `C:\xampp\php\php.ini`
    - Pada MAMP (Mac): `/Applications/MAMP/bin/php/php[versi]/conf/php.ini`
    - Pada Ubuntu/Linux (Apache): `/etc/php/[versi]/apache2/php.ini`
    - Pada Ubuntu/Linux (Nginx/FPM): `/etc/php/[versi]/fpm/php.ini`
    - Pada Laravel Herd (Mac): Buka pengaturan Herd, lalu klik **php.ini** pada versi PHP yang aktif.

2. Cari variabel-variabel berikut dan perbarui nilainya. Pastikan hierarki nilainya: `memory_limit` > `post_max_size` > `upload_max_filesize`.

    ```ini
    ; Ukuran maksimal satu file yang boleh diunggah.
    ; Jika ingin memperbolehkan unggahan hingga 10MB, set minimal 10M.
    upload_max_filesize = 20M

    ; Ukuran total maksimal data dari request POST (termasuk semua file).
    ; Nilainya harus lebih besar atau sama dengan upload_max_filesize.
    post_max_size = 25M

    ; Batas memori maksimum yang boleh dikonsumsi oleh satu script PHP.
    ; Nilainya harus lebih besar dari post_max_size.
    memory_limit = 128M

    ; Waktu maksimal (dalam detik) server diizinkan mengeksekusi script.
    ; Naikkan nilai ini agar server tidak timeout jika waktu unggah (upload time) butuh proses yang lama akibat ukuran file yang besar.
    max_execution_time = 120

    ; Waktu maksimal (dalam detik) script diizinkan menerima data dari input, seperti proses upload.
    max_input_time = 120
    ```

3. Simpan file `php.ini`.

4. **Restart** _web server_ atau _service_ PHP Anda:
    - Apache/XAMPP: Klik _Stop_ lalu _Start_ pada modul Apache.
    - Nginx/FPM: `sudo systemctl restart php[versi]-fpm`
    - Laravel Herd: Klik _Stop All_ dan _Start All_ pada menu bar Herd.

## Catatan Penting

- Jangan lupa untuk menyesuaikan validasi pada Form Request atau di _controller_ Laravel Anda. Misalnya:
    ```php
    'file' => 'required|file|max:6144', // max dalam satuan Kilobyte (6MB = 6144 KB)
    ```
- Jika menggunakan _proxy server_ seperti **Nginx** di _environment production_, Anda juga wajib mengubah limit di konfigurasi Nginx (`nginx.conf` atau konfigurasi _server block_ `.conf`), jika tidak file akan tetap ditolak sebelum mencapai PHP:
    ```nginx
    server {
        ...
        client_max_body_size 25M;
        ...
    }
    ```
