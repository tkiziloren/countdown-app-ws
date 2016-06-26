<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 09.02.2013
 * Time: 20:20
 * To change this template use File | Settings | File Templates.
 */
require_once("StatsUtility.php");
require_once("ArrayToXml.php");


$username = "tevfik1";

$stats = array();
$stats[] = "islem1";
$stats[] = "islem2";
$stats[] = "islem3";
$stats[] = "islem4";
$stats[] = "islem5";
$stats[] = "islem6";
$stats[] = "islem7";
$stats[] = "islem8";
$stats[] = "kelime1";
$stats[] = "kelime2";
$stats[] = "kelime3";
$stats[] = "kelime4";
$stats[] = "kelime5";
$stats[] = "kelime6";
$stats[] = "kelime7";
$stats[] = "kelime8";
$stats[] = "genel";
$stats[] = "ben";

foreach ($stats as $statsId) {

    $results = StatsUtility::getStats($username, $statsId);
    echo "<hr/>" . $statsId . "<hr/>";

    if(Constants::$MESSAGE_STYLE == Constants::$MESSAGE_STYLE_JSON){
        echo json_encode($results);
    }
    echo "<br><br>";

}
