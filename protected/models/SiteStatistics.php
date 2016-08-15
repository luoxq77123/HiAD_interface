<?php

class SiteStatistics extends CActiveRecord {
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

    function add($arr) {
        $con = Yii::app()->db2;
        $str = '';
        $str2 = '';
        foreach($arr as $ke => $va){
            $str .= mysql_real_escape_string($ke).',';
            $str2 .= '\''.mysql_real_escape_string($va).'\',';
        }
        $str = substr($str, 0, -1);
        $str2 =substr($str2, 0, -1); 
        $sql = "INSERT INTO site_20121020 (".$str.") VALUES(".$str2.")";
        $command = $con->createCommand($sql)->execute();
    }

    /**
     * ¸ù¾Ý¹ã¸æÇëÇó ²åÈëÍ³¼Æ·Ö±í
     */
    public function addStatDetail($data) {
        // »ñÈ¡¶©µ¥id
        $adData = Ad::model()->getOneById($data['ad_id']);
        $data['order_id'] = $adData->order_id;
        $orderData = Orders::model()->getOneById($data['order_id']);
        // »ñÈ¡¿Í»§id
        $data['client_id'] = ($orderData)? $orderData->client_company_id : 0;
        // »ñÈ¡ÏúÊÛÈËÔ±id
        $data['seller_id'] = ($orderData)? $orderData->salesman_id : 0;
        // ´´½¨Í³¼Æ±í
        $this->createStatTable();
        // ÉèÖÃÕ¹Ê¾»òÕßµã»÷ÊÕÈë
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // Ìí¼ÓÍ³¼ÆÐÅÏ¢
        $statData = $this->addSubStatData($data);
        // Ìí¼ÓÎïÁÏÍ³¼ÆÐÅÏ¢
        $this->addMaterialData($data, $statData);
        // Èç¹û¹ã¸æ¼Æ·ÑÄ£Ê½ÊÇÕ¹ÏÖÇÒ¶©µ¥´æÔÚ Ôò¸üÐÂ¶©µ¥»°·Ñ
        if ($data['cost_mode']==2 && $data['order_id']>0 && $data['cost']>0) {
            Orders::model()->updateCostById($data['order_id'], $data['cost']);
        }
        return $statData;
    }
    
    /**
     * Ìí¼Ó·Ö±íÍ³¼Æ Õë¶ÔHimiController¹ã¸æ»ñÈ¡Í³¼Æ£¬²»×öÎïÁÏÍ³¼Æ£¬µÈÎïÁÏÉ¸Ñ¡ºóÔÙ×÷Í³¼Æ
     */
    public function addStatDetailForSite($data) {
        // »ñÈ¡¶©µ¥id
        $adData = Ad::model()->getOneById($data['ad_id']);
        $data['order_id'] = $adData->order_id;
        $orderData = Orders::model()->getOneById($data['order_id']);
        // »ñÈ¡¿Í»§id
        $data['client_id'] = ($orderData)? $orderData->client_company_id : 0;
        // »ñÈ¡ÏúÊÛÈËÔ±id
        $data['seller_id'] = ($orderData)? $orderData->salesman_id : 0;
        // ´´½¨Í³¼Æ±í
        $this->createStatTable();
        // ÉèÖÃÕ¹Ê¾»òÕßµã»÷ÊÕÈë
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // Ìí¼ÓÍ³¼ÆÐÅÏ¢
        $statData = $this->addSubStatData($data);
        // Èç¹û¹ã¸æ¼Æ·ÑÄ£Ê½ÊÇÕ¹ÏÖÇÒ¶©µ¥´æÔÚ Ôò¸üÐÂ¶©µ¥»°·Ñ
        if ($data['cost_mode']==2 && $data['order_id']>0 && $data['cost']>0) {
            Orders::model()->updateCostById($data['order_id'], $data['cost']);
        }
        unset($data);
        return $statData;
    }
    
    /**
     * Ìí¼ÓÎïÁÏ·Ö±íÍ³¼Æ Õë¶ÔHimiController¹ã¸æ»ñÈ¡Í³¼Æ
     */
    public function addMaterialStatForSite($data, $statData) {
        // ÉèÖÃÕ¹Ê¾»òÕßµã»÷ÊÕÈë
        $data['cost'] = $this->getRowCost($data['cost_mode'], $data['price']);
        // Ìí¼ÓÎïÁÏÍ³¼ÆÐÅÏ¢
        $this->addMaterialData($data, $statData);
    }
    
    /**
     * ²åÈë·Ö±íÊý¾Ý
     */
    public function addSubStatData($data) {
        $date = date("Ymd", $data['create_time']);
        // Ìí¼ÓÍ³¼ÆÐÅÏ¢
        $AppStat = new SiteStat($date);
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
        // ½«Êý¾Ý¿âÑ¡Ôñµ½Ö÷¿â
        CActiveRecord::$db = Yii::app()->db;
        return $return;
    }
    
