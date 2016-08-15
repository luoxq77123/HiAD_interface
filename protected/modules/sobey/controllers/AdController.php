<?php

/**
 * 广告控制器
 */
class AdController extends BaseController {

    /**
     * 广告投放动作
     */
    public function actions() {
        return array(
            'setAd' => 'sobey.controllers.ad.SetAdAction', //设置广告
            'setPolicy' => 'sobey.controllers.ad.SetPolicyAction', //设置投放策略
            'setMaterial' => 'sobey.controllers.ad.SetMaterialAction', //设置素材
        );
    }

    /**
     * 广告列表
     */
    public function actionList() {

        // 设置默认选择状态为未完成
        $_GET['status'] = isset($_GET['status']) ? $_GET['status'] : 11;

        // 根据条件获取广告列表和分页信息
        $adData = Ad::model()->getPagerList();
        $adlist = $adData['list'];
        $pager = $adData['pager'];

        $status = array('all' => '全部') + Ad::model()->getAdStatus();

        $setArray = array(
            'adlist' => $adlist,
            'pages' => $pager,
            'status' => $status
        );

        $this->renderPartial('list', $setArray);
    }

    /**
     * 设置广告状态 投放
     */
    public function actionSetStartAd() {
        $aids = $_POST['aids'];
        if ($aids == "") {
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            echo json_encode($return);
            return false;
        }
        $user = Yii::app()->session['user'];
        $arrAid = explode(",", $aids);

        $condition = 'id in (';
        $params = array();
        foreach ($arrAid as $key => $val) {
            $condition .= $key == 0 ? ':id' . $key : ',:id' . $key;
            $params[':id' . $key] = $val;
        }
        $condition .= ') and com_id=:com_id';
        $params[':com_id'] = $user['com_id'];

        $ad = new Ad();
        $attribe = array(
            'status' => 1
        );
        $ad->updateAll($attribe, $condition, $params);
        Yii::app()->oplog->add(); //添加日志
        $return['code'] = 1;
        $return['msg'] = "修改状态成功";
        echo json_encode($return);
    }

    /**
     * 设置广告状态 暂停 已经完成的广告不能设置为暂停
     */
    public function actionSetStopAd() {
        $aids = $_POST['aids'];
        if ($aids == "") {
            $return['code'] = 0;
            $return['msg'] = "参数错误";
            echo json_encode($return);
            return false;
        }
        $user = Yii::app()->session['user'];
        $arrAid = explode(",", $aids);
        
        $condition = 'id in (';
        $params = array();
        foreach ($arrAid as $key => $val) {
            $condition .= $key == 0 ? ':id' . $key : ',:id' . $key;
            $params[':id' . $key] = $val;
        }
        //$condition .= ') and com_id=:com_id and ads_end_time>:end_time';
        $condition .= ') and com_id=:com_id';
        $params[':com_id'] = $user['com_id'];
        //$params[':end_time'] = time();

        $ad = new Ad();
        $attribe = array(
            'status' => 2
        );
        $ad->updateAll($attribe, $condition, $params);
        Yii::app()->oplog->add(); //添加日志
        $return['code'] = 1;
        $return['msg'] = "修改状态成功";
        echo json_encode($return);
    }

    /**
     * 修改广告状态 删除
     */
    public function actionDel() {
        $aids = $_POST['aids'];
        if ($aids == "") {
            $return['code'] = 0;
            $return['message'] = "参数错误";
            echo json_encode($return);
            exit;
        }
        $user = Yii::app()->session['user'];
        $arrAid = explode(",", $aids);

        $condition = 'id in (';
        $condition1 = 'ad_id in (';
        $params = array();
        $tempStr = "";
        foreach ($arrAid as $key => $val) {
            $tempStr .= $tempStr == "" ? ':id' . $key : ',:id' . $key;
            $params[':id' . $key] = $val;
        }
        $condition .= $tempStr . ') and com_id=:com_id';
        $condition1 .= $tempStr . ') and com_id=:com_id';
        $params[':com_id'] = $user['com_id'];
        // 这里删除 并未真的删除 只是修改状态
        $ad = new Ad();
        $attribe = array(
            'status' => -1
        );
        $ad->updateAll($attribe, $condition, $params);
        Yii::app()->oplog->add(); //添加日志
        /*
          // 删除广告对应的投放信息
          $siteAd = new SiteAd();
          $siteAd->deleteAll($condition1, $params);

          // 删除广告
          $ad = new Ad();
          $ad->deleteAll($condition, $params);
         */
        $return['code'] = 1;
        $return['message'] = "删除广告成功";
        echo json_encode($return);
        exit;
    }

