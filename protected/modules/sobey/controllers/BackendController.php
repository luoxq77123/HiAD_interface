<?php

/**
 * 后端操作页面首页
 */
class BackendController extends BaseController {
    
    public function actionIndex(){ 
        $this->checkLogin();
        
        $this->layout = 'backend';
        $this->render('index');
    }
}