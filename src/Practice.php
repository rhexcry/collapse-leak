<?php

declare(strict_types=1);

namespace collapse;

use collapse\block\tile\CollapseCampfire;
use collapse\block\tile\CollapseChiseledBookshelf;
use collapse\censor\Censor;
use collapse\censor\PatternBuilder;
use collapse\command\CommandManager;
use collapse\cooldown\CooldownManager;
use collapse\cosmetics\CosmeticsManager;
use collapse\feature\EventBus;
use collapse\feature\FeatureContext;
use collapse\feature\FeatureManager;
use collapse\feature\trigger\TriggerManager;
use collapse\game\duel\DuelManager;
use collapse\game\ffa\FreeForAllManager;
use collapse\game\GameListener;
use collapse\hologram\HologramManager;
use collapse\i18n\TranslatorManager;
use collapse\item\ItemListener;
use collapse\leaderboard\LeaderboardManager;
use collapse\lobby\LobbyManager;
use collapse\network\rcon\RconLoader;
use collapse\npc\NPCManager;
use collapse\player\chat\ChatListener;
use collapse\player\CollapsePlayer;
use collapse\player\profile\ProfileManager;
use collapse\player\rank\RankManager;
use collapse\player\scoreboard\ScoreboardUpdateTask;
use collapse\player\settings\SettingsListener;
use collapse\player\skins\SkinManager;
use collapse\punishments\PunishmentManager;
use collapse\report\ReportManager;
use collapse\skin\CollapseSkinAdapter;
use collapse\skin\SkinFactory;
use collapse\social\SocialManager;
use collapse\system\anticheat\AnticheatManager;
use collapse\system\broadcast\BroadcastTask;
use collapse\system\clan\ClanManager;
use collapse\system\friend\FriendManager;
use collapse\system\internal\network\ServerLagTask;
use collapse\system\internal\network\ServerTickTask;
use collapse\system\kiteditor\KitEditorManager;
use collapse\system\moderatorpoints\ModeratorPointsManager;
use collapse\system\observe\ObserveManager;
use collapse\system\internal\InternalManager;
use collapse\system\party\PartyManager;
use collapse\system\restart\RestartManager;
use collapse\system\shop\ShopManager;
use collapse\system\telegram\TelegramManager;
use collapse\wallet\WalletManager;
use collapse\world\area\AreaManager;
use collapse\world\block\lagfix\BlockLagFixListener;
use collapse\world\format\CollapseLevelDB;
use collapse\world\generator\VoidGenerator;
use collapse\world\MissingVanillaBlocks;
use Dotenv\Dotenv;
use pocketmine\block\tile\TileFactory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\WritableWorldProviderManagerEntry;
use pocketmine\world\generator\GeneratorManager;
use function define;
use function dirname;
use function is_file;
use function stripos;
use function strlen;
use function strtolower;
use const COLLAPSE_AUTOLOADER_PATH;
use const COLLAPSE_SERVER_PATH;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use const PHP_INT_MAX;

define('COLLAPSE_SERVER_PATH', dirname(__FILE__, 5) . DIRECTORY_SEPARATOR);
define('COLLAPSE_AUTOLOADER_PATH', dirname(__FILE__, 2) . '/vendor/autoload.php');
if(!is_file(COLLAPSE_AUTOLOADER_PATH)){
	echo 'Missing Collapse Composer!' . PHP_EOL;
	exit(1);
}
require_once(COLLAPSE_AUTOLOADER_PATH);
$dotenv = Dotenv::createImmutable(COLLAPSE_SERVER_PATH);
$dotenv->load();

define('EOL', "\n");

final class Practice extends PluginBase{
	use SingletonTrait;

	private static ?bool $isTestServer = null;

	private readonly TranslatorManager $translatorManager;

	private readonly SkinFactory $skinFactory;

	private readonly NPCManager $npcManager;

	private readonly ProfileManager $profileManager;

	private readonly SkinManager $skinManager;

	private readonly CommandManager $commandManager;

	private readonly RankManager $rankManager;

	private readonly PunishmentManager $punishmentManager;

	private readonly AreaManager $areaManager;

	private readonly HologramManager $hologramManager;

	private readonly LobbyManager $lobbyManager;

	private readonly DuelManager $duelManager;

	private readonly FreeForAllManager $freeForAllManager;

	private readonly CooldownManager $cooldownManager;

	private readonly Censor $censorFilter;

	private readonly SocialManager $socialManager;

	private readonly WalletManager $walletManager;

	private readonly CosmeticsManager $cosmeticsManager;

	private readonly LeaderboardManager $leaderboardManager;

