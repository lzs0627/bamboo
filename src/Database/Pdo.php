<?php
namespace IQnote\Bamboo\Database;

/**
 * PDO でDB接続クラス
 * Class Pdo
 * @package IQnote\Bamboo\Database
 */
class Pdo extends AbstractDriver
{
    /**
     * @var \PDO $connection
     */
    protected $connection;

    public function __construct($name, array $config)
    {
        parent::__construct($name, $config);

        $dsn = isset($config['connection']['dsn']) ? $config['connection']['dsn'] : '';
        $collonPos = strpos($dsn, ':');
        $this->dbType = $collonPos ? substr($dsn, 0, $collonPos) : null;
    }
    
    public function connect()
    {
        if ($this->connection) {
            return ;
        }

        /**
         * @var $dsn
         * @var $username
         * @var $password
         * @var $persistent
         * @var $compress
         */
        extract($this->config['connection'] + array(
            'dsn'        => '',
            'username'   => null,
            'password'   => null,
            'persistent' => false,
            'compress'   => false,
        ));
                
        //Force PDO to use exceptions for all errors
        $attrs = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);

        $persistent === true && $attrs[\PDO::ATTR_PERSISTENT] = true;

        if (in_array(strtolower($this->dbType), array('mysql', 'mysqli')) && $compress) {
            // Use client compression with mysql or mysqli (doesn't work with mysqlnd)
            $attrs[\PDO::MYSQL_ATTR_COMPRESS] = true;
        }

        try {
            // Create a new PDO connection
            $this->connection = new \PDO($dsn, $username, $password, $attrs);
            
        } catch (\PDOException $e) {
            $errorCode = is_numeric($e->getCode()) ? $e->getCode() : 0;
            //パスワードがある場合、"*"に変更する
            throw new \Exception(str_replace($password, str_repeat('*', 10), $e->getMessage()), $errorCode);
        }

        if (! empty($this->config['charset'])) {
            // Set Charset for SQL Server connection
            if (strtolower($this->driverName()) == 'sqlsrv') {
                $this->connection->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_SYSTEM);
            } else {
                // Set the character set
                $this->setCharset($this->config['charset']);
            }
        }
        
        
    }
    
    public function disconnect()
    {
        // Destroy the PDO object
        $this->connection = null;
        return true;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->connection();
        
        $this->connection->exec('SET NAMES '.$charset);
    }
    
    public function query($sql)
    {
        $this->connection();

        try {
            $result = $this->connection->query($sql);
        } catch (\Exception $e) {
            $errCode = is_numeric($e->getCode()) ? $e->getCode() : 0;
            $errMsg = $e->getMessage().' with query: "'.$sql.'"';
            throw new \Exception($errMsg, $errCode);
        }

        $this->lastQuery = $sql;

        if (strpos(strtolower($sql), 'select') === 0) {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } elseif (strpos(strtolower($sql), 'insert') === 0) {
           
            return $this->connection->lastInsertId();
        } else {
            // Statement's errorCode() returns an empty string before execution,
            // and '00000' (five zeros) after a sucessfull execution:
            return $result->errorCode() === '00000' ? $result->rowCount() : -1;
        }
    }
    
    public function errorInfo()
    {
        return $this->connection->errorInfo();
    }
    
    public function inTransaction()
    {
        return $this->isIntransaction;
    }
    
    public function startTransaction()
    {
        $this->connect();
        $this->isIntransaction = true;

        return $this->connection->beginTransaction();
    }
    
    public function commitTransaction()
    {
        $this->isIntransaction = false;

        return $this->connection->commit();
    }
    
    public function rollbackTransaction()
    {
        $this->isIntransaction = false;

        return $this->connection->rollBack();
    }
    
    public function escape($string)
    {
        if (is_numeric($string)) {
            return $string;
        }
        $this->connect();
        return $this->connection->quote($string);
        
    }
    public function driverName()
    {
        return $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
}
