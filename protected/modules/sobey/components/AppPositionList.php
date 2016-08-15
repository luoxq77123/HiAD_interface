<?php

class AppPositionList extends CWidget {

    public function run() {
        $user = Yii::app()->session['user'];
        
        $criteria = new CDbCriteria();
        $criteria->order = 'sort asc,createtime desc';
        $criteria->select = 'Position.id as id,Position.name as name,Position.position_size as position_size,Position.status as status,app_id';
        $criteria->addColumnCondition(array('com_id' =>  $user['com_id']));
        
        $criteria->addColumnCondition(array('ad_type_id' => 2));
        // 附加搜索条件
        if(isset($_GET['appGroupId']) && $_GET['appGroupId'] != ''){
            $apps = App::model()->findAllByAttributes(array('app_group_id' => $_GET['appGroupId']), 'status = 1');
            $app_ids = $apps ? CHtml::listData($apps, 'id', 'id') : array();
            $criteria->addInCondition('app_id', $app_ids);
        }else if (isset($_GET['appId']) && $_GET['appId'] != '') {
            $criteria->addColumnCondition(array('app_id' =>  $_GET['appId']));
        }
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $criteria->addColumnCondition(array('status' =>  $_GET['status']));
        }
        if (isset($_GET['type']) && $_GET['type'] != 0) {
            $criteria->addColumnCondition(array('ad_show_id' =>  $_GET['type']));
        }
        if (isset($_GET['size']) && $_GET['size'] != '') {
            $criteria->addColumnCondition(array('position_size' =>  $_GET['size']));
        }
        if (isset($_GET['name']) && $_GET['name'] != '') {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }
        
        // 分页
        $count = AppPosition::model()->with('Position')->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->route = 'appPosition/list';
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);

        $spList = AppPosition::model()->with('Position')->findAll($criteria);
        //print_r($spList);exit;
        // 搜索下拉
        $app = App::model()->getAppsByComId(Yii::app()->session['user']['com_id']);
        $adShows = AdShow::model()->getPositionAdShows(2);
        $usedSize = Position::model()->getUsedSize(Yii::app()->session['user']['com_id']);
        $set = array(
            'spList' => $spList,
            'adShows' => $adShows,
            'pages' => $pager,
            'apps' => $app,
            'usedSize' => $usedSize
        );

        $this->render('appPositionList', $set);
    }
}