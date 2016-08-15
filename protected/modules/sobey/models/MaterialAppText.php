<?php

class MaterialAppText extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_atext}}';
    }

    public function rules() {
        return array(
            array('text,click_link', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('size, color,click_link', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'text' => '文字内容:',
            'size' => '文字大小:',
            'color' => '默认文字颜色:',
            'click_link' => '点击链接:'
        );
    }


}