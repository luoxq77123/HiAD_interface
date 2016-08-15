<?php
class InterfaceController extends CController {
    
    public function actionIndex() {
        $return = array();
        $interfaces = array(
            'bindAccount' => 'account/bindAccount',
            'modifyAccount' => 'account/modifyAccount',
            'adminAccount' => 'account/adminAccount',
            'deleteAccount' => 'account/deleteAccount',
            'getAdPositionData' => 'adPosition/getAdPositionData',
            'getAdPositionList' => 'adPosition/getAdPositionList',
            'getAdMaterial' => 'adPosition/getAdMaterial',
            'getCutInAd' => 'adSite/getCutInAd'
        );
        if (!Yii::app()->request->isPostRequest) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '请求方式错误';
        } else if (!isset($_POST['method']) || !isset($_POST['parameter']) || !isset($_POST['ITFV'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
        } else if (!array_key_exists($_POST['method'], $interfaces)) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口名错误';
        } else {
            $module = Yii::app()->getModule('sobey');
            $co = Yii::app()->createController($interfaces[$_POST['method']], $module);
            list($controller, $action) = $co;
            $return = $controller->$action();
        }
        //echo "<pre>";
        //var_dump($return); exit; //调试代码，线上需注销
        // 返回的中文不转码 服务器上有问题
        //echo urlencode(preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", json_encode($return)));exit;
        echo json_encode($return); exit;
    }
    
    public function actionUi() {
        $interfaces = array(
            'addPosition' => 'sobey/position/add',
            'addAd' => 'sobey/ad/setAd',
            'addMaterial' => 'sobey/material/add'
        );
        $error = '';
        if (!isset($_GET['method']) || !isset($_GET['token'])) {
            $error = '接口参数错误';
        } else if (!array_key_exists($_GET['method'], $interfaces)) {
            $error = '接口名错误';
        } else if (!User::model()->checkToken($_GET['token'])) {
            $error = '用户信息验证未通过';
        }
        if ($error != '') {
            Yii::import("application.modules.sobey.helpers.Common");
            echo Common::showMsg($error);
        } else {
            $module = Yii::app()->getModule('sobey');
            Yii::app()->runController($interfaces[$_GET['method']]);
        }
    }
}