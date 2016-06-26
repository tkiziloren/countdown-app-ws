<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 29.06.2012
 * Time: 14:30
 * To change this template use File | Settings | File Templates.
 */
require_once("Constants.php");
require_once("Utility.php");

class StatsUtility{

    public $STATS_NAMES;
    function __construct()
    {
        $STATS_NAMES = array();
        $STATS_NAMES[] = array("islem1"  => StatsQueries::$QUERY_ISLEM1);
        $STATS_NAMES[] = array("islem2"  => StatsQueries::$QUERY_ISLEM2);
        $STATS_NAMES[] = array("islem3"  => StatsQueries::$QUERY_ISLEM3);
        $STATS_NAMES[] = array("islem4"  => StatsQueries::$QUERY_ISLEM4);
        $STATS_NAMES[] = array("islem5"  => StatsQueries::$QUERY_ISLEM5);
        $STATS_NAMES[] = array("islem6"  => StatsQueries::$QUERY_ISLEM6);
        $STATS_NAMES[] = array("islem7"  => StatsQueries::$QUERY_ISLEM7);
        $STATS_NAMES[] = array("islem8"  => StatsQueries::$QUERY_ISLEM8);

        $STATS_NAMES[] = array("kelime1" => StatsQueries::$QUERY_KELIME1);
        $STATS_NAMES[] = array("kelime2" => StatsQueries::$QUERY_KELIME2);
        $STATS_NAMES[] = array("kelime3" => StatsQueries::$QUERY_KELIME3);
        $STATS_NAMES[] = array("kelime4" => StatsQueries::$QUERY_KELIME4);
        $STATS_NAMES[] = array("kelime5" => StatsQueries::$QUERY_KELIME5);
        $STATS_NAMES[] = array("kelime6" => StatsQueries::$QUERY_KELIME6);
        $STATS_NAMES[] = array("kelime7" => StatsQueries::$QUERY_KELIME7);
        $STATS_NAMES[] = array("kelime8" => StatsQueries::$QUERY_KELIME8);

        $STATS_NAMES[] = array("genel"   => StatsQueries::$QUERY_GENEL);
        $STATS_NAMES[] = array("ben"     => StatsQueries::$QUERY_BEN);
    }

    public static function getStats($username, $statsId)
    {
        if($statsId == "genel")
            return StatsUtility::getStatsForGenel($statsId);
        else if($statsId == "ben"){
            $userId = Utility::getIdForUserName($username);
            if($userId == null){
                return null;
            }
            return StatsUtility::getStatsForSpecificUser($statsId,$userId);
        }

        $query = StatsUtility::getQueryForStatsId($statsId);
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        Utility::setCharset();
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $data = array();
        while($temp = mysql_fetch_assoc($results)){
            if($temp == false){
                continue;
            }
            $data[]=$temp;
        }
        mysql_close($link);
        return $data;
    }

    public static function getStatsForGenel($statsId){

        $query = StatsUtility::getQueryForStatsId($statsId);
        $query = str_replace("GAME_START_DATE", Constants::$GAME_PUBLISHED_DATE, $query);
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        Utility::setCharset();
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $data = array();
        while($temp = mysql_fetch_assoc($results)){
            if($temp == false){
                continue;
            }
            $data[]=$temp;
        }
        mysql_close($link);
        return $data;
    }


    public static function getStatsForSpecificUser($statsId, $userid){

        $query = StatsUtility::getQueryForStatsId($statsId);
        $query = str_replace("'USER_ID'", $userid, $query);
        $link = mysql_connect(Constants::$hostname, Constants::$username, Constants::$password) or die('Cannot connect to the DB');
        Utility::setCharset();
        mysql_select_db(Constants::$database, $link) or die('Cannot select the DB');
        $results = mysql_query($query, $link) or die('Errant query:  '.$query);

        $data = array();
        while($temp = mysql_fetch_assoc($results)){
            if($temp == false){
                continue;
            }
            $data[]=$temp;
        }
        mysql_close($link);
        return $data;

    }
    public static function  getQueryForStatsId($statsId){

        if($statsId == "islem1")
            return StatsQueries::$QUERY_ISLEM1;

        if($statsId == "islem2")
            return StatsQueries::$QUERY_ISLEM2;

        if($statsId == "islem3")
            return StatsQueries::$QUERY_ISLEM3;

        if($statsId == "islem4")
            return StatsQueries::$QUERY_ISLEM4;

        if($statsId == "islem5")
            return StatsQueries::$QUERY_ISLEM5;

        if($statsId == "islem6")
            return StatsQueries::$QUERY_ISLEM6;

        if($statsId == "islem7")
            return StatsQueries::$QUERY_ISLEM7;

        if($statsId == "islem8")
            return StatsQueries::$QUERY_ISLEM8;

        if($statsId == "kelime1")
            return StatsQueries::$QUERY_KELIME1;

        if($statsId == "kelime2")
            return StatsQueries::$QUERY_KELIME2;

        if($statsId == "kelime3")
            return StatsQueries::$QUERY_KELIME3;

        if($statsId == "kelime4")
            return StatsQueries::$QUERY_KELIME4;

        if($statsId == "kelime5")
            return StatsQueries::$QUERY_KELIME5;

        if($statsId == "kelime6")
            return StatsQueries::$QUERY_KELIME6;

        if($statsId == "kelime7")
            return StatsQueries::$QUERY_KELIME7;

        if($statsId == "kelime8")
            return StatsQueries::$QUERY_KELIME8;

        if($statsId == "genel")
            return StatsQueries::$QUERY_GENEL;

        if($statsId == "ben")
            return StatsQueries::$QUERY_BEN;

        return null;

    }


    public static function isStatsIdValid($statsId){
        return StatsUtility::getQueryForStatsId($statsId) != null;
    }



}