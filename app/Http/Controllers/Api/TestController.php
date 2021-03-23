<?php

namespace App\Http\Controllers\Api;
use App\Classes\RabbitMQ2;
use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\AppHelper;
use App\Jobs\TestJob;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use App\Events\UserRegisteredEvent;
use App\Events\MessageEvent;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Message\AMQPMessage;

use App\Classes\RabbitMQ;
use App\Providers\RabbitServiceProvider;

class TestController extends Controller
{
    private $rabbitMQ;
    public function __construct(RabbitMQ2 $rabbitMQ)
    {
// можно защищать роуты, а можно здесь защищать методы
        $this->middleware('auth:api', ['except' => ['index','testdb',
                          'testQueue',  'testQueue1', 'getProgress','redisSave','redisLoad']]);
        $this->rabbitMQ = $rabbitMQ;
    }


    public function index()
    {
        var_dump('Hello');

        $title = AppHelper::translit('Привет мир');
        var_dump($title);
        $title = AppHelper::translit('Hello word');
        var_dump($title);
        return 'OK';
    }
    public function testdb(Request $request)
    {
        var_dump('testdb', ($request->user()) ? $request->user()->name : 'no user');
        var_dump('Data pull from DB start');
        $tests = DB::select('select * from clients');

//        var_dump('Hello');
        var_dump('Data pull from DB');
        return $tests;
    }
    public function testdb1(Request $request)
    {
        var_dump('testdb1', ($request->user()) ? $request->user()->name : 'no user');
        $tests = DB::select('select * from clients');

        var_dump('Data pull from DB');
        return $tests;
    }

    public function getClients(Request $request)
    {
  //      var_dump('getClient');
        $userName =  ($request->user()) ? $request->user()->name : 'no user';
        $tests = DB::select('select * from clients');
//        Log::info('User  success select clients:'.$request->user()->name);
        Log::channel('daily')->info('User id='.$request->user()->id. ' ('. AppHelper::translit($userName) .') get client list');

// WebSocket сообщение на фронт
// Работает через Pusher
//        event(new \App\Events\PodcastProcessed([ 'id' => 100, 'name' => "Иван"],'Андрей 2021-02-09'));
// Работает через Laravel Echo Server / Redis
//        event(new \App\Events\SocketIOTest([ 'id' => 3, 'name' => "Test Soket"]));

         event(new MessageEvent('Hello Andrey'));
//          event(new UserRegisteredEvent($request->user()));
        return $tests;

// для проверки отработки ошибок
//        return response()->json(['code' => 403, 'message' => 'Недостаточно прав'], 403);
    }

// Тест очереди
// тест продолжительной по времени операции
        public function testQueue()
    {
  // базовое применение
//        dispatch(new TestJob($task->id));
// такой способ по документации при запуске теряется  $task->id !!!
//        $job = (new TestJob())
//                   ->delay(Carbon::now()->addMinutes(2));
//        dispatch($job);

        $task = new Task;
        $task->progress = 0;
        $task->save();
        Log::channel('daily')->info('testQueue '.$task->id. ' в очередь');

        $job = (new TestJob($task->id));
   //                ->delay(now()->addMinutes(1));
        dispatch($job);

        return response()->json([ 'code' => 200, 'data'=>[], 'idTask'=> $task->id,'progress'=> 0 ],200);
    }

// тест прогресс продолжительной операции
    public function getProgress(Request $request) {
          $taskId = $request->input('task_id');
          if( !$taskId )
              return response()->json([ 'code' => 404, message => 'task not found' ],404);
          $task = Task::find($taskId);
          if( !$task )
              return response()->json([ 'code' => 404, message => 'task not found' ],404);
          return response()->json([ 'code' => 200, 'result'=>json_decode($task->result), 'idTask'=> $task->id,'progress'=> $task->progress ],200);

    }

    public function testQueue1()
    {
        $tests = DB::select('select * from clients');
   // преобразование массива stdClacc в простой массив
        $data=array_map(function($item){
            return (array) $item;
        },$tests);
   // мсссив -> коллекция -> Json
        $resultJson =  collect($data)->toJson();


        $task = Task::find(28);
        $task->progress = 100;
        $task->result = $resultJson;
        $task->save();
        return 'OK';
// в результает в базе лежит Json результат запроса и его можно получить другим запросом getProgress!
    }

