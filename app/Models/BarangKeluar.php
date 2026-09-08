<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'user_id',
        'jumlah',
        'kembali',
        'kembali_dari',
        'terjual',
        'terjual_dari',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'kembali' => 'boolean',
            'terjual' => 'boolean',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function riwajatKembali(): HasMany
    {
        return $this->hasMany(self::class, 'kembali_dari')->where('kembali', true);
    }

    public function riwajatTerjual(): HasMany
    {
        return $this->hasMany(self::class, 'terjual_dari')->where('terjual', true);
    }

    public function jumlahSudahKembali(): Attribute
    {
        return Attribute::get(fn () => (int) $this->riwajatKembali()->sum('jumlah'));
    }

    public function jumlahSudahTerjual(): Attribute
    {
        return Attribute::get(fn () => (int) $this->riwajatTerjual()->sum('jumlah'));
    }

    public function sisaBelumKembali(): Attribute
    {
        $belum = $this->jumlah - $this->jumlah_sudah_kembali - $this->jumlah_sudah_terjual;

        return Attribute::get(fn () => max(0, $belum));
    }

    public function totalSudahKembali(): int
    {
        return $this->jumlah_sudah_kembali;
    }
}
