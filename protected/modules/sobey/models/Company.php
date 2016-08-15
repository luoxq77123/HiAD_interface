<?php

class Company extends CActiveRecord {
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
        return '{{company}}';
    }

	public function relations() {
        return array(
            'user' => array(self::HAS_ONE, 'User', 'com_id')
        );
    }
    public function rules() {
        return array(
            array('name', 'required', 'message' => '{attribute}不能为空')
        );
    }
    public function getComById($com_id){
        $cache_name = md5('model_Company_getComById' . $com_id);

        $company = Yii::app()->memcache->get($cache_name);
        if (!$company) {
            $company_data = $this->findByPk($com_id);
            $company = array(
                'id' => $company_data->id,
                'name' => $company_data->name,
                'status' => $company_data->status,
                'super_uid' => $company_data->super_uid
            );
            Yii::app()->memcache->set($cache_name, $company, 300);
        }
        return $company;
    }
 public function attributeLabels() {
        return array(
            'name' => '公司名称',
            'type' => '类型',
            'icon' => '企业图标',
            'contact_name' => '联系人名称',
            'phone' => '电话',
            'com_id' => '',
            'com_key' => '',
            'description' => '描述'
        );
    }
    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_clientCompany_getCom_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }
    public function getCom($com_id) {
        $cache_name = md5('model_clientCompany_getCom_'.$com_id);
        $com = Yii::app()->memcache->get($cache_name);
        if (!$com) {
            $data = $this->findAll(array(
                'select'=>'id,name,description,createtime,type',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id)
            ));
            $com = array();
            foreach ($data as $one) {
                $com[$one->id] = array(
                    'id' => $one->id,
                    'name' => $one->name,
                    'description' => $one->description,
                    'createtime' => $one->createtime,
                    'type' => $one->type
                );
            }
            Yii::app()->memcache->set($cache_name, $com, 300);
        }
    }
    
	public function getStatus() {
        return array(
            '-1' => '删除',
            '0' => '禁用',
            '1' => '启用',
        );
    }

    public function setZt($id,$zt){
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $id);
        //$criteria->addColumnCondition(array('com_id' => Yii::app()->session['user']['com_id']));
        return $this->updateAll(array('status' => $zt), $criteria);
    }
}