<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $approvers = [
            ['name' => 'Approver One', 'email' => 'approver1@gmail.com', 'sequence' => 1, 'label' => 'Initial Review'],
            ['name' => 'Approver Two', 'email' => 'approver2@gmail.com', 'sequence' => 2, 'label' => 'Department Review'],
            ['name' => 'Approver Three', 'email' => 'approver3@gmail.com', 'sequence' => 3, 'label' => 'Budget Review'],
            ['name' => 'Approver Four', 'email' => 'approver4@gmail.com', 'sequence' => 4, 'label' => 'Final Approval'],
        ];

        foreach($approvers as $a) {
            $user = User::create([
                'name' => $a['name'],
                'email' => $a['email'],
                'password' => Hash::make('password'),
                'role' => 'approver',
            ]);

            DB::table('approval_levels')->insert([
                'user_id' => $user->id,
                'sequence' => $a['sequence'],
                'label' => $a['label'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
