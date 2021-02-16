<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factoryのインスタンスを取得
        factory(User::class)->create([
            'name' => 'めるぴっと太郎',
            'email' => 'test@test.test',
            'email_verified_at' => now(),
            'password' => Hash::make('sqdzyo58'),
        ]);
        factory(User::class)->create([
            'name' => 'test1',
            'email' => 'test1@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('sqdzyo58'),
        ]);
        factory(User::class)->create([
            'name' => 'test2',
            'email' => 'test2@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('sqdzyo58'),
        ]);
        factory(User::class)->create([
            'name' => 'test3',
            'email' => 'test3@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('sqdzyo58'),
        ]);
    }
}