<?php
/**
 * RResque Command
 *
 * This is a console command for manage RResque workers
 *
 * @author Rolies106 <rolies106@gmail.com>
 * @version 0.1.0
 */
class RResqueCommand extends CConsoleCommand
{
    public $defaultAction = 'index';

    public function actionIndex()
    {
        echo <<<EOD
This is the command for the yii-resque component. Usage:

    ./yiic rresque <command>

Available commands are:

    start --queue=[queue_name | *] --interval=[int] --verbose[0|1]
    stop --quit=[0|1]

EOD;
    }

    public function actionStart($queue = '*', $interval = 5, $verbose = 1)
    {
        $resquePath = YiiBase::getPathOfAlias('application.components.yii-resque');

        if (empty(Yii::app()->resque)) {
            echo 'resque component cannot be found on your console.php configuration';
            die();
        }

        $server = (!empty(Yii::app()->resque->server)) ? Yii::app()->resque->server : 'localhost';
        $port = (!empty(Yii::app()->resque->port)) ? Yii::app()->resque->port : '6379';
        $db = (!empty(Yii::app()->resque->database)) ? Yii::app()->resque->database : '3';

        $host = $server . ':' . $port;

        $command = 'nohup sh -c "QUEUE=' . $queue . ' REDIS_BACKEND=' . $host . ' REDIS_BACKEND_DB=' . $db . ' INTERVAL=' . $interval . ' VERBOSE=' . $verbose . ' php ' . dirname(__FILE__) . '/../components/yii-resque/bin/resque" >> ' . dirname(__FILE__) . '/../runtime/yii_resque_log.log 2>&1 &';

        exec($command, $return);
    }

    public function actionStop($quit = null)
    {
        exec("ps aux | grep resque", $out);

        foreach ($out as $pid) {
            if (strpos($pid, 'yiic rresque stop')) {
                continue;
            }
            
            $pids = explode(' ', $pid);

            $processID = (empty($pids[1])) ? $pids[2] : $pids[1];

            if (empty($processID)) {
                continue;
            }

            if (empty($quit)) {
                $command = 'kill ' . $processID;
            } else {
                $command = 'kill -s SIGQUIT' . $processID;
            }

            exec($command);
        }
    }
}