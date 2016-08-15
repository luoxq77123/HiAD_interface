<?php

class AppAdController extends BaseController {
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

    public function adApp(){
		$arr['totaltime'] = 20;
		$param = json_decode($_POST['parameter'],true);
		$tenant = $param['tenant'];
		$cushion = $param['cushion'];
		$catalogid = $param['catalogid'];
		$time = $param['time'];
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
				"type"=>2,
				"src"=>"http://123.56.29.145/video/366673.jpg",
				"cushion"=>2,
				"href"=>"http://ad.hrbtv.net:82/dataService/stat?p=dHlwZT1zaXRlJnNpZD0xJnRpbWU9MTQ2NzkwNzk2MiZocmVmPWh0dHA6Ly93d3cuYmFpZHUuY29t",
				"appstore"=>0,
				"time"=>10
			);
			$arr[] =array(
				"type"=>3,
				"src"=>"http://123.56.29.145/video/guide_video.mp4",
				"cushion"=>1,
				"href"=>"http://ad.hrbtv.net:82/dataService/stat?p=dHlwZT1zaXRlJnNpZD0xJnRpbWU9MTQ2NzkwNzk2MiZocmVmPWh0dHA6Ly93d3cuYmFpZHUuY29t",
				"appstore"=>0,
				"time"=>10
			);
		}
	
	
		$this->return['returnCode'] = 100;
        $this->return['returnDesc'] = '返回成功';
        $this->return['returnData'] =$arr; 
		return $this->return;


    }

    public function adInfo() {
        $this->return['returnCode'] = 200;
        $this->return['returnDesc'] = '缺少必要参数';
        $param_arr = array('time', 'clientInfo', 'positionId');
        if ($this->isPraramerValue($this->params, $param_arr)) {
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
        $data = array();
        // 广告位属性
        $data['position'] = $this->_getPositionAttr();
        if ($this->return['returnCode'] != 100) {
            return array();
        }
        // 广告属性
        $data['ad'] = $this->_getAdAttr();
        if ($this->return['returnCode'] != 100) {
            return array();
        }
        // 物料属性
        $data['material'] = $this->_getMaterialAttr();
        if ($this->return['returnCode'] != 100) {
            return array();
        }
        // 添加统计日志
        $statData = AppStatistics::model()->addStatDetail($this->_adToStat);
        // 组合数据 广告点击链接
        $Setting = Setting::model()->getSettings(); //系统设置
        foreach($data['material']['list'] as $key=>$val) {
            $href = '';
            if ($val['link'] != '') {
                $href = $Setting['INTERFACE_URL'] . '/dataService/stat?p=';
                $href .= base64_encode('type=app&sid='.$statData['sid'].'&time='.$statData['time'].'&href='.$val['link']);
            }
            $data['material']['list'][$key]['link'] = $href;
        }
        return $data;
    }
    
    /**
     * 获取广告位属性
     */
    private function _getPositionAttr() {
        $positionId = $this->params['positionId'];
        $clientInfo = json_decode($this->params['clientInfo'], true);
        $appType = $clientInfo['appType'];
        // 广告位信息
        $position = Position::model()->getPositionById($positionId);
        if (!$this->checkArrData($position)) {
            return array();
        }
        if ($position->ad_type_id != 2 || $position->status != 1) {
            $this->checkArrData(false);
            return array();
        }
        // 客户端广告位信息
        $appPosition = AppPosition::model()->getDataByPoistionId($positionId);
        if (!$this->checkArrData($appPosition)) {
            return array();
        }
        // 应用信息 判断客户端系统
        $app = App::model()->getDataById($appPosition->app_id);
        if (!$this->checkArrData($app)) {
            return array();
        }
        $appTypeList = App::model()->appTypeList();
        if ($appTypeList[$app->app_type_id] != $appType || $app->status != 1) {
            $this->checkArrData(false);
            return array();
        }
        // 显示类型 和 组合数据
        $showType = $position->showTypeConvert($position->ad_show_id);
        $posParams = unserialize($appPosition->params);
        $attrParams = $posParams['attr'];
        $pKey = $showType['code'];
        $pAttr = array();
        if ($appType == 'ios' && $posParams['appType']=='ios') {
            $pAttr['width'] = $attrParams['width'];
            $pAttr['height'] = $attrParams['height'];
            if ($pKey == 'fixed') {//固定
                $pAttr['idleTake'] = $appPosition->idle_take;//固定占位
            } else if ($pKey == 'pop') {//插播
                $pAttr['isFullScreen'] = $appPosition->is_full;//是否全屏
                $pAttr['showTime'] = $appPosition->staytime;
                $pAttr['left'] = $attrParams['left'];
                $pAttr['top'] = $attrParams['top'];
            }
        } else if ($appType == 'android' && $posParams['appType']=='android') {
            $pAttr['width'] = round($attrParams['scale_xs']/100, 2);
            $pAttr['scaleWH'] = $attrParams['scale_x']/$attrParams['scale_y'];
            if ($pKey == 'fixed') {
                $pAttr['idleTake'] = $appPosition->idle_take;
            } else if ($pKey == 'pop') {
                $pAttr['isFullScreen'] = $appPosition->is_full;
                $pAttr['showTime'] = $appPosition->staytime;
                $pAttr['offsetLeft'] = round($attrParams['offset_left']/100, 2);
                $pAttr['offsetTop'] = round($attrParams['offset_top']/100, 2);
            }
        }
        if (!$this->checkArrData($pAttr)) {
            return array();
        }
        $data = array();
        $data['showType'] = $showType['id'];
        $data[$pKey] = $pAttr;
        return $data;
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
        $ad_arr = Ad::model()->getArrId($params['positionId'], 2);
        if (!$this->checkArrData($ad_arr)) {
            return array();
        }
        return $this->_adFilterBySql($ad_arr);
    }
    
    private function _getAdInfoBySphinx() {
        $params = $this->params;
        // 广告位上的广告
        $ad_arr = Ad::model()->getArrId($params['positionId'], 2);
        if (!$this->checkArrData($ad_arr)) {
            return array();
        }
        $strAdId = implode(",", $ad_arr);
        $data = array();
        $w = date('wH', time());
        Yii::App()->sphinxSearch->conn();
        $Ads = Yii::App()->sphinxSearch->query('select id,ad_id,com_id,priority_mode,priority,weights,limit_day_show_mode,limit_day_show_num,limit_one_show,material,mrotate_mode,mrotate_time,start_time,end_time,brand_id,connect_id,language_id,region_id,resolution_id,platform_id,timing from ad_main where ad_id in ('.$strAdId.') and timing in (0,'.$w .')');
        if (!$this->checkArrData($Ads)) {
            return array();
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
        return $this->_adFilterByShpinx($new_Ads,$ad_arr);
    }

    private function _adFilterBySql($ad_arr) {
        // 根据时间和精准定向过滤广告
        $client_info = json_decode($this->params['clientInfo'], true);
        $appAd = AppAd::model()->getByArrAdId($ad_arr);
        if (!$this->checkArrData($appAd)) {
            return array();
        }
        $w = date('wH', time());
        //此处代码为调试屏蔽，正式开放需去掉注释
        /* $Network = new NetworkComponent;
          $ip =$Network->getIP(); */
        $ip = '16910595';
        //$data = AreaIp::model()->getDataByIp($ip);
        //$ipCode = isset($data->code) ? $data->code : 0;
        $ipCode = 0;
        $connect = $client_info['connect'];//
        $data = AppAdConnect::model()->getDataByName($connect);
        $connect_id = isset($data->id) ? $data->id : 0;
        $brand = $client_info['brand'];//品牌
        $data = AppAdBrand::model()->getDataByName($brand);
        $brand_id = isset($data->id) ? $data->id : 0;
        $platform = $client_info['platform'];//平台
        $data = AppAdPlatform::model()->getDataByName($platform);
        $platform_id = isset($data->id) ? $data->id : 0;
        $name = $client_info['screenWidth'] . '*' . $client_info['screenHeight'];
        $data = SiteAdResolution::model()->getDataByName($name);
        $resolution_id = isset($data->id) ? $data->id : 0;
        $un_arr = array();
        foreach ($appAd as $one) {
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
                if (isset($directional['brand_set'])) {//品牌
                    if ($directional['brand_set'] != 0 && $directional['brand_set'] != $brand_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['platform_set'])) {//平台
                    if ($directional['platform_set'] != 0 && $directional['platform_set'] != $platform_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['resolution_set'])) {//分辨率 显示尺寸Url
                    if ($directional['resolution_set'] != 0 && $directional['resolution_set'] != $resolution_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
            }
        }
        $arrAdId = array_diff($ad_arr, $un_arr);//过滤广告
        //$arrAdId = $ad_arr;
        if (!$this->checkArrData($arrAdId)) {
            return array();
        }
        $arrAppAdData = array(); 
        foreach($arrAdId as $id) {
            $arrAppAdData[$id] = $appAd[$id];
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
        $this->_adToStat['brand_id'] = $brand_id;
        $this->_adToStat['platform_id'] = $platform_id;
        $this->_adToStat['resolution_id'] = $resolution_id;
        return $return;
    }
    
    private function _adFilterByShpinx($adInfo, $ad_arr) {
        $client_info = json_decode($this->params['clientInfo'], true);
        $w = date('wH', time());
        $Network = new NetworkComponent;
        $ip =$Network->getIP();
        $ip = '16910595';
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
        $name = $client_info['screenWidth'] . '*' . $client_info['screenHeight'];
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
        $arrAdId = array_diff($ad_arr, $un_arr);
        if (!$this->checkArrData($arrAdId)) {
            return array();
        }
        $arrAppAdData = array(); 
        foreach($arrAdId as $id) {
            $arrAppAdData[$id] = $adInfo[$id];
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
        $this->_adToStat['brand_id'] = $browser_id;
        $this->_adToStat['platform_id'] = $language_id;
        $this->_adToStat['resolution_id'] = $resolution_id;
        return $return;
    }

    /**
     * 根据广告 独立访客展现量限制、投放次数限制、优先级、权重 筛选广告
     */
    private function _getAdInfoByPolicy($arrAppAd) {
        // 根据广告 独立访客展现量限制、投放次数限制 筛选广告
        $filterAid = array();
        $appStat = array();
        foreach($arrAppAd as $id => $val) {
            $today = date("Y-m-d 00:00:00", time());
            $startTime = $this->mstrToTime($today);
            $showStat = $this->_getShowStatistics($id, $startTime);
            $appStat[$id] = $showStat;
            // 对独立访客展现量限制 统计数据库需添加自段
            if (!empty($val['limit_one_show'])) {
                
            }
            if ($val['limit_day_show_mode'] == 1) { //展现量
                if (!empty($showStat)) {
                    if (count($showStat) >= $val['limit_day_show_num']) {
                        $filterAid[] = $id;
                        continue;
                    }
                }
            } else if ($val['limit_day_show_mode'] == 2) { //点击量
                $today = date("Y-m-d 00:00:00", time());
                $startTime = Common::model()->mstrToTime($today);
                $clickStat = $this->_getClickStatistics($id, $startTime);
                if (!empty($clickStat)) {
                    if (count($clickStat) >= $val['limit_day_show_num']) {
                        $filterAid[] = $id;
                        continue;
                    }
                }
            }
        }
        $arrAdId = array_diff(array_keys($arrAppAd), $filterAid);
        if (!$this->checkArrData($arrAdId)) {
            return array();
        }
        // 根据广告 优先级、权重 筛选广告
        // 等待完善
        $resultAid = reset($arrAdId);
        $return = array();
        $return['isRotate'] = AppAd::model()->materialIsRotate($arrAppAd[$resultAid]['mrotate_mode']);
        $return['rotateTime'] = $arrAppAd[$resultAid]['mrotate_time'];
        // 将获取的数据传入到物料获取中 避免再次查询数据库
        $this->_adMaterialInfo['ad_id'] = $resultAid;
        $this->_adMaterialInfo['mrotate_mode'] = $arrAppAd[$resultAid]['mrotate_mode'];
        $this->_adMaterialInfo['mrotate_time'] = $arrAppAd[$resultAid]['mrotate_time'];
        $this->_adMaterialInfo['material'] = unserialize($arrAppAd[$resultAid]['material']);
        $this->_adStatInfo = $appStat[$resultAid];
        $this->_adToStat['ad_id'] = $resultAid;
        $this->_adToStat['position_id'] = $this->params['positionId'];
        $this->_adToStat['com_id'] = $arrAppAd[$resultAid]['com_id'];
        $this->_adToStat['cost_mode'] = $arrAppAd[$resultAid]['costing_mode'];
        $this->_adToStat['price'] = $arrAppAd[$resultAid]['price'];
        $this->_adToStat['create_time'] = time();
        $this->_adToStat['info'] = "";
        return $return;
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
        $materrial = Material::model()->getInfoByArrId($arrMaterialId, 2);
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
        $this->_adToStat['material_id'] = $arrMid[0];
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
        $rData = AppStat::model($table)->findAll($criteria);
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
}