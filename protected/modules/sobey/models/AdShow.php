<?php
class AdShow extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{ad_show}}';
    }
    
    public function getPositionAdShows($ad_type_id=1){
        $cache_name = md5('model_AdShow_getPositionAdShows_'.$ad_type_id);
        $adShows = Yii::app()->memcache->get($cache_name);
        if (!$adShows) {
            $data = $this->findAll(array(
                    'select'=>'id, name',
                    'condition'=>'ad_type_id=:ad_type_id',
                    'order'=>'id asc',
                    'params'=>array(':ad_type_id' => $ad_type_id)
                ));
            foreach ($data as $one) {
                $adShows[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $adShows, 30000);
        }
        return $adShows;
        
    }
    
    public function getListByTypeId($adTypeId=1) {
        $cache_name = md5('model_AdShow_getListByTypeId_'.$adTypeId);
        $adShows = Yii::app()->memcache->get($cache_name);
        if (!$adShows) {
            $data = $this->findAll(array(
                'select'=>'id, name, code',
                'condition'=>'ad_type_id=:ad_type_id',
                'order'=>'id asc',
                'params'=>array(':ad_type_id' => $adTypeId)
            ));
            foreach ($data as $one) {
                $adShows[$one->id]['id'] = $one->id;
                $adShows[$one->id]['name'] = $one->name;
                $adShows[$one->id]['code'] = $one->code;
            }
            Yii::app()->memcache->set($cache_name, $adShows, 30000);
        }
        return $adShows;
    }

    public function getAdShows(){
        $cache_name = md5('model_AdShow_getAdShows');
        $adShows = Yii::app()->memcache->get($cache_name);
        if (!$adShows) {
            $data = $this->findAll();
            foreach ($data as $one) {
                $adShows[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $adShows, 30000);
        }
        return $adShows;
        
    }
}