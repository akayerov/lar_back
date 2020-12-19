<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
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


    public function testRefreshToken()
    {
        $token = $this->getToken();
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sYXJhdmVsLmxvY2FsOjgwODBcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE2MDc1OTQwMjMsImV4cCI6MTYwNzU5NzYyMywibmJmIjoxNjA3NTk0MDIzLCJqdGkiOiJrejZMbmx2VUlrd1d5cHJ2Iiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.rFwgLWhdp3_NJv2dPeLP4G87J93MeROis6gWXqn53eM';

        var_dump('token=', $token);
                
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $token])
                ->post($this->apiurl.'auth/refresh');
        
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
        $response->assertStatus(200);
    }
}
