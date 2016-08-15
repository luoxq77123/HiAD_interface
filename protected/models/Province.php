<?php

class Province extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{province}}';
    }

    public function rules() {
        return array(
        );
    }

    public function getList() {
        $cache_name = md5('model_Province_getList');
        $list = Yii::app()->memcache->get($cache_name);
        if (!$list) {
            $list = array();
            $data = $this->findAll(array('order' => 'id asc'));
            foreach ($data as $one) {
                $list[$one->provinceid] = $one->province;
            }
            Yii::app()->memcache->set($cache_name, $list, 30000);
        }
        return $list;
    }

}