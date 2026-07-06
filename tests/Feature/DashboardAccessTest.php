<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DashboardAccessTest extends TestCase
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
        
        $this->store = \App\Models\Store::firstOrCreate(['id' => 1], ['name' => 'Main Store', 'slug' => 'main-store', 'is_active' => true]);

        $this->owner = $this->createUserWithRole('owner');
        $this->kasir = $this->createUserWithRole('kasir');
        $this->teknisi = $this->createUserWithRole('teknisi');
        $this->gudang = $this->createUserWithRole('gudang');
    }

    protected function createUserWithRole(string $roleName)
    {
        $role = \App\Models\Role::firstOrCreate(['name' => $roleName]);
        return User::factory()->create([
            'role_id' => $role->id,
            'store_id' => $this->store->id,
        ]);
    }

    public function test_all_roles_can_access_dashboard_and_settings()
    {
        $roles = [$this->owner, $this->kasir, $this->teknisi, $this->gudang];

        foreach ($roles as $role) {
            $this->actingAs($role)->get('/dashboard')->assertStatus(200);
            $this->actingAs($role)->get('/settings')->assertStatus(200);
        }
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
        
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }
}
