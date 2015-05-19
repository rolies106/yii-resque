# Yii Resque

Yii resque is a component for Yii to queue your background jobs, this component based on [php-resque](https://github.com/chrisboulton/php-resque) and [php-resque-scheduler](https://github.com/chrisboulton/php-resque-scheduler) with some enhancement for support phpredis, I'm also added log handler using [Monolog](https://github.com/Seldaek/monolog), already tested with [ResqueBoard](https://github.com/kamisama/ResqueBoard).

## Requirement

- php pcntl extension.
- [Redis.io](http://redis.io)
- [phpredis](https://github.com/nicolasff/phpredis) extension for better performance, otherwise it'll automatically use [Credis](https://github.com/colinmollenhour/credis) as fallback.
- Yii Framework >1.1.x

## Configuration

- Copy files to each folder
- Add to your ```config/main.php``` and your ```config/console.php```

```php
...
'components'=>array(
    ...
    'resque'=>array(
        'class' => 'application.components.yii-resque.RResque',
        'server' => 'localhost',     // Redis server address
        'port' => '6379',            // Redis server port
        'database' => 0,             // Redis database number
        'password' => '',            // Redis password auth, set to '' or null when no auth needed
        'includeFiles' => array()    // Absolute path of files that will be included when initiate queue
    ),
    ...
)
...
```

- You may want to add these additional lines to your ```console.php``` for fixing auto loading and enabling the usage of models and helpers inside your workers :

```php
...
'import' => array(
    'application.models.*',
    'application.helpers.*',
    'application.components.*',
),
```

For performance reason you can just load required class by adding ```Yii::import()``` on your ```setUp``` function inside worker class, e.g :

```php
public function setUp()
{
    Yii::import('application.models.*');
}
```

## How to

### Create job and Workers

You can put this line where ever you want to add jobs to queue

```php
Yii::app()->resque->createJob('queue_name', 'Worker_ClassWorker', $args = array());
```

Put your workers inside Worker folder and name the class with ```Worker_``` as prefix, e.g you want to create worker with name SendEmail then you can create file inside Worker folder and name it SendEmail.php, class inside this file must be ```Worker_SendEmail```

### Delete Job

This method could delete or remove job based on queue, worker class, and/or job id :

```php
// This will remove job with key 'b6487da4b6d162f958bb06b405df6963' inside 'queue_name' queue and worker 'Worker_ClassWorker'
Yii::app()->resque->deleteJob('queue_name', 'Worker_ClassWorker', 'b6487da4b6d162f958bb06b405df6963');

// This will remove all jobs inside worker 'Worker_ClassWorker' and 'queue_name' queue
Yii::app()->resque->deleteJob('queue_name', 'Worker_ClassWorker');

// This will remove all jobs inside 'queue_name' queue
Yii::app()->resque->deleteJob('queue_name');
```

This method will return ```boolean```.

### Create Delayed Job

You can run job at specific time

```php
$time = 1332067214;
Yii::app()->resque->enqueueJobAt($time, 'queue_name', 'Worker_ClassWorker', $args = array());
```

or run job after n second 

```php
$in = 3600;
$args = array('id' => $user->id);
Yii::app()->resque->enqueueIn($in, 'email', 'Worker_ClassWorker', $args);
```

### Create Recurring Job

This is some trick that sometime useful if you want to do some recurring job like sending weekly newsletter, I just made some modification in my worker ```tearDown``` event

```php
public function tearDown()
{
    $interval = 3600; # This job will repeat every hour

    # Add next job queue based on interval
    Yii::app()->resque->enqueueJobIn($interval, 'queue_name', 'Worker_Newsletter', $args = array());
}
```

So everytime job has done completely the worker will send queue for same job.

### Get Current Queues

This will return all job in queue (EXCLUDE all active job)

```php
Yii::app()->resque->getQueues();
```

### Start and Stop workers

Run this command from your console/terminal :

Start queue

```bash
yiic rresque start
```

or 

```bash
yiic rresque start --queue=queue_name --interval=5 --verbose=0
```

Start delayed or scheduled queue

```bash
yiic rresque startrecurring
```

Stop queue

```bash
yiic rresque stop
```

Stop queue with QUIT signal

```bash
yiic rresque stop --quit=true
```

## Start Worker Options

This is available options for starting worker using `yiic` command :

* Set queue name

```bash
--queue=[queue_name]
```
This option default to `*` means all queue.

* Set interval time

```bash
--interval=[time in second]
```
Set your interval time for checking new job.

* Run in verbose mode

```bash
--verbose=[1 or 0]
```
Set to `1` if you want to see more information in log file.

* Number of worker

```bash
--count=[integer]
```

* Log handler name and log handler target

```bash
--loghandler=[string] --logtarget=[string]
```
You can see available log handler at [Monolog-Init](https://github.com/kamisama/Monolog-Init).

## Copyrights

Copyright (c) 2013 Rolies106

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Contributors

- [All Contributors](https://github.com/rolies106/yii-resque/graphs/contributors)
- You are next
