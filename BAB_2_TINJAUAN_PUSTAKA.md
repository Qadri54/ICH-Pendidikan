# BAB 2 — TINJAUAN PUSTAKA

> Disusun mengikuti struktur & gaya penulisan laporan akhir referensi (Sita Adila), disesuaikan
> dengan topik laporan akhir Anda: **Rancang Bangun Integrated Management System (IMS) pada
> Lembaga Pendidikan IQRA' Creative House Berbasis Web**.
>
> Catatan format (lihat pedoman POLMED): istilah asing dimiringkan, sitasi nama–tahun, bentuk pasif
> ("penulis"). Sitasi yang sudah pasti diisi dari folder `referensi/` Anda. Subbab yang masih
> memerlukan sumber (konsep dasar umum) ditandai dengan **(……)** — silakan lengkapi; lihat daftar di
> bagian akhir berkas ini.

---

## 2.1 Penelitian Terdahulu

Dalam penulisan laporan akhir ini, penulis memperoleh banyak sumber informasi dari berbagai
penelitian terdahulu yang berkaitan dengan topik yang diangkat. Berikut adalah sejumlah penelitian
yang berkaitan dengan laporan akhir penulis.

**Tabel 2.1 Penelitian Terdahulu**

| No | Nama Peneliti, Tahun, Judul | Hasil Penelitian | Persamaan dan Perbedaan |
|----|------------------------------|------------------|--------------------------|
| 1 | Rosidin, M., Haedar Z. H., M., & Sulistyo, W. Y. (2025). *Perancangan Sistem Informasi Manajemen Asosiasi dengan Metode Waterfall dan RBAC di Majelis Diktilitbang Muhammadiyah.* | Penelitian ini membangun sistem informasi manajemen asosiasi berbasis web menggunakan framework Laravel dan Filament dengan metode Waterfall. Sistem menerapkan *Role-Based Access Control* (RBAC) untuk mengatur hak akses pengguna sesuai perannya. Hasil pengujian *Black Box* menunjukkan sistem berjalan sesuai spesifikasi dan meningkatkan efisiensi serta keterstrukturan pengelolaan data. | **Persamaan:** sama-sama membangun sistem informasi manajemen berbasis web dengan Laravel, metode Waterfall, dan penerapan RBAC untuk manajemen hak akses pengguna. **Perbedaan:** penelitian ini berfokus pada manajemen asosiasi, sedangkan sistem yang dirancang penulis berfokus pada integrasi seluruh administrasi lembaga pendidikan (PPDB, keuangan, absensi, raport, dan tabungan siswa) dalam satu *Integrated Management System*. |
| 2 | Saputra, T. D., Abdullah, D., Khairullah, & Mahfuzhi, A. R. W. (2026). *Rancang Bangun Aplikasi Presensi Guru dengan Metode Global Positioning System (GPS) dan Foto Selfi Berbasis Web (Studi Kasus: SMP N 26 Seluma).* | Penelitian ini menghasilkan aplikasi presensi guru berbasis web yang memverifikasi kehadiran menggunakan GPS dan foto selfie real-time, dikembangkan dengan model Waterfall. Sistem terbukti meningkatkan akurasi dan efisiensi pencatatan kehadiran serta mengurangi manipulasi data absensi. | **Persamaan:** sama-sama menerapkan verifikasi kehadiran guru berbasis GPS dan foto selfie pada sistem berbasis web. **Perbedaan:** penelitian ini hanya menitikberatkan pada modul presensi guru, sedangkan sistem penulis menjadikan absensi guru berbasis *geofencing* sebagai salah satu dari beberapa modul yang terintegrasi dalam satu sistem manajemen lembaga pendidikan. |
| 3 | Sidiq, S., Kasidin, F. V., Fadhlullah, S. F., & Haryono, W. (2025). *Implementasi Sistem Aplikasi Pembayaran Sekolah dan Pendaftaran Siswa Berbasis Web.* | Penelitian ini membangun sistem pembayaran SPP dan pendaftaran siswa baru berbasis web pada sebuah Taman Kanak-Kanak (Raudhatul Athfal). Sistem memudahkan orang tua melakukan pembayaran dan pendaftaran secara daring serta meningkatkan transparansi dan akurasi data administrasi sekolah. | **Persamaan:** sama-sama mengembangkan modul pendaftaran siswa baru (PPDB) dan pembayaran biaya/SPP berbasis web pada lembaga pendidikan anak usia dini. **Perbedaan:** penelitian ini hanya mencakup pembayaran dan pendaftaran, sedangkan sistem penulis mengintegrasikan modul tersebut bersama absensi, raport digital, dan tabungan siswa dalam satu sistem dengan *Role-Based Access Control*. |
| 4 | Setiawan, A., & Maulana, H. (2025). *Implementasi Framework Laravel Sistem Informasi Pengelolaan dan Pengarsipan Raport Berbasis Web di SDN 2 Jayagiri.* | Penelitian ini mengimplementasikan sistem informasi pengelolaan dan pengarsipan raport berbasis web menggunakan framework Laravel dengan metode Waterfall dan pengujian *Black Box*. Sistem mempermudah pengelolaan nilai raport, pelacakan perkembangan akademik, serta menyimpan data secara terpusat dan terstruktur. | **Persamaan:** sama-sama mengembangkan modul pengelolaan raport/penilaian berbasis web dengan framework Laravel dan metode Waterfall. **Perbedaan:** penelitian ini berfokus pada pengelolaan dan pengarsipan raport, sedangkan sistem penulis menyajikan raport digital (penilaian naratif, checklist perkembangan, pengukuran fisik, dan kondisi kesehatan) sebagai bagian dari sistem manajemen terpadu lembaga PAUD. |
| 5 | Irfan, A., & Yuliana. (2022). *Sistem Informasi Tabungan Siswa Berbasis Web pada SDN 79 Enrekeng Kabupaten Soppeng.* | Penelitian ini menghasilkan sistem informasi tabungan siswa berbasis web yang menggantikan pencatatan buku manual. Sistem dikembangkan dengan metode Waterfall, dimodelkan dengan UML, dan diuji dengan *Black Box testing*, sehingga mempermudah pencatatan setoran, penarikan, dan laporan transaksi tabungan siswa. | **Persamaan:** sama-sama membangun modul tabungan siswa berbasis web untuk menggantikan pencatatan buku manual. **Perbedaan:** penelitian ini hanya berfokus pada pengelolaan tabungan siswa, sedangkan sistem penulis menjadikan tabungan siswa sebagai salah satu modul yang terintegrasi dengan modul administrasi lainnya dalam satu *Integrated Management System*. |

