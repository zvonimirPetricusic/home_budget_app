<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthTest extends TestCase
{
    public function test_register(){
        $response = $this->post('api/register', [
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'admin' 
        ]);

        $response->assertRedirect('/');
    }

    public function test_get_login(){
        $response = $this->post('api/login', [
            'email' => 'admin@admin.com',
            'password' => 'admin' 
        ]);
        
        $response->assertStatus(200);
    }

    public function test_user_dupliciation(){
        $user1 = User::make([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => bcrypt('test123')
        ]);

        $user2 = User::make([
            'name' => 'Johnny Depp',
            'email' => 'johnnydepp@gmail.com',
            'password' => bcrypt('test123')
        ]);

        $this->assertTrue($user1->email != $user2->email);
    }


}
