<?php

class App extends CActiveRecord {

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
        return '{{app}}';
    }
    
     public function rules() {
        return array(
            array('name,sort,app_type_id', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('sort', 'numerical', 'integerOnly'=>true),
            array('name', 'acheckName', 'on' => 'add'),
            array('name', 'echeckName', 'on' => 'edit'),
            array('description,app_group_id','safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '应用名称',
            'sort' => '显示顺序',
            'description' => '说明',
            'app_type_id' => '应用类型',
            'app_group_id' => '应用分组'
        );
    }

    function afterSave() {
        parent::afterSave();
        // 删除缓存
        $user = Yii::app()->session['user'];
        $getByIds_cache = md5('model_Site_getAppsByComId'.$user['com_id']);
        Yii::app()->memcache->delete($getByIds_cache);
    }

    public function getAppsByComId($com_id) {
        $cache_name = md5('model_App_getAppsByComId'.$com_id);
        $apps = Yii::app()->memcache->get($cache_name);
        if (!$apps) {
            $data = $this->findAll(array(
                'select'=>'id,name',
                'condition'=>'com_id=:com_id',
                'order'=>'sort asc',
                'params'=>array(':com_id' => $com_id)
            ));
            foreach ($data as $one) {
                $apps[$one->id] = $one->name;
            }
            Yii::app()->memcache->set($cache_name, $apps, 300);
        }
        return $apps;
    }
    
    /**
     * 获取应用信息 区分apptype
     */
    public function getAppList() {
        $comId = Yii::app()->session['user']['com_id'];
        $apps = array();
        //$cache_name = md5('model_App_getAppList_'.$comId);
        //$apps = Yii::app()->memcache->get($cache_name);
        //if (!$apps) {
            $data = $this->findAll(array(
                'select'=>'id,name,app_type_id',
                'condition'=>'com_id=:com_id and status=:status',
                'order'=>'sort asc',
                'params'=>array(':com_id' => $comId, ':status' => 1)
            ));
            $appType = $this->getAppType();
            foreach ($data as $one) {
                $apps[$appType[$one->app_type_id]][$one->id] = $one->name;
            }
            //Yii::app()->memcache->set($cache_name, $apps, 300);
        //}
        return $apps;
    }

    public function relations() {
        return array(
            'SiteGroup' => array(self::BELONGS_TO, 'SiteGroup', 'app_group_id')
        );
    }
    
    public function getAppType() {
        return array(
            '1' => 'IOS应用',
            '2' => 'Android应用'
        );
    }

     /**
     * 名称唯一
     */
    public function acheckName() {
        if (!$this->hasErrors()) {
            $com_id = Yii::app()->session['user']['com_id'];
            
            $name=$this->count(array(
                'condition'=>'com_id=:com_id and name =:name',
                'params'=>array(':com_id' => $com_id,':name'=>$this->name)
            ));
            if($name > 0){
                $this->addError('name', '此名称已存在，请填入其他名称！');
            }
        }
    }

     /**
     * 名称唯一
     */
    public function echeckName() {
        if (!$this->hasErrors()) {
            $com_id = Yii::app()->session['user']['com_id'];
            
            $name=$this->count(array(
                'condition'=>'com_id=:com_id and name =:name and id !=:id',
                'params'=>array(':com_id' => $com_id,':name'=>$this->name,':id'=>$this->id)
            ));
            if($name > 0){
                $this->addError('name', '此名称已存在，请填入其他名称！');
            }
        }
    }

}