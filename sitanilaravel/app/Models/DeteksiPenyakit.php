<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeteksiPenyakit extends Model
{
    protected $table = 'DeteksiPenyakit';
    protected $primaryKey = 'id_deteksi';
    public $timestamps = false;

    protected $fillable = [
        'id_akun',
        'tanggal',
        'gambar_url',
        'hasil_deteksi',
        'rekomendasi',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_akun', 'id_akun');
    }

    // Hapus file gambar otomatis saat record dihapus
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($deteksi) {
            $file = public_path('uploads/' . $deteksi->gambar_url);
            if ($deteksi->gambar_url && file_exists($file)) {
                @unlink($file);
            }
        });
    }
}
