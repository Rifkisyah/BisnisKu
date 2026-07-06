<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;

class ServiceRepairRoleTest extends TestCase
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

    public function test_owner_can_manage_service_repairs()
    {
        $this->actingAs($this->owner)->get('/service-repairs')->assertStatus(200);
        $this->actingAs($this->owner)->get('/service-repairs/create')->assertStatus(200);
    }

    public function test_teknisi_can_manage_service_repairs_but_cannot_create()
    {
        $this->actingAs($this->teknisi)->get('/service-repairs')->assertStatus(200);
        $this->actingAs($this->teknisi)->get('/service-repairs/create')->assertStatus(403);
    }

    public function test_kasir_can_manage_service_repairs()
    {
        $this->actingAs($this->kasir)->get('/service-repairs')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/service-repairs/create')->assertStatus(200);
    }

    public function test_gudang_cannot_manage_service_repairs_but_can_view()
    {
        // Gudang cannot see index or create
        $this->actingAs($this->gudang)->get('/service-repairs')->assertStatus(403);
        $this->actingAs($this->gudang)->get('/service-repairs/create')->assertStatus(403);

        // Gudang CAN view show page (404 means it passes authorization but record not found)
        $this->actingAs($this->gudang)->get('/service-repairs/SRV001')->assertStatus(404);
    }
}
