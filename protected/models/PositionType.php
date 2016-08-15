<?php

class PositionType extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{position_type}}';
    }
	
	public function rules() {
        return array(
        );
    }
	
}