---

## 2.2 Landasan Teori

Dalam penelitian ini, penulis menyusun landasan teori yang didapat dari analisis terhadap berbagai
sumber literatur yang berkaitan dan relevan terhadap topik utama yang akan diteliti. Berikut adalah
landasan teori yang digunakan:

### 2.2.1 Rancang
Rancang adalah aktivitas yang bertujuan untuk merancang sistem baru yang dapat mengatasi masalah
yang dihadapi oleh pengguna, yang diperoleh dari pemilihan sistem terbaik (……). Rancang juga
merupakan serangkaian langkah untuk menerjemahkan hasil analisis ke dalam bentuk pemrograman agar
dapat diimplementasikan secara rinci (……). Dari uraian tersebut, dapat disimpulkan bahwa rancang
adalah langkah untuk merancang sistem baru berdasarkan analisis guna menyelesaikan masalah yang
dihadapi pengguna, yang kemudian dituangkan dalam format pemrograman agar dapat diterapkan secara
detail.

### 2.2.2 Bangun
Bangun adalah istilah yang berkaitan dengan pembangunan, yaitu kegiatan untuk menciptakan sistem
baru atau mengganti serta memperbaiki sistem yang sudah ada, baik secara keseluruhan maupun sebagian
(……). Berdasarkan pandangan tersebut, dapat disimpulkan bahwa bangun adalah kegiatan yang
berhubungan dengan menciptakan, mengganti, atau memperbaiki sistem yang sudah ada, baik secara
keseluruhan maupun sebagian.

### 2.2.3 Rancang Bangun
Rancang bangun merupakan tahap pembangunan sistem yang bertujuan untuk membuat sistem baru atau
melakukan penggantian serta perbaikan pada sistem yang sudah ada (……). Rancang bangun juga
merupakan proses yang mengubah hasil analisis sistem menjadi kode program dengan tujuan menjelaskan
secara mendetail cara setiap komponen diimplementasikan (……). Berdasarkan pendapat di atas, dapat
disimpulkan bahwa rancang bangun adalah proses pembangunan sistem yang mengubah hasil analisis
menjadi kode program untuk menciptakan, mengganti, atau memperbaiki sistem, serta menjelaskan secara
rinci cara kerja masing-masing bagian.

### 2.2.4 Website
*Website* merupakan suatu sistem yang menampilkan informasi dalam bentuk teks, gambar, audio, dan
sebagainya, yang disimpan di *server* internet dan ditampilkan dalam format *hypertext* (……).
*Website* juga merupakan sekumpulan halaman di internet yang menyediakan informasi tertentu sesuai
kebutuhan masing-masing pengguna (……). Berdasarkan pandangan tersebut, dapat disimpulkan bahwa
*website* adalah serangkaian halaman yang tersimpan di *server* internet dan ditampilkan dalam format
*hypertext*, yang menyajikan informasi dalam berbagai bentuk sesuai kebutuhan pengguna.

### 2.2.5 Sistem
Sistem adalah serangkaian elemen, komponen, atau variabel yang saling terhubung dan bekerja sama
untuk mencapai tujuan tertentu (……). Sistem juga merupakan sekumpulan elemen atau sub-sistem yang
saling berhubungan dan bekerja sama dengan baik untuk mencapai suatu tujuan (……). Berdasarkan
beberapa pendapat di atas, dapat disimpulkan bahwa sistem merupakan sekumpulan elemen atau komponen
yang saling terhubung dan bekerja sama untuk mencapai tujuan.

