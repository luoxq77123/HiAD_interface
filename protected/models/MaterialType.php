<?php

class MaterialType extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_type}}';
    }
	
	public function getMaterialTypes(){
		$cache_name = md5('model_AdType_MaterialTypes_');
        $MaterialTypes = Yii::app()->memcache->get($cache_name);
        if (!$MaterialTypes) {
            $data = $this->findAll(array(
					'select'=>'id, name,code',
					'order'=>'id asc'
                ));
            foreach ($data as $one) {
                //$MaterialTypes[$one->id] = $one->name;
				$MaterialTypes[$one->id] = array(
						'id' => $one->id,
						'name' => $one->name,
						'code' => $one->code
					);
            }
            Yii::app()->memcache->set($cache_name, $MaterialTypes, 300);
        }
        return $MaterialTypes;
        
    }
	
}