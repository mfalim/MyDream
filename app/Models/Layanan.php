<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama_layanan',
        'kategori',
        'harga',
        'dimensi_panggung',
        'daya_listrik_rigging',
        'alokasi_kru',
        'durasi_loading_teardown',
        'deskripsi',
        'grand_entrance_gate',
        'meja_akad',
        'aisle_carpet',
        'photo_booth',
        'foto_utama',
        'gallery_1',
        'gallery_2',
        'gallery_3',
        'gallery_4',
        'status',
    ];
}