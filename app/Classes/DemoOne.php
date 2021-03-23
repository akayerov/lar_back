<?php


namespace App\Classes;


use Illuminate\Support\Facades\Log;

class DemoOne
{
    public function __construct()
    {
        Log::channel('daily')->info("Constructor DemoOne...");
    }
    public function sendMessageToExchange($message) {
        Log::channel('daily')->info("Output from DemoOne...");
        return 'Output from DemoOne';

    }

}
