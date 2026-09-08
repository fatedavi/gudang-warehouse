<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiGudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kapasitas_maks',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_maks' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public static function kapasitasAktif(): int
    {
        return (int) self::aktif()->latest('id')->value('kapasitas_maks');
    }
}
