<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingAccountMapping extends Model
{
    use HasFactory;

    protected $table = 'accounting_account_mappings';

    protected $fillable = [
        'mapping_key',
        'label',
        'kategori_modul',
        'kode_perkiraan',
        'nama_perkiraan',
        'keterangan',
    ];
}
