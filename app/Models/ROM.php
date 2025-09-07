<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ROM extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'feature',
        'date_build',
        'new_updated_date'
    ];
}
