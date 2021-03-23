<?php


namespace App\Classes;


use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{
    protected static $_instance;  //экземпляр объекта
    private $url;
    private $port;
    private $user;
    private $password;
    public $connection;
    public $channel;
    public $exchange;


    /**
     * RabbitMQ constructor.
     * @param $url
     * @param $port
     * @param $user
     * @param $password
     * @param $exchange
     */
    private function __construct($url='192.168.65.2', $port = '5672', $user = 'guest', $password = 'guest', $exchange = 'root')
    {
        Log::channel('daily')->info("RabbitMQ constructor start");

        $this->url = $url;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;

        $this->connection = new AMQPStreamConnection($url, $port, $user, $password);
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($exchange, AMQPExchangeType::FANOUT, false, true, false);
        Log::channel('daily')->info("RabbitMQ constructor end");
        $this->exchange = $exchange;
    }

    public static function getInstance()
    { // получить экземпляр данного класса
        if (self::$_instance === null) { // если экземпляр данного класса  не создан
            self::$_instance = new self;  // создаем экземпляр данного класса
        }
        return self::$_instance; // возвращаем экземпляр данного класса
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    public function sendMessageToExchange($message) {
        Log::channel('daily')->info("sendMessageToExchange...");
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::FANOUT, false, true, false);

    }

}
