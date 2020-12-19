<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayloadTest extends TestCase
{
    private $apiurl  = 'http://laravel.local:8080/api/';
    
    /**
     * A basic test example.
     *
     * @return void
     */
    protected function getToken() {
        $response = $this
                ->post($this->apiurl.'auth/login', [
                    'name' => 'admin',
                    'email' => 'admin@admin.com',
                    'password' => '123456',
                ]);
        if( $response->status() == 200 ) {
            $dataArray = json_decode($response->content(), true);
            return $dataArray['access_token'];
            
        }       
        else 
            return '';        
    }


    public function testUser()
    {
        $token = $this->getToken();
        var_dump('token=', $token);
                
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $token])
                ->get($this->apiurl.'auth/payload');
        
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
        $response->assertStatus(200);
    }
}
