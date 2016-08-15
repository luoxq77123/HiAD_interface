<?php

class SiteAdUrl extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{site_ad_url}}';
    }

    public function rules() {
        return array(
        );
    }
   public function getDataByUrl($Url = null) {
        $cache_name = md5('model_SiteAdUrl_getDataByUrl_'.$Url);
        $data = Yii::app()->memcache->get($cache_name);
        if (!$data) {
            $data = $this->find('url = :url', array(':url' => $Url));
            Yii::app()->memcache->set($cache_name, $data, 30000);
        }
        return $data;
    }
	public function getOneByUrl($url){
		return $this->find('url=:url', array(':url'=>$url));
	}
	
	public function addOneByUrl($url){
		$this->url = $url;
		return $this->save();
	}

}