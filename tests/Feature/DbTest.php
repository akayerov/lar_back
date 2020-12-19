<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DbTest extends TestCase
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

/*
    public function testDbFree()
    {
        var_dump('Start');
     //   $token = $this->getToken();

        $response = $this
    //            ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->get($this->apiurl.'testdb');
        
        $response->assertStatus(200);
        $dataArray = json_decode($response->content(), true);
        var_dump('FREE',$dataArray);
    }
 * 
 */
    public function testDbAuth()
    {
        var_dump('testDbAuth');
        $token = $this->getToken();
        $token = '122234'; 
        var_dump($token);
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $token])
                ->get($this->apiurl.'testdb1');
        
        $dataArray = json_decode($response->content(), true);
        var_dump($dataArray);
        $response->assertStatus(200);
    }
    
}
