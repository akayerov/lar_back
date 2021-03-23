<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
    protected $fillable = ['progress'];

    protected $hidden = [];
    public $timestamps = false;
    protected $table = 'long_task';
}
