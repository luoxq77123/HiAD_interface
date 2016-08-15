<?php

class Position extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{position}}';
    }

    public function rules() {
        return array(
            array('name,sort,ad_type_id,position_size', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('site_id,description', 'safe', 'on' => 'add,edit'),
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '广告位名称',
            'sort' => '显示顺序',
            'ad_type_id' => '类型',
            'position_size' => '尺寸',
            'description' => '说明'
        );
    }

    public function relations() {
        return array(
            'AdType' => array(self::BELONGS_TO, 'AdType', 'id'),
            'AdTime' => array(self::HAS_MANY, 'AdTime', 'position_id'),
            'Schedule' => array(self::HAS_MANY, 'Schedule', 'position_id')
        );
    }
    
    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_Position_getUsedSize_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);

        $getByIds_cache = md5('model_Position_getPosition_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);

        $getByIds_cache = md5('model_Position_getPositionInfo_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }

    public function getUsedSize($com_id){
        $cache_name = md5('model_Position_getUsedSize_'.$com_id);
        $usedSize = Yii::app()->memcache->get($cache_name);
        if (!$usedSize) {
            $data = $this->findAll(array(
                    'select'=>'position_size',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
            $usedSize = CHtml::listData($data, 'position_size', 'position_size');
            Yii::app()->memcache->set($cache_name, $usedSize, 300);
        }
        return $usedSize;
    }
    
    public function getPositionById($position_id) {
        $data = $this->find(array(
            'select'=>'id,com_id,ad_type_id,ad_show_id,status',
            'condition'=>'id=:id',
            'params'=>array(':id' => $position_id)
        ));
        return $data;
    }

    public function getPosition($com_id){
        $cache_name = md5('hiad-interface_model_Position_getPosition_'.$com_id);
        $position = Yii::app()->memcache->get($cache_name);
        if(!$position){
            $data = $this->findAll(array(
                'select'=>'id,name',
                'condition'=>'com_id=:com_id and status=:status',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id, ':status'=>1)
            ));
            $position=array();
            if (!empty($data)) {
                foreach ($data as $one) {
                    $position[$one->id] =$one->name;
                }
            }
            Yii::app()->memcache->set($cache_name, $position, 300);
        }
        return $position;
    }

    public function getPositionInfo($com_id){
        $cache_name = md5('model_Position_getPositionInfo_'.$com_id);
        $position = Yii::app()->memcache->get($cache_name);
        if(!$position){
            $data = $this->findAll(array(
                //'select'=>'id,name',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id)
            ));
            $position=array();
            foreach ($data as $one) {
                $position[$one->id] =$one;
            }
            Yii::app()->memcache->set($cache_name, $position, 300);
        }
        return $position;
    }
    
    /**
     * 客户端显示类型转换成对应的接口输出值
     * fixed    4 -> 1
     * pop      5 -> 2
     * player   7 -> 3
     */
    public function showTypeConvert($showTypeId) {
        $data = array(
            '4' => array('id' => 1, 'code' => 'fixed'),//固定
            '5' => array('id' => 2, 'code' => 'pop'),//插播
            '7' => array('id' => 3, 'code' => 'player')//播放器
        );
        return $data[$showTypeId];
    }
    
    /**
     * 根据条件获取广告位列表
     */
    public function getPositionList($search=array(), $initPageSize=3){
        $user = Yii::app()->session['user'];
        
        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'Position.id as id,Position.name as name,Position.position_size as position_size,Position.status as status,site_id';
        $criteria->addColumnCondition(array('com_id' =>  $user['com_id']));
        // 附加搜索条件
        if(isset($search['siteGroupId']) && $search['siteGroupId'] != ''){
            $sites = Site::model()->findAllByAttributes(array('site_group_id' => $search['siteGroupId']), 'status = 1');
            $site_ids = CHtml::listData($sites, 'id', 'id');
            $criteria->addInCondition('site_id', $site_ids);
        }else if (isset($search['siteId']) && $search['siteId'] != '') {
            $criteria->addColumnCondition(array('site_id' =>  $search['siteId']));
        }
        if (isset($search['status']) && $search['status'] != '') {
            $criteria->addColumnCondition(array('status' =>  $search['status']));
        }
        if (isset($search['type']) && $search['type'] != 0) {
            $criteria->addColumnCondition(array('ad_type_id' =>  $search['type']));
        }
        if (isset($search['size']) && $search['size'] != '') {
            $criteria->addColumnCondition(array('position_size' =>  $search['size']));
        }
        if (isset($search['name']) && $search['name'] != '') {
            $criteria->addSearchCondition('name', urldecode($search['name']));
        }
        
        // 分页
        $count = SitePosition::model()->with('Position')->count($criteria);
        $pageSize = (isset($search['pagesize']) && $search['pagesize']) ? $search['pagesize'] : $initPageSize;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $pager->route = 'ad/setAd';

        $spList = SitePosition::model()->with('Position')->findAll($criteria);
        
        $data = array();
        $data['pager'] = $pager;
        $data['list'] = $spList;
        
        return $data;
    }

}