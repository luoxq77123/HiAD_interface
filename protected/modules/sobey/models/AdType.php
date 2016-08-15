<?php

class AdType extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{ad_type}}';
    }

    public function rules() {
        return array(
        );
    }

    public function getAdTypeName(){
        $cache_name = md5('model_adtype_getAdTypeName');
        $typeName = Yii::app()->memcache->get($cache_name);
        if (!$typeName) {
            $data = $this->findAll();
             $typeName = array();
            foreach ($data as $one) {
                $typeName[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $typeName, 300);
        }
        return $typeName;
    }

}