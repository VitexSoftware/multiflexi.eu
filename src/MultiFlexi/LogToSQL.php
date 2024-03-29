<?php

/**
 * Database Engine class
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2018-2023 Vitex@hippy.cz (G)
 */

namespace MultiFlexi;

/**
 * Description of LogToSQL
 *
 * @author vitex
 */
class LogToSQL extends \Ease\SQL\Engine implements \Ease\Logger\Loggingable
{
    /**
     * Saves obejct instace (singleton...).
     */
    private static $instance = null;
    public $myTable = 'log';
    public $applicationId = null;
    public $userId = null;

    /**
     *
     */
    public function __construct()
    {
        //        parent::__construct();
        $this->setUser(User::singleton()->getUserID());
    }

    /**
     * Pri vytvareni objektu pomoci funkce singleton (ma stejne parametry, jako
     * konstruktor) se bude v ramci behu programu pouzivat pouze jedna jeho
     * instance (ta prvni).
     *
     * @link http://docs.php.net/en/language.oop5.patterns.html Dokumentace a
     * priklad
     */
    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * ID of current application
     * @param int $id
     */
    public function setApplication($id)
    {
        $this->applicationId = $id;
    }

    /**
     * ID of current user
     * @param int $id
     */
    public function setUser($id)
    {
        $this->userId = $id;
    }

    /**
     * Zapise zapravu do logu.
     *
     * @param string $caller  název volajícího objektu
     * @param string $message zpráva
     * @param string $type    typ zprávy (success|info|error|warning|*)
     *
     * @return int ID of log in databae
     */
    public function addToLog($caller, $message, $type = 'message')
    {
        return $this->insertToSQL([
                    'venue' => self::venuize($caller),
                    'severity' => $type,
                    'message' => $this->getPdo()->quote(self::removeEmoji($message)),
                    'apps_id' => $this->applicationId,
                    'user_id' => $this->userId
        ]);
    }

    /**
     * Prepare venue able to be saved into sql column
     *
     * @param mixed $caller
     */
    public static function venuize($caller)
    {
        switch (gettype($caller)) {
            case 'object':
                if (method_exists($caller, 'getObjectName')) {
                    $venue = $caller->getObjectName();
                } else {
                    $venue = get_class($caller);
                }
                break;
            case 'string':
            default:
                $venue = $caller;
                break;
        }
        return substr($venue, 0, 254);
    }

    /**
     *
     * @param string $string
     *
     * @return string
     */
    public static function removeEmoji($string)
    {
        if ($string) {
            // Match Enclosed Alphanumeric Supplement
            $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
            $clear_string = preg_replace($regex_alphanumeric, '', $string);
            // Match Miscellaneous Symbols and Pictographs
            $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
            $clear_string = preg_replace($regex_symbols, '', strval($clear_string));
            // Match Emoticons
            $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
            $clear_string = preg_replace($regex_emoticons, '', $clear_string);
            // Match Transport And Map Symbols
            $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
            $clear_string = preg_replace($regex_transport, '', $clear_string);
            // Match Supplemental Symbols and Pictographs
            $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
            $clear_string = preg_replace($regex_supplemental, '', $clear_string);
            // Match Miscellaneous Symbols
            $regex_misc = '/[\x{2600}-\x{26FF}]/u';
            $clear_string = preg_replace($regex_misc, '', $clear_string);
            // Match Dingbats
            $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
            $clear_string = preg_replace($regex_dingbats, '', $clear_string);
        } else {
            $clear_string = '';
        }
        return $clear_string;
    }
}
