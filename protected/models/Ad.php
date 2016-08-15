<?php

class Ad extends CActiveRecord {
    //保存站点客户端信息对应的id
    private $_arrSiteClient;
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
            array('name', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
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

    public function getAdTypeName($posTypeId, $typeId) {
        $type = AdType::model()->getPositionAdTypes($posTypeId);
        return $type[$typeId];
    }

    public function getArrId($positionId, $adTypeId = 1) {
        $criteria1 = new CDbCriteria();
        $criteria1->select = 'id';
        $criteria1->addColumnCondition(array('position_id' => $positionId));
        $criteria1->addColumnCondition(array('ad_type_id' => $adTypeId));
        $criteria1->addColumnCondition(array('status' => 1));
        $data = $this->findAll($criteria1);
        $return = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $return[] = $val->id;
            }
        }
        return $return;
    }

    // 获取一条信息
    public function getOneById($id) {
        $data = $this->find('id=:id', array(':id' => $id));
        return $data;
    }

    public function cleanAdSession() {
        if (isset(Yii::app()->session['create_ad_info'])) {
            unset(Yii::app()->session['create_ad_info']);
        }
    }

    public function getAdSession() {
        $data = array();
        if (isset(Yii::app()->session['create_ad_info'])) {
            $data = Yii::app()->session['create_ad_info'];
        }
        return $data;
    }

    public function filterSQLData($ad_arr, $client_info) {
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('ad_id', $ad_arr);
        $SiteAd = SiteAd::model()->findAll($criteria1);
        $w = date('wH', time());
        $Network = new NetworkComponent; 
        $ip = ip2long($Network->getIP());
        $data = AreaIp::model()->getDataByIp($ip);
        $ipCode = isset($data->code) ? $data->code : 0;
        $connect = isset($data->connect) ? $data->connect : 0;
        $data = SiteAdConnect::model()->getDataByName($connect);
        $connect_id = isset($data->id) ? $data->id : 0;
        $browser = $client_info['browser'];
        $data = SiteAdBrowser::model()->getDataByName($browser);
        $browser_id = isset($data->id) ? $data->id : 0;
        $language = $client_info['language'];
        $data = SiteAdLanguage::model()->getDataByName($language);
        $language_id = isset($data->id) ? $data->id : 0;
        $accessurl = $client_info['access_url'];
        $data = SiteAdUrl::model()->getDataByUrl($accessurl);
        $accessurl_id = isset($data->id) ? $data->id : 0;
        $formurl = $client_info['referer'];
        $data = SiteAdUrl::model()->getDataByUrl($formurl);
        $formurl_id = isset($data->id) ? $data->id : 0;
        $name = $client_info['width'] . '*' . $client_info['height'];
        $data = SiteAdResolution::model()->getDataByName($name);
        $resolution_id = isset($data->id) ? $data->id : 0;
        $system_name = $client_info['system'];
        $data = SiteAdSystem::model()->getDataByName($system_name);
        $system_id = isset($data->id) ? $data->id : 0;
        $un_arr = array();
        foreach ($SiteAd as $one) {
            if ($one->directional_info) {
                $directional = unserialize($one->directional_info);
                if (isset($directional['time_set'])) {//排期
                    if ($directional['time_set'] != 0 && $directional['time_set'] != $w) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['area_set'])) {//地区
                    if ($directional['area_set'] != 0 && $directional['area_set'] != $ipCode) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['connect_set'])) {//接入点
                    if ($directional['connect_set'] != 0 && $directional['connect_set'] != $connect_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['btype_set'])) {//浏览器
                    if ($directional['btype_set'] != 0 && $directional['btype_set'] != $browser_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['blanguage_set'])) {//浏览器语言
                    if ($directional['blanguage_set'] != 0 && $directional['blanguage_set'] != $language_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['accessurl_set'])) {//使用者url
                    if ($directional['accessurl_set'] != 0 && $directional['accessurl_set'] != $accessurl_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['formurl_set'])) {//来源Url
                    if ($directional['formurl_set'] != 0 && $directional['formurl_set'] != $formurl_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['resolution_set'])) {//分辨率 显示尺寸Url
                    if ($directional['resolution_set'] != 0 && $directional['resolution_set'] != $resolution_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
                if (isset($directional['osystem_set'])) {//系统
                    if ($directional['osystem_set'] != 0 && $directional['osystem_set'] != $system_id) {
                        $un_arr[] = $one->ad_id;
                    }
                }
            }
        }
        // 保存客户端信息方便统计
        $this->_arrSiteClient['ip'] = $ip;
        $this->_arrSiteClient['region_id'] = $ipCode;
        $this->_arrSiteClient['connect_id'] = $connect_id;
        $this->_arrSiteClient['browser_id'] = $browser_id;
        $this->_arrSiteClient['language_id'] = $language_id;
        $this->_arrSiteClient['system_id'] = $system_id;
        $this->_arrSiteClient['resolution_id'] = $resolution_id;
        $this->_arrSiteClient['referer_id'] = $formurl_id;
        $this->_arrSiteClient['accessurl_id'] = $accessurl_id;

        // 优化广告筛选并返回筛选后的广告信息 以减少数据重复查询
        $arr_adid = array_diff($ad_arr, $un_arr);
        if (empty($arr_adid))
            return false;
        $arrAppAd = array();
        foreach($SiteAd as $one) {
            if (in_array($one->ad_id, $arr_adid)) {
                $arrAppAd[$one->ad_id] = $one;
            }
        }
        unset($SiteAd);
        return $this->getOneAdByPolicy($arrAppAd);
    }
    
    /**
     * 获取展现的广告信息，根据广告优先级，权重等比较返回
     */
    public function getOneAdByPolicy($arrAppAd){
        $data = false;
        // 根据广告 独立访客展现量限制、投放次数限制 筛选广告
        $filterAid = array();
        $today = date("Y-m-d 00:00:00", time());
        $startTime = $this->mstrToTime($today);
        foreach($arrAppAd as $id => $val) {
            // 对独立访客展现量限制 统计数据库需添加自段, 独立访客项目暂时不加限制
            if (!empty($val['limit_one_show'])) {}
            // 总投放量控制
            if ($val['costing_mode'] == 2 && $val['cost_num']>0) { //每千次展现费用
                $showStat = $this->_getShowStatistics($id, $startTime);
                // 统计今天以前的展现量
                $oldShow = StatisticsAd::model()->getShowNumByAid($id);
                $total = $showStat + $oldShow;
                if ($total >= $val['cost_num']) {
                    $filterAid[] = $id;
                    continue;
                }
            } else if ($val['costing_mode'] == 3 && $val['cost_num']>0) { //每次点击费用
                $clickStat = $this->_getClickStatistics($id, $startTime);
                // 统计今天以前的点击量
                $oldClick = StatisticsAd::model()->getClickNumByAid($id);
                $total = $clickStat + $oldClick;
                if ($total >= $val['cost_num']) {
                    $filterAid[] = $id;
                    continue;
                }
            }
            if ($val['limit_day_show_mode'] == 1) { //每日展现量限制
                $showStat = $this->_getShowStatistics($id, $startTime);
                if ($showStat >= $val['limit_day_show_num']) {
                    $filterAid[] = $id;
                    continue;
                }
            } else if ($val['limit_day_show_mode'] == 2) { //每日点击量限制
                $clickStat = $this->_getClickStatistics($id, $startTime);
                if ($clickStat >= $val['limit_day_show_num']) {
                    $filterAid[] = $id;
                    continue;
                }
            }
        }
        $arrAid = array_diff(array_keys($arrAppAd), $filterAid);
        if (count($arrAid)==0)
            return false;
        // 根据广告 优先级、权重 筛选广告
        if (count($arrAid)==1) {
            foreach($arrAppAd as $one) {
                if ($one->ad_id == reset($arrAid)) {
                    $data = $one;
                    break;
                }
            }
        } else { //广告位有多个广告
            $newSiteAd = array();
            $priority = array();
            foreach($arrAppAd as $one) {
                foreach($arrAid as $aid) {
                    if ($one->ad_id == $aid) {
                        $newSiteAd[$aid] = $one;
                        $priority[$one->priority_mode][$aid] = $one->priority;
                        break;
                    }
                }
            }
            unset($arrAppAd);
            // 独占
            if (isset($priority[1])) {
                if (count($priority[1])==1) {
                    $data = $newSiteAd[reset(array_keys($priority[1]))];
                } else {
                    $reAid = $this->get_rand($priority[1]);
                    $data = $newSiteAd[$reAid];
                }
            } else if (isset($priority[2])) { // 标准
                if (count($priority[2])==1) {
                    $data = $newSiteAd[reset(array_keys($priority[2]))];
                } else {
                    $reAid = $this->get_rand($priority[2]);
                    $data = $newSiteAd[$reAid];
                }
            } else if (isset($priority[3])) { // 补余
                if (count($priority[3])==1) {
                    $data = $newSiteAd[reset(array_keys($priority[3]))];
                } else {
                    $reAid = $this->get_rand($priority[3]);
                    $data = $newSiteAd[$reAid];
                }
            } else if (isset($priority[4])) { // 底层
                if (count($priority[4])==1) {
                    $data = $newSiteAd[reset(array_keys($priority[4]))];
                } else {
                    $reAid = $this->get_rand($priority[4]);
                    $data = $newSiteAd[$reAid];
                }
            }
        }
        return $data;
    }

    public function filterSphinxData($Ad_info, $ad_arr, $client_info) {

        $w = date('wH', time());
        /* $Network = new NetworkComponent;         
          $ip =$Network->getIP(); */
        $ip = 16843265;
        //  Yii::App()->sphinxSearch->conn();
        $data = Yii::App()->sphinxSearch->query('select id,start_ip,end_ip,code,connect,address,local from area_ip where start_ip <=  ' . $ip . ' and end_ip >= ' . $ip);
        $data = isset($data[0]) ? $data[0] : array();
        $ipCode = isset($data['code']) ? $data['code'] : 0;
        $connect = isset($data['connect']) ? $data['connect'] : 0;
        $data = SiteAdConnect::model()->getDataByName($connect);
        $connect_id = isset($data->id) ? $data->id : 0;
        $browser = $client_info['browser'];
        $data = SiteAdBrowser::model()->getDataByName($browser);
        $browser_id = isset($data->id) ? $data->id : 0;
        $language = $client_info['language'];
        $data = SiteAdLanguage::model()->getDataByName($language);
        $language_id = isset($data->id) ? $data->id : 0;
        $accessurl = $client_info['access_url'];
        $data = SiteAdUrl::model()->getDataByUrl($accessurl);
        $accessurl_id = isset($data->id) ? $data->id : 0;
        $formurl = $client_info['referer'];
        $data = SiteAdUrl::model()->getDataByUrl($formurl);
        $formurl_id = isset($data->id) ? $data->id : 0;
        $name = $client_info['width'] . '*' . $client_info['height'];
        $data = SiteAdResolution::model()->getDataByName($name);
        $resolution_id = isset($data->id) ? $data->id : 0;
        $system_name = $client_info['system'];
        $data = SiteAdSystem::model()->getDataByName($system_name);
        $system_id = isset($data->id) ? $data->id : 0;
        $un_arr = array();
        foreach ($Ad_info as $one) {
            if (isset($one['timing'])) {//排期
                if ($one['timing'] != 0 && $one['timing'] != $w) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['region_id'])) {//地区
                if ($one['region_id'] != 0 && $one['region_id'] != $ipCode) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['connect_id'])) {//接入点
                if ($one['connect_id'] != 0 && $one['connect_id'] != $connect_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['browser_id'])) {//浏览器
                if ($one['browser_id'] != 0 && $one['browser_id'] != $browser_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($directional['blanguage_set'])) {//浏览器语言
                if ($directional['blanguage_set'] != 0 && $directional['blanguage_set'] != $language_id) {
                    $un_arr[] = $one->ad_id;
                }
            }
            if (isset($one['url_id'])) {//使用者url
                if ($one['url_id'] != 0 && $one['url_id'] != $accessurl_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['referer_id'])) {//来源Url
                if ($one['referer_id'] != 0 && $one['referer_id'] != $formurl_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['resolution_id'])) {//分辨率 显示尺寸Url
                if ($one['resolution_id'] != 0 && $one['resolution_id'] != $resolution_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
            if (isset($one['system_id'])) {//系统
                if ($one['system_id'] != 0 && $one['system_id'] != $system_id) {
                    $un_arr[] = $one['ad_id'];
                }
            }
        }
        // 保存客户端信息方便统计
        $this->_arrSiteClient['ip'] = $ip;
        $this->_arrSiteClient['region_id'] = $ipCode;
        $this->_arrSiteClient['connect_id'] = $connect_id;
        $this->_arrSiteClient['browser_id'] = $browser_id;
        $this->_arrSiteClient['language_id'] = $language_id;
        $this->_arrSiteClient['system_id'] = $system_id;
        $this->_arrSiteClient['resolution_id'] = $resolution_id;
        $this->_arrSiteClient['referer_id'] = $formurl_id;
        $this->_arrSiteClient['accessurl_id'] = $accessurl_id;

        return array_diff($ad_arr, $un_arr);
    }

    /**
     * 获取广告信息 包括物料
     */
    public function getAdInfo_sql($p_id = 0, $client_info = null) {
        $data = array();
        $arrAid = AdTime::model()->getAdIdByCondition($p_id, time());
        if (!$arrAid) {
            return array();
        }
        $criteria1 = new CDbCriteria();
        $criteria1->addInCondition('id', $arrAid);
        $criteria1->addColumnCondition(array('position_id' => $p_id));
        $criteria1->addColumnCondition(array('ad_type_id' => 1));
        $criteria1->addColumnCondition(array('status' => 1));
        $Ads = $this->findAll($criteria1);
        if (!$Ads) {
            return array();
        }
        $ad_arr = array();
        //去掉重复广告
        foreach ($Ads as $one) {
            if (!in_array($one->id, $ad_arr)) {
                $ad_arr[] = $one->id;
            }
        }
        $Addata = $this->filterSQLData($ad_arr, $client_info);
        if (!$Addata) {
            return array();
        }
        $json = $Addata->material;
        $adInfo = $json ? unserialize($json) : '';
        $mrotate_id = array();
        $mrotate = array();
        if ($adInfo) {
            foreach ($adInfo as $one) {
                $mrotate[$one['id']] = $one;
                $mrotate_id[] = $one['id'];
            }
        }
        //获取物料数据
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $mrotate_id);
        $materials = Material::model()->findAll($criteria);
        $materialInfo = array();
        $mrotate_ids = array();
        foreach ($materials as $one) {
            if ($one->material_type_id == 1) {
                $material = MaterialText::model()->find(array('condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else if ($one->material_type_id == 2) {
                $material = MaterialPic::model()->find(array('select' => 'material_id,url,click_link', 'condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else if ($one->material_type_id == 3) {
                $material = MaterialFlash::model()->find(array('select' => 'material_id,url,click_link', 'condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else if ($one->material_type_id == 4) {
                $material = MaterialMedia::model()->find(array('select' => 'material_id,template_mode,content', 'condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else {
                $material = null;
            }
            if ($material) {
                $weights = isset($mrotate[$one->id]['weights']) ? $mrotate[$one->id]['weights'] : 0;
                $materialInfo['material'][] = $material;
                $materialInfo['weights'][] = $weights;
                $materialInfo['material_type_id'][] = $one->material_type_id;
                $mrotate_ids[] = $one->id;
            }
        }

        $data['mrotate'] = $materialInfo;
        $data['mrotate_mode'] = $Addata->mrotate_mode;
        $data['adinfo'] = array(
            'ad_id' => $Addata->ad_id,
            'position_id' => $p_id,
            'com_id' => $Addata->com_id,
            'cost_mode' => $Addata->costing_mode,
            'price' => $Addata->price,
            'create_time' => time(),
            'info' => "",
            'ip' => $this->_arrSiteClient['ip'],
            'region_id' => $this->_arrSiteClient['region_id'],
            'connect_id' => $this->_arrSiteClient['connect_id'],
            'browser_id' => $this->_arrSiteClient['browser_id'],
            'language_id' => $this->_arrSiteClient['language_id'],
            'system_id' => $this->_arrSiteClient['system_id'],
            'resolution_id' => $this->_arrSiteClient['resolution_id'],
            'referer_id' => $this->_arrSiteClient['referer_id'],
            'accessurl_id' => $this->_arrSiteClient['accessurl_id'],
            'material_ids' => implode(",", $mrotate_ids)
        );
        $data['mrotate_time'] = $Addata->mrotate_time;
        return $data;
    }

    function getAdInfo_sphinx($p_id = 0, $client_info = null) {

        $data = array();
        $w = date('wH', time());
        Yii::App()->sphinxSearch->conn();
        $Ads = Yii::App()->sphinxSearch->query('select id,name,material,mrotate_mode,mrotate_time,position_id,start_time,end_time,url_id,browser_id,connect_id,language_id,referer_id,region_id,resolution_id,system_id,timing from ad_main where status = 1 and position_id = ' . intval($p_id) . ' and timing in (0,' . $w . ')');
        if (!$Ads) {
            return array();
        }
        $ad_arr = array();
        $new_Ads = array();
        //去掉重复广告     
        foreach ($Ads as $one) {
            if (!in_array($one['id'], $ad_arr)) {
                $new_Ads[$one['id']] = $one;
                $ad_arr[] = $one['id'];
            }
        }
        $ad_arr = $this->filterSphinxData($new_Ads, $ad_arr, $client_info);
        $json = isset($new_Ads[$ad_arr[0]]['material']) ? $new_Ads[$ad_arr[0]]['material'] : null;
        $adInfo = unserialize($json);
        $mrotate_id = array();
        $mrotate = array();
        if ($adInfo) {
            foreach ($adInfo as $one) {
                $mrotate[$one['id']] = $one;
                $mrotate_id[] = $one['id'];
            }
        }
        //获取物料数据
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $mrotate_id);
        $materials = Material::model()->findAll($criteria);
        $materialInfo = array();
        $mrotate_ids = array();
        foreach ($materials as $one) {
            if ($one->material_type_id == 1) {
                $material = MaterialText::model()->find(array('condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else if ($one->material_type_id == 2) {
                $material = MaterialPic::model()->find(array('select' => 'url,click_link', 'condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else if ($one->material_type_id == 3) {
                $material = MaterialFlash::model()->find(array('select' => 'url,click_link', 'condition' => 'material_id=:material_id', 'params' => array(':material_id' => $one->id)));
            } else {
                $material = null;
            }
            if ($material) {
                $weights = isset($mrotate[$one->id]['weights']) ? $mrotate[$one->id]['weights'] : 0;
                $materialInfo['material'][] = $material;
                $materialInfo['weights'][] = $weights;
                $materialInfo['material_type_id'][] = $one->material_type_id;
                $mrotate_ids[] = $one->id;
            }
        }
        $data['mrotate'] = $materialInfo;
        $data['mrotate_mode'] = $new_Ads[$ad_arr[0]]['mrotate_mode'];
        $data['mrotate_time'] = $new_Ads[$ad_arr[0]]['mrotate_time'];
        $data['adinfo'] = array('ad_id' => $ad_arr[0], 'mrotate_id' => $mrotate_ids);
        return $data;
    }
    
    /**
     * 获取客户端信息
     */
    public function getClientInfo() {
        $agent = new UserAgent;
        $client_info = array();
        if($agent->browser() == 'Internet Explorer'){
            $version = $agent->version();                
            $client_info['browser'] =$agent->browser().' '.floor($version);
        }else{
            $client_info['browser'] = $agent->browser();
        }
        $client_info['browser_version'] = $agent->version();
        if ($agent->is_mobile()) {
            $client_info['mobile'] = $agent->mobile();
        }
        $client_info['language'] = $agent->language();
        /*$arrReferer = parse_url($_SERVER['HTTP_REFERER']);
        $referer = $arrReferer['host'];
        $refererUrl = explode('.', $arrReferer['host']);
        $countUrl = count($refererUrl);
        if ($countUrl > 2) {
            $referer = $refererUrl[$countUrl-2] . '.' . $refererUrl[$countUrl-1];
        }*/
        $client_info['system'] = $agent->platform();
        $client_info['access_url'] = isset($_GET['access_url']) ? $_GET['access_url'] : '';
        $client_info['referer'] = isset($_GET['referer']) ? $_GET['referer'] : '';
        $client_info['width'] = isset($_GET['sWidth']) ? intval($_GET['sWidth']) : 0;
        $client_info['height'] = isset($_GET['sHeight']) ? intval($_GET['sHeight']) : 0;
        $client_info['is_flash'] = isset($_GET['isFlash']) ? intval($_GET['isFlash']) : 0;
        return $client_info;
    }

    /**
     *  概率随即
     */
    private function get_rand($proArr) {
        $result = '';
        $proSum = array_sum($proArr);
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }

    /**
     * 获取客户端广告展现统计详细信息 根据条件
     */
    private function _getShowStatistics($adId, $startTime) {
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("ad_id" => $adId));
        $criteria->addCondition("create_time>$startTime");
        $table = date("Ymd", time());
        $rData = SiteStat::model($table)->count($criteria);
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        return $rData;
    }

    /**
     * 获取客户端广告展现点击详细信息 根据条件
     */
    private function _getClickStatistics($adId, $startTime) {
        // 根据条件查询统计信息
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array("ad_id" => $adId));
        $criteria->addColumnCondition(array("is_click" => 1));
        $criteria->addCondition("click_time>$startTime");
        $table = date("Ymd", time());
        $rData = SiteStat::model($table)->count($criteria);
        // 将数据库选择到主库
        CActiveRecord::$db = Yii::app()->db;
        return $rData;
    }

    /**
     * 时间字符串转换时间戳
     */ 
    public function mstrToTime($strTime)
    {
        $times = explode(" ", $strTime);
        $date = explode("-", $times[0]);
        $time = explode(":", $times[1]);
        $time[2] = isset($time[2]) ? $time[2] : 0;
        return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }

    //获取用户当前时间下正在投放的广告
    public static function getUserPosition($com_id,$position_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'id,ad_type_id,ads_start_time,ads_end_time';
        $criteria->addColumnCondition(array(
            "com_id" => $com_id,
            "position_id"=> $position_id,
            "status" => 1
        ));
        $time = time();
        $criteria->addCondition("ads_start_time<$time");
        $ret = self::model()->findAll($criteria);
        $arr = array();
        foreach($ret as $val){
            if(!$val['attributes']['ads_end_time']){
                $arr[] = $val['attributes'];
            }else{
                if($time<=$val['attributes']['ads_end_time']){
                    $arr[] = $val['attributes'];
                }
            }
        }
        return $arr;
    }


}