<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pathology extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pathologies';

    protected $fillable = [
        'name',
        'description',
    ];
}
