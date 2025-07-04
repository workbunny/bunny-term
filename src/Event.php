<?php

// 严格模式
declare(strict_types=1);

namespace Bunny\Term;

use Bunny\Term\Control;
use Bunny\Term\Draw;

class Event
{
    public Control $control;
    public Draw $draw;
    public Keyboard $keyboard;

    public function __construct()
    {
        $this->control = new Control();
        $this->draw = new Draw();
        $this->keyboard = new Keyboard();
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
