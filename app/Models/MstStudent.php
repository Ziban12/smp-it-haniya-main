<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MstStudent Model
 * 
 * Represents a student in the system.
 * Attributes: student_id, first_name, last_name, nis, birth_date, birth_place, gender,
 *             address, father_name, mother_name, father_phone, mother_phone, father_job,
 *             mother_job, password, entry_date, graduation_date, profile_photo, status,
 *             created_at, updated_at, created_by, updated_by
 */
class MstStudent extends Model
{
    protected $table = 'mst_students';
    protected $primaryKey = 'student_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'nis',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'father_name',
        'mother_name',
        'father_phone',
        'mother_phone',
        'father_job',
        'mother_job',
        'password',
        'entry_date',
        'graduation_date',
        'profile_photo',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'birth_date' => 'date',
        'entry_date' => 'date',
        'graduation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the full name of the student
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