### 2.2.6 Informasi
Informasi adalah hasil dari pengolahan data yang disajikan dalam format yang jelas dan berguna bagi
penerimanya dalam pengambilan keputusan, baik untuk saat ini maupun masa depan (……). Informasi juga
merupakan produk pengolahan data dari satu atau beberapa sumber yang diproses untuk memberikan nilai,
makna, dan manfaat (……). Berdasarkan pendapat di atas, dapat disimpulkan bahwa informasi adalah
hasil pengolahan data yang memiliki nilai, makna, dan manfaat, serta disajikan secara jelas untuk
mendukung pengambilan keputusan.

### 2.2.7 Sistem Informasi
Sistem informasi adalah cara yang terorganisir untuk mengumpulkan, memasukkan, memproses, dan
menyimpan data, serta mengatur, mengendalikan, dan melaporkan informasi agar organisasi dapat
mencapai targetnya (……). Sistem informasi juga merupakan serangkaian kegiatan yang mencakup
pengumpulan, input, pengolahan, penyimpanan, pengendalian, dan pelaporan data sehingga menghasilkan
informasi yang mendukung proses pengambilan keputusan dalam organisasi (……). Berdasarkan uraian di
atas, dapat disimpulkan bahwa sistem informasi adalah serangkaian proses terorganisir yang mengubah
data menjadi informasi yang berguna untuk mendukung pengambilan keputusan dan pencapaian tujuan
organisasi.

### 2.2.8 Integrated Management System (IMS)
*Integrated Management System* (IMS) atau sistem manajemen terpadu adalah sebuah sistem yang
menggabungkan berbagai proses, prosedur, dan fungsi pengelolaan ke dalam satu kerangka kerja yang
terintegrasi sehingga dapat dikelola secara terpusat dan efisien (……). Dalam konteks lembaga
pendidikan, IMS berbasis web menyatukan berbagai modul administrasi — seperti pendaftaran peserta
didik, pengelolaan keuangan, pencatatan kehadiran, penilaian, dan tabungan siswa — ke dalam satu
platform terpadu, sehingga data antarbagian saling terhubung, mengurangi duplikasi pencatatan, dan
mempermudah pemantauan oleh pihak manajemen. Penyatuan proses administrasi sekolah ke dalam satu
sistem berbasis web terbukti meningkatkan efisiensi, transparansi, dan akurasi data serta mengurangi
ketergantungan pada proses manual berbasis kertas (Sidiq dkk., 2025). Berdasarkan uraian tersebut,
dapat disimpulkan bahwa *Integrated Management System* adalah sistem yang mengintegrasikan berbagai
proses pengelolaan organisasi ke dalam satu kerangka kerja terpusat guna meningkatkan efisiensi dan
keterpaduan pengelolaan data.

### 2.2.9 Lembaga Pendidikan Anak Usia Dini (PAUD)
Lembaga Pendidikan Anak Usia Dini (PAUD) merupakan satuan pendidikan yang menyelenggarakan program
pembelajaran bagi anak sejak lahir hingga usia enam tahun, yang ditujukan untuk membantu pertumbuhan
dan perkembangan jasmani serta rohani anak agar memiliki kesiapan dalam memasuki pendidikan lebih
lanjut (……). IQRA' Creative House (ICH) merupakan lembaga pendidikan anak usia dini yang
menyelenggarakan dua program, yaitu Taman Kanak-Kanak (TK) dan Mengaji. Sebagai lembaga PAUD, ICH
mengelola berbagai proses administrasi yang melibatkan banyak pihak, mulai dari pendaftaran siswa,
pengelolaan keuangan, kehadiran guru, penilaian dan penerbitan raport, hingga pengelolaan tabungan
siswa.

### 2.2.10 Penerimaan Peserta Didik Baru (PPDB)
Penerimaan Peserta Didik Baru (PPDB) merupakan proses penerimaan dan penyeleksian calon peserta didik
pada suatu satuan pendidikan untuk menjadi peserta didik pada tahun ajaran tertentu. PPDB berbasis web
dirancang untuk mengatasi permasalahan pada proses pendaftaran manual, seperti ketidakefisienan,
risiko kesalahan data, dan kesulitan dalam mengelola data dalam jumlah besar; sistem PPDB berbasis web
mampu memudahkan proses pendaftaran, mempercepat pengolahan data, serta meningkatkan aksesibilitas dan
ketepatan informasi bagi calon siswa dan orang tua (Syahidah dkk., 2024). Penerapan sistem pendaftaran
siswa secara daring juga memudahkan orang tua melakukan pendaftaran tanpa harus datang langsung ke
sekolah serta meningkatkan transparansi dan akurasi data administrasi (Sidiq dkk., 2025). Berdasarkan
uraian tersebut, dapat disimpulkan bahwa PPDB online adalah proses pendaftaran peserta didik baru yang
dilakukan secara daring melalui sistem berbasis web guna meningkatkan efisiensi, transparansi, dan
ketepatan pengelolaan data pendaftaran.

