<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Category;

class CategoryRoleTest extends TestCase
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

    public function test_owner_can_manage_categories()
    {
        $response = $this->actingAs($this->owner)->get('/categories');
        $response->assertStatus(200);

        $response = $this->actingAs($this->owner)->post('/categories', [
            'name' => 'Test Category Owner',
            'type' => 'product',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Test Category Owner']);
    }

    public function test_kasir_cannot_access_categories()
    {
        $response = $this->actingAs($this->kasir)->get('/categories');
        $response->assertStatus(403);
    }

    public function test_teknisi_cannot_access_categories()
    {
        $response = $this->actingAs($this->teknisi)->get('/categories');
        $response->assertStatus(403);
    }

    public function test_gudang_cannot_access_categories()
    {
        $response = $this->actingAs($this->gudang)->get('/categories');
        $response->assertStatus(403);
    }
}
