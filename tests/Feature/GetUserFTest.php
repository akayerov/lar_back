<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserFTest extends TestCase
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
//        $token = $this->getToken();
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sYXJhdmVsLmxvY2FsOjgwODBcL2FwaVwvYXV0aFwvcmVmcmVzaCIsImlhdCI6MTYwNzU5OTE2MywiZXhwIjoxNjA3NjAyNzYzLCJuYmYiOjE2MDc1OTkxNjMsImp0aSI6IjA3Q1FyVnd4TGhlaWhkczkiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.SQDDPsGzWSu7-FwiDFYwbaxAr1bQ56K7LySMRdx3OvU';
        var_dump('token=', $token);
                
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $token])
                ->get($this->apiurl.'user');
        
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
        $response->assertStatus(200);
    }
}
