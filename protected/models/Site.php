<?php

class Site extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{site}}';
    }

    public function rules() {
        return array(
            array('name,sort', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
			array('sort', 'numerical', 'integerOnly'=>true),
            array('description,site_group_id','safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '站点名称',
            'sort' => '显示顺序',
            'description' => '说明',
            'site_group_id' => '站点分组'
        );
    }
    
    public function getSitesByComId($com_id){
		$cache_name = md5('model_Site_getSitesByComId'.$com_id);
        $sites = Yii::app()->memcache->get($cache_name);
        if (!$sites) {
            $data = $this->findAll(array(
					'select'=>'id,name',
					'condition'=>'com_id=:com_id',
					'order'=>'sort asc',
					'params'=>array(':com_id' => $com_id)
                ));
            foreach ($data as $one) {
                $sites[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $sites, 300);
        }
        return $sites;
    }

    public function relations() {
        return array(
            'SiteGroup' => array(self::BELONGS_TO, 'SiteGroup', 'site_group_id')
        );
    }

}