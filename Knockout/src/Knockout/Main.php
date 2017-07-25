<?php

namespace Knockout;

use pocketmine\block\Block;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Chest;
use pocketmine\inventory\PlayerInventory;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as Color;

Class Main extends PluginBase implements Listener{
    public $config = [
        "weapon" => array(),
        "armor" => array(),
        "coins" => 0,
        "k" => 0,
        "d" => 0
    ];
    public $menu = [];
    public $cooldown = [];
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder(). "data")){
            @mkdir($this->getDataFolder(). "data");
        }
        $this->getServer()->getScheduler()->scheduleRepeatingTask($this->getTask(), 20);
        // TODO: Create a config to edit ALL things.
    }
    public function getTask(){
        return new KnockoutTask($this);
    }
    public function onPreLogin(PlayerPreLoginEvent $event){
        $name = $event->getPlayer()->getName();
        if(!file_exists($this->getDataFolder(). "data/". $name . ".json")){
            $config = new Config($this->getDataFolder(). "data/". $name . ".json", Config::JSON);
            $config->setAll($this->config);
            $config->save();
        }
    }
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();

        $player->teleport($player->getLevel()->getSafeSpawn()); // TODO: Create a sign to join the game.

        $this->setNextPage($player, 0);

        $this->menu[$player->getName()] = 0;
        $this->cooldown[$player->getName()] = round(microtime(true) * 1000) + 6001;
    }
    public function customItem($player, $slot, $id, $dex, $amount, $name){
        $item = Item::get($id, $dex, $amount);
        $item->setCustomName($name);
        $player->getInventory()->setItem($slot, $item);
    }
    public function getStats($player){
        $name = $player->getName();
        $config = new Config($this->getDataFolder(). "data/". $name . ".json", Config::JSON);
        if(round(microtime(true) * 1000) - $this->cooldown[$player->getName()] < 3001){
            $player->sendMessage(Color::RED."Please wait a little bit to see your stats again!");
            return;
        }
        $player->sendMessage(Color::GRAY."----+---+---+----");
        $player->sendMessage(Color::GOLD."Kills: ". $config->get("k"));
        $player->sendMessage(Color::GOLD."Deaths: ". $config->get("d"));
        $player->sendMessage(Color::GOLD."Coins: ". $config->get("coins"));
        $player->sendMessage(Color::GRAY."----+---+---+----");
        $this->cooldown[$name] = round(microtime(true) * 1000);
    }
    public function setBack($int){
        return $int - 1;
    }
    public function armorchange(EntityArmorChangeEvent $event){
        $event->setCancelled(true);
    }
    public function setNextPage($player, int $i){
        $inv = $player->getInventory();
        $inv->clearAll();
        switch($i){
            case 0;
                $inv->clearAll();
                $inv->setItem(0, Item::get(280, 0, 1));
                $this->customItem($player, 4, 54, 0, 1, "§aUpgrades");
                $this->customItem($player, 5, 388, 0, 1, "§aEquipment");
                $this->customItem($player, 8, 339, 0, 1, "§aStats");
                break;
            case 1; // Upgrades.
                $inv->clearAll();
                $config = new Config($this->getDataFolder(). "data/". $player->getName() . ".json", Config::JSON);
                $this->customItem($player, 0, 268, 0, 1, "§eWeapon - §6" . count($config->get("weapon")). " §eUnlocked!");
                $this->customItem($player, 1, 299, 0, 1, "§eArmor");
                $this->customItem($player, 8, 35, 14, 1, "§cBack");
                break;
            case 2; // Weapon (Upgrades).
                $inv->clearAll();
                $this->customItem($player, 0, 268, 0, 1, "§6Wooden Sword - 210 Coins");
                $this->customItem($player, 1, 283, 0, 1, "§6Golden Sword - 370 Coins");
                $this->customItem($player, 2, 272, 0, 1, "§6Stone Sword - 460 Coins");
                $this->customItem($player, 3, 267, 0, 1, "§6Iron Sword - 640 Coins");
                $this->customItem($player, 4, 276, 0, 1, "§6Diamond Sword - 970 Coins");
                $this->customItem($player, 8, 35, 14, 1, "§cBack");
                break;
            case 3; // Armor (Upgrades).
                $inv->clearAll();
                $this->customItem($player, 0, 298, 0, 1, "§eHelmets");
                $this->customItem($player, 1, 299, 0, 1, "§eChestplates");
                $this->customItem($player, 2, 300, 0, 1, "§eLeggings");
                $this->customItem($player, 3, 301, 0, 1, "§eBoots");
                $this->customItem($player, 8, 35, 14, 1, "§cBack");
                break;
        }
    }
    public function onInteract(PlayerInteractEvent $event){
        $name = $event->getPlayer()->getName();
        if($event->getItem()->getId() == 54){
            $this->setNextPage($event->getPlayer(), 1);
            $event->setCancelled(true);
            $this->menu[$name] = 1;
        }
        if($event->getItem()->getId() == 35){
            $this->setNextPage($event->getPlayer(), $this->setBack($this->menu[$name]));
            $event->setCancelled(true);
            $this->menu[$name] = $this->setBack($this->menu[$name]);
        }
        $config = new Config($this->getDataFolder(). "data/". $name . ".json", Config::JSON);
        if($event->getItem()->getId() == 268 && $event->getItem()->getName() == "§eWeapon - §6" . count($config->get("weapon")). " §eUnlocked!"){
            $this->setNextPage($event->getPlayer(), 2);
            $event->setCancelled(true);
            $this->menu[$name] = 2;
        }
        if($event->getItem()->getId() == 299 && $event->getItem()->getName() == "§eArmor"){
            $event->setCancelled(true);
            $this->setNextPage($event->getPlayer(), 3);
            $this->menu[$name] = 2;
        }
        if($event->getItem()->getId() == 339){
            $this->getStats($event->getPlayer());
            $event->setCancelled(true);
        }
    }
}
