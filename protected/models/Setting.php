<?php

class Setting extends CActiveRecord {

    public function __construct() {
        CActiveRecord::$db = Yii::app()->db;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{setting}}';
    }

    public function rules() {
        return array(
            array('name,set_key,set_val', 'required', 'message' => '{attribute}不能为空', 'on' => 'edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '参数名称:',
            'set_key' => '参数键名:',
            'set_val' => '参数值:',
        );
    }

    public function relations() {
        return array();
    }

    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $getSettings_cache = md5('model_Settings_getSettings');
        Yii::app()->memcache->delete($getSettings_cache);
    }

    public function getSettings() {
        $cache_name = md5('model_Settings_getSettings');
        $settings = Yii::app()->memcache->get($cache_name);
        if (!$settings) {
            $data = $this->findAll();
            $settings = array();
            foreach ($data as $one) {
                $settings[$one->set_key] = $one->set_val;
            }
            Yii::app()->memcache->set($cache_name, $settings, 300);
        }
        return $settings;
    }

}