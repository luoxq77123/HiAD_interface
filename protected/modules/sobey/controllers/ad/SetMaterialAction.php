<?php

/**
 * 广告投放物料设置动作。
 */
class SetMaterialAction extends CAction {
    public function run(){
        $user = Yii::app()->session['user'];
        if (isset($_GET['adPosition'])) {
            $src = array("-a","-b","-c");
            $dist  = array("/","+","=");
            $txt  = str_replace($src, $dist, $_GET['adPosition']);
            $adInfo = unserialize(base64_decode($txt));
            //$adInfo = unserialize(base64_decode($_GET['adPosition']));
        } else {
            $adInfo = array();
        }
        $aid = 0;
        if (isset($_REQUEST['aid'])) {
            $aid = intval($_REQUEST['aid']);
        } else if (isset($adInfo['ad']['aid'])){
            $aid = $adInfo['ad']['aid'];
        }
        if ($aid==0 || $aid!=$adInfo['ad']['aid']) {
            echo "广告物料设置的参数有误，请重新再试";
            exit();
        }
        $adPosition = $_GET['adPosition'];
        // 获取处理动作
        $do = '';
        if (isset($_REQUEST['do'])) {
            $do = $_REQUEST['do'];
        } else if (isset($adInfo['ad']['do']) && $adInfo['ad']['do']=='modify') {
            $do = 'modify';
        }
        // 广告类型
        //$adShow = ($adInfo['ad']['ad_show'] == "播放器")? 'player' : '';
        $material = array();
        // 保存广告位设置
        if ('save'==$do) {
            $msg = $this->_saveMaterialSet();
            echo json_encode($msg);
            exit();
        } else if ('modify'==$do) {
            $material = $this->getMaterialHtml($aid);
        }
        
        $rotateList = Material::model()->getRotate();
        if (empty($material)) {
            $material['mrotate_mode'] = '1';
            $material['mrotate_time'] = "";
            $material['material'] = "";
        }
        $material['aid'] = $aid;
        $material['adPosition'] = $adPosition;
        $set = array(
            'rotateList' => $rotateList,
            'material' => $material
        );

        $controller = $this->getController();
        $controller->renderPartial('setMaterial', $set);
    }
    
