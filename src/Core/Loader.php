<?php

namespace Coco\SourceWatcher\Core;

use Coco\SourceWatcher\Core\IO\Outputs\Output;

abstract class Loader extends Step
{
    protected ?Output $output = null;

    public function getOutput () : ?Output
    {
        return $this->output;
    }

    public function setOutput ( ?Output $output ) : void
    {
        $this->output = $output;
    }

    abstract public function load ( Row $row );
}
