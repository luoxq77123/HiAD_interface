<?php 
class AccountController extends CController {
    private $_params;

    function __construct(){
        $this->_params = json_decode(stripslashes($_POST['parameter']), true);
    }
    /**
    *添加用户
    */
    public function bindAccount() {
        $return = array();
        if (!isset($this->_params['userName']) || !isset($this->_params['email']) || !isset($this->_params['password']) || !isset($this->_params['addtime']) || !isset($this->_params['expiredtime'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        $return['returnCode'] = '101';
        $return['returnDesc'] = '处理失败';
        $com = new Company();
        $comUser = new User();
        $uid = 0;
        $com_id = 0;
        $createtime = User::model()->mstrToTime($this->_params['addtime']);
        $token = '';
        $comUser->name = $this->_params['userName'];
        $comUser->email = $this->_params['email'];
        $comUser->role_id = 1;
        if ($comUser->validate()) {
            $comUser->com_id = $com_id;
            $comUser->salt = $comUser->generateSalt();
            $comUser->password = md5($this->_params['password'] . $comUser->salt);
            $comUser->createtime = $createtime;
            $comUser->department_id = 0;
            if (!$comUser->save()) {
                return $return;
            }
            $uid = $comUser->attributes['uid'];
        }
        if ($comUser->hasErrors()) {
            foreach ($comUser->errors as $item) {
                foreach ($item as $one)
                   $return['returnDesc'] .= '<p>' . $one . '</p>';
            }
            return $return;
        }
        $com->name = $this->_params['userName'];
        if ($com->validate()) { 
            $com->createtime = time();
            $com->name = $this->_params['userName'];
            $com->super_uid = $uid;
            $com->expiredtime = $this->_params['expiredtime'];
            if (!$com->save()) {
                return $return;
            }
            $com_id = $com->attributes['id'];
            $token = User::model()->makeToken($this->_params['email'], $uid, $com_id);
            // 更新用户com_id
            $comUser->com_id = $com_id;
            $comUser->token = $token;
            if (!$comUser->save()) {
                return $return;
            }
        }
        if ($com->hasErrors()) {
            foreach ($com->errors as $item) {
                foreach ($item as $one)
                    $return['returnDesc'] .= '<p>' . $one . '</p>';
            }
            return $return;
        }
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $return['returnData'] = array('verifyCode'=>$token);
        return $return;
    }
    
    /**
    *修改用户
    */
    public function modifyAccount() {
        $return = array();
		if (!isset($this->_params['email']) || !isset($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $comUser = User::model()->getOneByToken($this->_params['verifyCode']);
        if (empty($comUser)) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '用户信息不存在';
            return $return;
        }
        $token = User::model()->makeToken($this->_params['email'], $comUser->uid, $comUser->com_id);
        if($this->_params['verifyCode'] != $token){
            $return['returnCode'] = '101';
            $return['returnDesc'] = '用户验证失败';
            return $return;
        }
        if (isset($this->_params['userName']) && $this->_params['userName']) {
            $comUser->name = $this->_params['userName'];//更新用户表的姓名
        }
        if (isset($this->_params['password']) && $this->_params['password']) {
            $comUser->salt = $comUser->generateSalt();
            $comUser->password = md5($this->_params['password'] . $comUser->salt);
        }
        if (!$comUser->save()) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '处理失败';
            return $return;
        }
        if ($comUser->hasErrors()) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '处理失败';
            foreach ($comUser->errors as $item) {
                foreach ($item as $one){
                    $return['returnDesc'] .= '<p>' . $one . '</p>';
                }
            }
            return $return;
        }
        if (isset($this->_params['userName']) && $this->_params['userName']) {
            $attribe = array('name'=>$this->_params['userName']);
            Company::model()->updateAll($attribe, 'id=:id', array(':id' => $comUser['com_id']));//更新用户企业的名字
        }
        return $return;
    }

    /**
    *停用客户
    */
    public function adminAccount(){
		$return = array();
		if (!isset($this->_params['status']) || !isset($this->_params['email']) || !isset($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        $criteria = new CDbCriteria();
        $criteria->select = 'uid,name,email,status,createtime,com_id';
        $criteria->addColumnCondition(array('email' => $this->_params['email']));
        $comUser = User::model()->find($criteria);
        $token = User::model()->makeToken($this->_params['email'], $comUser->uid, $comUser->com_id);
        if($this->_params['verifyCode'] != $token){
            $return['returnCode'] = '101';
            $return['returnDesc'] = '用户验证失败';
            return $return;
        }
        $comUser->status = $this->_params['status'];//更新用户表的状态信息
    
        if (!$comUser->save()) {
            $return['returnCode'] = '101';
            $return['returnDesc'] = '处理失败';
            return $return;
        }
        if ($comUser->hasErrors()) {
            foreach ($comUser->errors as $item) {
                foreach ($item as $one){
                    $return['returnDesc'] .= '<p>' . $one . '</p>';
                }
            }
            return $return;
        }   
        $attribe = array('status'=>$this->_params['status']);
        /*
        **更新用户企业的状态
        */
        Company::model()->updateAll($attribe, 'id=:id', array(':id' => $comUser['com_id']));
        return $return; 
    }
    
    /**
    *删除用户
    */
    public function deleteAccount(){
        $return = array();
        if (!isset($this->_params['email']) || !isset($this->_params['verifyCode'])) {
            $return['returnCode'] = '200';
            $return['returnDesc'] = '接口参数错误';
            return $return;
        }
        $return['returnCode'] = '100';
        $return['returnDesc'] = '处理成功';
        
        $criteria = new CDbCriteria();
        $criteria->select = 'uid,email,com_id';
        $criteria->addColumnCondition(array('email' => $this->_params['email']));
        $comUser = User::model()->find($criteria);
        $token = User::model()->makeToken($this->_params['email'], $comUser->uid, $comUser->com_id);
        if($this->_params['verifyCode'] != $token){
            $return['returnCode'] = '101';
            $return['returnDesc'] = '用户验证失败';
            return $return;
        }
        $text_id = "";//对各种物料材质赋一个初始值
        $pic_id = "";
        $flash_id = "";
        $media_id = "";
        $video_id = "";
        $www_root = dirname(Yii::app()->BasePath);
        
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('com_id' => $comUser['com_id']));
        $schedules = Schedule::model()->findAll($criteria);
        $schedule = "";
        foreach ($schedules as $key=>$val){
            if($schedule == ""){
                $schedule = $val['id'];
            } else {
                $schedule = $schedule.",".$val['id'];
            }
        }
        Position::model()->deleteAll('com_id = '.$comUser['com_id'].'');//删除广告位信息
        ClientCompany::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除广告公司信息
        ClientContact::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除订单客户信息
        Orders::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除订单信息
        StatisticsMaterial::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除统计信息
        StatisticsAd::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除广告统计信息
        StatisticsOrder::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除订单统计信息
        StatisticsPosition::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除广告位统计信息
        StatisticsSeller::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除销售统计信息
        StatisticsClient::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除物料统计信息
        /*
        **根据排期id，删除与之对应的排期时间。
        */
        if($schedule != ""){
            ScheduleTime::model()->deleteAll('schedule_id in ('.$schedule.')');
        }
        Schedule::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除排期信息
        SiteAd::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除投放广告的一些参数
        AppAd::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除投放广告的物料一些参数
        Ad::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除广告信息
        $criteria = new CDbCriteria();
        $criteria->select = 'id,name,material_type_id,com_id';
        $criteria->addColumnCondition(array('com_id' => $comUser['com_id']));
        $comMaterial = Material::model()->findAll($criteria);
        $strs = array();
        foreach($comMaterial as $key=>$vals) {
            $strs[$key]['materialId'] = $vals['id'];
            $strs[$key]['materialType'] = $vals['material_type_id'];
            switch($strs[$key]['materialType']){//保存不同物料材质所对应的id
                case 1:
                {
                    if($text_id == ""){
                        $text_id = $vals['id'];
                    } else {
                        $text_id = $text_id.",".$vals['id'];
                    }
                    break;
                }
                case 2:
                {
                    if($pic_id == ""){
                        $pic_id = $vals['id'];
                        } else {
                        $pic_id = $pic_id.",".$vals['id'];
                        }
                        break;
                }
                case 3:
                {
                    if($flash_id == ""){
                        $flash_id = $vals['id'];
                        } else {
                        $flash_id = $flash_id.",".$vals['id'];}
                        break;
                }
                case 4:
                {
                    if($media_id == ""){
                        $media_id  = $vals['id'];
                        } else {
                        $media_id  = $media_id .",".$vals['id'];}
                        break;
                }
                case 5:
                {
                    if($video_id == ""){
                        $video_id = $vals['id'];
                        } else {
                        $video_id =$video_id.",".$vals['id'];}
                        break;
                }
            }
        }
        if($text_id != ""){
            $materialText = MaterialText::model()->deleteAll('material_id in('.$text_id.')');
        }
        if ($pic_id != ""){
            $materialPic = MaterialPic::model()->getByMaterialPic($pic_id);
            foreach($materialPic as $val){
                $myfile = $www_root . $val['materialSrc'];
                if (file_exists($myfile)) {
                    $result=unlink ($myfile);//释放物料图片的物理空间
                }
            }
            $materialPic = MaterialPic::model()->deleteAll('material_id in('.$pic_id.')');//删除物理图片信息
        }
        if ($flash_id != "") {
            $materialFlash = MaterialFlash::model()->getByMaterialFlash($flash_id);
            foreach($materialFlash as $val){
                $myfile = $www_root . $val['materialSrc'];
                if (file_exists($myfile)) {
                    $result=unlink ($myfile);
                }
            }
            $materialFlash = MaterialFlash::model()->deleteAll('material_id in('.$flash_id.')');
        }
        if ($media_id != "") {
            $materialMedia = MaterialMedia::model()->getParamsByMaterialIds($media_id);
            $adLeftSrc = array();//保存params的信息
            foreach($materialMedia as $key=>$val) {
                $adLeftSrc = $val['params'];
                $adLeftSrcsUrl = array();//保存富媒体物料的地址
                foreach($adLeftSrc as $key=>$val) {
                    if($val['type'] == 1){
                        $adLeftSrcsUrl = $www_root .Substr($val['value'],19);
                        if (file_exists($adLeftSrcsUrl)) {
                            $result=unlink ($adLeftSrcsUrl);
                        }
                    }
                }
            }
            $materialMedia = MaterialMedia::model()->deleteAll('material_id in('.$media_id.')');
        }
        if ($video_id != "") {
            $materialVideo = MaterialVideo::model()->getByMaterialVideo($video_id);
            foreach($materialVideo as $val){
                $myfile = $www_root . $val['materialSrc'];
                if (file_exists($myfile)) {
                    $result=unlink ($myfile);
                }
            }
            $materialVideo = MaterialVideo::model()->deleteAll('material_id in('.$video_id.')');
        }
        MaterialTemplate::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除物料模板信息
        Material::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除物料信息
        Company::model()->deleteAll('id = ('.$comUser['com_id'].')');//删除企业信息
        User::model()->deleteAll('com_id = ('.$comUser['com_id'].')');//删除用户信息
        return $return;
    }
}
?>