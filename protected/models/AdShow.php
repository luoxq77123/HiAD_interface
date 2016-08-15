<?php

class AdShow extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{ad_show}}';
    }

	public function attributeLabels() {
        return array(
            'name' => '显示状态',
        );
    }
	
	public function getList(){
		$cache_name = md5('model_AdStatus_getList');
        $list = Yii::app()->memcache->get($cache_name);
        if (!$list) {
            $list = array();
            $data = $this->findAll();
            foreach ($data as $one) {
                $list[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $list, 30000);
        }
        return $list;
	}
	
}