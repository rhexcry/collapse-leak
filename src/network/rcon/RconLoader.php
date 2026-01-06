<?php

declare(strict_types=1);

namespace collapse\network\rcon;

use collapse\Practice;
use pocketmine\utils\Filesystem;
use Symfony\Component\Filesystem\Path;
use function base64_encode;
use function file_exists;
use function file_put_contents;
use function inet_pton;
use function is_array;
use function is_float;
use function is_int;
use function is_string;
use function json_decode;
use function json_encode;
use function random_bytes;

final readonly class RconLoader{
	private const string RCON_CONFIG = 'rcon.json';

	public function __construct(Practice $plugin){
		$config = $this->loadConfig($plugin);
		$plugin->getLogger()->info('Starting RCON on ' . $config->ip . ':' . $config->port);
		$plugin->getServer()->getNetwork()->registerInterface(new Rcon(
			$config,
			function(string $commandLine) use ($plugin) : string{
				$response = new RconCommandSender($plugin->getServer(), $plugin->getServer()->getLanguage());
				$response->recalculatePermissions();
				$plugin->getServer()->dispatchCommand($response, $commandLine);
				return $response->getMessage();
			},
			$plugin->getServer()->getLogger(),
			$plugin->getServer()->getTickSleeper()
		));
	}

	private function loadConfig(Practice $plugin) : RconConfig{
		$fileLocation = Path::join($plugin->getDataFolder(), self::RCON_CONFIG);
		if(!file_exists($fileLocation)){
			$config = [
				'ip' => $plugin->getServer()->getIp(),
				'port' => $plugin->getServer()->getPort(),
				'max-connections' => 50,
				'password' => base64_encode(random_bytes(8))
			];
			file_put_contents($fileLocation, json_encode($config));
			$plugin->getLogger()->notice('RCON config file generated at ' . $fileLocation . '. Please customize it.');
		}else{
			$config = json_decode(Filesystem::fileGetContents($fileLocation), true);
		}

		if(!is_array($config)){
			throw new \RuntimeException('Failed to parse config file');
		}

		$ip = null;
		$port = null;
		$maxConnections = null;
		$password = null;
		foreach($config as $key => $value){
			match($key){
				'ip' => is_string($value) && inet_pton($value) !== false ? $ip = $value : throw new \RuntimeException('Invalid IP address'),
				'port' => is_int($value) && $value > 0 && $value < 65535 ? $port = $value : throw new \RuntimeException('Invalid port, must be a port in range 0-65535'),
				'max-connections' => is_int($value) && $value > 0 ? $maxConnections = $value : throw new \RuntimeException('Invalid max connections, must be a number greater than 0'),
				'password' => is_string($value) || is_int($value) || is_float($value) ? $password = (string) $value : throw new \RuntimeException('Invalid password, must be a string'),
				default => throw new \RuntimeException('"Unexpected config key "' . $key . '"')
			};
		}
		if($ip === null){
			throw new \RuntimeException("Missing IP address");
		}
		if($port === null){
			throw new \RuntimeException("Missing port");
		}
		if($maxConnections === null){
			throw new \RuntimeException("Missing max connections");
		}
		if($password === null){
			throw new \RuntimeException("Missing password");
		}

		return new RconConfig($ip, $port, $maxConnections, $password);
	}
}
