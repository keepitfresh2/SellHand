<?php

declare(strict_types=1);

namespace Keepitfresh\SellHand2;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use onebone\economyapi\EconomyAPI;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\Utils\TextFormat as TF;
class Main extends PluginBase{
	//checks if file exists, if it doesnt make up a value that the user can repeat
	public $cfg;
	public function onEnable() : void{
		$file = ($this->getDataFolder()."prices.json");
		
		if(!file_exists($file))
		{
			$this->cfg = new Config($this->getDataFolder() ."prices.json", Config::JSON, array());
			$this->cfg->set("prices",[
				1 => 64,
				2 => 12
			]);
			$this->cfg->save();
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		$inv = $sender->getInventory();
        $item = $inv->getItemInHand();
        //foreach ($this->cfg->getAll()["prices"] as $price) {
        $this->cfg = new Config($this->getDataFolder() ."prices.json", Config::JSON, array());
		switch($command->getName()){
			case "sellhand":
				if (!isset($args[0]))
            	{
                	$sender->sendMessage(TF::RED.TF::BOLD."(!) Please do an amount you would like to sell");
                    return false;
            	}
            	if(!$sender instanceof Player)
            	{
            		$sender->sendMessage(TF::RED.TF::BOLD."(!) You must be a player to run this command!");
                    return false;
            	}
            	if(!is_numeric($args[0]))
            	{
            		$sender->sendMessage(TF::RED.TF::BOLD."(!) The amount of blocks must be numeric!");
                    return false;
            	}
            	if($args[0] <= 0){
            		$sender->sendMessage(TF::RED.TF::BOLD."(!) The amount must be above zero!");
                    return false;
            	}
            	if(!isset($this->cfg->getAll()["prices"][$item->getId()]))
            	{
            		$sender->sendMessage(TF::RED.TF::BOLD."(!) This item isn't for sale!");
                    return false;
            	}
            	else{
            		$money = EconomyAPI::getInstance()->myMoney($sender);
            		$worth = $this->cfg->get("prices")[$item->getId()] * $args[0];
            		EconomyAPI::getInstance()->setMoney($sender, $money + $worth);
            		//$item->count-$args[0];
            		$item->setCount($item->getCount() - $args[0]);
                    $inv->setItemInHand($item);
            		$sender->sendMessage(TF::GREEN.TF::BOLD."(!) Item sold for ". $worth);
                    return true;
            	}
			return true;
		}
		//}
	}

	//Checks if configs exist
	public function cfgExists($file)
	{
		if(file_exists($file))
		{
			return true;
		}else{
			return false;
		}
	}
}
