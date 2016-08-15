<?php

class Material extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{material}}';
    }
    
    public function rules() {
        return array(
            array('name,material_type_id', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '物料名称:',
            'material_type_id' => '类型:'
        );
    }

    public function getUsedSize($com_id){
        $cache_name = md5('model_Material_getUsedSize'.$com_id);
        $usedSize = Yii::app()->memcache->get($cache_name);
        if (!$usedSize) {
            $data = $this->findAll(array(
                    'select'=>'material_size',
                    'condition'=>'com_id=:com_id',
                    'order'=>'createtime desc',
                    'params'=>array(':com_id' => $com_id)
                ));
            $usedSize = CHtml::listData($data, 'material_size', 'material_size');
            Yii::app()->memcache->set($cache_name, $usedSize, 300);
        }
        return $usedSize;
    }
    
    public function getInfoByArrId($arrId, $adTypeId=1) {
        $criteria1 = new CDbCriteria();
        $criteria1->select = 'id, material_type_id, material_size';
        $criteria1->addInCondition('id', $arrId);
        $criteria1->addColumnCondition(array('status' => 1));
        $criteria1->addColumnCondition(array('ad_type_id' => $adTypeId));
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            $textTypeIds = array();
            $picTypeIds = array();
            $Setting = Setting::model()->getSettings(); //系统设置
            foreach($data as $val) {
                switch($val->material_type_id) {
                    case 1:
                        $textTypeIds[] = $val->id;
                        break;
                    case 2:
                        $picTypeIds[] = $val->id;
                        break;
                }
            }
            if (!empty($textTypeIds)) {
                $textMaterial = MaterialAtext::model()->getContentByMaterialIds($textTypeIds);
            }
            if (!empty($picTypeIds)) {
                $picMaterial = MaterialApic::model()->getContentByMaterialIds($picTypeIds);
            }
            foreach($data as $val) {
                $return[$val->id]['type'] = $val->material_type_id;
                if ($val->material_type_id==1) {
                    $return[$val->id]['src'] = $textMaterial[$val->id]['text'];
                    $return[$val->id]['link'] = $textMaterial[$val->id]['click_link'];
                    $return[$val->id]['fontColor'] = $textMaterial[$val->id]['color'];
                    $return[$val->id]['fontSize'] = 12;
                } else if ($val->material_type_id==2) {
                    $return[$val->id]['src'] = $Setting['STATIC_URL'].$picMaterial[$val->id]['url'];
                    $return[$val->id]['link'] = $picMaterial[$val->id]['click_link'];
                }
            }
        }
        return $return;
    }

    /**
     * @adtype 广告类型 默认1：站点广告、2：客户端广告、3：视频广告
     */
    public function getMaterialInfo($arrId, $adtype = 1) {
        $criteria1 = new CDbCriteria();
        $criteria1->select = 'id, material_type_id, material_size';
        $criteria1->addInCondition('id', $arrId);
        $criteria1->addColumnCondition(array('status' => 1));
        $criteria1->addColumnCondition(array('ad_type_id' => $adtype));
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            $textTypeIds = array();
            $picTypeIds = array();
            $flashTypeIds = array();
            $videoTypeIds = array();
            $Setting = Setting::model()->getSettings(); //系统设置
            foreach($data as $val) {
                switch($val->material_type_id) {
                    case 1:
                        $textTypeIds[] = $val->id;
                        break;
                    case 2:
                        $picTypeIds[] = $val->id;
                        break;
                    case 3:
                        $flashTypeIds[] = $val->id;
                        break;
                    case 5:
                        $videoTypeIds[] = $val->id;
                        break;
                }
            }
            switch ($adtype) {
            case 1:
                if (!empty($textTypeIds)) {
                    $textMaterial = MaterialText::model()->getInfoByMaterialIds($textTypeIds);
                }
                if (!empty($picTypeIds)) {
                    $picMaterial = MaterialPic::model()->getInfoByMaterialIds($picTypeIds);
                }
                if (!empty($flashTypeIds)) {
                    $flashMaterial = MaterialFlash::model()->getInfoByMaterialIds($flashTypeIds);
                }
                if (!empty($videoTypeIds)) {
                    $videoMaterial = MaterialVideo::model()->getInfoByMaterialIds($videoTypeIds);
                }
                break;
            case 3:
                if (!empty($textTypeIds)) {
                    $textMaterial = MaterialVtext::model()->getInfoByMaterialIds($textTypeIds);
                }
                if (!empty($picTypeIds)) {
                    $picMaterial = MaterialVpic::model()->getInfoByMaterialIds($picTypeIds);
                }
                if (!empty($flashTypeIds)) {
                    $flashMaterial = MaterialVflash::model()->getInfoByMaterialIds($flashTypeIds);
                }
                if (!empty($videoTypeIds)) {
                    $videoMaterial = MaterialVvideo::model()->getInfoByMaterialIds($videoTypeIds);
                }
                break;
            }
            $playerAdType = $this->playerAdType();
            foreach($data as $val) {
                $return[$val->id]['type'] = $val->material_type_id;
                if ($val->material_type_id==1) {
                    $return[$val->id]['src'] = $textMaterial[$val->id]['text'];
                    $return[$val->id]['href'] = $textMaterial[$val->id]['click_link'];
                    $return[$val->id]['adtype'] = $playerAdType['text'];
                } else if ($val->material_type_id==2) {
                    $return[$val->id]['src'] = $Setting['STATIC_URL'].$picMaterial[$val->id]['url'];
                    $return[$val->id]['href'] = $picMaterial[$val->id]['click_link'];
                    $return[$val->id]['adtype'] = $playerAdType['picture'];
                } else if ($val->material_type_id==3) {
                    $return[$val->id]['src'] = $Setting['STATIC_URL'].$flashMaterial[$val->id]['url'];
                    $return[$val->id]['href'] = $flashMaterial[$val->id]['click_link'];
                    $return[$val->id]['adtype'] = $playerAdType['media'];
                } else if ($val->material_type_id==5) {
                    $return[$val->id]['src'] = $Setting['STATIC_URL'].$videoMaterial[$val->id]['url'];
                    $return[$val->id]['href'] = $videoMaterial[$val->id]['click_link'];
                    $return[$val->id]['adtype'] = $playerAdType['video'];
                }
                $return[$val->id]['duration'] = '20';
            }
        }
        return $return;
    }
    
    // get ad material rotate mode
    public function getRotate(){
        $list = array(
            '1' => '均匀',
            '2' => '手动权重',
            '3' => '幻灯片轮换'
        );
        return $list;
    }
    
    /**
     * 索贝播放器接口 广告类型参数
     * 1.Picture,2.Video,3.Text,4.Codes,js codes,5.Rich media
     */
    public function playerAdType() {
        return array(
            'picture' => '1',
            'video' => '2',
            'text' => '3',
            'codes' => '4',
            'media' => '5'
        );
    }

    public static function getMaterialType($arr)
    {
        if (!$arr) {
            return false;
        }
        $criteria = new CDbCriteria();
        $criteria->select = 'id,material_type_id';
        $criteria->addInCondition('id', $arr);
        $criteria->addColumnCondition(array('status'=> 1));
        $ret = self::model()->findAll($criteria);
        $retArr = array();
        foreach ($ret as $val) {
            $attr = $val['attributes'];
            $retArr[$attr['material_type_id']][] = $attr['id'];
        }
        return $retArr;
    }


}