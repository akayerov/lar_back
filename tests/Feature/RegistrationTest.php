<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    private $apiurl  = 'http://laravel.local:8080/api/';
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRegistration()
    {
        $response = $this
//                ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->post($this->apiurl.'auth/registration', [
                    'name' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => '123456',
                ]);
        
        $response->assertStatus(200);
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
    }
}
