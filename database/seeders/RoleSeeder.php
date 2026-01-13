<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() {
        foreach (['Admin','Seller','Pelanggan'] as $r) {
            Role::create(['name'=>$r]);
        }
    }
}
