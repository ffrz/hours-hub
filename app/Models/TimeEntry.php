<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $fillable = ['user_id', 'project_id', 'title', 'start_time', 'end_time', 'duration', 'notes'];
}
