<?php

class Aca extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{aca}}';
    }

    public function getAcaMap() {
        $cache_name = md5('model_Aca_getAcaMap');

        $acaMap = Yii::app()->memcache->get($cache_name);
        if (!$acaMap) {
            $acaMap = array();
            $acas = $this->findAll();
            foreach ($acas as $one) {
                if (trim($one->controller) && trim($one->action))
                    $acaMap[$one->controller][$one->action] = $one->id;
            }
            Yii::app()->memcache->set($cache_name, $acaMap, 300);
        }
        return $acaMap;
    }

    public function getAcaName() {
        $cache_name = md5('model_Aca_getAcaName');

        $acaName = Yii::app()->memcache->get($cache_name);
        if (!$acaName) {
            $acaName = array();
            $acas = $this->findAll();
            foreach ($acas as $one) {
                if (trim($one->controller) && trim($one->action))
                    $acaName[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $acaName, 300);
        }
        return $acaName;
    }

}