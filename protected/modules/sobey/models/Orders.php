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
            array('price', 'numerical', 'message'=>'{attribute}必须是有效的数字', 'on' => 'add,edit'),
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
        $cache_name = md5('model_order_getOrdersList_'.$user['com_id']);
        Yii::app()->memcache->delete($cache_name);
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

    // 获取所有内容 根据名称 或者 id
    public function getDataByNameOrIds($ids=array(), $name="") {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        // 附加搜索条件
        if (is_array($ids)&&!empty($ids)) {
            $criteria->addInCondition('id',$ids);
        }
        if (isset($name) && $name) {
            $criteria->addSearchCondition('name', $name);
        }
        $data = $this->findAll($criteria);
        return $data;
    }

    /**
     * 获取订单列表 根据公司id
     */
    public function getOrdersList($com_id) {
        $cache_name = md5('model_order_getOrdersList_'.$com_id);
        $orderName = Yii::app()->memcache->get($cache_name);
        if (!$orderName) {
            $data = $this->findAll(array(
                    'select'=>'id,name,client_company_id,start_time,end_time,createtime,status,price',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
            $orderName = array();
            foreach ($data as $one) {
                $orderName[$one->id]['name'] = $one->name;
                $orderName[$one->id]['client_company_id'] = $one->client_company_id;$orderName[$one->id]['client_contact_id'] = $one->client_contact_id;
                $orderName[$one->id]['start_time'] = $one->start_time;
                $orderName[$one->id]['end_time'] = $one->end_time;
                $orderName[$one->id]['createtime'] = $one->createtime;
                $orderName[$one->id]['status'] = $one->status;
                $orderName[$one->id]['price'] = $one->price;
            }
            Yii::app()->memcache->set($cache_name, $orderName, 300);
        }
        return $orderName;
    }
    
    public function getStatus() {
        return array(
            '-1' => '删除',
            '0' => '禁用',
            '1' => '启用',
        );
    }

}