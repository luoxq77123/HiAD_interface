<?php

class VpCatalog extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{vp_catalog}}';
    }

    //获取栏目ID下的广告位ID(目前一个栏目位只能绑定一个广告位)
    public static function getpositionId($catalogid)
    {
        $sql = "select B.id from hm_vp_catalog A left join hm_position B on A.position_id = B.id and B.status = 1 where A.catalog_id=%d";
        $sql = sprintf($sql,$catalogid);
        return Yii::app()->db->createCommand()->setText($sql)->queryRow();
    }


}