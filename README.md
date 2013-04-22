# Yii Resque

Yii resque is a component for Yii to queue your background jobs, this component based on [php-resque](https://github.com/chrisboulton/php-resque) with some enhancement for support phpredis.

## Requirement

- php pnctl extension.
- [Redis.io](http://redis.io)
- [phpredis](https://github.com/nicolasff/phpredis) extension for better performance, otherwise it'll automatically using [Credis](https://github.com/colinmollenhour/credis) as fallback.
- Yii Framework >1.1.x

## Configuration

- Copy files to each folder
- Add to your ```config.php``` and your ```console.php```

```php
    ...
    'components'=>array(
        ...
        'resque'=>array(
            'class' => 'application.components.yii-resque.RResque',
            'server' => 'localhost',    // Redis server address
            'port' => '6379',           // Redis server port
            'database' => 0             // Redis database number
        ),
        ...
    )
    ...
```

- Change path for Yii framework folder in ```components/yii-resque/bin/resque```

## How to

### Create job and Workers

You can put this line where ever you want to add jobs to queue

```php
    Yii::app()->resque->createJob('queue_name', 'Worker_ClassWorker', $args = array());
```

Put your workers inside Worker folder and name the class with ```Worker_``` as prefix, e.g you want to create worker with name SendEmail then you can create file inside Worker folder and name it SendEmail.php, class inside this file must be ```Worker_SendEmail```

### Start and Stop workers

Run this command from your console/terminal :

Start queue

```bash
    yiic rresque start
```

Stop queue :

```bash
    yiic rresque stop
```

Stop queue with QUIT signal :

```bash
    yiic rresque stop --quit=true
```

## Copyrights

Copyright (c) 2013 Rolies106

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.