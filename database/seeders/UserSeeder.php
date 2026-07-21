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
        $deptUtama = Department::where('code', 'BKPSDM')->first() ?? Department::firstOrCreate(['code' => 'BKPSDM'], ['name' => 'BKPSDM Kabupaten Pemalang', 'is_active' => true]);
        $deptSekretariat = Department::where('code', 'SEKRETARIAT')->first() ?? Department::firstOrCreate(['code' => 'SEKRETARIAT'], ['name' => 'Sekretariat', 'is_active' => true]);
        $deptBinaProgram = Department::where('code', 'BINAPROG')->first() ?? Department::firstOrCreate(['code' => 'BINAPROG'], ['name' => 'Subbagian Bina Program dan Keuangan', 'is_active' => true]);
        $deptUmumKepeg = Department::where('code', 'UMUMKEPEG')->first() ?? Department::firstOrCreate(['code' => 'UMUMKEPEG'], ['name' => 'Subbagian Umum dan Kepegawaian', 'is_active' => true]);
        $deptPPI = Department::where('code', 'PPI')->first() ?? Department::firstOrCreate(['code' => 'PPI'], ['name' => 'Bidang Pengadaan, Pemberhentian dan Informasi Kepegawaian', 'is_active' => true]);
        $deptMutasi = Department::where('code', 'MUT')->first() ?? Department::firstOrCreate(['code' => 'MUT'], ['name' => 'Bidang Mutasi dan Promosi', 'is_active' => true]);
        $deptPenilaian = Department::where('code', 'PENILAIAN')->first() ?? Department::firstOrCreate(['code' => 'PENILAIAN'], ['name' => 'Bidang Penilaian dan Evaluasi Kinerja Aparatur', 'is_active' => true]);
        $deptBangkom = Department::where('code', 'BANGKOM')->first() ?? Department::firstOrCreate(['code' => 'BANGKOM'], ['name' => 'Bidang Pengembangan Sumber Daya Manusia', 'is_active' => true]);

        // 3. Positions
        $posKepalaBkpsdm = Position::firstOrCreate(['name' => 'Kepala BKPSDM Kabupaten Pemalang', 'department_id' => $deptUtama->id], ['level' => '1', 'is_active' => true]);
        
        $posSekretaris = Position::firstOrCreate(['name' => 'Sekretaris', 'department_id' => $deptSekretariat->id], ['level' => '2', 'is_active' => true]);
        $posKasubbagBinaProgram = Position::firstOrCreate(['name' => 'Kepala Subbagian Bina Program dan Keuangan', 'department_id' => $deptBinaProgram->id], ['level' => '3', 'is_active' => true]);
        $posKasubbagUmum = Position::firstOrCreate(['name' => 'Kepala Subbagian Umum dan Kepegawaian', 'department_id' => $deptUmumKepeg->id], ['level' => '3', 'is_active' => true]);
        
        $posKabidPPI = Position::firstOrCreate(['name' => 'Kepala Bidang Pengadaan, Pemberhentian dan Informasi Kepegawaian', 'department_id' => $deptPPI->id], ['level' => '2', 'is_active' => true]);
        $posKabidMutasi = Position::firstOrCreate(['name' => 'Kepala Bidang Mutasi dan Promosi', 'department_id' => $deptMutasi->id], ['level' => '2', 'is_active' => true]);
        $posKabidPenilaian = Position::firstOrCreate(['name' => 'Kepala Bidang Penilaian dan Evaluasi Kinerja Aparatur', 'department_id' => $deptPenilaian->id], ['level' => '2', 'is_active' => true]);
        $posKabidBangkom = Position::firstOrCreate(['name' => 'Kepala Bidang Pengembangan Sumber Daya Manusia', 'department_id' => $deptBangkom->id], ['level' => '2', 'is_active' => true]);
        
        $posAnalisMutasi = Position::firstOrCreate(['name' => 'Analis SDM Aparatur Ahli Muda', 'department_id' => $deptMutasi->id], ['level' => '3', 'is_active' => true]);
        $posPengelolaPPI = Position::firstOrCreate(['name' => 'Pengelola Kepegawaian', 'department_id' => $deptPPI->id], ['level' => '3', 'is_active' => true]);
        $posStafBangkom = Position::firstOrCreate(['name' => 'Staf Pelaksana', 'department_id' => $deptBangkom->id], ['level' => '4', 'is_active' => true]);

        // 4. Create Users / Employees with Password = NIP

        // A. Admin BKPSDM
        Employee::updateOrCreate([
            'nip' => '198001012005011001'
        ], [
            'name' => 'Administrator BKPSDM',
            'email' => 'admin@pemalang.go.id',
            'password' => Hash::make('198001012005011001'),
            'department_id' => $deptUtama->id,
            'position_id' => $posKepalaBkpsdm->id,
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
            'department_id' => $deptUtama->id,
            'position_id' => $posKepalaBkpsdm->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => null,
            'is_active' => true,
        ]);

        // Sekretaris (Head of Sekretariat)
        $sekretaris = Employee::updateOrCreate([
            'nip' => '197201012000031001'
        ], [
            'name' => 'Budi Raharjo, S.STP, M.Si',
            'email' => 'sekretaris@pemalang.go.id',
            'password' => Hash::make('197201012000031001'),
            'gender' => 'L',
            'department_id' => $deptSekretariat->id,
            'position_id' => $posSekretaris->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // C. Kabid Mutasi dan Promosi (Head)
        $kabidMutasi = Employee::updateOrCreate([
            'nip' => '197508201999031002'
        ], [
            'name' => 'Drs. Bambang Wijaya, M.M',
            'email' => 'kabid.mutasi@pemalang.go.id',
            'password' => Hash::make('197508201999031002'),
            'gender' => 'L',
            'department_id' => $deptMutasi->id,
            'position_id' => $posKabidMutasi->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // D. Kabid Pengembangan Sumber Daya Manusia (Head)
        $kabidBangkom = Employee::updateOrCreate([
            'nip' => '197803152002122003'
        ], [
            'name' => 'Siti Rahmawati, S.SH, M.Si',
            'email' => 'kabid.bangkom@pemalang.go.id',
            'password' => Hash::make('197803152002122003'),
            'gender' => 'P',
            'department_id' => $deptBangkom->id,
            'position_id' => $posKabidBangkom->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // E. Kabid PPI (Head)
        $kabidPPI = Employee::updateOrCreate([
            'nip' => '198011252006041004'
        ], [
            'name' => 'Eko Kurniawan, S.STP',
            'email' => 'kabid.ppi@pemalang.go.id',
            'password' => Hash::make('198011252006041004'),
            'gender' => 'L',
            'department_id' => $deptPPI->id,
            'position_id' => $posKabidPPI->id,
            'role_id' => $headRole?->id,
            'supervisor_id' => $kepala->id,
            'is_active' => true,
        ]);

        // Kabid Penilaian dan Evaluasi Kinerja (Head)
        $kabidPenilaian = Employee::updateOrCreate([
            'nip' => '198212122008011001'
        ], [
            'name' => 'Agus Santoso, S.Kom, M.M.',
            'email' => 'kabid.penilaian@pemalang.go.id',
            'password' => Hash::make('198212122008011001'),
            'gender' => 'L',
            'department_id' => $deptPenilaian->id,
            'position_id' => $posKabidPenilaian->id,
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
            'position_id' => $posAnalisMutasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidMutasi->id,
            'is_active' => true,
        ]);

        // G. Staf Pelaksana Mutasi (Employee - Subordinate of Kabid Mutasi)
        Employee::updateOrCreate([
            'nip' => '199207182015031006'
        ], [
            'name' => 'Ahmad Fauzi, A.Md',
            'email' => 'fauzi.mutasi@pemalang.go.id',
            'password' => Hash::make('199207182015031006'),
            'gender' => 'L',
            'department_id' => $deptMutasi->id,
            'position_id' => $posAnalisMutasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidMutasi->id,
            'is_active' => true,
        ]);

        // H. Analis SDM Bangkom (Employee - Subordinate of Kabid Bangkom)
        Employee::updateOrCreate([
            'nip' => '199009052014022007'
        ], [
            'name' => 'Dewi Lestari, S.Kom',
            'email' => 'dewi.bangkom@pemalang.go.id',
            'password' => Hash::make('199009052014022007'),
            'gender' => 'P',
            'department_id' => $deptBangkom->id,
            'position_id' => $posAnalisMutasi->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidBangkom->id,
            'is_active' => true,
        ]);

        // I. Pengelola Kepegawaian PPI (Employee - Subordinate of Kabid PPI)
        Employee::updateOrCreate([
            'nip' => '199512012019031008'
        ], [
            'name' => 'Budi Santoso, S.Sos',
            'email' => 'budi.ppi@pemalang.go.id',
            'password' => Hash::make('199512012019031008'),
            'gender' => 'L',
            'department_id' => $deptPPI->id,
            'position_id' => $posPengelolaPPI->id,
            'role_id' => $employeeRole?->id,
            'supervisor_id' => $kabidPPI->id,
            'is_active' => true,
        ]);
    }
}
