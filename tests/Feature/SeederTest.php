<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('stores', ['slug' => 'armal-cellular']);
        $this->assertDatabaseHas('users', ['email' => 'owner@armalcellular.test']);
        $this->assertDatabaseHas('categories', ['category_code' => 'CAT0001']);
        $this->assertDatabaseHas('products', ['product_code' => 'PRD00001']);
        $this->assertDatabaseHas('payment_settings', ['qris_provider' => 'DANA']);
    }
}
