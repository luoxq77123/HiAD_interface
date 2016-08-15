<?php

/**
 * 物料控制器
 */
class MaterialController extends BaseController {

    /**
     * 物料列表
     */
    public function actionList() {
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'id,name,material_type_id,status,material_size';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' => 1));

        // 附加搜索条件
        if (isset($_GET['status']) && $_GET['status']) {
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        }

        if (isset($_GET['type']) && $_GET['type']) {
            $criteria->addColumnCondition(array('material_type_id' => $_GET['type']));
        }

        if (isset($_GET['size']) && $_GET['size']) {
            $criteria->addColumnCondition(array('material_size' => $_GET['size']));
        }

        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }

        if (isset($_GET['aid']) && $_GET['aid']) {
            $materialid = SiteAd::model()->find('ad_id=:ad_id', array(':ad_id' => $_GET['aid']));
            $materialids = array();
            if ($materialid) {
                $materialids = unserialize($materialid->material);
                $id = array();
                if (!empty($materialids)) {
                    foreach ($materialids as $one)
                        $id[] = $one['id'];
                    $criteria->addInCondition('id', $id);
                } else {
                    $criteria->addInCondition('id', array('0' => 0));
                }
            } else {
                $criteria->addInCondition('id', array('0' => 0));
            }
        }

        $count = Material::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $materiallist = Material::model()->findAll($criteria);

        $materialType = array(0 => '-请选择-') + @CHtml::listData(MaterialType::model()->getMaterialTypes(), 'id', 'name');
        $usedSize = Material::model()->getUsedSize($user['com_id']);
        $usedSize = array('' => '-请选择-') + $usedSize;
        $status = array(1 => '启用', -1 => '禁用');

        $setArray = array(
            'materiallist' => $materiallist,
            'pages' => $pager,
            'materialType' => $materialType,
            'usedSize' => $usedSize,
            'status' => $status
        );