    /**
     * ²åÈëÎïÁÏ±íÊý¾Ý ÕâÀïÒ»¸ö¹ã¸æ¶ÔÓ¦¶à¸öÎïÁÏ Îª·½±ã¸ù¾ÝÎïÁÏÍ³¼ÆÐèÌí¼ÓÁÙÊ±ÎïÁÏÐÅÏ¢
     */
    public function addMaterialData($data, $statData) {
        if ($data['material_ids']) {
            //创建物料记录
            SiteStatMate::model()->createStatTable();
            $arrMaterial = explode(",", $data['material_ids']);
            if (!empty($arrMaterial)) {
                $date = date("Ymd", $data['create_time']);
                $table = 'hm_sitematerial_'.$date;
                $sql = "insert into $table (material_id, stat_id, com_id, ip, is_click, click_count, cost_mode, cost, create_time) values ";
                foreach($arrMaterial as $mid) {
                    $sql .= "(".$mid.", ".$statData['sid'].", ".$data['com_id'].", '".$data['ip']."', 0, 0, ".$data['cost_mode'].", ".$data['cost'].", ".$data['create_time']."),";
                }
                $sql = substr($sql, 0, -1);
                Yii::app()->db_stat_sitemate->createCommand($sql)->execute();
            }
        }
    }

    /**
     * ÉèÖÃÃ¿ÌõÕ¹Ê¾»òÕßµã»÷ÊÕÈë
     */
    public function getRowCost($costMode, $cost) {
        $cost = ($cost>0)? $cost : 0.00;
        $reCost = 0.00;
        if ($costMode == 2) { // cpm
            // ²éÑ¯Õ¹ÏÖ¼ÆÊý Ã¿Ò»Ç§´ÎËãÒ»´ÎÊÕÈë
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
            // ÔÝÃ¿´Î¼Æ·Ñ°´ÉèÖÃµÄcpm/1000À´¼ÆËã
            $reCost = $cost/1000;
        } else { // cpc cpd
            $reCost = $cost;
        }
        return $reCost;
    }

    /**
     * ´´½¨Í³¼Æ·Ö±í
     */
    public function createStatTable() {
        $date = date("Ymd", time());
        $table = 'site_' . $date;
        $query = "
        CREATE TABLE IF NOT EXISTS `$table` (
          `id` int(10) NOT NULL auto_increment,
          `ad_id` int(10) default NULL COMMENT '¹ã¸æid',
          `position_id` int(10) default NULL COMMENT '¹ã¸æÎ»id',
          `order_id` int(10) default '0' COMMENT '¶©µ¥id',
          `client_id`  int(10) NULL DEFAULT 0 COMMENT '¿Í»§id',
          `seller_id` int(10) default '0' COMMENT 'ÏúÊÛid',
          `material_ids` varchar(255) default NULL COMMENT 'ËùÓÐÕ¹Ê¾ÎïÁÏid',
          `ip` int(10) default '0' COMMENT 'ip×ª»»ÕûÐÎ',
          `region_id` int(10) default '0' COMMENT 'µØÓòid',
          `connect_id` int(5) default '0' COMMENT '½ÓÈë·½Ê½id',
          `browser_id` int(5) default '0' COMMENT 'ä¯ÀÀÆ÷ÀàÐÍ',
          `language_id` int(5) default '0' COMMENT 'ä¯ÀÀÆ÷ÓïÑÔ',
          `system_id` int(5) default '0' COMMENT 'ÏµÍ³',
          `resolution_id` int(5) default '0' COMMENT '·Ö±æÂÊ',
          `referer_id` int(10) default '0' COMMENT 'À´Ô´Óò',
          `accessurl_id` int(10) default '0' COMMENT '±»·Ãurl',
          `is_click` tinyint(1) default '0' COMMENT 'ÊÇ·ñµã»÷ 1£ºÊÇ  0 ²»ÊÇ',
          `create_time` int(11) default NULL COMMENT '¹ã¸æÏÔÊ¾Ê±¼ä',
          `click_time` int(11) default NULL COMMENT '¹ã¸æµã»÷Ê±¼ä',
          `cost_mode` tinyint(1) default '0' COMMENT '¼Æ·ÑÄ£Ê½£»1.CPD; 2.CPM; 3.CPC',
          `cost` float(8,2) default '0' COMMENT '¼Æ·Ñ',
          `info` mediumtext COMMENT 'ÆäËûÐÅÏ¢À©Õ¹',
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
        SiteStat::model()->createStatTable();
        $model = SiteStat::model($db_name)->findByPk($params['sid']);
        if ($model) {
            $orderId = $model->order_id;
            $costMode = $model->cost_mode;
            $orderCost = $model->cost;
            $model->is_click = 1;
            $model->click_time = time();
            $model->save();
            // ½«Êý¾Ý¿âÑ¡Ôñµ½Ö÷¿â
            CActiveRecord::$db = Yii::app()->db;
            // ¸üÐÂÎïÁÏÓ³Éä±í
            $table = 'hm_sitematerial_'.$db_name;
            $sql = "update $table set is_click=1, click_count=click_count+1, click_time=".$time." where stat_id=".$params['sid']." and create_time=".$params['time'];
            Yii::app()->db_stat_sitemate->createCommand($sql)->execute();
            // ¸üÐÂ¶©µ¥»¨·Ñ
            if ($orderId>0 && $costMode==3 && $orderCost>0) {
                Orders::model()->updateCostById($orderId, $orderCost);
            }
        } else {
            // ½«Êý¾Ý¿âÑ¡Ôñµ½Ö÷¿â
            CActiveRecord::$db = Yii::app()->db;
        }
    }
}