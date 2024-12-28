<?php

namespace Tests\Unit;

use App\Models\Kategory;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_kategory()
    {
        $kategory = Kategory::create([
            'nama_kategory' => 'Makanan',
        ]);

        $this->assertDatabaseHas('kategory', [
            'nama_kategory' => 'Makanan',
        ]);
    }

    /** @test */
    public function a_kategory_can_have_multiple_menus()
    {
        $kategory = Kategory::factory()->create();

        $menus = Menu::factory(3)->create(['kategory_id' => $kategory->kategory_id]);

        $this->assertCount(3, $kategory->menus);
    }

    /** @test */
    public function it_can_retrieve_menus_from_a_kategory()
    {
        $kategory = Kategory::factory()->create();
        $menu = Menu::factory()->create(['kategory_id' => $kategory->kategory_id]);

        $this->assertTrue($kategory->menus->contains($menu));
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Kategory::create([ // Missing 'nama_kategory'
        ]);
    }
}