### 2.2.11 Pembayaran SPP dan Keuangan Sekolah
Sumbangan Pembinaan Pendidikan (SPP) merupakan iuran rutin yang dibayarkan oleh peserta didik atau
orang tua kepada sekolah untuk mendukung pembiayaan penyelenggaraan pendidikan. Pengelolaan keuangan
sekolah yang masih mengandalkan pencatatan manual rentan terhadap kesalahan, kehilangan data, dan
laporan yang tidak akurat, sehingga diperlukan sistem informasi keuangan berbasis web yang dapat
mencatat transaksi secara otomatis, menyediakan pemantauan status pembayaran secara *real-time* bagi
siswa dan orang tua, serta membantu kepala sekolah dalam memantau laporan keuangan (Wirawan dkk.,
2024). Sistem pembayaran sekolah berbasis web juga mempermudah orang tua melakukan pembayaran secara
daring serta meningkatkan transparansi dan akurasi data administrasi sekolah (Sidiq dkk., 2025).
Berdasarkan uraian tersebut, dapat disimpulkan bahwa sistem pembayaran SPP dan keuangan sekolah
berbasis web adalah sistem yang mengelola pencatatan dan pelaporan pembayaran biaya pendidikan secara
digital guna meningkatkan efisiensi, transparansi, dan akurasi data keuangan sekolah.

### 2.2.12 Absensi (Presensi)
Absensi atau presensi merupakan kegiatan pencatatan kehadiran seseorang dalam suatu kegiatan atau
institusi yang menjadi dasar pemantauan disiplin dan aktivitas kerja. Sistem absensi manual yang masih
mengandalkan tanda tangan atau pencatatan konvensional memiliki kelemahan berupa ketidaktepatan
pencatatan, potensi kecurangan, dan kesulitan dalam pengelolaan data (Ngulum dkk., 2024). Oleh karena
itu, sistem absensi berbasis web dikembangkan untuk meningkatkan akurasi dan efisiensi proses
pencatatan kehadiran serta memudahkan pemantauan data secara *real-time* oleh pihak manajemen.
Berdasarkan uraian tersebut, dapat disimpulkan bahwa absensi adalah proses pencatatan kehadiran yang,
ketika diterapkan secara digital, dapat meningkatkan akurasi, efisiensi, dan akuntabilitas data
kehadiran.

### 2.2.13 Global Positioning System (GPS) dan Geofencing
*Global Positioning System* (GPS) adalah sistem navigasi berbasis satelit yang digunakan untuk
menentukan posisi geografis suatu objek di permukaan bumi berdasarkan koordinat lintang dan bujur.
*Geofencing* merupakan teknologi yang memanfaatkan data lokasi (*geolocation*) untuk membentuk batas
wilayah virtual (radius) tertentu, sehingga sistem dapat menentukan apakah suatu perangkat berada di
dalam atau di luar area yang telah ditetapkan. Penerapan teknologi *geolocation* yang dilengkapi foto
*real-time* pada sistem absensi guru terbukti dapat meningkatkan akurasi dan efisiensi proses absensi
serta mengurangi potensi kecurangan (Ngulum dkk., 2024). Pemanfaatan GPS untuk memverifikasi lokasi
guru saat melakukan presensi, yang dilengkapi foto selfie sebagai bukti visual kehadiran, juga terbukti
meningkatkan akurasi dan transparansi pencatatan kehadiran (Saputra dkk., 2026). Selain itu, integrasi
*geofencing* pada sistem presensi sekolah mampu memastikan presensi hanya dapat dilakukan di dalam area
yang sah (PucangAnom dkk., 2025). Berdasarkan uraian tersebut, dapat disimpulkan bahwa GPS dan
*geofencing* adalah teknologi penentuan dan pembatasan lokasi yang, pada sistem absensi, digunakan untuk
memastikan kehadiran dilakukan di lokasi yang sah sehingga meningkatkan akurasi dan keabsahan data
kehadiran.

### 2.2.14 Raport Digital
Raport merupakan dokumen laporan hasil belajar peserta didik yang memuat penilaian perkembangan dan
pencapaian peserta didik dalam suatu periode pembelajaran. Pengelolaan nilai raport yang masih
dilakukan secara manual menyulitkan pencarian data, berisiko hilang, dan memakan waktu serta tenaga
guru, sehingga diperlukan sistem informasi raport digital (e-raport) berbasis web yang dapat mengelola
dan menampilkan nilai raport secara lebih efisien serta mempermudah pelacakan perkembangan akademik
(Setiawan & Maulana, 2025). Sistem e-raport berbasis web juga mempermudah sekolah dalam mengelola data
nilai siswa dan menyampaikan informasi kepada guru, siswa, serta orang tua (Effendi & Sulaksono, 2021).
Berdasarkan uraian tersebut, dapat disimpulkan bahwa raport digital adalah sistem pengelolaan laporan
hasil belajar peserta didik berbasis web yang meningkatkan efisiensi penilaian, mempermudah pelacakan
perkembangan akademik, dan memperluas akses informasi bagi pihak yang berkepentingan.

