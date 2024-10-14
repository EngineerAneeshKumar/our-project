<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thoughts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_name' , 'thoughts_content', 'user_profile', 'bg_img'
    ];
}
