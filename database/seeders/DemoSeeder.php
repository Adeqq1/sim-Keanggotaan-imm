<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\MateriKegiatan;
use App\Models\Pendaftaran;
use App\Models\PenilaianKegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\SesiKegiatan;
use App\Models\User;
use App\Services\DemoDataFiles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use LogicException;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new LogicException('DemoSeeder tidak boleh dijalankan di environment production.');
        }

        DB::transaction(function (): void {
            $users = $this->seedUsersAndMembers();
            $this->seedRegistrations($users);
            $kegiatans = $this->seedActivities();
            $this->seedAttendance($users, $kegiatans);
            $this->seedContent($users, $kegiatans);
        });

        app(DemoDataFiles::class)->provisionFiles();
    }

    /** @return array<string, User|Anggota> */
    private function seedUsersAndMembers(): array
    {
        $accounts = [
            'admin' => ['Admin SIM IMM', 'admin@admin.com', 'admin'],
            'instruktur' => ['Fajar Hidayat', 'instruktur@example.com', 'instruktur'],
            'instruktur2' => ['Rahma Nuraini', 'rahma.instruktur@example.com', 'instruktur'],
            'aisyah' => ['Aisyah Rahmawati', 'kader@example.com', 'kader'],
            'nabila' => ['Nabila Putri Ramadhani', 'nabila@example.com', 'kader'],
            'fikri' => ['Fikri Maulana', 'fikri@example.com', 'kader'],
            'rafi' => ['Rafi Pratama', 'rafi@example.com', 'kader'],
            'siti' => ['Siti Hanifah', 'siti@example.com', 'kader'],
            'zahra' => ['Zahra Amalia', 'zahra@example.com', 'kader'],
            'dimas' => ['Dimas Saputra', 'dimas@example.com', 'kader'],
            'farhan' => ['Farhan Akbar', 'farhan@example.com', 'kader'],
            'salma' => ['Salma Nurfadilah', 'salma@example.com', 'kader'],
            'bagas' => ['Bagas Ramadhan', 'bagas@example.com', 'kader'],
            'laila' => ['Laila Fitriani', 'laila@example.com', 'kader'],
            'inactive' => ['Rizky Kurniawan', 'rizky.nonaktif@example.com', 'kader'],
        ];

        $result = [];
        foreach ($accounts as $key => [$name, $email, $role]) {
            $result[$key] = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'role' => $role, 'email_verified_at' => now()],
            );
        }

        $members = [
            'aisyah' => ['24260001', 'Yogyakarta', '2003-02-14', '081234560001', 'ahmad-dahlan', 2026, true, 8],
            'nabila' => ['24260002', 'Bantul', '2004-06-21', '081234560002', 'ahmad-dahlan', 2026, true, 7],
            'fikri' => ['24260003', 'Sleman', '2003-09-08', '081234560003', 'buya-hamka', 2026, true, 6],
            'rafi' => ['24260004', 'Kulon Progo', '2004-01-17', '081234560004', 'buya-hamka', 2026, true, 5],
            'siti' => ['24260005', 'Yogyakarta', '2003-12-11', '081234560005', 'ahmad-dahlan', 2026, true, 4],
            'zahra' => ['24250001', 'Magelang', '2002-07-04', '081234560006', 'buya-hamka', 2025, true, 10],
            'dimas' => ['24250002', 'Klaten', '2002-03-26', '081234560007', 'ahmad-dahlan', 2025, true, 9],
            'farhan' => ['24250003', 'Purworejo', '2001-11-19', '081234560008', 'buya-hamka', 2025, true, 8],
            'salma' => ['24240001', 'Yogyakarta', '2001-05-30', '081234560009', 'ahmad-dahlan', 2024, true, 12],
            'bagas' => ['24240002', 'Sleman', '2002-10-02', '081234560010', 'buya-hamka', 2024, true, 11],
            'laila' => [null, 'Bantul', '2004-08-15', '081234560011', 'ahmad-dahlan', 2026, true, 2],
            'inactive' => ['24230001', 'Wonosari', '2001-04-09', '081234560012', 'buya-hamka', 2023, false, 14],
        ];

        foreach ($members as $key => [$nia, $birthPlace, $birthDate, $phone, $commissariat, $year, $active, $monthsAgo]) {
            $user = $result[$key];
            $member = Anggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nia' => $nia,
                    'nama_lengkap' => $user->name,
                    'tempat_lahir' => $birthPlace,
                    'tanggal_lahir' => $birthDate,
                    'alamat' => 'Jl. Kader Muhammadiyah No. '.($user->id % 30 + 1).', Daerah Istimewa Yogyakarta',
                    'no_telp' => $phone,
                    'foto_profil' => in_array($key, ['aisyah', 'nabila', 'fikri'], true) ? "foto_profil/demo/{$key}.png" : null,
                    'status_aktif' => $active,
                    'komisariat_id' => $commissariat,
                    'tahun_daftar' => $year,
                ],
            );
            $member->timestamps = false;
            $member->created_at = now()->subMonths($monthsAgo);
            $member->updated_at = now();
            $member->save();
            $result[$key] = $member;
        }

        return $result;
    }

    /** @param array<string, User|Anggota> $users */
    private function seedRegistrations(array $users): void
    {
        $registrations = [
            ['Calon Kader Ahmad Dahlan', 'calon.ahmad@example.com', 'kader', 'ahmad-dahlan', 'ktm', 'pending', null, null],
            ['Calon Kader Buya Hamka', 'calon.buya@example.com', 'kader', 'buya-hamka', 'ktp', 'pending', null, null],
            ['Calon Instruktur IMM', 'calon.instruktur@example.com', 'instruktur', null, 'ktp', 'pending', null, null],
            ['Nabila Putri Ramadhani', 'nabila@example.com', 'kader', 'ahmad-dahlan', 'ktm', 'disetujui', 'Data dan dokumen telah diverifikasi.', $users['nabila']->user],
            ['Pendaftar Dokumen Buram', 'ditolak@example.com', 'kader', 'buya-hamka', 'ktm', 'ditolak', 'Dokumen KTM tidak terbaca. Silakan daftar ulang.', null],
        ];

        foreach ($registrations as $index => [$name, $email, $role, $commissariat, $document, $status, $note, $user]) {
            Pendaftaran::updateOrCreate(
                ['email' => $email],
                [
                    'user_id' => $user?->id,
                    'nama_lengkap' => $name,
                    'password' => $status === 'pending' ? 'password' : null,
                    'role' => $role,
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => Carbon::create(2003, ($index % 10) + 1, 12),
                    'no_telp' => '0821000000'.($index + 1),
                    'alamat' => 'Daerah Istimewa Yogyakarta',
                    'tanggal_daftar' => now()->subDays(5 - $index),
                    'file_persyaratan' => "pendaftaran/demo/pendaftaran-{$index}.".($index === 1 ? 'png' : 'pdf'),
                    'jenis_dokumen_identitas' => $document,
                    'komisariat_id' => $commissariat,
                    'tahun_daftar' => 2026,
                    'status_validasi' => $status,
                    'catatan_admin' => $note,
                ],
            );
        }
    }

    /** @return array<string, Kegiatan> */
    private function seedActivities(): array
    {
        $activities = [
            'kajian' => ['Kajian Rutin Keislaman', -75, 'Masjid Kampus Utama', Kegiatan::SATU_SESI, 1, [2025, 2026]],
            'dad' => ['Darul Arqam Dasar 2026', -45, 'Balai Diklat Muhammadiyah', Kegiatan::MULTI_SESI, 3, [2026]],
            'workshop' => ['Workshop Administrasi Organisasi', -20, 'Aula Ahmad Dahlan', Kegiatan::SATU_SESI, 1, [2025, 2026]],
            'baksos' => ['Bakti Sosial Ramadan', -10, 'Desa Binaan IMM', Kegiatan::SATU_SESI, 1, [2024, 2025, 2026]],
            'seminar' => ['Seminar Kepemimpinan Kader', 7, 'Auditorium Universitas', Kegiatan::SATU_SESI, 1, [2025, 2026]],
            'musykom' => ['Musyawarah Komisariat', 14, 'Gedung Dakwah Muhammadiyah', Kegiatan::SATU_SESI, 1, [2025, 2026]],
            'literasi' => ['Sekolah Literasi Digital', 28, 'Laboratorium Komputer', Kegiatan::MULTI_SESI, 3, [2026]],
            'diskusi' => ['Diskusi Publik Gerakan Mahasiswa', -180, 'Pendopo Kampus', Kegiatan::SATU_SESI, 1, [2024, 2025]],
        ];

        $result = [];
        foreach ($activities as $key => [$name, $day, $location, $type, $minimum, $years]) {
            $date = now()->startOfDay()->addDays($day)->setTime(9, 0);
            $activity = Kegiatan::updateOrCreate(
                ['nama_kegiatan' => $name],
                [
                    'deskripsi' => "Kegiatan {$name} untuk memperkuat kapasitas, ideologi, dan kolaborasi kader IMM.",
                    'tanggal_waktu' => $date,
                    'lokasi' => $location,
                    'thumbnail' => "kegiatan_thumbnails/demo/{$key}.png",
                    'jenis_pelaksanaan' => $type,
                    'minimum_sesi_terverifikasi' => $minimum,
                ],
            );
            $activity->timestamps = false;
            $activity->created_at = $date->copy()->subDays(20);
            $activity->updated_at = now();
            $activity->save();

            foreach ($years as $year) {
                $activity->tahunAngkatans()->updateOrCreate(['tahun_daftar' => $year]);
            }
            $activity->tahunAngkatans()->whereNotIn('tahun_daftar', $years)->delete();

            $sessionNames = $type === Kegiatan::MULTI_SESI
                ? ['Keislaman', 'Ke-IMM-an', 'Kepemimpinan', 'Evaluasi dan Rencana Tindak Lanjut']
                : [$name];
            foreach ($sessionNames as $index => $sessionName) {
                $activity->sesiKegiatans()->updateOrCreate(
                    ['urutan' => $index + 1],
                    ['nama_sesi' => $sessionName, 'mulai_pada' => $date->copy()->addHours($index * 3)],
                );
            }
            $activity->sesiKegiatans()->where('urutan', '>', count($sessionNames))->delete();
            $result[$key] = $activity;
        }

        return $result;
    }

    /**
     * @param  array<string, User|Anggota>  $users
     * @param  array<string, Kegiatan>  $kegiatans
     */
    private function seedAttendance(array $users, array $kegiatans): void
    {
        $instructor = $users['instruktur'];
        $singleSession = $kegiatans['kajian']->sesiKegiatans()->firstOrFail();
        $singleStates = [
            'aisyah' => ['hadir', 'terverifikasi'],
            'nabila' => ['hadir', 'pending'],
            'fikri' => ['hadir', 'ditolak'],
            'rafi' => ['izin', 'pending'],
            'siti' => ['alfa', 'pending'],
            'zahra' => ['hadir', 'terverifikasi'],
            'dimas' => ['hadir', 'terverifikasi'],
        ];
        foreach ($singleStates as $memberKey => [$attendance, $verification]) {
            $this->upsertAttendance($singleSession, $users[$memberKey], $attendance, $verification, $instructor);
        }

        $dadSessions = $kegiatans['dad']->sesiKegiatans()->get();
        $progress = ['aisyah' => 4, 'nabila' => 3, 'fikri' => 2, 'rafi' => 3, 'siti' => 3];
        foreach ($progress as $memberKey => $verifiedCount) {
            foreach ($dadSessions as $index => $session) {
                $verification = $index < $verifiedCount ? 'terverifikasi' : 'pending';
                if ($memberKey === 'rafi' && $index === 2) {
                    $verification = 'ditolak';
                }
                $this->upsertAttendance($session, $users[$memberKey], 'hadir', $verification, $instructor);
            }
        }

        foreach (['aisyah', 'nabila', 'zahra', 'dimas', 'farhan'] as $memberKey) {
            $this->upsertAttendance(
                $kegiatans['workshop']->sesiKegiatans()->firstOrFail(),
                $users[$memberKey],
                'hadir',
                'terverifikasi',
                $instructor,
            );
        }

        foreach (['aisyah' => 'A', 'nabila' => 'B', 'siti' => 'C'] as $memberKey => $grade) {
            PenilaianKegiatan::updateOrCreate(
                ['kegiatan_id' => $kegiatans['dad']->id, 'anggota_id' => $users[$memberKey]->id],
                ['nilai' => $grade],
            );
        }
    }

    private function upsertAttendance(
        SesiKegiatan $session,
        Anggota $member,
        string $attendance,
        string $verification,
        User $instructor,
    ): void {
        $reviewed = in_array($verification, ['terverifikasi', 'ditolak'], true);
        Presensi::updateOrCreate(
            ['sesi_kegiatan_id' => $session->id, 'anggota_id' => $member->id],
            [
                'kegiatan_id' => $session->kegiatan_id,
                'waktu_hadir' => $attendance === 'hadir' ? $session->mulai_pada->copy()->addMinutes(5) : null,
                'status_kehadiran' => $attendance,
                'status_klaim' => null,
                'bukti_kehadiran' => null,
                'status_verifikasi' => $verification,
                'pemeriksa_id' => $reviewed ? $instructor->id : null,
                'diperiksa_pada' => $reviewed ? $session->mulai_pada->copy()->addHours(6) : null,
            ],
        );
    }

    /**
     * @param  array<string, User|Anggota>  $users
     * @param  array<string, Kegiatan>  $kegiatans
     */
    private function seedContent(array $users, array $kegiatans): void
    {
        $materials = [
            [$kegiatans['kajian'], 'Modul Kajian Keislaman', 'Pokok bahasan kajian dan referensi diskusi.', 'kajian-modul.pdf'],
            [$kegiatans['dad'], 'Modul Darul Arqam Dasar', 'Modul utama perkaderan dasar IMM.', 'dad-modul.pdf'],
            [$kegiatans['dad'], 'Panduan Rencana Tindak Lanjut', 'Panduan menyusun tindak lanjut peserta.', 'dad-rtl.pdf'],
            [$kegiatans['workshop'], 'Template Administrasi Komisariat', 'Contoh surat dan administrasi organisasi.', 'administrasi.pdf'],
            [$kegiatans['diskusi'], 'Ringkasan Diskusi Gerakan', 'Ringkasan materi dan rekomendasi diskusi.', 'diskusi.pdf'],
        ];
        foreach ($materials as [$activity, $title, $description, $file]) {
            $material = MateriKegiatan::updateOrCreate(
                ['kegiatan_id' => $activity->id, 'judul' => $title],
                ['deskripsi' => $description, 'file_materi' => "materi_kegiatan/demo/{$file}"],
            );
            if ($title === 'Modul Darul Arqam Dasar') {
                $users['aisyah']->materiTersimpan()->syncWithoutDetaching([$material->id]);
            }
        }

        foreach (['dad', 'workshop', 'diskusi'] as $key) {
            $activity = $kegiatans[$key];
            LaporanKegiatan::updateOrCreate(
                ['kegiatan_id' => $activity->id],
                [
                    'tujuan' => 'Memperkuat pemahaman ideologi IMM dan kemampuan kepemimpinan kader.',
                    'ringkasan' => "{$activity->nama_kegiatan} berjalan tertib, partisipatif, dan sesuai agenda.",
                    'agenda' => 'Pembukaan, penyampaian materi, diskusi kelompok, evaluasi, dan penutup.',
                    'narasumber' => 'Fajar Hidayat dan Rahma Nuraini',
                    'hasil' => 'Peserta memahami materi dan menyusun rencana tindak lanjut di komisariat.',
                    'kendala' => 'Penyesuaian waktu pada sesi diskusi karena antusiasme peserta.',
                    'tindak_lanjut' => 'Mentoring dua pekan dan evaluasi pelaksanaan program komisariat.',
                    'file_lampiran' => "laporan_kegiatan/demo/{$key}.pdf",
                ],
            );
        }

        $archives = [
            ['aisyah', 'IMM/PROP/2026/001', 'Proposal Darul Arqam Dasar 2026', 'proposal'],
            ['aisyah', 'IMM/LPJ/2026/001', 'LPJ Kajian Rutin Keislaman', 'lpj'],
            ['aisyah', 'IMM/SK/2026/001', 'Surat Keputusan Panitia DAD', 'surat_keputusan'],
            ['nabila', 'IMM/SM/2026/002', 'Undangan Seminar Kepemimpinan', 'surat_masuk'],
            ['fikri', 'IMM/SKEL/2026/003', 'Surat Tugas Bakti Sosial', 'surat_keluar'],
            ['zahra', 'IMM/LAIN/2025/004', 'Notulen Musyawarah Komisariat', 'lainnya'],
        ];
        foreach ($archives as $index => [$memberKey, $number, $title, $category]) {
            Arsip::updateOrCreate(
                ['nomor_dokumen' => $number],
                [
                    'anggota_id' => $users[$memberKey]->id,
                    'judul_dokumen' => $title,
                    'kategori_arsip' => $category,
                    'file_arsip' => "arsip/demo/arsip-{$index}.pdf",
                    'tanggal_unggah' => now()->subDays($index * 3),
                ],
            );
        }

        $certificates = [
            [$kegiatans['kajian'], $users['aisyah'], 'IMM/KJ/2026/001', Sertifikat::SATU_SESI, null, 'kajian-aisyah.pdf'],
            [$kegiatans['dad'], $users['aisyah'], 'IMM/DAD/2026/001', Sertifikat::MULTI_SESI, 'A', 'dad-aisyah.pdf'],
            [$kegiatans['dad'], $users['nabila'], 'IMM/DAD/2026/002', Sertifikat::MULTI_SESI, 'B', 'dad-nabila.pdf'],
            [$kegiatans['workshop'], $users['aisyah'], 'IMM/ADM/2026/001', Sertifikat::SATU_SESI, null, 'workshop-aisyah.pdf'],
            [$kegiatans['workshop'], $users['zahra'], 'IMM/ADM/2026/002', Sertifikat::SATU_SESI, null, 'workshop-zahra.pdf'],
            [$kegiatans['workshop'], $users['dimas'], 'IMM/ADM/2026/003', Sertifikat::SATU_SESI, null, 'workshop-dimas.pdf'],
        ];
        foreach ($certificates as [$activity, $member, $number, $type, $grade, $file]) {
            Sertifikat::updateOrCreate(
                ['kegiatan_id' => $activity->id, 'anggota_id' => $member->id],
                [
                    'nomor_sertifikat' => $number,
                    'file_sertifikat' => "sertifikat/demo/{$file}",
                    'tipe_sertifikat' => $type,
                    'nilai_snapshot' => $grade,
                ],
            );
        }
    }
}
