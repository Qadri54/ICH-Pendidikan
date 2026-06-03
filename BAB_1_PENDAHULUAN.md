# BAB 1 — PENDAHULUAN

## 1.1 Latar Belakang

IQRA' Creative House (ICH) merupakan lembaga pendidikan anak usia dini yang menyelenggarakan dua
program pendidikan, yaitu TK (Taman Kanak-Kanak) dan Mengaji. Sebagai lembaga pendidikan, ICH
mengelola berbagai proses administrasi yang melibatkan banyak pihak dan saling berkaitan.
Kompleksitas proses tersebut menuntut pengelolaan data yang cepat, akurat, dan terpusat, mulai dari
pendaftaran siswa baru, pengelolaan keuangan seperti pembayaran SPP dan biaya pendaftaran, pencatatan
kehadiran guru, penilaian dan penerbitan raport, hingga pengelolaan program tabungan siswa. Namun,
seluruh proses tersebut saat ini masih dilaksanakan secara manual, mulai dari pencatatan kehadiran
guru di buku absensi, pengelolaan pembayaran SPP dengan kartu dan buku besar, hingga penyusunan
raport siswa yang membutuhkan waktu lama dan rawan kesalahan. Kondisi inilah yang menjadikan
kebutuhan akan sistem pengelolaan yang terintegrasi menjadi mendesak bagi ICH.

Ketergantungan pada proses manual tersebut menimbulkan beberapa permasalahan nyata di ICH. Proses
pendaftaran siswa baru mengharuskan calon siswa dan orang tua datang langsung ke sekolah, mengisi
formulir kertas, dan menunggu konfirmasi yang memakan waktu lama, sehingga meningkatkan risiko
kesalahan pencatatan. Pada aspek keuangan, pencatatan pembayaran SPP secara manual dengan buku besar
rentan terhadap kesalahan, kehilangan data, dan tidak dapat memberikan informasi status pembayaran
secara *real-time* kepada orang tua siswa. Sistem absensi guru yang masih mengandalkan buku tanda
tangan konvensional tidak dapat memverifikasi lokasi kehadiran secara akurat, membuka peluang
kecurangan seperti titip absen, dan tidak dapat dimonitor secara *real-time* oleh pihak manajemen
(Saputra dkk., 2026). Pengelolaan nilai raport siswa yang masih dilakukan secara manual juga memakan
waktu dan tenaga yang besar bagi guru, serta menyulitkan orang tua dalam memantau perkembangan
akademik anak mereka sewaktu-waktu. Begitu pula dengan program tabungan siswa yang masih dicatat di
buku tabungan fisik, yang berpotensi hilang, rusak, dan tidak memberikan transparansi kepada orang
tua terkait saldo dan riwayat transaksi tabungan anak mereka.

Gabungan dari seluruh permasalahan tersebut menyebabkan rendahnya efisiensi operasional dan kurangnya
transparansi dalam pengelolaan data di ICH. Ketergantungan pada sistem manual membuat pengelola
sekolah kesulitan dalam mengambil keputusan secara cepat dan akurat, serta menyulitkan koordinasi
antar pihak yang terlibat seperti guru, orang tua, dan manajemen yayasan. Apabila kondisi ini
dibiarkan, beban administrasi akan semakin berat sehingga kualitas pelayanan lembaga berisiko
menurun. Oleh karena itu, diperlukan suatu solusi yang mampu menyatukan seluruh proses administrasi
tersebut ke dalam satu sistem yang terdigitalisasi dan terpusat.

Seiring dengan kebutuhan tersebut, pemanfaatan teknologi informasi merupakan langkah strategis yang
sangat diperlukan. *Website* adalah salah satu bentuk teknologi yang paling umum digunakan saat ini
karena kemampuannya dalam menyampaikan informasi dengan cepat, efisien, dan dapat diakses kapan saja
serta dari mana saja melalui perangkat yang terhubung dengan internet. Sistem informasi berbasis
*website* telah terbukti efektif dalam menyajikan data secara *real-time*, menghilangkan hambatan
ruang dan waktu, serta memberikan akses yang luas bagi para pengguna (Suwirmayanti dkk., 2023). Dengan
mempertimbangkan hal tersebut, perancangan dan pembangunan sebuah *Integrated Management System* (IMS)
berbasis web untuk ICH merupakan solusi yang tepat agar seluruh proses administrasi dapat dilakukan
secara terdigitalisasi, terpusat, dan mudah diakses oleh semua pihak yang berkepentingan.

Sistem yang dirancang mencakup modul pendaftaran siswa baru (PPDB) secara *online*, pengelolaan
keuangan sekolah meliputi pembayaran SPP dan biaya pendaftaran, sistem absensi guru berbasis GPS dan
foto *selfie* dengan validasi *geofencing* untuk memastikan kehadiran dilakukan di lokasi yang sah,
absensi siswa, sistem raport digital, pengelolaan tabungan siswa, serta manajemen data master. Sistem
absensi guru menggunakan teknologi *geolocation* yang memverifikasi posisi GPS guru pada saat
melakukan presensi dan membandingkannya dengan radius yang telah dikonfigurasi oleh admin
(*geofencing*), sehingga hanya presensi yang dilakukan di dalam area sekolah yang dapat diterima oleh
sistem. Foto *selfie real-time* juga diambil sebagai bukti visual kehadiran untuk mencegah manipulasi
data absensi (Saputra dkk., 2026).

Dalam aspek keamanan dan pengelolaan hak akses, sistem ini menerapkan metode *Role-Based Access
Control* (RBAC) menggunakan *framework* Laravel. RBAC merupakan model keamanan yang membatasi akses
pengguna berdasarkan peran (*role*) yang diberikan dalam organisasi, sehingga setiap pengguna hanya
dapat mengakses fitur dan data yang sesuai dengan kewenangannya. Sistem ini mengakomodasi enam peran
pengguna, yaitu Admin, Kepala Sekolah, Kepala Yayasan, Guru, Guru Ngaji, dan Orang Tua, yang
masing-masing memiliki hak akses berbeda sesuai tanggung jawabnya. Metode pengembangan yang digunakan
adalah metode *Waterfall*, yang memberikan pendekatan pengembangan sistematis dan terurut sehingga
memudahkan perencanaan, implementasi, serta pemeliharaan sistem (Rosidin dkk., 2025).

Berdasarkan uraian di atas, penulis memilih judul **"Rancang Bangun *Integrated Management System*
(IMS) Pada Lembaga Pendidikan IQRA' Creative House Berbasis Web"**. Hal ini bertujuan untuk
menyelesaikan permasalahan yang telah diidentifikasi sebelumnya, serta hasil dari penelitian ini
diharapkan mampu memberikan solusi nyata terhadap masalah pengelolaan administrasi pendidikan yang ada
di ICH, sekaligus menjadi bentuk kontribusi dalam mendukung transformasi digital di lingkungan
lembaga pendidikan anak usia dini.
