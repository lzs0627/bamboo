<?php
namespace IQnote\Bamboo\Database\Helper;

/**
 * Class ConnCreator
 * データベースの接続名を定義する
 * @package IQnote\Bamboo\Database\Helper
 */
trait ConnCreator
{

    protected $dbConnName;

    /**
     * @param null|string $dbConnName
     */
    public function __construct($dbConnName = null)
    {
        $this->setConnName($dbConnName);
    }

    /**
     * @param string $dbConnName
     */
    public function setConnName($dbConnName)
    {
        $this->dbConnName = $dbConnName;
    }
}