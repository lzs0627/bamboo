<?php
namespace IQnote\Bamboo\Database\Helper;

/**
 * SQLのselect文を作成するクラスです。
 * Class SqlCreator
 * @package IQnote\Bamboo\Database\Helper;
 */
class SqlCreator
{
    /**
     * $select = array('xxx.aaa','xxx.bbb','xxx.ccc')
     */
    protected $select = array();
    
    /**
     * $from=array('alias1'=>'table1','alias2'=>'table2')
     * @var array
     */
    protected $from = array();
    
    /**
     * $join=array(
     *          'left'=>array(
     *                      'alias1'=>array(
     *                                  'table'=>'category',
     *                                  'where' => array('f.aa=ddd','f.bb=33')
     *                              )
     *                  )
     *       )
     * @var array
     */
    protected $join = array();
    
    /**
     * $where= array('f.aa=ddd','f.bb=33');
     * @var array
     */
    protected $where = array();
    
    /**
     * $group=array('alias1.aa','alias2.bb')
     * @var array
     */
    protected $group = array();


    /**
     * $order = array('alias.field desc','alias2.field asc')
     * @var array
     */
    protected $order = array();
    /**
     *
     * @var int
     */
    protected $limit = null;
    /**
     *
     * @var int
     */
    protected $offset = null;

    /**
     * @param array $options
     * @param boolean $useSqlCalcFoundRows
     * @return string
     */
    public function createSql($options, $useSqlCalcFoundRows = true)
    {
        $this->setOptions($options);
        
        $selectAppend = '';
        
        if (!is_null($this->limit)
            && !is_null($this->offset)
            && $useSqlCalcFoundRows
        ) {
             $selectAppend = ' SQL_CALC_FOUND_ROWS';
        }

        if (empty($this->select)) {
            $select = "SELECT$selectAppend * ";
        } else {
            $select = "SELECT$selectAppend " .implode(',', array_filter($this->select));
        }
        $from = '';
        foreach ($this->from as $alias => $table) {
            if (! $alias || ! $table) {
                continue;
            }
            $from[] = $table . ' as ' . $alias;
        }
        $select .= " FROM " . implode(', ', $from);
        
        foreach ($this->join as $joinType => $tables) {
            foreach ($tables as $alias => $cond) {
                if (! $alias || ! $cond) {
                    continue;
                }
                $select .= ' ' . $joinType . ' JOIN ' . $cond['table'] . ' as '. $alias;
                $select .= ' ON ' . implode(' AND ', $cond['where']);
            }
        }
        
        if (! empty($this->where)) {
            $select .= ' WHERE ' . implode(' AND ', array_filter($this->where));
        }
        
        if (!empty($this->group)) {
            $select .= ' GROUP BY ' . implode(', ', array_filter($this->group));
        }
        
        if (!empty($this->order)) {
            $select .= ' ORDER BY ' . implode(', ', array_filter($this->order));
        }
        
        if (!is_null($this->limit) && !is_null($this->offset)) {
            $select .= ' LIMIT ' . intval($this->offset) . ', ' . intval($this->limit);
        }
        
        return $select;
    }

    /**
     * リセット
     */
    public function clearOptions()
    {
        $this->select = array();
        $this->from = array();
        $this->join = array();
        $this->group = array();
        $this->where = array();
        $this->order = array();
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * Selectに必要となるキーワードを設定
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->clearOptions();
        $optionFields = array('select', 'from', 'join', 'group', 'where', 'offset', 'limit', 'order');

        /**
         * TODO::小文字と大文字両方チェックするように
         */
        foreach ($optionFields as $field) {
            if (isset($options[$field])) {
                $this->$field = $options[$field];
            }
        }
    }
}
