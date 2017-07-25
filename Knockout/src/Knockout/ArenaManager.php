<?php

namespace Knockout;

class ArenaManager {
    public $active = [];

    public function isActive($name){
        if(isset($this->active[$name])){
            if($this->active[$name] == true){
                return true;
            }
            return false;
        }
        return false;
    }
    public function setActive($name){
        if(!$this->isActive($name)){
            $this->active[$name] = true;
        }
    }
}