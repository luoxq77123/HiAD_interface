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
            array('name,sort,ad_show_id,position_size', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('site_id,description', 'safe', 'on' => 'add,edit'),
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '广告位名称',
            'sort' => '显示顺序',
            'ad_show_id' => '类型',
            'position_size' => '尺寸',
            'description' => '说明'
        );
    }

    public function relations() {
        return array(
            'AdShow' => array(self::BELONGS_TO, 'AdShow', 'id'),
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
        $data = array();
        $data = $this->find(array(
            'select'=>'id,com_id,ad_type_id,ad_show_id,position_size,name,status',
            'condition'=>'id=:id',
            'params'=>array(':id' => $position_id)
        ));
        return $data;
    }

    public function getPosition($com_id){
        $cache_name = md5('model_Position_getPosition_'.$com_id);
        $position = Yii::app()->memcache->get($cache_name);
        if(!$position){
            $data = $this->findAll(array(
                'select'=>'id,name',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id)
            ));
            $position=array();
            foreach ($data as $one) {
                $position[$one->id] =$one->name;
            }
            Yii::app()->memcache->set($cache_name, $position, 300);
        }
        return $position;
    }

    public function getPositionInfo($com_id){
        $cache_name = md5('model_Position_getPositionInfo_'.$com_id);
        $reData = Yii::app()->memcache->get($cache_name);
        if(!$reData){
            $data = $this->findAll(array(
                //'select'=>'id,name',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id)
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
    public function getDataByNameOrIds($ids=array(), $name=""){
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
    
    /**
     * 获得广告列表和分页信息
     */
    public function getPagerList($adIds=array()){
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));

        // 附加搜索条件
        if (isset($_GET['status']) && $_GET['status'] != 'all') {
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        }

        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }
        
        if (is_array($adIds) && !empty($adIds)) {
            $criteria->addInCondition('id', $adIds);
        }

        // 分页
        $count = $this->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $adlist = $this->findAll($criteria);
        
        $data = array();
        $data['list'] = $adlist;
        $data['pager'] = $pager;
        
        return $data;
    }

    /**
     * 根据条件获取广告位列表
     */
    public function getPositionList($search=array(), $initPageSize=3, $adTypeId=1){
        $user = Yii::app()->session['user'];
        $positionType = 'site_id';
        if ($adTypeId==2) {
            $positionType = 'app_id';
        }
        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'Position.id as id,Position.name as name,Position.position_size as position_size,Position.status as status,'.$positionType;
        $criteria->addColumnCondition(array('com_id' =>  $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' =>  $adTypeId));
        // 附加搜索条件
        if(isset($search['siteGroupId']) && $search['siteGroupId'] != ''){
            $sites = Site::model()->findAllByAttributes(array('site_group_id' => $search['siteGroupId']), 'status = 1');
            $site_ids = CHtml::listData($sites, 'id', 'id');
            $criteria->addInCondition($positionType, $site_ids);
        }else if (isset($search['siteId']) && $search['siteId'] != '') {
            $criteria->addColumnCondition(array($positionType =>  $search['siteId']));
        }
        if (isset($search['status']) && $search['status'] != '') {
            $criteria->addColumnCondition(array('status' =>  $search['status']));
        }
        if (isset($search['type']) && $search['type'] != 0) {
            $criteria->addColumnCondition(array('ad_show_id' =>  $search['type']));
        }
        if (isset($search['size']) && $search['size'] != '') {
            $criteria->addColumnCondition(array('position_size' =>  $search['size']));
        }
        if (isset($search['name']) && $search['name'] != '') {
            $criteria->addSearchCondition('name', urldecode($search['name']));
        }

        // 分页
        $count = 0;
        if ($adTypeId == 1) {
            $count = SitePosition::model()->with('Position')->count($criteria);
        } else if ($adTypeId == 2) {
            $count = AppPosition::model()->with('Position')->count($criteria);
        }
        $pageSize = (isset($search['pagesize']) && $search['pagesize']) ? $search['pagesize'] : $initPageSize;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);

        $spList = array();
        if ($adTypeId == 1) {
            $pager->route = 'ad/setAd';
            $spList = SitePosition::model()->with('Position')->findAll($criteria);
        } else if ($adTypeId == 2) {
            $pager->route = 'appAd/setAd';
            $spList = AppPosition::model()->with('Position')->findAll($criteria);
        }

        $data = array();
        $data['pager'] = $pager;
        $data['list'] = $spList;

        return $data;
    }

}