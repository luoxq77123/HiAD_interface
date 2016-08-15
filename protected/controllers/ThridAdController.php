<?php

/**
 * Himi广告控制器
 */
class ThridAdController extends CController {

    /**
     * 获取广告位信息
     */
    public function actionIndex() {
        if (isset($_GET['pid'])) {
            $Position = Position::model()->findByPk($_GET['pid']);
            if (!$Position) {
                echo "广告位不存在";
                exit;
            } else if ($Position->status<1) {
                echo "广告位停止使用";
                exit;
            }
            // 添加统计代码
            $cntTime = time();
            $Network = new NetworkComponent;         
            $ip = ip2long($Network->getIP());
            $date = date("Ymd", $cntTime);
            $ThridStat = new ThridStat($date);
            $ThridStat->position_id = $Position['id'];
            $ThridStat->ip = $ip;
            $ThridStat->com_id = $Position['com_id'];
            $ThridStat->create_time = $cntTime;
            $ThridStat->save();
            $statId = $ThridStat->id;
            // 将数据库选择到主库
            CActiveRecord::$db = Yii::app()->db;
            $info = $this->_get360UnionUrl($statId, $Position['position_size'], $cntTime);

            echo '
<html>
  <head>
  </head>
  <body style="margin:0;border:0;">
    <iframe frameborder=\'0\' height=\'100%\' width=\'100%\' style=\'overflow:hidden;border:none;\' src=\''.$info.'\'></iframe>
  </body>
</html>
            ';
            exit;
        }
        exit;
    }

    function actionStatInfo(){
        $params = @json_decode($this->decrycode($_GET['info'], "ADMS-THRID-AD"));
        $db_name = date('Ymd', $params->time);
        $model = ThridStat::model($db_name)->findByPk($params->statId);
        if ($model) {
            $model->is_click = 1;
            $model->click_count = $model->click_count+1;
            $model->save();
        } 
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        exit;
    }

    /**
     * 生成360联盟广告链接
     */
    private function _get360UnionUrl($statId, $size, $cntTime) {
        $size = explode("*", $size);
        $config = Config::model()->getConfigs();
        $arrUrlInfo = parse_url($config['360union_ad_url']);
        parse_str($arrUrlInfo['query']);
        $hiadInfo = array("statId" => $statId, "time" => $cntTime);
        $hiadInfo = self::encrycode(json_encode($hiadInfo), "ADMS-THRID-AD");
        
        return "http://".$arrUrlInfo['host'].$arrUrlInfo['path']."?sx=".$size[0]."&sy=".$size[1]."&c=sobey&qihoo_id=".$qihoo_id."&uid=".$uid."&SOBEY_AD=".$hiadInfo;
    }

    /**
    * 字符串加密和解密
    */
    function decrycode($txt, $encrypt_key){
        $txt = self::base64DnCode(base64_decode($txt));
        return self::crycode($txt, $encrypt_key);
    }

    function encrycode($txt, $encrypt_key){
        return self::base64EnCode(base64_encode(self::crycode($txt, $encrypt_key)));
    }

    function crycode($txt, $encrypt_key){
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }
    
    /**
     * base64编码替换特殊字符
     */
    function base64EnCode($string) {
        $src  = array("/","+","=");
        $dist = array("-a","-b","-c");
        return str_replace($src, $dist, $string);
    }

    /**
     * base64编码反替换特殊字符
     */
    function base64DnCode($string) {
        $src = array("-a","-b","-c");
        $dist  = array("/","+","=");
        return str_replace($src, $dist, $string);
    }
}