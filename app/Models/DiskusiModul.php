<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskusiModul extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
