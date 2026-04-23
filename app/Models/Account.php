<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function defineGroup($sub)
    {
        // ASET
        if (in_array($sub, ['Aset Lancar', 'Aset Tetap'])) {
            return 'Aset';
        }

        // KEWAJIBAN
        if (in_array($sub, ['Jangka Pendek', 'Simpanan'])) {
            return 'Kewajiban';
        }

        // MODAL
        if ($sub == 'Modal') {
            return 'Modal';
        }

        // PENDAPATAN
        if (in_array($sub, [
            'Toko Anggota',
            'Toko Umum',
            'Simpan Pinjam Anggota',
            'Simpan Pinjam Umum',
            'Jasa Anggota',
            'Jasa Umum',
            'Lain-lain'
        ])) {
            return 'Pendapatan';
        }

        // HPP
        if ($sub == 'Toko') {
            return 'HPP';
        }

        // BEBAN
        if (in_array($sub, ['Operasional', 'Lain-lain'])) {
            return 'Beban';
        }

        return null; // fallback biar aman
    }

    public static function defineNormalBalance($group)
    {
        if($group == "Aset" or $group == "Beban"){
            return "Debit";
        } else {
            return "Credit";
        }
    }
}
