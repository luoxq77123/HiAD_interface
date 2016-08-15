<?php

/**
 * Himi广告控制器
 */
class HimiController extends CController {

    /**
     * 获取广告位信息
     */
    public function actionIndex() {
        if (isset($_GET['Pid'])) {
            $id_arr = explode(',', $_GET['Pid']);
            foreach ($id_arr as $k => $one) {
                /*if (!eregi("^[0-9]+$", $one)) {
                    unset($id_arr[$k]);
                }*/
                if (!preg_match("/^[0-9]+$/i", $one)) {
                    unset($id_arr[$k]);
                }
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $id_arr);
            $Position = Position::model()->findAll($criteria);
            $criteria1 = new CDbCriteria();
            $criteria1->addInCondition('position_id', $id_arr);
            $sitePositions = SitePosition::model()->findAll($criteria1);
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
            $client_info['system'] = $agent->platform();
            $client_info['access_url'] = isset($_GET['access_url']) ? $_GET['access_url'] : '';
            $client_info['referer'] = isset($_GET['referer']) ? $_GET['referer'] : '';
            $client_info['width'] = isset($_GET['sWidth']) ? intval($_GET['sWidth']) : 0;
            $client_info['height'] = isset($_GET['sHeight']) ? intval($_GET['sHeight']) : 0;
            $client_info['is_flash'] = isset($_GET['isFlash']) ? intval($_GET['isFlash']) : 0;
            if ($Position && $sitePositions) {
                $json_str = array();
                $sitePosition_arr = array();
                foreach ($sitePositions as $one) {
                    $sitePosition_arr[$one->position_id] = $one;
                }
                foreach ($Position as $Pos) {
                    $sitePosition = $Pos;
                    $size = explode('*', $Pos->position_size);
                    $json_str['type'] = $Pos->ad_show_id;
                    $json_str['pId'] = $Pos->id;
                    $json_str['_w'] = isset($size[0]) ? $size[0] . 'px' : '';
                    $json_str['_h'] = isset($size[1]) ? $size[1] . 'px' : '';
                    if (isset($sitePosition_arr[$Pos->id])) {
                        $sitePosition = $sitePosition_arr[$Pos->id];
                        $json_str['stayTime'] = $sitePosition->staytime[0] ? $sitePosition->staytime[0] : '';
                        if ($sitePosition->float_x[0] == 1) {
                            $json_str['_l'] = $sitePosition->space_x . 'px';
                        } else if ($sitePosition->float_x[0] == 2) {
                            $json_str['_r'] = $sitePosition->space_x . 'px';
                        }
                        if ($sitePosition->float_y[0] == 1) {
                            $json_str['_t'] = $sitePosition->space_y . 'px';
                        } else if ($sitePosition->float_y[0] == 2) {
                            $json_str['_b'] = $sitePosition->space_y . 'px';
                        }
                        if ($sitePosition->scroll[0] == 1) {
                            $json_str['scroll'] = 1;
                        }
                    }
                    //根据广告位ID获取广告物料信息
                    if (isset(Yii::App()->sphinxSearch)) {
                        $data = Ad::model()->getAdInfo_sphinx($Pos->id);
                    } else {                     
                        $data = Ad::model()->getAdInfo_sql($Pos->id, $client_info); //根据广告位获取广告
                    }
                    //插入数据
                    if (isset($data['adinfo']['ad_id'])) {
                        $statData = SiteStatistics::model()->addStatDetailForSite($data['adinfo']);
                        $data['urlInfo']['id'] = $statData['sid'];
                        $data['urlInfo']['time'] = $data['adinfo']['create_time'];
                        $Material = $this->getDateByAdMaterial($data);
                        if (isset($Material['material_ids']) && $Material['material_ids']!=""){
                            $data['adinfo']['material_ids'] = $Material['material_ids'];
                            SiteStatistics::model()->addMaterialStatForSite($data['adinfo'], $statData);
                            unset($data);
                        }
                        if (isset($Material['material_ids']))
                            unset($Material['material_ids']);
                        $json_str = array_merge($json_str, $Material);
                    }
                    //var_dump($json_str);exit;
                    echo 'SET_HIMI_POSITION(' . json_encode($json_str) . ');' . "\r\n";
                }
            }
        }
        exit;
    }

    /**
     *  地址跳转
     */
    function actionGotoURL() {
        $url = 'http://www.baidu.com';
        if (isset($_GET['data'])) {
            $P = new PassportComponent;
            $data = $P->passport_decrypt($_GET['data'], 'HIMIAD');
            if ($data) {
                $urlinfo = json_decode($data);
                $url = isset($urlinfo->url) ? $urlinfo->url : $url;
                if (isset($urlinfo->time) && isset($urlinfo->id)) {
                    $db_name = date('Ymd', $urlinfo->time);
                    $model = SiteStat::model($db_name)->findByPk($urlinfo->id);
                    if ($model) {
                        $orderId = $model->order_id;
                        $costMode = $model->cost_mode;
                        $orderCost = $model->cost;
                        $model->is_click = 1;
                        $model->click_time = time();
                        $model->save();
                        // 更新物料映射表
                        $table = 'hm_sitematerial_'.$db_name;
                        $sql = "update $table set is_click=1, click_count=click_count+1, click_time=".time()." where stat_id=".$urlinfo->id." and create_time=".$urlinfo->time;
                        Yii::app()->db_stat_sitemate->createCommand($sql)->execute();
                        // 将数据库选择到主库
                        CActiveRecord::$db = Yii::app()->db;
                        // 更新订单花费
                        if ($orderId>0 && $costMode==3 && $orderCost>0) {
                            Orders::model()->updateCostById($orderId, $orderCost);
                        }
                    } else {
                        // 将数据库选择到主库
                        CActiveRecord::$db = Yii::app()->db;
                    }
                }
            }
        }
        $this->redirect($url);
    }

