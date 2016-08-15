<?php

/**
 * 站点控制器
 */
class SiteController extends BaseController {

    /**
     * 站点列表
     */
    public function actionList() {
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'sort asc,createtime desc';
        $criteria->select = 'id,name,status,site_group_id';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));

        // 附加搜索条件
        if (isset($_GET['status']) && $_GET['status']) {
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        }
        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }

        $count = Site::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);

        $sitelist = Site::model()->findAll($criteria);
        $sitegroup = SiteGroup::model()->getSitegroup($user['com_id']);
        $status = array(1 => '启用', 0 => '删除', -1 => '禁用');
        $setArray = array(
            'sitelist' => $sitelist,
            'pages' => $pager,
            'sitegroup' => $sitegroup,
            'status' => $status
        );

        $this->renderPartial('list', $setArray);
    }

    /**
     * 添加站点
     */
    public function actionAdd() {
        $user = Yii::app()->session['user'];
        $site = new Site('add');

        if (isset($_POST['Site'])) {
            $return = array('code' => 1, 'message' => '添加成功');

            $site->attributes = $_POST['Site'];
            if ($site->validate()) {
                $site->com_id = $user['com_id'];
                $site->createtime = time();
                if ($site->save()) {
                    Yii::app()->oplog->add(); //添加日志
                }
            }
            if ($site->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($site->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }

        $sitegroup = array('0' => '请选择') + SiteGroup::model()->getSitegroup($user['com_id']);
        $this->renderPartial('add', array('site' => $site, 'sitegroup' => $sitegroup));
    }

    /**
     * 编辑站点
     */
    public function actionEdit() {
        $user = Yii::app()->session['user'];
        $id = $_GET['id'];
        $site = Site::model()->findByPk($id);
        $site->setScenario('edit');
        if (isset($_POST['Site'])) {
            $return = array('code' => 1, 'message' => '修改成功');

            $site->attributes = $_POST['Site'];
            if ($site->validate()) {
                if ($site->save()) {
                    Yii::app()->oplog->add(); //添加日志
                }
            }
            if ($site->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">修改失败</p>';
                foreach ($site->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }
        $sitegroup = array('0' => '请选择') + SiteGroup::model()->getSitegroup($user['com_id']);
        $this->renderPartial('edit', array('site' => $site, 'sitegroup' => $sitegroup));
    }

    /**
     * 删除站点 
     */
    public function actionDel() {
        $return = array('code' => 1, 'message' => '删除成功');
        if (isset($_POST['site']) && count($_POST['site'])) {
            $_POST['site'] = (array) $_POST['site'];
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['site']);
            $criteria->addColumnCondition(array('com_id' => Yii::app()->session['user']['com_id']));
            // Site::model()->deleteAll($criteria);
            site::model()->updateAll(array('status' => -1), $criteria);
            Yii::app()->oplog->add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择用户');
        }
        die(json_encode($return));
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
            Site::model()->updateAll(array('status' => $status), $criteria);
            Yii::app()->oplog->add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择站点');
        }
        die(json_encode($return));
    }

}