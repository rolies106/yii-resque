<?php
/**
 * Yii Resque Component
 *
 * Yii component to work with php resque
 *
 * PHP version 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author        Rolies Deby <rolies106@gmail.com>
 * @copyright     Copyright 2012, Rolies Deby <rolies106@gmail.com>
 * @link          http://www.rolies106.com/
 * @package       yii-resque
 * @since         0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class RResque extends CApplicationComponent
{
    /**
     * @var string Redis server address
     */
    public $server = 'localhost';

    /**
     * @var string Redis port number
     */
    public $port = '6379';

    /**
     * @var int Redis database index
     */
    public $database = 0;

    /**
     * @var string Redis password auth
     */
    public $password = '';


    public $prefix = '';

    /**
     * @var mixed include file in daemon (userul for defining YII_DEBUG, etc), may be string or array
     */
    public $includeFiles = '';

    /**
     * Initializes the connection.
     */
    public function init()
    {
        parent::init();
        
        if(!class_exists('RResqueAutoloader', false)) {
            # Turn off our amazing library autoload
            spl_autoload_unregister(array('YiiBase','autoload'));

            # Include Autoloader library
            include(dirname(__FILE__) . '/RResqueAutoloader.php');

            # Run request autoloader
            RResqueAutoloader::register();

            # Give back the power to Yii
            spl_autoload_register(array('YiiBase','autoload'));
        }

        Resque::setBackend($this->server . ':' . $this->port, $this->database, $this->password);
        if ($this->prefix) {
          Resque::redis()->prefix($this->prefix);    
        }
        
    }

    /**
     * Create a new job and save it to the specified queue.
     *
     * @param string $queue The name of the queue to place the job in.
     * @param string $class The name of the class that contains the code to execute the job.
     * @param array $args Any optional arguments that should be passed when the job is executed.
     *
     * @return string
     */
    public function createJob($queue, $class, $args = array(), $track_status = false)
    {
        return Resque::enqueue($queue, $class, $args, $track_status);
    }

    /**
     * Delete a job based on job id or key, if worker_class is empty then it'll remove
     * all jobs within the queue, if job_key is empty then it'll remove all jobs within
     * provided queue and worker_class
     *
     * @param string $queue The name of the queue to place the job in.
     * @param string $worker_class The name of the class that contains the code to execute the job.
     * @param string $job_key Job key
     * 
     * @return bool
     */
    public function deleteJob($queue, $worker_class = null, $job_key = null)
    {
        if (!empty($job_key) && !empty($worker_class))
            return Resque::dequeue($queue, array($worker_class => $job_key)); // Remove job with specific job key
        else if (!empty($worker_class) && empty($job_key))
            return Resque::dequeue($queue, array($worker_class)); // Remove all jobs inside specified worker and queue
        else 
            return Resque::dequeue($queue); // Remove all jobs inside queue
    }

    /**
     * Create a new scheduled job and save it to the specified queue.
     *
     * @param int $in Second count down to job.
     * @param string $queue The name of the queue to place the job in.
     * @param string $class The name of the class that contains the code to execute the job.
     * @param array $args Any optional arguments that should be passed when the job is executed.
     *
     * @return string
     */
    public function enqueueJobIn($in, $queue, $class, $args = array())
    {
        return ResqueScheduler::enqueueIn($in, $queue, $class, $args);
    }

    /**
     * Create a new scheduled job and save it to the specified queue.
     *
     * @param timestamp $at UNIX timestamp when job should be executed.
     * @param string $queue The name of the queue to place the job in.
     * @param string $class The name of the class that contains the code to execute the job.
     * @param array $args Any optional arguments that should be passed when the job is executed.
     *
     * @return string
     */
    public function enqueueJobAt($at, $queue, $class, $args = array())
    {
        return ResqueScheduler::enqueueAt($at, $queue, $class, $args);
    }

    /**
     * Get delayed jobs count
     *
     * @return int
     */
    public function getDelayedJobsCount()
    {
        return (int)Resque::redis()->zcard('delayed_queue_schedule');
    }

    /**
     * Check job status
     *
     * @param string $token Job token ID
     *
     * @return string Job Status
     */
    public function status($token)
    {
        $status = new Resque_Job_Status($token);
        return $status->get();
    }

    /**
     * Return Redis
     *
     * @return object Redis instance
     */
    public function redis()
    {
        return Resque::redis();
    }

   /**
     * Get queues
     *
     * @return object Redis instance
     */
    public function getQueues()
    {
        return $this->redis()->zRange('delayed_queue_schedule', 0, -1);
    }
}
