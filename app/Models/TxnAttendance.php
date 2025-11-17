<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TxnAttendance extends Model
{
    protected $table = 'txn_attendance';
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'attendance_id',
        'student_class_id',
        'attendance_date',
        'status',
        'notes',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];
}
