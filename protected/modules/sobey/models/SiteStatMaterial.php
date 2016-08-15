<?php
class SiteStatMaterial extends CActiveRecord {
    // attribute used select data
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

    public static function model($className = __CLASS__) {
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db_stat_site;
        return parent::model($className);
    }

    public function tableName() {
        return 'map_stat_material';
    }
}