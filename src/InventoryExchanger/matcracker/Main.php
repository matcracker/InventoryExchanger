<?php
namespace InventoryExchanger\matcracker;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\item\Item;

class Main extends PluginBase{

	public function onEnable(){
		$this->getLogger()->info(TextFormat::GREEN."InventoryExchanger is activated.");
		$this->loadYml();
	}
	
	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED."InventoryExchanger is disabled.");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$prefix = "§b[InventoryExchanger]";
		$name = $sender->getName();

		if(!$sender instanceof Player){
			$sender->sendMessage($prefix . "Please run this command in-game");
			return true;
		}else{
			if(strtolower($cmd->getName()) == "inventoryexchanger"){
				if($sender->hasPermission("inventoryexchanger.command.inve")){
					$inv = $sender->getInventory();
					if (!isset($this->ie[$name])) $this->ie[$name] = [];
					$getInv = [];

					foreach ($inv->getContents() as $gI) {
						if ($gI->getID() !== 0 and $gI->getCount() > 0) $getInv[] = [$gI->getID(), $gI->getDamage(), $gI->getCount()];
					}

					$setInv = [];
					foreach ($this->ie[$name] as $iE)
						$setInv[] = Item::get($iE[0], $iE[1], $iE[2]);

					$this->ie[$name] = $getInv;
					$inv->setContents($setInv);
					$this->saveYml();

					$sender->sendMessage($prefix . "§2You correctly change inventory!");

				}else{
					$sender->sendMessage("§4You don't have permission to use this command!");
				}
			}
		}
		return true;
	}

	public function loadYml(){
		@mkdir($this->getServer()->getDataPath() . "/plugins/InventoryExchanger/");
		$inventories = new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger" . "Inventories.yml", Config::YAML);
		$this->ie = $inventories->getAll();
	}

	public function saveYml(){
		$invetories = new Config($this->getServer()->getDataPath() . "/plugins/InventoryExchanger" . "Inventories.yml", Config::YAML);
		asort($this->ie);
		$invetories->setAll($this->ie);
		$invetories->save();
		$this->loadYml();
	}

}
