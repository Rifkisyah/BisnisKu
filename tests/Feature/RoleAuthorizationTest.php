<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $ownerRole;
    protected $kasirRole;
    protected $ownerUser;
    protected $kasirUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ownerRole = Role::create(['name' => 'owner']);
        $this->kasirRole = Role::create(['name' => 'kasir']);
        
        $store = Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'is_active' => true,
        ]);
        
        $this->ownerUser = User::factory()->create([
            'role_id' => $this->ownerRole->id,
            'store_id' => $store->id,
        ]);
        
        $this->kasirUser = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $store->id,
        ]);
    }

    public function test_owner_can_access_employees_page()
    {
        $response = $this->actingAs($this->ownerUser)->get('/employees');
        $response->assertStatus(200);
    }

    public function test_kasir_cannot_access_employees_page()
    {
        $response = $this->actingAs($this->kasirUser)->get('/employees');
        $response->assertStatus(403);
    }
}
