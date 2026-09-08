<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Barang extends Model
{
    use HasFactory;

    public const STATUS_BARU = 'baru';

    public const STATUS_LAMA = 'lama';

    public const STATUS_DI_GUDANG = 'di_gudang';

    public const STATUS_TERJUAL = 'terjual';

    protected $fillable = [
        'kode_produk',
        'jenis_barang',
        'merk_produk',
        'ukuran_produk',
        'warna_produk',
        'kondisi_barang',
        'qty',
        'terjual',
        'keluar',
        'sisa_stok',
        'harga_gudang',
        'harga_jual',
        'margin_kotor',
        'margin_persen',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'terjual' => 'integer',
            'keluar' => 'integer',
            'sisa_stok' => 'integer',
            'harga_gudang' => 'integer',
            'harga_jual' => 'integer',
            'margin_kotor' => 'integer',
            'margin_persen' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Barang $barang) {
            $barang->historis()->create([
                'aksi' => 'masuk',
                'detail' => "Produk {$barang->jenis_barang} {$barang->merk_produk} ({$barang->kode_produk}) ditambahkan, qty {$barang->qty}, sisa stok {$barang->sisa_stok}.",
            ]);
        });

        static::updated(function (Barang $barang) {
            if (! $barang->isDirty()) {
                return;
            }

            $ubah = collect($barang->getChanges())
                ->except(['updated_at'])
                ->map(fn ($nilai, $kolom) => "$kolom: $nilai")
                ->implode(', ');

            $barang->historis()->create([
                'aksi' => 'ubah',
                'detail' => "Produk {$barang->kode_produk} diubah — {$ubah}.",
            ]);
        });

        static::deleting(function (Barang $barang) {
            HistoriBarang::query()->create([
                'barang_id' => $barang->id,
                'aksi' => 'hapus',
                'detail' => "Produk {$barang->jenis_barang} {$barang->merk_produk} ({$barang->kode_produk}, sisa stok {$barang->sisa_stok}) dihapus.",
            ]);
        });
    }

    protected function nama(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->jenis_barang} {$this->merk_produk} ukuran {$this->ukuran_produk} - {$this->warna_produk}"));
    }

    protected function status(): Attribute
    {
        return Attribute::get(function () {
            if ($this->sisa_stok <= 0) {
                return self::STATUS_TERJUAL;
            }

            return $this->pernahDikembalikan() ? self::STATUS_LAMA : self::STATUS_BARU;
        });
    }

    public function pernahDikembalikan(): bool
    {
        return $this->keluars()->where('kembali', true)->exists();
    }

    public static function statusList(): array
    {
        return [
            self::STATUS_DI_GUDANG => 'Barang di gudang',
            self::STATUS_BARU => 'Barang baru',
            self::STATUS_LAMA => 'Barang lama',
            self::STATUS_TERJUAL => 'Barang terjual',
        ];
    }

    public function scopeStokGudang($query)
    {
        return $query->where('sisa_stok', '>', 0);
    }

    public function scopeTerjual($query)
    {
        return $query->where('sisa_stok', 0);
    }

    public function scopeBaru($query)
    {
        return $query->stokGudang()
            ->whereDoesntHave('keluars', fn ($q) => $q->where('kembali', true));
    }

    public function scopeLama($query)
    {
        return $query->stokGudang()
            ->whereHas('keluars', fn ($q) => $q->where('kembali', true));
    }

    protected static array $segmenKode = [
        'Sepatu Running [unisex]' => ['RUN', 'U'],
        'Sepatu Running [male]' => ['RUN', 'M'],
        'Sepatu Running [female]' => ['RUN', 'F'],
        'Sepatu Casual [unisex]' => ['CAS', 'U'],
        'Sepatu Casual [male]' => ['CAS', 'M'],
        'Sepatu Casual [female]' => ['CAS', 'F'],
        'Sepatu Formal [unisex]' => ['FOR', 'U'],
        'Sepatu Formal [male]' => ['FOR', 'M'],
        'Sepatu Formal [female]' => ['FOR', 'F'],
        'Sepatu Futsal' => ['FUT', null],
        'Sepatu Bola' => ['BOL', null],
        'Sandal Laki-Laki' => ['SDL', 'M'],
        'Sandal Perempuan' => ['SDL', 'F'],
    ];

    public static function kodeSegmen(string $jenis): ?array
    {
        return self::$segmenKode[$jenis] ?? null;
    }

    public static function jenisOtomatis(): array
    {
        return array_keys(self::$segmenKode);
    }

    public const SUFIX_BARU = 'N';

    public const SUFIX_LAMA = 'O';

    public static function tanpaSuffix(string $kode): string
    {
        if (str_ends_with($kode, '-'.self::SUFIX_BARU) || str_ends_with($kode, '-'.self::SUFIX_LAMA)) {
            return substr($kode, 0, -2);
        }

        return $kode;
    }

    public static function buatKodeOtomatis(string $jenis, ?string $suffix = null): string
    {
        $segmen = self::kodeSegmen($jenis);

        if ($segmen === null) {
            throw new \InvalidArgumentException("Jenis barang '{$jenis}' tidak memiliki segmen kode otomatis.");
        }

        $suffix = $suffix ?? self::SUFIX_BARU;

        [$prefix, $gender] = $segmen;
        $awalan = $prefix.($gender ? "-{$gender}" : '');

        $nomorTerakhir = (int) self::query()
            ->where('kode_produk', 'like', "{$awalan}-%")
            ->pluck('kode_produk')
            ->map(fn ($kode) => (int) Str::afterLast(self::tanpaSuffix((string) $kode), '-'))
            ->max();

        return "{$awalan}-".str_pad($nomorTerakhir + 1, 3, '0', STR_PAD_LEFT).'-'.$suffix;
    }

    public function suffixKode(): string
    {
        return $this->status === self::STATUS_LAMA ? self::SUFIX_LAMA : self::SUFIX_BARU;
    }

    public function kodeTampil(): string
    {
        $kode = (string) $this->kode_produk;

        if (str_ends_with($kode, '-'.self::SUFIX_BARU) || str_ends_with($kode, '-'.self::SUFIX_LAMA)) {
            return $kode;
        }

        return $kode.'-'.$this->suffixKode();
    }

    public function perbaruiSuffixKode(): void
    {
        $denganSuffix = self::tanpaSuffix($this->kode_produk).'-'.$this->suffixKode();

        if ($this->kode_produk !== $denganSuffix) {
            $this->updateQuietly(['kode_produk' => $denganSuffix]);
        }
    }

    public function historis(): HasMany
    {
        return $this->hasMany(HistoriBarang::class);
    }

    public function keluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function sinkronkanKeluar(): void
    {
        $keluar = (int) $this->keluars()
            ->where('kembali', false)
            ->whereNull('kembali_dari')
            ->where('terjual', false)
            ->whereNull('terjual_dari')
            ->get()
            ->sum(fn ($k) => $k->jumlah - $k->jumlah_sudah_kembali - $k->jumlah_sudah_terjual);

        $this->updateQuietly(['keluar' => max(0, $keluar)]);
    }
}
