<?php
class ClientContact extends CActiveRecord {

    public $email_re;
    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{client_contact}}';
    }
    
    public function rules() {
        return array(
            array('name,email,email_re,client_company_id', 'required', 'message' => '{attribute}不能为空','on' => 'add,edit'),
            array('email', 'email', 'message' => '账号必须为email格式'),
            array('cellphone','match','pattern'=>'/^[1][1-9][0-9]{9}$/','message'=>'手机号码由11位数字组成','on' => 'add,edit'),
            array('telephone,fax','length', 'min' => 7, 'max' => 20, 'message' => '号码长度在7-20位','on' => 'add,edit'),
            array('address','length', 'max' => 20, 'message' => '地址长度在不超过120位','on' => 'add,edit'),
            array('position','length', 'max' => 50, 'message' => '职务长度在不超过120位','on' => 'add,edit'),
            array('email', 'compare', 'compareAttribute' => 'email_re', 'message' => '两次邮箱输入不一致', 'on' => 'add,edit'),
            array('position,description', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '联系人姓名',
            'email' => '电子邮件',
            'email_re' => '确认邮件',
            'client_company_id' => '所属公司 ',
            'position' => '职务',
            'cellphone' => '手机',
            'telephone' => '电话',
            'fax' => '传真',
            'address' => '地址',
            'description' => '说明 ',
            'attention' => '邀请联系人查看相关报告',
        );
    }

    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_ClientContact_getClientContactById_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }

    public function getClientContactById($com_id){
        $cache_name = md5('model_ClientContact_getClientContactById_'.$com_id);
        $clientContact = Yii::app()->memcache->get($cache_name);
        if (!$clientContact) {
           $data = $this->findAll(array(
                'select'=>'id,name,email',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id),
            ));
           $clientContact = array();
            foreach ($data as $one) {
                $clientContact[$one->id] = array(
                    'id' => $one->id,
                    'name' => $one->name,
                    'email' => $one->email
                );
            }
            Yii::app()->memcache->set($cache_name, $clientContact, 300);
        }
        return $clientContact;
    }


}