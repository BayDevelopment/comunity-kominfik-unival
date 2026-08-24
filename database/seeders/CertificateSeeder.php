<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\CertificateTemplat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user dari Tinker & template yang sudah ada di database
        $user = User::first();
        $template = CertificateTemplat::first();

        // Daftar penerima sertifikat terbaru
        $recipients = [
            ['name' => 'Ayu Mita Nuraini',         'email' => 'ayumita3004@gmail.com'],
            ['name' => 'Mochamad alif Ramadhan',   'email' => 'hendrialif25@gmail.com'],
            ['name' => 'Muhammad Dzaky Zaeny',     'email' => 'dzakyzaeny@gmail.com'],
            ['name' => 'Muhammad Faisal Aristama', 'email' => 'mfaisalarist@gmail.com'],
            ['name' => 'Muhammad Zaidan Idris',    'email' => 'zaidanidris7@gmail.com'],
            ['name' => 'Muhammad Zidane Maulana',  'email' => 'zidanemaulana4779@gmail.com'],
            ['name' => 'Nabilah Hadianti',         'email' => 'hadiantinabila610@gmail.com'],
            ['name' => 'Rama Maulana',             'email' => 'damrey969@gmail.com'],
            ['name' => 'Roihatul Jannah',          'email' => 'rohiatuljannah6@gmail.com'],
            ['name' => 'Selvi Komala Sari',        'email' => 'selviatikarahayu@gmail.com'],
            ['name' => 'Shilvy Nur Atifa',         'email' => 'shilvyviviii@gmail.com'],
            ['name' => 'Adam Kurniawan',           'email' => 'adamkakaroto3@gmail.com'],
            ['name' => 'Ahmad Fauzan Ali',         'email' => 'fauzan92653@gmail.com'],
            ['name' => 'Dania Salsabila',          'email' => 'dzdania288@gmail.com'],
            ['name' => 'Hasbi Ali Maulidi',        'email' => 'hasbialimaulidiali7@gmail.com'],
            ['name' => 'Mei Dianti Dwi',           'email' => 'meidiantidwi@icloud.com'],
            ['name' => 'Nisa Ayu',                 'email' => 'nisaayuusyarifah@gmail.com'],
            ['name' => 'Novitasari',               'email' => 'ns2127911@gmail.com'],
            ['name' => 'Muhamad Rizki Ramadhani',   'email' => 'mrizkiramadhani1426@gmail.com'],
        ];

        foreach ($recipients as $recipient) {
            Certificate::create([
                'uuid'                     => (string) Str::uuid(),
                'certificate_template_id' => $template?->id,
                'user_id'                 => $user?->id,
                'certificate_number'       => Certificate::generateCertificateNumber(),

                // NAMA & EMAIL PENERIMA
                'recipient_name'           => $recipient['name'],
                'recipient_email'          => $recipient['email'],

                // DATA LAINNYA DISERAGAMKAN
                'event_name'               => 'Webinar Nasional Teknologi 2026',
                'course_name'              => 'Pengembangan Aplikasi Modern dengan Laravel & Vue 3',
                'description'              => 'Diberikan atas partisipasi dan penyelesaian seluruh modul pelatihan.',
                'signatory_name'           => 'Dr. Hendra Wijaya, M.Kom.',
                'signatory_role'           => 'Director of Digital Education',
                'signatory_signature_path' => null, // Tanda tangan gambar dikosongkan
                'issued_at'                => now(),
                'expired_at'               => now()->addYears(2),
                'status'                   => 'published',
                'revoke_reason'            => null,
                'revoked_at'               => null,
                'revoked_by'               => null,
                'file_path'                => null,
                'verification_code'        => strtoupper(Str::random(10)),
                'metadata'                 => [
                    'hours' => 32,
                    'grade' => 'A',
                ],
                'download_count'           => 0,
                'last_downloaded_at'       => null,
            ]);
        }
    }
}
