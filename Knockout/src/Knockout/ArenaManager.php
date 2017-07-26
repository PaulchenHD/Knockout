<?php

namespace Knockout;

class ArenaManager {
    public $active = [];
    public $maxarenas = 10;

    public function getMain(){
        return new Main();
    }
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
    public function getAllActiveArenas(){
        for($i=0; $i<$this->maxarenas; $i++){

        }
    }
}
