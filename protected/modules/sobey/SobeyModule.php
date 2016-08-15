<?php 
class SobeyModule extends CWebModule { 
    public $_assetsUrl;

    public function getAssetsUrl()
    {
        if($this->_assetsUrl===null)
            $this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.sobey.assets'), false, -1, YII_DEBUG);
        return $this->_assetsUrl;
    }

    public function setAssetsUrl($value)
    {
        $this->_assetsUrl=$value;
    }

    public function init() {
        $this->setImport(array(
            'sobey.models.*',
            'sobey.components.*',
            'sobey.helpers.*',
        ));
    }
    
    public function beforeControllerAction($controller, $action) {
        if(parent::beforeControllerAction($controller, $action)) {
            return true;
        }
        else
            return false;
    }

} 
?>