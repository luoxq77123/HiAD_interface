<?php

class Material extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material}}';
    }
    
    public function rules() {
        return array(
            array('name,material_type_id', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '物料名称:',
            'material_type_id' => '类型:'
        );
    }

    public function getUsedSize($com_id){
        $cache_name = md5('model_Material_getUsedSize'.$com_id);
        $usedSize = Yii::app()->memcache->get($cache_name);
        if (!$usedSize) {
            $data = $this->findAll(array(
                    'select'=>'material_size',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
            $usedSize = CHtml::listData($data, 'material_size', 'material_size');
            Yii::app()->memcache->set($cache_name, $usedSize, 300);
        }
        return $usedSize;
    }
    
    public function getAll($com_id){
        $cache_name = md5('model_Material_getAll_'.$com_id);
        $reData = Yii::app()->memcache->get($cache_name);
        if (!$reData) {
            $data = $this->findAll(array(
                //'select'=>'material_size',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id' => $com_id)
            ));
            if (!empty($data)) {
                foreach ($data as $one) {
                    $reData[$one->id] = $one;
                }
            }
            Yii::app()->memcache->set($cache_name, $reData, 300);
        }
        return $reData;
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

    // get ad material rotate mode
    public function getRotate(){
        $list = array(
            '1' => '均匀',
            '2' => '手动权重',
            '3' => '幻灯片轮换'
        );
        return $list;
    }
    
    //根据广告下面的物料ID查找物料的类型、名字
    public function getByMaterial($str) {
        $criteria = new CDbCriteria();
        $criteria->select = 'id,name,material_type_id';
        $criteria->addInCondition('id' , $str);
        $material = $this->findAll($criteria);
        $return = array();
        if (!empty($material)) {
            foreach($material as $val) {
                $return[$val->id] = $val;
            }
        }
        return $return;
    }
}