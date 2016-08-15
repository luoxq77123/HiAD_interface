<?php

class Orders extends CActiveRecord {

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
        return '{{orders}}';
    }
    
    public function rules() {
        return array(
            array('name,client_company_id', 'required', 'message' => '{attribute}不能为空','on' => 'add,edit'),
            array('salesman_id, client_contact_id,proxy_agency_id,proxy_contact_id,other_contact_id,externalID,start_time,end_time,description,limit_day,limit_all,price', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '订单名称',
            'client_company_id' => '广告客户',
            'salesman_id' => '销售人员',
            'client_contact_id' => '广告客户联系人 ',
            'proxy_agency_id' => '代理机构',
            'proxy_contact_id' => '代理机构联系人',
            'other_contact_id' => '其他联系人',
            'externalID' => '外部ID',
            'start_time' => '开始日期',
            'end_time' => '结束日期 ',
            'description' => '说明',
            'limit_day' => '限制每日投放数量',
            'limit_all' => '订单总量控制',
            'price'=>'价格'
        );
    }
    
    public function relations() {
        return array(
            'ClientCompany' => array(self::BELONGS_TO, 'ClientCompany', 'client_company_id')
        );
    }
    
    public function getByIds($com_id, $orders_ids){
        $cache_name = md5('model_Position_getByIds'.$com_id);
        $orders = Yii::app()->memcache->get($cache_name);
        $key = md5(serialize($orders_ids));
        if (!isset($orders[$key])) {
            $criteria = new CDbCriteria();
            $criteria->order = 't.createtime desc';
            $criteria->addColumnCondition(array('t.com_id' => $com_id));
            $criteria->addInCondition('t.id', $orders_ids);
            $orders_data = $this->with('ClientCompany')->findAll($criteria);

            $orders = $orders ? $orders : array();
            $orders[$key] = array();
            foreach($orders_data as $one){
                $orders[$key][$one->id] = array('company_id' => $one->ClientCompany->id,
                    'company_name' => $one->ClientCompany->name,
                    'id' => $one->id,
                    'name' => $one->name
                );
            }
            Yii::app()->memcache->set($cache_name, $orders, 300);
        }
        return $orders[$key];
    }

        // 获取一条信息
    public function getOneById($id) {
        $data = $this->find('id=:id', array(':id' => $id));
        return $data;
    }

    public function getOrders($com_id){
        if($com_id){
            $data = $this->findAll(array(
                'select'=>'id,name',
                'condition'=>'com_id=:com_id',
                'order'=>'createtime desc',
                'params'=>array(':com_id'=>$com_id)
            ));
            foreach ($data as $one) {
                $orders[$one->id] =$one->name;
            }
        }
        return $orders;
    }

    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_Position_getByIds'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
        
        $getByIds_cache = md5('model_order_getOrderName_'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);

       // $getByIds_cache = md5('model_order_getOrderCom_'.$user['com_id']);
       // Yii::app()->memcache->delete($getByIds_cache);
    }
    public function getOrderName($com_id){
        $cache_name = md5('model_order_getOrderName_'.$com_id);
        $orderName = Yii::app()->memcache->get($cache_name);
        if (!$orderName) {
            $data = $this->findAll(array(
                    'select'=>'id, name',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
             $orderName = array();
            foreach ($data as $one) {
                $orderName[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $orderName, 300);
        }
        return $orderName;
    }
    
    // 更新订单花费
    public function updateCostById($id, $cost) {
        $Orders = $this->findByPk($id);
        if ($Orders) {
            $Orders->cost = $Orders->cost+$cost;
            $Orders->save();
        }
    }
}