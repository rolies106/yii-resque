<?php
/**
* Worker for ClassWorker
*/
class Worker_ClassWorker
{
    public function setUp()
    {
        # Set up environment for this job
        echo "Set up\n";
    }

    public function perform()
    {
        # Run task
        echo "Run\n";
    }

    public function tearDown()
    {
        # Remove environment for this job
        echo "Tear down\n";
    }
}