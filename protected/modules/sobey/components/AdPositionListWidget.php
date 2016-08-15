<?php

class AdPositionListWidget extends CWidget {

    // 获取内容地址
    public $rote;
    // 设置每页条数限制组
    public $arrPageSize;
    // 广告类型
    public $adTypeId;
    // 请求方式是否是ajax
    public $ajaxRequest;

    public function init() {
        if ($this->rote === null)
            $this->rote = 'ad/getAdPositionList';
        if ($this->arrPageSize === null)
            $this->arrPageSize = array(10 => 10, 20 => 20, 50 => 50);
        if ($this->adTypeId === null)
            $this->adTypeId = 1;
        if ($this->ajaxRequest === null)
            $this->ajaxRequest = 1;
    }

    public function run() {
        $user = Yii::app()->session['user'];
        // 获取广告位列表
        $_GET['status'] = 1;
        $_GET['ajaxRequest'] = $this->ajaxRequest;
        $positionList = Position::model()->getPositionList($_GET, 3, $this->adTypeId);
        $spList = $positionList['list'];
        $pager = $positionList['pager'];
        $pager->route = $this->rote;

        // 搜索下拉
        if ($this->adTypeId == 1) {
            $site = Site::model()->getSitesByComId(Yii::app()->session['user']['com_id']);
        } else if ($this->adTypeId == 2) {
            $site = App::model()->getAppsByComId(Yii::app()->session['user']['com_id']);
        }
        $adShowList = AdShow::model()->getPositionAdShows($this->adTypeId);
        $adShows[0] = '-请选择-';
        foreach ($adShowList as $key => $val) {
            $adShows[$key] = $val;
        }
        $usedSize = Position::model()->getUsedSize(Yii::app()->session['user']['com_id']);
        $usedSize = array('' => '-请选择-') + $usedSize;

        $set = array(
            'spList' => $spList,
            'adShows' => $adShows,
            'pages' => $pager,
            'sites' => $site,
            'usedSize' => $usedSize,
            'adTypeId' => $this->adTypeId
        );

        $this->render('adPositionList', $set);
    }

}