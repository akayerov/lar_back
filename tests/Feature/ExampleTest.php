<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    private $apiurl  = 'http://laravel.local:8080/api/';
    private $token   = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9kaWdpdGFsc3ZwLmxvY2FsOjgwODBcL2FwaVwvbG9naW4iLCJpYXQiOjE1OTY0NjQ4MjIsImV4cCI6MTU5NjU1MTIyMiwibmJmIjoxNTk2NDY0ODIyLCJqdGkiOiJLQ2R1cDV3OVkybk5BQ3hsIiwic3ViIjo3LCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.QdRGC8SEBHqUGQpYQJTfZ76MjoJopa3lCTKdE3BsISA';
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFirst()
    {
        $response = $this
//                ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->get($this->apiurl);

        $response->assertStatus(200);
    }

    public function test2()
    {
        $response = $this
//                ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->get($this->apiurl.'testdb');
        
        $response->assertStatus(200);
        $dataArray = json_decode($response->content(), true);
        dd($dataArray);
    }
    
    
    
/*
    public function testGetListUsersBadToken()
    {
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . '12345'])
                ->get($this->apiurl . 'users');

        $response->assertStatus(401);
    }
    public function testGetListUsers()
    {
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $this->token])
                ->get($this->apiurl . 'users');
        
        $response->assertStatus(200);
        
        
    }
    
    public function testGetListFileTypes()
    {
        $response = $this
                ->withHeaders(['Authorization' => 'Bearer ' . $this->token])
                ->get($this->apiurl . 'digression-file-types');
        
        $response->assertStatus(200);

        $dataArray = json_decode($response->content(), true);
        
        $digressionFileTypes = $dataArray['digressionFileType'];
// проверка по числу полученных элементов
//        $this->assertTrue(count($digressionFileTypes) == 4);
        $this->assertTrue(count($digressionFileTypes) > 0);
// проверка по содержанию
        $this->assertTrue($digressionFileTypes[0]['name'] == 'тест');
// по содержанию в строке ответа        
        $data2 = $response->content();
        $str2 = '{"id":1,"name":"\u0442\u0435\u0441\u0442"},{"id":3,"name":"mp4"}';
        $this->assertTrue((strpos ($data2, $str2) != false ) ? true : false );

    }

 */
}
