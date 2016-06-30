<?php
/**
 * @author matcracker
 * Plugin for PocketMine and ImagicalMine
 * Version 2.4
 * API: 2.0.0
 */
namespace InventoryExchanger\matcracker;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Configs extends PluginBase{
    //Configurations
    public $cfgInv;
    public $cfgMultiInv = [];
    private $plugin;
    
    public function __construct(Main $instance){
        $this->plugin = $instance;
    }
    
    public function loadYml(){
        $inventories = new Config($this->plugin->getDataFolder() . "inventory.yml", Config::YAML);
        $sharedInv = new Config($this->plugin->getDataFolder() . "multi-inventories.yml", Config::YAML);
        $this->cfgInv = $inventories->getAll();
        $this->cfgSharedInv = $sharedInv->getAll();
    }

    public function translateColors($symbol, $message){
        $message = str_replace($symbol."0", TextFormat::BLACK, $message);
        $message = str_replace($symbol."1", TextFormat::DARK_BLUE, $message);
        $message = str_replace($symbol."2", TextFormat::DARK_GREEN, $message);
        $message = str_replace($symbol."3", TextFormat::DARK_AQUA, $message);
        $message = str_replace($symbol."4", TextFormat::DARK_RED, $message);
        $message = str_replace($symbol."5", TextFormat::DARK_PURPLE, $message);
        $message = str_replace($symbol."6", TextFormat::GOLD, $message);
        $message = str_replace($symbol."8", TextFormat::DARK_GRAY, $message);
        $message = str_replace($symbol."9", TextFormat::BLUE, $message);
        $message = str_replace($symbol."a", TextFormat::GREEN, $message);
        $message = str_replace($symbol."b", TextFormat::AQUA, $message);
        $message = str_replace($symbol."c", TextFormat::RED, $message);
        $message = str_replace($symbol."d", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace($symbol."e", TextFormat::YELLOW, $message);
        $message = str_replace($symbol."f", TextFormat::WHITE, $message);
        $message = str_replace($symbol."k", TextFormat::OBFUSCATED, $message);
        $message = str_replace($symbol."l", TextFormat::BOLD, $message);
        $message = str_replace($symbol."m", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace($symbol."n", TextFormat::UNDERLINE, $message);
        $message = str_replace($symbol."o", TextFormat::ITALIC, $message);
        $message = str_replace($symbol."r", TextFormat::RESET, $message);

        return $message;
    }
    
    public function saveYml(){
        $inventories = new Config($this->getDataFolder() . "inventory.yml", Config::YAML);
        $multiInv = new Config($this->getDataFolder() . "multi-inventories.yml", Config::YAML);
        asort($this->cfgInv);
        $inventories->setAll($this->cfgInv);
        $inventories->save();
        foreach ($this->cfgMultiInv as $wk => $wi) {
            if (!isset($wi["Worlds"]) || !isset($wi["LastWorld"])) {
                unset($this->cfgMultiInv[$wk]);
            } else {
                foreach ($wi["Worlds"] as $k => $v) {
                    ksort($v);
                    $this->cfgMultiInv[$k] = $v;
                }
            }
        }
        ksort($this->cfgMultiInv);
        $multiInv->setAll($this->cfgMultiInv);
        $multiInv->save();
        $this->loadYml();
    }

    /**
     * @param $option
     * @return bool|mixed
     */
    public function getOption($option){
        return $this->plugin->getConfig()->get($option);
    }

    /**
     * @param $option
     */
    public function setOption($option){
        $this->plugin->getConfig()->set($option);
        $this->plugin->getConfig()->save();
    }

    /**
     * @param Player $p
     * @param String $worldName
     * @return bool
     */
    public function createInv(Player $p, String $world){
        $name = strtolower($p->getName());
        $change = false;
        if(!isset($this->cfgMultiInv[$name])){
            $this->cfgMultiInv[$name] = [
                "LastWorld" => strtolower($p->getLevel()->getFolderName()),
                "Worlds" => []
            ];
            $change = true;
        }
        $conf = $this->cfgMultiInv[$name];
        if(!isset($conf["Worlds"])){
            $conf["Worlds"] = [];
            $change = true;
        }
        $confWorld = $conf["Worlds"];
        if(!isset($confWorld[$world])){
            $confWorld[$world] = [];
            $change = true;
        }
        if(!isset($conf["LastWorld"])){
            $conf["LastWorld"] = $world;
            $change = true;
        }

        if($change){
            $this->cfgMultiInv[$name] = [
                "LastWorld" => $world,
                "Worlds" => $confWorld
            ];
            $this->saveYml();
        }
        return $change;
    }

}