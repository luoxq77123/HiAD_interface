<?php
/**
 * 调用hm_adms_a客户端统计数据库数据
 */
class AppStat extends CActiveRecord {

    private $_date;
    private $_md;
    public static $_models;

    public function __construct($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_client;
        if ($date != null) {
            $this->_date = $date;
            parent::__construct();
        }
    }

    public static function model($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_client;
        if (isset(self::$_models[$date]))
            return self::$_models[$date];
        else {
            $model = self::$_models[$date] = new AppStat(null);
            $model->_date = $date;
            $model->_md = new CActiveRecordMetaData($model);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    public function refreshMetaData() {
        $finder = self::model($this->_date);
        $finder->_md = new CActiveRecordMetaData($finder);
        if ($this !== $finder)
            $this->_md = $finder->_md;
    }

    protected function instantiate($attributes) {
        $model = new AppStat(null);
        $model->_date = $this->_date;
        return $model;
    }

    public function getMetaData() {
        if ($this->_md !== null)
            return $this->_md;
        else
            return $this->_md = self::model($this->_date)->_md;
    }

    public function tableName() {
        return $this->getTableName($this->_date);
    }

    private function getTableName($date) {
        $table_name = "app_$date";
        $table_name = in_array($table_name, $this->getTables()) ? $table_name : "app";
        return "{{".$table_name."}}";
    }
    
    private function getTables(){
        $cache_name = md5('model_AppStat_getTables');
        $tables = Yii::app()->memcache->get($cache_name);
        if(!$tables){
            $tables_data = CActiveRecord::$db->createCommand('show tables;')->queryAll();
            $tables = array();
            foreach($tables_data as $f){
                foreach($f as $s){
                    $tables[] = $s;
                }
            }
            Yii::app()->memcache->set($cache_name, $tables, 300);
        }
        return $tables;
    }
}