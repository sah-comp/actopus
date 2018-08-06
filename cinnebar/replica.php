<?php
/**
 * @see https://github.com/gabordemooij/redbean
 */
$pat = "/\/\*([\n]|.)+?\*\//";

$code = "";
$loader = simplexml_load_file("replica.xml");
$items = $loader->load->item;
foreach ($items as $item) {
    echo "Adding: $item \n";
    $code .= file_get_contents($item) . "\n";
}
$code .= "

/**
 * Cinnebar.
 */
class Cinnebar extends Cinnebar_Facade
{
}

";

//Clean php tags and whitespace from codebase.
$code = "<?php ".str_replace(array("<?php", "<?", "?>"), array("", "", ""), $code);
file_put_contents("cinnebar.php", $code);

if (isset($_SERVER['argc']) && $_SERVER['argc']>1 && $_SERVER['argv'][1]=='-s') {
    file_put_contents("cinnebar.php", php_strip_whitespace("cinnebar.php"));
}

$fileStr = file_get_contents('cinnebar.php');
$newStr  = '';


function removeEmptyLines($string)
{
    return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);
}

$commentTokens = array(T_COMMENT);

if (defined('T_DOC_COMMENT')) {
    $commentTokens[] = T_DOC_COMMENT;
} // PHP 5
if (defined('T_ML_COMMENT')) {
    $commentTokens[] = T_ML_COMMENT;
}  // PHP 4

$tokens = token_get_all($fileStr);

foreach ($tokens as $token) {
    if (is_array($token)) {
        if (in_array($token[0], $commentTokens)) {
            continue;
        }

        $token = $token[1];
    }

    $newStr .= $token;
}

$newStr = removeEmptyLines($newStr);

$newStr = str_replace("<"."?php", "", $newStr);
$newStr = "<"."?php\n// Written by Stephan A. Hombergs, Copyright 2012-2018. Licensed @see license.txt\n".$newStr;

file_put_contents('cinnebar.pack.php', $newStr);
