<?php

class City extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{city}}';
    }

    public function rules() {
        return array(
        );
    }

    public function getListByProvince($provinceid) {
        $cache_name = md5('model_city_getListByProvince_'.$provinceid);
        $list = Yii::app()->memcache->get($cache_name);
         if (!$list) {
            $list = array();
            $data = $this->findAll(array('condition' => 'fatherid=:fatherid', 'order' => 'id asc', 'params'=>array('fatherid'=>$provinceid)));
            foreach ($data as $one) {
                $list[$one->cityid] = $one->city;
            }
            Yii::app()->memcache->set($cache_name, $list, 30000);
        }
        return $list;
    }

}