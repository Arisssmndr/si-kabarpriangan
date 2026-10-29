<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'nama_customer',
        'identitas',
        'no_telepon',
        'alamat',
        'kota',
        'tipe',
    ];

    /**
     * Relasi ke seluruh Faktur milik customer ini.
     */
    public function fakturs(): HasMany
    {
        return $this->hasMany(Faktur::class, 'customer_id');
    }
}
