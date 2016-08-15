<?php
class DataServiceController extends CController {
    
    public function actionIndex() {

        $return = array();
        $interfaces = array(
            'getPlayerAdAPP'=>'appAd/adApp',
            'getAppAd' => 'appAd/adInfo',
            'getPlayerAd' => 'playerAd/adInfo',
            'getAdPosition' => 'position/positionData',
            'getVideoPosition' => 'position/videoPosition'
        );

        //app广告访问接口
        //www.hiad-interface.dev/DataService/?ITFV=1&parameter={"positionId": 79,"time": 123123123,"clientInfo": "{\"appType\":\"ios\",\"connect\":\"\\u79fb\\u52a8\",\"brand\":\"\\u82f9\\u679c\",\"platform\":\"iphone\",\"screenWidth\":100,\"screenHeight\":100}"}&method=getAppAd&appId=1&appkey=1184ee5c4989b41dbef3d8be0ad30dbb
        //echo json_encode(array('appType'=>'ios','connect'=>'移动','brand'=>'苹果','platform'=>'iphone','screenWidth'=>100,'screenHeight'=>100));exit;
        //echo json_encode(array('positionId'=>25,'time'=>123123123,'clientInfo'=>'{"appType":"ios","connect":"\u79fb\u52a8","brand":"\u82f9\u679c","platform":"iphone","screenWidth":100,"screenHeight":100}'));exit;
        //echo PassportHelper::decrycode('RT@@P@TWTDQ','1184ee5c4989b41dbef3d8be0ad30dbb');exit;
        //echo PassportHelper::crycode('abssdfsdfw312312sdf','1184ee5c4989b41dbef3d8be0ad30dbb');exit;

        $_POST = $_REQUEST;
         //新测试代码
        if(!isset($_POST['ITFV'])){
            $co = Yii::app()->createController($interfaces[$_POST['method']]);
            list($controller, $action) = $co;
            $return = $controller->$action();
            echo json_encode($return);
            exit;
        }

        //老接口代码
        if (isset($_POST) || empty($_POST)) {
            $_POST = $_GET;
        }
        if (Yii::app()->request->isPostRequest) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '请求方式错误';
        } else if (!isset($_POST['method']) || !isset($_POST['parameter']) || !isset($_POST['ITFV'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
        } else if (!array_key_exists($_POST['method'], $interfaces)) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口名错误';
        } else if (!$this->_checkParams()){
            $return['returnCode'] = '201';
            $return['returnDesc'] = '接口参数校验错误，系统拒绝处理';
        } else {
            $co = Yii::app()->createController($interfaces[$_POST['method']]);
            list($controller, $action) = $co;
            $return = $controller->$action();
        }
        //var_dump($return['returnData']); exit; //调试代码，线上需注销
        echo json_encode($return); exit;
    }
    
    public function actionJsonp(){
        $return = array();
        $interfaces = array(
            'getPlayerAd' => 'playerAd/adInfo'
        );
        if (!isset($_GET['callback']) || !isset($_GET['dataType']) || !isset($_GET['method']) || !isset($_GET['positionId']) || !isset($_GET['ITFV'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
        } else if (!array_key_exists($_GET['method'], $interfaces)) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口名错误';
        } else if ($_GET['dataType']!='jsonp'){
            $return['returnCode'] = '201';
            $return['returnDesc'] = '接口参数校验错误，系统拒绝处理';
        } else {
            $co = Yii::app()->createController($interfaces[$_GET['method']]);
            list($controller, $action) = $co;
            $return = $controller->$action();
        }
        //var_dump($return); exit; //调试代码，线上需注销
        echo $_GET['callback'], '([', json_encode($return), '])'; exit;
    }
    
    /**
     * 广告点击链接信息存入统计信息
     * @sid   统计表id
     * @type  广告类型 分site、app
     * @date  广告获取日期 以方便查询表 如20121212
     * @href  广告点击链接地址
     */
    public function actionStat() {
        $params = $this->_parseParams();
        if (empty($params) || empty($params['sid']) || empty($params['type']) || empty($params['time']) || empty($params['href'])) {
            exit;
        }
        if ($params['type'] == 'site') {
            SiteStatistics::model()->addClickStatLog($params);
        } else if ($params['type'] == 'app') {
            AppStatistics::model()->addClickStatLog($params);
        }
        header("location:".$params['href']);
    }

    // 公共必填参数校验
    private function _checkParams(){
        switch($_POST['method']) {
        case 'getAppAd': // 客户端广告获取
            if (empty($_POST['appId'])) {
                return false;
            }
            $reData = App::model()->getDataById($_POST['appId']);
            if (empty($reData)) {
                return false;
            }
            return $this->_checkEncryptParams($reData->app_key);
        case 'getPlayerAd': // 播放器广告获取
            $params = @json_decode($_POST['parameter'], true);
            return isset($params['time']) && isset($params['isDebug']);
        case 'getVideoPosition': // 播放器广告获取
            $params = @json_decode($_POST['parameter'], true);
            return isset($params['time']) && isset($params['isDebug']);
        case 'getAdPosition': // cms广告位获取
            if (empty($_POST['appId'])) {
                return false;
            }
            $reData = Company::model()->getComById($_POST['appId']);
            if (empty($reData)) {
                return false;
            }
            return $this->_checkEncryptParams($reData['com_key']);
        }
        return false;
    }
    
    private function _checkEncryptParams($key) {
        return true;
        $params = @json_decode(PassportHelper::decrycode($_POST['parameter'], $key), true);
        return isset($params['appKey']) && ($params['appKey']==$key) && isset($params['time']) && isset($params['isDebug']);
    }
    
    // 解析参数 解析_GET或取得参数
    private function _parseParams(){
        //$_GET['p']=>type=site&sid=1&time=1467907962&href=http://www.baidu.com
        $params = array();
        if (!isset($_GET['p'])) {
            return $params;
        }
        $param = base64_decode($_GET['p']);
        $arrTemp = explode("&", $param);
        foreach($arrTemp as $val) {
            $temp = explode("=", $val);
            if (count($temp)!=2) {
                return array();
            }
            $params[$temp[0]] = $temp[1];
        }
        return $params;
    }
}