<?php

class Config extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{config}}';
    }

    public function rules() {
    }

    public function attributeLabels() {
        return array(
            'name' => '参数名称:',
            'key' => '参数键名:',
            'val' => '参数值:',
        );
    }

    public function relations() {
        return array();
    }
    
    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $getSettings_cache = md5('model_Config_getConfigs');
        Yii::app()->memcache->delete($getSettings_cache);
    }

    public function getConfigs(){
        $cache_name = md5('model_Config_getConfigs');
        $configs = Yii::app()->memcache->get($cache_name);
        if (!$configs) {
            $data = $this->findAll();
            $configs=array();
            foreach ($data as $one) {
                $configs[$one->key] =$one->val;
            }
            Yii::app()->memcache->set($cache_name, $configs, 300);
        }
        return $configs;
    }
}