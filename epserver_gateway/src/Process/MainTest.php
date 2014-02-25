<?php

namespace Server\Process;

class MainTest
{
    public function __construct()
    {
        echo '------>>';
        new \EvTimer(0., 1, function (){
            error_log('-->>' . PHP_EOL, 3, SERVER_PATH . 't.log');
        });
    }

    public function start()
    {
        echo '>>>Start>>>';
    }
}