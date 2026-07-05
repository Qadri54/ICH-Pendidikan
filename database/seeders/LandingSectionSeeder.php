<?php

namespace Database\Seeders;

use App\Models\LandingSection;
use Illuminate\Database\Seeder;

class LandingSectionSeeder extends Seeder
{
    public function run(): void
    {
        LandingSection::truncate();

        LandingSection::create([
            'key' => 'hero',
            'title' => 'Hero Banner',
            'order' => 1,
            'content' => [
                'badge' => 'PG/TK Plus · Hafidz, Arab, English, Jepang, Mandarin',
                'title' => 'Smart dan Religius',
                'subtitle' => "Menyiapkan Generasi Era Digital Berakhlak Mulia Dari Usia Dini. IQRA' Creative House membimbing anak-anak dengan ilmu, akhlak, dan kreativitas.",
                'image' => 'images/hero-students.jpg',
                'stats' => [
                    ['icon' => 'book', 'title' => "Hafidz & Al-Qur'an", 'subtitle' => 'Tahsin, Tahfidz & Bahasa Arab'],
                    ['icon' => 'school', 'title' => '5 Bahasa', 'subtitle' => 'Arab, English, Jepang, Mandarin'],
                    ['icon' => 'users', 'title' => 'Smart & Religius', 'subtitle' => 'Pendidikan Akhlak Mulia'],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'tentang',
            'title' => 'Tentang Kami',
            'order' => 2,
            'content' => [
                'label' => 'Tentang Kami',
                'title' => "Smart dan Religius\nSejak Usia Dini",
                'description' => "IQRA' Creative House adalah PG/TK Plus yang mengintegrasikan program Hafidz, Bahasa Arab, English, Jepang, dan Mandarin. Kami menyiapkan generasi era digital yang berakhlak mulia dari usia dini.",
                'image' => 'images/activity-2.jpg',
                'badge_number' => '3',
                'badge_text' => "Angkatan Lulusan\nPG/TK Plus ICH",
                'features' => [
                    ['title' => 'Multilingual', 'description' => 'program 5 bahasa: Hafidz, Arab, English, Jepang, dan Mandarin sejak usia dini.'],
                    ['title' => 'Pendidikan akhlak mulia', 'description' => 'membentuk karakter islami melalui pembiasaan adab dan praktik langsung.'],
                    ['title' => 'Laporan perkembangan digital', 'description' => 'orang tua memantau kehadiran, pembayaran, dan raport anak secara real-time.'],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'struktur',
            'title' => 'Struktur Organisasi',
            'order' => 3,
            'content' => [
                'label' => 'Organisasi',
                'title' => 'Struktur Organisasi Yayasan',
                'subtitle' => "Struktur organisasi IQRA' Creative House yang mendukung pengelolaan pendidikan secara profesional dan terintegrasi.",
                'members' => [
                    ['id' => 1, 'parent_id' => null, 'position' => 'Pembina', 'name' => 'Antoni', 'photo' => null, 'type' => 'advisory', 'order' => 1],
                    ['id' => 2, 'parent_id' => null, 'position' => 'Ketua Yayasan', 'name' => 'Aulia, S.Si., M.Sc', 'photo' => null, 'type' => 'head', 'order' => 2],
                    ['id' => 3, 'parent_id' => null, 'position' => 'Pengawas', 'name' => 'Adzkia Safitri, A.Md.Kom', 'photo' => null, 'type' => 'advisory', 'order' => 3],
                    ['id' => 4, 'parent_id' => 1, 'position' => 'Kord. Maghrib Mengaji', 'name' => 'Novi Hariyanti', 'photo' => null, 'type' => 'default', 'order' => 1],
                    ['id' => 5, 'parent_id' => 2, 'position' => 'Kepala Sekolah TK', 'name' => 'Adli Qarin, S.S., M.Ikom', 'photo' => null, 'type' => 'default', 'order' => 1],
                    ['id' => 6, 'parent_id' => 2, 'position' => 'Direktur Training / R&B', 'name' => 'Prof. Dr. Ir. Roslina, M.I.T.', 'photo' => null, 'type' => 'default', 'order' => 2],
                    ['id' => 7, 'parent_id' => 4, 'position' => 'Guru Iqra', 'name' => 'Novi Hariyanti', 'photo' => null, 'type' => 'default', 'order' => 1],
                    ['id' => 8, 'parent_id' => 4, 'position' => 'Administrasi', 'name' => 'Rangga Alif Mulya', 'photo' => null, 'type' => 'default', 'order' => 2],
                    ['id' => 9, 'parent_id' => 4, 'position' => "Guru Al-Qur'an", 'name' => 'Yun Anggraini', 'photo' => null, 'type' => 'default', 'order' => 3],
                    ['id' => 10, 'parent_id' => 7, 'position' => null, 'name' => 'Mutiara Shahira A.Md.A.B.', 'photo' => null, 'type' => 'staff', 'order' => 1],
                    ['id' => 11, 'parent_id' => 7, 'position' => null, 'name' => 'Almira Salsabila', 'photo' => null, 'type' => 'staff', 'order' => 2],
                    ['id' => 12, 'parent_id' => 5, 'position' => 'Bendahara', 'name' => 'Almira Salsabila', 'photo' => null, 'type' => 'default', 'order' => 1],
                    ['id' => 13, 'parent_id' => 5, 'position' => 'Tata Usaha', 'name' => 'Rangga Alif Mulya', 'photo' => null, 'type' => 'default', 'order' => 2],
                    ['id' => 14, 'parent_id' => 12, 'position' => 'Kurikulum', 'name' => 'Mutiara Shahira A.Md.A.B.', 'photo' => null, 'type' => 'default', 'order' => 1],
                    ['id' => 15, 'parent_id' => 14, 'position' => 'Guru / Wali Kelas', 'name' => 'Sofia Aurora Susanto S.Pd', 'photo' => null, 'type' => 'staff', 'order' => 1],
                    ['id' => 16, 'parent_id' => 14, 'position' => 'Guru / Wali Kelas', 'name' => 'Lisma Farida Pane S.Pd.I', 'photo' => null, 'type' => 'staff', 'order' => 2],
                    ['id' => 17, 'parent_id' => 6, 'position' => 'Training', 'name' => 'Tim', 'photo' => null, 'type' => 'unit', 'order' => 1],
                    ['id' => 18, 'parent_id' => 6, 'position' => 'R & D', 'name' => 'Tim', 'photo' => null, 'type' => 'unit', 'order' => 2],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'program',
            'title' => 'Program Unggulan',
            'order' => 4,
            'content' => [
                'label' => 'Program Unggulan',
                'title' => 'Program Unggulan ICH',
                'subtitle' => 'Setiap program dirancang untuk membentuk generasi yang smart dan religius, siap menghadapi era digital dengan akhlak mulia.',
                'items' => [
                    ['title' => 'Hafidz', 'description' => "Program hafalan Al-Qur'an terstruktur dari Iqra hingga hafalan surat pendek, dengan bimbingan tajwid dan muroja'ah harian.", 'highlighted' => false],
                    ['title' => 'Bahasa Arab', 'description' => "Pengenalan kosakata dan percakapan dasar Bahasa Arab untuk membangun fondasi pemahaman Al-Qur'an sejak dini.", 'highlighted' => false],
                    ['title' => 'English', 'description' => 'Pembelajaran Bahasa Inggris melalui lagu, cerita, dan aktivitas interaktif yang menyenangkan bagi anak usia dini.', 'highlighted' => false],
                    ['title' => 'Bahasa Jepang', 'description' => 'Pengenalan bahasa dan budaya Jepang melalui permainan edukatif dan kosakata dasar yang mudah dipahami anak.', 'highlighted' => false],
                    ['title' => 'Bahasa Mandarin', 'description' => 'Pengenalan kosakata dan percakapan dasar Bahasa Mandarin untuk memperluas wawasan bahasa anak sejak dini.', 'highlighted' => false],
                    ['title' => 'Pendidikan Akhlak', 'description' => 'Membentuk karakter islami — adab terhadap orang tua, guru, teman, dan lingkungan — melalui kisah dan praktik langsung.', 'highlighted' => true],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'aktivitas',
            'title' => 'Kegiatan Belajar',
            'order' => 5,
            'content' => [
                'label' => 'Aktivitas',
                'title' => 'Kegiatan Belajar Anak',
                'subtitle' => "Potret keseharian santri IQRA' Creative House yang penuh semangat belajar dan berkreasi.",
                'items' => [
                    ['image' => 'images/images_ich/kegiatan1.jpeg', 'tag' => 'Hafidz', 'title' => 'Kegiatan Mengaji & Hafalan', 'meta' => 'Seluruh Kelas'],
                    ['image' => 'images/images_ich/kegiatan2.jpeg', 'tag' => 'Wisuda', 'title' => 'Wisuda & Pelepasan Angkatan 3', 'meta' => 'Tahun Ajaran 2025/2026'],
                    ['image' => 'images/activity-1.jpg', 'tag' => 'Arab', 'title' => 'Belajar Bahasa Arab', 'meta' => 'Kelas PG & TK'],
                    ['image' => 'images/activity-3.jpg', 'tag' => 'English', 'title' => 'Pembelajaran Bahasa Inggris', 'meta' => 'Kelas PG & TK'],
                    ['image' => 'images/activity-4.jpg', 'tag' => 'Akhlak', 'title' => 'Pendidikan Adab & Akhlak', 'meta' => 'Seluruh Kelas'],
                    ['image' => 'images/activity-2.jpg', 'tag' => 'Kreativitas', 'title' => 'Kegiatan Seni & Kreativitas', 'meta' => 'Seluruh Kelas'],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'testimoni',
            'title' => 'Testimoni',
            'order' => 6,
            'content' => [
                'label' => 'Testimoni',
                'title' => 'Kata Mereka',
                'items' => [
                    ['text' => 'Aku senang belajar di ICH. Ustadzahnya baik, aku bisa mengaji dan hafal doa. Belajarnya sambil main, jadi tidak bosan.', 'name' => 'Fatimah, 5 tahun', 'role' => 'Siswa TK A · 2025/2026', 'avatar' => null],
                    ['text' => 'Alhamdulillah, anak saya progress hafalannya luar biasa. Dalam 3 bulan sudah hafal 5 surat pendek. Ustadzahnya sabar dan penuh kasih.', 'name' => 'Ibu Siti Nurhaliza', 'role' => 'Orang Tua Siswa · Kelas TK B', 'avatar' => null],
                    ['text' => 'Sistem laporan orang tua sangat membantu. Saya bisa pantau kehadiran dan nilai anak langsung dari ponsel. ICH benar-benar inovatif!', 'name' => 'Bapak Rizal Ahmad', 'role' => 'Orang Tua Siswa · Kelas PG A', 'avatar' => null],
                ],
            ],
        ]);

        LandingSection::create([
            'key' => 'cta',
            'title' => 'Call to Action',
            'order' => 7,
            'content' => [
                'title' => "Daftarkan Putra-Putri Anda\nBersama Kami",
                'subtitle' => 'Pendaftaran dibuka Januari – Juli 2026, jam 08.00–12.00 WIB. Tempat terbatas — jangan sampai terlewat!',
                'whatsapp' => '6281360765971',
            ],
        ]);

        LandingSection::create([
            'key' => 'footer',
            'title' => 'Footer',
            'order' => 8,
            'content' => [
                'description' => "IQRA' Creative House adalah PG/TK Plus dengan program Hafidz, Arab, English, Jepang, dan Mandarin. Smart dan Religius.",
                'email' => 'ichsumut@gmail.com',
                'address' => 'Jl. Datuk Kabu Gg. Ridho No. 11E, Medan',
                'hours' => "Senin – Sabtu\n08.00 – 12.00 WIB",
                'phone' => '0813-6076-5971',
                'whatsapp' => '6281360765971',
                'copyright' => "IQRA' CREATIVE GROUP",
            ],
        ]);
    }
}
