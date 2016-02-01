<?php
/**
 * @author matcracker
 * Plugin for PocketMine and ImagicalMine
 * Version 2.1
 * API: 1.13.0
 */

namespace InventoryExchanger\matcracker;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\item\Item;

class Main extends PluginBase{

	protected $cfgInv;

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->loadYml();
		$this->getLogger()->info(TextFormat::GREEN . "InventoryExchanger is activated.");
	}
	
	public function onDisable(){
		$this->saveResource("config.yml");
		$this->saveResource("inventory.yml");
		$this->getLogger()->info(TextFormat::RED . "InventoryExchanger is disabled.");
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

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if($this->getConfig()->get("enable.prefix", true))
			$prefix = "&b[InventoryExchanger]";
		else
			$prefix = "";

		$name = $sender->getName();

		try{
			if(strtolower($cmd->getName()) == "inventoryexchanger"){
				$args[0] = strtolower($args[0]);
				if($args[0] === "help"){
					if($sender->hasPermission("inventoryexchanger.command.help")){
						$sender->sendMessage($this->translateColors("&", "&2=====>&b[InventoryExchanger]&2<====="));
						$sender->sendMessage($this->translateColors("&", "&3/inve change&2: Change inventory in this world"));
						$sender->sendMessage($this->translateColors("&", "&3/inve help&2: Show help page."));
						$sender->sendMessage($this->translateColors("&", "&3/inve info&2: Show info about plugin."));
						$sender->sendMessage($this->translateColors("&", "&3/inve reload&2: Reload plugin's configuration."));
					}else{
						$sender->sendMessage($this->translateColors("&", $this->getMessage("message-no-permission")));
					}
					return true;

				}else if($args[0] === "reload"){
					if($sender->hasPermission("inventoryexchanger.command.reload")){
						try{
							$this->saveDefaultConfig();
							$this->getConfig()->reload();
							$sender->sendMessage($this->translateColors("&", "&aConfiguration reloaded!"));
						}catch(\Exception $e){
							$sender->sendMessage(TextFormat::DARK_RED . "An error occured during the reloading! Check console.");
							$this->getLogger()->critical($e);
						}

					}else{
						$sender->sendMessage($this->translateColors("&", $this->getMessage("message-no-permission")));
					}
					return true;

				}else if($args[0] === "info"){
					if($sender->hasPermission("inventoryexchanger.command.info")){
						$sender->sendMessage($this->translateColors("&", "&b" . $this->getDescription()->getName() . " v&2" . $this->getDescription()->getVersion()));
						$sender->sendMessage($this->translateColors("&", "&cDeveloped by &2matcracker!"));

					}else{
						$sender->sendMessage($this->translateColors("&", $this->getMessage("message-no-permission")));
					}
					return true;

				}else if($args[0] === "change"){
					if(!$sender instanceof Player){
						$sender->sendMessage($prefix . $this->getMessage("message-console"));
						return true;
					}else{
						if ($sender->hasPermission("inventoryexchanger.command.inve")) {
							$inv = $sender->getInventory();
							if (!isset($this->cfgInv[$name])) $this->cfgInv[$name] = [];
							$getInv = [];

							foreach ($inv->getContents() as $gI) {
								if ($gI->getID() !== 0 and $gI->getCount() > 0) $getInv[] = [$gI->getID(), $gI->getDamage(), $gI->getCount()];
							}

							$setInv = [];
							foreach ($this->cfgInv[$name] as $iE)
								$setInv[] = Item::get($iE[0], $iE[1], $iE[2]);

							$this->cfgInv[$name] = $getInv;
							$inv->setContents($setInv);
							$this->saveYml();

							$sender->sendMessage($this->translateColors("&", $prefix . $this->getMessage("message-command")));

						}else{
							$sender->sendMessage($this->translateColors("&", $this->getMessage("message-no-permission")));
						}
					}
					return true;
				}
			}else{
				return false;
			}
		}catch(\Exception $e){
			$sender->sendMessage($this->translateColors("&", "&cUse &b/inve help &cfor all the commands!"));
		}
		return false;
	}

	//Configurations
	public function loadYml(){
		$inventories = new Config($this->getDataFolder() . "inventory.yml", Config::YAML);
		$this->cfgInv = $inventories->getAll();
	}

	public function saveYml(){
		$inventories = new Config($this->getDataFolder() . "inventory.yml", Config::YAML);
		asort($this->cfgInv);
		$inventories->setAll($this->cfgInv);
		$inventories->save();
		$this->loadYml();
	}

	public function getMessage($message){
		return $this->getConfig()->get($message);
	}

	public function setMessage($message){
		$this->getConfig()->set($message);
		$this->getConfig()->save();
	}

}