    /**
     * 更具广告物料组合数据
     */
    private function getDateByAdMaterial($data = null) {
        if ($data) {
            $json_str['mrotate_mode'] = $data['mrotate_mode'];
            $json_str['mrotate_time'] = $data['mrotate_time'];
            $mrotate = array();
            $Setting = Setting::model()->getSettings(); //图片地址路径            
            if ($data['mrotate_mode'] == 1 || $data['mrotate_mode'] == 2) {
                if ($data['mrotate_mode'] == 2) {
                    $mun = $this->get_rand($data['mrotate']['weights']);
                } else {
                    $data['mrotate']['material'] = isset($data['mrotate']['material']) ? $data['mrotate']['material'] : 0;
                    $len = count($data['mrotate']['material']);
                    if ($len <= 1) {
                        $len = 1;
                    }
                    $mun = rand(0, ($len - 1));
                }
                if (isset($data['mrotate']['material'][$mun])) {
                    $one = $data['mrotate']['material'][$mun];
                    if (isset($one->url)) {
                        $mrotate['url'] = $Setting['STATIC_URL'] . $one->url;
                    }
                    if (isset($one->click_link)) {
                        $P = new PassportComponent;
                        if (isset($data['urlInfo']['id'])) {
                            $data['urlInfo']['url'] = $one->click_link;
                            $data['urlInfo']['material_id'] = $one->material_id;
                            $url_arr = $data['urlInfo'];
                        }
                        $url = json_encode($url_arr);
                        $setting = Setting::model()->getSettings();
                        $mrotate['link'] = $setting['INTERFACE_URL'].'/Himi/gotoURL?data=' . $P->passport_encrypt($url, 'HIMIAD');
                    }
                    if (isset($one->text)) {
                        $mrotate['text'] = $one->text;
                    }
                    $mrotate['material_type_id'] = isset($data['mrotate']['material_type_id'][$mun]) ? $data['mrotate']['material_type_id'][$mun] : '';
                    if ($mrotate['material_type_id'] == 1) {
                        $mrotate['style'] = $this->setText($one); //设置字体
                    } else if ($mrotate['material_type_id'] == 4) {
                        $mrotate['content'] = stripslashes($one->content); //设置富媒体内容
                        $mrotate['template_mode'] = $one->template_mode;
                    }
                    $json_str['mrotate'][] = $mrotate;
                    $json_str['material_ids'] = $one->material_id;
                } else {
                    $json_str['mrotate'] = '';
                    $json_str['material_ids'] = '';
                }
            } else {
                if ($data['mrotate']['material']) {
                    foreach ($data['mrotate']['material'] as $k => $one) {
                        if (isset($one->url)) {
                            $mrotate['url'] = $Setting['STATIC_URL'] . $one->url;
                        }
                        if (isset($one->click_link)) {
                            $mrotate['link'] = $one->click_link;
                        }
                        if (isset($one->text)) {
                            $mrotate['text'] = $one->text;
                        }
                        $mrotate['material_type_id'] = isset($data['mrotate']['material_type_id'][$k]) ? $data['mrotate']['material_type_id'][$k] : '';
                        if ($mrotate['material_type_id'] == 1) {
                            $mrotate['style'] = $this->setText($one); //设置字体
                        } else if ($mrotate['material_type_id'] == 4) {
                            $mrotate['content'] = stripslashes($one->content); //设置富媒体内容
                            $mrotate['template_mode'] = $one->template_mode;
                        }
                        $json_str['mrotate'][] = $mrotate;
                    }
                    $json_str['material_ids'] = $data['material_ids'];
                }
            }
        } else {
            $json_str = array();
        }
        return $json_str;
    }

    /**
     * 设置字体样式
     */
    private function setText($one = null) {
        $style = '';
        $float_style = '';
        $_style = '';
        if (isset($one->size)) {
            $style .= 'font-size:' . $one->size . 'px;';
        }
        if (isset($one->color)) {
            $style .= 'color:' . $one->color . ';';
            $_style .='this.style.color = \'' . $one->color . '\';';
        }
        if (isset($one->style)) {
            if (strstr($one->style, "-1") !== false) {
                $style .= 'text-decoration: underline;';
                $_style .= 'this.style.textDecoration = \'underline\';';
            } else {
                $style .= 'text-decoration: none;';
                $_style .= 'this.style.textDecoration = \'none\';';
            }
            if (strstr($one->style, "-2") !== false) {
                $style .= 'font-weight:bold;';
                $_style .= 'this.style.fontWeight = \'bold\';';
            } else {
                $style .= 'font-weight:normal;';
                $_style .= 'this.style.fontWeight = \'normal\';';
            }
            if (strstr($one->style, "-3") !== false) {
                $style .= 'font-style: italic;';
                $_style .= 'this.style.fontStyle = \'italic\';';
            } else {
                $style .= 'font-style: normal;';
                $_style .= 'this.style.fontStyle = \'normal\';';
            }
        }
        if (isset($one->float_color)) {
            $float_style .='this.style.color = \'' . $one->float_color . '\';';
        }
        if (isset($one->float_style)) {
            if (strstr($one->float_style, "-1") !== false) {
                $float_style .= 'this.style.textDecoration = \'underline\';';
            }
            if (strstr($one->float_style, "-2") !== false) {
                $float_style .= 'this.style.fontWeight=\'bold\';';
            }
            if (strstr($one->float_style, "-3") !== false) {
                $float_style .= 'this.style.fontStyle = \'italic\';';
            }
        }

        return 'style="' . $style . '" onmouseout="' . $_style . '"  onmouseover="' . $float_style . '"';
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

}