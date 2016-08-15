<?php

class AppAdDirectionalWidget extends CWidget {

    public function run() {

        // 获取定向类型
        $directionalType = AppAd::model()->getDirectionalType();
        // 获取定向模式
        $directionalMode = AppAd::model()->getDirectionalMode();
        // 投放策略参数
        $adInfo = Yii::app()->session['create_ad_info'];
        $directional = array();

        $set = array(
            'directionalType' => $directionalType,
            'directionalMode' => $directionalMode,
            'directional' => $directional
        );

        $this->render('appAdDirectional', $set);
    }
}