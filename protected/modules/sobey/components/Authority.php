<?php

/**
 * 权限检查组件
 */
class Authority extends CComponent {
    private $NOTLOGIN = -1;
    private $FAILED = -2;
    private $PASS = 1;

    public function init() {
        
    }

    /**
     * 检查是否登陆
     * @return boolean 
     */
    function isLogin() {
        return isset(Yii::app()->session['user']) ? $this->PASS : $this->NOTLOGIN;
    }

    /**
     * 检查用户操作权限
     * @return boolean 
     */
    function checkAca($controller, $action) {
        $pass = $this->PASS;
        $acaList = Aca::model()->getAcaMap();
        
        if(isset($acaList[$controller][$action])){
            if($this->isLogin()){
                $user = Yii::app()->session['user'];
                if($user['role_id'] != 'super'){
                    $role_acas = RoleAca::model()->getAcaIdsByRole($user['role_id']);
                    if(!in_array($acaList[$controller][$action], $role_acas)){
                        $pass = $this->FAILED;
                    }
                }
            }else{
                $pass = $this->NOTLOGIN;
            }
        }
        return $pass;
    }

    /**
     * 获取状态值
     * @param string $name
     * @return int 
     */
    public function getStatus($name){
        return $this->$name;
    }
}