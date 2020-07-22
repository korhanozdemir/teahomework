<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeworkStudentResult extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

}
