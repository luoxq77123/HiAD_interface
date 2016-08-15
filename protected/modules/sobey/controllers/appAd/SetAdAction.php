<?php

/**
 * 错误处理控制器。
 */
class SetAdAction extends CAction {

    public function run() {
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
        $adInfo = Ad::model()->getAdSession();
        if (isset($adInfo['ad'])) {
            $ad = $adInfo['ad'];
        }
        if (empty($ad)) {
            $ad = $this->initAdParam();
        }

        // 获取订单
        $orderList = Orders::model()->getOrderName(Yii::app()->session['user']['com_id']);
        $orderName[0] = "-请选择-";
        foreach ($orderList as $key => $val) {
            $orderName[$key] = $val;
        }

        $set = array(
            'orderName' => $orderName,
            'ad' => $ad
        );

        $controller = $this->getController();
        $controller->renderPartial('setAd', $set);
    }

    // 初始化广告参数
    public function initAdParam() {
        $params = array(
            'aid' => "",
            'position_id' => "",
            'name' => "",
            'order_id' => 0,
            'description' => ""
        );
        return $params;
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
            'ad_show' => Ad::model()->getAdShowName($data->ad_type_id, $data->ad_show_id),
            'name' => "",
            'order_id' => $arrData->orders_id,
            'description' => ""
        );
        $adInfo['policy'] = $policyTime;

        Yii::app()->session['create_ad_info'] = $adInfo;

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
        $positionId = intval($_POST['pid']);
        $positioin = new Position();
        $data = $positioin->getPositionById($positionId);
        if (empty($data)) {
            $return['code'] = 0;
            $return['message'] = "没有找到对应的广告位";
            return $return;
        }

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

        // 保存数据到数据库
        $ad = new Ad();
        $adId = isset($_POST['aid']) ? intval($_POST['aid']) : 0;

        if ($adId > 0) {
            $attribe = array(
                'name' => $name,
                'description' => $description,
                'com_id' => $user['com_id'],
                'uid' => $user['uid'],
                'position_id' => $data->id,
                'position_name' => $data->name,
                'order_id' => $orderId,
                'order_name' => $orders['name'],
                'post_time' => time()
            );
            $ad->updateAll($attribe, 'id=:id', array(':id' => $adId));
            Yii::app()->oplog->add(); //添加日志
        } else {
            $ad->name = $name;
            $ad->ad_type_id = 2;
            $ad->description = $description;
            $ad->com_id = $user['com_id'];
            $ad->uid = $user['uid'];
            $ad->position_id = $data->id;
            $ad->position_name = $data->name;
            $ad->order_id = $orderId;
            $ad->order_name = $orders['name'];
            $ad->post_time = time();
            $ad->save();
            $adId = Yii::app()->db->getLastInsertID();
            Yii::app()->oplog->add(); //添加日志
        }

        // 保存到缓存
        $adInfo = array();
        if (isset(Yii::app()->session['create_ad_info']))
            $adInfo = Yii::app()->session['create_ad_info'];
        if (isset($adInfo['ad']['do'])) {
            $do = $adInfo['ad']['do'];
            $adInfo['ad'] = array(
                'aid' => $adId,
                'position_id' => $data->id,
                'com_id' => $data->com_id,
                'position_size' => $data->position_size,
                'position_name' => $data->name,
                'position_status' => $data->status,
                'ad_show' => $ad->getAdShowName($data->ad_type_id, $data->ad_show_id),
                'name' => $name,
                'order_id' => $orderId,
                'description' => $description,
                'do' => $do
            );
        } else {
            $adInfo['ad'] = array(
                'aid' => $adId,
                'position_id' => $data->id,
                'com_id' => $data->com_id,
                'position_size' => $data->position_size,
                'position_name' => $data->name,
                'position_status' => $data->status,
                'ad_show' => $ad->getAdShowName($data->ad_type_id, $data->ad_show_id),
                'name' => $name,
                'order_id' => $orderId,
                'description' => $description
            );
        }
        Yii::app()->session['create_ad_info'] = $adInfo;

        $return['code'] = $adId;
        return $return;
    }

    public function checkForm() {
        $return = array();
        //广告位
        if (!isset($_POST['pid'])) {
            $return['code'] = 0;
            $return['message'] = "参数错误";
            return $return;
        }
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