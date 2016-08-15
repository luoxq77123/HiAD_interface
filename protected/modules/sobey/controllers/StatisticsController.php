<?php

/**
 * 统计控制器
 */
class StatisticsController extends BaseController {

    public function actions() {
        return array(
            'site' => 'sobey.controllers.statistics.SiteAction', //站点广告
            'app' => 'sobey.controllers.statistics.AppAction', //客户端广告
        );
    }
    
    public function actionGetStatList() {
        $this->renderPartial('getStatList');
    }
    
    public function actionGetAppStatList() {
        $this->renderPartial('getAppList');
    }
}