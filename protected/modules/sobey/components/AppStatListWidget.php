<?php

class AppStatListWidget extends CWidget {
    
    // 获取内容地址
    public $route;
    // 设置每页条数限制组
    public $arrPageSize;

    public function init() {
        if ($this->route === null)
            $this->route = 'statistics/getAppStatList';
        if ($this->arrPageSize === null)
            $this->arrPageSize = array(10 => 10, 20 => 20, 50 => 50);
    }

    public function run() {
        // 统计
        $set = $this->getStatisticsInfo();
        $type = isset($_GET['type'])&&$_GET['type']!=""? $_GET['type'] : "ad";
        $arrType = SiteStatistics::model()->getStatTypeName();
        $typeName = $arrType[$type];
        $set['type'] = $type;
        $set['typeName'] = $typeName;
        $this->render('siteStatList', $set);
    }
    
    // 组合广告统计数据
    public function getStatisticsInfo() {
        $params = SiteStatistics::model()->parseParams();
        $return = array();
        if (empty($params['typeid']) && !empty($_GET['ad_name'])) {
            $return = SiteStatistics::model()->combineData(array(), $params['type']);
            return  $return;
        }
        // 根据条件获取统计数据
        $statisticsData = AppStatistics::model()->getPageListByType($params['type'], $params['typeid'], $params['startDate'], $params['endDate'], $this->route);
        // 组合数据
        $return = SiteStatistics::model()->combineData($statisticsData, $params['type']);
        return  $return;
    }
}