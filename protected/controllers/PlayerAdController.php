<?php

class PlayerAdController extends BaseController {
    // 被选中广告物料信息 包括 广告id 物料轮换模式 轮换时间 素材信息
    private $_adMaterialInfo;
    // 被选中广告当天展示统计详细信息
    private $_adStatInfo;
    // 保存广告相关数据 方便添加广告统计日志
    private $_adToStat;

    function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->_adMaterialInfo = array();
        $this->_adStatInfo = array();
        $this->_adToStat = array();
    }

    public function adInfo() {
		$param = json_decode($_POST['parameter'],true);
		$tenant = $param['tenant'];//租户
		$cushion = $param['cushion'];//广告播放类型 (1 缓冲广告、2 暂停广告、3 片尾广告、4插播广告)
		$catalogid = $param['catalogid'];//scms栏目ID
		$time = $param['time'];//请求时间
        //根据租户获取租户ID
        $Company = Company::getComId($tenant);
        $com_id = $Company?$Company->id:'';
        if(!$com_id){
            $this->AppResponse();
        }
        //获取栏目ID下的广告位ID
        $VpCatalog = VpCatalog::getpositionId($catalogid);
        $position_id = (isset($VpCatalog['id']) && $VpCatalog['id'])?$VpCatalog['id']:'';
        if(!$position_id){
            $this->AppResponse();
        }
        //获取该用户在该广告位下正在投放的广告
        $adArr = Ad::getUserPosition($com_id,$position_id);
        if(!$adArr){
            $this->AppResponse();
        }
        //从投放--视频扩展表获取物料ID信息
        $VideoAd = VideoAd::getMaterialData($adArr);
        if(!$VideoAd){
            $this->AppResponse();
        }
        $materialIdArr = array();
        foreach($VideoAd as $val){
            $unval = unserialize($val);
            $materialIdArr[] = $unval[0]['id'];
        }
        //从物料表获取当前物料类型
        $MaterialTypeArr = Material::getMaterialType($materialIdArr);

        //根据物料类型去不同的物料扩展表获取数据
        //图片
        $MaterialVpic =  isset($MaterialTypeArr[2])?MaterialVpic::getMaterData($MaterialTypeArr[2]):array();
        //视频
        $MaterialVvideo = isset($MaterialTypeArr[5])?MaterialVvideo::getMaterData($MaterialTypeArr[5]):array();

        //组装返回数据
        $Response = array();
        //播放类型为暂停广告是则只返回图片
        if($cushion==2){
            foreach ($MaterialVpic as $val) {
                $arr['type'] = 2;
                $arr['src'] = $val['url'] ? Yii::app()->params->pictureUrl.$val['url'] : '';
                $arr['cushion'] = $cushion;
                $arr['href'] = $val['click_link'] ? $val['click_link'] : '';
                $arr['appstore'] = 0;
                $arr['time'] = 10;
                $Response[] = $arr;
            }
        }else{
            foreach ($MaterialVpic as $val) {
                $arr['type'] = 2;
                $arr['src'] = $val['url'] ? Yii::app()->params->pictureUrl . $val['url'] : '';
                $arr['cushion'] = $cushion;
                $arr['href'] = $val['click_link'] ? $val['click_link'] : '';
                $arr['appstore'] = 0;
                $arr['time'] = 10;
                $Response[] = $arr;
            }
            foreach ($MaterialVvideo as $val) {
                $arr['type'] = 3;
                $arr['src'] = $val['url'] ? $val['url'] : '';
                $arr['cushion'] = $cushion;
                $arr['href'] = $val['click_link'] ? $val['click_link'] : '';
                $arr['appstore'] = 0;
                $arr['time'] = 10;
                $Response[] = $arr;
            }

        }

        $ResponseData['totaltime'] = 20;
        $ResponseData['data'] = $Response;
        $this->AppResponse($ResponseData);
        exit;
        $arr = array();
        if($cushion==2){
            $arr[] = array(
                "type"=>2,
                "src"=>"http://123.56.29.145/video/366673.jpg",
                "cushion"=>2,
                "href"=>"http://ad.hrbtv.net:82/dataService/stat?p=dHlwZT1zaXRlJnNpZD0xJnRpbWU9MTQ2NzkwNzk2MiZocmVmPWh0dHA6Ly93d3cuYmFpZHUuY29t",
                "appstore"=>0,
                "time"=>10
            );
        }else{
            $arr[] = array(
                "type" => 2,
                "src" => "http://123.56.29.145/video/366673.jpg",
                "cushion" => 2,
                "href" => "http://ad.hrbtv.net:82/dataService/stat?p=dHlwZT1zaXRlJnNpZD0xJnRpbWU9MTQ2NzkwNzk2MiZocmVmPWh0dHA6Ly93d3cuYmFpZHUuY29t",
                "appstore" => 0,
                "time" => 10
            );
            $arr[] = array(
                "type" => 3,
                "src" => "http://123.56.29.145/video/guide_video.mp4",
                "cushion" => 1,
                "href" => "http://ad.hrbtv.net:82/dataService/stat?p=dHlwZT1zaXRlJnNpZD0xJnRpbWU9MTQ2NzkwNzk2MiZocmVmPWh0dHA6Ly93d3cuYmFpZHUuY29t",
                "appstore" => 0,
                "time" => 10
            );
	    }
        $data['totaltime'] = 20;
        $data['data'] = $arr;
		exit;
		
		
		//老接口
        $this->return['returnCode'] = 200;
        $this->return['returnDesc'] = '缺少必要参数';
        $this->return['returnData'] = array();
        $param_arr = array('positionId', 'cushion');
        if ($this->isPraramerValue($this->params, $param_arr)) {
            if (!$this->_checkPosition()) {
                return $this->return;
            }
            $this->return['returnCode'] = 100;
            $this->return['returnDesc'] = '返回成功';
            $this->return['returnData'] = $this->_getReturnData();
        }
        return $this->return;
    }
    
    /**
     * 获取返回的广告数据
     */
    private function _getReturnData() {
        // 广告属性
        $ad = $this->_getAdAttr();
        if ($this->return['returnCode'] != 100) {
            return array();
        }
        // 物料属性
        $material = $this->_getMaterialAttr();
        if ($this->return['returnCode'] != 100) {
            return array();
        }
        // 添加统计日志
        $statData = VideoStatistics::model()->addStatDetail($this->_adToStat);
        // 组合返回的数据
        $returnData = array();
        $Setting = Setting::model()->getSettings(); //系统设置
        if ($material['total'] < 2) {
            $returnData = $ad;
            $returnData['src'] = $material['list'][0]['src'];
            $returnData['duration'] = $material['list'][0]['duration'];
            $returnData['adtype'] = $material['list'][0]['adtype'];
            $href = '';
            if ($material['list'][0]['href'] != '') {
                $href = $Setting['INTERFACE_URL'] . '/dataService/stat?p=';
                $href .= base64_encode('type=site&sid='.$statData['sid'].'&time='.$statData['time'].'&href='.$material['list'][0]['href']);
            }
            $returnData['href'] = $href;
        } else {
            $data = $ad;
            $data['duration'] = $this->_adMaterialInfo['mrotate_time'];
            foreach($material['list'] as $val) {
                $data['src'] = $val['src'];
                $data['duration'] = $val['duration'];
                $data['adtype'] = $val['adtype'];
                $href = '';
                if ($val['href'] != '') {
                    $href = $Setting['INTERFACE_URL'] . '/dataService/stat?p=';
                    $href .= base64_encode('type=site&sid='.$statData['sid'].'&time='.$statData['time'].'&href='.$val['href']);
                }
                $data['href'] = $href;
                $returnData[] = $data;
            }
        }
        return $returnData;
    }

    /**
     * 获取广告属性
     */
    private function _getAdAttr() {
        $data = array();
        if (isset(Yii::App()->sphinxSearch)) {
            $data = $this->_getAdInfoBySphinx();
        } else {
            $data = $this->_getAdInfoBySql();
        }
        return $data;
    }
    
    private function _getAdInfoBySql() {
        $params = $this->params;
        // 广告位上的广告
        $ad_arr = Ad::model()->getArrId($params['positionId'], 3);
        if (!$this->checkArrData($ad_arr)) {
            return array();
        }
        $clientInfo = Ad::model()->getClientInfo();
        return $this->_adFilterBySql($ad_arr, $clientInfo);
    }
    
    private function _getAdInfoBySphinx() {
        $params = $this->params;
        // 广告位上的广告
        $ad_arr = Ad::model()->getArrId($params['positionId'], 1);
        if (!$this->checkArrData($ad_arr)) {
            return $this->return;
        }
        $strAdId = implode(",", $ad_arr);
        $data = array();
        $w = date('wH', time());
        Yii::App()->sphinxSearch->conn();
        $Ads = Yii::App()->sphinxSearch->query('select id,ad_id,com_id,priority_mode,priority,weights,limit_day_show_mode,limit_day_show_num,limit_one_show,cushion,width,height,pos_x,pos_y,material,mrotate_mode,mrotate_time,start_time,end_time,url_id,browser_id,connect_id,language_id,referer_id,region_id,resolution_id,system_id,timing from ad_main where ad_id in (' . $strAdId . ') and timing in (0,' . $w . ')');
        if (!$this->checkArrData($Ads)) {
            return $this->return;
        }
        $ad_arr = array();
        $new_Ads = array();
        //去掉重复广告     
        foreach ($Ads as $one) {
            if (!in_array($one['id'], $ad_arr)) {
               $new_Ads[$one['id']] = $one;
               $ad_arr[] = $one['id'];
            }
        }
        $clientInfo = Ad::model()->getClientInfo();
        $arrAdId = Ad::model()->filterSphinxData($new_Ads, $ad_arr, $clientInfo);
        if (!$this->checkArrData($arrAdId)) {
            return array();
        }
        $arrAppAdData = array();
        foreach($new_Ads as $one) {
            if (in_array($one['ad_id'], $arrAdId )) {
                $arrAppAdData[$one['ad_id']] = $one;
            }
        }
        // 根据广告 独立访客展现量限制、投放次数限制、优先级、权重 筛选广告
        $return = $this->_getAdInfoByPolicy($arrAppAdData);
        if (!$this->checkArrData($return)) {
            return array();
        }
        return $return;
    }

    private function _adFilterBySql($ad_arr, $client_info) {
        // 根据时间和精准定向过滤广告
        $cushion = $this->params['cushion'];
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('ad_id', $ad_arr);
        $criteria1->addColumnCondition(array('cushion' => $cushion));
        $VideoAd = VideoAd::model()->findAll($criteria1);
        if (!$this->checkArrData($VideoAd)) {
            return array();
        }
        $w = date('wH', time());
        $Network = new NetworkComponent;         
          $ip = ip2long($Network->getIP());
        $data = AreaIp::model()->getDataByIp($ip);
        $ipCode = isset($data->code) ? $data->code : 0;
        $connect = isset($data->connect) ? $data->connect : 0;
        $data = SiteAdConnect::model()->getDataByName($connect);
        $connect_id = isset($data->id) ? $data->id : 0;
        $browser = $client_info['browser'];
        $data = SiteAdBrowser::model()->getDataByName($browser);
        $browser_id = isset($data->id) ? $data->id : 0;
        $language = $client_info['language'];
        $data = SiteAdLanguage::model()->getDataByName($language);
        $language_id = isset($data->id) ? $data->id : 0;
        $accessurl = $client_info['access_url'];
        $data = SiteAdUrl::model()->getDataByUrl($accessurl);
        $accessurl_id = isset($data->id) ? $data->id : 0;
        $formurl = $client_info['referer'];
        $data = SiteAdUrl::model()->getDataByUrl($formurl);
        $formurl_id = isset($data->id) ? $data->id : 0;
        $name = $client_info['width'] . '*' . $client_info['height'];
        $data = SiteAdResolution::model()->getDataByName($name);
        $resolution_id = isset($data->id) ? $data->id : 0;
        $system_name = $client_info['system'];
        $data = SiteAdSystem::model()->getDataByName($system_name);
        $system_id = isset($data->id) ? $data->id : 0;
        $un_arr = array();
        foreach ($VideoAd as $one) {
            if ($one->directional_info) {
                $directional = unserialize($one->directional_info);
                if (isset($directional['time_set'])) {//排期
                    if ($directional['time_set'] != 0 && $directional['time_set'] != $w) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['area_set'])) {//地区
                    if ($directional['area_set'] != 0 && $directional['area_set'] != $ipCode) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['connect_set'])) {//接入点
                    if ($directional['connect_set'] != 0 && $directional['connect_set'] != $connect_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['btype_set'])) {//浏览器
                    if ($directional['btype_set'] != 0 && $directional['btype_set'] != $browser_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['blanguage_set'])) {//浏览器语言
                    if ($directional['blanguage_set'] != 0 && $directional['blanguage_set'] != $language_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['accessurl_set'])) {//使用者url
                    if ($directional['accessurl_set'] != 0 && $directional['accessurl_set'] != $accessurl_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['formurl_set'])) {//来源Url
                    if ($directional['formurl_set'] != 0 && $directional['formurl_set'] != $formurl_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['resolution_set'])) {//分辨率 显示尺寸Url
                    if ($directional['resolution_set'] != 0 && $directional['resolution_set'] != $resolution_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['osystem_set'])) {//系统
                    if ($directional['osystem_set'] != 0 && $directional['osystem_set'] != $system_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
            }
        }
        $arrAdId = array_diff($ad_arr, $un_arr);
        if (!$this->checkArrData($arrAdId)) {
            return array();
        }
        $arrAppAdData = array();
        foreach($VideoAd as $one) {
            if (in_array($one->ad_id, $arrAdId )) {
                $arrAppAdData[$one->ad_id] = $one;
            }
        }
        // 根据广告 独立访客展现量限制、投放次数限制、优先级、权重 筛选广告
        $return = $this->_getAdInfoByPolicy($arrAppAdData);
        if (!$this->checkArrData($return)) {
            return array();
        }
        // 设置统计需要的数据
        $this->_adToStat['ip'] = $ip;
        $this->_adToStat['region_id'] = $ipCode;
        $this->_adToStat['connect_id'] = $connect_id;
        $this->_adToStat['browser_id'] = $browser_id;
        $this->_adToStat['language_id'] = $language_id;
        $this->_adToStat['system_id'] = $system_id;
        $this->_adToStat['resolution_id'] = $resolution_id;
        $this->_adToStat['referer_id'] = $formurl_id;
        $this->_adToStat['accessurl_id'] = $accessurl_id;
        return $return;
    }
    
    private function _adFilterByShpinx($adInfo, $ad_arr) {
        $client_info = json_decode($this->params['clientInfo'], true);
        $w = date('wH', time());
        /* $Network = new NetworkComponent;
          $ip =$Network->getIP(); */
        $ip =16843265;
        $data = Yii::App()->sphinxSearch->query('select id,start_ip,end_ip,code,connect,address,local from area_ip where start_ip <=  '.$ip.' and end_ip >= '.$ip);
        $data = isset($data[0]) ? $data[0] : array();
        $ipCode = isset($data['code']) ? $data['code'] : 0;
        $connect = isset($data['connect']) ? $data['connect'] : 0;
        $data = AppAdConnect::model()->getDataByName($connect);
        $connect_id = isset($data->id) ? $data->id : 0;
        $brand = $client_info['brand'];
        $data = AppAdBrand::model()->getDataByName($brand);
        $brand_id = isset($data->id) ? $data->id : 0;
        $platform = $client_info['platform'];
        $data = AppAdPlatform::model()->getDataByName($platform);
        $platform_id = isset($data->id) ? $data->id : 0;
        $name = $client_info['width'] . '*' . $client_info['height'];
        $data = SiteAdResolution::model()->getDataByName($name);
        $resolution_id = isset($data->id) ? $data->id : 0;
        $un_arr = array();
        foreach ($adInfo as $one) {
            if (isset($one['timing'])) {//排期
                if ($one['timing'] != 0 && $one['timing'] != $w) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['region_id'])) {//地区
                if ($one['region_id'] != 0 && $one['region_id'] != $ipCode) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['connect_id'])) {//接入点
                if ($one['connect_id'] != 0 && $one['connect_id'] != $connect_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['brand_id'])) {//品牌
                if ($one['brand_id'] != 0 && $one['brand_id'] != $brand_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['resolution_id'])) {//分辨率 显示尺寸Url
                if ($one['resolution_id'] != 0 && $one['resolution_id'] != $resolution_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['platform_id'])) {//系统
                if ($one['platform_id'] != 0 && strpos($one['platform_id'],$platform_id)==-1) {
                    $un_arr[] = $one['ad_id'];
                }
            }
        }
        return array_diff($ad_arr, $un_arr);
    }

    /**
     * 根据广告 独立访客展现量限制、投放次数限制、优先级、权重 筛选广告
     */
    private function _getAdInfoByPolicy($arrAppAd) {
        // 根据广告 独立访客展现量限制、投放次数限制 筛选广告
        $reAd = Ad::model()->getOneAdByPolicy($arrAppAd);
        if (!$this->checkArrData($reAd)) {
            return array();
        }
        // 等待完善
        $cushion = $this->params['cushion'];
        $resultAid = $reAd['ad_id'];
        $data = array();
        $data['src'] = "";
        $data['id'] = "$resultAid";
        $data['stretch'] = '1';
        $data['duration'] = '20';
        $data['x'] = $arrAppAd[$resultAid]['pos_x'];
        $data['y'] = $arrAppAd[$resultAid]['pos_y'];
        $data['opacity'] = '1';
        $data['playtime'] = '0';
        $data['adtype'] = '0';
        $data['interval'] = '0';
        $data['cushion'] = (string)$cushion;
        $data['height'] = '1';//$arrAppAd[$resultAid]->height;
        $data['width'] = '1';//$arrAppAd[$resultAid]->width;
        $data['CanClose'] = '0';
        $data['href'] = "";
        // 将获取的数据传入到物料获取中 避免再次查询数据库
        $this->_adMaterialInfo['ad_id'] = $resultAid;
        $this->_adMaterialInfo['mrotate_mode'] = $arrAppAd[$resultAid]['mrotate_mode'];
        $this->_adMaterialInfo['mrotate_time'] = $arrAppAd[$resultAid]['mrotate_time'];
        $this->_adMaterialInfo['material'] = unserialize($arrAppAd[$resultAid]['material']);
        //$this->_adStatInfo = $appStat[$resultAid];
        $this->_adToStat['ad_id'] = $resultAid;
        $this->_adToStat['position_id'] = $this->params['positionId'];
        $this->_adToStat['com_id'] = $arrAppAd[$resultAid]['com_id'];
        $this->_adToStat['cost_mode'] = $arrAppAd[$resultAid]['costing_mode'];
        $this->_adToStat['price'] = $arrAppAd[$resultAid]['price'];
        $this->_adToStat['create_time'] = time();
        $this->_adToStat['info'] = "";
        return $data;
    }

    private function _getMaterialAttr(){
        //获取广告物料
        $adMaterial = $this->_adMaterialInfo;
        if (!$this->checkArrData($adMaterial['material'])) {
            return array();
        }
        $arrMaterialId = array();
        foreach($adMaterial['material'] as $val) {
            $arrMaterialId[] = $val['id'];
        }
        $materrial = Material::model()->getMaterialInfo($arrMaterialId, 3);
        if (!$this->checkArrData($materrial)) {
            return array();
        }
        $arrMid = array_keys($materrial);
        $strMid = $arrMid[0];
        $list = array();
        $total = count($materrial);
        if ($adMaterial['mrotate_mode']==3) {
            $list = $materrial;
            $strMid = implode(",", $arrMid);
        } else {
            // 需根据权重等选择出一个物料
            $list[0] = $materrial[$arrMid[0]];
            $total = 1;
        }
        // 将获取的数据传入到物料获取中 避免再次查询数据库
        $this->_adToStat['material_ids'] = $strMid;

        $return['total'] = $total;
        $return['list'] = $list;
        return $return;
    }

    /**
     * 获取客户端广告展现统计详细信息 根据条件
     */
    private function _getShowStatistics($adId, $startTime) {
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("ad_id" => $adId));
        $criteria->addCondition("create_time>$startTime");
        $table = date("Ymd", time());
        $rData = SiteStat::model($table)->findAll($criteria);
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        return $rData;
    }
    
    /**
     * 获取客户端广告展现点击详细信息 根据条件
     */
    private function _getClickStatistics($adId, $startTime) {
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("ad_id" => $adId));
        $criteria->addColumnCondition(array("is_click" => 1));
        $criteria->addCondition("click_time>$startTime");
        $table = date("Ymd", time());
        $rData = AppStat::model($table)->findAll($criteria);
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        return $rData;
    }
    
     /**
     * 获取广告位属性
     */
    private function _checkPosition() {
        $positionId = $this->params['positionId'];
        $position = Position::model()->getPositionById($positionId);
        $playerType = array(6,8,9);
        if (!$this->checkArrData($position)) {
            return false;
        } else if ($position->status != 1) {
            return $this->checkArrData(false);
        } else if (!in_array($position->ad_show_id, $playerType)) {
            return $this->checkArrData(false);
        }
        return true;
    }
}