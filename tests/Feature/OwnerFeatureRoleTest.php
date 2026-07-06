<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;

class OwnerFeatureRoleTest extends TestCase
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

    public function test_owner_can_access_exclusive_features()
    {
        $this->actingAs($this->owner)->get('/employees')->assertStatus(200);
        $this->actingAs($this->owner)->get('/reports')->assertStatus(200);
        $this->actingAs($this->owner)->get('/reports/business-performance')->assertStatus(200);
        $this->actingAs($this->owner)->get('/settings/payment')->assertStatus(200);
    }

    public function test_non_owners_cannot_access_exclusive_features()
    {
        $roles = [$this->kasir, $this->teknisi, $this->gudang];
        $routes = [
            '/employees',
            '/reports',
            '/reports/business-performance',
            '/settings/payment',
        ];

        foreach ($roles as $role) {
            foreach ($routes as $route) {
                $this->actingAs($role)->get($route)->assertStatus(403);
            }
        }
    }
}
