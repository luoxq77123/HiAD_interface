<?php

class AppPosition extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{app_position}}';
    }
    
    public function primaryKey() {
        return 'position_id';
    }

    public function rules() {
        return array(
            //array('position_id', 'required', 'message' => '{attribute}不能为空'),
            array('app_id,idle_take', 'required', 'on' => 'fixed'),
            array('app_id,idle_take', 'required', 'on' => 'player'),
            array('app_id,idle_take', 'required', 'on' => 'pop')
        );
    }
    
    public function attributeLabels() {
        return array(
            'is_full' => '是否全屏',
            'idle_take' => '空闲时固定占位',
            'staytime' => '停留时间',
            'app_id' => '所属应用'
        );
    }
    
    public function getDataByPoistionId($positionId) {
        $data = $this->find(array(
            'condition'=>'position_id=:position_id',
            'params'=>array(':position_id' => $positionId)
        ));
        return $data;
    }

    public function getIsOption(){
        return array(1 => '是', 0 => '否');
    }

    public function getIdleTakeOption(){
        return array(1 => '是', 0 => '否');
    }

    public function relations() {
        return array(
            'Position' => array(self::HAS_ONE, 'Position', 'id')
        );
    }
}