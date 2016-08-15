<?php
class AdPositionController extends CController {
    private $_params;

    function __construct(){
        $this->_params = json_decode(stripslashes($_POST['parameter']), true);
    }
    
    /**
     * 查找广告位、广告、物料
     */
    public function getAdPositionData(){

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
        $sortField = (isset($this->_params['sortField']) && $this->_params['sortField']!='')? $this->_params['sortField'] : 'createtime';
        $sort = (isset($this->_params['sort']) && $this->_params['sort']!='')? $this->_params['sort'] : 'DESC';
        $pageSize = (isset($this->_params['pageSize']) && $this->_params['pageSize']>0)? $this->_params['pageSize'] : 10;
        $pageNum = (isset($this->_params['pageNum']) && $this->_params['pageNum']!='')? $this->_params['pageNum'] :1;
        if ($pageNum>0) $pageNum -= 1;
        $criteria = new CDbCriteria();
        $criteria->order = $sortField.' '.$sort;
        $criteria->select = 'id,name,status,description,ad_show_id';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' => 1));
        $criteria->addColumnCondition(array('ad_show_id' => 6));
        if (isset($this->_params['status']) && $this->_params['status'] != '') {
            $criteria->addColumnCondition(array('status' => $this->_params['status']));
        } else {
            $this->_params['status'] = 1;
            $criteria->addColumnCondition(array('status' => $this->_params['status']));
        }
        if (isset($this->_params['keyWord']) && $this->_params['keyWord'] != '') {
            $criteria->addSearchCondition('name',urldecode($this->_params['keyWord']));
        }
        $startFormatTime = '';
        $endFormatTime = '';
        if (isset($this->_params['startTime']) && $this->_params['startTime'] != '') {
            $startTime = User::model()->mstrToTime($this->_params['startTime']);
            $endTime = time();
            if (isset($this->_params['endTime']) && $this->_params['endTime'] != '') {
                $endTime = User::model()->mstrToTime($this->_params['endTime']);
            }
            $criteria->addBetweenCondition('createtime', $startTime, $endTime);
            $startFormatTime = $this->_params['startTime'];
            $endFormatTime = date("Y-m-d H:i:s", $endTime);
        }
        if (isset($this->_params['positionIds']) && $this->_params['positionIds'] != '') {
            $arrPositionId = explode(",", $this->_params['positionIds']);
            $criteria->addInCondition('id', $arrPositionId);
        }
        // 1
        $count = Position::model()->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        //$pageNum = (isset($this->_params['pageNum']) && $this->_params['pageNum']) ? $this->_params['pageNum'] : $pager->validateCurrentPage;//validateCurrentPage 判断当前页是否大于或者小于总页数，对其赋值为1.
        $pager->setCurrentPage($pageNum);
        $pager->applyLimit($criteria);
        $positionlist = Position::model()->findAll($criteria);
        $arrPosId = array();//保存所有符合条件的广告位id
        foreach($positionlist as $heg) {
                $arrPosId[] = $heg->id;
        }
        $arrPosList = array();//组合广告位信息
        foreach($positionlist as $keys=>$hege) {
                $arrPosList[$hege->id]['positionId'] = $hege->id;
                $arrPosList[$hege->id]['positionName'] = $hege->name;
                $arrPosList[$hege->id]['positionStatus'] = $hege->status;
                $arrPosList[$hege->id]['positionDesc'] = $hege->description;
        }
        unset($hege,$positionlist);
        // 2
        /**
        *查找广告
        */
        $adlist = Ad::model()->getByPositionId($arrPosId);
        $ids = array();//根据广告位id查找与之对应的广告id
        foreach($adlist as $key=>$id){
            $ids[] = $id->id;
        }
        $sitead = SiteAd::model()->getBySiteAd($ids);
        $newarray = array();//组合广告信息的数据,以广告id为索引
        foreach($adlist as $key=>$val) {
            if(!empty($sitead[$key])){
                $newarray[$key]['adId'] = $val->id;
                $newarray[$key]['adName'] = $val->name;
                $newarray[$key]['adStatus'] = $val->status;
                if($val->ads_end_time == 0) {
                    $newarray[$key]['endTime'] = $val->ads_end_time;
                } else {
                    $newarray[$key]['endTime'] = date('Y-m-d H:m:s', $val->ads_end_time);
                }
                $newarray[$key]['startTime'] = date('Y-m-d H:m:s', $val->ads_start_time);
                $newarray[$key]['position_id'] = $val->position_id;
                $newarray[$key]['adPosX'] = $sitead[$key]->pos_x;
                $newarray[$key]['adPosY'] = $sitead[$key]->pos_y;
                $newarray[$key]['adWidth'] = $sitead[$key]->width;
                $newarray[$key]['adHeight'] = $sitead[$key]->height;
                $newarray[$key]['adType'] = $sitead[$key]->cushion;
                $newarray[$key]['material'] = unserialize($sitead[$key]->material);
            }
        }
        // 3
        $arrPosAdId = array();//组合广告信息的数据,以广告位id为索引
        foreach($newarray as $key=>$val) {
            $arrPosAdId[$val['position_id']][] = $key;
            unset($newarray[$key]['position_id']);
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
        $materialList = Material::model()->getByMaterial($arrMateId);
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
                        $video_id =$video_id.",".$vals['id'];}
                        break;
                }
            }
        }
        //7
        $newsarray =array();//组合物料与物料材质
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
        **组合物料与物料相对应的广告id的信息（以物料id为索引）
        */
        foreach($arrAdMateId as $key=>$val) {
            foreach ($val as $a){
                $newarray[$key]['materiallist'][] = $newsarray[$a];
                unset($newarray[$key]['material']);
            }
        }
        //9
        /**
        *组合广告与物料信息（以广告id为索引）
        */
        $positionList = array();
        foreach ($arrPosList as $key=>$val){
            $arrPosList[$key]['adlist'] = array();
            if (isset($arrPosAdId[$key])) {
                foreach ($arrPosAdId[$key] as $ad_id) {
                    $arrPosList[$key]['adlist'][] = $newarray[$ad_id];
                }
            }
            $positionList[] = $arrPosList[$key];//改变以广告位id所成的索引
        }
        
        // 10
        /**
        *组合所有数据
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
            'startTime' => $startFormatTime,
            'endTime' => $endFormatTime,
            'positionList' => $positionList
        );
        //print_r($return);exit;//测试用例
        return $return;
    } 

    /**
     * 查找广告位列表
     */
    public function getAdPositionList(){
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
        // 设置变量保存数据
        $user = User::model()->getOneByToken($this->_params['verifyCode']);
        $sortField = (isset($this->_params['sortField']) && $this->_params['sortField']!='')? $this->_params['sortField'] : 'createtime';
        $sort = (isset($this->_params['sort']) && $this->_params['sort']!='')? $this->_params['sort'] : 'DESC';
        $pageSize = (isset($this->_params['pageSize']) && $this->_params['pageSize']>0)? $this->_params['pageSize'] : 10;
        $pageNum = (isset($this->_params['pageNum']) && $this->_params['pageNum']!='')? $this->_params['pageNum'] :0;
        if ($pageNum>0) $pageNum -= 1;
        $criteria = new CDbCriteria();
        $criteria->order = $sortField.' '.$sort;
        $criteria->select = 'id,name,status,description';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' => 1));
        $criteria->addColumnCondition(array('ad_show_id' => 6));
        if (isset($this->_params['status']) && $this->_params['status'] != '') {
            $criteria->addColumnCondition(array('status' => $this->_params['status']));
        } else {
            $this->_params['status'] = 1;
            $criteria->addColumnCondition(array('status' => $this->_params['status']));
        }
        if (isset($this->_params['keyWord']) && $this->_params['keyWord'] != '') {
            $criteria->addSearchCondition('name',urldecode($this->_params['keyWord']));
        }
        $startFormatTime = '';
        $endFormatTime = '';
        if (isset($this->_params['startTime']) && $this->_params['startTime'] != '') {
            $startTime = User::model()->mstrToTime($this->_params['startTime']);
            $endTime = time();
            if (isset($this->_params['endTime']) && $this->_params['endTime'] != '') {
                $endTime = User::model()->mstrToTime($this->_params['endTime']);
            }
            $criteria->addBetweenCondition('createtime', $startTime, $endTime);
            $startFormatTime = $this->_params['startTime'];
            $endFormatTime = date("Y-m-d H:i:s", $endTime);
        }
        if (isset($this->_params['positionIds']) && $this->_params['positionIds'] != '') {
            $arrPositionId = explode(",", $this->_params['positionIds']);
            $criteria->addInCondition('id', $arrPositionId);
        }
        
        // 1
        $count = Position::model()->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->setCurrentPage($pageNum);
        $pager->applyLimit($criteria);
        $positionList = Position::model()->findAll($criteria);
        if (empty($positionList)) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '没有查询到相关的广告位信息';
            return $return;
        }
        $arrPosList = array();//组合广告位信息
        $index = 0;
        foreach($positionList as $keys=>$hege) {
            $arrPosList[$index]['positionId'] = $hege->id;
            $arrPosList[$index]['positionName'] = $hege->name;
            $arrPosList[$index]['positionStatus'] = $hege->status;
            $arrPosList[$index]['positionDesc'] = $hege->description;
            $index ++;
        }
        unset($positionList);
        // 返回数据
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $return['returnData'] = array(
            'total' => $count,
            'pageTotal' => ceil($count/$pageSize),
            'pageSize' => $pageSize,
            'pageNum' => $pageNum,
            'sortField' => $sortField,
            'sort' => $sort,
            'startTime' => $startFormatTime,
            'endTime' => $endFormatTime,
            'positionList' => $arrPosList
        );
        return $return;
    }

    /**
     * 根据广告位id 获取广告及素材
     */
    public function getAdMaterial() {
        $return = array();
        if (!isset($this->_params['positionId']) || !isset($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        $positionId = $this->_params['positionId'];
        $token = $this->_params['verifyCode'];
        // 验证用户
        if (!User::model()->checkToken($token)) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '用户验证失败';
            return $return;
        }
        // 设置变量保存数据
        $user = User::model()->getOneByToken($token);
        // 验证广告位id所属公司是否正确
        $position = Position::model()->findByPk($positionId);
        if (empty($position) || $position['com_id'] != $user['com_id']) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '广告位“'.$positionId.'”不存在或暂无权限操作该广告位';
            return $return;
        }
        // 获取广告信息
        $adlist = Ad::model()->getByPositionId(array($positionId));
        if (empty($adlist)) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '广告位“'.$positionId.'”还没有广告和素材信息';
            return $return;
        }
        $ids = array();//根据广告位id查找与之对应的广告id
        foreach($adlist as $key=>$id){
            $ids[] = $id->id;
        }
        $sitead = SiteAd::model()->getBySiteAd($ids);
        $newarray = array();//组合广告信息的数据,以广告id为索引
        foreach($adlist as $key=>$val) {
            if(!empty($sitead[$key])){
                $newarray[$key]['adId'] = $val->id;
                $newarray[$key]['adName'] = $val->name;
                $newarray[$key]['adStatus'] = $val->status;
                if( $val->ads_end_time > 0) {
                    $newarray[$key]['endTime'] = date("Y-m-d H:i:s", $val->ads_end_time);
                } else {
                    $newarray[$key]['endTime'] = '不限';
                }
                $newarray[$key]['startTime'] = date("Y-m-d H:i:s", $val->ads_start_time);
                $newarray[$key]['adPosX'] = $sitead[$key]->pos_x;
                $newarray[$key]['adPosY'] = $sitead[$key]->pos_y;
                $newarray[$key]['adWidth'] = $sitead[$key]->width;
                $newarray[$key]['adHeight'] = $sitead[$key]->height;
                $newarray[$key]['adType'] = $sitead[$key]->cushion;
                $newarray[$key]['material'] = unserialize($sitead[$key]->material);
            }
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
        $materialList = Material::model()->getByMaterial($arrMateId);
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
                        $flash_id = $flash_id.",".$vals['id'];
                    }
                    break;
                }
                case 4:
                {
                    if($media_id == "")
                        $media_id  = $vals['id'];
                    else
                        $media_id  = $media_id .",".$vals['id'];
                    break;
                }
                case 5:
                {
                    if($video_id == "")
                        $video_id = $vals['id'];
                    else
                        $video_id =$video_id.",".$vals['id'];
                    break;
                }
            }
        }
        //7
        $arrMaterial =array();//组合物料与物料材质
        if($text_id != ""){
            $materialText = MaterialText::model()->getByMaterialText($text_id);
            foreach($materialText as $key=>$val) {
                $arrMaterial[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($pic_id != ""){
            $materialPic = MaterialPic::model()->getByMaterialPic($pic_id);
            foreach($materialPic as $key=>$val) {
                $arrMaterial[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($flash_id != "") {
            $materialFlash = MaterialFlash::model()->getByMaterialFlash($flash_id);
            foreach($materialFlash as $key=>$val) {
                $arrMaterial[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($media_id != "") {
            $materialMedia = MaterialMedia::model()->getByMaterialMedia($media_id);
            foreach($materialMedia as $key=>$val) {
                $arrMaterial[$key] = array_merge($val,$strs[$key]);
            }
        }
        if ($video_id != "") {
            $materialVideo = MaterialVideo::model()->getByMaterialVideo($video_id);
            foreach($materialVideo as $key=>$val) {
                $arrMaterial[$key] = array_merge($val,$strs[$key]);
            }
        }
        //8
        /**
        **组合物料与物料相对应的广告id的信息（以物料id为索引）
        */
        $arrAdMatrial = array();
        foreach($arrAdMateId as $key=>$val) {
            unset($newarray[$key]['material']);
            foreach ($val as $mid){
                $newarray[$key]['materialList'][] = $arrMaterial[$mid];
            }
            $arrAdMatrial[] = $newarray[$key];
        }
        unset($newarray, $arrAdMateId, $arrMaterial);
        // 返回数据
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $return['returnData'] = $arrAdMatrial;
        return $return;
    }
}