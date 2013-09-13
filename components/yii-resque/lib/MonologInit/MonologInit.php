<?php
/**
 * Monolog Init File
 *
 * Very basic and light Dependency Injector Container for Monolog
 *
 * PHP version 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author        Wan Qi Chen <kami@kamisama.me>
 * @copyright     Copyright 2012, Wan Qi Chen <kami@kamisama.me>
 * @link          https://github.com/kamisama/Monolog-Init
 * @package       MonologInit
 * @since         0.1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
// namespace MonologInit;

// use Monolog\Handler;

class MonologInit_MonologInit
{
    public $handler = null;
    public $target = null;
    protected $instance = null;

    const VERSION = '0.1.1';


    public function __construct($handler = false, $target = false)
    {
        if ($handler === false || $target === false) {
            return null;
        }

        $this->createLoggerInstance($handler, $target);
    }

    /**
     * Return a Monolog Logger instance
     *
     * @return Monolog\Logger instance, ready to use
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Create a Monolog\Logger instance and attach a handler
     *
     * @param  string $handler Name of the handler, without the "Handler" part
     * @param  string $target  Comma separated list of arguments to pass to the handler
     * @return void
     */
    protected function createLoggerInstance($handler, $target)
    {
        $handlerClassName = $handler . 'Handler';

        if (class_exists('\Monolog\Logger') && class_exists('\Monolog\Handler\\' . $handlerClassName)) {
            if (null !== $handlerInstance = $this->createHandlerInstance($handlerClassName, $target)) {
                $this->instance = new \Monolog\Logger('main');
                $this->instance->pushHandler($handlerInstance);
            }

            $this->handler = $handler;
            $this->target = $target;
        }
    }

    /**
     * Create an Monolog Handler instance
     *
     * @param  string $className   Monolog handler classname
     * @param  string $handlerArgs Comma separated list of arguments to pass to the handler
     * @return Monolog\Handler instance if successfull, null otherwise
     */
    protected function createHandlerInstance($className, $handlerArgs)
    {
        if (method_exists($this, 'init' . $className)) {
            return call_user_func(array($this, 'init' . $className), explode(',', $handlerArgs));
        } else {
            return null;
        }
    }

    public function initCubeHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\CubeHandler');
        return $reflect->newInstanceArgs($args);
    }

    public function initRotatingFileHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\RotatingFileHandler');
        return $reflect->newInstanceArgs($args);
    }

    public function initChromePHPHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\ChromePHPHandler');
        return $reflect->newInstanceArgs($args);
    }

    public function initSyslogHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\SyslogHandler');
        return $reflect->newInstanceArgs($args);
    }

    public function initSocketHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\SocketHandler');
        return $reflect->newInstanceArgs($args);
    }

    public function initMongoDBHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\MongoDBHandler');
        $mongo = new \Mongo(array_shift($args));
        array_unshift($args, $mongo);
        return $reflect->newInstanceArgs($args);
    }

    /**
     *
     * @since 0.1.1
     */
    public function initCouchDBHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\CouchDBHandler');
        if (isset($args[0])) {
            $args[0] = explode(':', $args[0]);
        }
        return $reflect->newInstanceArgs($args);
    }

    /**
     *
     * @since 0.1.1
     */
    public function initHipChatHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\HipChatHandler');
        return $reflect->newInstanceArgs($args);
    }

    /**
     *
     * @since 0.1.1
     */
    public function initPushOverHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\PushOverHandler');
        return $reflect->newInstanceArgs($args);
    }

    /**
     *
     * @since 0.1.1
     */
    public function initZendMonitorHandler($args)
    {
        $reflect  = new \ReflectionClass('\Monolog\Handler\ZendMonitorHandler');
        return $reflect->newInstanceArgs($args);
    }
}

