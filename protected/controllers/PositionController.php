<?php

class PositionController extends BaseController {

    function __construct($id, $module = null) {
        parent::__construct($id, $module);
    }

    public function positionData() {
        $this->return['returnCode'] = 200;
        $this->return['returnDesc'] = '缺少必要参数';
        $param_arr = array('appKey');
        if ($this->isPraramerValue($this->params, $param_arr)) {
            $this->return['returnCode'] = 100;
            $this->return['returnDesc'] = '返回成功';
            $this->return['returnData'] = $this->_getReturnData();
        }
        return $this->return;
    }

    // 根据vms栏目id和播放器id 获取广告位
    public function videoPosition() {
        $this->return['returnCode'] = 200;
        $this->return['returnDesc'] = '缺少必要参数';
        $param_arr = array('catalogId','playerId');
        if ($this->isPraramerValue($this->params, $param_arr)) {
            $this->return['returnCode'] = 100;
            $this->return['returnDesc'] = '返回成功';
            //$this->return['returnData'] = $this->_getReturnData();
            $arrCatalogId = explode(",", $this->params['catalogId']);
            $playerId = $this->params['playerId'];
            $criteria = new CDbCriteria;
            $criteria->addInCondition('catalog_id', $arrCatalogId);
            //$criteria->addInCondition('channel_id', $arrCatalogId);
            $catalog = VpCatalog::model()->findAll($criteria);
            //$catalog = VpChannel::model()->findAll($criteria);
            $data = array();
            if (!empty($catalog)) {
                foreach($arrCatalogId as $cid) {
                    foreach($catalog as $one) {
                        //if($one['catalog_id'] == $cid) {
                        //if($one['channel_id'] == $cid) {
                            $data['catalogId'] = $one['position_id'];
                          //  break;
                        //}
                    }
                    if (isset($data['catalogId']))
                        break;
                }
                $data['playerId'] = 0;
            } else {
                $data['catalogId'] = 0;
                $player = VpPlayer::model()->findByAttributes(array('player_id'=>$playerId));
                if ($player) {
                    $data['playerId'] = $player->position_id;
                } else {
                    $data['playerId'] = 0;
                }
            }
            $this->return['returnData'] = $data;
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
        return $data;
    }

    /**
     * 获取广告位属性
     */
    private function _getPositionAttr() {
        $comId = $_POST['appId'];
        // 广告位信息
        $position = Position::model()->getPosition($comId);
        if (!$this->checkArrData($position)) {
            return array();
        }
        // 组合数据
        $data = array();
        $index = 0;
        foreach($position as $key => $val) {
            $data[$index]['positionId'] = $key;
            $data[$index]['positionName'] = $val;
            $index ++;
        }
        return $data;
    }
}