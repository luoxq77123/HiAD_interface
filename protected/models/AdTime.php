<?php

class AdTime extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{ad_time}}';
    }
    
    public function getAdIdByCondition($positionId, $time) {
        $criteria1 = new CDbCriteria();
        $criteria1->select = 'ad_id';
        $criteria1->addCondition('end_time>='.$time);
        $criteria1->addCondition('end_time=0', 'OR');
        $criteria1->addColumnCondition(array('position_id' => $positionId));
        $criteria1->addCondition('start_time<='.$time);
        $data = $this->findAll($criteria1);
        $arrAid = array();
        if ($data) {
            foreach($data as $one) {
                $arrAid[] = $one->ad_id;
            }
        }
        return $arrAid;
    }
    
    // 周列表
    public function weekList(){
        $week = array(
            '1' => '星期一',
            '2' => '星期二',
            '3' => '星期三',
            '4' => '星期四',
            '5' => '星期五',
            '6' => '星期六',
            '7' => '星期日',
        );
        return $week;
    }
    
     public function deleteByAdId($adId){	
        return $this->deleteAll('ad_id=:ad_id', array(':ad_id'=>$adId));
    }
    
    // 时间字符串转换时间戳
    public function mstrToTime($strTime){
        $times = explode(" ", $strTime);
        $date = explode("-", $times[0]);
        $time = explode(":", $times[1]);
        $time[2] = isset($time[2])? $time[2] : 0;
        return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }
}