<?php

class Role extends CActiveRecord {

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
        return '{{role}}';
    }
    
    public function getRoles(){
        $cache_name = md5('model_Role_getRoles');

        $roleList = Yii::app()->memcache->get($cache_name);
        if (!$roleList) {
            $roleList = array();
            $criteria = new CDbCriteria();
            $criteria->order = 'sort asc';
            $role_data = $this->findAll($criteria);

            foreach ($role_data as $one) {
                $roleList[$one->id] = array(
                    'id' => $one->id,
                    'name' => $one->name,
                    'description' => $one->description
                );
            }
            Yii::app()->memcache->set($cache_name, $roleList, 300);
        }
        return $roleList;
    }
}