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
        return $com;
    }

    // 获取所有内容 根据名称 或者 id
    public function getDataByNameOrIds($ids=array(), $name="") {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        // 附加搜索条件
        if (is_array($ids)&&!empty($ids)) {
            $criteria->addInCondition('id',$ids);
        }
        if (isset($name) && $name) {
            $criteria->addSearchCondition('name', $name);
        }
        $data = $this->findAll($criteria);
        return $data;
    }
    public function getOrdersList($com_id) {
        $cache_name = md5('model_order_getOrdersList_'.$com_id);
        $orderName = Yii::app()->memcache->get($cache_name);
        if (!$orderName) {
            $data = $this->findAll(array(
                    'select'=>'id,name,description,type,createtime,status',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
            $orderName = array();
            foreach ($data as $one) {
                $orderName[$one->id]['name'] = $one->name;
                $orderName[$one->id]['description'] = $one->description;$orderName[$one->id]['type'] = $one->type;
                $orderName[$one->id]['createtime'] = $one->createtime;
               
                $orderName[$one->id]['status'] = $one->status;
            }
            Yii::app()->memcache->set($cache_name, $orderName, 300);
        }
        return $orderName;
    }	
    
    public function getType() {
        return array(1 => '广告客户', 2 => '代理机构');
    }
	 public function getStatus() {
        return array(
            '-1' => '删除',
            '0' => '禁用',
            '1' => '启用',
        );
    }
    /**
     * 对表status字段值的改变
     * @id 传上来的id值
     * @zt 传上来的状态值 
     */
    public function setZt($id,$zt){
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $id);
        $criteria->addColumnCondition(array('com_id' => Yii::app()->session['user']['com_id']));
        return $this->updateAll(array('status' => $zt), $criteria);
    }
}