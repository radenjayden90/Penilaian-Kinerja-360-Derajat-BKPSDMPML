<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Enums\EmployeeRole;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::where('name', EmployeeRole::ADMIN->value)->first();
        $headRole = Role::where('name', EmployeeRole::HEAD->value)->first();
        $employeeRole = Role::where('name', EmployeeRole::EMPLOYEE->value)->first();

        // 2. Departments
        $deptBkpsdm = Department::where('code', 'BKPSDM')->first() ?? Department::firstOrCreate(['code' => 'BKPSDM'], ['name' => 'BKPSDM Kabupaten Pemalang', 'is_active' => true]);
        $deptSekretariat = Department::where('code', 'SEKRETARIAT')->first() ?? Department::firstOrCreate(['code' => 'SEKRETARIAT'], ['name' => 'Sekretariat', 'is_active' => true]);
        $deptPPIK = Department::where('code', 'PPIK')->first() ?? Department::firstOrCreate(['code' => 'PPIK'], ['name' => 'Bidang Pengadaan, Pemberhentian dan Informasi Kepegawaian', 'is_active' => true]);
        $deptMutasi = Department::where('code', 'MUTASI')->first() ?? Department::firstOrCreate(['code' => 'MUTASI'], ['name' => 'Bidang Mutasi dan Promosi', 'is_active' => true]);
        $deptPEKA = Department::where('code', 'PEKA')->first() ?? Department::firstOrCreate(['code' => 'PEKA'], ['name' => 'Bidang Penilaian dan Evaluasi Kinerja Aparatur', 'is_active' => true]);
        $deptPSDM = Department::where('code', 'PSDM')->first() ?? Department::firstOrCreate(['code' => 'PSDM'], ['name' => 'Bidang Pengembangan Sumber Daya Manusia', 'is_active' => true]);

        // 3. Positions
<<<<<<< HEAD
        $posAdministrator = Position::firstOrCreate(['name' => 'Administrator', 'department_id' => null], ['level' => '0', 'is_active' => true]);
        $posKepalaBkpsdm = Position::firstOrCreate(['name' => 'Kepala BKPSDM Kabupaten Pemalang', 'department_id' => $deptSekretariat->id], ['level' => '1', 'is_active' => true]);
=======
        $posKepalaBkpsdm = Position::updateOrCreate(['name' => 'Kepala BKPSDM Kabupaten Pemalang'], ['department_id' => $deptBkpsdm->id, 'level' => '1', 'is_active' => true]);
