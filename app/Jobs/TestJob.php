<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $taskId;
    public function __construct($taskId = null)
    {
        //
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::channel('daily')->info('Execute job start '. $this->taskId);
        for($proc = 0; $proc <= 100; $proc += 10) {
 //         Log::channel('daily')->info('Execute job '.$this->taskId. ' progress '.$proc. ' %');
          $task = Task::find($this->taskId);
          $task->progress = $proc;
          $task->save();

          sleep(1);
        }
        // Для имитации полезной работы - выборка из базы и запись результата в json
        $tests = DB::select('select * from clients');
        // преобразование массива stdClacc в простой массив
        $data=array_map(function($item){
            return (array) $item;
        },$tests);
        // мсссив -> коллекция -> Json
        $resultJson =  collect($data)->toJson();
        $task->result = $resultJson;
        $task->save();
        Log::channel('daily')->info('Execute job finish'. $this->taskId);

    }
}
