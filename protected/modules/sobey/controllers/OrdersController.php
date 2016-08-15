    <?php

/**
 * 订单控制器
 */
class OrdersController extends BaseController {

    /**
     * 订单列表
     */
    public function actionList() {
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'id,name,client_company_id,start_time,end_time,createtime';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        // 附加搜索条件
        if (isset($_GET['status'])) {
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        } else {
            $_GET['status'] = 1;
            $criteria->addColumnCondition(array('status' => $_GET['status']));
        }
        if (isset($_GET['com']) && $_GET['com']) {
            $criteria->addColumnCondition(array('client_company_id' => $_GET['com']));
        }
        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }

        // 分页
        $count = Orders::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $orderlist = Orders::model()->findAll($criteria);
        $com = array(0 => '-请选择-') + @CHtml::listData(ClientCompany::model()->getCom($user['com_id']), 'id', 'name');
        $status = Orders::model()->getStatus();

        $setArray = array(
            'orderlist' => $orderlist,
            'pages' => $pager,
            'com' => $com,
            'status' => $status
        );

        $this->renderPartial('list', $setArray);
    }


    /**
     * 添加订单
     */
    public function actionAdd() {
        $user = Yii::app()->session['user'];
        $order = new Orders('add');

        if (isset($_POST['Orders'])) {
            $return = array('code' => 1, 'message' => '添加成功');
            if ($_POST['Orders']['start_time'])
                $_POST['Orders']['start_time'] = strtotime($_POST['Orders']['start_time']);

            if (isset($_POST['Orders']['end_time']) && $_POST['Orders']['end_time'])
                $_POST['Orders']['end_time'] = strtotime($_POST['Orders']['end_time']);

            $order->attributes = $_POST['Orders'];
            if ($order->validate()) {
                $order->com_id = $user['com_id'];
                $order->createtime = time();
                if ($order->save()) {
                    oplog::add(); //添加日志
                }
                // 返回添加的order id和name
                $message['id'] = Yii::app()->db->getLastInsertID();
                $message['name'] = $_POST['Orders']['name'];
                $return['message'] = $message;
            }
            if ($order->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($order->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }

        $com = array('' => '-请选择-') + @CHtml::listData(ClientCompany::model()->getCom($user['com_id']), 'id', 'name');
        $roleuser = array(0 => '-请选择-') + @CHtml::listData(User::model()->getUserByRole($user['com_id'], 3), 'id', 'name');
        $contact = array(0 => '-请选择-') + @CHtml::listData(ClientContact::model()->getClientContactById($user['com_id']), 'id', 'name');
        $data = array(
            'order' => $order,
            'com' => $com,
            'roleuser' => $roleuser,
            'contact' => $contact
        );
        $this->renderPartial('add', $data);
    }

    /**
     * 编辑订单
     */
    public function actionEdit() {
        $user = Yii::app()->session['user'];
        $id = $_GET['id'];
        $order = Orders::model()->findByPk($id);
        $order->setScenario('edit');

        if (isset($_POST['Orders'])) {
            $return = array('code' => 1, 'message' => '修改成功');
            if ($_POST['Orders']['start_time'])
                $_POST['Orders']['start_time'] = strtotime($_POST['Orders']['start_time']);

            if (isset($_POST['Orders']['end_time']) && $_POST['Orders']['end_time'])
                $_POST['Orders']['end_time'] = strtotime($_POST['Orders']['end_time']);

            $order->attributes = $_POST['Orders'];
            if ($order->validate()) {
                if ($order->save()) {
                    oplog::add(); //添加日志
                }
                // 同步广告中的订单名称
                $attribe = array(
                    'order_name' => $_POST['Orders']['name']
                );
                Ad::model()->updateAll($attribe, 'order_id=:order_id', array(':order_id' => $id));
            }
            if ($order->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">修改失败</p>';
                foreach ($order->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
            }
            die(json_encode($return));
        }

        $com = array('' => '-请选择-') + @CHtml::listData(ClientCompany::model()->getCom($user['com_id']), 'id', 'name');
        $roleuser = array(0 => '-请选择-') + @CHtml::listData(User::model()->getUserByRole($user['com_id'], 3), 'id', 'name');
        $contact = array(0 => '-请选择-') + @CHtml::listData(ClientContact::model()->getClientContactById($user['com_id']), 'id', 'name');
        $data = array(
            'order' => $order,
            'com' => $com,
            'roleuser' => $roleuser,
            'contact' => $contact
        );
        $this->renderPartial('edit', $data);
    }

    /**
     * 删除订单 
     */
    public function actionSetStatus() {
        $return = array('code' => 1, 'message' => '操作成功');
        if (isset($_POST['order']) && count($_POST['order']) && isset($_POST['status'])) {
            $_POST['order'] = (array) $_POST['order'];
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['order']);
            $criteria->addColumnCondition(array('com_id' => Yii::app()->session['user']['com_id']));
            $status = $_POST['status'];
            Orders::model()->updateAll(array('status' => $status), $criteria);
            Yii::app()->oplog->add(); //添加日志
        } else {
            $return = array('code' => -1, 'message' => '未选择订单');
        }
        die(json_encode($return));
    }
}