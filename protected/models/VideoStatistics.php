<?php

class VideoStatistics extends CActiveRecord {
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
        return '{{site_statistics}}';
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
        // 设置展示或者点击收入
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // 添加统计信息
        $statData = $this->addSubStatData($data);
        // 添加物料统计信息
        VideoStatMate::model()->createStatTable();
        $this->addMaterialData($data, $statData);
        // 如果广告计费模式是展现且订单存在 则更新订单话费
        if ($data['cost_mode']==2 && $data['order_id']>0 && $data['cost']>0) {
            Orders::model()->updateCostById($data['order_id'], $data['cost']);
        }
        return $statData;
    }
    
    /**
     * 添加分表统计 针对HimiController广告获取统计，不做物料统计，等物料筛选后再作统计
     */
    public function addStatDetailForSite($data) {
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
        // 设置展示或者点击收入
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // 添加统计信息
        $statData = $this->addSubStatData($data);
        // 如果广告计费模式是展现且订单存在 则更新订单话费
        if ($data['cost_mode']==2 && $data['order_id']>0 && $data['cost']>0) {
            Orders::model()->updateCostById($data['order_id'], $data['cost']);
        }
        unset($data);
        return $statData;
    }
    
    /**
     * 添加物料分表统计 针对HimiController广告获取统计
     */
    public function addMaterialStatForSite($data, $statData) {
        // 设置展示或者点击收入
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // 添加物料统计信息
        $this->addMaterialData($data, $statData);
    }
    
    /**
     * 插入分表数据
     */
    public function addSubStatData($data) {
        $date = date("Ymd", $data['create_time']);
        // 添加统计信息
        $AppStat = new VideoStat($date);
        $AppStat->ad_id = $data['ad_id'];
        $AppStat->position_id = $data['position_id'];
        $AppStat->order_id = $data['order_id'];
        $AppStat->client_id = $data['client_id'];
        $AppStat->seller_id = $data['seller_id'];
        $AppStat->material_ids = $data['material_ids'];
        $AppStat->ip = $data['ip'];
        $AppStat->region_id = $data['region_id'];
        $AppStat->connect_id = $data['connect_id'];
        $AppStat->browser_id = $data['browser_id'];
        $AppStat->language_id = $data['language_id'];
        $AppStat->system_id = $data['system_id'];
        $AppStat->resolution_id = $data['resolution_id'];
        $AppStat->referer_id = $data['referer_id'];
        $AppStat->accessurl_id = $data['accessurl_id'];
        $AppStat->is_click = 0;
        $AppStat->create_time = $data['create_time'];
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
        if ($data['material_ids']) {
            $arrMaterial = explode(",", $data['material_ids']);
            if (!empty($arrMaterial)) {
                $date = date("Ymd", $data['create_time']);
                $table = 'hm_videomaterial_'.$date;
                $sql = "insert into $table (material_id, stat_id, com_id, ip, is_click, click_count, cost_mode, cost, create_time) values ";
                foreach($arrMaterial as $mid) {
                    $sql .= "(".$mid.", ".$statData['sid'].", ".$data['com_id'].", ".$data['ip'].", 0, 0, ".$data['cost_mode'].", ".$data['cost'].", ".$data['create_time']."),";
                }
                $sql = substr($sql, 0, -1);
                Yii::app()->db_stat_videomate->createCommand($sql)->execute();
            }
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
        } else { // cpc cpd
            $reCost = $cost;
        }
        return $reCost;
    }

    /**
     * 创建统计分表
     */
    public function createStatTable() {
        $date = date("Ymd", time());
        $table = 'video_' . $date;
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
          `region_id` int(10) default '0' COMMENT '地域id',
          `connect_id` int(5) default '0' COMMENT '接入方式id',
          `browser_id` int(5) default '0' COMMENT '浏览器类型',
          `language_id` int(5) default '0' COMMENT '浏览器语言',
          `system_id` int(5) default '0' COMMENT '系统',
          `resolution_id` int(5) default '0' COMMENT '分辨率',
          `referer_id` int(10) default '0' COMMENT '来源域',
          `accessurl_id` int(10) default '0' COMMENT '被访url',
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
        Yii::app()->db_stat_site->createCommand($query)->execute();
    }
    
    public function addClickStatLog($params) {
        $time = time();
        $db_name = date('Ymd', $params['time']);
        $model = SiteStat::model($db_name)->findByPk($params['sid']);
        if ($model) {
            $orderId = $model->order_id;
            $costMode = $model->cost_mode;
            $orderCost = $model->cost;
            $model->is_click = 1;
            $model->click_time = time();
            $model->save();
            // 将数据库选择到主库
            CActiveRecord::$db = Yii::app()->db;
            // 更新物料映射表
            $table = 'hm_sitematerial_'.$db_name;
            $sql = "update $table set is_click=1, click_count=click_count+1, click_time=".$time." where stat_id=".$params['sid']." and create_time=".$params['time'];
            Yii::app()->db_stat_sitemate->createCommand($sql)->execute();
            // 更新订单花费
            if ($orderId>0 && $costMode==3 && $orderCost>0) {
                Orders::model()->updateCostById($orderId, $orderCost);
            }
        } else {
            // 将数据库选择到主库
            CActiveRecord::$db = Yii::app()->db;
        }
    }
}