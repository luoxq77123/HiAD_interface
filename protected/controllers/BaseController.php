<?php

/**
 * 基础controller
 */
class BaseController extends CController {
    public $params = array();
    public $return = array('returnCode' => 100, 'returnDesc' =>'成功');

    function __construct($id, $module = null) {
        parent::__construct($id, $module);
        if (isset($_POST['appId'])) {
            $this->params = json_decode($_POST['parameter'],true);
            //$app = App::model()->getDataById($_POST['appId']);
            //$this->params = @json_decode(PassportHelper::decrycode($_POST['parameter'], $app->app_key), true);
        } else if (isset($_POST['parameter'])) {
            $this->params = @json_decode($_POST['parameter'], true);
        } else if (isset($_GET['callback']) && isset($_GET['positionId'])) {
            $this->params = $_GET;
        }
    }
    
    /**
     * 时间字符串转换时间戳
     */ 
    public function mstrToTime($strTime) {
        $times = explode(" ", $strTime);
        $date = explode("-", $times[0]);
        $time = explode(":", $times[1]);
        $time[2] = isset($time[2]) ? $time[2] : 0;
        return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }
    
    /**
     * 检查客户端传入参数是否合法
     */
    protected function isPraramerValue($content, $array) {
        for ($i = 0; $i < count($array); $i++) {
            if (!isset($content[$array[$i]])) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查是否有符合条件的数据
     */
    protected function checkArrData($arrData) {
        if (empty($arrData)) {
            $this->return['returnCode'] = 101;
            $this->return['returnDesc'] = '没有找到符合条件的广告';
            return false;
        }
        return true;
    }

    //统一返回接口
    public function AppResponse($dataArr=array(),$code=100)
    {
        //错误编码
        $data['returnCode'] = $code;
        //错误描述
        $data['returnDesc'] = $this->codeMsg($code);
        //返回内容
        $data['returnData'] =$dataArr;
        echo json_encode($data);exit;
    }

    //获取错误编码对应的错误描述
    public function codeMsg($code)
    {
       $msgArr = array(
           100=>'返回成功',
           101=>'没有找到符合条件的广告',
           200=>'缺少必要参数',
       );
       return isset($msgArr[$code])?$msgArr[$code]:'';
    }
}