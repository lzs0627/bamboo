<?php
namespace IQnote\Bamboo\Database;

/**
 * 共通DB操作するAPIを定義する
 * Class AbstractDriver
 * @package IQnote\Bamboo\Database
 */
abstract class AbstractDriver
{
    protected $instanceName;
    protected $config;
    protected $connection;
    protected $isIntransaction = false;
    protected $lastQuery;
    public $dbType = '';

    protected function __construct($instanceName, array $config)
    {
        $this->instanceName = $instanceName;
        $this->config = $config;
    }
    
    /**
     * データベースへ接続
     */
    abstract public function connect();
    abstract public function disconnect();
    abstract public function setCharset($charset);
    abstract public function query($sql);
    abstract public function errorInfo();
    abstract public function inTransaction();
    abstract public function startTransaction();
    abstract public function commitTransaction();
    abstract public function rollbackTransaction();
    abstract public function escape($sql);


    public function connection()
    {
        $this->connection or $this->connect();

        return $this->connection;
    }
    
    public function hasConnection()
    {
        return $this->connection ? true : false;
    }
    
    final public function __toString()
    {
        return $this->instanceName;
    }
    
    final public function __destruct()
    {
        $this->disconnect();
    }
}
