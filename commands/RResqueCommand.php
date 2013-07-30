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

    start --queue=[queue_name | *] --interval=[int] --verbose=[0|1]
    startrecurring --queue=[queue_name | *] --interval=[int] --verbose=[0|1]
    stop --quit=[0|1]

EOD;
    }

    public function actionStart($queue = '*', $interval = 5, $verbose = 1)
    {
        $resquePath = YiiBase::getPathOfAlias('application.components.yii-resque');

        if (!isset(Yii::app()->resque)) {
            echo 'resque component cannot be found on your console.php configuration';
            die();
        }

        $server = (isset(Yii::app()->resque->server)) ? Yii::app()->resque->server : 'localhost';
        $port = (isset(Yii::app()->resque->port)) ? Yii::app()->resque->port : '6379';
        $db = (isset(Yii::app()->resque->database)) ? Yii::app()->resque->database : '3';
        $auth = (isset(Yii::app()->resque->password)) ? Yii::app()->resque->password : '';

        $host = $server . ':' . $port;

        $command = 'nohup sh -c "QUEUE=' . $queue . ' REDIS_BACKEND=' . $host . ' REDIS_BACKEND_DB=' . $db . ' REDIS_AUTH=' . $auth . ' INTERVAL=' . $interval . ' VERBOSE=' . $verbose . ' php ' . dirname(__FILE__) . '/../components/yii-resque/bin/resque" >> ' . dirname(__FILE__) . '/../runtime/yii_resque_log.log 2>&1 &';

        exec($command, $return);
    }

    public function actionStartrecurring($queue = '*', $interval = 5, $verbose = 1)
    {
        $resquePath = YiiBase::getPathOfAlias('application.components.yii-resque');

        if (!isset(Yii::app()->resque)) {
            echo 'resque component cannot be found on your console.php configuration';
            die();
        }

        $server = (isset(Yii::app()->resque->server)) ? Yii::app()->resque->server : 'localhost';
        $port = (isset(Yii::app()->resque->port)) ? Yii::app()->resque->port : '6379';
        $db = (isset(Yii::app()->resque->database)) ? Yii::app()->resque->database : '3';
        $auth = (isset(Yii::app()->resque->password)) ? Yii::app()->resque->password : '';

        $host = $server . ':' . $port;

        $command = 'nohup sh -c "QUEUE=' . $queue . ' REDIS_BACKEND=' . $host . ' REDIS_BACKEND_DB=' . $db . ' REDIS_AUTH=' . $auth . ' INTERVAL=' . $interval . ' VERBOSE=' . $verbose . ' php ' . dirname(__FILE__) . '/../components/yii-resque/bin/resque-scheduler" >> ' . dirname(__FILE__) . '/../runtime/yii_resque_scheduler_log.log 2>&1 &';

        exec($command, $return);
    }

    public function actionStop($quit = null)
    {
        exec("ps aux | grep resque", $out);

        foreach ($out as $pid) {
            if (strpos($pid, 'yiic rresque stop')) {
                continue;
            }

            if (strpos($pid, 'ps aux | grep resque')) {
                continue;
            }

            if (strpos($pid, 'grep resque')) {
                continue;
            }
            
            $pids = explode(' ', $pid);
            $pids = array_values(array_filter($pids, 'strlen'));

            $processID = (empty($pids[1])) ? $pids[2] : $pids[1];

            if (empty($processID)) {
                continue;
            }

            if (empty($quit)) {
                $command = 'kill -9 ' . $processID;
            } else {
                $command = 'kill -s QUIT ' . $processID;
            }

            exec($command, $a);
        }
    }
}