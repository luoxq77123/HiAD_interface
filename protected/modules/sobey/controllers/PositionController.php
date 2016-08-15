<?php

/**
 * 广告位控制器
 */
class PositionController extends BaseController {

    /**
     * 添加广告位
     */
    public function actionAdd() {
        if (!isset($_GET['token'])) {
            if(isset($_POST['token'])) {
               $return['returnCode'] = '200';
                $return['returnDesc'] = '接口参数错误';
                return $return; 
            }
        } else {
            $token = $_GET['token'];
        }
        $model = new Position();
        if (isset($_POST['Position'])) {
            $user = User::model()->getOneByToken($_GET['token']);
            $com_id = $user['com_id'];
            $model->sort = $_POST['Position']['sort'];
            $model->name = $_POST['Position']['name'];
            $model->description = $_POST['Position']['description'];
            $model->ad_show_id = 6;
            $model->ad_type_id = 1;
            $model->com_id = $com_id;
            $model->createtime = time();
            $return = array('code' => 1, 'message' => '添加成功');
            if ($model->validate()) {
                if (!$model->save()) {
                    $return['code'] = -1;
                    $return['message'] = '<p style="color:red;">添加失败</p>';
                    die(json_encode($return));
                }
                $sitePosition = new SitePosition;
                $sitePosition->position_id = $model->id;
                $sitePosition->site_id = 0;
                if (!$sitePosition->save()) {
                    $model->delete();
                    $return['code'] = -1;
                    $return['message'] = '<p style="color:red;">添加失败</p>';
                    die(json_encode($return));
                }
            }
            if ($model->hasErrors()) {
                $return['code'] = -1;
                $return['message'] = '<p style="color:red;">添加失败</p>';
                foreach ($model->errors as $item) {
                    foreach ($item as $one)
                        $return['message'] .= '<p>' . $one . '</p>';
                }
                die(json_encode($return));
            }
            die(json_encode($return));
        }
        $set = array(
            'position' => $model,
            'token' => $token
        );
        $this->renderPartial('add', $set);
    }

    /**
     * 编辑广告位
     */
    public function actionEdit() {
        
    }

    /**
     * 删除广告位 
     */
    public function actionDel() {
        
    }
}