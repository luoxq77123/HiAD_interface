<?php

class ScConnect extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{sc_connect}}';
    }

    public function rules() {
        return array(
        );
    }

    public function deleteByAdId($adId){
		return $this->deleteAll('ad_id=:ad_id', array(':ad_id'=>$adId));
	}

}