<?php

namespace ARCore\AntiLagg

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

class AntiLagg extends Command implements PluginIdentifiableCommand {
  
  public $plugin;
  
  public function __construct(Loader $plugin) {
    parent::__construct("antilagg", "Clear server Lag!", "/antilagg <check/clear/killmobs/clearall>", ["al"]);
    $this->setPermission("clearlagg.command.clearlagg");
    $this->plugin = $plugin;
  }
  public function getPlugin() {
    return $this->plugin;
  }
  public function execute(CommandSender $sender, $alias, array $args) {
    if(!$this->testPermission($sender)) {
      return false;
    }
    
    $chunksCollected = 0;
		$entitiesCollected = 0;
		$tilesCollected = 0;
		$memory = memory_get_usage();
		
		foreach($sender->getServer()->getLevel() as $level){
		  
		  $area = $sender->getLocation();
		  $distance = $area->distance($sender);
		  
		  $areaargs = count($distance);
		  
			$diff = [count($level->getChunks()), count($level->getEntities()), count($level->getTiles())];
			$level->unloadChunks(true);
			
			$chunks = count($sender->getLevel()->getChunks());
			$chunks2 = count($level->getChunks());
			$tiles = count($level->getTiles());
			$ec = count($level->getEntities());
			
			$chunksCollected += $diff[0] - count($level->getChunks());
			$entitiesCollected += $diff[1] - count($level->getEntities());
			$tilesCollected += $diff[2] - count($level->getTiles());
			$level->clearCache(true);
		}
		
		/*$cyclesCollected = $sender->getServer()->getMemoryManager()->triggerGarbageCollector();
		$sender->sendMessage(TextFormat::GREEN . "---- " . TextFormat::WHITE . "%pocketmine.command.gc.title" . TextFormat::GREEN . " ----");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.gc.chunks" . TextFormat::RED . \number_format($chunksCollected));
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.gc.entities" . TextFormat::RED . \number_format($entitiesCollected));
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.gc.tiles" . TextFormat::RED . \number_format($tilesCollected));
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.gc.cycles" . TextFormat::RED . \number_format($cyclesCollected));
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.gc.memory" . TextFormat::RED . \number_format(\round((($memory - \memory_get_usage()) / 1024) / 1024, 2))." MB");
		return true;*/
    
    if(isset($args[0])) {
      switch($args[0]) {
        case "clear":
          $sender->sendMessage("Removed " . $this->getPlugin()->removeEntities() . " entities.");
          return true;
        case "check":
        case "count":
          $c = $this->getPlugin()->getEntityCount();
          $sender->sendMessage("There are " . $c[0] . " players, " . $c[1] . " mobs, and " . $c[2] . " entities.");
          return true;
        case "reload":
          // TODO
          return true;
        case "killmobs":
          $sender->sendMessage("Removed " . $this->getPlugin()->removeMobs() . " mobs.");
          return true;
        case "clearall":
          $sender->sendMessage("Removed " . ($d = $this->getPlugin()->removeMobs()) . " mob" . ($d == 1 ? "" : "s") . " and " . ($d = $this->getPlugin()->removeEntities()) . " entit" . ($d == 1 ? "y" : "ies") . ".");
          return true;
        case "area":
          //NOT TESTED YET
          $sender->sendMessage(TextFormat::GREEN . "Your area is: " . TextFormat::YELLOW . \number_format($areaargs));
          return true;
        case "unloadchunks":
          $level->unloadChunks(true);
          $sender->sendMessage(TextFormat::GOLD . "Chunks Collected: " . TextFormat::RED . \number_format($chunksCollected));
          return true;
        case "chunks":
          $sender->sendMessage(TextFormat::GOLD . "Total Loaded Chunks: " . TextFormat::YELLOW . \number_format($chunks));
          return true;
        case "tpchunk":
          //NOT TESTED YET
          $x = $chunks->x;
          $y = $chunks->y;
          $z = $chunks->z;
          $roundx = round($x);
          $roundy = round($y);
          $roundz = round($z);
          $sender->sendMessage(TextFormat::AQUA . "Teleporting to a Chunk with the coords: ". $roundx . "," . $roundy . "," . $roundz));
          $sender->getLevel()->teleport(new Vector3($x, $y, $z);
          return true;
        default:
          return false;
      }
    }
    return false;
  }
}
