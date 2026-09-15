<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangUnit extends Model
{
    use HasFactory;

    public const STATUS_DI_GUDANG = 'di_gudang';

    public const STATUS_KELUAR = 'keluar';

    public const STATUS_TERJUAL = 'terjual';

    protected $fillable = [
        'barang_id',
        'nomor_urut',
        'pernah_keluar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pernah_keluar' => 'boolean',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function scopeStokBaru($query)
    {
        return $query->where('status', self::STATUS_DI_GUDANG)
            ->where('pernah_keluar', false);
    }

    public function scopeStokLama($query)
    {
        return $query->where('status', self::STATUS_DI_GUDANG)
            ->where('pernah_keluar', true);
    }
}
