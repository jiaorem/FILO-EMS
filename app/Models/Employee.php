<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'birth_date',
        'gender',
        'employee_id',
        'holidays',
        'employee_attendance',
        'leaves',
        'clients',
        'projects',
        'tasks',
        'assets',
        'timing_sheets',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function employeeAttendances (): HasMany
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id', 'id');
    }
}
