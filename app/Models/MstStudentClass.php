<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MstStudentClass Model
 * 
 * Represents the assignment of students to classes in a specific academic year
 * 
 * @property string $student_class_id Primary key
 * @property string $student_id Foreign key to mst_students
 * @property string $class_id Foreign key to mst_classes
 * @property string $academic_year_id Foreign key to mst_academic_year
 * @property string $status Active/Inactive status
 * @property string $created_by User who created the record
 * @property string $updated_by User who last updated the record
 * @property \Carbon\Carbon $created_at Creation timestamp
 * @property \Carbon\Carbon $updated_at Last update timestamp
 */
class MstStudentClass extends Model
{
    /**
     * Table name
     */
    protected $table = 'mst_student_classes';

    /**
     * Primary key
     */
    protected $primaryKey = 'student_class_id';

    /**
     * Primary key is not auto-incrementing
     */
    public $incrementing = false;

    /**
     * Primary key type
     */
    protected $keyType = 'string';

    /**
     * Fillable attributes
     */
    protected $fillable = [
        'student_class_id',
        'student_id',
        'class_id',
        'academic_year_id',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Timestamp columns
     */
    public $timestamps = true;
}
