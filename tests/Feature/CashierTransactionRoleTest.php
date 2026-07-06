<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;

class CashierTransactionRoleTest extends TestCase
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

    public function test_owner_can_access_cashier_and_transactions()
    {
        $this->actingAs($this->owner)->get('/cashier')->assertStatus(200);
        $this->actingAs($this->owner)->get('/transactions')->assertStatus(200);
        $this->actingAs($this->owner)->get('/debts')->assertStatus(200);
    }

    public function test_kasir_can_access_cashier_and_transactions()
    {
        $this->actingAs($this->kasir)->get('/cashier')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/transactions')->assertStatus(200);
        $this->actingAs($this->kasir)->get('/debts')->assertStatus(200);
    }

    public function test_teknisi_cannot_access_cashier_and_transactions()
    {
        $this->actingAs($this->teknisi)->get('/cashier')->assertStatus(403);
        $this->actingAs($this->teknisi)->get('/transactions')->assertStatus(403);
        $this->actingAs($this->teknisi)->get('/debts')->assertStatus(403);
    }

    public function test_gudang_cannot_access_cashier_and_transactions()
    {
        $this->actingAs($this->gudang)->get('/cashier')->assertStatus(403);
        $this->actingAs($this->gudang)->get('/transactions')->assertStatus(403);
        $this->actingAs($this->gudang)->get('/debts')->assertStatus(403);
    }

    public function test_only_owner_can_cancel_or_destroy_transactions()
    {
        $transaction = \App\Models\Transaction::create([
            'transaction_code' => 'TRX001',
            'store_id' => $this->store->id,
            'cashier_id' => $this->owner->id,
            'total_amount' => 1000,
            'payment_method' => 'cash',
            'status' => 'completed',
            'transaction_date' => now(),
        ]);

        $this->actingAs($this->kasir)->post("/transactions/{$transaction->transaction_code}/cancel")->assertStatus(403);
        $this->actingAs($this->kasir)->delete("/transactions/{$transaction->transaction_code}")->assertStatus(403);

        $this->actingAs($this->owner)->post("/transactions/{$transaction->transaction_code}/cancel")->assertRedirect();
    }
}
