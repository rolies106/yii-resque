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

    start --queue=[queue_name | *] --interval=[int] --verbose=[0|1] --count=[int]
    startrecurring --queue=[queue_name | *] --interval=[int] --verbose=[0|1]
    stop --quit=[0|1]

EOD;
    }

    protected function runCommand($queue, $interval, $verbose, $count, $script)
    {
        $return = null;
        $yiiPath = Yii::getPathOfAlias('system');
        $appPath = Yii::getPathOfAlias('application');
        $resquePath = Yii::getPathOfAlias('application.components.yii-resque');

        if (!isset(Yii::app()->resque)) {
            echo 'resque component cannot be found on your console.php configuration';
            die();
        }

        $server = Yii::app()->resque->server ?: 'localhost';
        $port = Yii::app()->resque->port ?: 6379;
        $host = $server.':'.$port;
        $db = Yii::app()->resque->database ?: 0;
        $auth = Yii::app()->resque->password ?: '';
        $prefix = Yii::app()->resque->prefix;

        $command = 'nohup sh -c "PREFIX='.$prefix.' QUEUE='.$queue.' COUNT='.$count.' REDIS_BACKEND='.$host.' REDIS_BACKEND_DB='.$db.' REDIS_AUTH='.$auth.' INTERVAL='.$interval.' VERBOSE='.$verbose.' YII_PATH='.$yiiPath.' APP_PATH='.$appPath.' php '.$resquePath.'/bin/'.$script.'" >> '.$appPath.'/runtime/yii_resque_log.log 2>&1 &';

        exec($command, $return);

        return $return;
    }

    public function actionStart($queue = '*', $interval = 5, $verbose = 1, $count = 5)
    {
        $this->runCommand($queue, $interval, $verbose, $count, 'resque');
    }

    public function actionStartrecurring($queue = '*', $interval = 5, $verbose = 1, $count = 1)
    {
        $this->runCommand($queue, $interval, $verbose, $count, 'resque-scheduler');
    }

    public function actionStop($quit = null)
    {
        $quit_string = $quit ? '-s QUIT': '-9';
        exec("ps ux | grep resque | grep -v grep | awk {'print $2'} | xargs kill $quit_string");
    }
}
