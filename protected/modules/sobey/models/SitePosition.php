<?php

class SitePosition extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{site_position}}';
    }
    
    public function primaryKey() {
        return 'position_id';
    }

    public function rules() {
        return array(
            array('site_id,idle_take', 'required', 'on' => 'fixed'),
            array('site_id,float_x,float_y,space_x,space_y,staytime,scroll', 'required', 'on' => 'float'),
            array('site_id,float_x,float_y,space_x,space_y,staytime,poptime', 'required', 'on' => 'pop'),
            array('site_id', 'required', 'on' => 'player')
        );
    }
    
    public function attributeLabels() {
        return array(
            'space_x' => '边距',
            'space_y' => '边距',
            'idle_take' => '空闲时固定占位',
            'poptime' => '弹出时间',
            'staytime' => '停留时间',
            'scroll' => '跟随滚动条',
            'site_id' => '所属站点',
        );
    }
    
    public function getFloatXOption(){
        return array(1 => '居左', 2 => '居右');
    }
    
    public function getFloatYOption(){
        return array(1 => '居上', 2 => '居下');
    }
    
    public function getPopTimeOption(){
        return array(0 => '进入时弹出', -1 => '退出时弹出');
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