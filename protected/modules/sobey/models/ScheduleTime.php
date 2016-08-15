<?php

class ScheduleTime extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{schedule_time}}';
    }

    public function getRowsByScheduleId($scheduleid){
        $data = $this->findAll(array('condition' => 'schedule_id=:schedule_id', 'params'=>array(':schedule_id'=>$scheduleid)));
        return $data;
    }
    
    public function getRowsByArrScheduleId($arrScheduleid){
        $criteria = new CDbCriteria();
        $criteria->addInCondition('schedule_id',$arrScheduleid);
        $data = $this->findAll($criteria);
        return $data;
    }
}