<?php

namespace Knockout;

use pocketmine\scheduler\PluginTask;

Class KnockoutTask extends PluginTask
{
    /* @var Main */
    public function __construct(Main $main)
    {
        parent::__construct($main);
        $this->main = $main;
    }

    /**
     * @param $currentTick
     */
    public function onRun($currentTick)
    {
        foreach($this->getOwner()->getServer()->getLevels() as $level){
            foreach($level->getTiles() as $tiles){
                if($tiles instanceof Sign){
                    $sign = $tiles->getText();
                    if($sign[0] == "§7[§aKnockout§7]"){
                        // Coming soon.
                    }
                }
            }
        }
    }
}