        $this->renderPartial('list', $setArray);
    }

    /**
     * 添加物料
     */
    public function actionAdd() {
        
        $material = new Material('add');
        $materialText = new MaterialText('add');
        $materialPic = new MaterialPic('add');
        $materialFlash = new MaterialFlash('add');
        $materialMedia = new MaterialMedia('add');
        $materialVideo = new MaterialVideo('add');
        $materialType = MaterialType::model()->getMaterialTypes();
        $templateMode = MaterialMedia::model()->getTemplateMode();
        $setting = Setting::model()->getSettings();
        if (isset($_POST['Material'])) {
            $return = array('code' => 1, 'message' => '添加成功');
            $material->attributes = $_POST['Material'];
            $flag = array();
            if ($material->validate()) {
                $material->com_id = Yii::app()->session['user']['com_id'];
                $material->ad_type_id = 1;
                $material->createtime = time();
                if ($_POST['Material']['material_type_id'] == 1) {//文字
                    $materialText->attributes = $_POST['MaterialText'];
                    if ($materialText->validate()) {
                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialText->material_id = $material->attributes['id'];
                            if (isset($_POST['MaterialText']['style_1']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_1'];
                            if (isset($_POST['MaterialText']['style_2']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_2'];
                            if (isset($_POST['MaterialText']['style_3']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_3'];
                            if (isset($_POST['MaterialText']['float_style_1']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_1'];
                            if (isset($_POST['MaterialText']['float_style_2']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_2'];
                            if (isset($_POST['MaterialText']['float_style_3']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_3'];
                            if (isset($_POST['MaterialText']['monitor'])) {
                                $materialText->monitor_link = $_POST['MaterialText']['monitor_link'];
                                $materialText->monitor = 1;
                            } else {
                                $materialText->monitor = 0;
                            }
                            $materialText->save();
                        }
                    }
                    if ($materialText->hasErrors()) {
                        $flag = $materialText->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 2) {//图片
                    //echo "<pre>";print_r($_POST['MaterialPic']);
                    $materialPic->attributes = $_POST['MaterialPic'];
                    if ($materialPic->validate()) {
                        if ($_POST['MaterialPic']['pic_x'] && $_POST['MaterialPic']['pic_y']){
                            $material->material_size = $_POST['MaterialPic']['pic_x'] . '*' . $_POST['MaterialPic']['pic_y'];
                            $materialPic->pic_x=$_POST['MaterialPic']['pic_x'];
                            $materialPic->pic_y=$_POST['MaterialPic']['pic_y'];                        
                        }
                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialPic->material_id = $material->attributes['id'];
                            if (!isset($_POST['MaterialPic']['monitor'])) {
                                $materialPic->monitor_link = $_POST['MaterialPic']['monitor_link'];
                                $materialPic->monitor = 1;
                            } else {
                                $materialPic->monitor = 0;
                            }
                            $materialPic->save();
                        }
                    }
                    if ($materialPic->hasErrors()) {
                        $flag = $materialPic->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 3) {//flash
                    $materialFlash->attributes = $_POST['MaterialFlash'];
                    if ($materialFlash->validate()) {
                        if ($_POST['MaterialFlash']['flash_x'] && $_POST['MaterialFlash']['flash_y']){
                            $material->material_size = $_POST['MaterialFlash']['flash_x'] . '*' . $_POST['MaterialFlash']['flash_y'];
                            $materialFlash->flash_x=$_POST['MaterialFlash']['flash_x'];
                            $materialFlash->flash_y=$_POST['MaterialFlash']['flash_y'];  
                        }
                        if ($_POST['MaterialFlash']['flashpic_x'] && $_POST['MaterialFlash']['flashpic_y']){
                            $material->material_size = $_POST['MaterialFlash']['flashpic_x'] . '*' . $_POST['MaterialFlash']['flashpic_y'];
                            $materialFlash->flashpic_x=$_POST['MaterialFlash']['flashpic_x'];
                            $materialFlash->flashpic_y=$_POST['MaterialFlash']['flashpic_y'];  
                        }

                        if ($material->save()) {
                            Oplog::add(); //添加日志
                            $materialFlash->material_id = $material->attributes['id'];
                            if (!isset($_POST['MaterialFlash']['monitor_flash']))
                                $materialFlash->click_link = 'http://';
                            if (!isset($_POST['MaterialFlash']['reserve'])) {
                                $materialFlash->reserve_pic_url = NULL;
                                $materialFlash->reserve_pic_link = 'http://';
                            }
                            if (!isset($_POST['MaterialFlash']['monitor']))
                                $materialFlash->monitor_link = 'http://';
                            $materialFlash->save();
                        }
                    }
                    if ($materialFlash->hasErrors()) {
                        $flag = $materialFlash->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 4) {//富媒体
                    $materialMedia->attributes = $_POST['MaterialMedia'];
                    if ($materialMedia->validate()) {
                        if ($material->save()) {
                            Oplog::add(); //添加日志
                            // 添加富媒体参数
                            $materialMedia->material_id = $material->attributes['id'];
                            // 解析模板 当使用模板时
                            if ($_POST['MaterialMedia']['template_mode']>0 && substr($_POST['MaterialMedia']['template_id'],0,5)=='mtId_') {
                                $tplId = str_replace('mtId_', '', $_POST['MaterialMedia']['template_id']);
                                // 获取模板信息
                                $materialTpl = MaterialTemplate::model()->getOneById($tplId);
                                $tplParams = unserialize($materialTpl->params);
                                $content = $materialTpl->html;
                                // 解析模板参数
                                foreach($tplParams as $key=>$val) {
                                    $tmpValue = $_POST['RichMediaTpl'][$val['name']];
                                    $tplParams[$key]['value'] = $tmpValue;
                                    switch($val['type']) {
                                    case 1: // 图片或flash
                                        $tplParams[$key]['options']['mode'] = $_POST['RichMediaTplMediaMode_'.$val['name']];
                                        if ($tplParams[$key]['options']['mode']==2) {
                                            $tmpValue = $_POST['RichMediaTplMedia'][$val['name']];
                                            $tplParams[$key]['value'] = $tmpValue;
                                        }
                                        break;
                                    case 2: // 超链接
                                        if (isset($_POST['RichMediaTplLink'][$val['name']]) && $_POST['RichMediaTplLink'][$val['name']]==1) {
                                            $tmpValue = MaterialMedia::model()->makeStatUrl($_POST['RichMediaTpl'][$val['name']]);
                                            $tplParams[$key]['options']['clickstat'] = 1;
                                        }
                                        break;
                                    }
                                    $content = str_replace('${'.$val['name'].'}', $tmpValue, $content);
                                }
                                $materialMedia->template_id = $tplId;
                                $materialMedia->template_mode = ($materialTpl->recommend==1)? 2 : 1;
                                $materialMedia->template_name = $materialTpl->name;
                                $materialMedia->template_html = $materialTpl->html;
                                $materialMedia->template_params = serialize($tplParams);
                                $materialMedia->content = addslashes($content);
                            } else { // 未使用模板
                                $content = $_POST['MaterialMedia']['content'];
                                $pattern = "/%%BEGIN_LINK%%(.*?)%%END_LINK%%/i";
                                if (preg_match_all($pattern, $content, $match)) {
                                    $arrPattern = $match[0];
                                    $arrReplace = array();
                                    foreach($match[1] as $k=>$v) {
                                        $arrReplace[$k] = MaterialMedia::model()->makeStatUrl($v);
                                    }
                                    $content = str_replace($arrPattern, $arrReplace, $content);
                                }
                                $materialMedia->template_mode = 0;
                                $materialMedia->template_id = 0;
                                $materialMedia->content = addslashes($content);
                            }
                            $materialMedia->save();
                        }
                    }
                    if ($materialMedia->hasErrors()) {
                        $flag = $materialMedia->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 5) {//video
                    $materialVideo->attributes = $_POST['MaterialVideo'];
                    if ($materialVideo->validate()) {
                        if ($_POST['MaterialVideo']['video_x'] && $_POST['MaterialVideo']['video_y'])
                            $material->material_size = $_POST['MaterialVideo']['video_x'] . '*' . $_POST['MaterialVideo']['video_y'];
                        else if ($_POST['MaterialVideo']['videopic_x'] && $_POST['MaterialVideo']['videopic_y'])
                            $material->material_size = $_POST['MaterialVideo']['videopic_x'] . '*' . $_POST['MaterialVideo']['videopic_y'];

                        if ($material->save()) {
                            Oplog::add(); //添加日志
                            $materialVideo->material_id = $material->attributes['id'];
                            if (!isset($_POST['MaterialVideo']['monitor_video']))
                                $materialVideo->click_link = 'http://';
                            if (!isset($_POST['MaterialVideo']['reserve'])) {
                                $materialVideo->reserve_pic_url = NULL;
                                $materialVideo->reserve_pic_link = 'http://';
                            }
                            if (!isset($_POST['MaterialVideo']['monitor']))
                                $materialVideo->monitor_link = 'http://';
                            $materialVideo->save();
                        }
                    }
                    if ($materialVideo->hasErrors()) {
                        $flag = $materialVideo->errors;
                    }
                }
            }

            if ($material->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($material->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            } else if ($flag) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($flag->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }
        if (isset($_GET['adPosition'])) {
            $adPosition = $_GET['adPosition'];
        } else {
            $adPosition = "";
        }
        $set = array(
            'adPosition' => $adPosition,
            'material' => $material,
            'materialText' => $materialText,
            'materialPic' => $materialPic,
            'materialFlash' => $materialFlash,
            'materialMedia' => $materialMedia,
            'materialVideo' => $materialVideo,
            'materialType' => $materialType,
            'templateMode' => $templateMode,
            'setting' => $setting
        );
        $this->renderPartial('add', $set);
    }

    /**
     * 编辑物料
     */
    public function actionEdit() {
        $user = Yii::app()->session['user'];
        $id = $_GET['id'];
        $material = Material::model()->findByPk($id);
        $material->setScenario('edit');
        $templateMode = MaterialMedia::model()->getTemplateMode();
        $setting = Setting::model()->getSettings();
        if ($material->material_type_id == 1) {
            $materialText = MaterialText::model()->find('material_id=:material_id', array(':material_id' => $id));
            if (!empty($materialText)) {
                $materialText->setScenario('edit');
            } else {
                $materialText = new MaterialText('add');
            }
            $old_type = $materialText;
            $materialPic = new MaterialPic('add');
            $materialFlash = new MaterialFlash('add');
            $materialMedia = new MaterialMedia('add');
            $materialVideo = new MaterialVideo('add');
        } else if ($material->material_type_id == 2) {
            $materialPic = MaterialPic::model()->find('material_id=:material_id', array(':material_id' => $id));
            if (!empty($materialPic)) {
                $materialPic->setScenario('edit');
            } else {
                $materialPic = new MaterialPic('add');
            }
            $old_type = $materialPic;
            $materialText = new MaterialText('add');
            $materialFlash = new MaterialFlash('add');
            $materialMedia = new MaterialMedia('add');
            $materialVideo = new MaterialVideo('add');
        } else if ($material->material_type_id == 3) {
            $materialFlash = MaterialFlash::model()->find('material_id=:material_id', array(':material_id' => $id));
            if (!empty($materialFlash)) {
                $materialFlash->setScenario('edit');
            } else {
                $materialFlash = new MaterialFlash('add');
            }
            $old_type = $materialFlash;
            $materialText = new MaterialText('add');
            $materialPic = new MaterialPic('add');
            $materialMedia = new MaterialMedia('add');
            $materialVideo = new MaterialVideo('add');
        } else if ($material->material_type_id == 4) {
            $materialMedia = MaterialMedia::model()->find('material_id=:material_id', array(':material_id' => $id));
            if (!empty($materialMedia)) {
                $materialMedia->setScenario('edit');
            } else {
                $materialMedia = new MaterialMedia('add');
            }
            if ($materialMedia->template_mode=1) {
                $materialMedia->template_params = unserialize($materialMedia->template_params);
            }
            $old_type = $materialMedia;
            $materialText = new MaterialText('add');
            $materialPic = new MaterialPic('add');
            $materialFlash = new MaterialFlash('add');
            $materialVideo = new MaterialVideo('add');
        } else if ($material->material_type_id == 5) {
            $materialVideo = MaterialVideo::model()->find('material_id=:material_id', array(':material_id' => $id));
            if (!empty($materialVideo)) {
                $materialVideo->setScenario('edit');
            } else {
                $materialVideo = new MaterialVideo('add');
            }
            $old_type = $materialVideo;
            $materialText = new MaterialText('add');
            $materialPic = new MaterialPic('add');
            $materialFlash = new MaterialFlash('add');
            $materialMedia = new MaterialMedia('add');
        }
        if (isset($_POST['Material'])) {
            $return = array('code' => 1, 'message' => '编辑成功');
            $material->attributes = $_POST['Material'];
            $flag = array();
            if ($material->validate()) {
                if ($_POST['Material']['material_type_id'] == 1) {//文字
                    $materialText->attributes = $_POST['MaterialText'];
                    if ($materialText->validate()) {
                        $material->material_size = NULL;
                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialText->material_id = $material->id;
                            $materialText->style = '';
                            $materialText->float_style = '';
                            if (isset($_POST['MaterialText']['style_1']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_1'];
                            if (isset($_POST['MaterialText']['style_2']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_2'];
                            if (isset($_POST['MaterialText']['style_3']))
                                $materialText->style .='-' . $_POST['MaterialText']['style_3'];
                            if (isset($_POST['MaterialText']['float_style_1']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_1'];
                            if (isset($_POST['MaterialText']['float_style_2']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_2'];
                            if (isset($_POST['MaterialText']['float_style_3']))
                                $materialText->float_style .='-' . $_POST['MaterialText']['float_style_3'];
                            if (isset($_POST['MaterialText']['monitor'])) {
                                $materialText->monitor_link = $_POST['MaterialText']['monitor_link'];
                                $materialText->monitor = 1;
                            } else {
                                $materialText->monitor = 0;
                            }

                            $materialText->save();
                        }
                    }
                    if ($materialText->hasErrors()) {
                        $flag = $materialText->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 2) {//图片
                    $materialPic->attributes = $_POST['MaterialPic'];
                    if ($materialPic->validate()) {
                        if ($_POST['MaterialPic']['pic_x'] && $_POST['MaterialPic']['pic_y']){
                            $material->material_size = $_POST['MaterialPic']['pic_x'] . '*' . $_POST['MaterialPic']['pic_y'];
                            $materialPic->pic_x=$_POST['MaterialPic']['pic_x'];
                            $materialPic->pic_y=$_POST['MaterialPic']['pic_y'];                        
                        }
                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialPic->material_id = $material->id;
                            if (!isset($_POST['MaterialPic']['monitor'])) {
                                $materialPic->monitor = 0;
                                $materialPic->monitor_link = 'http://';
                            }
                            $materialPic->save();
                        }
                    }
                    if ($materialPic->hasErrors()) {
                        $flag = $materialPic->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 3) {//flash
                    $materialFlash->attributes = $_POST['MaterialFlash'];
                    if ($materialFlash->validate()) {
                        if ($_POST['MaterialFlash']['flash_x'] && $_POST['MaterialFlash']['flash_y']){
                            $material->material_size = $_POST['MaterialFlash']['flash_x'] . '*' . $_POST['MaterialFlash']['flash_y'];
                            $materialFlash->flash_x=$_POST['MaterialFlash']['flash_x'];
                            $materialFlash->flash_y=$_POST['MaterialFlash']['flash_y'];  
                        }
                        if ($_POST['MaterialFlash']['flashpic_x'] && $_POST['MaterialFlash']['flashpic_y']){
                            $material->material_size = $_POST['MaterialFlash']['flashpic_x'] . '*' . $_POST['MaterialFlash']['flashpic_y'];
                            $materialFlash->flashpic_x=$_POST['MaterialFlash']['flashpic_x'];
                            $materialFlash->flashpic_y=$_POST['MaterialFlash']['flashpic_y'];                          
                        }

                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialFlash->material_id = $material->id;
                            if (!isset($_POST['MaterialFlash']['monitor_flash']))
                                $materialFlash->click_link = 'http://';
                            if (!isset($_POST['MaterialFlash']['reserve'])) {
                                $materialFlash->reserve_pic_url = NULL;
                                $materialFlash->reserve_pic_link = 'http://';
                            }
                            if (!isset($_POST['MaterialFlash']['monitor']))
                                $materialFlash->monitor_link = 'http://';
                            $materialFlash->save();
                        }
                    }
                    if ($materialFlash->hasErrors()) {
                        $flag = $materialFlash->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 4) {//富媒体
                    $materialMedia->attributes = $_POST['MaterialMedia'];
                    if ($materialMedia->validate()) {
                        $material->material_size = NULL;
                        if ($material->save()) {
                            Oplog::add(); //添加日志
                            // 添加富媒体参数
                            $materialMedia->material_id = $material->attributes['id'];
                            // 解析模板 当使用模板时
                            if ($_POST['MaterialMedia']['template_mode']>0 && substr($_POST['MaterialMedia']['template_id'],0,5)=='mtId_') {
                                $tplId = str_replace('mtId_', '', $_POST['MaterialMedia']['template_id']);
                                // 获取模板信息
                                $materialTpl = MaterialTemplate::model()->getOneById($tplId);
                                $tplParams = unserialize($materialTpl->params);
                                $content = $materialTpl->html;
                                foreach($tplParams as $key=>$val) {
                                    $tmpValue = $_POST['RichMediaTpl'][$val['name']];
                                    $tplParams[$key]['value'] = $tmpValue;
                                    switch($val['type']) {
                                    case 1: // 图片或flash
                                        $tplParams[$key]['options']['mode'] = $_POST['RichMediaTplMediaMode_'.$val['name']];
                                        if ($tplParams[$key]['options']['mode']==2) {
                                            $tmpValue = $_POST['RichMediaTplMedia'][$val['name']];
                                            $tplParams[$key]['value'] = $tmpValue;
                                        }
                                        break;
                                    case 2: // 超链接
                                        if (isset($_POST['RichMediaTplLink'][$val['name']]) && $_POST['RichMediaTplLink'][$val['name']]==1) {
                                            $tmpValue = MaterialMedia::model()->makeStatUrl($_POST['RichMediaTpl'][$val['name']]);
                                            $tplParams[$key]['options']['clickstat'] = 1;
                                        }
                                        break;
                                    }
                                    $content = str_replace('${'.$val['name'].'}', $tmpValue, $content);
                                }
                                $materialMedia->template_id = $tplId;
                                $materialMedia->template_mode = ($materialTpl->recommend==1)? 2 : 1;
                                $materialMedia->template_html = $materialTpl->html;
                                $materialMedia->template_params = serialize($tplParams);
                                $materialMedia->content = addslashes($content);
                            } else { // 未使用模板
                                $content = $_POST['MaterialMedia']['content'];
                                $pattern = "/%%BEGIN_LINK%%(.*?)%%END_LINK%%/i";
                                if (preg_match_all($pattern, $content, $match)) {
                                    $arrPattern = $match[0];
                                    $arrReplace = array();
                                    foreach($match[1] as $k=>$v) {
                                        $arrReplace[$k] = MaterialMedia::model()->makeStatUrl($v);
                                    }
                                    $content = str_replace($arrPattern, $arrReplace, $content);
                                }
                                $materialMedia->template_mode = 0;
                                $materialMedia->template_id = 0;
                                $materialMedia->content = addslashes($content);
                            }
                            $materialMedia->save();
                        }
                    }
                    if ($materialMedia->hasErrors()) {
                        $flag = $materialMedia->errors;
                    }
                } else if ($_POST['Material']['material_type_id'] == 5) {//视频
                    $materialVideo->attributes = $_POST['MaterialVideo'];
                    if ($materialVideo->validate()) {
                        if ($_POST['MaterialVideo']['video_x'] && $_POST['MaterialVideo']['video_y']) {
                            $material->material_size = $_POST['MaterialVideo']['video_x'] . '*' . $_POST['MaterialVideo']['video_y'];
                        }
                        if ($_POST['MaterialVideo']['videopic_x'] && $_POST['MaterialVideo']['videopic_y']) {
                            $material->material_size = $_POST['MaterialVideo']['videopic_x'] . '*' . $_POST['MaterialVideo']['videopic_y'];
                        }

                        if ($material->save()) {
                            Oplog::add(); //添加日志

                            $materialVideo->material_id = $material->id;
                            if (!isset($_POST['MaterialVideo']['monitor_video'])) {
                                $materialVideo->monitor_video = 0;
                                $materialVideo->click_link = 'http://';
                            }
                            if (!isset($_POST['MaterialVideo']['reserve'])) {
                                $materialVideo->reserve = 0;
                                $materialVideo->reserve_pic_url = NULL;
                                $materialVideo->reserve_pic_link = 'http://';
                            }
                            if (!isset($_POST['MaterialVideo']['monitor']))
                                $materialVideo->monitor_link = 'http://';
                            $materialVideo->save();
                        }
                    }
                    if ($materialVideo->hasErrors()) {
                        $flag = $materialVideo->errors;
                    }
                }
            }

            if ($material->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">编辑失败</p>';
                foreach ($material->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            } else if ($flag) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($flag->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            } else {
                if ($_POST['Material']['material_type_id'] != $_POST['Material']['old_type']) {
                    if ($_POST['Material']['old_type'] == 1)
                        $materialText->delete();
                    else if ($_POST['Material']['old_type'] == 2)
                        $materialPic->delete();
                    else if ($_POST['Material']['old_type'] == 3)
                        $materialFlash->delete();
                    else if ($_POST['Material']['old_type'] == 4)
                        $materialMedia->delete();
                    else if ($_POST['Material']['old_type'] == 5)
                        $materialVideo->delete();
                }
            }
            die(json_encode($return));
        }
        $materialType = MaterialType::model()->getMaterialTypes();
        $set = array(
            'materialType' => $materialType,
            'material' => $material,
            'materialText' => $materialText,
            'materialPic' => $materialPic,
            'materialFlash' => $materialFlash,
            'materialMedia' => $materialMedia,
            'materialVideo' => $materialVideo,
            'templateMode' => $templateMode,
            'setting' => $setting
        );
        $this->renderPartial('edit', $set);
    }

    /**
     * 删除物料
     */
    public function actionDel() {
        
    }

    /**
     * 修改状态
     */
    public function actionStatus() {
        $user = Yii::app()->session['user'];
        $return = array('code' => 1, 'message' => '设置成功');
        if (isset($_POST['ids']) && count($_POST['ids'])) {
            $_POST['ids'] = (array) $_POST['ids'];
            $status = isset($_POST['status']) && $_POST['status'] == 1 ? 1 : -1;
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['ids']);
            $criteria->addColumnCondition(array('com_id' => $user['com_id']));
            Material::model()->updateAll(array('status' => $status), $criteria);
            Oplog::add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择站点');
        }
        die(json_encode($return));
    }

}