### 2.2.15 Tabungan Siswa
Tabungan siswa merupakan program penyimpanan dana yang dikelola oleh sekolah untuk membiasakan peserta
didik menabung sejak dini, yang umumnya mencatat data setoran, penarikan, dan saldo siswa. Sistem
tabungan siswa yang masih menggunakan buku dalam pencatatan membutuhkan waktu yang lama, berisiko
hilang atau rusak, serta memerlukan tempat penyimpanan khusus, sehingga diperlukan sistem informasi
tabungan siswa berbasis web yang dapat membantu pengelolaan tabungan secara komputerisasi (Irfan &
Yuliana, 2022). Berdasarkan uraian tersebut, dapat disimpulkan bahwa tabungan siswa berbasis web adalah
sistem pencatatan transaksi tabungan peserta didik secara digital yang meningkatkan keamanan,
ketertiban, dan transparansi pengelolaan tabungan dibandingkan pencatatan buku manual.

### 2.2.16 Role-Based Access Control (RBAC)
*Role-Based Access Control* (RBAC) merupakan model keamanan yang membatasi akses pengguna berdasarkan
peran (*role*) yang diberikan dalam suatu organisasi, sehingga hanya pengguna dengan hak akses tertentu
yang dapat melakukan tindakan sesuai perannya (Putrawan & Harahap, 2024). RBAC telah menjadi pendekatan
utama dalam meningkatkan keamanan data pada berbagai sistem informasi karena mampu mengelola hak akses
pengguna secara terstruktur sesuai kebutuhan organisasi (Sahyudi & Susanto, 2025). Penerapan RBAC pada
sistem informasi berbasis web juga digunakan untuk mengatur hak akses pengguna sesuai perannya
masing-masing (Rosidin dkk., 2025). Berdasarkan uraian tersebut, dapat disimpulkan bahwa RBAC adalah
model pengendalian akses yang membatasi hak pengguna berdasarkan peran yang dimilikinya, sehingga setiap
pengguna hanya dapat mengakses fitur dan data yang sesuai dengan kewenangannya dalam sistem.

### 2.2.17 Metode Waterfall
Metode *Waterfall* merupakan pendekatan dalam pengembangan perangkat lunak yang mengedepankan proses
kerja sistem secara berurutan atau linear, di mana setiap tahap harus diselesaikan sebelum melanjutkan
ke tahap berikutnya (……). Tahapan metode *Waterfall* secara umum meliputi analisis kebutuhan, desain
sistem, implementasi, pengujian, dan pemeliharaan. Metode ini banyak digunakan dalam pengembangan
sistem informasi berbasis web di lingkungan pendidikan karena pendekatannya yang sistematis dan
terstruktur, sebagaimana diterapkan pada pembangunan sistem informasi PPDB (Syahidah dkk., 2024),
sistem informasi presensi guru (Saputra dkk., 2026), serta sistem informasi manajemen berbasis RBAC
(Rosidin dkk., 2025). Berdasarkan uraian tersebut, dapat disimpulkan bahwa metode *Waterfall* adalah
metode pengembangan perangkat lunak yang dilakukan secara berurutan melalui tahapan analisis kebutuhan,
desain, implementasi, pengujian, dan pemeliharaan.

> **Gambar 2.1 Tahapan Metode Waterfall** — *(sisipkan gambar bagan waterfall; Sumber: ……)*

### 2.2.18 Framework
*Framework* adalah sekumpulan perintah yang disusun dalam *class* dan *function* yang memiliki perannya
masing-masing untuk memudahkan pengembang dalam mengaksesnya tanpa perlu menulis sintaks program yang
sama berulang kali, sehingga dapat menghemat waktu (……). Berdasarkan pendapat di atas, dapat
disimpulkan bahwa *framework* adalah kumpulan *class* dan *function* yang dapat digunakan kembali untuk
mempermudah dan mempercepat proses pengembangan aplikasi.

### 2.2.19 Model-View-Controller (MVC)
*Model-View-Controller* (MVC) adalah sebuah konsep arsitektur perangkat lunak yang memisahkan
pengelolaan data (*model*), proses pengaturan (*controller*), dan tampilan antarmuka (*view*) (……).
Terdapat tiga komponen utama yang membentuk pola MVC, yaitu:
1. *Model*, yang umumnya terhubung langsung dengan basis data untuk melakukan manipulasi data (menambah,
   memperbarui, menghapus, mencari) serta menangani validasi.
2. *View*, yaitu elemen yang mengelola *presentation logic*, umumnya berupa berkas *template* yang
   ditampilkan kepada pengguna.
