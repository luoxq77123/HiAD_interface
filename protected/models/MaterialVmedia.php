<?php

class MaterialVmedia extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_vmedia}}';
    }
    
    public function rules() {
        return array(
            array('template_params', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'template_id' => '广告物料模板:',
            'content' => '代码:'
        );
    }

    public function getInfoByMaterialIds($arrMaterialId) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('material_id', $arrMaterialId);
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach($data as $val) {
                $return[$val->material_id]['content'] = $val->content;
            }
        }
        return $return;
    }

    public function getTemplateMode(){
        return array(
            2 => '从已有模板中选择',
            1 => '从推荐模板中选择 (推荐)',
            0 => '不使用广告物料模板'
        );
    }
    
    // 生成统计链接
    public function makeStatUrl($url){
        //$setting = Setting::model()->getSettings();
        //$P = new PassportComponent;
        //$newUrl = $setting['INTERFACE_URL'].'/Himi/gotoURL?data=' . $P->passport_encrypt($url, 'HIMIAD');
        // 需重新设计富媒体点击统计（因为这个统计链接是提前生成好的，没有对应的stat_id）
        $newUrl = $url;
        return $newUrl;
    }
}