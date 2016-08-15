<?php

/**
 * 添加日志
 */
class Oplog extends CComponent {

    public function init(){
        
    }
    /**
     * 添加日志
     * @return boolean 
     */
    public static function add() {
        $controller = Yii::app()->getController()->getId();
        $action = Yii::app()->getController()->getAction()->id;
        $acaList = Aca::model()->getAcaMap();
        if (isset($acaList[$controller][$action])) {
            $url = $_SERVER['REQUEST_URI'];
            $network = new Network;
            $ip = $network->getIP();

            $user = Yii::app()->session['user'];
            $oplog = new Oplogs('add');
            $oplog->aca_id = $acaList[$controller][$action];
            $oplog->url = $url;
            $oplog->uid = $user['uid'];
            $oplog->com_id = $user['com_id'];
            $oplog->ip = $ip;
            $oplog->createtime = time();
            return $oplog->save();
        }
    }

}