<?php

declare(strict_types=1);

namespace collapse\generate_known_translation_apis;

use pocketmine\lang\Translatable;
use pocketmine\utils\Utils;
use Symfony\Component\Filesystem\Path;
use function array_map;
use function count;
use function dirname;
use function file_put_contents;
use function fwrite;
use function implode;
use function is_numeric;
use function ksort;
use function ob_get_clean;
use function ob_start;
use function parse_ini_file;
use function preg_match_all;
use function str_replace;
use function strtoupper;
use const INI_SCANNER_RAW;
use const SORT_NUMERIC;
use const SORT_STRING;
use const STDERR;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

function constantify(string $permissionName) : string{
	return strtoupper(str_replace(['.', '-'], '_', $permissionName));
}

function functionify(string $permissionName) : string{
	return str_replace(['.', '-'], '_', $permissionName);
}

const SHARED_HEADER = <<<'HEADER'
<?php

declare(strict_types=1);

namespace collapse\i18n;

HEADER;

/**
 * @param string[] $languageDefinitions
 * @phpstan-param array<string, string> $languageDefinitions
 */
function generate_known_translation_keys(array $languageDefinitions) : void{
	ob_start();

	echo SHARED_HEADER;
	echo <<<'HEADER'

/**
 * This class is generated automatically, do NOT modify it by hand.
 *
 * @internal
 */
final class CollapseTranslationKeys{


HEADER;

	ksort($languageDefinitions, SORT_STRING);
	foreach(Utils::stringifyKeys($languageDefinitions) as $k => $_){
		echo "\tpublic const string ";
		echo constantify($k);
		echo " = '" . $k . "';\n";
	}

	echo "}\n";

	file_put_contents(dirname(__DIR__) . '/src/i18n/CollapseTranslationKeys.php', ob_get_clean());

	echo "Done generating CollapseTranslationKeys.\n";
}

/**
 * @param string[] $languageDefinitions
 * @phpstan-param array<string, string> $languageDefinitions
 */
function generate_known_translation_factory(array $languageDefinitions) : void{
	ob_start();

	echo SHARED_HEADER;
	echo <<<'HEADER'

use pocketmine\lang\Translatable;

/**
 * This class is generated automatically, do NOT modify it by hand.
 *
 * @internal
 */
final class CollapseTranslationFactory{


HEADER;
	ksort($languageDefinitions, SORT_STRING);

	$parameterRegex = '/{%(.+?)}/';

	$translationContainerClass = (new \ReflectionClass(Translatable::class))->getShortName();
	foreach(Utils::stringifyKeys($languageDefinitions) as $key => $value){
		$parameters = [];
		$allParametersPositional = true;
		if(preg_match_all($parameterRegex, $value, $matches) > 0){
			foreach($matches[1] as $parameterName){
				if(is_numeric($parameterName)){
					$parameters[$parameterName] = "param$parameterName";
				}else{
					$parameters[$parameterName] = $parameterName;
					$allParametersPositional = false;
				}
			}
		}
		if($allParametersPositional){
			ksort($parameters, SORT_NUMERIC);
		}
		echo "\tpublic static function " .
			functionify($key) .
			"(" . implode(", ", array_map(fn(string $paramName) => "$translationContainerClass|string \$$paramName", $parameters)) . ") : $translationContainerClass{\n";
		echo "\t\treturn new $translationContainerClass(CollapseTranslationKeys::" . constantify($key) . ", [";
		foreach($parameters as $parameterKey => $parameterName){
			echo "\n\t\t\t";
			if(!is_numeric($parameterKey)){
				echo "'$parameterKey'";
			}else{
				echo $parameterKey;
			}
			echo " => \$$parameterName,";
		}
		if(count($parameters) !== 0){
			echo "\n\t\t";
		}
		echo "]);\n\t}\n\n";
	}

	echo "}\n";

	file_put_contents(dirname(__DIR__) . '/src/i18n/CollapseTranslationFactory.php', ob_get_clean());

	echo "Done generating CollapseTranslationFactory.\n";
}

$lang = parse_ini_file(Path::join(dirname(__DIR__), 'resources', 'i18n', 'en_US.ini'), false, INI_SCANNER_RAW);
if($lang === false){
	fwrite(STDERR, "Missing language files!\n");
	exit(1);
}

generate_known_translation_keys($lang);
generate_known_translation_factory($lang);
