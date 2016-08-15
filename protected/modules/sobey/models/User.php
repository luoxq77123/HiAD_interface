<?php

class User extends CActiveRecord {

    public $email;
    public $name;
    public $password_first;
    public $password_repeat;
    public $role_id;
    public $telephone;
    public $cellphone;
    public $description;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{user}}';
    }

    
    public function primaryKey() {
        return 'uid';
    }
    
    public function rules() {
        return array(
            array('email', 'required', 'message' => '{attribute}不能为空', 'on' => 'add'),
            array('name', 'required', 'message' => '{attribute}不能为空'),
            array('email', 'email', 'message' => '账号必须为email格式'),
            array('email', 'unique', 'message' => '{attribute}已存在，请填入其他账号'),
            array('password_first, password_repeat', 'required', 'message' => '{attribute}不能为空', 'on' => 'add'),
            array('password_first', 'length', 'min' => 6, 'max' => 18, 'message' => '密码长度在6-18位', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '管理员姓名',
            'email' => '账号',
            'password' => '密码',
            'password_first' => '密码',
            'password_repeat' => '确认密码',
            'role_id' => '角色',
            'telephone' => '电话',
            'cellphone' => '手机',
            'description' => '说明',
        );
    }

    public function validatePassword($password) {
        return $this->hashPassword($password, $this->salt) === $this->password;
    }

    public function hashPassword($password, $salt) {
        return md5(md5($password) . $salt);
    }

    public function generateSalt() {
        $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($str) - 1;
        $string = '';
        for ($i = 0; $i < 6; $i++) {
            $string .= $str[mt_rand(0, $len)];
        }
        return $string;
    }

    /**
     * 验证token信息
     */
    public function checkToken($token){
        $data = $this->getOneByToken($token);
        if (empty($data))
            return false;
        $strToken = $this->makeToken($data['email'], $data['uid'], $data['com_id']);
        if ($strToken != $token)
            return false;
        Yii::app()->session['user'] = array(
            'uid' => $data['uid'],
            'email' => $data['email'],
            'name' => $data['name'],
            'role_id' => $data['role_id'],
            'department_id' => $data['department_id'],
            'com_id' => $data['com_id']
        );
        return true;
    }

    /**
     * 根据token获取用户信息
     */
    public function getOneByToken($token) {
        $cache_name = md5('model_User_getOneByToken_' . $token);
        $data = Yii::app()->memcache->get($cache_name);
        if (!$data) {
            $data = $this->find(array(
                'select' => 'uid, email, name, department_id, role_id, com_id, status',
                'condition' => 'token=:token',
                'params' => array(':token' => $token)
            ));
            Yii::app()->memcache->set($cache_name, $data, 300);
        }
        return $data;
    }

    public function getUserById($com_id,$role_id){
        $cache_name = md5('model_User_getUserById' . $com_id);
        $user = Yii::app()->memcache->get($cache_name);
        if ($com_id) {
           $data = $this->findAll(array(
                'select'=>'uid,name,email',
                'condition'=>'com_id=:com_id and role_id=:role_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id,':role_id'=>$role_id),
            ));
            foreach ($data as $one) {
                $user[$one->uid] = array(
                    'id' => $one->uid,
                    'name' => $one->name,
                    'email' => $one->email,
                    'status' => $one->status
                );
            Yii::app()->memcache->set($cache_name, $user, 300);
            }
            return $user;
        }
    }

    function getUserByName($com_id){
        //$cache_name = md5('model_User_getUserById' . $com_id);
        //$user = Yii::app()->memcache->get($cache_name);
        if ($com_id) {
            $data = $this->findAll(array(
                'select'=>'uid,name,email,status',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id),
            ));
            foreach ($data as $one) {
                $user[$one->uid] = array(
                    'id' => $one->uid,
                    'name' => $one->name,
                    'email' => $one->email,
                    'status' => $one->status
                );
            //Yii::app()->memcache->set($cache_name, $user, 300);
            }
            return $user;
        }

    }
    
    /**
     * 生成接口token
     */
    public function makeToken($email, $uid, $comid) {
        return md5($email . '&' . $uid . '&' . $comid . '&IHIMI_ADMS');
    }

    // 时间字符串转换时间戳
    public function mstrToTime($strTime){
        $times = explode(" ", $strTime);
        $date = explode("-", $times[0]);
        $time = explode(":", $times[1]);
        $time[2] = isset($time[2])? $time[2] : 0;
        return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }
    
    public function getUserByRole($com_id, $role_id) {
        $cache_name = md5('model_User_getUserByRole_' . $com_id.'_'.$role_id);
        $user = Yii::app()->memcache->get($cache_name);
        if (!$user) {
            $data = $this->findAll(array(
                'select' => 'uid,name,email',
                'condition' => 'com_id=:com_id and role_id=:role_id',
                'order' => 'createtime desc',
                'params' => array(':com_id' => $com_id, ':role_id' => $role_id),
                    ));
            foreach ($data as $one) {
                $user[$one->uid] = array(
                    'id' => $one->uid,
                    'name' => $one->name,
                    'email' => $one->email
                );
            }
            Yii::app()->memcache->set($cache_name, $user, 300);
        }
        return $user;
    }

}