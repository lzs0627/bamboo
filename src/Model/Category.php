<?php
namespace IQnote\Bamboo\Model;

class Category extends Model
{
    public $primaryKeys = array('categoryid');
    
    public $dbConnName = false;//defaultを使う
    
    public $tableName = 's_category';
}
