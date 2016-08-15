<?php

/**
 * 错误处理控制器。
 */
class SetPolicyAction extends CAction {
    public function run(){
        $user = Yii::app()->session['user'];
        $adInfo = Yii::app()->session['create_ad_info'];
        $aid = 0;
        if (isset($_REQUEST['aid'])) {
            $aid = intval($_REQUEST['aid']);
        } 
        if ($aid<1 && isset($adInfo['ad']['aid'])){
            $aid = $adInfo['ad']['aid'];
        }
        if ($aid==0 || $aid!=$adInfo['ad']['aid']) {
            echo "广告投放传入的参数有误，请重新再试";
            exit();
        }
        // 获取处理动作
        $do = '';
        if (isset($_REQUEST['do'])) {
            $do = $_REQUEST['do'];
        } else if (isset($adInfo['ad']['do']) && $adInfo['ad']['do']=='modify') {
            $do = 'modify';
        }

        // 保存广告位设置
        if ('save'==$do) {
            $msg = $this->_saveTempPolicy();
            echo json_encode($msg);
            exit();
        } else if ('modify'==$do && !isset($adInfo['policy'])) {
            $return = $this->sessionPolicyData($aid);
            if ($return['code']<1) {
                echo $return['message'];
                exit();
            }
            $adInfo = Yii::app()->session['create_ad_info'];
        }

        // 优先级方式
        $priorityMode = SiteAd::model()->getPriorityMode();
        // 优先级参数列表
        $priorityList = SiteAd::model()->getPriorityList();
        // 权重参数列表
        $weightList = SiteAd::model()->getWeightList();
        // 计费方式
        $costMode = SiteAd::model()->getCostMode();
        // 显示每日投放数量方式
        $limitDayMode = SiteAd::model()->getLimitDayMode();
        // 限制对独立访客的展现次数方式
        $limitOneShowMode = SiteAd::model()->getLimitOneShowMode();	

        // 投放策略参数
        $policy = array();
        // 精准定位
        $directional = array();

        if (isset($adInfo['policy'])) {
            $policy = $adInfo['policy'];
            if (!empty($policy['directional_info'])){
                $directionalInfo = $policy['directional_info'];
                $directional = $this->parseDirectional($directionalInfo);
            }
        } else {
            $policy = array(
                'time_mode' => 'default',
                'start_time' => date("Y-m-d 00:00", time()+3600),
                'set_endtime' => 0,
                'end_time' => date("Y-m-d 23:59", time()),
                'gap_time' => "",
                'days'=>0,
                'priority_mode' => 2,
                'priority' => 5,
                'set_weights' => 0,
                'weights' => 5,
                'set_cost' => 0,
                'cost_mode' => 1,
                'price' => "",
                'cost_num' => 0,
                'set_limit_day' => 0,
                'limit_day_mode' => 1,
                'limit_day_num' => "",
                'set_limit_one' => 0,
                'limit_one' => array(),
                //'directional_info' => array()
            );
        }
        $policy['aid'] = $aid;

        $set = array(
            'priorityMode' => $priorityMode,
            'priorityList' => $priorityList,
            'weightList' => $weightList,
            'costMode' => $costMode,
            'limitDayMode' => $limitDayMode,
            'limitOneShowMode' => $limitOneShowMode,
            'policy' => $policy,
            'directional' => $directional
        );
        
        $controller = $this->getController();
        $controller->renderPartial('setPolicy', $set);
    }
    
    // 解析精确定位设置
    public function parseDirectional($directionalInfo){
        // 精准定位
        $directional = array();
        //地区
        if ($directionalInfo['area_set']!=0) {
            $directional['area_set']['value'] = $directionalInfo['area_set'];
            $arrAddrId = explode(",", $directionalInfo['area_set']);
            $province = Province::model()->getList();
            $areaText = "";
            foreach($arrAddrId as $val) {
                if ($val%10000==0){
                    $areaText .= $province[$val].",";
                } else if ($val%100==0) {
                    $provinceId = floor($val/10000)*10000;
                    $citys = City::model()->getListByProvince($provinceId);
                    $areaText .= $citys[$val].",";
                } else {
                    $provinceId = floor($val/10000)*10000;
                    $citys = City::model()->getListByProvince($provinceId);
                    $cityId = key($citys);
                    $districts = District::model()->getListByCity($cityId);
                    $areaText .= $districts[$val].",";
                }
            }
            $directional['area_set']['text'] = $areaText;
        }
        
        //链接
        if ($directionalInfo['connect_set']!=0) {
            $directional['connect_set']['value'] = $directionalInfo['connect_set'];
            $lists = AppAdConnect::model()->getList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['connect_set']))? explode("," ,$directionalInfo['connect_set']) : array();

