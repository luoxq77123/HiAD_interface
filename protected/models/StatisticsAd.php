<?php
class StatisticsAd extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{statistics_ad}}';
    }
    
    public function getShowNumByAid($aid) {
        $cache_name = md5('hiad-interface_StatisticsAd_getShowNumByAid_'.$aid);
        $total = Yii::app()->memcache->get($cache_name);
        if (!$total) {
            $criteria = new CDbCriteria();
            $criteria->select = 'sum(show_num) as show_num';
            $criteria->addColumnCondition(array('ad_id' => $aid));
            $data = $this->find($criteria);
            $total = 0;
            if ($data) {
                $total = $data->show_num;
            }
            Yii::app()->memcache->set($cache_name, $total, 300);
        }
        return $total;
    }

    public function getClickNumByAid($aid) {
        $cache_name = md5('hiad-interface_StatisticsAd_getClickNumByAid_'.$aid);
        $total = Yii::app()->memcache->get($cache_name);
        if (!$total) {
            $criteria = new CDbCriteria();
            $criteria->select = 'sum(click_num) as click_num';
            $criteria->addColumnCondition(array('ad_id' => $aid));
            $data = $this->find($criteria);
            $total = 0;
            if ($data) {
                $total = $data->click_num;
            }
            Yii::app()->memcache->set($cache_name, $total, 300);
        }
        return $total;
    }
}