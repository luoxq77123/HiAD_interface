<?php

/**
 * SiteStat provides dynamic table model supports for some application environments
 * such as dynamic-generated database tables, or simple CRUD actions.
 * @property string $date the table name associate with the denamic model.
 * new record :
 * $model = new SiteStat('20121020'); 
 * //use table prefix:
 * $model = new SiteStat('20121020');
 * $model->id = $id;
 * $model->name = 'zhangxugg@163.com';
 * $model->save();
 * 
 * update:
 * $model = SiteStat::model('20121020')->findByPk(1);
 * if($model) {
 *   $model->name = 'zhangxugg@163.com'
 *   $model->save();
 * }
 * $list = $model->findAll();
 * 
 * use non-default database connection :
 * SiteStat::$db = Yii::app()->getCompoments('db-extra');
 * tips : you must define the database connection informations in config/main.php
 * 'components' => array(
 *     'db-extra' => array(
 *         'class' => 'CDbConnection',
 *         'connectionString' => 'mysql:host=localhost;dbname=cdcol;charset=utf8',
 *         'username' => 'root',
 *         'password' =>'',
 *         'tablePrefix' => '',
 *         'autoConnect' => false,
 *     ),
 * )
 * 
 *
 */
class SiteStat extends CActiveRecord {

    private $_date;
    private $_md;
    public static $_models;
    // attribute used select data
    public $show_num;
    public $click_num;
    public $cpm_cost;
    public $cpd_cost;
    public $cpc_cost;
    public $name;
    public $ctr;
    public $unique_users;
    public $dedicgotd_ip;
    public $time_alias;
    public $balance;

    public function __construct($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_site;
        if ($date != null) {
            $this->_date = $date;
            parent::__construct();
        }
    }

    public static function model($date = '') {
        if (isset(self::$_models[$date])) {
            CActiveRecord::$db = Yii::app()->db_stat_site;
            return self::$_models[$date];
        } else {
            $model = self::$_models[$date] = new SiteStat(null);
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
        $model = new SiteStat(null);
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
        $table_name = "site_$date";
        $table_name = in_array($table_name, $this->getTables()) ? $table_name : "site";
        return "{{".$table_name."}}";
    }
    
    private function getTables(){
        $cache_name = md5('model_SiteStat_getTables');

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