	private readonly ObserveManager $observeManager;

	private readonly ReportManager $reportManager;

	private readonly TriggerManager $triggerManager;
	private readonly FeatureManager $featureManager;

	private readonly ClanManager $clanManager;

	private readonly FriendManager $friendManager;

	private readonly ShopManager $shopManager;

	private readonly KitEditorManager $kitEditorManager;

	private readonly PartyManager $partyManager;

	private readonly RestartManager $restartManager;

	private readonly ModeratorPointsManager $moderatorPointsManager;

	private readonly TelegramManager $telegramManager;

	private readonly InternalManager $internalManager;

	private readonly AnticheatManager $anticheatManager;

	public function onLoad() : void{
		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, 'void', fn() => null);
		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, 'flat', fn() => null, true);
		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, 'normal', fn() => null, true);

		TileFactory::getInstance()->register(CollapseCampfire::class, ['Campfire', 'minecraft:campfire']);
		TileFactory::getInstance()->register(CollapseChiseledBookshelf::class, ["ChiseledBookshelf", "minecraft:chiseled_bookshelf"]);

		MissingVanillaBlocks::registerBlocks();
		$this->getServer()->getAsyncPool()->addWorkerStartHook(function(int $worker) : void{
			$this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask{

				public function onRun() : void{
					MissingVanillaBlocks::registerBlocks();
				}
			}, $worker);
		});

		$providerManager = $this->getServer()->getWorldManager()->getProviderManager();
		$providerManager->setDefault($provider = new WritableWorldProviderManagerEntry(CollapseLevelDB::isValid(...), fn(string $path, \Logger $logger) => new CollapseLevelDB($path, $logger), CollapseLevelDB::generate(...)));
		$providerManager->addProvider($provider, 'leveldb', true);

		new RconLoader($this);
	}

	public function onEnable() : void{
		self::setInstance($this);

		if(!Practice::isTestServer()){
			CreativeInventory::getInstance()->clear();
		}

		$skinAdapter = new CollapseSkinAdapter($this);
		foreach(TypeConverter::getAll() as $typeConverter){
			$typeConverter->setSkinAdapter($skinAdapter);
		}

		$this->getServer()->getWorldManager()->setAutoSave(false);

		$this->translatorManager = new TranslatorManager($this);
		$this->skinFactory = new SkinFactory();
		$this->npcManager = new NPCManager($this);
		$this->profileManager = new ProfileManager($this);
		$this->skinManager = new SkinManager();
		$this->commandManager = new CommandManager($this);
		$this->rankManager = new RankManager($this);
		$this->punishmentManager = new PunishmentManager($this);
		$this->areaManager = new AreaManager($this);
		$this->hologramManager = new HologramManager();
		$this->lobbyManager = new LobbyManager($this);
		$this->duelManager = new DuelManager($this);
		$this->freeForAllManager = new FreeForAllManager($this);
		$this->cooldownManager = new CooldownManager();
		$this->censorFilter = new Censor(new PatternBuilder(), '*');
		$this->socialManager = new SocialManager($this);
		$this->walletManager = new WalletManager($this);
		$this->cosmeticsManager = new CosmeticsManager($this);
		$this->leaderboardManager = new LeaderboardManager($this);
		$this->reportManager = new ReportManager();
		$this->observeManager = new ObserveManager($this);
		$this->clanManager = new ClanManager($this);
		$this->friendManager = new FriendManager($this);
		$this->shopManager = new ShopManager($this);
		$this->kitEditorManager = new KitEditorManager($this);
		$this->partyManager = new PartyManager();
		$this->restartManager = new RestartManager($this);
		$this->telegramManager = new TelegramManager($this);
		$this->moderatorPointsManager = new ModeratorPointsManager($this);
		$this->internalManager = new InternalManager($this);
		//$this->anticheatManager = new AnticheatManager($this);

		$this->triggerManager = new TriggerManager($this);
		$this->featureManager = new FeatureManager(new FeatureContext($this, (new EventBus())->hook(), $this->triggerManager));

		//$this->featureManager->register(QuestFeature::class);

		$this->getServer()->getPluginManager()->registerEvents(new PracticeListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ChatListener($this->censorFilter), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ItemListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new SettingsListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new GameListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new BlockLagFixListener(), $this);
		$this->getScheduler()->scheduleRepeatingTask(new ScoreboardUpdateTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), BroadcastTask::DELAY);
		$this->getScheduler()->scheduleRepeatingTask(ServerTickTask::getInstance(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new ServerLagTask($this->internalManager), 18);
	}

	public function onDisable() : void{
		// HACK: the client bugs if the player's form is opened,
		// and it kicks (can't click anything in the client, just relog in to the game)
		foreach(self::onlinePlayers() as $player){
			$player->closeAllForms();
			$player->transfer(self::getServerAddress());
		}

		$this->duelManager->close();
		$this->npcManager->close();
		$this->profileManager->close();
		$this->featureManager->close();
	}

	public function getTranslatorManager() : TranslatorManager{
		return $this->translatorManager;
	}

	public function getSkinFactory() : SkinFactory{
		return $this->skinFactory;
	}

	public function getNPCManager() : NPCManager{
		return $this->npcManager;
	}

	public function getProfileManager() : ProfileManager{
		return $this->profileManager;
	}

	public function getSkinManager() : SkinManager{
		return $this->skinManager;
	}

	public function getCommandManager() : CommandManager{
		return $this->commandManager;
	}

	public function getRankManager() : RankManager{
		return $this->rankManager;
	}

	public function getPunishmentManager() : PunishmentManager{
		return $this->punishmentManager;
	}

	public function getAreaManager() : AreaManager{
		return $this->areaManager;
	}

	public function getHologramManager() : HologramManager{
		return $this->hologramManager;
	}

	public function getLobbyManager() : LobbyManager{
		return $this->lobbyManager;
	}

	public function getDuelManager() : DuelManager{
		return $this->duelManager;
	}

	public function getFreeForAllManager() : FreeForAllManager{
		return $this->freeForAllManager;
	}

	public function getCooldownManager() : CooldownManager{
		return $this->cooldownManager;
	}

	public function getCensorFilter() : Censor{
		return $this->censorFilter;
	}

	public function getSocialManager() : SocialManager{
		return $this->socialManager;
	}

	public function getWalletManager() : WalletManager{
		return $this->walletManager;
	}

	public function getCosmeticsManager() : CosmeticsManager{
		return $this->cosmeticsManager;
	}

	public function getLeaderboardManager() : LeaderboardManager{
		return $this->leaderboardManager;
	}

	public function getReportManager() : ReportManager{
		return $this->reportManager;
	}

	public function getTriggerManager() : TriggerManager{
		return $this->triggerManager;
	}

	public function getFeatureManager() : FeatureManager{
		return $this->featureManager;
	}

	public function getObserveManager() : ObserveManager{
		return $this->observeManager;
	}

	public function getClanManager() : ClanManager{
		return $this->clanManager;
	}

	public function getFriendManager() : FriendManager{
		return $this->friendManager;
	}

	public function getShopManager() : ShopManager{
		return $this->shopManager;
	}

	public function getKitEditorManager() : KitEditorManager{
		return $this->kitEditorManager;
	}

	public function getPartyManager() : PartyManager{
		return $this->partyManager;
	}

	public function getRestartManager() : RestartManager{
		return $this->restartManager;
	}

	public function getTelegramManager() : TelegramManager{
		return $this->telegramManager;
	}
	
	public function getModeratorPointsManager() : ModeratorPointsManager{
		return $this->moderatorPointsManager;
	}

	public function getInternalManager() : InternalManager{
		return $this->internalManager;
	}

	public static function isTestServer() : bool{
		return ($_ENV['ENVIRONMENT'] ?? 'test') !== 'production';
	}

	public static function getDatabaseName() : string{
		return self::isTestServer() ? 'collapse_practice_test' : 'collapse_practice';
	}

	public static function getServerAddress() : string{
		return self::isTestServer() ? '127.0.0.1' : 'clps.gg';
	}

	/**
	 * @return CollapsePlayer[]
	 */
	public static function onlinePlayers() : array{
		return Server::getInstance()->getOnlinePlayers();
	}

	public static function getPlayerByPrefix(string $name) : ?CollapsePlayer{
		$found = null;
		$name = strtolower($name);
		$delta = PHP_INT_MAX;
		foreach(self::onlinePlayers() as $player){
			if(stripos($player->getName(), $name) === 0){
				$curDelta = strlen($player->getName()) - strlen($name);
				if($curDelta < $delta){
					$found = $player;
					$delta = $curDelta;
				}
				if($curDelta === 0){
					break;
				}
			}
		}

		return $found;
	}

	public static function getPlayerExact(string $name) : ?CollapsePlayer{
		$player = Server::getInstance()->getPlayerExact($name);
		if(!$player instanceof CollapsePlayer){
			return null;
		}
		return $player;
	}

	public static function getPlayerByXuid(string $xuid) : ?CollapsePlayer{
		$player = Server::getInstance()->getPlayerByXuid($xuid);
		if(!$player instanceof CollapsePlayer){
			return null;
		}
		return $player;
	}
}
