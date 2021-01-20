<?php
    
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Traits\ResetsPasswords;

    class PasswordController extends Controller
    {
        use ResetsPasswords;
    
        public function __construct()
        {
            $this->broker = 'users';
        }
    }
