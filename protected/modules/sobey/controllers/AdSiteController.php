<?php
class AdSiteController extends CController {
    private $_params;

    function __construct(){
        $this->_params = json_decode(stripslashes($_POST['parameter']), true);
    }
    
    /**
    *查找插播广告、物料
    */
    public function getCutInAd(){
		$return = array();
        if (!isset($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        // 验证用户
        if (!User::model()->checkToken($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '用户验证失败';
            return $return;
        }
        $user = User::model()->getOneByToken($this->_params['verifyCode']);
        $sortField = (isset($this->_params['sortField']) && $this->_params['sortField']!='')? $this->_params['sortField'] : 'post_time';
        if($sortField == "createtime") {
            $sortField = 'post_time';
        }
        $sort = (isset($this->_params['sort']) && $this->_params['sort']!='')? $this->_params['sort'] : 'DESC';
        $criteria = new CDbCriteria();
        $criteria->order = 'com'.".".$sortField." ".$sort;
        $criteria->with = 'SiteAd';
        $criteria->alias = 'com';
        $criteria->select = 'id, name,  ads_start_time, ads_end_time, status, order_id, order_name';
        $criteria->addColumnCondition(array('com.com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('com.ad_type_id' => 1));
        $criteria->addColumnCondition(array('cushion' => 4));
        
        if (isset($this->_params['status']) && $this->_params['status']) {
            $criteria->addColumnCondition(array('com.status' => $this->_params['status']));
        } else {
            $this->_params['status'] = 1;
            $criteria->addColumnCondition(array('com.status' => $this->_params['status']));
        }
        if (isset($this->_params['keyWord']) && $this->_params['keyWord']) {
           $criteria->addSearchCondition('com.name',urldecode($this->_params['keyWord']));
        }
        // 1
        $count = Ad::model()->count($criteria);
        $pager = new CPagination($count);
        $pageSize = (isset($this->_params['pageSize']) && $this->_params['pageSize']) ? $this->_params['pageSize'] : 10;
        $pageNum = (isset($this->_params['pageNum']) && $this->_params['pageNum']) ? $this->_params['pageNum'] : $pager->validateCurrentPage;
        $pager->pageSize = $pageSize;
        $page->pageVar = $pageNum;
        $pager->applyLimit($criteria);
        $adlist = Ad::model()->findAll($criteria);
        
        $newarray = array();//组合广告信息，根据是相同的广告id
        foreach($adlist as $val) {
            $newarray[$val->id]['adId'] = $val->id;
            $newarray[$val->id]['adName'] = $val->name;
            $newarray[$val->id]['adStatus'] = $val->status;
            $newarray[$val->id]['endTime'] = date("Y-m-d H:i:s", $val->ads_end_time);
            $newarray[$val->id]['startTime'] = date("Y-m-d H:i:s", $val->ads_start_time);
            $newarray[$val->id]['adPosX'] = $val->SiteAd['pos_x'];
            $newarray[$val->id]['adPosY'] = $val->SiteAd['pos_y'];
            $newarray[$val->id]['adWidth'] = $val->SiteAd['width'];
            $newarray[$val->id]['adHeight'] = $val->SiteAd['height'];
            $newarray[$val->id]['adType'] = $val->SiteAd['show_type'];
            $newarray[$val->id]['material'] = unserialize($val->SiteAd['material']);
        }
        // 5
        $arrMateId = array();//组合物料id
        $arrAdMateId = array();//组合广告下面的物料id
        foreach($newarray as $key=>$row){
            if(!empty($row['material'])){
                foreach($row['material'] as $k=>$val) {
                    $arrAdMateId[$key][] = $val['id'];
                    $arrMateId[] = $val['id'];
                }
            }
        }
        //6
        $materialList = Material::model()->getByMaterial($arrMateId);//根据物料id查找物料信息
        $text_id = "";//对各种物料材质赋一个初始值
        $pic_id = "";
        $flash_id = "";
        $media_id = "";
        $video_id = "";
        $strs =array();//保存所有的物料信息
        foreach($materialList as $key=>$vals) {
            $strs[$key]['materialId'] = $vals['id'];
            $strs[$key]['materialName'] = $vals['name'];
            $strs[$key]['materialType'] = $vals['material_type_id'];
            switch($strs[$key]['materialType']){//保存不同物料材质所对应的id
                case 1:
                {
                    if($text_id == ""){
                        $text_id = $vals['id'];
                    }else{
                        $text_id = $text_id.",".$vals['id'];
                    }
                    break;
                }
                case 2:
                {
                    if($pic_id == ""){
                        $pic_id = $vals['id'];
                        }else{
                        $pic_id = $pic_id.",".$vals['id'];
                        }
                        break;
                }
                case 3:
                {
                    if($flash_id == ""){
                        $flash_id = $vals['id'];
                        }else{
                        $flash_id = $flash_id.",".$vals['id'];}
                        break;
                }
                case 4:
                {
                    if($media_id == ""){
                        $media_id  = $vals['id'];
                        }else{
                        $media_id  = $media_id .",".$vals['id'];}
                        break;
                }
                case 5:
                {
                    if($video_id == ""){
                        $video_id = $vals['id'];
                        }else{
                        $strs['materialVideo_id'] =$video_id.",".$vals['id'];}
                        break;
                }
            }
        }
        //7
        $newsarray =array();//组合物料材质与物料信息
        if($text_id != ""){
            $materialText = MaterialText::model()->getByMaterialText($text_id);
            foreach($materialText as $key=>$val) {
                $newsarray[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($pic_id != ""){
            $materialPic = MaterialPic::model()->getByMaterialPic($pic_id);
            foreach($materialPic as $key=>$val) {
                    $newsarray[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($flash_id != "") {
            $materialFlash = MaterialFlash::model()->getByMaterialFlash($flash_id);
            foreach($materialFlash as $key=>$val) {
                    $newsarray[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($media_id != "") {
            $materialMedia = MaterialMedia::model()->getByMaterialMedia($media_id);
            foreach($materialMedia as $key=>$val) {
                    $newsarray[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($video_id != "") {
            $materialVideo = MaterialVideo::model()->getByMaterialVideo($video_id);
            foreach($materialVideo as $key=>$val) {
                    $newsarray[$key] = array_merge($val,$strs[$key]);
            }
        }
        //8
        /**
        *组合物料信息与广告信息（以物料id做为索引）
        */
        foreach($arrAdMateId as $key=>$val) {
            foreach ($val as $a){
                $newarray[$key]['materiallist'][] = $newsarray[$a];
                unset($newarray[$key]['material']);
            }
        }
        
        // 10
        /**
        *组合所有信息
        */
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $return['returnData'] = array(
            'total' => $count,
            'pageTotal' => ceil($count/$pageSize),
            'pageSize' => $pageSize,
            'pageNum' => $pageNum,
            'sortField' => $sortField,
            'sort' => $sort,
            'adList' => $newarray
        );
        //print_r($returnCode);测试数据
        return $return;
    } 
  
    
}