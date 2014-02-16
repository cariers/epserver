<?php

namespace EP\Process;

class Base
{
    protected $name = 'EP';
    public function setName($name)
    {
        cli_set_process_title(sprintf('%s:%s', $this->name, $name));
    }
}