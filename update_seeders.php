<?php
$dir = __DIR__ . "/database/seeders";

// DatabaseSeeder.php
$db = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \$this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            AdminSeeder::class,
            AssessmentCategorySeeder::class,
            AssessmentIndicatorSeeder::class,
        ]);
    }
}
EOD;
file_put_contents("$dir/DatabaseSeeder.php", $db);

// RoleSeeder.php
$role = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Enums\EmployeeRole;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        \$roles = [
            ["name" => EmployeeRole::SUPER_ADMIN->value, "description" => "Super Administrator System"],
            ["name" => EmployeeRole::ADMIN->value, "description" => "Administrator BKPSDM"],
            ["name" => EmployeeRole::HEAD->value, "description" => "Kepala Bidang / Pimpinan"],
            ["name" => EmployeeRole::EMPLOYEE->value, "description" => "Pegawai Biasa"],
        ];
        
        foreach (\$roles as \$r) {
            Role::firstOrCreate(["name" => \$r["name"]], \$r);
        }
    }
}
EOD;
file_put_contents("$dir/RoleSeeder.php", $role);

// DepartmentSeeder.php
$dept = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        \$departments = [
            ["name" => "Bidang Mutasi dan Promosi", "code" => "MUT"],
            ["name" => "Bidang Pengembangan Kompetensi Aparatur", "code" => "BANGKOM"],
            ["name" => "Bidang Pengadaan, Pemberhentian dan Informasi", "code" => "PPI"],
        ];
        
        foreach (\$departments as \$d) {
            Department::firstOrCreate(["code" => \$d["code"]], \$d);
        }
    }
}
EOD;
file_put_contents("$dir/DepartmentSeeder.php", $dept);

// PositionSeeder.php
$pos = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Department;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        \$dept = Department::first();
        if (!\$dept) return;

        \$positions = [
            ["department_id" => \$dept->id, "name" => "Kepala Bidang", "level" => "Eselon III"],
            ["department_id" => \$dept->id, "name" => "Analis SDM Aparatur", "level" => "Fungsional"],
        ];
        
        foreach (\$positions as \$p) {
            Position::firstOrCreate(["name" => \$p["name"], "department_id" => \$dept->id], \$p);
        }
    }
}
EOD;
file_put_contents("$dir/PositionSeeder.php", $pos);

// AdminSeeder.php
$admin = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Role;
use App\Enums\EmployeeRole;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        \$role = Role::where("name", EmployeeRole::ADMIN->value)->first();
        
        Employee::firstOrCreate([
            "email" => "admin@bkpsdm.go.id"
        ], [
            "nip" => "198001012005011001",
            "name" => "Admin BKPSDM",
            "password" => Hash::make("password"),
            "role_id" => \$role?->id,
            "is_active" => true,
        ]);
    }
}
EOD;
file_put_contents("$dir/AdminSeeder.php", $admin);

// AssessmentCategorySeeder.php
$cat = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentCategory;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        \$categories = [
            "Berorientasi Pelayanan",
            "Akuntabel",
            "Kompeten",
            "Harmonis",
            "Loyal",
            "Adaptif",
            "Kolaboratif"
        ];
        
        foreach (\$categories as \$index => \$c) {
            AssessmentCategory::firstOrCreate(["name" => \$c], [
                "display_order" => \$index + 1
            ]);
        }
    }
}
EOD;
file_put_contents("$dir/AssessmentCategorySeeder.php", $cat);

// AssessmentIndicatorSeeder.php
$ind = <<<EOD
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentIndicator;
use App\Models\AssessmentCategory;

class AssessmentIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        \$cat = AssessmentCategory::where("name", "Berorientasi Pelayanan")->first();
        if (\$cat) {
            AssessmentIndicator::firstOrCreate(["indicator" => "Memahami dan memenuhi kebutuhan masyarakat", "category_id" => \$cat->id]);
            AssessmentIndicator::firstOrCreate(["indicator" => "Ramah, cekatan, solutif, dan dapat diandalkan", "category_id" => \$cat->id]);
        }
    }
}
EOD;
file_put_contents("$dir/AssessmentIndicatorSeeder.php", $ind);

