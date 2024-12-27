<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index dapat diakses oleh admin dan kepala staf
     */
    public function test_index_page_accessible_by_admin_and_kepala_staf()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Act
        $response = $this->get(route('transaksi.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('transaksi.index');
    }

    /**
     * Test: Halaman index tidak dapat diakses oleh non-admin dan non-kepala staf
     */
    public function test_index_page_inaccessible_by_non_admin_and_non_kepala_staf()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        // Act
        $response = $this->get(route('transaksi.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
    }

    /**
     * Test: Data transaksi dapat diambil dengan filter tanggal
     */
    public function test_get_data_with_date_filter()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        Transaksi::factory()->create(['created_at' => Carbon::now()->subDays(1)]);
        Transaksi::factory()->create(['created_at' => Carbon::now()]);

        $startDate = Carbon::now()->subDays(2)->toDateString();
        $endDate = Carbon::now()->toDateString();

        // Act
        $response = $this->get(route('transaksi.data', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test: Data transaksi default hanya menampilkan hari ini tanpa filter
     */
    public function test_get_data_default_today_only()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        Transaksi::factory()->create(['created_at' => Carbon::now()->subDays(1)]);
        Transaksi::factory()->create(['created_at' => Carbon::now()]);

        // Act
        $response = $this->get(route('transaksi.data'));

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test: Halaman detail transaksi dapat diakses oleh admin dan kepala staf
     */
    public function test_show_page_accessible_by_admin_and_kepala_staf()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $transaksi = Transaksi::factory()->create();

        // Act
        $response = $this->get(route('transaksi.show', $transaksi->kode_transaksi));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('transaksi.detail');
    }

    /**
     * Test: Halaman detail transaksi tidak dapat diakses oleh non-admin dan non-kepala staf
     */
    public function test_show_page_inaccessible_by_non_admin_and_non_kepala_staf()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'Kasir']);
        $this->actingAs($user);

        $transaksi = Transaksi::factory()->create();

        // Act
        $response = $this->get(route('transaksi.show', $transaksi->kode_transaksi));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('errors.error');
    }
}
