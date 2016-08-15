<?php

class AppAd extends CActiveRecord {

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
        return '{{app_ad}}';
    }

    public function relations() {
        return array(
            'ClientCompany' => array(self::BELONGS_TO, 'ClientCompany', 'client_company_id')
        );
    }
    
    // 获取id根据广告id
    public function getIdByAdId($adId){
        $data = $this->find(array(
            'select'=>'id',
            'condition'=>'ad_id=:ad_id',
            'params'=>array(':ad_id'=>$adId),
        ));
        return ($data==null)? 0 : $data['id'];
    }
    
    public function getOneByAdId($aid){
        $data = $this->find('ad_id=:ad_id', array(':ad_id'=>$aid));
        return $data;
    }
    
    //优先级模式
    public function getPriorityMode(){
        $priority = array(
            '1' => '独占',
            '2' => '标准',
            '3' => '补余',
            '4' => '底层'
        );
        return $priority;
    }
    
    //优先级列表
    public function getPriorityList(){
        $priority = array(
            '1' => '1级',
            '2' => '2级',
            '3' => '3级',
            '4' => '4级',
            '5' => '5级',
            '6' => '6级',
            '7' => '7级',
            '8' => '8级',
            '9' => '9级',
            '10' => '10级'
        );
        return $priority;
    }
    
    //权重列表
    public function getWeightList(){
        $weight = array(
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10'
        );
        return $weight;
    }
    
    //计费方式
    public function getCostMode(){
        $costMode = array(
            '1' => '每日费用(CPD)',
            '2' => '每千次展现费用(CPM)',
            '3' => '每次点击费用(CPC)'
        );
        return $costMode;
    }
    
    //显示每日投放数量方式
    public function getLimitDayMode(){
        $data = array(
            '1' => '展现',
            '2' => '点击'
        );
        return $data;
    }
    
    //限制对独立访客的展现次数方式
    public function getLimitOneShowMode(){
        $data = array(
            '1' => '每天',
            '2' => '每小时',
            '3' => '每30分钟',
            '4' => '每20分钟',
            '5' => '每10分钟',
            '6' => '每分钟'
        );
        return $data;
    }
    
    //定向选项
    public function getDirectionalType(){
        $data = array(
            '1' => '地域',
            '2' => '接入方式',
            '3' => '时间',
            '4' => '手机品牌',
            //'5' => '浏览器语言',
            '6' => '投放平台',
            '7' => '分辨率'
        );
        return $data;
    }
    
    //定向选项
    public function getDirectionalMode(){
        $data = array(
            '1' => '等于',
            //'0' => '不等于'
        );
        return $data;
    }
}