            $text = "";
            foreach($lists as $key=>$val){
                if (in_array($key, $arraySet)) {
                    $text .= $val.",";
                }
            }
            $directional['connect_set']['text'] = $text;
        }

        // 时间
        if ($directionalInfo['time_set']!=0) {
            $directional['time_set']['value'] = $directionalInfo['time_set'];
            $weekList = AdTime::model()->weekList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['time_set']))? explode("," ,$directionalInfo['time_set']) : array();
            
            $text = "";
            foreach ($weekList as $key=>$val) {
                $text .= '<span>'.$val.'：';
                $timeText = '';
                foreach ($arraySet as $val) {
                    $arrTime = explode("-", $val);
                    $wk = floor($arrTime[0]/100);
                    if($key==$wk) {
                        if($arrTime[1]-$arrTime[0]==24){
                            $timeText .= '全天投放';
                            break;
                        } else {
                            $timeText .= ($arrTime[0]%100).':00--'.($arrTime[1]%100).':00,';
                        }
                    }
                }
                $timeText = ($timeText=='')? '全天暂停' : $timeText;
                $text .= $timeText.'</span>';
            }
            $directional['time_set']['text'] = $text;
        }

        //手机品牌
        if ($directionalInfo['brand_set']!=0) {
            $directional['brand_set']['value'] = $directionalInfo['brand_set'];
            $lists = AppAdBrand::model()->getList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['brand_set']))? explode("," ,$directionalInfo['brand_set']) : array();

            $text = "";
            foreach ($lists as $key=>$val) {
                if (in_array($key, $arraySet)) {
                    $text .= $val.",";
                }
            }
            $directional['brand_set']['text'] = $text;
        }
        
        //浏览器语言
        /*if ($directionalInfo['blanguage_set']!=0) {
            $directional['blanguage_set']['value'] = $directionalInfo['blanguage_set'];
            $lists = SiteAdLanguage::model()->getList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['blanguage_set']))? explode("," ,$directionalInfo['blanguage_set']) : array();
            
            $text = "";
            foreach($lists as $key=>$val){
                if (in_array($key, $arraySet)) {
                    $text .= $val.",";
                }
            }
            $directional['blanguage_set']['text'] = $text;
        }*/

        //投放平台
        if ($directionalInfo['platform_set']!=0) {
            $directional['platform_set']['value'] = $directionalInfo['platform_set'];
            $lists = AppAdPlatform::model()->getList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['platform_set']))? explode("," ,$directionalInfo['platform_set']) : array();

            $text = "";
            foreach($lists as $key=>$val){
                if (in_array($key, $arraySet)) {
                    $text .= $val.",";
                }
            }
            $directional['platform_set']['text'] = $text;
        }

        //分辨率
        if ($directionalInfo['resolution_set']!=0) {
            $directional['resolution_set']['value'] = $directionalInfo['resolution_set'];
            $lists = SiteAdResolution::model()->getList();
            // 获取设置参数
            $arraySet = (!empty($directionalInfo['resolution_set']))? explode("," ,$directionalInfo['resolution_set']) : array();

            $text = "";
            foreach($lists as $key=>$val){
                if (in_array($key, $arraySet)) {
                    $text .= $val.",";
                }
            }
            $directional['resolution_set']['text'] = $text;
        }

        return $directional;
    }

    public function sessionPolicyData($aid) {
        $appAd = new AppAd();
        $data = $appAd->getOneByAdId($aid);
        if (!empty($data)) {
            $user = Yii::app()->session['user'];
            if ($user['com_id']!=$data->com_id){
                $return['code'] = 0;
                $return['message'] = "对不起，你没有权限修改广告投放设置";
                return $return;
            }
            $adInfo = Yii::app()->session['create_ad_info'];
            
            $time_mode = ($data->start_time==0 && $data->other_time!="")? "" : "default";
            $set_endtime = ($data->end_time==0)? 0 : 1;
            $set_weights = ($data->weights==0)? 0 : 1;
            $set_cost = ($data->costing_mode==0)? 0 : 1;
            $set_limit_day = ($data->limit_day_show_mode==0)? 0 : 1;
            $set_limit_one = ($data->limit_one_show=="")? 0 : 1;
            // 存入缓存
            $days=0;
            if($data->other_time){
                $times = array();
                $time = explode("\n", trim($data->other_time)); 
                foreach ($time as $one) {
                  $times = explode(" ~ ", $one);     
                  $days+=ceil((strtotime($times[1].' 23:59:59')-strtotime($times[0].' 00:00:00'))/86400);
                }
            }
            $adInfo['policy'] = array(
                'site_ad_id' => $data->id,
                'time_mode' => $time_mode,
                'start_time' => ($time_mode=="default")? date("Y-m-d H:i", $data->start_time) : "",
                'set_endtime' => $set_endtime,
                'end_time' => ($set_endtime==1)? date("Y-m-d H:i", $data->end_time):"",
                'gap_time' => $data->other_time,
                'days'=>$days,
                'priority_mode' => $data->priority_mode,
                'priority' => $data->priority,
                'set_weights' => $set_weights,
                'weights' => $data->weights,
                'set_cost' => $set_cost,
                'cost_mode' => $data->costing_mode,
                'price' => $data->price,
                'cost_num' => $data->cost_num,
                'set_limit_day' => $set_limit_day,
                'limit_day_mode' => $data->limit_day_show_mode,
                'limit_day_num' => $data->limit_day_show_num,
                'set_limit_one' => $set_limit_one,
                'limit_one' => unserialize($data->limit_one_show),
                'directional_info' => unserialize($data->directional_info)
            );
            // 物料信息
            $adInfo['material'] = array(
                'mrotate_mode' => $data->mrotate_mode,
                'mrotate_time' => $data->mrotate_time,
                'material' => unserialize($data->material)
            );

            Yii::app()->session['create_ad_info'] = $adInfo;
        }
        $return['code'] = 1;
        return $return;
    }

    // 临时保存广告位设置
    private function _saveTempPolicy(){
        //检查参数
        $return = $this->checkForm();
        if (0==$return['code']) {
            return $return;
        }
        $user = Yii::app()->session['user'];

        foreach($_POST as $key=>$val) {
            $$key = $val;
        }
        $adInfo = Yii::app()->session['create_ad_info'];
        $aid = 0;
        $positionId = isset($adInfo['ad']['position_id'])? $adInfo['ad']['position_id'] : 0;
        if (isset($_REQUEST['aid'])) {
            $aid = intval($_REQUEST['aid']);
        } else if (isset($adInfo['ad']['aid'])){
            $aid = $adInfo['ad']['aid'];
        }
        if ($aid==0 || $aid!=$adInfo['ad']['aid']) {
            $return['code'] = 0;
            $return['message'] = "广告参数有误。";
            return $return;
        }

        // 保存独立访问设置参数
        $limitOne = array();
        if ($set_limit_one) {
            $arrOne = explode("&&", $limit_one);
            foreach($arrOne as $key=>$val) {
                $kv = explode("||",$val);
                $limitOne[$key]['mode'] = $kv[0];
                $limitOne[$key]['num'] = $kv[1];
            }
        }
        $serLimitOne = serialize($limitOne);

        // 精准定位
        $directional = array();
        $directional['area_set'] = $areaSet;
        $directional['connect_set'] = $connectSet;
        $directional['time_set'] = $timeSet;
        $directional['brand_set'] = $brandSet;
        //$directional['blanguage_set'] = $blanguageSet;
        $directional['platform_set'] = $platformSet;
        $directional['resolution_set'] = $resolutionSet;
        /*$directional['formurl_set'] = $fromurlSet;
        $directional['accessurl_set'] = $accessurlSet;*/
        $serDirectional = serialize($directional);

        //保存到数据库
        $appAd = new AppAd();
        $saId = isset($_POST['said'])? intval($_POST['said']) : $appAd->getIdByAdId($aid);

        $tsTime = ($time_mode=="default")? AdTime::model()->mstrToTime($start_time) : 0;
        $teTime = ($time_mode=="default" && $set_endtime)? AdTime::model()->mstrToTime($end_time) : 0;
        $toTime = ($time_mode=="default")? "" : $gap_time;
        $days=0;
        if ($toTime!="") {
            $times = array();
            $time = explode("\n", trim($toTime)); 
            foreach ($time as $one) {
              $times = explode(" ~ ", $one);     
              $days += ceil((strtotime($times[1].' 23:59:59')-strtotime($times[0].' 00:00:00'))/86400);
            }
        }
        $tweights = ($set_weights)?  $weights : 0;
        $tcostMode = ($set_cost)? $cost_mode : 1;
        $tprice = ($set_cost && $price>0)? $price : 0.00;
        $tcostNum = ($set_cost && $cost_num>0)? $cost_num : 0;
        $tlimitDayMode = ($set_limit_day)? $limit_day_mode : 0;
        $tlimitDayNum = ($set_limit_day)? $limit_day_num : 0;
        $tlimitOne = ($set_limit_one)? $serLimitOne : "";

        if($saId>0) {
            $attribe = array(
                'com_id' => $user['com_id'],
                'uid' => $user['uid'],
                'start_time' => $tsTime,
                'end_time' => $teTime,
                'other_time' => $toTime,
                'priority_mode' => $priority_mode,
                'priority' => $priority,
                'weights' => $tweights,
                'costing_mode' => $tcostMode,
                'price' => $tprice,
                'cost_num' => $tcostNum,
                'limit_day_show_mode' => $tlimitDayMode,
                'limit_day_show_num' => $tlimitDayNum,
                'limit_one_show' => $tlimitOne,
                'directional_info' => $serDirectional,
                'posttime' => time()
            );
            $appAd->updateAll($attribe, 'id=:id', array(':id'=>$saId));
        } else {
            $appAd->ad_id = $aid;
            $appAd->com_id = $user['com_id'];
            $appAd->uid = $user['uid'];
            $appAd->start_time = $tsTime;
            $appAd->end_time = $teTime;
            $appAd->other_time = $toTime;
            $appAd->priority_mode = $priority_mode;
            $appAd->priority = $priority;
            $appAd->weights = $tweights;
            $appAd->costing_mode = $tcostMode;
            $appAd->price = $tprice;
            $appAd->cost_num = $tcostNum;
            $appAd->limit_day_show_mode = $tlimitDayMode;
            $appAd->limit_day_show_num = $tlimitDayNum;
            $appAd->limit_one_show = $tlimitOne;
            $appAd->directional_info = $serDirectional;
            $appAd->posttime = time();
            $appAd->save();
            $saId = Yii::app()->db->getLastInsertID();
        }

        // 同步广告表里投放时间
        $adsStartTime = 0;
        $adsEndTime = 0;
        if ($time_mode=="default") {
            $adsStartTime = $tsTime;
            $adsEndTime = $teTime;
        } else {
            $sign = array("\r\n", "\n", "\r");
            $strOtherTime = str_replace($sign, ",", trim($toTime));
            $arrOtherTime = explode(",", $strOtherTime);
            $ArrTimeLen = count($arrOtherTime);
            $arrAdsStartTime = explode(" ~ ", $arrOtherTime[0]);
            $arrAdsEndTime = explode(" ~ ", $arrOtherTime[$ArrTimeLen-1]);
            $adsStartTime = AdTime::model()->mstrToTime($arrAdsStartTime[0]." 00:00:00");
            $adsEndTime = AdTime::model()->mstrToTime($arrAdsEndTime[1]." 23:59:59");
        }
        $attribe = array(
            'ads_start_time' => $adsStartTime,
            'ads_end_time' => $adsEndTime
        );
        $ad = new Ad();
        $ad->updateAll($attribe, 'id=:id and com_id=:com_id', array(':id'=>$aid,':com_id'=>$user['com_id']));
        
        // 同步广告时间ad_time表
        AdTime::model()->deleteByAdId($aid);
        if ($time_mode=="default") {
            $adTime = new AdTime();
            $adTime->ad_id = $aid;
            $adTime->position_id = $positionId;
            $adTime->start_time = $tsTime;
            $adTime->end_time = $teTime;
            $adTime->save();
        } else {
            $sign = array("\r\n", "\n", "\r");
            $strOtherTime = str_replace($sign, ",", trim($toTime));
            $arrOtherTime = explode(",", $strOtherTime);
            foreach($arrOtherTime as $val) {
                $cntGapTime = explode(" ~ ", $val);
                if (count($cntGapTime)==2) {
                    $gapStartTime = AdTime::model()->mstrToTime($cntGapTime[0]." 00:00:00");
                    $gapEndTime = AdTime::model()->mstrToTime($cntGapTime[1]." 23:59:59");
                    $adTime = new AdTime();
                    $adTime->ad_id = $aid;
                    $adTime->position_id = $positionId;
                    $adTime->start_time = $gapStartTime;
                    $adTime->end_time = $gapEndTime;
                    $adTime->save();
                }
            }
        }
        
        // 同步精准定位映射表 方便sphinx检索
        $this->syncDirectional($aid, $directional);

        // 存入缓存
        $adInfo['policy'] = array(
            'site_ad_id' => $saId,
            'time_mode' => $time_mode,
            'start_time' => $start_time,
            'set_endtime' => $set_endtime,
            'end_time' => $end_time,
            'gap_time' => $toTime,
            'days' => $days,
            'priority_mode' => $priority_mode,
            'priority' => $priority,
            'set_weights' => $set_weights,
            'weights' => $tweights,
            'set_cost' => $set_cost,
            'cost_mode' => $tcostMode,
            'price' => $tprice,
            'cost_num' => $tcostNum,
            'set_limit_day' => $set_limit_day,
            'limit_day_mode' => $tlimitDayMode,
            'limit_day_num' => $tlimitDayNum,
            'set_limit_one' => $set_limit_one,
            'limit_one' => $limitOne,
            'directional_info' => $directional
        );

        Yii::app()->session['create_ad_info'] = $adInfo;
        $return['code'] = $saId;
        return $return;
    }
    
    public function syncDirectional($aid, $directional=array()){
        //地址
        $scRegion = new AcRegion();
        $scRegion->deleteByAdId($aid);
        if ($directional['area_set']==0){
            $scRegion = new AcRegion();
            $scRegion->ad_id = $aid;
            $scRegion->region_id = 0;
            $scRegion->save();
        } else {
            $arrScData = explode(",", $directional['area_set']);
            if (!empty($arrScData)) {
                foreach ($arrScData as $val) {
                    $scRegion = new AcRegion();
                    $scRegion->ad_id = $aid;
                    $scRegion->region_id = $val;
                    $scRegion->save();
                }
            }
        }
        
        //链接
        $scConnect = new AcConnect();
        $scConnect->deleteByAdId($aid);
        if ($directional['connect_set']==0){
            $scConnect = new AcConnect();
            $scConnect->ad_id = $aid;
            $scConnect->connect_id = 0;
            $scConnect->save();
        } else {
            $arrScData = explode(",", $directional['connect_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $scConnect = new AcConnect();
                    $scConnect->ad_id = $aid;
                    $scConnect->connect_id = $val;
                    $scConnect->save();
                }
            }
        }
        
        //时间
        $scTiming = new AcTiming();
        $scTiming->deleteByAdId($aid);
        if ($directional['time_set']==0){
            $scTiming = new AcTiming();
            $scTiming->ad_id = $aid;
            $scTiming->timing = 0;
            $scTiming->save();
        } else {
            $arrScData = explode(",", $directional['time_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $temp = explode("-", $val);
                    if (count($temp)==2) {
                        for($i=$temp[0]; $i<$temp[1]; $i++ ) {
                            $scTiming = new AcTiming();
                            $scTiming->ad_id = $aid;
                            $scTiming->timing = $i;
                            $scTiming->save();
                        }
                    }
                }
            }
        }

        //手机品牌
        $scBrowser = new AcBrand();
        $scBrowser->deleteByAdId($aid);
        if ($directional['brand_set']==0){
            $scBrowser = new AcBrand();
            $scBrowser->ad_id = $aid;
            $scBrowser->brand_id = 0;
            $scBrowser->save();
        } else {
            $arrScData = explode(",", $directional['brand_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $scBrowser = new AcBrand();
                    $scBrowser->ad_id = $aid;
                    $scBrowser->brand_id = $val;
                    $scBrowser->save();
                }
            }
        }

        // 浏览器语言
        /*$scLanguage = new AcLanguage();
        $scLanguage->deleteByAdId($aid);
        if ($directional['blanguage_set']==0){
            $scLanguage = new AcLanguage();
            $scLanguage->ad_id = $aid;
            $scLanguage->language_id = 0;
            $scLanguage->save();
        } else {
            $arrScData = explode(",", $directional['blanguage_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $scLanguage = new AcLanguage();
                    $scLanguage->ad_id = $aid;
                    $scLanguage->language_id = $val;
                    $scLanguage->save();
                }
            }
        }*/

        //系统
        $scSystem = new AcPlatform();
        $scSystem->deleteByAdId($aid);
        if ($directional['platform_set']==0){
            $scSystem = new AcPlatform();
            $scSystem->ad_id = $aid;
            $scSystem->platform_id = 0;
            $scSystem->save();
        } else {
            $arrScData = explode(",", $directional['platform_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $scSystem = new AcPlatform();
                    $scSystem->ad_id = $aid;
                    $scSystem->platform_id = $val;
                    $scSystem->save();
                }
            }
        }

        //分辨率
        $scResolution = new AcResolution();
        $scResolution->deleteByAdId($aid);
        if ($directional['resolution_set']==0){
            $scResolution = new AcResolution();
            $scResolution->ad_id = $aid;
            $scResolution->resolution_id = 0;
            $scResolution->save();
        } else {
            $arrScData = explode(",", $directional['resolution_set']);
            if (!empty($arrScData)) {
                foreach($arrScData as $val) {
                    $scResolution = new AcResolution();
                    $scResolution->ad_id = $aid;
                    $scResolution->resolution_id = $val;
                    $scResolution->save();
                }
            }
        }
    }

    public function checkForm(){
        $return = array();
        foreach($_POST as $key=>$val) {
            $$key = $val;
        }
        if ($aid<1) {
            $return['code'] = 0;
            $return['message'] = "请设置投放的广告";
            return $return;
        }
        if ($time_mode=="default") {
            if ($start_time=="") {
                $return['code'] = 0;
                $return['message'] = "投放开始时间不能为空。";
                return $return;
            }
            if ($set_endtime) {
                if ($end_time=="") {
                    $return['code'] = 0;
                    $return['message'] = "投放结束时间不能为空。";
                    return $return;
                }
                $sDate = explode("-", substr($start_time, 0, 10));
                $sTimes = explode(":", substr($start_time, 10, 5));
                $stime = mktime($sTimes[0], $sTimes[1], 0, $sDate[1], $sDate[2], $sDate[0]);
                $eDate = explode("-", substr($end_time, 0, 10));
                $eTimes = explode(":", substr($end_time, 10, 5));
                $etime = mktime($eTimes[0], $eTimes[1], 0, $eDate[1], $eDate[2], $eDate[0]);
                if ($etime<=$stime) {
                    $return['code'] = 0;
                    $return['message'] = "投放结束时间不能小于等于投放开始时间。";
                    return $return;
                }
            }
        } else {
            if (trim($gap_time)=="") {
                $return['code'] = 0;
                $return['message'] = "投放时间段不能为空。";
                return $return;
            }
        }
        // 计费
        if ($set_cost) {
            if ($price!="" && !is_numeric($price)) {
                $return['code'] = 0;
                $return['message'] = "计费价格必须是整数。";
                return $return;
            } else if ($set_cost_num) {
                if (!is_numeric($cost_num) || $cost_num<1 || $cost_num>1000000000) {
                    $return['code'] = 0;
                    $return['message'] = "计费数量请填写>0且≤1,000,000,000的整数。";
                    return $return;
                }
            }
        }
        //每日限制
        if ($set_limit_day) {
            if ($limit_day_num=="" || !is_numeric($limit_day_num) || $limit_day_num<1 || $limit_day_num>1000000000) {
                $return['code'] = 0;
                $return['message'] = "限制数量请填写>0且≤1,000,000,000的整数。";
                return $return;
            }
        }
        //独立用户访问限制
        if ($set_limit_one) {
            $arrOne = explode("&&", $limit_one);
            $limitOne = array();
            foreach($arrOne as $key=>$val) {
                $kv = explode("||",$val);
                $limitOne[$key]['mode'] = $kv[0];
                $limitOne[$key]['num'] = $kv[1];
            }
            $oneLen = count($limitOne);
            for($i=0; $i<$oneLen; $i++) {
                for($j=$i+1; $j<$oneLen; $j++) {
                    if ($limitOne[$i]['mode']==$limitOne[$j]['mode']) {
                        $return['code'] = 0;
                        $return['message'] = "独立用户访问已经设置此上限，请勿重复设置。";
                        return $return;
                    }
                }
                if (!is_numeric($limitOne[$i]['num']) || $limitOne[$i]['num']<1 || $limitOne[$i]['num']>1000000000) {
                    $return['code'] = 0;
                    $return['message'] = "独立用户访问限制数量请填写>0且≤1,000,000,000的整数。";
                    return $return;
                }
            }
        }
        $return['code'] = 1;
        return $return ;
    }
    
}