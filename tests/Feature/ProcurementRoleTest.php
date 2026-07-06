<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;

class ProcurementRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $kasir;
    protected $teknisi;
    protected $gudang;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->store = Store::firstOrCreate(['id' => 1], ['name' => 'Main Store', 'slug' => 'main-store', 'is_active' => true]);

        $this->owner = $this->createUserWithRole('owner');
        $this->kasir = $this->createUserWithRole('kasir');
        $this->teknisi = $this->createUserWithRole('teknisi');
        $this->gudang = $this->createUserWithRole('gudang');
    }

    protected function createUserWithRole(string $roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        return User::factory()->create([
            'role_id' => $role->id,
            'store_id' => $this->store->id,
        ]);
    }

    public function test_owner_can_manage_procurement()
    {
        $this->actingAs($this->owner)->get('/suppliers')->assertStatus(200);
        $this->actingAs($this->owner)->get('/product-purchases')->assertStatus(200);
    }

    public function test_gudang_can_manage_procurement()
    {
        $this->actingAs($this->gudang)->get('/suppliers')->assertStatus(200);
        $this->actingAs($this->gudang)->get('/product-purchases')->assertStatus(200);
    }

    public function test_kasir_cannot_manage_procurement_but_can_view_purchases()
    {
        $this->actingAs($this->kasir)->get('/suppliers')->assertStatus(403);
        $this->actingAs($this->kasir)->get('/product-purchases')->assertStatus(403);

        // Kasir CAN view show page for product-purchases (404 means it passes authorization but record not found)
        $this->actingAs($this->kasir)->get('/product-purchases/PO001')->assertStatus(404);
    }

    public function test_teknisi_cannot_manage_procurement_but_can_view_purchases()
    {
        $this->actingAs($this->teknisi)->get('/suppliers')->assertStatus(403);
        $this->actingAs($this->teknisi)->get('/product-purchases')->assertStatus(403);

        // Teknisi CAN view show page for product-purchases (404 means it passes authorization but record not found)
        $this->actingAs($this->teknisi)->get('/product-purchases/PO001')->assertStatus(404);
    }
}
