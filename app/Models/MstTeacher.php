<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MstTeacher Model
 * 
 * Represents a teacher in the system.
 * Attributes: teacher_id, first_name, last_name, npk, gender, birth_place, 
 *             birth_date, profile_photo, address, phone, entry_date, 
 *             password, level, status, created_at, updated_at, created_by, updated_by
 */
class MstTeacher extends Model
{
    protected $table = 'mst_teachers';
 protected $primaryKey = 'teacher_id';
public $incrementing = false;
protected $keyType = 'string';


    public $timestamps = true;

    protected $fillable = [
    'teacher_id',
    'first_name',
    'last_name',
    'npk',
    'gender',
    'birth_place',
    'birth_date',
    'profile_photo',
    'address',
    'phone',
    'entry_date',
    'password',
    'level',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the full name of the teacher
     * 
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
