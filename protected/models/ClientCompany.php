<?php

class ClientCompany extends CActiveRecord {

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
        return '{{client_company}}';
    }
	
	 public function rules() {
        return array(
            array('name', 'required', 'message' => '{attribute}不能为空'),
            array('type, description', 'safe', 'on' => 'add,edit')
        );
    }
	
	public function attributeLabels() {
        return array(
            'name' => '公司名称',
            'type' => '类型',
            'description' => '说明 ',
        );
    }
	function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_clientCompany_getCom_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }
	public function getCom($com_id){
		
		$cache_name = md5('model_clientCompany_getCom_'.$com_id);
        $com = Yii::app()->memcache->get($cache_name);
        if (!$com) {
			$data = $this->findAll(array(
				'select'=>'id,name,description',
				'condition'=>'com_id=:com_id',
				'order'=>'createtime desc',
				'params'=>array(':com_id'=>$com_id)
			));
			 $com = array();
			foreach ($data as $one) {
                $com[$one->id] = array(
                    'id' => $one->id,
                    'name' => $one->name,
                    'description' => $one->description
                );
            }
            Yii::app()->memcache->set($cache_name, $com, 300);
		}
        return $com;
	}
}