<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) {
            $feedbackId = $faker->numberBetween(1, 100);
            $commentCount = $faker->numberBetween(1, 15);

            for ($j = 0; $j < $commentCount; $j++) {
                DB::table('comments')->insert([
                    'message' => $faker->sentence,
                    'user_id' => $faker->numberBetween(1, 50),
                    'feedback_id' => $feedbackId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
