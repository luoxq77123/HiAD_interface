<?php

class AreaIp extends CActiveRecord {



    public function __construct() {
        CActiveRecord::$db = Yii::app()->db_ip;       
    }

    public function getDataByIp($ip = null) {
        $cache_name = md5('model_AreaIp_getDataByIp_'.$ip);
        $data = Yii::app()->memcache->get($cache_name);
        if (!$data) {
            $data = $this->find('start_ip <= :start_ip and end_ip >= :end_ip', array(':start_ip' => $ip, ':end_ip' => $ip));
            Yii::app()->memcache->set($cache_name, $data, 30000);
        }
        return $data;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{area_ip}}';
    }

    public function rules() {
        return array(
        );
    }

    public function deleteByAdId($adId){	
		return $this->deleteAll('ad_id=:ad_id', array(':ad_id'=>$adId));
	}

}