3. *Controller*, yang mengatur interaksi antara *model* dan *view*, menerima permintaan dari pengguna,
   lalu menentukan langkah yang akan diambil oleh aplikasi.

### 2.2.20 Laravel
Laravel merupakan sebuah *framework website* yang bersifat *open-source* dan gratis, yang dikembangkan
untuk membangun aplikasi *website* dengan menggunakan pola *Model-View-Controller* (MVC) (……). Pada
penelitian ini, framework Laravel dipilih sebagai kerangka kerja utama dalam membangun sistem; pemilihan
Laravel sejalan dengan berbagai penelitian sistem informasi di lingkungan pendidikan yang juga
memanfaatkan Laravel, seperti pada sistem informasi akademik (Suwirmayanti dkk., 2023), sistem informasi
e-raport (Setiawan & Maulana, 2025; Effendi & Sulaksono, 2021), serta sistem informasi manajemen
berbasis RBAC (Rosidin dkk., 2025). Berdasarkan uraian tersebut, dapat disimpulkan bahwa Laravel adalah
*framework* PHP berbasis pola MVC yang bersifat *open-source* dan banyak digunakan dalam pengembangan
sistem informasi berbasis web.

### 2.2.21 PHP
PHP (*PHP: Hypertext Preprocessor*) adalah bahasa pemrograman *script server-side* yang terintegrasi
dengan HTML dan digunakan untuk membangun halaman *website* yang bersifat dinamis (……). Berdasarkan
pendapat di atas, dapat disimpulkan bahwa PHP adalah bahasa pemrograman *server-side* yang bersifat
*open source* dan dirancang untuk pengembangan web dinamis.

### 2.2.22 Database
*Database* merupakan sekumpulan data yang tersimpan secara teratur di dalam komputer sehingga dapat
dikelola oleh program komputer untuk memperoleh informasi (……). *Database* dikelola menggunakan sistem
manajemen basis data (*Database Management System* / DBMS) yang memungkinkan pembuatan, pemeliharaan,
pencarian, dan pengaksesan data. Berdasarkan uraian di atas, dapat disimpulkan bahwa *database* adalah
sistem terorganisir yang menyimpan data secara digital sehingga dapat dikelola dan diakses dengan mudah
menggunakan DBMS.

### 2.2.23 MySQL
MySQL merupakan salah satu jenis *server* basis data (*database*) yang sangat populer dan menggunakan
bahasa SQL untuk mengakses datanya (……). MySQL dapat dijalankan di berbagai sistem operasi dan banyak
digunakan sebagai basis data pada sistem informasi berbasis web. Berdasarkan uraian tersebut, dapat
disimpulkan bahwa MySQL adalah perangkat lunak sistem manajemen basis data berbasis SQL yang digunakan
untuk menyimpan dan mengelola data pada sistem berbasis web.

### 2.2.24 Blackbox Testing
*Black Box testing* merupakan metode pengujian yang menitikberatkan pada kebutuhan fungsional sebuah
aplikasi tanpa melihat struktur internal atau kode program (……). Sasaran pengujian *Black Box* adalah
untuk memperlihatkan bagaimana suatu fungsi dalam aplikasi beroperasi serta memeriksa apakah keluaran
yang dihasilkan sesuai dengan harapan ketika diberikan suatu masukan. Metode *Black Box testing* banyak
digunakan untuk menguji sistem informasi berbasis web, sebagaimana diterapkan pada beberapa penelitian
terdahulu (Rosidin dkk., 2025; Setiawan & Maulana, 2025; Irfan & Yuliana, 2022). Berdasarkan uraian
tersebut, dapat disimpulkan bahwa *Black Box testing* adalah metode pengujian yang berfokus pada
kesesuaian fungsi sistem dengan kebutuhan pengguna berdasarkan masukan dan keluaran.

### 2.2.25 Unified Modeling Language (UML)
*Unified Modeling Language* (UML) adalah suatu representasi visual yang sering dipakai untuk
mendeskripsikan sistem yang berorientasi objek, dan banyak digunakan pengembang (dalam bentuk diagram)
untuk merancang sistem (……). Pada penelitian ini, UML digunakan untuk memodelkan rancangan sistem.
Beberapa jenis diagram UML yang digunakan adalah sebagai berikut.

**1. Use Case Diagram**
*Use Case Diagram* merupakan jenis diagram UML yang digunakan untuk menunjukkan hubungan antara berbagai
aktor (baik pengguna maupun sistem dari luar) dengan sebuah sistem, serta memperlihatkan cara pengguna
berinteraksi dengan sistem untuk mencapai tujuan tertentu (……). Berikut simbol-simbol *use case
diagram*.

**Tabel 2.2 Simbol-simbol Use Case Diagram**

