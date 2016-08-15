<?php
class Ad extends CActiveRecord {

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
        return '{{ad}}';
    }
    
    public function rules() {
        return array(
            array('name', 'required', 'message' => '{attribute}不能为空','on' => 'add,edit'),
            array('description, com_id,com_name,uid,user_name,position_id,position_name,order_id,order_name,sehedule_id', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '广告名称',
            'description' => '广告说明',
            'com_name' => '公司名称',
            'user_name' => '用户名称 ',
            'position_name' => '广告位名称',
        );
    }
    
    public function relations() {
        return array(
            'SiteAd' => array(self::HAS_ONE, 'SiteAd', 'ad_id')
        );
    }
    
    public function getAdShowName($adTypeId, $showId) {
        $show = AdShow::model()->getPositionAdShows($adTypeId);
        return $show[$showId];
    }
    
    // 获取一条信息
    public function getOneById($id) {
        $data = $this->find('id=:id', array(':id'=>$id));
        return $data;
    }
    
    //根据广告位id获取相应广告
    public function getByPositionId($position_id) {
        $criteria = new CDbCriteria();
        $criteria->select = 'id, name, position_id, position_name, status, ads_end_time, ads_start_time';
        $criteria->addInCondition('Position_id', $position_id);
        $adlist = $this->findAll($criteria);
        $return = array();
        if (!empty($adlist)) {
            foreach($adlist as $val) {
                $return[$val->id] = $val;
            }
        }
        return $return;
    }
    
    // 获取所有广告
    public function getAll() {
        $user = Yii::app()->session['user'];
        $cache_name = md5('model_ad_getAll_'.$user['com_id']);
        $reData = Yii::app()->memcache->get($cache_name);
        if (!$reData) {
            $data = $this->findAll(array(
                'select'=>'id, name, position_id, position_name, order_id, order_name',
                'condition'=>'com_id=:com_id',
                'order'=>'id desc',
                'params'=>array(':com_id' => $user['com_id'])
            ));
            if (!empty($data)) {
                foreach ($data as $one) {
                    $reData[$one->id] = $one;
                }
            }
            Yii::app()->memcache->set($cache_name, $reData, 300);
        }
        return $reData;
    }
    
    // 获取所有内容 根据名称 或者 id
    public function getDataByNameOrIds($ids=array(), $name="") {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name,position_id,position_name,order_id,order_name,status,ads_start_time,ads_end_time,post_time';
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

    // 根据广告位投放广告
    public function setAdByPosition($positionId) {
        $user = Yii::app()->session['user'];
        $positioin = new Position();
        $data = $positioin->getPositionById($positionId);
        
        $adInfo = array();
        $adInfo['ad'] = array(
            'aid' => 0,
            'position_id' => $positionId,
            'com_id' => $user['com_id'],
            'position_size' => $data->position_size,
            'position_name' => $data->name,
            'position_status' => $data->status,
            'ad_show' => Ad::model()->getAdShowName($data->ad_type_id, $data->ad_show_id),
            'name' => "",
            'order_id' => 0,
            'description' => ""
        );

        Yii::app()->session['create_ad_info'] = $adInfo;
    } 
    
    /**
     * 获得广告列表和分页信息
     */
    public function getPagerList($adIds=array(), $adType=1) {
        $user = Yii::app()->session['user'];

        $criteria = new CDbCriteria();
        $criteria->order = 'id desc';
        $criteria->select = 'id,name,position_id,position_name,order_id,order_name,status,ads_start_time,ads_end_time,post_time';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type_id' => $adType));

        // 附加搜索条件
        if (isset($_GET['status']) && $_GET['status'] != 'all') {
            if ($_GET['status']>10){
                $criteria->addColumnCondition(array('status' => 1));
                switch($_GET['status']){
                case 11: // 未完成 包含：准备投放、正在投放
                    $criteria->addCondition("ads_end_time = 0 OR ads_end_time > ".time());
                    break;
                case 12: // 准备投放
                    $criteria->addCondition("ads_start_time > ".time());
                    break;
                case 13: // 正在投放
                    $criteria->addCondition("ads_start_time <= ".time());
                    $criteria->addCondition("ads_end_time = 0 OR ads_end_time>".time());
                    break;
                case 14: // 投放完成
                    $criteria->addCondition("ads_end_time != 0");
                    $criteria->addCondition("ads_end_time <= ".time());
                    break;
                }
            } else {
                $criteria->addColumnCondition(array('status' => $_GET['status']));
            }
        }

        if (isset($_GET['name']) && $_GET['name']) {
            $criteria->addSearchCondition('name', urldecode($_GET['name']));
        }

        if (isset($_GET['positionid']) && $_GET['positionid']) {
            $criteria->addColumnCondition(array('position_id' => $_GET['positionid']));
        }
        
        if (is_array($adIds) && !empty($adIds)) {
            $criteria->addInCondition('id', $adIds);
        }

        // 分页
        $count = $this->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        $adlist = $this->findAll($criteria);
        
        $data = array();
        $data['list'] = $adlist;
        $data['pager'] = $pager;
        
        return $data;
    }
    
    public function getAdStatus() {
        $status = array(
            '2' => '下线',
            '11' => '未完成',
            '12' => '准备投放',
            '13' => '正在投放',
            '14' => '投放完成',
            '0' => '草稿',
            '-1' => '删除'
        );
        return $status;
    }
    
    /**
     * 广告播放器类型
     * 0.片头广告、1.缓冲广告、2.暂停广告、3.片尾广告、4.插播广告
     */
    public function getPlayerCushion() {
        $data = array(
            //'0' => '片头广告',
            '1' => '缓冲广告',
            '2' => '暂停广告',
            '3' => '片尾广告',
            //'4' => '插播广告'
        );
        return $data;
    }
    
    /**
     * 插播广告展现方式
     * 展现方式：1居中悬浮、2顶部横幅、3底部横幅、4右下角悬浮、5左下角悬浮;仅插播广告使用
     */
    public function getShowType() {
        $data = array(
            '1' => '居中悬浮',
            '2' => '顶部横幅',
            '3' => '底部横幅',
            '4' => '右下角悬浮',
            '5' => '左下角悬浮'
        );
        return $data;
    }
    
    // 清空广告缓存
    public function cleanAdSession() {
        if (isset(Yii::app()->session['create_ad_info'])) {
            unset(Yii::app()->session['create_ad_info']);
        }
    }
    
    // 获取广告缓存
    public function getAdSession() {
        $data = array();
        if (isset(Yii::app()->session['create_ad_info'])) {
            $data = Yii::app()->session['create_ad_info'];
        }
        return $data;
    }
}