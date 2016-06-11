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
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

class Main extends PluginBase{
    protected $conf;

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->conf = new Configs();
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->conf->loadYml();
		$this->getLogger()->info(TextFormat::GREEN . "InventoryExchanger is activated.");
	}

	public function onDisable() : void{
		$this->saveResource("config.yml");
		$this->saveResource("inventory.yml");
		$this->getLogger()->info(TextFormat::RED . "InventoryExchanger is disabled.");
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) : boolean{
        $this->conf = new Configs();
		if($this->getConfig()->get("enable-prefix") === true)
			$prefix = "&b[InventoryExchanger] ";
		else
			$prefix = "";

		$name = $sender->getName();

		try{
			if(strtolower($cmd->getName()) == "inventoryexchanger"){
				$args[0] = strtolower($args[0]);
				if($args[0] === "help"){
					if($sender->hasPermission("inventoryexchanger.command.help")){
						$sender->sendMessage($this->conf->translateColors("&", "&2=====>&b[InventoryExchanger]&2<====="));
						$sender->sendMessage($this->conf->translateColors("&", "&3/inve change&2: Change inventory in this world"));
						$sender->sendMessage($this->conf->translateColors("&", "&3/inve help&2: Show help page."));
						$sender->sendMessage($this->conf->translateColors("&", "&3/inve info&2: Show info about plugin."));
						$sender->sendMessage($this->conf->translateColors("&", "&3/inve reload&2: Reload plugin's configuration."));
					}else{
						$sender->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-no-permission")));
					}
					return true;

				}else if($args[0] === "reload"){
					if($sender->hasPermission("inventoryexchanger.command.reload")){
						try{
							$this->saveDefaultConfig();
							$this->getConfig()->reload();
							$sender->sendMessage($this->conf->translateColors("&", $prefix . "&aConfiguration reloaded!"));
						}catch(\Exception $e){
							$sender->sendMessage($prefix . TextFormat::DARK_RED . "An error occured during the reloading! Check console.");
							$this->getLogger()->critical($e);
						}

					}else{
						$sender->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-no-permission")));
					}
					return true;

				}else if($args[0] === "info"){
					if($sender->hasPermission("inventoryexchanger.command.info")){
						$sender->sendMessage($this->conf->translateColors("&", "&b" . $this->getDescription()->getName() . " v&2" . $this->getDescription()->getVersion()));
						$sender->sendMessage($this->conf->translateColors("&", "&cDeveloped by &2matcracker!"));

					}else{
						$sender->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-no-permission")));
					}
					return true;
				}else if($args[0] === "change"){
					if(!$sender instanceof Player){
						$sender->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-console")));
						return true;
					}else{
						if($sender->hasPermission("inventoryexchanger.command.inve")) {
							$inv = $sender->getInventory();
							$getInv = [];
							$setInv = [];

							if(!isset($this->conf->cfgInv[$name]))
								$this->conf->cfgInv[$name] = [];

							foreach($inv->getContents() as $contents)
								if($contents->getId() !== 0 and $contents->getCount() > 0)
									$getInv[] = [$contents->getId(), $contents->getDamage(), $contents->getCount()];

							foreach($this->conf->cfgInv[$name] as $iE)
								$setInv[] = Item::get($iE[0], $iE[1], $iE[2]);

							$this->conf->cfgInv[$name] = $getInv;
							$inv->setContents($setInv);
							$this->conf->saveYml();
							$sender->sendMessage($this->conf->translateColors("&", $prefix . $this->conf->getOption("message-command")));

						}else{
							$sender->sendMessage($this->conf->translateColors("&", $this->conf->getOption("message-no-permission")));
						}
					}
					return true;
				}
			}else{
				return false;
			}
		}catch(\Exception $e){
			$sender->sendMessage($this->conf->translateColors("&", "&cUse &b/inve help &cfor all the commands!"));
		}
		return false;
	}



}
