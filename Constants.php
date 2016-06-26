<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tkiziloren
 * Date: 29.06.2012
 * Time: 14:33
 * To change this template use File | Settings | File Templates.
 */
require_once("StatsQueries.php");
class Constants
{
    // Global Constants

    public static $GAME_PUBLISHED_DATE  = '2013-01-01';

    public static $WORK_AT_LOCALHOST = true;
    public static $WORK_ENCRYPTED = true;

    public static $MESSAGE_STYLE_XML = 'XML';
    public static $MESSAGE_STYLE_JSON = 'JSON';

    public static $MESSAGE_STYLE;

    public static $DEBUG_MODE_ACCEPT_BOTH_GET_AND_POST = true;
    public static $key = '!1905GALATASARAYGALATASARAY1905!';

    // Success Codes
    public static $SUCCESS_GENERAL_SUCCESS_CODE = 200; //
    public static $SUCCESS_GAME_RESULT_INSERTED = 201; //
    public static $SUCCESS_USER_INSERTED = 202; //

    // Error Codes
    public static $ERROR_INVALID_JSON = 901; //
    public static $ERROR_WHEN_INSERTING_GAME_RESULT = 902; //
    public static $ERROR_WHEN_INSERTING_USER = 903; //


    public static $RESULT_USERNAME_EXISTS = 951;//
    public static $RESULT_USERNAME_DOESNT_EXIST = 952;//

    public static $RESULT_WORD_IS_CORRECT = 953;
    public static $RESULT_WORD_IS_INCORRECT = 954;

    public static $RESULT_LOGIN_SUCCESS = 955;
    public static $RESULT_LOGIN_FAIL = 956;

    public static $RESULT_SIGNUP_SUCCESS = 957;
    public static $RESULT_SIGNUP_FAIL = 958;

    public static $RESULT_REMIND_PASSWORD_SUCCESS = 959;
    public static $RESULT_REMIND_PASSWORD_FAIL = 960;

    public static $RESULT_SUGGEST_WORD_SUCCESS = 961;
    public static $RESULT_SUGGEST_WORD_FAIL = 962;
    public static $RESULT_SUGGEST_WORD_EXISTS = 963;

    public static $RESULT_UPDATE_USER_SUCCESS = 964;
    public static $RESULT_UPDATE_USER_FAIL = 965;

    //URL Params
    public static $URL_PARAM_ACTION = "a";
    public static $URL_PARAM_CIPHER_TEXT = "ct";
    public static $URL_PARAM_PLAIN_BASE64_ENCODED_TEXT = "pt";

    //Actions For User Operations
    public static $ACTION_LOGIN_USER = "lu";
    public static $ACTION_SIGNUP_USER = "su";
    public static $ACTION_REMIND_PASSWORD  = "rp";

    public static $ACTION_GET_STATS = "gs";

    //Actions For Word Game
    public static $ACTION_CHECK_FOR_WORD = "cfw";
    public static $ACTION_SUGGEST_WORD = "sw";

    //Actions For Number Game
    public static $ACTION_SAVE_NUMBER_GAME_RESULTS = "snr";
    public static $ACTION_UPDATE_USER = "uu";

    public static $hostname;
    public static $username;
    public static $password;
    public static $database;


    public static function init(){
        if (self::$WORK_AT_LOCALHOST) {
            self::$hostname = 'localhost';
            self::$username = 'root';
            self::$password = 'root';
            self::$database = 'bkbi';
        }
        else {
            self::$hostname = ’somehostingdb’;
            self::$username = 'bkbiturkce';
            self::$password = ‘somehostingpassword’;
            self::$database = 'bkbiturkce';
        }

        self::$MESSAGE_STYLE = self::$MESSAGE_STYLE_JSON;
        //self::$MESSAGE_STYLE = self::$MESSAGE_STYLE_XML;
    }
}
Constants::init();

