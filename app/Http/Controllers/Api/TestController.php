<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct()
    {
// можно защищать роуты, а можно здесь защищать методы        
//        $this->middleware('auth:api', ['except' => ['index','testdb']]);
    }

    
    public function index()
    {
        var_dump('Hello');
        return 'OK';
    }
    public function testdb(Request $request)
    {
        var_dump('testdb', ($request->user()) ? $request->user()->name : 'no user');
        $tests = DB::select('select * from test');

//        var_dump('Hello');
        return $tests;
    }
    public function testdb1(Request $request)
    {
        var_dump('testdb1', ($request->user()) ? $request->user()->name : 'no user');
        $tests = DB::select('select * from test');

//        var_dump('Hello');
        return $tests;
    }

    
}