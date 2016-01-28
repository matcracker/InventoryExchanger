<?php
namespace InventoryExchanger\matcracker;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\item\Item;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getLogger()->info(TextFormat::GREEN."InventoryExchanger is activated.");
		$this->loadYml();
	}
	
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED."InventoryExchanger is disabled.");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$prefix = "[InventoryExchanger]";
		$name = $sender->getName();

		if(!$sender instanceof Player){
			$sender->sendMessage($prefix . "Please run this command in-game");
			return true;
		}

		$inv = $sender->getInventory();
		if(!isset($ie[$name])) $ie[$name] = [];
		$getInv = [];

		foreach($inv->getContents() as $gI){
			if($gI->getID() !== 0 and $gI->getCount() > 0) $getInv[] = [$gI->getID(),$gI->getDamage(),$gI->getCount() ];
		}

		$setInv = [];
		foreach($ie[$name] as $iE)
			$setInv[] = Item::get($iE[0], $iE[1], $iE[2]);

		$ie[$name] = $getInv;
		$inv->setContents($setInv);
		$this->saveYml();

		return true;
	}

	public function loadYml(){
		@mkdir($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/");
		$config = new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/" . "config.yml", Config::YAML);
		$ie = $config->getAll();
	}

	public function saveYml(){
		$config = new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/" . "config.yml", Config::YAML);
		asort($ie);
		$config->setAll($ie);
		$config->save();
		$this->loadYml();
	}

}