| No | Nama | Keterangan |
|----|------|------------|
| 1 | *Actor* | Menspesifikasikan kumpulan peran yang dijalankan pengguna saat berinteraksi dengan *use case*. |
| 2 | *Dependency* | Hubungan di mana perubahan pada elemen mandiri (*independent*) memengaruhi elemen yang bergantung padanya. |
| 3 | *Generalization* | Hubungan di mana objek turunan (*descendent*) berbagi struktur dan perilaku dari objek induk (*ancestor*). |
| 4 | *Include* | Menspesifikasikan bahwa *use case* asal secara jelas memuat *use case* lain. |
| 5 | *Extend* | Menspesifikasikan bahwa *use case* tujuan memperluas perilaku *use case* asal pada kondisi tertentu. |
| 6 | *Association* | Menghubungkan satu objek dengan objek lainnya. |
| 7 | *System* | Menspesifikasikan paket yang menampilkan sistem dengan batasan tertentu. |
| 8 | *Use Case* | Urutan tindakan yang ditunjukkan sistem dan menghasilkan hasil terukur bagi seorang aktor. |

*(Sumber: ……)*

**2. Activity Diagram**
*Activity Diagram* adalah jenis diagram UML yang berfungsi untuk menunjukkan alur kerja atau aktivitas
dalam sebuah sistem atau proses (……). Berikut simbol-simbol *activity diagram*.

**Tabel 2.3 Simbol-simbol Activity Diagram**

| No | Nama | Keterangan |
|----|------|------------|
| 1 | *Activity* | Menggambarkan aktivitas/cara kerja, diisi dengan kata kerja; satu alur masuk dan satu alur keluar. |
| 2 | *State Transition* | Menambahkan transisi antara satu *activity* dengan *activity* lainnya. |
| 3 | *Start State* | Menunjukkan tempat proses kerja dimulai. |
| 4 | *End State* | Menunjukkan tempat proses kerja berakhir. |
| 5 | *Decision* | Menggambarkan keputusan atau tindakan yang perlu diambil pada situasi tertentu. |
| 6 | *Fork* / Percabangan | Satu alur yang menghasilkan dua atau lebih *activity* yang dikerjakan bersamaan. |
| 7 | *Join* / Penggabungan | Beberapa alur bergabung untuk melanjutkan *activity*. |
| 8 | *Swimlane* | Metode untuk mengelompokkan *activity* berdasarkan aktor. |

*(Sumber: ……)*

**3. Class Diagram**
*Class Diagram* adalah jenis diagram UML yang berfungsi untuk menunjukkan struktur statis dari sebuah
sistem atau aplikasi yang berbasis objek (……). Berikut simbol-simbol *class diagram*.

**Tabel 2.4 Simbol-simbol Class Diagram**

| No | Nama | Keterangan |
|----|------|------------|
| 1 | *Class* | Kelas pada struktur sistem. |
| 2 | *Association* | Hubungan antar kelas dengan pengertian umum, sering disertai *multiplicity*. |
| 3 | *Generalization* | Hubungan antar kelas dengan pengertian generalisasi–spesialisasi (umum–khusus). |
| 4 | *Aggregation* | Hubungan antar kelas dengan pengertian bagian dari keseluruhan (*whole-part*). |
| 5 | *Composition* | Hubungan ketika sebuah kelas tidak bisa berdiri sendiri dan harus menjadi bagian dari kelas lain. |
| 6 | *Dependency* | Hubungan antar kelas dengan pengertian ketergantungan. |

*(Sumber: ……)*

---

## Catatan Pengerjaan & Daftar Sitasi

### A. Sitasi yang sudah terisi (sumber dari folder `referensi/` Anda)
Sumber-sumber berikut sudah dirujuk dengan benar di dalam BAB 2 dan **wajib** dimasukkan ke Daftar
Pustaka (format APA):

