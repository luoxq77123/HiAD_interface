<?php

class AppAd extends CActiveRecord {

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

    public function getMaterailByAdId($aid) {
        $criteria1 = new CDbCriteria();
        $criteria1->select = 'ad_id, mrotate_mode, mrotate_time, material';
        $criteria1->addColumnCondition(array('ad_id' => $aid));
        $data = $this->find($criteria1);
        $return = array();
        if (!empty($data)) {
            $return['mrotate_mode'] = $data->mrotate_mode;
            $return['mrotate_time'] = $data->mrotate_time;
            $return['material'] = unserialize($data->material);
        }
        return $return;
    }
    
    /**
     * ��ȡ�ͻ��˹��Ͷ������ ��ݹ��id
     */
    public function getByArrAdId($arrAid, $cushion=0) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('ad_id', $arrAid);
        $criteria1->addColumnCondition(array('cushion' => $cushion));//广告播放器类型：0 一般广告、1 缓冲广告、2 暂停广告、3 片尾广告
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach($data as $val) {
                $return[$val->ad_id] = $val;
            }
        }
        return $return;
    }
    
    /**
     * ���ѡ������ģʽ �����Ƿ����ֲ����
     */
    public function materialIsRotate($rotateMode=1) {
        return ($rotateMode==3)? 1 : 0;
    }
}