    public function redisSave(Request $request) {
        $name = $request->input('name');
        if(!$name) $name='obj';

        $obj = $request->input('obj');
        var_dump('name=', $name);
        Cache::store('redis')->put($name, $obj, 600);

        $john = 'Джон';
        $anne = 'Анна';
        Cache::tags(['people', 'artists'])->put('John', $john, 600);
        Cache::tags(['people', 'authors'])->put('Anne', $anne, 600);


        return 'Ok';
    }
    public function redisLoad(Request $request) {
        $name = $request->input('name');
//        var_dump('name=', $name);
        if (Cache::store('redis')->has($name)) {
            var_dump('Key found!', $name);
        }
        else {
            var_dump('Key NOT found!', $name);
        }
        $obj = Cache::store('redis')->get($name, 'not found');

        $john = Cache::tags(['people', 'artists'])->get('John');
        $anne = Cache::tags(['people', 'authors'])->get('Anne');
        var_dump($john, $anne);
        return $obj;
    }


    public function genEvent(Request $request )
    {
        //      var_dump('getClient');
        Log::channel('daily')->info("getEvent");

        // event(new MessageEvent(message ));
        // Сообщение уходит в react Exchange RabbitMQ и там раскладывается по очередям
        // это работает
          $this->setMessageRabbitMQ_MOD("#1 It is the answer from server (" . date('H:i:s') . ')');
        return "OK";

    }

// Внедрение зависимости особого смясла не принесло, так как при каждом запросесоздается объек с connect к Rabbit
// а этого я и хотел избежать вынося подключение в отдельный провайдер
/*
    public function genEvent(Request $request, DemoOne $customServiceInstance )
    {
        //      var_dump('getClient');
        Log::channel('daily')->info("getEvent2");
        $res = $customServiceInstance->sendMessageToExchange("Hello andrey");
        return $res;

    }*/
    // RabbitMQ сообщение
    private function setMessageRabbitMQ( $message ) {
        $connection = new AMQPStreamConnection('192.168.65.2', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $exchange = 'react';
        $channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, true, false);

        $msg = new AMQPMessage('{ "key": "message", "value":"'.$message. '"}');
        $channel->basic_publish($msg, 'react', 'react');


        $channel->close();
        $connection->close();

    }

    // использование singleton экономит только в рамках одного запроса
    // новый запрос приводит к создание заново объетов и прочего!!!

    private function setMessageRabbitMQ_MOD( $message ) {
        $RMQ = RabbitMQ::getInstance();

        $RMQ->channel = $RMQ->connection->channel();
        $RMQ->channel->exchange_declare( $RMQ->exchange, AMQPExchangeType::FANOUT, false, true, false);

        $msg = new AMQPMessage('{ "key": "message", "value":"'.$message. '"}');
        $RMQ->channel->basic_publish($msg, 'react', 'react');

    }

// С использованием сервис контейнера
// Можно передавать в функцию доп параметр RabbitMQ2 $rabbitMQ как в конструкторе и использовать!
// Но я предпочмитаю передавать в конструкторе
    public function genEvent2(Request $request )
    {
        //      var_dump('getClient');
        Log::channel('daily')->info("getEvent2");
        $this->setMessageRabbitMQ_MOD2("#M1 It is the answer from server (" . date('H:i:s') . ')');
        return "OK";

    }

    private function setMessageRabbitMQ_MOD2( $message ) {
        $this->rabbitMQ->channel = $this->rabbitMQ->connection->channel();
        $this->rabbitMQ->channel->exchange_declare( $this->rabbitMQ->exchange, AMQPExchangeType::FANOUT, false, true, false);

        $msg = new AMQPMessage('{ "key": "message", "value":"'.$message. '"}');
        $this->rabbitMQ->channel->basic_publish($msg, 'react', 'react');

    }

    public function setBindExchange(  Request $request ) {
        $queue = $request->input('queue');
        $exchange = $request->input('exchange');

        $this->rabbitMQ->channel->queue_bind($queue, $exchange);
        return "OK";

    }


}
