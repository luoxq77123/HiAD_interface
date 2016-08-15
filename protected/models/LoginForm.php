<?php

class LoginForm extends CFormModel {

    public $email;
    public $password;
    public $captcha;
    public $rememberMe = false;
    public $userInfo;

    public function rules() {
        return array(
            array('email, password, captcha', 'required', 'message' => '{attribute}不能为空'),
            array('captcha', 'checkCaptcha'),
            array('password', 'authenticate'),
        );
    }

    /**
     * 验证码验证
     * @param type $attribute
     * @param type $params 
     */
    public function checkCaptcha($attribute, $params) {
        if (!$this->hasErrors()) {
            $captcha = Yii::app()->session['captchaCode'];
            if (!isset($captcha['time']) || $captcha['time'] + 300 < time()) {
                $this->addError('captcha', '验证码过期。');
            } else if (strtolower($captcha['content']) != strtolower($this->captcha)) {
                $this->addError('captcha', '验证码错误。');
            }
        }
    }

    /**
     * 登陆验证
     * @param type $attribute
     * @param type $params 
     */
    public function authenticate($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = User::model()->findByAttributes(array('email' => $this->email));

            if ($user && $user->password == $user->hashPassword($this->password, $user->salt)) {
                $this->userInfo = $user;
                unset(Yii::app()->session['captchaCode']);
            }
            else
                $this->addError('password', '账号或密码错误。');
        }
    }

    public function attributeLabels() {
        return array(
            'email' => '用户名',
            'password' => '密码',
            'captcha' => '验证码',
            'rememberMe' => '记住登陆'
        );
    }

}

