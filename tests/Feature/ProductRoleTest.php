<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Category;

class ProductRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $kasir;
    protected $teknisi;
    protected $gudang;
    protected $store;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->store = Store::firstOrCreate(['id' => 1], ['name' => 'Main Store', 'slug' => 'main-store', 'is_active' => true]);

        $this->owner = $this->createUserWithRole('owner');
        $this->kasir = $this->createUserWithRole('kasir');
        $this->teknisi = $this->createUserWithRole('teknisi');
        $this->gudang = $this->createUserWithRole('gudang');

        $this->category = Category::create([
            'category_code' => 'CAT0001',
            'store_id' => $this->store->id,
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);
    }

    protected function createUserWithRole(string $roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        return User::factory()->create([
            'role_id' => $role->id,
            'store_id' => $this->store->id,
        ]);
    }

    protected function getProductData($name)
    {
        return [
            'name' => $name,
            'category_code' => $this->category->category_code,
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'type' => 'physical',
            'status' => 'active',
            'unit' => 'pcs',
        ];
    }

    public function test_owner_can_manage_products()
    {
        $response = $this->actingAs($this->owner)->get('/products');
        $response->assertStatus(200);

        $response = $this->actingAs($this->owner)->post('/products', $this->getProductData('Prod Owner'));
        $response->assertRedirect('/products?tab=physical');
        $this->assertDatabaseHas('products', ['name' => 'Prod Owner']);
    }

    public function test_kasir_can_view_but_cannot_modify_products()
    {
        $response = $this->actingAs($this->kasir)->get('/products');
        $response->assertStatus(200);

        $response = $this->actingAs($this->kasir)->post('/products', $this->getProductData('Prod Kasir'));
        $response->assertStatus(403);
    }

    public function test_gudang_can_manage_products()
    {
        $response = $this->actingAs($this->gudang)->get('/products');
        $response->assertStatus(200);

        $response = $this->actingAs($this->gudang)->post('/products', $this->getProductData('Prod Gudang'));
        $response->assertRedirect('/products?tab=physical');
        $this->assertDatabaseHas('products', ['name' => 'Prod Gudang']);
    }

    public function test_teknisi_cannot_access_products()
    {
        $response = $this->actingAs($this->teknisi)->get('/products');
        $response->assertStatus(403);
    }
}
