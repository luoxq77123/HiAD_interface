<?php

class MaterialText extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_text}}';
    }
	
	public function rules() {
        return array(
            array('text,click_link', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('size, corlor,style,float_corlor,float_style,click_link,monitor,monitor_link,target_window', 'safe', 'on' => 'add,edit')
        );
    }

    public function getInfoByMaterialIds($arrMaterialId) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('material_id', $arrMaterialId);
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach($data as $val) {
                $return[$val->material_id]['text'] = $val->text;
                $return[$val->material_id]['click_link'] = $val->click_link;
            }
        }
        return $return;
    }

    public function attributeLabels() {
        return array(
            'text' => '文字内容:',
            'size' => '文字大小:',
            'corlor' => '默认文字颜色:',
            'style' => '默认文字样式:',
            'float_corlor' => '悬停文字颜色:',
            'float_style' => '悬停文字样式:',
            'click_link' => '点击链接:',
            'monitor' => '设置第三方展现监控:',
            'monitor_link' => '监控链接:'
        );
    }

	public function getWindowOption(){
        return array(1 => '新窗口', 2 => '原窗口');
    }

}