<?php

class Menu extends CActiveRecord {

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
        return '{{menu}}';
    }
    
    public function getTree(){
        $cache_name = md5('model_Menu_getTree');

        $tree = Yii::app()->memcache->get($cache_name);
        if (!$tree) {
            $criteria = new CDbCriteria();
            $criteria->order = 'sort asc';
            $criteria->addColumnCondition(array('status' => 1));
            $menu_data = $this->findAll($criteria);

            $tree = array();
            foreach ($menu_data as $one) {
                $url = $one->route ? Yii::app()->createUrl($one->route) : '';
                $tmp = array(
                    'id' => $one->id,
                    'name' => $one->name,
                    'url' => $url,
                    'system' => $one->system
                );
                if($one->parent_id){
                    $tree[$one->parent_id]['child'][$one->id] = $tmp;
                }else{
                    $tree[$one->id] = isset($tree[$one->id]) ? $tree[$one->id] + $tmp : $tmp;
                }
            }
            Yii::app()->memcache->set($cache_name, $tree, 300);
        }
        return $tree;
    }
}