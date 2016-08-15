<?php

class SiteGroup extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{site_group}}';
    }

    public function rules() {
        return array(
            array('name,sort', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('description', 'safe', 'on' => 'add,edit'),
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '站点组名',
            'sort' => '显示顺序',
            'description' => '说明'
        );
    }

	function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_siteGroup_getSitegroup_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }

	public function getSitegroup($com_id){
		
		$cache_name = md5('model_siteGroup_getSitegroup_'.$com_id);
        $com = Yii::app()->memcache->get($cache_name);
		if(!$com){
			$data = $this->findAll(array(
				'select'=>'id,name',
				'condition'=>'com_id=:com_id',
				'order'=>'createtime desc',
				'params'=>array(':com_id'=>$com_id)
			));
			$com=array();
			foreach ($data as $one) {
				$com[$one->id] =$one->name;
            }
            Yii::app()->memcache->set($cache_name, $com, 300);
		}
        return $com;
	}
}