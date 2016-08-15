<?php

/**
 * 站点广告统计动作。
 */
class SiteAction extends CAction {
    public function run() {
        $set = $this->initParams();
        // 统计
        $statistics = $this->getStatisticsInfo();
        $set['statistics'] = $statistics;
        $type = isset($_GET['type'])&&$_GET['type']!=""? $_GET['type'] : "ad";
        $arrType = SiteStatistics::model()->getStatTypeName();
        $set['arrType'] = $arrType;
        $set['typeName'] = $arrType[$type];
        
        $controller = $this->getController();
        $controller->renderPartial('site', $set);
    }

    // 初始化广告参数
    public function initParams() {
        // 搜索信息
        $search = array();
        $search['timing'] = isset($_GET['timing']) ? $_GET['timing'] : "t2";
        $search['ad_id'] = isset($_GET['ad_id']) ? $_GET['ad_id'] : "";
        $search['time_period'] = isset($_GET['time_period']) ? $_GET['time_period'] : "";
        $search['ad_name'] = isset($_GET['ad_name']) ? urldecode($_GET['ad_name']) : "";
        $search['type'] = isset($_GET['type']) ? $_GET['type'] : "";

        $params = array();
        $params['search'] = $search;
        // 统计时间选择参数
        $params['timePeriods'] = SiteStatistics::model()->timePeriodsParams();

        return $params;
    }

    // 组合广告统计数据
    public function getStatisticsInfo() {
        $params = SiteStatistics::model()->parseParams();
        $return = array();
        if (empty($params['typeid']) && !empty($_GET['ad_name'])) {
            $return = $this->combineData(array(), $params['typeField'], $params['startDate'], $params['endDate']);
            return  $return;
        }
        // 根据条件获取统计数据
        $statisticsData = SiteStatistics::model()->getStatisticsByDate($params['type'], $params['typeid'], $params['startDate'], $params['endDate']);
        // 缓存数据方便excel导出和查询统计数据列表
        $return = $this->combineData($statisticsData, $params['typeField'], $params['startDate'], $params['endDate']);
        return  $return;
    }
    
    public function combineData($statisticsData = array(), $type = "ad_id", $startDate = "", $endDate = "") {
        // 时间点统计数据
        $timingShow = array();
        $timingClick = array();
        // 获得需要方式的id列表数据
        $isOneDay = 1;
        $tickInterval = 1 * 3600 * 1000;

        // 生成 时间点-数据二维矩阵 用于绘制统计线图
        $strShowData = array();
        $strClickData = array();
        $strCostData = array();
        // 按小时计算
        if ($startDate == $endDate) {
            $statChart = $statisticsData['statChart'];
            for ($i = 0; $i < 24; $i++) {
                $timing = SiteStatistics::model()->mstrToTime($startDate . " " . sprintf("%02d", $i+8) . ":00:00");
                $tempShow = isset($statChart[$i]) ? $statChart[$i]['show_num'] : 0;
                $tempClick = isset($statChart[$i]) ? $statChart[$i]['click_num'] : 0;
                $strShowData[$i]['time'] = $timing;
                $strShowData[$i]['num'] = $tempShow;
                $strClickData[$i]['time'] = $timing;
                $strClickData[$i]['num'] = $tempClick;
            }
        } else { // 按天计算
            $unixSTime = SiteStatistics::model()->mstrToTime($startDate . " 01:00:00");
            $unixETime = SiteStatistics::model()->mstrToTime($endDate . " 01:00:00");
            $index = 0;
            $statChart = $statisticsData['statChart'];
            for ($i = $unixSTime; $i <= $unixETime; $i+=86400) {
                $date = date("Y-m-d", $i);
                // 08:00:00 是由于highcharts绘图格林威治时间 需+8
                $timing = SiteStatistics::model()->mstrToTime($date . " 08:00:00");
                $tempShow = isset($statChart[$date]) ? $statChart[$date]['show_num'] : 0;
                $tempClick = isset($statChart[$date]) ? $statChart[$date]['click_num'] : 0;
                $strShowData[$index]['time'] = $timing;
                $strShowData[$index]['num'] = $tempShow;
                $strClickData[$index]['time'] = $timing;
                $strClickData[$index]['num'] = $tempClick;
                $index ++;
            }
            $isOneDay = 0;
            // 设置highcharts x axis 时间间隔
            $tickInterval = ($index>7)? ($index/6)*24*3600*1000 : $index*24*3600*1000; 
        }
        $return = empty($statisticsData)? $this->_initCombineData() : $statisticsData['statTotal'];
        $return['strShow'] = json_encode($strShowData);
        $return['strClick'] = json_encode($strClickData);
        $return['isOneDay'] = $isOneDay;
        $return['tickInterval'] = $tickInterval;
        unset($statisticsData);
        return $return;
    }
    
    private function _initCombineData(){
        $return = array();
        $return['totalShow'] = 0;
        $return['totalClick'] = 0;
        $return['totalCtr'] = "-";
        $return['totalCpdCost'] = 0;
        $return['totalCpmCost'] = 0;
        $return['totalCpcCost'] = 0;
        $return['totalCost'] = 0;
        return $return;
    }
}