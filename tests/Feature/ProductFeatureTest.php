<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = Role::create(['name' => 'owner']);
        $store = Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'is_active' => true,
        ]);
        
        $this->user = User::factory()->create([
            'role_id' => $role->id,
            'store_id' => $store->id,
        ]);
        
        $store->update(['owner_id' => $this->user->id]);

        $this->category = Category::create([
            'category_code' => 'CAT0001',
            'store_id' => $store->id,
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_product_list()
    {
        $response = $this->actingAs($this->user)->get('/products');
        $response->assertStatus(200);
    }

    public function test_user_can_create_product_with_valid_data()
    {
        $data = [
            'name' => 'Laptop',
            'category_code' => $this->category->category_code,
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'type' => 'physical',
            'status' => 'active',
            'unit' => 'pcs',
        ];

        $response = $this->actingAs($this->user)->post('/products', $data);
        
        $response->assertRedirect('/products?tab=physical');
        $this->assertDatabaseHas('products', ['name' => 'Laptop']);
    }

    public function test_user_cannot_create_product_with_invalid_data()
    {
        $data = [
            'name' => '', // Invalid
            'category_code' => $this->category->category_code,
        ];

        $response = $this->actingAs($this->user)->post('/products', $data);
        
        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_update_product()
    {
        $product = Product::create([
            'product_code' => 'PRD00001',
            'store_id' => $this->user->store_id,
            'name' => 'Old Laptop',
            'category_code' => $this->category->category_code,
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'type' => 'physical',
            'status' => 'active',
            'unit' => 'pcs',
        ]);

        $response = $this->actingAs($this->user)->put("/products/{$product->product_code}", [
            'name' => 'New Laptop',
            'category_code' => $this->category->category_code,
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'type' => 'physical',
            'status' => 'active',
            'unit' => 'pcs',
        ]);
        
        $response->assertRedirect('/products?tab=physical');
        $this->assertDatabaseHas('products', ['name' => 'New Laptop']);
    }

    public function test_user_can_delete_product()
    {
        $product = Product::create([
            'product_code' => 'PRD00002',
            'store_id' => $this->user->store_id,
            'name' => 'Delete Me',
            'category_code' => $this->category->category_code,
            'purchase_price' => 5000000,
            'selling_price' => 6000000,
            'stock' => 10,
            'minimum_stock' => 2,
            'type' => 'physical',
            'status' => 'active',
            'unit' => 'pcs',
        ]);

        $response = $this->actingAs($this->user)->delete("/products/{$product->product_code}");
        
        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['name' => 'Delete Me']);
    }
}
