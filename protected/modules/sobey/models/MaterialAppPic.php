<?php

class MaterialAppPic extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_apic}}';
    }

    public function rules() {
        return array(
            array('url,click_link', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('description,click_link,pic_x,pic_y', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
           'url' => '图片文件:',
           'description' => '图片描述:',
            'click_link' => '点击链接:'
        );
    }


}