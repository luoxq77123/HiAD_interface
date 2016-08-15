<?php

class App extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{app}}';
    }
    
    public function primaryKey() {
        return 'id';
    }

    public function getDataById($appId) {
        $cacheKey = md5('hiad-interface_App_getDataById_'.$appId);
        $data = Yii::app()->memcache->get($cacheKey);
        if (!$data) {
            $data = $this->find(array(
                'select' => 'app_type_id,app_key,status',
                'condition'=>'id=:id',
                'params'=>array(':id' => $appId)
            ));
            Yii::app()->memcache->set($cacheKey, $data, 300);
        }
        return $data;
    }
    
    public function appTypeList() {
        return array(
            '1' => 'ios',
            '2' => 'android'
        );
    }
}