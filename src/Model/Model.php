<?php
namespace IQnote\Bamboo\Model;

use \IQnote\Bamboo\Database\DB as DB;
use \IQnote\Bamboo\Database\Record\RecordSet as RecordSet;
use \IQnote\Bamboo\Database\Helper\ConnCreator as ConnCreator;
use \IQnote\Bamboo\Database\Helper\SqlCreator as SqlCreator;
/**
 * ＤＢのテーブルと関連するモデルクラスです。テーブルのupdate,delete,insert,find機能を提供する
 * Class Model
 * @package IQnote\Bamboo\Model
 */
class Model extends SqlCreator implements \ArrayAccess
{
    use ConnCreator;

    /**
     * tableの列を格納する
     * @var array
     */
    protected $fields = array();
    
    /**
     * 主キー
     * @var array
     */
    protected $primaryKeys = array();
    
    /**
     *tableの名前
     * @var string
     */
    protected $tableName;



    /**
     * 主キー$primary_keysによってレコードを更新する
     * @return mixed
     */
    public function update()
    {
        $updateFields = null;
        $primaryFields = null;
        $sql = null;

        foreach ($this->fields as $k => $v) {
            if (in_array($k, $this->primaryKeys)) {
                $primaryFields[] = $k
                    . '=' . (is_numeric($v) ? $v : (DB::getInstance($this->dbConnName)->escape($v)));
                continue;
            }
            
            if (is_null($v)) {
                    $updateFields[] = $k . '=NULL';
            } elseif (empty ($v)) {
                $updateFields[] = $k . "=''";
            } else {
                $updateFields[] = $k
                    . '=' . (is_numeric($v) ? $v : (DB::getInstance($this->dbConnName)->escape($v)));
            }
        }

        if ($updateFields) {
            $sql = "update " . $this->tableName . ' set ' . implode(',', $updateFields);
        }
        if ($sql && $primaryFields) {
            $sql .= " where " . implode(' and ', $primaryFields);
        } else {
            throw new \UnexpectedValueException('更新するパラメータを見つけません');
        }

        return DB::getInstance($this->dbConnName)->query($sql);
        
    }

    /**
     * @return mixed
     */
    public function insert()
    {
        $fieldsAtrr = null;
        $valuesAtrr = null;

        foreach ($this->fields as $k => $v) {
            $fieldsAtrr[] = $k;
            $valuesAtrr[] = is_numeric($v) ? $v : (DB::getInstance($this->dbConnName)->escape($v));
        }
        
        if ($fieldsAtrr && $valuesAtrr) {
            $sql = 'insert into' ." ". $this->tableName . ' (' . implode(',', $fieldsAtrr) . ') values (' . implode(',', $valuesAtrr). ')';

            return DB::getInstance($this->dbConnName)->query($sql);
        } else {
            throw new \UnexpectedValueException('更新するパラメータを見つけません');
        }
        
    }

    /**
     * データベースからデータを取得する
     * @param array $options
     * @return RecordSet
     */
    public function find(array $options)
    {
        $totalCount = false;
        $isMysql = false;
        if (in_array(strtolower(DB::getInstance($this->dbConnName)->dbType), array('mysql', 'mysqli'))) {
            $isMysql = true;
        }
        //limit と　offsetが設定された場合、トータルカウントを取得する
        if (isset($options['limit']) && isset($options['offset'])) {
            $totalCount = true;
        }

        //$options['totalcount']=falseが設定された場合、トータルカウントを取得しない
        if (isset($options['totalcount']) && $options['totalcount'] === false) {
            $totalCount = false;
        }

        $selectSql = $this->createSql($options, $isMysql);
        $rs = DB::getInstance($this->dbConnName)->query($selectSql);

        if (empty($rs) || $totalCount === false) {
            return new RecordSet($rs);
        }

        if ($isMysql) {
            $countSql = "SELECT FOUND_ROWS() AS count";
        } else {
            $options['select'] = array('count(*) as count');
            unset($options['limit']);
            unset($options['offset']);
            $countSql = $this->createSql($options, $isMysql);
        }
        $count = DB::getInstance($this->dbConnName)->query($countSql);

        return new RecordSet($rs, $count[0]['count']);
    }

    /**
     * データをクリアーする
     */
    public function clear()
    {
        $this->fields = array();
    }

    /**
     * AarrayAccess Interface 実装
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->fields[] = $value;
        } else {
            $this->fields[$offset] = $value;
        }
    }

    /**
     * AarrayAccess Interface 実装
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * AarrayAccess Interface 実装
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * AarrayAccess Interface 実装
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
    }
    /**
     * tableのfieldの値を設定する
     * @param array $data
     */
    public function set(array $data)
    {
        $this->fields = array();
        foreach ($data as $k => $v) {
            $this->fields[$k] = $v;
        }
    }
    
    /**
     * tableのfieldの値を取得する
     * @param string $name
     * @return null
     */
    public function __get($name)
    {
        return $this->fields[$name];
    }
    
    /**
     * tableのfieldの値を設定する
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }
}
