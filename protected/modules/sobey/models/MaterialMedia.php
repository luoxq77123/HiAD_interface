<?php

class MaterialMedia extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_media}}';
    }
    
    public function getTemplateMode(){
        return array(
            2 => '从已有模板中选择',
            1 => '从推荐模板中选择 (推荐)',
            0 => '不使用广告物料模板'
        );
    }

    public function getInfoByMaterialIds($arrMaterialId) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('material_id', $arrMaterialId);
        $criteria->select = 'material_id,content';
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach($data as $val) {
                $return[$val->material_id]['content'] = $val->content;
            }
        }
        return $return;
    }
    
    public function getByMaterialMedia($materialMedia_id) {
        $media_id = explode(",",$materialMedia_id);
        $criteria = new CDbCriteria();
        $criteria->select = 'material_id,content'; 
        $criteria->addInCondition('material_id', $media_id);
        $materialMedia = $this->findAll($criteria);
        $return = array();
        if (!empty($materialMedia)) {
            foreach($materialMedia as $val) {
                $return[$val->material_id]['materialSrc'] = $val['content'];
            }
        }
        return $return;
    }
    
    public function getParamsByMaterialIds($materialMedia_id) {
        $media_id = explode(",",$materialMedia_id);
        $criteria = new CDbCriteria();
        $criteria->select = 'material_id,template_params';
        $criteria->addInCondition('material_id', $media_id);
        $materialMedia = $this->findAll($criteria);
        $return = array();
        if (!empty($materialMedia)) {
            foreach($materialMedia as $val) {
                $return[$val->material_id]['params'] = unserialize($val['template_params']);
            }
        }
        return $return;
    }
}