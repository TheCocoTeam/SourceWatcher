<?php

namespace Coco\SourceWatcher\Core;

interface ArrayListAccess
{
    public function get ( string $key );

    public function set ( string $key, $value ) : void;

    public function remove ( string $key ) : void;
}
