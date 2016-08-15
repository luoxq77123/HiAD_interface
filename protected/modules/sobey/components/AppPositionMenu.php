<?php

class AppPositionMenu extends CWidget {

    public function run() {
        $appGroupsData = AppGroup::model()->findAll(array(
            'select' => 'id, name',
            'condition' => 'com_id=:com_id and status = 1',
            'order' => 'sort asc, createtime desc',
            'params' => array(':com_id' => Yii::app()->session['user']['com_id'])));
        $appsData = App::model()->findAll(array(
            'select' => 'id, name, app_group_id',
            'condition' => 'com_id = :com_id and status = 1',
            'order' => 'sort asc, createtime desc',
            'params' => array(':com_id' => Yii::app()->session['user']['com_id'])));

        $route = isset($_GET['a']) && $_GET['a'] == 'table' ? 'appPosition/table' : 'appPosition/list';
        $appGroups = array();
        foreach($appGroupsData as $one){
            $appGroups[$one->id] = array(
                'id' => $one->id, 
                'name' => $one->name,
                'url' => Yii::app()->createUrl($route, array('appGroupId' => $one->id)));
        }
        
        $has_group_apps = array();
        $none_group_apps = array();
        foreach($appsData as $one){
            $tmp_array = array(
                'id' => $one->id,
                'name' => $one->name,
                'app_group_id' => $one->app_group_id,
                'url' => Yii::app()->createUrl($route, array('appId' => $one->id)));
            if($one->app_group_id && isset($appGroups[$one->app_group_id]))
                $has_group_apps[$one->app_group_id][] = $tmp_array;
            else
                $none_group_apps[] = $tmp_array;
        }
        
        
        $set = array(
			'route' => $route,
            'appGroups' => $appGroups,
            'hasGroupApps' => $has_group_apps,
            'noneGroupApps' => $none_group_apps
        );
        
        $this->render('appPositionMenu', $set);
    }

}