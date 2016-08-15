<?php

/**
 * SiteStat provides dynamic table model supports for some application environments
 * such as dynamic-generated database tables, or simple CRUD actions.
 * @property string $date the table name associate with the denamic model.
 * new record :
 * $model = new SiteStat('20121020'); 
 * //use table prefix:
 * $model = new SiteStat('20121020');
 * $model->id = $id;
 * $model->name = 'zhangxugg@163.com';
 * $model->save();
 * 
 * update:
 * $model = SiteStat::model('20121020')->findByPk(1);
 * if($model) {
 *   $model->name = 'zhangxugg@163.com'
 *   $model->save();
 * }
 * $list = $model->findAll();
 * 
 * use non-default database connection :
 * SiteStat::$db = Yii::app()->getCompoments('db-extra');
 * tips : you must define the database connection informations in config/main.php
 * 'components' => array(
 *     'db-extra' => array(
 *         'class' => 'CDbConnection',
 *         'connectionString' => 'mysql:host=localhost;dbname=cdcol;charset=utf8',
 *         'username' => 'root',
 *         'password' =>'',
 *         'tablePrefix' => '',
 *         'autoConnect' => false,
 *     ),
 * )
 * 
 *
 */
class SiteStat extends CActiveRecord {

    private $_date;
    private $_md;
    public static $_models;

    public function __construct($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_site;
        if ($date != null) {
            $this->_date = $date;
            parent::__construct();
        }
    }

    public static function model($date = '') {
        CActiveRecord::$db = Yii::app()->db_stat_site;
        if (isset(self::$_models[$date]))
            return self::$_models[$date];
        else {
            $model = self::$_models[$date] = new SiteStat(null);
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
        $model = new SiteStat(null);
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
        return "{{site_$date}}";
    }

    public function createStatTable() {
        $date = date("Ymd", time());
        $table = 'site_' . $date;
        $query = "
        CREATE TABLE IF NOT EXISTS `$table` (
          `id` int(10) NOT NULL auto_increment,
          `ad_id` int(10) default NULL COMMENT '¹ã¸æid',
          `position_id` int(10) default NULL COMMENT '¹ã¸æÎ»id',
          `order_id` int(10) default '0' COMMENT '¶©µ¥id',
          `client_id`  int(10) NULL DEFAULT 0 COMMENT '¿Í»§id',
          `seller_id` int(10) default '0' COMMENT 'ÏúÊÛid',
          `material_ids` varchar(255) default NULL COMMENT 'ËùÓÐÕ¹Ê¾ÎïÁÏid',
          `ip` int(10) default '0' COMMENT 'ip×ª»»ÕûÐÎ',
          `region_id` int(10) default '0' COMMENT 'µØÓòid',
          `connect_id` int(5) default '0' COMMENT '½ÓÈë·½Ê½id',
          `browser_id` int(5) default '0' COMMENT 'ä¯ÀÀÆ÷ÀàÐÍ',
          `language_id` int(5) default '0' COMMENT 'ä¯ÀÀÆ÷ÓïÑÔ',
          `system_id` int(5) default '0' COMMENT 'ÏµÍ³',
          `resolution_id` int(5) default '0' COMMENT '·Ö±æÂÊ',
          `referer_id` int(10) default '0' COMMENT 'À´Ô´Óò',
          `accessurl_id` int(10) default '0' COMMENT '±»·Ãurl',
          `is_click` tinyint(1) default '0' COMMENT 'ÊÇ·ñµã»÷ 1£ºÊÇ  0 ²»ÊÇ',
          `create_time` int(11) default NULL COMMENT '¹ã¸æÏÔÊ¾Ê±¼ä',
          `click_time` int(11) default NULL COMMENT '¹ã¸æµã»÷Ê±¼ä',
          `cost_mode` tinyint(1) default '0' COMMENT '¼Æ·ÑÄ£Ê½£»1.CPD; 2.CPM; 3.CPC',
          `cost` float(8,2) default '0' COMMENT '¼Æ·Ñ',
          `info` mediumtext COMMENT 'ÆäËûÐÅÏ¢À©Õ¹',
          `com_id` int(11) default NULL,
          UNIQUE KEY `id` (`id`),
          KEY `index` USING BTREE (`ad_id`,`position_id`,`order_id`,`client_id`,`seller_id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        Yii::app()->db_stat_site->createCommand($query)->execute();
    }
}