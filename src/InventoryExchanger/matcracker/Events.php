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
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class Events implements Listener{
    
    private $conf;
    private $creative = [];
    private $inv = [];

    public function __construct(Configs $cf){
        $this->conf = $cf;
    }

    public function onPlayerDeath(PlayerDeathEvent $event){
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

    public function onPlayerDrop(PlayerDropItemEvent $event){
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

    public function onPlayerMove(PlayerMoveEvent $event){
        if(!$this->conf->getOption("enabled-worlds-inventory")) return;

        if($this->conf->getConfig()->get("enable-prefix") === true)
            $prefix = "&b[InventoryExchanger] ";
        else
            $prefix = "";

        $p = $event->getPlayer();
        if($p->hasPermission("invetoryexchanger.sharedinv.change")) return;
        $name = strtolower($p->getName());
        $world = strtolower($event->getTo()->getLevel()->getFolderName());
        $this->conf->createInv($p, $world);
        
        $this->inv = [$name];
        $confWorlds = $this->inv["Worlds"];
        $confLastWorlds = $this->inv["LastWorld"];
        $inv = $p->getInventory();
        if(isset($this->creative[$name])){
            foreach($this->creative[$name] as $k => $i){
                $this->creative[$name][$k] = Item::get(...(explode(":",$i)));
            }
            $inv->setContents($this->creative[$name]);
            unset($this->creative[$name]);
        }
        if($confLastWorlds !== $world){
            $confWorlds[$confLastWorlds] = [];

            if(!isset($confLastWorlds[$world]))
                $confWorlds[$world] = [];

            foreach($inv->getContents() as $i)
                if($i->getId() !== 0 and $i->getCount() > 0)
                    $confWorlds[$confLastWorlds][] = $i->getId() . ":" . $i->getDamage() . ":" . $i->getCount();

            foreach($confWorlds[$world] as $k => $i)
                $confWorlds[$world][$k] = Item::get(...(explode(":",$i)));

            $inv->setContents($confWorlds[$world]);
            $confWorlds[$world] = [];
            $this->inv[$name] = [
                "LastWorld" => $world,
                "Worlds" => $confWorlds
            ];
            $this->conf->saveYml();
            if(!$this->conf->getOption("hide-message-onChange"))
                $p->sendMessage($prefix . $this->conf->translateColors("&", $this->conf->getOption("message-changed-inventory")));
        }
    }

    public function onPlayerGameModeChange(PlayerGameModeChangeEvent $event){
        if(!$this->conf->getOption("enabled-worlds-inventory")) return;

        if($this->conf->getConfig()->get("enable-prefix") === true)
            $prefix = "&b[InventoryExchanger] ";
        else
            $prefix = "";

        if($event->isCancelled()) return;
        $p = $event->getPlayer();

        if(!$p->hasPermission("invetoryexchanger.sharedinv.change")) return;
        $name = strtolower($p->getName());
        $world = strtolower($p->getLevel()->getFolderName());
        $this->conf->createInv($p, $world);
        $confWorlds = $this->inv[$name]["Worlds"][$world];
        $mode = $event->getNewGamemode();
        
        if($mode == 1){
            $inv = $p->getInventory();
            foreach($inv->getContents() as $i){
                if($i->getId() !== 0 and $i->getCount() > 0) $wiw[] = $i->getId().":".$i->getDamage().":".$i->getCount();
            }
            $inv->clearAll();
        }else{
            $this->creative[$name] = $confWorlds;
            $confWorlds= [];
        }
        $this->inv[$name]["Worlds"][$world] = $confWorlds;
        $this->conf->saveYml();

        if(!$this->conf->getOption("hide-message-onChange"))
            $p->sendMessage($prefix . $this->conf->translateColors("&", $this->conf->getOption("message-changed-gamemode")));
    }
}