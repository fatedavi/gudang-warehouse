<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GudangPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->penjual = User::where('email', 'penjual@example.com')->firstOrFail();
    }

    private function loginSebagaiAdmin(): void
    {
        $this->actingAs($this->admin);
    }

    public function test_dashboard_menampilkan_data_dari_excel(): void
    {
        $this->loginSebagaiAdmin();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Sisa Stok Gudang')
            ->assertSee('Sepatu Running');
    }

    public function test_halaman_barang_menampilkan_produk(): void
    {
        $this->loginSebagaiAdmin();

        $this->get('/barang')
            ->assertOk()
            ->assertSee('Kode Produk');

        $this->get('/barang?cari=RUN-U-001')
            ->assertOk()
            ->assertSee('RUN-U-001')
            ->assertSee('nike');
    }

    public function test_total_seeded_sesuai_excel(): void
    {
        self::assertSame(55, Barang::count());
        self::assertSame(78, (int) Barang::sum('sisa_stok'));
        self::assertSame(273, (int) Barang::sum('qty'));
    }

    public function test_filter_status_terjual_berfungsi(): void
    {
        $this->loginSebagaiAdmin();

        $totalSisaNol = Barang::where('sisa_stok', 0)->count();
        self::assertGreaterThan(0, $totalSisaNol);

        $this->get('/barang?status=terjual')->assertOk();
    }

    public function test_konfigurasi_dan_histori_berfungsi(): void
    {
        $this->loginSebagaiAdmin();

        $this->get('/konfigurasi')->assertOk()->assertSee('Kapasitas Gudang Utama');
        $this->get('/histori')->assertOk()->assertSee('Riwayat Aktivitas');
    }

    public function test_detail_barang_berfungsi(): void
    {
        $this->loginSebagaiAdmin();

        $barang = Barang::where('kode_produk', 'RUN-U-001-N')->firstOrFail();

        $this->get("/barang/{$barang->id}")
            ->assertOk()
            ->assertSee('Harga Jual')
            ->assertSee('Sisa Stok');
    }

    public function test_generate_kode_lanjutan_dari_max_existing(): void
    {
        self::assertSame('RUN-U-007-N', Barang::buatKodeOtomatis('Sepatu Running [unisex]'));
        self::assertSame('SDL-M-005-N', Barang::buatKodeOtomatis('Sandal Laki-Laki'));
        self::assertSame('BOL-008-N', Barang::buatKodeOtomatis('Sepatu Bola'));
    }

    public function test_kode_segmen_null_untuk_jenis_tak_dikenal(): void
    {
        self::assertNull(Barang::kodeSegmen('Sepatu Kanvas'));
        self::assertNull(Barang::kodeSegmen(''));
    }

    public function test_endpoint_kode_otomatis(): void
    {
        $this->loginSebagaiAdmin();

        $this->getJson('/barang/kode?jenis=Sepatu+Running+%5Bunisex%5D')
            ->assertOk()
            ->assertJson(['tersedia' => true, 'kode' => 'RUN-U-007-N']);

        $this->getJson('/barang/kode?jenis=Sepatu+Kanvas')
            ->assertOk()
            ->assertJson(['tersedia' => false]);
    }

    public function test_store_mengisi_kode_otomatis_saat_kosong(): void
    {
        $this->loginSebagaiAdmin();

        $response = $this->post('/barang', [
            'kode_produk' => '',
            'jenis_barang' => 'Sandal Perempuan',
            'merk_produk' => 'test',
            'ukuran_produk' => '39',
            'warna_produk' => 'hitam',
            'kondisi_barang' => 'baru',
            'qty' => 5,
            'terjual' => 0,
            'harga_gudang' => 50000,
            'harga_jual' => 75000,
            'margin_kotor' => 25000,
            'margin_persen' => 50.0,
        ]);

        $response->assertRedirect(route('barang.index'));

        $barang = Barang::where('kode_produk', 'SDL-F-004-N')->firstOrFail();
        self::assertSame('Sandal Perempuan', $barang->jenis_barang);
    }

    public function test_store_wajib_manual_untuk_jenis_tak_dikenal(): void
    {
        $this->loginSebagaiAdmin();

        $response = $this->post('/barang', [
            'kode_produk' => '',
            'jenis_barang' => 'Sepatu Kanvas',
            'merk_produk' => 'test',
            'ukuran_produk' => '39',
            'warna_produk' => 'hitam',
            'kondisi_barang' => 'baru',
            'qty' => 1,
            'terjual' => 0,
            'harga_gudang' => 50000,
            'harga_jual' => 75000,
            'margin_kotor' => 25000,
            'margin_persen' => 50.0,
        ]);

        $response->assertSessionHasErrors('kode_produk');
        self::assertNull(Barang::where('jenis_barang', 'Sepatu Kanvas')->first());
    }

    public function test_update_regenerate_kode_saat_jenis_berubah(): void
    {
        $this->loginSebagaiAdmin();

        $barang = Barang::where('kode_produk', 'RUN-U-001-N')->firstOrFail();

        $this->put("/barang/{$barang->id}", [
            'kode_produk' => '',
            'jenis_barang' => 'Sepatu Futsal',
            'merk_produk' => $barang->merk_produk,
            'ukuran_produk' => $barang->ukuran_produk,
            'warna_produk' => $barang->warna_produk,
            'kondisi_barang' => $barang->kondisi_barang,
            'qty' => $barang->qty,
            'terjual' => $barang->terjual,
            'sisa_stok' => $barang->sisa_stok,
            'harga_gudang' => $barang->harga_gudang,
            'harga_jual' => $barang->harga_jual,
            'margin_kotor' => $barang->margin_kotor,
            'margin_persen' => $barang->margin_persen,
        ]);

        $barang->refresh();
        self::assertSame('FUT-004-N', $barang->kode_produk);
        self::assertSame('Sepatu Futsal', $barang->jenis_barang);
    }

    public function test_halaman_keluar_diakses_semua_role(): void
    {
        $this->actingAs($this->admin)->get('/barang-keluar')->assertOk();
        $this->actingAs($this->penjual)->get('/barang-keluar')->assertOk();
    }

    public function test_user_penjual_tidak_bisa_akses_halaman_admin(): void
    {
        $this->actingAs($this->penjual);

        $this->get('/barang')->assertRedirect(route('keluar.index'));
        $this->get('/konfigurasi')->assertRedirect(route('keluar.index'));
        $this->get('/histori')->assertRedirect(route('keluar.index'));
    }

    public function test_barang_keluar_mengurangi_stok_dan_tercatat(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 3)->orderBy('sisa_stok', 'desc')->firstOrFail();
        $sisaAwal = $barang->sisa_stok;

        $this->post('/barang-keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'catatan' => 'Dibawa ke toko.',
        ])->assertRedirect(route('keluar.index'));

        $barang->refresh();
        self::assertSame($sisaAwal - 2, $barang->sisa_stok);
        self::assertSame(2, $barang->keluar);

        $this->assertDatabaseHas('barang_keluars', [
            'barang_id' => $barang->id,
            'user_id' => $this->penjual->id,
            'jumlah' => 2,
            'kembali' => false,
        ]);

        $this->assertDatabaseHas('histori_barangs', [
            'barang_id' => $barang->id,
            'aksi' => 'keluar',
        ]);
    }

    public function test_barang_keluar_ditolak_bila_melebihi_sisa(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 0)->firstOrFail();

        $this->post('/barang-keluar', [
            'barang_id' => $barang->id,
            'jumlah' => $barang->sisa_stok + 5,
        ])->assertSessionHasErrors('jumlah');

        $barang->refresh();
        self::assertSame($barang->sisa_stok, Barang::find($barang->id)->sisa_stok);
    }

    public function test_barang_kembali_menambah_stok(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 3)->orderBy('sisa_stok', 'desc')->firstOrFail();

        $this->post('/barang-keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 3,
        ]);

        $keluar = BarangKeluar::where('kembali', false)->firstOrFail();

        $barang->refresh();
        $sisaSetelahKeluar = $barang->sisa_stok;

        $this->post("/barang-keluar/{$keluar->id}/kembali", [
            'jumlah' => 1,
        ])->assertRedirect(route('keluar.index'));

        $barang->refresh();
        self::assertSame($sisaSetelahKeluar + 1, $barang->sisa_stok);
        self::assertSame(2, $barang->keluar);
        self::assertSame(2, $keluar->sisa_belum_kembali);
        self::assertSame(1, $keluar->totalSudahKembali());
    }

    public function test_login_dengan_role_redirect_tepat(): void
    {
        $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->post('/logout')->assertRedirect(route('login'));

        $this->post('/login', [
            'email' => $this->penjual->email,
            'password' => 'password',
        ])->assertRedirect(route('keluar.index'));
    }

    public function test_login_salah_kredensial(): void
    {
        $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'salah',
        ])->assertSessionHasErrors('email');
    }

    public function test_status_baru_lama_dan_terjual(): void
    {
        $baru = Barang::where('sisa_stok', '>', 0)->firstOrFail();
        $terjual = Barang::where('sisa_stok', 0)->firstOrFail();

        self::assertSame(Barang::STATUS_BARU, $baru->status);
        self::assertSame(Barang::STATUS_TERJUAL, $terjual->status);
        self::assertSame(0, Barang::lama()->count());

        $this->actingAs($this->penjual);

        $this->post('/barang-keluar', [
            'barang_id' => $baru->id,
            'jumlah' => 1,
        ]);

        $keluar = BarangKeluar::where('kembali', false)->where('barang_id', $baru->id)->firstOrFail();

        $this->post("/barang-keluar/{$keluar->id}/kembali", ['jumlah' => 1]);

        $baru->refresh();
        self::assertSame(Barang::STATUS_LAMA, $baru->status);
        self::assertSame(1, Barang::lama()->count());
    }

    public function test_filter_barang_status_baru_dan_lama(): void
    {
        $this->loginSebagaiAdmin();

        $baru = Barang::where('sisa_stok', '>', 0)->latest('id')->firstOrFail();

        $this->get('/barang?status=baru')->assertOk()->assertSee($baru->kode_produk);

        $this->actingAs($this->penjual);
        $this->post('/barang-keluar', [
            'barang_id' => $baru->id,
            'jumlah' => 1,
        ]);

        $keluar = BarangKeluar::latest('id')->firstOrFail();
        $this->post("/barang-keluar/{$keluar->id}/kembali", ['jumlah' => 1]);

        $baru->refresh();

        $this->actingAs($this->admin);
        $this->get('/barang?status=lama')->assertOk()->assertSee($baru->kode_produk);
    }

    public function test_halaman_laporan_berfungsi(): void
    {
        $this->loginSebagaiAdmin();

        $this->get('/laporan/gudang')->assertOk()->assertSee('Barang di Gudang');
        $this->get('/laporan/baru')->assertOk()->assertSee('Barang Baru');
        $this->get('/laporan/lama')->assertOk()->assertSee('Barang Lama');
        $this->get('/laporan/terjual')->assertOk()->assertSee('Barang Terjual');
    }

    public function test_halaman_laporan_diblokir_penjual(): void
    {
        $this->actingAs($this->penjual);

        $this->get('/laporan/gudang')->assertRedirect(route('keluar.index'));
        $this->get('/laporan/baru')->assertRedirect(route('keluar.index'));
        $this->get('/laporan/lama')->assertRedirect(route('keluar.index'));
        $this->get('/laporan/terjual')->assertRedirect(route('keluar.index'));
    }

    public function test_penjual_hanya_melihat_barang_yang_dibawanya(): void
    {
        $barangPenjual = Barang::where('sisa_stok', '>', 3)->orderBy('sisa_stok', 'desc')->firstOrFail();

        $this->actingAs($this->penjual);

        $this->post('/barang-keluar', ['barang_id' => $barangPenjual->id, 'jumlah' => 2]);
        $this->post('/barang-keluar', ['barang_id' => $barangPenjual->id, 'jumlah' => 1]);

        $keluarPenjual = BarangKeluar::where('user_id', $this->penjual->id)->count();
        self::assertSame(2, $keluarPenjual);

        $response = $this->get('/barang-keluar');
        $response->assertOk();

        $barangLain = Barang::where('sisa_stok', '>', 3)->orderBy('sisa_stok', 'asc')->firstOrFail();

        $keluarLain = BarangKeluar::create([
            'barang_id' => $barangLain->id,
            'user_id' => $this->admin->id,
            'jumlah' => 1,
            'kembali' => false,
        ]);

        $response = $this->get('/barang-keluar');
        $response->assertDontSee('Diambil Oleh');

        $this->assertDatabaseHas('barang_keluars', ['id' => $keluarLain->id, 'user_id' => $this->admin->id]);

        $barangPenjual->refresh();
        $this->actingAs($this->admin);
        $this->get('/barang-keluar')->assertOk()->assertSee('Diambil Oleh');
    }

    public function test_barang_jual_memindahkan_keluar_ke_terjual(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 3)->orderBy('sisa_stok', 'desc')->firstOrFail();

        $this->post('/barang-keluar', ['barang_id' => $barang->id, 'jumlah' => 4]);

        $keluar = BarangKeluar::where('kembali', false)->where('barang_id', $barang->id)->firstOrFail();

        $barang->refresh();
        $terjualAwal = $barang->terjual;
        $keluarAwal = $barang->keluar;
        $sisaSebelumJual = $barang->sisa_stok;

        $this->post("/barang-keluar/{$keluar->id}/jual", ['jumlah' => 3])
            ->assertRedirect(route('keluar.index'));

        $barang->refresh();
        self::assertSame($sisaSebelumJual, $barang->sisa_stok);
        self::assertSame($terjualAwal + 3, $barang->terjual);
        self::assertSame($keluarAwal - 3, $barang->keluar);
        self::assertSame(1, $keluar->sisa_belum_kembali);
        self::assertSame(3, $keluar->jumlah_sudah_terjual);

        $this->assertDatabaseHas('barang_keluars', [
            'barang_id' => $barang->id,
            'terjual' => true,
            'terjual_dari' => $keluar->id,
            'jumlah' => 3,
        ]);

        $this->assertDatabaseHas('histori_barangs', [
            'barang_id' => $barang->id,
            'aksi' => 'terjual',
        ]);
    }

    public function test_barang_jual_ditolak_bila_melebihi_sisa(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 0)->firstOrFail();

        $this->post('/barang-keluar', ['barang_id' => $barang->id, 'jumlah' => 1]);

        $keluar = BarangKeluar::where('kembali', false)->firstOrFail();

        $this->post("/barang-keluar/{$keluar->id}/jual", ['jumlah' => 2])
            ->assertSessionHasErrors('jumlah');

        $barang->refresh();
        self::assertSame($barang->terjual, Barang::find($barang->id)->terjual);
    }

    public function test_barang_menjadi_terjual_saat_seluruh_dibawa_laku(): void
    {
        $this->actingAs($this->penjual);

        $barang = Barang::where('sisa_stok', '>', 2)->orderBy('sisa_stok', 'desc')->firstOrFail();

        $this->post('/barang-keluar', ['barang_id' => $barang->id, 'jumlah' => $barang->sisa_stok]);

        $keluar = BarangKeluar::where('kembali', false)->where('barang_id', $barang->id)->firstOrFail();

        $this->post("/barang-keluar/{$keluar->id}/jual", ['jumlah' => $barang->sisa_stok]);

        $barang->refresh();
        self::assertSame(0, $barang->sisa_stok);
        self::assertSame(Barang::STATUS_TERJUAL, $barang->status);
    }
}
