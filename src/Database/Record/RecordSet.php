<?php
namespace IQnote\Bamboo\Database\Record;

/**
 * Modelクラスで取得した結果を格納するカラスです
 * @author lizhaoshi
 */
class RecordSet implements \Iterator, \ArrayAccess, \Countable
{
    protected $arrayList;
    protected $pos = 0;
    protected $totalCount = 0;

    /**
     * @param array $dataSet
     * @param null|int $totalCount
     */
    public function __construct(array $dataSet, $totalCount = null)
    {
        $this->arrayList = $dataSet;
        $this->pos= 0;
        $this->totalCount = is_null($totalCount) ? count($dataSet) : $totalCount;
    }

    /**
     * Select文で取得した結果の全部の数；
     * @return int
     */
    public function totalCount()
    {
        return $this->totalCount;
    }

    /**
     * RecordSetにセットされた数
     * @return int
     */
    public function pageCount()
    {
        return count($this->arrayList);
    }
    /**
     * RecordSetにセットされた数
     * @return int
     */
    public function count()
    {
        return count($this->arrayList);
    }

    /**
     * Iterator Interface実装
     */
    public function rewind()
    {
        $this->pos = 0;
    }

    /**
     * Iterator Interface実装
     */
    public function current()
    {
        return $this;
    }

    /**
     * Iterator Interface実装
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * Iterator Interface実装
     */
    public function next()
    {
        ++$this->pos;
    }

    /**
     * Iterator Interface実装
     */
    public function valid()
    {
        return isset($this->arrayList[$this->pos]);
    }

    /**
     * AarrayAccess Interface 実装
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->arrayList[$this->pos][] = $value;
        } else {
            $this->arrayList[$this->pos][$offset] = $value;
        }
    }

    /**
     * AarrayAccess Interface 実装
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->arrayList[$this->pos][$offset]);
    }

    /**
     * AarrayAccess Interface 実装
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->arrayList[$this->pos][$offset]);
    }

    /**
     * AarrayAccess Interface 実装
     * @param int $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return isset($this->arrayList[$this->pos][$offset]) ? $this->arrayList[$this->pos][$offset] : null;
    }

    /**
     * 指針でデータを取得できるように修正
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->arrayList[$this->pos][$name];
    }

    /**
     * 指針でデータをを設定する
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->arrayList[$this->pos][$name] = $value;
    }

    /**
     * posが指定しているデータを返す
     * @return mixed
     */
    public function currentRecord()
    {
        return $this->arrayList[$this->pos];
    }
}
