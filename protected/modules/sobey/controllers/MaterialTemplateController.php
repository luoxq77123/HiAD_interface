<?php

/**
 * 客户端物料控制器
 */
class MaterialTemplateController extends BaseController {

    /**
     * 客户端物料列表
     */
    public function actionList() {
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name,description, status';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('recommend' => 0));
        // 附加搜索条件
        if (isset($_GET['status']) && $_GET['status']) {
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        }
        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }
        $count = MaterialTemplate::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $materiallist = MaterialTemplate::model()->findAll($criteria);
        $status = array(1 => '启用', -1 => '删除');

        $setArray = array(
            'materiallist' => $materiallist,
            'pages' => $pager,
            'status' => $status
        );

        $this->renderPartial('list', $setArray);
    }
    
    public function actionAjaxGetList() {
        $user = Yii::app()->session['user'];
        $isRecommend = ($_REQUEST['templateMode']==1)? 1 : 0;
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name,description, status';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('status' => 1));
        $criteria->addColumnCondition(array('recommend' => $isRecommend));

        // 附加搜索条件
        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }
        $count = MaterialTemplate::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 3;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $materiallist = MaterialTemplate::model()->findAll($criteria);

        $setArray = array(
            'materiallist' => $materiallist,
            'pages' => $pager
        );

        $this->renderPartial('ajaxList', $setArray);
    }
    
    public function actionAjaxGetParam() {
        $return = array('code' => 1, 'message' => '添加成功');
        if (!isset($_POST['templateId'])) {
            $return['code'] = -1;
            $return['message'] = '参数不对';
            die(json_encode($return));
        }
        $tId = $_POST['templateId'];
        $data = MaterialTemplate::model()->find(array('select'=>'params', 'condition'=>'id=:id', 'params'=>array(':id'=>$tId)));
        if (!$data) {
            $return['code'] = -1;
            $return['message'] = '系统没有找到对应的模板';
            die(json_encode($return));
        }
        $return['result'] = unserialize($data['params']);
        echo json_encode($return);
    }

    /**
     * 添加客户端物料
     */
    public function actionAdd() {
        $material = new MaterialTemplate('add');
        if (isset($_POST['MaterialTemplate'])) {
            $return = array('code' => 1, 'message' => '添加成功');
            $material->attributes = $_POST['MaterialTemplate'];
            $flag = array();
            if ($material->validate()) {
                $user = Yii::app()->session['user'];
                $arrData = MaterialTemplate::model()->parseCode($_POST['MaterialTemplate']['code']);
                $material->com_id = $user['com_id'];
                $material->description = $_POST['MaterialTemplate']['description'];
                $material->params = serialize($arrData['params']);
                $material->html = $arrData['html'];
                $material->status = 1;
                $material->create_time = time();
                $material->save();
                if ($material->hasErrors()) {
                    $flag = $material->errors;
                }
            }

            if ($material->hasErrors()) {
                $return['code'] = -2;
                $return['message'] = '<span style="color:red;">错误提示：</span>';
                foreach ($material->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= $one;
                }
            } else if ($flag) {
                $return['code'] = -1;
                $return['message'] = '<span style="color:red;">添加失败：</span>';
                foreach ($flag->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }
        $set = array(
            'material' => $material
        );
        $this->renderPartial('add', $set);
    }

    /**
     * 编辑客户端物料
     */
    public function actionEdit() {
        $user = Yii::app()->session['user'];
        $id = $_GET['id'];
        $material = MaterialTemplate::model()->findByPk($id);
        $material->setScenario('edit');
        if (isset($_POST['MaterialTemplate'])) {
            $return = array('code' => 1, 'message' => '添加成功');
            $material->attributes = $_POST['MaterialTemplate'];
            $flag = array();
            if ($material->validate()) {
                $user = Yii::app()->session['user'];
                $arrData = MaterialTemplate::model()->parseCode($_POST['MaterialTemplate']['code']);
                $material->com_id = $user['com_id'];
                $material->description = $_POST['MaterialTemplate']['description'];
                $material->params = serialize($arrData['params']);
                $material->html = $arrData['html'];
                $material->status = 1;
                $material->save();
                if ($material->hasErrors()) {
                    $flag = $material->errors;
                }
            }

            if ($material->hasErrors()) {
                $return['code'] = -2;
                $return['message'] = '<span style="color:red;">错误提示：</span>';
                foreach ($material->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= $one;
                }
            } else if ($flag) {
                $return['code'] = -1;
                $return['message'] = '<span style="color:red;">添加失败：</span>';
                foreach ($flag->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }
        $set = array(
            'material' => $material
        );
        $this->renderPartial('edit', $set);
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
            MaterialTemplate::model()->updateAll(array('status' => $status), $criteria);
            Yii::app()->oplog->add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择站点');
        }
        die(json_encode($return));
    }

}