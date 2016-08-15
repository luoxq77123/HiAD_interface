<?php

class AppStatistics extends CActiveRecord {

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
        return '{{app_statistics}}';
    }

    public function relations() {
        return array(
            'ad' => array(self::HAS_ONE, 'Ad', 'id')
        );
    }

    /**
     * 获取站点广告统计信息
     * @$type 	统计方式，如广告统计 广告位统计
     * @$typeid 统计方式条件id，如广告id 广告位id
     * @$startDate 开始日期
     * @$endDate 结束日期
     */
    public function getStatisticsByDate($type = 'ad_id', $typeid = array(), $startDate = "", $endDate = "") {
        // 查询一天数据时 显示其24小时详细信息 
        if ($startDate != "" && $startDate == $endDate) {
            return $this->getStatisticsOneDay($type, $typeid, $startDate);
        }
        $user = Yii::app()->session['user'];
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("com_id" => $user['com_id']));
        if (is_array($typeid) && !empty($typeid)) {
            switch ($type) {
                case 'ad_id':
                    $criteria->addInCondition('ad_id', $typeid);
                    break;
                case 'position_id':
                    $criteria->addInCondition('position_id', $typeid);
                    break;
            }
        }
        if ($startDate != "" && $endDate != "") {
            $criteria->addBetweenCondition('dates', $startDate, $endDate);
        } else if ($startDate != "" && $endDate = "") {
            $criteria->addBetweenCondition('dates', $startDate, date("Y-m-d", time()));
        }
        $rData = $this->findAll($criteria);

        return $rData;
    }

    /**
     * 获取站点广告一天统计信息
     * @$type   统计方式，如广告统计 广告位统计
     * @$typeid 统计方式条件id，如广告id 广告位id
     * @$date   当天时间
     */
    public function getStatisticsOneDay($type = 'ad_id', $typeid = array(), $date = "") {
        $user = Yii::app()->session['user'];
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("com_id" => $user['com_id']));
        if (is_array($typeid) && !empty($typeid)) {
            switch ($type) {
                case 'ad_id':
                    $criteria->addInCondition('ad_id', $typeid);
                    break;
                case 'position_id':
                    $criteria->addInCondition('position_id', $typeid);
                    break;
            }
        }
        $startTime = strtotime($date . " 00:00:00");
        $endTime = strtotime($date . " 23:59:59");
        $table = str_replace("-", "", $date);
        $criteria->addBetweenCondition('create_time', $startTime, $endTime);
        $rData = SiteStat::model($table)->findAll($criteria);
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;

        return $rData;
    }
    
    /**
     * 根据广告请求 插入统计分表
     */
    public function addStatDetail($data) {
        // 获取订单id
        $adData = Ad::model()->getOneById($data['ad_id']);
        $data['order_id'] = $adData->order_id;
        $orderData = Orders::model()->getOneById($data['order_id']);
        // 获取客户id
        $data['client_id'] = ($orderData)? $orderData->client_company_id : 0;
        // 获取销售人员id
        $data['seller_id'] = ($orderData)? $orderData->salesman_id : 0;
        // 创建统计表
        $this->createStatTable();
        //创建物料统计表
        AppStatMate::model()->createStatTable();
        // 设置展示或者点击收入
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // 添加统计信息
        $statData = $this->addSubStatData($data);
        // 添加物料统计信息
        $this->addMaterialData($data, $statData);
        return $statData;
    }
    
    /**
     * 插入分表数据
     */
    public function addSubStatData($data) {
        $date = date("Ymd", $data['create_time']);
        // 添加统计信息
        $AppStat = new AppStat($date);
        $AppStat->ad_id = $data['ad_id'];
        $AppStat->position_id = $data['position_id'];
        $AppStat->order_id = $data['order_id'];
        $AppStat->client_id = $data['client_id'];
        $AppStat->seller_id = $data['seller_id'];
        $AppStat->material_ids = $data['material_ids'];
        $AppStat->ip = $data['ip'];
        $AppStat->region_id = $data['region_id'];
        $AppStat->connect_id = $data['connect_id'];
        $AppStat->brand_id = $data['brand_id'];
        $AppStat->platform_id = $data['platform_id'];
        $AppStat->resolution_id = $data['resolution_id'];
        $AppStat->create_time = $data['create_time'];
        $AppStat->is_click = 0;
        $AppStat->click_time = 0;
        $AppStat->cost_mode = $data['cost_mode'];
        $AppStat->cost = $data['cost'];
        $AppStat->info = $data['info'];
        $AppStat->com_id = $data['com_id'];
        $AppStat->save();
        $return = array();
        $return['sid'] = $AppStat->id;
        $return['time'] = $data['create_time'];
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        return $return;
    }
    
    /**
     * 插入物料表数据 这里一个广告对应多个物料 为方便根据物料统计需添加临时物料信息
     */
    public function addMaterialData($data, $statData) {
        $arrMaterial = explode(",", $data['material_ids']);
        if (!empty($arrMaterial)) {
            $date = date("Ymd", $data['create_time']);
            $table = 'hm_appmaterial_'.$date;
            $sql = "insert into $table (material_id, stat_id, com_id, ip, is_click, click_count, cost_mode, cost, create_time) values ";
            foreach($arrMaterial as $mid) {
                $sql .= "(".$mid.", ".$statData['sid'].", ".$data['com_id'].", ".$data['ip'].", 0, 0, ".$data['cost_mode'].", ".$data['cost'].", ".$data['create_time']."),";
            }
            $sql = substr($sql, 0, -1);
            Yii::app()->db_stat_clientmate->createCommand($sql)->execute();
        }
    }

    /**
     * 设置每条展示或者点击收入
     */
    public function getRowCost($costMode, $cost) {
        $cost = ($cost>0)? $cost : 0.00;
        $reCost = 0.00;
        if ($costMode == 2) { // cpm
            // 查询展现计数 每一千次算一次收入
            /*$sql = "select show_count from ad_show_count where ad_id=".$data['ad_id'];
            $result = Yii::app()->db_stat_site->createCommand($sql)->queryRow();
            if (!empty($result)) {
                if ($result['show_count']==999) {
                    $reCost = $cost;
                    $sql = "update ad_show_count set show_count=0 where ad_id=".$data['ad_id'];
                } else {
                    $sql = "update ad_show_count set show_count=show_count+1 where ad_id=".$data['ad_id'];
                }
            } else {
                $sql = "insert into ad_show_count (ad_id, show_count) values (".$data['ad_id'].", 1)";
            }
            Yii::app()->db_stat_site->createCommand($sql)->execute();
            */
            // 暂每次计费按设置的cpm/1000来计算
            $reCost = $cost/1000;
        } else { // cpc, cpd
            $reCost = $cost;
        }
        return $reCost;
    }

    /**
     * 创建统计分表
     */
    public function createStatTable() {
        $date = date("Ymd", time());
        $table = 'app_' . $date;
        $query = "
        CREATE TABLE IF NOT EXISTS `$table` (
          `id` int(10) NOT NULL auto_increment,
          `ad_id` int(10) default NULL COMMENT '广告id',
          `position_id` int(10) default NULL COMMENT '广告位id',
          `order_id` int(10) default '0' COMMENT '订单id',
          `client_id`  int(10) NULL DEFAULT 0 COMMENT '客户id',
          `seller_id` int(10) default '0' COMMENT '销售id',
          `material_ids` varchar(255) default NULL COMMENT '所有展示物料id',
          `ip` int(10) default '0' COMMENT 'ip转换整形',
          `mac` int(10) default '0' COMMENT '客户端mac',
          `region_id` int(10) default '0' COMMENT '地域id',
          `connect_id` int(5) default '0' COMMENT '接入方式id',
          `brand_id` int(5) default '0' COMMENT '品牌类型',
          `platform_id` int(5) default '0' COMMENT '平台类型',
          `resolution_id` int(5) default '0' COMMENT '分辨率',
          `is_click` tinyint(1) default '0' COMMENT '是否点击 1：是  0 不是',
          `create_time` int(11) default NULL COMMENT '广告显示时间',
          `click_time` int(11) default NULL COMMENT '广告点击时间',
          `cost_mode` tinyint(1) default '0' COMMENT '计费模式；1.CPD; 2.CPM; 3.CPC',
          `cost` float(8,2) default '0' COMMENT '计费',
          `info` mediumtext COMMENT '其他信息扩展',
          `com_id` int(11) default NULL,
          UNIQUE KEY `id` (`id`),
          KEY `index` USING BTREE (`ad_id`,`position_id`,`order_id`,`client_id`,`seller_id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        Yii::app()->db_stat_client->createCommand($query)->execute();
    }
    
    public function addClickStatLog($params) {
        $time = time();
        $db_name = date('Ymd', $params['time']);
        $model = AppStat::model($db_name)->findByPk($params['sid']);
        if ($model) {
            $orderId = $model->order_id;
            $costMode = $model->cost_mode;
            $orderCost = $model->cost;
            $model->is_click = 1;
            $model->click_time = time();
            $model->save();
            // 更新物料映射表
            $table = 'hm_sitematerial_'.$db_name;
            $sql = "update $table set is_click=1, click_count=click_count+1, click_time=".$time." where stat_id=".$params['sid']." and create_time=".$params['time'];
            Yii::app()->db_stat_clientmate->createCommand($sql)->execute();
            // 将数据库选择到主库
            CActiveRecord::$db = Yii::app()->db;
            // 更新订单花费
            if ($orderId>0 && $costMode==3 && $orderCost>0) {
                Orders::model()->updateCostById($orderId, $orderCost);
            }
        } else {
            // 将数据库选择到主库
            CActiveRecord::$db = Yii::app()->db;
        }
    }

    // 统计时间选择参数
    public function timePeriodsParams() {
        $time = array();
        $time['t1']['name'] = '今天';
        $time['t1']['start_time'] = $this->mstrToTime(date("Y-m-d 00:00:00", time()));
        $time['t1']['end_time'] = time();

        $time['t2']['name'] = '昨天';
        $time['t2']['start_time'] = $this->mstrToTime(date("Y-m-d 00:00:00", time() - 86400));
        $time['t2']['end_time'] = $this->mstrToTime(date("Y-m-d 23:59:59", time() - 86400));

        $time['t3']['name'] = '上周';
        $time['t3']['start_time'] = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 - 7, date("Y"));
        $time['t3']['end_time'] = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y"));

        $time['t4']['name'] = '前七天';
        $time['t4']['start_time'] = $this->mstrToTime(date("Y-m-d 00:00:00", time() - (86400 * 7)));
        $time['t4']['end_time'] = time();

        $time['t5']['name'] = '本月';
        $time['t5']['start_time'] = $this->mstrToTime(date("Y-m-1 00:00:00", time()));
        $time['t5']['end_time'] = time();

        $time['t6']['name'] = '上月';
        $time['t6']['start_time'] = $this->mstrToTime(date("Y-m-1 00:00:00", strtotime("-1 month", time())));
        $time['t6']['end_time'] = $this->mstrToTime(date("Y-m-t 23:59:59", strtotime("-1 month", time())));

        return $time;
    }

    // 时间字符串转换时间戳
    public function mstrToTime($strTime) {
        $times = explode(" ", $strTime);
        $date = explode("-", $times[0]);
        $time = explode(":", $times[1]);
        $time[2] = isset($time[2]) ? $time[2] : 0;
        return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }

}