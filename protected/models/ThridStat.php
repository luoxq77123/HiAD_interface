<?php

class ThridStat extends CActiveRecord {

    private $_date;
    private $_md;
    public static $_models;

    public function __construct($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_thrid;
        if ($date != null) {
            $this->_date = $date;
            parent::__construct();
        }
    }

    public static function model($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_thrid;
        if (isset(self::$_models[$date]))
            return self::$_models[$date];
        else {
            $model = self::$_models[$date] = new ThridStat(null);
            $model->_date = $date;
            $model->_md = new CActiveRecordMetaData($model);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    public function refreshMetaData() {
        $finder = self::model($this->_date);
        $finder->_md = new CActiveRecordMetaData($finder);
        if ($this !== $finder)
            $this->_md = $finder->_md;
    }

    protected function instantiate($attributes) {
        $model = new ThridStat(null);
        $model->_date = $this->_date;
        return $model;
    }

    public function getMetaData() {
        if ($this->_md !== null)
            return $this->_md;
        else
            return $this->_md = self::model($this->_date)->_md;
    }

    public function tableName() {
        return $this->getTableName($this->_date);
    }

    private function getTableName($date) {
        return "{{thrid_$date}}";
    }

}