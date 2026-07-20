<?php
$dir = __DIR__ . "/app/Models";

// Employee.php
$employee = <<<EOD
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmployeeRole;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected \$fillable = [
        "nip", "name", "email", "password", "phone", "gender", "birth_date",
        "address", "department_id", "position_id", "supervisor_id", "role_id", "is_active",
    ];

    protected \$hidden = [
        "password",
        "remember_token",
    ];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "is_active" => "boolean",
            "birth_date" => "date",
        ];
    }

    public function role() { return \$this->belongsTo(Role::class); }
    public function department() { return \$this->belongsTo(Department::class); }
    public function position() { return \$this->belongsTo(Position::class); }
    public function supervisor() { return \$this->belongsTo(Employee::class, "supervisor_id"); }
    public function subordinates() { return \$this->hasMany(Employee::class, "supervisor_id"); }
    public function assessments() { return \$this->hasMany(Assessment::class, "employee_id"); }
    public function results() { return \$this->hasMany(AssessmentResult::class, "employee_id"); }
}
EOD;
file_put_contents("$dir/Employee.php", $employee);

// Role.php
$role = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["name", "description"];
}
EOD;
file_put_contents("$dir/Role.php", $role);

// Department.php
$department = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["name", "code", "description"];

    public function employees() { return \$this->hasMany(Employee::class); }
}
EOD;
file_put_contents("$dir/Department.php", $department);

// Position.php
$position = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Position extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["department_id", "name", "level", "description"];

    public function department() { return \$this->belongsTo(Department::class); }
    public function employees() { return \$this->hasMany(Employee::class); }
}
EOD;
file_put_contents("$dir/Position.php", $position);

// Period.php
$period = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\PeriodStatus;

class Period extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["name", "month", "year", "start_date", "end_date", "is_active", "status"];

    protected \$casts = [
        "start_date" => "date",
        "end_date" => "date",
        "is_active" => "boolean",
        "status" => PeriodStatus::class,
    ];

    public function assessments() { return \$this->hasMany(Assessment::class); }
    public function results() { return \$this->hasMany(AssessmentResult::class); }
}
EOD;
file_put_contents("$dir/Period.php", $period);

// AssessmentCategory.php
$cat = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssessmentCategory extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["name", "description", "display_order"];

    public function indicators() { return \$this->hasMany(AssessmentIndicator::class, "category_id"); }
}
EOD;
file_put_contents("$dir/AssessmentCategory.php", $cat);

// AssessmentIndicator.php
$ind = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssessmentIndicator extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["category_id", "indicator", "description", "display_order", "is_active"];

    protected \$casts = [
        "is_active" => "boolean",
    ];

    public function category() { return \$this->belongsTo(AssessmentCategory::class, "category_id"); }
}
EOD;
file_put_contents("$dir/AssessmentIndicator.php", $ind);

// Assessment.php
$ass = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\AssessmentType;
use App\Enums\AssessmentStatus;

class Assessment extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["period_id", "assessor_id", "employee_id", "assessment_type", "status", "submitted_at"];

    protected \$casts = [
        "submitted_at" => "datetime",
        "assessment_type" => AssessmentType::class,
        "status" => AssessmentStatus::class,
    ];

    public function period() { return \$this->belongsTo(Period::class); }
    public function assessor() { return \$this->belongsTo(Employee::class, "assessor_id"); }
    public function employee() { return \$this->belongsTo(Employee::class, "employee_id"); }
    public function scores() { return \$this->hasMany(AssessmentScore::class); }
}
EOD;
file_put_contents("$dir/Assessment.php", $ass);

// AssessmentScore.php
$sco = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssessmentScore extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["assessment_id", "indicator_id", "score", "comment"];

    public function assessment() { return \$this->belongsTo(Assessment::class); }
    public function indicator() { return \$this->belongsTo(AssessmentIndicator::class, "indicator_id"); }
}
EOD;
file_put_contents("$dir/AssessmentScore.php", $sco);

// AssessmentResult.php
$res = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssessmentResult extends Model
{
    use HasFactory, HasUuids;

    protected \$fillable = ["employee_id", "period_id", "superior_score", "peer_score", "subordinate_score", "final_score", "category", "calculated_at"];

    protected \$casts = [
        "superior_score" => "float",
        "peer_score" => "float",
        "subordinate_score" => "float",
        "final_score" => "float",
        "calculated_at" => "datetime",
    ];

    public function employee() { return \$this->belongsTo(Employee::class); }
    public function period() { return \$this->belongsTo(Period::class); }
}
EOD;
file_put_contents("$dir/AssessmentResult.php", $res);


