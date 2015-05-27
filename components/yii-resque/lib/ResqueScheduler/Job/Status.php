<?php
/**
 * Status tracker/information for a job.
 *
 * @package     ResqueScheduler
 * @subpackage  ResqueScheduler.Job
 * @author      Wan Qi Chen <kami@kamisama.me>
 * @copyright   Copyright 2013, Wan Qi Chen <kami@kamisama.me>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ResqueScheduler_Job_Status extends Resque_Job_Status
{
    const STATUS_SCHEDULED = 63;
}