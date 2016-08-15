<?php
class VideoStatMate extends CActiveRecord {

    private $_date;
    private $_md;
    public static $_models;
    public $id;
    public $show_num;
    public $click_num;
    public $cpd_cost;
    public $cpm_cost;
    public $cpc_cost;
    public $name;
    public $ctr;
    public $unique_users;
    public $dedicgotd_ip;
    public $time_alias;

    public function __construct($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_videomate;
        if ($date != null) {
            $this->_date = $date;
            parent::__construct();
        }
    }

    public static function model($date = '') {
        if (isset(self::$_models[$date])) {
            CActiveRecord::$db = Yii::app()->db_stat_videomate;
            return self::$_models[$date];
        } else {
            $model = self::$_models[$date] = new VideoStatMate(null);
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
        $model = new VideoStatMate(null);
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
        $table_name = "hm_videomaterial_$date";
        $table_name = in_array($table_name, $this->getTables()) ? $table_name : "hm_videomaterial";
        return "{{".$table_name."}}";
    }

    private function getTables(){
        $cache_name = md5('model_VideoStatMate_getTables');
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

    /**
     * ´´½¨Í³¼Æ·Ö±í
     */
    public function createStatTable() {
        $date = date("Ymd", time());
        $table = 'hm_videomaterial_' . $date;
        $query = "
        CREATE TABLE IF NOT EXISTS `{$table}` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `material_id` int(10) NOT NULL DEFAULT '0' COMMENT '物料id',
          `stat_id` int(10) NOT NULL DEFAULT '0' COMMENT '统计id',
          `com_id` int(11) DEFAULT NULL,
          `ip` int(10) DEFAULT '0' COMMENT 'ip转换整形',
          `is_click` tinyint(1) DEFAULT '0' COMMENT '是否点击 1：是  0 不是',
          `click_count` int(10) DEFAULT '0' COMMENT '点击计数',
          `cost_mode` tinyint(1) DEFAULT '0' COMMENT '计费模式；1.CPD; 2.CPM; 3.CPC',
          `cost` float(6,2) DEFAULT '0.00' COMMENT '计费',
          `click_time` int(11) NOT NULL DEFAULT '0' COMMENT '点击时间',
          `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
          PRIMARY KEY (`id`),
          KEY `index` (`material_id`) USING BTREE
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        Yii::app()->db_stat_videomate->createCommand($query)->execute();
    }
}