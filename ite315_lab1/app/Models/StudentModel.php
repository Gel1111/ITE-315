<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentModel extends Model
{
    protected $table = 'student_models';

    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'course',
        'year',
        'section',
    ];
}
