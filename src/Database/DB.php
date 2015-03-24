<?php
namespace IQnote\Bamboo\Database;

use IQnote\Bamboo\Config;

/**
 * シングルトン デザインパターン
 * Class DB
 * @package IQnote\Bamboo\Database
 */
class DB
{
    public static $instances = array();

    /**
     * @param string $connectionName
     * @return Driver
     */
    public static function getInstance($connectionName = null)
    {
        ! $connectionName && $connectionName = 'default';
 
        if (! isset(static::$instances[$connectionName]) || ! static::$instances[$connectionName]) {
            $dbSetting = Config::get("db.{$connectionName}");

            if (! isset($dbSetting['type'])) {
                throw new \UnexpectedValueException('Database setting "type" not defined ');
            }
            $dbHandler = '\\IQnote\\Bamboo\\Database\\' . ucfirst($dbSetting['type']);
            static::$instances[$connectionName] = new $dbHandler($connectionName, $dbSetting['config']);
        }
        
        return static::$instances[$connectionName];
    }
}
