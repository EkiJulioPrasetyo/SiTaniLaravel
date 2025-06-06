<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    // Nama tabel di database (jika bukan "users")
    protected $table = 'Akun';

    // Primary key bernama id_akun (bukan id)
    protected $primaryKey = 'id_akun';

    // Kalau Anda tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa di‐mass assign
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'peran',
    ];

    // Sembunyikan kolom password jika nanti di‐serialize
    protected $hidden = [
        'password',
    ];
}