>>>>>>> 90951489684a7661b2f24f1df8dafc3fe3bb3b9d
        
        // Eselon 3 (Sekretaris & Kabid)
        $posSekretaris = Position::firstOrCreate(['name' => 'Sekretaris', 'department_id' => $deptSekretariat->id], ['level' => '2', 'is_active' => true]);
        $posKabid = Position::firstOrCreate(['name' => 'Kepala Bidang', 'department_id' => $deptPPIK->id], ['level' => '2', 'is_active' => true]);
        
        // Eselon 4 (Kasubbag / Pejabat Pengawas)
        $posKasubbagUmum = Position::firstOrCreate(['name' => 'Kasubbag Umum dan Kepegawaian', 'department_id' => $deptSekretariat->id], ['level' => '3', 'is_active' => true]);
        $posKasubbagBinaProgram = Position::firstOrCreate(['name' => 'Kasubbag Bina Program dan Keuangan', 'department_id' => $deptSekretariat->id], ['level' => '3', 'is_active' => true]);
        
        // Fungsional / Pelaksana
        $posAnalisSDM = Position::firstOrCreate(['name' => 'Analis SDM Aparatur Ahli Muda', 'department_id' => $deptMutasi->id], ['level' => '3', 'is_active' => true]);
        $posPengelola = Position::firstOrCreate(['name' => 'Pengelola Kepegawaian', 'department_id' => $deptPPIK->id], ['level' => '4', 'is_active' => true]);
        $posStafPEKA = Position::firstOrCreate(['name' => 'Staf Evaluasi Kinerja', 'department_id' => $deptPEKA->id], ['level' => '4', 'is_active' => true]);

        // 4. Create Users / Employees with Password = NIP

        // A. Admin BKPSDM
        Employee::updateOrCreate([
            'nip' => '198001012005011001'
        ], [
            'name' => 'Administrator BKPSDM',
            'email' => 'admin@pemalang.go.id',
            'password' => Hash::make('198001012005011001'),
            'department_id' => null,
            'position_id' => null,
            'role_id' => $adminRole?->id,
            'is_active' => true,
        ]);

        // B. Kepala BKPSDM (Pimpinan Utama)
        $kepala = Employee::updateOrCreate([
            'nip' => '196803231990031012'
        ], [
            'name' => 'Khaeron, S.H., M.M.',
            'email' => 'kepala@pemalang.go.id',
            'password' => Hash::make('196803231990031012'),
            'gender' => 'L',
            'department_id' => $deptBkpsdm->id,
            'position_id' => $posKepalaBkpsdm->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => null,
            'is_active' => true,
        ]);

        // C. Sekretaris
        $sekretaris = Employee::updateOrCreate([
            'nip' => '197203151998031003'
        ], [
            'name' => 'Dra. Rini Susanti, M.Si',
            'email' => 'sekretaris@pemalang.go.id',
            'password' => Hash::make('197203151998031003'),
            'gender' => 'P',
            'department_id' => $deptSekretariat->id,
            'position_id' => $posSekretaris->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // D. Kasubbag Umum dan Kepegawaian (Subordinate of Sekretaris)
        Employee::updateOrCreate([
            'nip' => '198502142010011004'
        ], [
            'name' => 'Hendra Setiawan, S.E',
            'email' => 'hendra.umum@pemalang.go.id',
            'password' => Hash::make('198502142010011004'),
            'gender' => 'L',
            'department_id' => $deptSekretariat->id,
            'position_id' => $posKasubbagUmum->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $sekretaris->id,
            'is_active' => true,
        ]);

        // E. Kabid Mutasi dan Promosi (Head)
        $kabidMutasi = Employee::updateOrCreate([
            'nip' => '197508201999031002'
        ], [
            'name' => 'Drs. Bambang Wijaya, M.M',
            'email' => 'kabid.mutasi@pemalang.go.id',
            'password' => Hash::make('197508201999031002'),
            'gender' => 'L',
            'department_id' => $deptMutasi->id,
            'position_id' => $posKabid->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // F. Analis SDM Mutasi (Employee - Subordinate of Kabid Mutasi)
        Employee::updateOrCreate([
            'nip' => '198804102012012005'
        ], [
            'name' => 'Rina Handayani, S.IP',
            'email' => 'rina.mutasi@pemalang.go.id',
            'password' => Hash::make('198804102012012005'),
            'gender' => 'P',
            'department_id' => $deptMutasi->id,
            'position_id' => $posAnalisSDM->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidMutasi->id,
            'is_active' => true,
        ]);

        // G. Kabid PEKA (Head)
        $kabidPEKA = Employee::updateOrCreate([
            'nip' => '197803152002122006'
        ], [
            'name' => 'Siti Rahmawati, S.SH, M.Si',
            'email' => 'kabid.peka@pemalang.go.id',
            'password' => Hash::make('197803152002122006'),
            'gender' => 'P',
            'department_id' => $deptPEKA->id,
            'position_id' => $posKabid->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // H. Staf PEKA
        Employee::updateOrCreate([
            'nip' => '199207182015031007'
        ], [
            'name' => 'Ahmad Fauzi, A.Md',
            'email' => 'fauzi.peka@pemalang.go.id',
            'password' => Hash::make('199207182015031007'),
            'gender' => 'L',
            'department_id' => $deptPEKA->id,
            'position_id' => $posStafPEKA->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPEKA->id,
            'is_active' => true,
        ]);
        

        $posPrakomPertama = Position::firstOrCreate(['name' => 'Pranata Komputer Ahli Pertama', 'department_id' => $deptPPIK->id], ['level' => '3', 'is_active' => true]);
        $posPengadministrasi = Position::firstOrCreate(['name' => 'Pengadministrasi Perkantoran', 'department_id' => $deptPPIK->id], ['level' => '4', 'is_active' => true]);
        $posPenelaah = Position::firstOrCreate(['name' => 'Penelaah Teknis Kebijakan', 'department_id' => $deptPPIK->id], ['level' => '4', 'is_active' => true]);
        $posPrakomMuda = Position::firstOrCreate(['name' => 'Pranata Komputer Ahli Muda', 'department_id' => $deptPPIK->id], ['level' => '3', 'is_active' => true]);
        $posAnalisSDMPPIK = Position::firstOrCreate(['name' => 'Analis Sumber Daya Manusia Aparatur Ahli Muda', 'department_id' => $deptPPIK->id], ['level' => '3', 'is_active' => true]);
        $posPrakomLanjutan = Position::firstOrCreate(['name' => 'Pranata Komputer Mahir/Pelaksana Lanjutan', 'department_id' => $deptPPIK->id], ['level' => '4', 'is_active' => true]);

        // 1. Kabid PPIK
        $kabidPPIK = Employee::updateOrCreate([
            'nip' => '197903072005011006'
        ], [
            'name' => 'Hadi Siswanto, S.Kom',
            'email' => 'hadisiswanto@pemalang.go.id',
            'password' => Hash::make('197903072005011006'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posKabid->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // 2. Fendi Heriawan
        Employee::updateOrCreate([
            'nip' => '198104192009011005'
        ], [
            'name' => 'Fendi Heriawan, S.Kom',
            'email' => 'fendiheriawan@pemalang.go.id',
            'password' => Hash::make('198104192009011005'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPrakomPertama->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 3. Maskuri
        Employee::updateOrCreate([
            'nip' => '196810151992031007'
        ], [
            'name' => 'Maskuri',
            'email' => 'maskuri@pemalang.go.id',
            'password' => Hash::make('196810151992031007'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPengadministrasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 4. Dian Fitriana
        Employee::updateOrCreate([
            'nip' => '197709192008012008'
        ], [
            'name' => 'Dian Fitriana, S.H.',
            'email' => 'dianfitriana@pemalang.go.id',
            'password' => Hash::make('197709192008012008'),
            'gender' => 'P',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPenelaah->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 5. Apit Setiawan
        Employee::updateOrCreate([
            'nip' => '198407302009031003'
        ], [
            'name' => 'Apit Setiawan, S.Kom.',
            'email' => 'apitsetiawan@pemalang.go.id',
            'password' => Hash::make('198407302009031003'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPrakomMuda->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 6. Mohamad Tarmanto
        Employee::updateOrCreate([
            'nip' => '197508062007011012'
        ], [
            'name' => 'Mohamad Tarmanto',
            'email' => 'mohamadtarmanto@pemalang.go.id',
            'password' => Hash::make('197508062007011012'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPengadministrasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 7. Abdul Wahid Zuhry
        Employee::updateOrCreate([
            'nip' => '197106071992031005'
        ], [
            'name' => 'Abdul Wahid Zuhry, S.IP, M.M.',
            'email' => 'abdulwahidzuhry@pemalang.go.id',
            'password' => Hash::make('197106071992031005'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posAnalisSDMPPIK->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 8. Rizki Septina Kusumaningsih
        Employee::updateOrCreate([
            'nip' => '198609022015022001'
        ], [
            'name' => 'Rizki Septina Kusumaningsih, S.T.',
            'email' => 'rizkiseptina@pemalang.go.id',
            'password' => Hash::make('198609022015022001'),
            'gender' => 'P',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPrakomLanjutan->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // 9. Tusmanto
        Employee::updateOrCreate([
            'nip' => '198208282014061003'
        ], [
            'name' => 'Tusmanto',
            'email' => 'tusmanto@pemalang.go.id',
            'password' => Hash::make('198208282014061003'),
            'gender' => 'L',
            'department_id' => $deptPPIK->id,
            'position_id' => $posPengadministrasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPIK->id,
            'is_active' => true,
        ]);

        // K. Kabid PSDM (Head)
        $kabidPSDM = Employee::updateOrCreate([
            'nip' => '198205102008011010'
        ], [
            'name' => 'Bagas Arya, S.E, M.Si',
            'email' => 'kabid.psdm@pemalang.go.id',
            'password' => Hash::make('198205102008011010'),
            'gender' => 'L',
            'department_id' => $deptPSDM->id,
            'position_id' => $posKabid->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // L. Staf PSDM (Employee - Subordinate of Kabid PSDM)
        $posStafPSDM = Position::firstOrCreate(['name' => 'Analis Pengembangan Kompetensi', 'department_id' => $deptPSDM->id], ['level' => '3', 'is_active' => true]);
        Employee::updateOrCreate([
            'nip' => '199602202020011011'
        ], [
            'name' => 'Wati Permatasari, S.Kom',
            'email' => 'wati.psdm@pemalang.go.id',
            'password' => Hash::make('199602202020011011'),
            'gender' => 'P',
            'department_id' => $deptPSDM->id,
            'position_id' => $posStafPSDM->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPSDM->id,
            'is_active' => true,
        ]);

        // M. Kasubbag Bina Program dan Keuangan (Subordinate of Sekretaris)
        Employee::updateOrCreate([
            'nip' => '198711302011011012'
        ], [
            'name' => 'Reza Pratama, S.E',
            'email' => 'reza.keuangan@pemalang.go.id',
            'password' => Hash::make('198711302011011012'),
            'gender' => 'L',
            'department_id' => $deptSekretariat->id,
            'position_id' => $posKasubbagBinaProgram->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $sekretaris->id,
            'is_active' => true,
        ]);
    }
}
