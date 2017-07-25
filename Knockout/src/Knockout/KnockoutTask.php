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

    }
}