    /**
     * 获取站点广告位列表
     */
    public function actionGetAdPositionList() {
        $this->renderPartial('adPositionList');
    }

    /**
     * 获取投放定向参数
     */
    public function actionDirectional() {
        $this->renderPartial('directional');
    }

    /**
     * 获取物料列表
     */
    public function actionGetMaterialList() {
        $this->renderPartial('materialList');
    }

    /**
     * 获取已经选择的物料信息
     */
    public function actionGetMaterialInfo() {
        $mids = $_POST['mids'];
        $adPosition =$_POST['adPosition'];
        if (empty($mids)) {
            $return['code'] = 0;
            $return['msg'] = "请选择物料";
            echo json_encode($return);
            exit();
        }
        $arrMid = explode(",", $mids);
        $condition = 'id in (';
        $params = array();
        foreach ($arrMid as $key => $val) {
            $condition .= $key == 0 ? ':id' . $key : ',:id' . $key;
            $params[':id' . $key] = $val;
        }
        $condition .= ')';
        $mMaterial = new Material();
        $mlist = $mMaterial->findAll(array(
            'select' => 'id,name,material_type_id,material_size',
            'condition' => $condition,
            'params' => $params
                ));
        $material = "";
        foreach ($mlist as $val) {
            $material .= '<li class="li_select" id="' . $val->id . '">';
            $materialType = MaterialType::model()->getMaterialTypes();
            $material .= '<span class="sw_200">' . $val->name . '</span><span class="sw_100">' . $val->material_size . '</span><span class="sw_100">' . $materialType[$val->material_type_id]['name'] . '</span>';
            $material .= '<span class="sw_150"> <a href="' . $this->createUrl('material/edit', array('id' => $val->id,'adPosition' => $adPosition)) . '" title="修改物料信息" class="load_frame">修改</a> | <a target="_blank" href="' . Yii::app()->createUrl('sobey/client/cbad', array('val' => $val->id, 'ad_type'=>2, 'type' => $val->material_type_id)) . '" onclick="">预览</a> | <a href="javascript:void(0);" onclick="removeCutData(\'' . $val->id . '\')">移除</a></span>';
            $material .= '<span class="sw_150" style="display:none;">权重' . CHtml::dropDownList('mweights_' . $val->id, @5, SiteAd::model()->getWeightList(), array('class' => 'select_box ml15', 'id' => 'mweights_' . $val->id)) . '</span>';
            $material .= '<br />';
            $material .= '<span class="material_content">';
            $content = "";
            if ($val->material_type_id == 1) {
                $data = MaterialText::model()->find(array(
                    'select' => 'text, click_link',
                    'condition' => 'material_id=:id',
                    'params' => array(':id' => $val->id)
                ));
                $content = '<a href="' . $data->click_link . '" target="_blank">' . $data->text . '</a>';
            } else if ($val->material_type_id == 2) {
                $data = MaterialPic::model()->find(array(
                    'select' => 'url, click_link, pic_x, pic_y',
                    'condition' => 'material_id=:id',
                    'params' => array(':id' => $val->id)
                ));
                $content = '<a href="' . $data->click_link . '" target="_blank"><img style=max-width:600px; src="' . Yii::app()->request->baseUrl.$data->url . '" alt="物料图片" onerror="this.src=\''.$this->module->assetsUrl.'/images/none.png\';"  width="'.$data->pic_x.'"  height="'.$data->pic_y.'" /></a>';
            } else if ($val->material_type_id == 3) {
                $data = MaterialFlash::model()->find(array(
                    'select' => 'url, click_link, reserve_pic_url, reserve_pic_link, flash_x, flash_y',
                    'condition' => 'material_id=:id',
                    'params' => array(':id' => $val->id)
                ));
                $content = '<a href="' . $data->click_link . '" target="_blank"><embed src="'.Yii::app()->request->baseUrl.$data->url.'" type="application/x-shockwave-flash" width="'.$data->flash_x.'" height="'.$data->flash_y.'" fullscreen="no" /></a>';
            } else if ($val->material_type_id == 4) {
                
            }
            $material .= $content;
            $material .= '</span></li>';
        }
        $return['code'] = 1;
        $return['msg'] = $material;
        echo json_encode($return);
    }

}