<?php

/**
 * 站点广告位控制器
 */
class SitePositionController extends BaseController {

    /**
     * 站点广告位首页
     */
    public function actionIndex() {
        $this->renderPartial('index');
    }

    /**
     * 站点广告位列表
     */
    public function actionList() {
        $this->renderPartial('list');
    }

    /**
     * 添加站点广告位
     */
    public function actionAdd() {
        $position = new Position('add');
        $sitePosition = new SitePosition();
        $adShows = AdShow::model()->getListByTypeId(1);
        if (isset($_POST['SitePosition']) && isset($_POST['Position'])) {
            $return = array('code' => 1, 'message' => '添加成功');
            $position->attributes = $_POST['Position'];
            // 自定义广告大小
            if ($_POST['size_defined']) {
                $position->position_size = $_POST['size_x'] . '*' . $_POST['size_y'];
            }
            $position->com_id = Yii::app()->session['user']['com_id'];
            $position->createtime = time();
            $position->ad_type_id = 1;
            //设置站点广告位model场景
            $sitePosition->setScenario($adShows[$_POST['Position']['ad_show_id']]['code']);
            $sitePosition->attributes = $_POST['SitePosition'];

            if ($position->validate() && $sitePosition->validate()) {
                if ($position->save()) {
                    $sitePosition->position_id = $position->id;
                    if ($sitePosition->save()) {
                        Yii::app()->oplog->add(); //添加日志
                    }
                }
            }

            if ($position->hasErrors() || $sitePosition->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($position->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
                foreach ($sitePosition->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }

            die(json_encode($return));
        }

        $sizes = PositionSize::model()->getSizes(1);
        $sites = Site::model()->getSitesByComId(Yii::app()->session['user']['com_id']);
        $sites = empty($sites)? array(0 => '请选择') : array(0 => '请选择') + $sites;
        $set = array(
            'sitePosition' => $sitePosition,
            'position' => $position,
            'sizes' => $sizes,
            'sites' => $sites,
            'adShows' => $adShows
        );
        $this->renderPartial('add', $set);
    }

    /**
     * 编辑站点广告位
     */
    public function actionEdit() {
        $id = $_GET['id'];
        $position = Position::model()->findByPk($id);
        $sitePosition = SitePosition::model()->find('position_id=:position_id', array(':position_id' => $id));
        $position->setScenario('edit');
        $sitePosition->setScenario('edit');
        $adShows = AdShow::model()->getListByTypeId(1);
        if (isset($_POST['SitePosition']) && isset($_POST['Position'])) {
            $return = array('code' => 1, 'message' => '修改成功');
            $position->attributes = $_POST['Position'];
            // 自定义广告大小
            if ($_POST['size_defined']) {
                $position->position_size = $_POST['size_x'] . '*' . $_POST['size_y'];
            }
            //设置站点广告位model场景
            $sitePosition->setScenario($adShows[$_POST['Position']['ad_show_id']]['code']);
            $sitePosition->attributes = $_POST['SitePosition'];
            if ($position->validate() && $sitePosition->validate()) {
                if ($position->save()) {
                    $sitePosition->save();
                    // 同步广告中的广告位名称
                    $attribe = array(
                        'position_name' => $_POST['Position']['name']
                    );
                    Ad::model()->updateAll($attribe, 'position_id=:position_id', array(':position_id' => $id));
                    Yii::app()->oplog->add(); //添加日志
                }
            }

            if ($position->hasErrors() || $sitePosition->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">修改失败</p>';
                foreach ($user->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
                foreach ($user->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }

            die(json_encode($return));
        }

        $sitePosition = $this->initialization($position->ad_show_id, $sitePosition);
        $sizes = PositionSize::model()->getSizes(1);
        $sites = array(0 => '请选择') + Site::model()->getSitesByComId(Yii::app()->session['user']['com_id']);
        $set = array(
            'sitePosition' => $sitePosition,
            'position' => $position,
            'sizes' => $sizes,
            'sites' => $sites,
            'adShows' => $adShows
        );
        $this->renderPartial('edit', $set);
    }

    /**
     * 删除站点广告位 
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
            Position::model()->updateAll(array('status' => $status), $criteria);
            Yii::app()->oplog->add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择用户');
        }
        die(json_encode($return));
    }

    function initialization($type, $sitePosition) {
        switch ($type) {
            case 1:
                $sitePosition->poptime = 0;
                $sitePosition->scroll = 0;
                $sitePosition->float_x = 1;
                $sitePosition->float_y = 1;
                $sitePosition->space_x = 0;
                $sitePosition->space_y = 0;
                break;
            case 2:
                $sitePosition->idle_take = 0;
                $sitePosition->poptime = 0;
                break;
            case 3:
                $sitePosition->idle_take = 0;
                $sitePosition->scroll = 0;
                break;
        }
        return $sitePosition;
    }

    /**
     * 广告位投放视图 
     */
    public function actionTable() {
        $this->renderPartial('table');
    }

    /**
     * 获取代码
     */
    public function actionGetCode() {
        $user = Yii::app()->session['user'];
        $positionName = Position::model()->getPosition($user['com_id']);
        $setting = Setting::model()->getSettings();

        $set = array(
            'positionName' => $positionName,
            'js' => $setting['AD_MAIN_JS_URL'],
            'postion_method' => 'HIMI_POSITION_INIT',
            'pre_method' => 'HIMI_PERLOAD'
        );

        $this->renderPartial('getCode', $set);
    }

}