1. Effendi, D. C., & Sulaksono, A. G. (2021). Implementasi *Framework* Laravel pada Perancangan Sistem Informasi E-Raport Berbasis Web SD Negeri 1 Kartoharjo. *Jurnal Inovasi Teknik dan Edukasi Teknologi*, 1(11), 815–822.
2. Irfan, A., & Yuliana. (2022). Sistem Informasi Tabungan Siswa Berbasis Web pada SDN 79 Enrekeng Kabupaten Soppeng. *Jurnal Ilmiah Sistem Informasi dan Teknik Informatika (JISTI)*, 5(1).
3. Ngulum, M. B., Arif, A. I., & Hernawan, S. R. (2024). Implementasi Teknologi *Geolocation* dan Foto *Realtime* untuk Optimalisasi Sistem Absensi Guru di MI Nurul Huda. *Jurnal Ilmu Komputer dan Sistem Informasi (JIKOMSI)*, 7(2), 341–348.
4. PucangAnom, I. D. G. A., Saskara, G. A. J., & Seputra, K. A. (2025). Penerapan Metode RAD serta Teknologi *Geofencing* dan *Face Recognition* pada Sistem Presensi Studi Kasus: SD Saraswati 4 Denpasar. *JITET (Jurnal Informatika dan Teknik Elektro Terapan)*, 14(2). *(verifikasi tahun terbit)*
5. Putrawan, & Harahap, A. M. (2024). Implementasi Metode *Role-Based Access Control* pada Aplikasi E-Raport di MIN 15 Langkat Berbasis Android. *Jurnal Teknik Informatika Unika St. Thomas (JTIUST)*, 9(1), 107–…
6. Rosidin, M., Haedar Z. H., M., & Sulistyo, W. Y. (2025). Perancangan Sistem Informasi Manajemen Asosiasi dengan Metode *Waterfall* dan RBAC di Majelis Diktilitbang Muhammadiyah. *JTEKSIS*, 7(3), 401–409.
7. Sahyudi, M., & Susanto, E. R. (2025). Analisis Implementasi Sistem Keamanan Basis Data Berbasis *Role-Based Access Control* (RBAC) pada Aplikasi *Enterprise Resource Planning*. 5(1), 105–116.
8. Saputra, T. D., Abdullah, D., Khairullah, & Mahfuzhi, A. R. W. (2026). Rancang Bangun Aplikasi Presensi Guru dengan Metode *Global Positioning System* (GPS) dan Foto Selfi Berbasis Web (Studi Kasus: SMP N 26 Seluma). *JUSTIKPEN*, 5(2), 252–259.
9. Setiawan, A., & Maulana, H. (2025). Implementasi *Framework* Laravel Sistem Informasi Pengelolaan dan Pengarsipan Raport Berbasis Web di SDN 2 Jayagiri. *JNKTI*, 8(2), 856–…
10. Sidiq, S., Kasidin, F. V., Fadhlullah, S. F., & Haryono, W. (2025). Implementasi Sistem Aplikasi Pembayaran Sekolah dan Pendaftaran Siswa Berbasis Web. *Switch: Jurnal Sains dan Teknologi Informasi*, 3(1), 27–36.
11. Suwirmayanti, N. L. G. P., Permana, P. A. G., Prayoga, P. A. A., Sukerti, N. K., & Hadi, R. (2023). Implementasi *Framework* Laravel pada Sistem Informasi Akademik SMA Negeri 1 Kediri Berbasis Web. *JNKTI*, 6(3), 260–267.
12. Syahidah, H., Irsandi, N., & Fadilah, R. N. (2024). Rancang Bangun Sistem Informasi Penerimaan Siswa Baru Menggunakan Metode *Waterfall*. *IJIRSE*, 4(2), 139–145.
13. Wirawan, I. K., Srirahayu, A., & Sopingi. (2024). Rancang Bangun Sistem Informasi Keuangan Sekolah Berbasis Website. *JTEKSIS*, 6(4), 639–648.

> Catatan: lengkapi nomor halaman akhir yang masih bertanda "…" dan verifikasi tahun terbit JITET
> (PucangAnom dkk.) langsung pada berkas PDF-nya. Tambahkan nomor halaman pada sitasi dalam teks bila
> mengutip langsung (mis. (Saputra dkk., 2026:255)) sesuai pedoman.

### B. Subbab yang MASIH perlu sumber (tanda **(……)**)
Konsep-konsep dasar berikut belum punya sumber dari folder Anda dan **harus Anda lengkapi sitasinya**
(cari 1–2 jurnal/buku per konsep, jangan menyalin sitasi dari laporan Sita karena akan terdeteksi
plagiarisme):

- 2.2.1 Rancang · 2.2.2 Bangun · 2.2.3 Rancang Bangun
- 2.2.4 Website · 2.2.5 Sistem · 2.2.6 Informasi · 2.2.7 Sistem Informasi
- 2.2.8 IMS · 2.2.9 Lembaga PAUD
- 2.2.17 Metode Waterfall (definisi umum) · 2.2.18 Framework · 2.2.19 MVC · 2.2.20 Laravel (definisi umum)
- 2.2.21 PHP · 2.2.22 Database · 2.2.23 MySQL · 2.2.24 Blackbox · 2.2.25 UML (definisi & sumber tabel simbol)

> Pedoman POLMED mensyaratkan **minimal 10 sumber** dalam 5 tahun terakhir dan **wajib mensitasi
> artikel dosen Politeknik Negeri Medan** yang relevan — pastikan terpenuhi setelah subbab di atas
> dilengkapi.

### C. Saran teknis
- Subbab metode pengembangan: laporan Anda memakai **Waterfall** (sesuai abstrak), jadi BAB 2 ini sudah
  memuat *Waterfall* (2.2.17), bukan SES seperti laporan Sita. Jika di Bab 3 Anda hanya memakai
  Waterfall, cukup 2.2.17 saja; tidak perlu membahas Prototype/Agile.
- Sesuaikan **urutan & penomoran** subbab dengan Daftar Isi setelah final.
- Untuk Gambar 2.1 (bagan Waterfall) dan tabel simbol UML, sisipkan gambar dan cantumkan sumbernya.
