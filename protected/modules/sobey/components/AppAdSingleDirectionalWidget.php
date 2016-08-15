<?php

class AppAdSingleDirectionalWidget extends CWidget {
    // 获取投放策略参数
    public $directional;
    // 精准定向类型
    public $directionalType;

    public function init()
    {
        if($this->directional===null)
            $this->directional = array();
        if($this->directionalType===null)
            $this->directionalType = "connect";
    }

    public function run() {
        $lists = array();
        $directional = array();
        $directional['type'] = $this->directionalType;
        switch($this->directionalType) {
            case 'connect':
                $lists = AppAdConnect::model()->getList();
                $directional['typeName'] = "网络连接类型";
                break;
            case 'brand':
                $lists = AppAdBrand::model()->getList();
                $directional['typeName'] = "手机品牌";
                break;
            case 'platform':
                $lists = AppAdPlatform::model()->getList();
                $directional['typeName'] = "投放平台";
                break;
        }
        // 获取设置参数
        $connectSet = (!empty($this->directional[$this->directionalType.'_set']))? explode("," ,$this->directional[$this->directionalType.'_set']) : array();

        $list = array();
        $content = array();
        foreach($lists as $key=>$val){
            $list[$key]['name'] = $val;
            $list[$key]['selected'] = false;
            if (in_array($key, $connectSet)) {
                $list[$key]['selected'] = true;
                $content[$key] = $val;
            }
        }

        $set = array(
            'list' => $list,
            'content' => $content,
            'directional' => $directional
        );

        $this->render('appAdSingleDirectional', $set);
    }
}