    public function getMaterialHtml($aid){
        $adInfo = Yii::app()->session['create_ad_info'];
        $return = array();
        if ($adInfo['ad']['aid']==$aid && !empty($adInfo['material'])){
            $mrotate_mode = (isset($adInfo['material']['mrotate_mode']))? $adInfo['material']['mrotate_mode'] : "";
            $mrotate_time = (isset($adInfo['material']['mrotate_time']))? $adInfo['material']['mrotate_time'] : "";
            $material = (isset($adInfo['material']['material']))? $adInfo['material']['material'] : "";
            
            if ($mrotate_mode=="" || $material==""){
                $adData = SiteAd::model()->getOneByAdId($aid);
                if (empty($adData)) {
                    return false;
                }
                $mrotate_mode = $adData->mrotate_mode;
                $mrotate_time = $adData->mrotate_time;
                $material = unserialize($adData->material);
            }

            if (empty($material)) {
                return false;
            }
            $condition = 'id in (';
            $params = array();
            $arrWeigths = array();
            foreach($material as $key=>$val){
                $condition .= $key==0? ':id'.$key : ',:id'.$key;
                $params[':id'.$key] = $val['id'];
                $arrWeigths[$val['id']] = $val['weights'];
            }
            $condition .= ')';
            $mMaterial = new Material();
            $mlist = $mMaterial->findAll(array(
                'select'=>'id,name,material_type_id,material_size',
                'condition'=>$condition,
                'params'=>$params
            ));
                    
            $materialText = '';
            foreach($mlist as $val){
                $materialText .= '<li class="li_select" id="'.$val->id.'">';
                $materialType = MaterialType::model()->getMaterialTypes();
                $materialText .= '<span class="sw_200">'.$val->name.'</span><span class="sw_100">'.$val->material_size.'</span><span class="sw_100">'.$materialType[$val->material_type_id]['name'].'</span>';
                $materialText .= '<span class="sw_150"><a href="'. Yii::app()->createUrl('material/edit', array('id' => $val->id)).'" title="修改物料信息" class="load_frame">修改</a> | <a target="_blank" href="'.Yii::app()->createUrl('client/cbad', array('val' => $val->id,'type'=>$val->material_type_id)).'" onclick="">预览</a>  | <a href="javascript:void(0);" onclick="removeCutData(\''.$val->id.'\')">移除</a></span>';
                $materialText .= '<span class="sw_150" style="display:none;">权重'.CHtml::dropDownList('mweights_'.$val->id, @$arrWeigths[$val->id], SiteAd::model()->getWeightList(), array('class' => 'select_box ml15', 'id' => 'mweights_'.$val->id)).'</span>';
                $materialText .= '<br />';
                $materialText .= '<span class="material_content">';
                // 物料素材
                $content = "";
                if ($val->material_type_id==1) {
                    $data = MaterialText::model()->find(array(
                        'select'=>'text, click_link',
                        'condition'=>'material_id=:id',
                        'params'=>array(':id'=>$val->id)
                    ));
                    $content = '<a href="'.$data->click_link.'" target="_blank">'.$data->text.'</a>';
                } else if ($val->material_type_id==2) {
                    $data = MaterialPic::model()->find(array(
                        'select'=>'url, click_link',
                        'condition'=>'material_id=:id',
                        'params'=>array(':id'=>$val->id)
                    ));
                    $content = '<a href="'.$data->click_link.'" target="_blank"><img src="'.$this->module->assetsUrl.$data['url'].'" onerror="this.src=\''. $this->module->assetsUrl.'/images/none.png\';" /></a>';
                } else if ($val->material_type_id==3) {
                    $data = MaterialFlash::model()->find(array(
                        'select'=>'url, click_link, reserve_pic_url, reserve_pic_link',
                        'condition'=>'material_id=:id',
                        'params'=>array(':id'=>$val->id)
                    ));
                    $content = '<a href="'.$data->click_link.'" target="_blank"><embed src="'.$data->url.'" type="application/x-shockwave-flash" fullscreen="no" /></a>';
                } else if ($val->material_type_id==4) {
                    
                }
                $materialText .= $content;
                $materialText .= '</span></li>';
            }
            $return['mrotate_mode'] = $mrotate_mode;
            $return['mrotate_time'] = $mrotate_time;
            $return['material'] = $materialText;
            return $return;
        }
        
        return false;
    }
    
    // 保存物料投放设置
    private function _saveMaterialSet(){
        $check = $this->_checkData();
        if ($check['code']==0) {
            return $check;
        }
        $user = Yii::app()->session['user'];
        foreach($_POST as $key=>$val) {
            $$key = $val;
        }
        $tempMaterail = explode("==", $material);
        $arrMaterial = array();
        foreach($tempMaterail as $key=>$val){
            $tempData = explode("||", $val);
            $arrMaterial[$key]['id'] = $tempData[0];
            $arrMaterial[$key]['weights'] = $tempData[1];
        }
        $strMaterail = serialize($arrMaterial);
        // 保存物料设置
        $siteAd = new SiteAd();
        $attribe = array(
            'mrotate_mode' => $rotate,
            'mrotate_time' => $rotateTime,
            'material' => $strMaterail
        );
        $siteAd->updateAll($attribe, 'ad_id=:ad_id and com_id=:com_id', array(':ad_id'=>$aid,':com_id'=>$user['com_id']));

        // 更新广告状态和时间
        $ad = new Ad();
        $attribe = array(
            'status' => 1,
            'post_time' => time()
        );
        $ad->updateAll($attribe, 'id=:id and com_id=:com_id', array(':id'=>$aid,':com_id'=>$user['com_id']));
        if(isset($_GET['adPosition'])){
            unset($_GET['adPosition']);
        }

        $return['code'] = 1;
        return $return;
    }
    
    private function _checkData(){
        $return = array();
        $return['code'] = 1;

        foreach($_POST as $key=>$val) {
            $$key = $val;
        }
        // 检查参数
        if (!isset($_POST['aid']) || $aid<1) {
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            return $return;
        }
        $rotateList = Material::model()->getRotate();
        if (!array_key_exists($rotate, $rotateList)){
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            return $return;
        }
        if ($rotate=='3' && $rotateTime<0) {
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            return $return;
        }
        if ($material=="") {
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            return $return;
        }
        return $return;
    }
}