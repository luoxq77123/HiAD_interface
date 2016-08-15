<?php

class MaterialListWidget extends CWidget {
    // 获取内容地址
    public $rote;
    // 设置每页条数限制组
    public $arrPageSize;
    // 广告类型
    public $adTypeId;
    // 广告显示类型 如果不是播放器广告 不能选择视频物料
    public $adShow;
    // 请求方式是否是ajax
    public $ajaxRequest;
    //保存投放第一步与第二步的信息
    //public $adPosition;
    
    public function init()
    {
        if($this->rote===null)
            $this->rote = 'ad/getMaterialList';
        if($this->arrPageSize===null)
            $this->arrPageSize = array(3 => 3, 10 => 10, 20 => 20);
        if($this->adTypeId===null)
            $this->adTypeId = 1;
        if($this->adShow===null)
            $this->adShow = '';
        if ($this->ajaxRequest === null)
            $this->ajaxRequest = 1;
        //if($this->adPosition===null)
            //$this->adPosition = $material['adPosition'];
    }
    
    
    public function run() {
        $user = Yii::app()->session['user'];
        //$adPosition = $this->adPosition;
        $_GET['ajaxRequest'] = $this->ajaxRequest;
        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'id,name,material_type_id,status,material_size';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' => $this->adTypeId));
        $criteria->addColumnCondition(array('status' => 1));
        // 广告显示类型 如果不是播放器广告 不能选择视频物料
        /*if ($this->adShow == '') {
            //$criteria->addNotInCondition('material_type_id', array(5));
        }*/
        // 附加搜索条件
        if (isset($_GET['type']) && $_GET['type']) {
            $criteria->condition .= ' and material_type_id =:type';
            $criteria->params['type'] = $_GET['type'];
        }

        if (isset($_GET['size']) && $_GET['size']) {
            $criteria->condition .= ' and material_size =:size';
            $criteria->params['size'] = $_GET['size'];
        }

        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->condition .= ' and name like :name';
            $criteria->params['name'] = '%' . urldecode($_GET['name']) . '%';
        }

        $count = Material::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 3;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $pager->route = $this->rote;
        $materiallist = Material::model()->findAll($criteria);
        
        $materialTypes = array();
        if ($this->adTypeId == 1) {
            $materialTypes = MaterialType::model()->getMaterialTypes();
        } else {
            $materialTypes = MaterialAtype::model()->getMaterialTypes();
        }
        $materialType = array(0 => '-请选择-') + @CHtml::listData($materialTypes, 'id', 'name');
        $usedSize = Material::model()->getUsedSize(Yii::app()->session['user']['com_id']);
        $usedSize = array('' => '-请选择-') + $usedSize;
        $status=array(1=>'启用',-1=>'禁用');
        //var_dump($MaterialType);exit;
        $setArray = array(
            'materiallist' => $materiallist,
            'pages' => $pager,
            'materialType' => $materialType,
            'usedSize' => $usedSize,
            'status' => $status,
            //'adPosition' => $adPosition,
            'adType' => $this->adTypeId,
            'asset_url' => Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('application.modules.sobey.assets'))
        );

        $this->render('materialList', $setArray);
    }
}