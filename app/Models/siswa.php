<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class siswa extends Model
{
    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'kelas',
        'status',
        'foto_kenangan',
    ];
}
