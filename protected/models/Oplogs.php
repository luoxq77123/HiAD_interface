<?php

class Oplogs extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{oplog}}';
    }

    public function rules() {
        return array(
            array('aca_id,url,uid,com_id,ip', 'required', 'message' => '{attribute}不能为空', 'on' => 'add')
        );
    }

    function getAcaId($com_id){
		$cache_name = md5('model_Oplogs_getAcaId'.$com_id);
        $acaId = Yii::app()->memcache->get($cache_name);
        if (!$acaId) {
            $data = $this->findAll(array(
					'select'=>'aca_id',
					'condition'=>'com_id=:com_id',
					'order'=>'createtime desc',
					'params'=>array(':com_id' => $com_id)
                ));
            $acaId = CHtml::listData($data, 'aca_id', 'aca_id');
            Yii::app()->memcache->set($cache_name, $acaId, 300);
        }
        return $acaId;
    }

}