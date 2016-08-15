<?php

class District extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{district}}';
    }

    public function rules() {
        return array(
        );
    }

    public function getListByCity($cityid) {
        $cache_name = md5('model_city_getListByCity_'.$cityid);
        $list = Yii::app()->memcache->get($cache_name);
         if (!$list) {
            $list = array();
            $data = $this->findAll(array('condition' => 'fatherid=:fatherid', 'order' => 'id asc', 'params'=>array('fatherid'=>$cityid)));
            foreach ($data as $one) {
                $list[$one->districtid] = $one->district;
            }
            Yii::app()->memcache->set($cache_name, $list, 30000);
        }
        return $list;
    }

}