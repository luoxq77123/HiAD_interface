<?php

class MaterialVideo extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material_video}}';
    }
    
    public function rules() {
        return array(
            array('url', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('monitor_video,monitor_video_type,click_link,reserve,reserve_pic_url,reserve_pic_link,monitor,monitor_link,target_window,video_x,video_y,videopic_x,videopic_y', 'safe', 'on' => 'add,edit')
        );
    }

    public function getInfoByMaterialIds($arrMaterialId) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('material_id', $arrMaterialId);
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach($data as $val) {
                $return[$val->material_id]['url'] = $val->url;
                $return[$val->material_id]['click_link'] = $val->click_link;
                $return[$val->material_id]['x'] = $val->video_x;
                $return[$val->material_id]['y'] = $val->video_y;
            }
        }
        return $return;
    }

    public function attributeLabels() {
        return array(
            'url' => 'video文件:',
            'monitor_video' => '允许对video进行点击监控:',
            'click_link' => 'video点击链接:',
            'reserve' => 'video无法展现时显示后备图片:',
            'reserve_pic_url' => '备用图片:',
            'reserve_pic_link' => '图片点击链接:',
            'monitor' => '设置第三方展现监控:',
            'monitor_link' => '监控链接监控链接:'
        );
    }

    public function getWindowOption(){
        return array(1 => '新窗口', 2 => '原窗口');
    }
    public function getFlashTypeOption(){
        return array(1 => '普通', 2 => 'clickTAG');
    }
    public function getFlashbgOption(){
        return array(1 => '透明', -1 => '不透明');
    }
    
    public function getByMaterialVideo($materialVideo_id) {
        $video_id = explode(",",$materialVideo_id);
        $criteria = new CDbCriteria();
        //$criteria->select = 'id,ad_id,cushion,com_id,uid,width,height,pos_x,pos_y,material';
        $criteria->addInCondition('material_id', $video_id);
        $materialVideo = $this->findAll($criteria);
        $return = array();
        if (!empty($materialVideo)) {
            foreach($materialVideo as $val) {
                $return[$val->material_id]['materialSrc'] = $val['url'];
            }
        }
        return $return;
    }
}