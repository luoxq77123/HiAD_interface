<?php

/**
 * 错误处理控制器。
 */
class SetAdAction extends CAction {
    
    public function run() {
        if(isset($_GET['token'])) {
            $user = User::model()->checkToken($_GET['token']);
        }
        $user = Yii::app()->session['user'];
        // 获取处理动作
        $do = isset($_REQUEST['do']) ? $_REQUEST['do'] : "";
        // 保存广告设置
        if ('save' == $do) {
            $msg = $this->_saveTempAdData();
            echo json_encode($msg);
            exit();
        } else if ('create' == $do) {
            // 清空广告数据缓存
            Ad::model()->cleanAdSession();
            // 排期投放接口 传参：do=create&scheduleid=scheduleid
            if (!empty($_REQUEST['scheduleid'])) {
                $scheduleId = $_REQUEST['scheduleid'];
                $this->setAdSessionBySchedule($scheduleId);
            }
            // 广告位投放接口
            if (!empty($_GET['positionId'])) {
                Ad::model()->setAdByPosition($_GET['positionId']);
            }
        } else if ('modify' == $do) {
            // 清空广告数据缓存
            Ad::model()->cleanAdSession();

            if (isset($_REQUEST['aid']) && $_REQUEST['aid'] > 0) {
                $return = $this->sessionAdData($_REQUEST['aid']);
                if ($return['code'] < 1) {
                    echo $return['message'];
                    exit();
                }
            } else {
                echo "广告修改参数不对，请返回到广告列表从新选择";
                exit();
            }
        }

        // 查看选择广告
        $ad = array();
        if(isset($_GET['adPosition'])) {
            $src = array("-a","-b","-c");
            $dist  = array("/","+","=");
            $txt  = str_replace($src, $dist, $_GET['adPosition']);
            $adInfo = unserialize(base64_decode($txt));
            if (isset($adInfo['ad'])) {
                $ad = $adInfo['ad'];
            }
        } else {
            $adInfo = array();
        }
        //$setAd = siteAd::model()->
        if (empty($ad)) {
            $ad = $this->initAdParam();
        }
        // 获取订单
        $orderList = Orders::model()->getOrderName(Yii::app()->session['user']['com_id']);
        $orderName[0] = "-请选择-";
        foreach ($orderList as $key => $val) {
            $orderName[$key] = $val;
        }
        //查看广告相关信息
        $siteAd = array();
        $siteAd = SiteAd::model()->findByAttributes(array('ad_id' => $ad['aid']));
        if($siteAd == ""){
            $siteAd = $this->_initPolicy();
        }
        // 广告播放器类型
        $playerCushion = Ad::model()->getPlayerCushion();
        // 插播广告展现方式
        $showType = Ad::model()->getShowType();
        // 投放策略参数
        $policy = array();
        // 精准定位
        $directional = array();
        if(!empty($_GET['positionId'])) {
            $position_id = $_GET['positionId'];
        } else {
            if(!empty($ad['position_id'])) {
                $position_id = $ad['position_id'];
            } else {
                $return['returnCode'] = '200';
                $return['returnDesc'] = '接口参数错误';
                return $return; 
            }
        }
        $set = array(
            'orderName' => $orderName,
            'ad' => $ad,
            'siteAd' => $siteAd,
            'playerCushion' => $playerCushion,
            'showType' => $showType,
            'position_id' => $position_id
        );
        $controller = Yii::app()->getController();
        $controller->renderPartial('setAd', $set);
    }

    // 初始化广告参数
    public function initAdParam() {
        $params = array(
            'aid' => "",
            'position_id' => "",
            'name' => "",
            'order_id' => 0,
            'description' => "",
            'cushion' => 1,
            'show_type' => 1,
            'show_time' => 20,
            'width' => 400,
            'height' => 300,
            'pos_x' => 0,
            'pos_y' => 0
        );
        return $params;
    }
    
    private function _initPolicy(){
        $adInfo = Yii::app()->session['create_ad_info'];
        $policy = array(
            'cushion' => 1,
            'show_type' => 1,
            'show_time' => 20
        );
        return $policy;
    }
    
    public function parseDirectional($directionalInfo){
        $directional = array();
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
    return $directional;
    }
    
