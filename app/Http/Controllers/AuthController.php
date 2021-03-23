<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
//
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\SetPasswordMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
// Работа с RabbitMq
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','registration','sendEmail','sendResetLinkEmail',
                                                    'resetPassword', 'setPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
// в качестве проверемого именпи можно использовать name или email
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->setBindExchangeRabbitMQ($credentials['email'], 'bind');
        return $this->respondWithToken($token);
    }

    // RabbitMQ подключить или отключить exhange к очереди
    private function setBindExchangeRabbitMQ( $email, $mode ) {
        $connection = new AMQPStreamConnection('192.168.65.2', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $exchange = 'react';
        $queue = $email;
        $channel->queue_declare($queue, false, true, false, false, false, new AMQPTable(array(
               'x-max-length' => 2
        )));
        $channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, true, false);
        if($mode == 'bind')
            $channel->queue_bind($queue, $exchange);
        else
            $channel->queue_unbind($queue, $exchange);     // также проверено работает
        $channel->close();
        $connection->close();
    }

    // RabbitMQ подключить или отключить exhange к очереди
    // вариант кода очередь здесь не определяется вообще
    // не очень поскольоку связка с exchange все же определяется здесь
    // иначе привязку к exchanhe надо делать во фронте, а поддерживается эт в Stomp Непоенятно
    // здесь пример толко обработки исключения  Rabbit
    private function setBindExchangeRabbitMQ2( $email, $mode ) {
        $connection = new AMQPStreamConnection('192.168.65.2', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $exchange = 'react';
        $queue = $email;
        if($mode == 'bind') {
            $channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, true, false);
            try {
                $channel->queue_bind($queue, $exchange);
            }
            catch ( \Exception $e  ) {
                Log::channel('daily')->error("Error  queue bind RabbitMQ#1");
            }
        }
        else {
            try {
                $channel->queue_unbind($queue, $exchange);     // также проверено работает
            }
            catch ( \Exception $e  ) {
                Log::channel('daily')->error("Error  queue bind RabbitMQ#1");
            }

        }
        $channel->close();
        $connection->close();

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
//        var_dump(auth()->user()->email); die();
//        $this->setBindExchangeRabbitMQ(auth()->user()->email, 'unbind');

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'fio' => auth()->user()->fio
        ]);
    }

    /**
    * User registration
    */
    public function registration()
    {
        $name = request('name');
        $email = request('email');
        $password = request('password');

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        return response()->json(['message' => 'Successfully registration!']);
    }
// test payload
    public function payload()
    {
        $payload = auth()->payload();

        return $payload->toArray();
    }

    public function loginNo()
    {
        return "No autorization";
    }
// Вторичные, дополнительные меотды регистрации
/*  генерация уникальной ссылки страницы для восстановления пароля
 */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
           return response()->json(['code' => 400, 'message' => 'no parameter email', 'link' => '' ], 400);
        }
        $email = request('email');
        $token = Str::uuid();
        $created_at = now();
        DB::insert('insert into password_resets (email, token, created_at ) values (?, ?, ?)', [$email, $token, $created_at]);
        $link = url('api/auth/resetpassword_link', [$token]);
        // отправка письма
        $this->sendEmailToAddress($email, $link);
        return response()->json(['code' => 200, 'message' => 'success', 'link' => $link ], 200);
    }

    public function resetPassword($token)
    {
        if (! $token ) {
           return response()->json(['code' => 400, 'message' => 'no parameter token' ], 400);
        }
        $resetObj = DB::table('password_resets')->where('token', $token)->first();

        if( !$resetObj)
           return response()->json(['code' => 400, 'message' => 'record not found' ], 400);

        $diffTime = time() - strtotime($resetObj->created_at);
        if( $diffTime > 1800)   // полчаса
           return response()->json(['code' => 400, 'message' => 'record is too old' ], 400);

        return view('auth.passwords.reset',['token' => $resetObj->token, 'email' => $resetObj->email]);
//        return response()->json(['code' => 200, 'message' => 'success', 'link' => '' ], 200);
    }

    public function setPassword(Request $request,$token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'password_confirmation' => 'required',
        ]);
        if ($validator->fails()) {
           return response()->json(['code' => 400, 'message' => 'no parameter password', 'link' => '' ], 400);
        }
        $password = request('password');
        $password_confirmation = request('password_confirmation');
        if($password != $password_confirmation)
           return 'Ошибка: пароли не совпадают';

        if (! $token ) {
           return response()->json(['code' => 400, 'message' => 'no parameter token' ], 400);
        }
        $resetObj = DB::table('password_resets')->where('token', $token)->first();

        if( !$resetObj)
           return response()->json(['code' => 400, 'message' => 'record not found' ], 400);

        $diffTime = time() - strtotime($resetObj->created_at);
        if( $diffTime > 1800) {  // полчаса
           return response()->json(['code' => 400, 'message' => 'record is too old' ], 400);
        }

        $user = User::where('email',$resetObj->email)->first();
        // только один раз можно сбросить пароль по одному token
        DB::table('password_resets')->where('token', $token)->delete();

        if( $user ) {
          $user->password = Hash::make($password);
          $user->save();
          Log::info('User id='.$user->id. ' email='.$user->email. ' success changed password');
          return 'Пароль успешно изменен';
        }
        else {
           return response()->json(['code' => 404, 'message' => 'user not found' ], 404);
        }
    }


    // отправка письма
    private function sendEmailToAddress($email, $link = null)
    {
        $url = $link;
        $password = '';
        Mail::to($email)->
            send(new SetPasswordMail('Установка пароля пользователя', $url, $email, $password));
        return "SendEmail";
    }

// отправка письма при регистрации
    public function sendEmail()
    {
/*
        Mail::to($email)->
            send(new SetPasswordMail('Установка пароля пользователя', $url));
 *
 */
        $url = 'http://testmail';
        $email = 'akayerov@yandex.ru';
        $password = '123456';

//        Mail::to('akayerov@yandex.ru')->
//            send(new SetPasswordMail('Тест очереди отправки', $url, $email, $password));
// через очередь
          Mail::to('akayerov@yandex.ru')
                ->later(now()->addMinutes(2), new SetPasswordMail('Тест очереди отправки почты', $url, $email, $password));
          Log::channel('daily')->info('SendEmail - задание отправлено в очередь');
          return "SendEmail";
    }



}
