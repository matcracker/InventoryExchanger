<?php
/**
 * @author matcracker
 * Plugin for PocketMine and ImagicalMine
 * Version 2.4
 * API: 2.0.0
 */
namespace InventoryExchanger\matcracker;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Configs extends PluginBase{
    //Configurations
    protected $cfgInv;
    
    public function __construct(){
        
    }
    
    public function loadYml() : void{
        $inventories = new Config($this->getDataFolder() . "inventory.yml", Config::YAML);
        $this->cfgInv = $inventories->getAll();
    }

    public function translateColors($symbol, $message) : string{
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

    public function saveYml() : void{
        $inventories = new Config($this->getDataFolder() . "inventory.yml", Config::YAML);
        asort($this->cfgInv);
        $inventories->setAll($this->cfgInv);
        $inventories->save();
        $this->loadYml();
    }

    public function getOption($option) : mixed{
        return $this->getConfig()->get($option);
    }

    public function setOption($option) : void{
        $this->getConfig()->set($option);
        $this->getConfig()->save();
    }
}