    public function sessionAdData($aid) {
        $ad = new Ad();
        $adData = $ad->getOneById($aid);
        if (empty($adData)) {
            $return['code'] = 0;
            $return['message'] = "没有找到对应的广告";
            return $return;
        }
        $user = Yii::app()->session['user'];
        if ($user['com_id'] != $adData->com_id) {
            $return['code'] = 0;
            $return['message'] = "对不起，你没有权限修改广告 " . $adData->name;
            return $return;
        }

        $positioin = new Position();
        $data = $positioin->getPositionById($adData->position_id);
        $adInfo = array();
        $adInfo['ad'] = array(
            'aid' => $adData->id,
            'position_id' => $adData->position_id,
            'com_id' => $adData->com_id,
            'position_size' => $data->position_size,
            'position_name' => $data->name,
            'position_status' => $data->status,
            'ad_show' => $ad->getAdShowName($data->ad_type_id, $data->ad_show_id),
            'name' => $adData->name,
            'order_id' => $adData->order_id,
            'description' => $adData->description,
            'do' => 'modify'
        );

        Yii::app()->session['create_ad_info'] = $adInfo;

        $return['code'] = $aid;
        return $return;
    }

    // 根据排期参数投放广告
    public function setAdSessionBySchedule($scheduleid) {
        $arrData = Schedule::model()->getOneById($scheduleid);
        if (empty($arrData)) {
            return false;
        }

        $user = Yii::app()->session['user'];
        if ($user['com_id'] != $arrData->com_id) {
            $return['code'] = 0;
            $return['message'] = "对不起，你没有权限投放排期 " . $arrData->name;
            return $return;
        }
        $positioin = new Position();
        $data = $positioin->getPositionById($arrData->position_id);
        $adShow = Ad::model()->getAdShowName($data->ad_type_id, $data->ad_show_id);
        // 获取排期时间
        $scheduleTime = ScheduleTime::model()->getRowsByScheduleId($arrData->id);
        $policyTime = array(
            'time_mode' => 'default',
            'start_time' => date("Y-m-d 00:00", time() + 3600),
            'set_endtime' => 0,
            'end_time' => date("Y-m-d 23:59", time()),
            'gap_time' => "",
            'days' => 0,
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
            'ad_show' => $adShow,
            'cushion' => 1,
            'show_type' => 1,
            'show_time' => 20,
            'width' => 400,
            'height' => 300,
            'pos_x' => 0,
            'pos_y' => 0
        );
        if ($arrData->multi_time == 0) {
            $policyTime['start_time'] = date("Y-m-d H:i", $scheduleTime[0]->start_time);
            $policyTime['set_endtime'] = 1;
            $policyTime['end_time'] = date("Y-m-d H:i", $scheduleTime[0]->end_time);
        } else {
            $timeText = "";
            $days = 0;
            foreach ($scheduleTime as $val) {
                $timeText .= date("Y-m-d", $val->start_time) . " ~ " . date("Y-m-d", $val->end_time) . "\n";
                $days += ceil(($val->end_time - $val->start_time) / 86400);
            }
            $policyTime['time_mode'] = "";
            $policyTime['gap_time'] = $timeText;
            $policyTime['days'] = $days;
        }

        $adInfo = array();
        $adInfo['ad'] = array(
            'aid' => 0,
            'position_id' => $arrData->position_id,
            'com_id' => $arrData->com_id,
            'position_size' => $data->position_size,
            'position_name' => $data->name,
            'position_status' => $data->status,
            'ad_show' => $adShow,
            'name' => "",
            'order_id' => $arrData->orders_id,
            'description' => ""
        );
        $adInfo['policy'] = $policyTime;

        //Yii::app()->session['create_ad_info'] = $adInfo;
        $return['adPosition'] = base64_encode(serialize($adInfo));
        $return['code'] = 1;
        return $return;
    }
    // 临时保存广告位设置
    private function _saveTempAdData() {
        $return = $this->checkForm();
        if ($return['code'] == 0) {
            return $return;
        }
        $user = Yii::app()->session['user'];
        // 获取广告位
        /*$positionId = intval($_POST['pid']);
        $positioin = new Position();
        $data = $positioin->getPositionById($positionId);
        if (empty($data)) {
            $return['code'] = 0;
            $return['message'] = "没有找到对应的广告位";
            return $return;
        }*/
        
        $name = $_POST['name'];
        // 获取订单信息
        $orderId = $_POST['order_id'];
        $orders['id'] = $orderId;
        $orders['name'] = "";
        if ($orderId > 0) {
            $ordersInfo = Orders::model()->find(array(
                'select' => 'id, name',
                'condition' => 'id=:id',
                'params' => array(':id' => $orderId))
            );
            if (is_object($ordersInfo)) {
                $orders['name'] = $ordersInfo->name;
            }
        }
        $description = $_POST['description'];
        $position_id = $_POST['position_id'];
        // 保存数据到数据库
        $ad = new Ad();
        $adId = isset($_POST['aid']) ? intval($_POST['aid']) : 0;
        if ($adId > 0) {
            $attribe = array(
                'name' => $name,
                'description' => $description,
                'com_id' => $user['com_id'],
                'uid' => $user['uid'],
                'position_id' => $position_id,
                'position_name' => "",
                'order_id' => $orderId,
                'order_name' => $orders['name'],
                'ads_start_time' => time(),
                'ads_end_time' => time(),
                'post_time' => time()
            );
            $ad->updateAll($attribe, 'id=:id', array(':id' => $adId));
            Oplog::add();//添加日志
        } else {
            $ad->name = $name;
            $ad->ad_type_id = 1;
            $ad->description = $description;
            $ad->com_id = $user['com_id'];
            $ad->uid = $user['uid'];
            $ad->position_id = $position_id;//保存传过来的广告位
            //$ad->position_name = $data->name;
            $ad->order_id = $orderId;
            $ad->order_name = $orders['name'];
            $ad->post_time = time();
            $ad->save();
            $adId = Yii::app()->db->getLastInsertID();
            Oplog::add(); //添加日志
        }
        //保存广告表的相关信息
        $setAd = new SiteAd();
        $setAd->ad_id = $ad['id'];
        $setAd->cushion = $_POST['cushion'];
        $setAd->width = $_POST['width'];
        $setAd->height = $_POST['height'];
        $setAd->pos_x = $_POST['pos_x'];
        $setAd->pos_y = $_POST['pos_y'];
        if($_POST['cushion'] == 4) {
            $setAd->show_time = $_POST['show_time'];
            $setAd->show_type = $_POST['show_type'];
        }
        if ($setAd->validate()) {
            $setAd->posttime = time();
            if (!$setAd->save()) {
                echo "节点信息插入失败";
                exit;
            }
        }
        
        // 保存到缓存
        $adInfo = array();
        if (isset($_GET['adPosition']))
            $adInfo = unserialize($_GET['adPosition']);
        if (isset($adInfo['ad']['do'])) {
            $do = $adInfo['ad']['do'];
            $adInfo['ad'] = array(
                'aid' => $adId,
                'position_id' => $position_id,
                'com_id' => $data->com_id,
                'position_size' => $data->position_size,
                'position_name' => $data->name,
                'position_status' => $data->status,
                'ad_show' => $ad->getAdShowName($data->ad_type_id, $data->ad_show_id),
                'name' => $name,
                'order_id' => $orderId,
                'description' => $description,
                'cushion' => $cushion,
                'show_time' => $show_time,
                'show_type' => $show_type,
                'width' => $width,
                'height' => $height,
                'pos_x' => $pos_x,
                'pos_y' => $pos_y,
                'do' => $do
            );
        } else {
            $adInfo['ad'] = array(
                'aid' => $adId,
                'position_id' => $position_id,
                /*'com_id' => $data->com_id,
                'position_size' => $data->position_size,
                'position_name' => $data->name,
                'position_status' => $data->status,
                'ad_show' => $ad->getAdShowName($data->ad_type_id, $data->ad_show_id),*/
                'name' => $name,
                'order_id' => $orderId,
                'description' => $description,
                'cushion' => $_POST['cushion'],
                'width' => $_POST['width'],
                'height' => $_POST['height'],
                'pos_x' => $_POST['pos_x'],
                'pos_y' => $_POST['pos_y'],
                'show_time' => $_POST['show_time'],
                'show_type' => $_POST['show_type'],
            );
        }
        //Yii::app()->session['create_ad_info'] = $adInfo;
        $return['code'] = $adId;
        $srcer = base64_encode(serialize($adInfo));
        $src  = array("/","+","=");
        $dist = array("-a","-b","-c");
        $return['adPosition']  = str_replace($src, $dist, $srcer);
        //$return['adPosition'] = base64_encode(serialize($adInfo));
        return $return;
    }

    public function checkForm() {
        $return = array();
        //广告位
        /*if (!isset($_POST['pid'])) {
            $return['code'] = 0;
            $return['message'] = "参数错误";
            return $return;
        }*/
        //广告名称
        $name = $_POST['name'];
        if (trim($name) == "") {
            $return['code'] = 0;
            $return['message'] = "参数错误";
            return $return;
        }

        $return['code'] = 1;
        return $return;
    }

}