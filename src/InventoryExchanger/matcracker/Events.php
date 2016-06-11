<?php
/**
 * @author matcracker
 * Plugin for PocketMine and ImagicalMine
 * Version 2.4
 * API: 2.0.0
 */
namespace InventoryExchanger\matcracker;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\Player;

class Events implements Listener{
    
    private $conf;

    public function __construct(Configs $cf){
        $this->conf = $cf;
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void{
        if($this->conf->getConfig()->get("enable-prefix") === true)
            $prefix = "&b[InventoryExchanger] ";
        else
            $prefix = "";

        $player = $event->getEntity();

        if(!$player instanceof Player) return;

        $dropWorlds = $this->conf->getConfig()->getAll();
        if($this->conf->getOption("enable-death-drop") === false && !$player->hasPermission("inventoryexchanger.bypass.deathdrops")){
            foreach($dropWorlds["worlds-death-drop"] as $worlds){
                if($player->getLevel()->getName() === $worlds){
                    $event->setDrops([]);
                    $player->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-death-drop")));
                }
            }
        }else if($this->conf->getOption("enable-death-drop") === true && !$player->hasPermission("inventoryexchanger.bypass.deathdrops")){
            foreach($dropWorlds["worlds-death-drop"] as $worlds){
                if($player->getLevel()->getName() != $worlds){
                    $event->setDrops([]);
                    $player->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-death-drop")));
                }
            }
        }
    }

    public function onPlayerDrop(PlayerDropItemEvent $event) : void{
        if($this->conf->getConfig()->get("enable-prefix") === true)
            $prefix = "&b[InventoryExchanger] ";
        else
            $prefix = "";

        $player = $event->getPlayer();
        $dropWorlds = $this->conf->getConfig()->getAll();
        if($this->conf->getOption("enable-drop") === false && !$player->hasPermission("inventoryexchanger.bypass.drops")){
            foreach($dropWorlds["worlds-drop"] as $worlds){
                if($player->getLevel()->getName() === $worlds){
                    $event->setCancelled(true);
                    $player->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-no-drop")));
                }
            }
        }else if($this->conf->getOption("enable-drop") === true && !$player->hasPermission("inventoryexchanger.bypass.drops")){
            foreach($dropWorlds["worlds-drop"] as $worlds){
                if($player->getLevel()->getName() != $worlds){
                    $event->setCancelled(true);
                    $player->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-no-drop")));
                }
            }
        }
    }

}