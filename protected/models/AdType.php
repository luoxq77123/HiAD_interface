<?php

class AdType extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{ad_type}}';
    }
	
	public function getPositionAdTypes($position_type_id){
		$cache_name = md5('model_AdType_getPositionAdTypes_'.$position_type_id);
        $adTypes = Yii::app()->memcache->get($cache_name);
        if (!$adTypes) {
            $data = $this->findAll(array(
					'select'=>'id, name',
					'condition'=>'position_type_id=:position_type_id',
					'order'=>'id asc',
					'params'=>array(':position_type_id' => $position_type_id)
                ));
            foreach ($data as $one) {
                $adTypes[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $adTypes, 30000);
        }
        return $adTypes;
        
    }
	
}