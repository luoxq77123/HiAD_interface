<?php

class SitePositionMenu extends CWidget {

    public function run() {
        $siteGroupsData = SiteGroup::model()->findAll(array(
            'select' => 'id, name',
            'condition' => 'com_id=:com_id and status = 1',
            'order' => 'sort asc, createtime desc',
            'params' => array(':com_id' => Yii::app()->session['user']['com_id'])));
        $sitesData = Site::model()->findAll(array(
            'select' => 'id, name, site_group_id',
            'condition' => 'com_id = :com_id and status = 1',
            'order' => 'sort asc, createtime desc',
            'params' => array(':com_id' => Yii::app()->session['user']['com_id'])));

        $route = isset($_GET['a']) && $_GET['a'] == 'table' ? 'sitePosition/table' : 'sitePosition/list';
        $siteGroups = array();
        foreach($siteGroupsData as $one){
            $siteGroups[$one->id] = array(
                'id' => $one->id, 
                'name' => $one->name,
                'url' => Yii::app()->createUrl($route, array('siteGroupId' => $one->id)));
        }
        
        $has_group_sites = array();
        $none_group_sites = array();
        foreach($sitesData as $one){
            $tmp_array = array(
                'id' => $one->id,
                'name' => $one->name,
                'site_group_id' => $one->site_group_id,
                'url' => Yii::app()->createUrl($route, array('siteId' => $one->id)));
            if($one->site_group_id && isset($siteGroups[$one->site_group_id]))
                $has_group_sites[$one->site_group_id][] = $tmp_array;
            else
                $none_group_sites[] = $tmp_array;
        }
        
        
        $set = array(
			'route' => $route,
            'siteGroups' => $siteGroups,
            'hasGroupSites' => $has_group_sites,
            'noneGroupSites' => $none_group_sites
        );
        
        $this->render('sitePositionMenu', $set);
    }

}