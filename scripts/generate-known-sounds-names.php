<?php

declare(strict_types=1);

namespace collapse\generate_known_sounds_names;

use pocketmine\utils\Utils;
use function dirname;
use function str_replace;
use function strtoupper;

require_once dirname(__DIR__, 3) . '/PocketMine-MP/vendor/autoload.php';

$opts = getopt('i:');
if(!isset($opts['i'])){
	exit('Usage: generate-known-sounds-names.php -i input_json');
}

function constantify(string $permissionName) : string{
	return strtoupper(str_replace(['.', '-'], '_', $permissionName));
}

const SHARED_HEADER = <<<'HEADER'
<?php

declare(strict_types=1);

namespace collapse\world\sound;

HEADER;

/**
 * @param string[] $soundDefinitions
 */
function generate_known_sounds_names(array $soundDefinitions) : void{
	ob_start();

	echo SHARED_HEADER;
	echo <<<'HEADER'

/**
 * This class is generated automatically, do NOT modify it by hand.
 *
 * @internal
 */
final class MinecraftSoundNames{


HEADER;

	ksort($soundDefinitions, SORT_STRING);
	foreach(Utils::stringifyKeys($soundDefinitions) as $k => $_){
		echo "\tpublic const string ";
		echo constantify($k);
		echo " = '" . $k . "';\n";
	}

	echo "}\n";

	file_put_contents(dirname(__DIR__) . '/src/world/sound/MinecraftSoundNames.php', ob_get_clean());

	echo "Done generating MinecraftSoundNames.\n";
}

$definitions = json_decode(file_get_contents($opts['i']), true);
generate_known_sounds_names($definitions);