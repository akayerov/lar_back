<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private $apiurl  = 'http://laravel.local:8080/api/';
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this
//                ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->post($this->apiurl.'auth/login', [
                    'name' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => '123456',
                ]);
        
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
        $response->assertStatus(200);
    }
}
