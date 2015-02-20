<?php
// This plugin is made by matcracker
namespace InventoryExchanger\InventoryExchanger;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\item\Item;

class InventoryExchanger extends PluginBase{

	public function onEnable(){
		$this->loadYml();
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $sub){
		$n = $sender->getName();
		$m = "[InventoryExchanger]";
		if($n == "CONSOLE"){
			$sender->sendMessage($this->LangItalian() ? $m . "Usa questo comando in gioco.": $m . "Please run this command in-game");
			return true;
		}
		$getInv = [];
		$inv = $sender->getInventory();
		if(!isset($this->ie[$n])) $this->ie[$n] = [];
		$getInv = [];
		foreach($inv->getContents() as $gI){
			if($gI->getID() !== 0 and $gI->getCount() > 0) $getInv[] = [$gI->getID(),$gI->getDamage(),$gI->getCount() ];
		}
		$setInv = [];
		foreach($this->ie[$n] as $iE)
			$setInv[] = Item::get($iE[0], $iE[1], $iE[2]);
		$this->ie[$n] = $getInv;
		$inv->setContents($setInv);
		$this->saveYml();
		$sender->sendMessage($this->LangItalian() ? $m . "Hai cambiato inventario": $m . "You change inventory");
		return true;
	}

	public function loadYml(){
		@mkdir($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/");
		$this->InventoryExchanger = new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/" . "InventoryExchanger.yml", Config::YAML);
		$this->ie = $this->InventoryExchanger->getAll();
	}

	public function saveYml(){
		asort($this->ie);
		$this->InventoryExchanger->setAll($this->ie);
		$this->InventoryExchanger->save();
		$this->loadYml();
	}
	public function LangItalian(){
		return (new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/" . "Italian.yml", Config::YAML, ["Italian" => false ]))->get("Italian");
	}
	
}