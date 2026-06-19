<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Services\User\UserService;
use Illuminate\Database\Seeder;

class ParentStudentSeeder extends Seeder
{
    public function __construct(private UserService $userService) {}

    public function run(): void
    {
        foreach ($this->getData() as $parentData) {
            $user = $this->userService->createUser([
                'name'      => $parentData['name'],
                'email'     => $parentData['email'],
                'no_hp'     => $parentData['no_hp'],
                'password'  => 'password123',
                'status'    => 'active',
                'role_name' => 'Orang Tua',
            ]);

            foreach ($parentData['children'] as $child) {
                Student::create(array_merge($child, [
                    'user_id' => $user->user_id,
                    'status'  => $child['class_id'] ? 'aktif' : 'alumni',
                ]));
            }
        }
    }

    private function getData(): array
    {
        return [
            // === T.A 2023/2024 (siswa tanpa kelas — alumni) ===
            [
                'name'  => 'Andri Wijaya',
                'email' => 'andri.wijaya@iqra.com',
                'no_hp' => '081200000001',
                'children' => [
                    [
                        'nama_siswa'    => 'Aditya Wijaya',
                        'NIS'           => '2301',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-03-25',
                        'nama_ayah'     => 'Andri Wijaya',
                        'nama_ibu'      => 'Nurliana Kadri',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Binsar Pradana Wakos Sitompul',
                'email' => 'binsar.sitompul@iqra.com',
                'no_hp' => '081200000002',
                'children' => [
                    [
                        'nama_siswa'    => 'Aisyah Andhara Sitompul',
                        'NIS'           => '2302',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-09-18',
                        'nama_ayah'     => 'Binsar Pradana Wakos Sitompul',
                        'nama_ibu'      => 'Herwina',
                        'class_id'      => null,
                    ],
                    [
                        'nama_siswa'    => 'Adara Nurazzahra Sitompul',
                        'NIS'           => '2320',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-04-16',
                        'nama_ayah'     => 'Binsar Pradana Wakos Sitompul',
                        'nama_ibu'      => 'Erwina',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Achmad Ruzaqi',
                'email' => 'achmad.ruzaqi@iqra.com',
                'no_hp' => '081200000003',
                'children' => [
                    [
                        'nama_siswa'    => 'Ayza Arsila',
                        'NIS'           => '2303',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2017-08-17',
                        'nama_ayah'     => 'Achmad Ruzaqi',
                        'nama_ibu'      => 'Ayu Sundari',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Ridho Syahputra Nasution',
                'email' => 'ridho.nasution@iqra.com',
                'no_hp' => '081200000004',
                'children' => [
                    [
                        'nama_siswa'    => 'Alfa Riansyah Nasution',
                        'NIS'           => '2304',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-01-10',
                        'nama_ayah'     => 'Ridho Syahputra Nasution',
                        'nama_ibu'      => 'Dewi Wahyuni',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Andrianto',
                'email' => 'andrianto@iqra.com',
                'no_hp' => '081200000005',
                'children' => [
                    [
                        'nama_siswa'    => 'Alifa Nayla Putri',
                        'NIS'           => '2305',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Bandar Gadang',
                        'tanggal_lahir' => '2017-10-30',
                        'nama_ayah'     => 'Andrianto',
                        'nama_ibu'      => 'Mila Roza',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Deny Wahyudi',
                'email' => 'deny.wahyudi@iqra.com',
                'no_hp' => '081200000006',
                'children' => [
                    [
                        'nama_siswa'    => 'Aqila Qiara Putri',
                        'NIS'           => '2306',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-04-15',
                        'nama_ayah'     => 'Deny Wahyudi',
                        'nama_ibu'      => 'Nurul Humayah Barus',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Masriandi',
                'email' => 'masriandi@iqra.com',
                'no_hp' => '081200000007',
                'children' => [
                    [
                        'nama_siswa'    => 'Arsya Dhia Filardha',
                        'NIS'           => '2307',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-10-18',
                        'nama_ayah'     => 'Masriandi',
                        'nama_ibu'      => 'Neti Hermayanti',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Budi Setiawan',
                'email' => 'budi.setiawan@iqra.com',
                'no_hp' => '081200000008',
                'children' => [
                    [
                        'nama_siswa'    => 'Azalea Khaliqa Dzahim',
                        'NIS'           => '2308',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Tanjung Morawa',
                        'tanggal_lahir' => '2019-11-30',
                        'nama_ayah'     => 'Budi Setiawan',
                        'nama_ibu'      => 'Afni Fadillah',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Firdaus',
                'email' => 'firdaus@iqra.com',
                'no_hp' => '081200000009',
                'children' => [
                    [
                        'nama_siswa'    => 'Fathan Dirga Putra',
                        'NIS'           => '2309',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-08-26',
                        'nama_ayah'     => 'Firdaus',
                        'nama_ibu'      => 'Mega Anggraini',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Aswir',
                'email' => 'aswir@iqra.com',
                'no_hp' => '081200000010',
                'children' => [
                    [
                        'nama_siswa'    => 'Khairunnisa',
                        'NIS'           => '2310',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2017-07-14',
                        'nama_ayah'     => 'Aswir',
                        'nama_ibu'      => 'Zairi Isma Intan',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Robby Endoh Pratama',
                'email' => 'robby.pratama@iqra.com',
                'no_hp' => '081200000011',
                'children' => [
                    [
                        'nama_siswa'    => 'Muhammad AL Fatih Pratama',
                        'NIS'           => '2311',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-06-08',
                        'nama_ayah'     => 'Robby Endoh Pratama',
                        'nama_ibu'      => 'Iis Dahlia',
                        'class_id'      => null,
                    ],
                    [
                        'nama_siswa'    => 'Yasmin Zahira',
                        'NIS'           => '2334',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-03-10',
                        'nama_ayah'     => 'Robby Endoh Pratama',
                        'nama_ibu'      => 'Iis Dahlia',
                        'class_id'      => 3,
                    ],
                ],
            ],
            [
                'name'  => 'Muhammad Yunus',
                'email' => 'muhammad.yunus@iqra.com',
                'no_hp' => '081200000012',
                'children' => [
                    [
                        'nama_siswa'    => 'Muhammad Khalif Pratama',
                        'NIS'           => '2312',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Pariaman',
                        'tanggal_lahir' => '2019-09-30',
                        'nama_ayah'     => 'Muhammad Yunus',
                        'nama_ibu'      => 'Yelsa Oktavia',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Tengkuh Muhammad Iqbal',
                'email' => 'tengkuh.iqbal@iqra.com',
                'no_hp' => '081200000013',
                'children' => [
                    [
                        'nama_siswa'    => 'Tengkuh Yazid Qaulam',
                        'NIS'           => '2313',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Jakarta',
                        'tanggal_lahir' => '2018-09-07',
                        'nama_ayah'     => 'Tengkuh Muhammad Iqbal',
                        'nama_ibu'      => 'Selly Andriani',
                        'class_id'      => null,
                    ],
                ],
            ],

            // === T.A 2024/2025 (siswa tanpa kelas — alumni) ===
            [
                'name'  => 'Nurdianto',
                'email' => 'nurdianto@iqra.com',
                'no_hp' => '081200000014',
                'children' => [
                    [
                        'nama_siswa'    => 'Aurel Raisyah',
                        'NIS'           => '2314',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-03-17',
                        'nama_ayah'     => 'Nurdianto',
                        'nama_ibu'      => 'Ely Susanti',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Edi Faldi',
                'email' => 'edi.faldi@iqra.com',
                'no_hp' => '081200000015',
                'children' => [
                    [
                        'nama_siswa'    => 'Husna Alfiani',
                        'NIS'           => '2315',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Tembung',
                        'tanggal_lahir' => '2018-03-11',
                        'nama_ayah'     => 'Edi Faldi',
                        'nama_ibu'      => 'Desi Purnama Sari',
                        'class_id'      => null,
                    ],
                ],
            ],
            [
                'name'  => 'Muhammad Juanda Harahap',
                'email' => 'juanda.harahap@iqra.com',
                'no_hp' => '081200000016',
                'children' => [
                    [
                        'nama_siswa'    => 'Muhammad Alif Baiquni Harahap',
                        'NIS'           => '2316',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-01-16',
                        'nama_ayah'     => 'Muhammad Juanda Harahap',
                        'nama_ibu'      => 'Lisma Farida Pane',
                        'class_id'      => null,
                    ],
                    [
                        'nama_siswa'    => 'Zunaira Safiya Afra Harahap',
                        'NIS'           => '2337',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-11-16',
                        'nama_ayah'     => 'Muhammad Juanda Harahap',
                        'nama_ibu'      => 'Lisma Farida Pane',
                        'class_id'      => 3,
                    ],
                ],
            ],
            [
                'name'  => 'Jumadi',
                'email' => 'jumadi@iqra.com',
                'no_hp' => '081200000017',
                'children' => [
                    [
                        'nama_siswa'    => 'Rafif Afkari',
                        'NIS'           => '2317',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Bandar Khalifah',
                        'tanggal_lahir' => '2020-05-04',
                        'nama_ayah'     => 'Jumadi',
                        'nama_ibu'      => 'Sutrina',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Riki Purtama',
                'email' => 'riki.purtama@iqra.com',
                'no_hp' => '081200000018',
                'children' => [
                    [
                        'nama_siswa'    => 'Ryuga Maheza Purnama',
                        'NIS'           => '2318',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-11-10',
                        'nama_ayah'     => 'Riki Purtama',
                        'nama_ibu'      => 'Sri Hartina',
                        'class_id'      => 3,
                    ],
                ],
            ],
            [
                'name'  => 'Muhammad Alfarizi',
                'email' => 'muhammad.alfarizi@iqra.com',
                'no_hp' => '081200000019',
                'children' => [
                    [
                        'nama_siswa'    => 'Sakyra Khairiah',
                        'NIS'           => '2319',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2018-07-25',
                        'nama_ayah'     => 'Muhammad Alfarizi',
                        'nama_ibu'      => 'Indah Wulandry',
                        'class_id'      => null,
                    ],
                ],
            ],

            // === T.A 2025/2026 (siswa aktif — assign ke kelas) ===
            [
                'name'  => 'Aswan Lubis',
                'email' => 'aswan.lubis@iqra.com',
                'no_hp' => '081200000020',
                'children' => [
                    [
                        'nama_siswa'    => 'Ahmad Sahydan Lubis',
                        'NIS'           => '2321',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-03-01',
                        'nama_ayah'     => 'Aswan Lubis',
                        'nama_ibu'      => 'Yeni Sunita Nasution',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Muhammad Yoko Prayogi Sembiring',
                'email' => 'yoko.sembiring@iqra.com',
                'no_hp' => '081200000021',
                'children' => [
                    [
                        'nama_siswa'    => 'Archilla Alfathunnisa Sembiring',
                        'NIS'           => '2322',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-03-08',
                        'nama_ayah'     => 'Muhammad Yoko Prayogi Sembiring',
                        'nama_ibu'      => 'Parida Haryani Nasution',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Yul Fadli',
                'email' => 'yul.fadli@iqra.com',
                'no_hp' => '081200000022',
                'children' => [
                    [
                        'nama_siswa'    => 'Arumi Nasha Azeta',
                        'NIS'           => '2323',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2021-03-20',
                        'nama_ayah'     => 'Yul Fadli',
                        'nama_ibu'      => 'May Tuti',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Aji Kurniawan',
                'email' => 'aji.kurniawan@iqra.com',
                'no_hp' => '081200000023',
                'children' => [
                    [
                        'nama_siswa'    => 'Arumi Zea Razeta',
                        'NIS'           => '2324',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2021-03-20',
                        'nama_ayah'     => 'Aji Kurniawan',
                        'nama_ibu'      => 'Herliana Yana Siregar',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Taufik Basrih',
                'email' => 'taufik.basrih@iqra.com',
                'no_hp' => '081200000024',
                'children' => [
                    [
                        'nama_siswa'    => 'Fathan Pratama',
                        'NIS'           => '2325',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Tembung',
                        'tanggal_lahir' => '2020-06-17',
                        'nama_ayah'     => 'Taufik Basrih',
                        'nama_ibu'      => 'Safrida Fitri',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Handi Sugandi',
                'email' => 'handi.sugandi@iqra.com',
                'no_hp' => '081200000025',
                'children' => [
                    [
                        'nama_siswa'    => 'Juliana Putri Anjani',
                        'NIS'           => '2326',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Tembung',
                        'tanggal_lahir' => '2020-07-18',
                        'nama_ayah'     => 'Handi Sugandi',
                        'nama_ibu'      => 'Siti Hanum',
                        'class_id'      => 1,
                    ],
                ],
            ],
            [
                'name'  => 'Dodi Pratikto',
                'email' => 'dodi.pratikto@iqra.com',
                'no_hp' => '081200000026',
                'children' => [
                    [
                        'nama_siswa'    => 'Muhammad Arsya Alfarizky',
                        'NIS'           => '2327',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Tembung',
                        'tanggal_lahir' => '2020-06-23',
                        'nama_ayah'     => 'Dodi Pratikto',
                        'nama_ibu'      => 'Betria Susanti',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Firman Wahid',
                'email' => 'firman.wahid@iqra.com',
                'no_hp' => '081200000027',
                'children' => [
                    [
                        'nama_siswa'    => 'Muhammad Zafran Khan',
                        'NIS'           => '2328',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-08-18',
                        'nama_ayah'     => 'Firman Wahid',
                        'nama_ibu'      => 'Riya Tri Wardani',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Arnoli Eko',
                'email' => 'arnoli.eko@iqra.com',
                'no_hp' => '081200000028',
                'children' => [
                    [
                        'nama_siswa'    => 'Nabila Husna',
                        'NIS'           => '2329',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-07-18',
                        'nama_ayah'     => 'Arnoli Eko',
                        'nama_ibu'      => 'Yosma Werni',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Syafriadi',
                'email' => 'syafriadi@iqra.com',
                'no_hp' => '081200000029',
                'children' => [
                    [
                        'nama_siswa'    => 'Nabila Nurzaqia',
                        'NIS'           => '2330',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Tembung',
                        'tanggal_lahir' => '2020-03-27',
                        'nama_ayah'     => 'Syafriadi',
                        'nama_ibu'      => 'Elfi Syahfira',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Sujono',
                'email' => 'sujono@iqra.com',
                'no_hp' => '081200000030',
                'children' => [
                    [
                        'nama_siswa'    => 'Nayra Azzahra',
                        'NIS'           => '2331',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2019-08-31',
                        'nama_ayah'     => 'Sujono',
                        'nama_ibu'      => 'Susilawati',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Yafizham',
                'email' => 'yafizham@iqra.com',
                'no_hp' => '081200000031',
                'children' => [
                    [
                        'nama_siswa'    => 'Rahmadani Hidayah',
                        'NIS'           => '2332',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-04-30',
                        'nama_ayah'     => 'Yafizham',
                        'nama_ibu'      => 'Nurul Hikmah',
                        'class_id'      => 2,
                    ],
                ],
            ],
            [
                'name'  => 'Rahmad Suryadi',
                'email' => 'rahmad.suryadi@iqra.com',
                'no_hp' => '081200000032',
                'children' => [
                    [
                        'nama_siswa'    => 'Raline Zevania Alisya',
                        'NIS'           => '2333',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2021-09-26',
                        'nama_ayah'     => 'Rahmad Suryadi',
                        'nama_ibu'      => 'Esa Desi Gheavani',
                        'class_id'      => 3,
                    ],
                ],
            ],
            [
                'name'  => 'Martuah Raja Siregar',
                'email' => 'martuah.siregar@iqra.com',
                'no_hp' => '081200000033',
                'children' => [
                    [
                        'nama_siswa'    => 'Zahra Adzkia Shabira Siregar',
                        'NIS'           => '2335',
                        'jenis_kelamin' => 'P',
                        'tempat_lahir'  => 'Medan',
                        'tanggal_lahir' => '2020-09-29',
                        'nama_ayah'     => 'Martuah Raja Siregar',
                        'nama_ibu'      => 'Sri Erawati',
                        'class_id'      => 3,
                    ],
                ],
            ],
            [
                'name'  => 'Zulfahmi Lubis',
                'email' => 'zulfahmi.lubis@iqra.com',
                'no_hp' => '081200000034',
                'children' => [
                    [
                        'nama_siswa'    => 'Zayn Alwi Lubis',
                        'NIS'           => '2336',
                        'jenis_kelamin' => 'L',
                        'tempat_lahir'  => 'Bandar Khalifah',
                        'tanggal_lahir' => '2020-10-17',
                        'nama_ayah'     => 'Zulfahmi Lubis',
                        'nama_ibu'      => 'Safrida Yanti',
                        'class_id'      => 3,
                    ],
                ],
            ],
        ];
    }
}
