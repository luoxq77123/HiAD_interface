<?php

class SitePositionTable extends CWidget {

    public function run() {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->order = 'createtime desc';
        $criteria->select = 'Position.id as id';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        
        $criteria->addColumnCondition(array('ad_type_id' => 1));
        // 附加搜索条件
        if (isset($_GET['siteGroupId']) && $_GET['siteGroupId'] != '') {
            $sites = Site::model()->findAllByAttributes(array('site_group_id' => $_GET['siteGroupId']), 'status = 1');
            $site_ids = $sites ? CHtml::listData($sites, 'id', 'id') : array();
            $criteria->addInCondition('site_id', $site_ids);
        } else if (isset($_GET['siteId']) && $_GET['siteId'] != '') {
            $criteria->addColumnCondition(array('site_id' => $_GET['siteId']));
        }
        // 分页
        $spList = SitePosition::model()->with('Position')->findAll($criteria);
        $positionid = array();
        foreach ($spList as $one) {
            $positionid[] = $one->Position->id;
        }

        if (isset($_GET['ym']) && intval($_GET['ym']) > 100000 && intval($_GET['ym']) < 999999 && $_GET['ym'] > date('Ym', time())) {
            $ym = $_GET['ym'];
            $month_first_day = strtotime($ym . '01 00:00:00');
        } else {
            $ym = date('Ym', time());
            $month_first_day = strtotime(date('Ymd', time()) . ' 00:00:00');
        }
        $date_num = date('t', $month_first_day);
        $ym1 = date('Ymd', $month_first_day + 31 * 24 * 3600);
        $month_last_day = strtotime($ym1 . ' 23:59:59');

        $st_with = "(start_time < $month_first_day AND end_time > $month_first_day) OR
                        (start_time < $month_last_day AND end_time > $month_last_day) OR
                        (start_time >= $month_first_day AND end_time <= $month_last_day)";

        $criteria = new CDbCriteria();
        //$criteria->addColumnCondition(array('com_id' =>  $user['com_id']));
        $criteria->addInCondition('id', $positionid);
        $criteria->order = 'createtime desc';

        // 附加搜索条件
        /* if (isset($_GET['ad_type']) && $_GET['ad_type']) {
          $criteria->addColumnCondition(array('ad_type_id' => $_GET['ad_type']));
          } */

        // 分页
        $count = Position::model()->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->route = 'sitePosition/table';
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);

        $positions = Position::model()->with(array('AdTime' => array('condition' => $st_with)))->findAll($criteria);
        $positionlist = array();
        foreach ($positions as $one) {
            $positionlist[$one->id] = array(
                'id' => $one->id,
                'name' => $one->name,
                'position_size' => $one->position_size,
                'ad_day' => array(),
                'half_day' => array(),
                'time' => array()
            );
            $time = array();
            foreach ($one->AdTime as $t) {
                /* $start_date = $t->start_time < $month_first_day ? 1 : date('j', $t->start_time);
                  $end_date = $t->end_time > $month_last_day ? $date_num : date('j', $t->end_time);
                  $offset = $end_date - $start_date + 1;
                  $dates = $offset > 0 ? array_fill($start_date, $offset, 'date') : array();
                  $positionlist[$one->id]['ad_day'] = array_merge($positionlist[$one->id]['ad_day'], array_keys($dates));
                  $positionlist[$one->id]['ad_day'] = array_unique($positionlist[$one->id]['ad_day']);
                  $positionlist[$one->id]['time'][] = array('start_time' =>date('Y-m-d', $t->start_time), 'end_time' => date('Y-m-d', $t->end_time)); */
                $positionlist[$one->id]['time'][] = array('start_time' => date('Y-m-d H:i:s', $t->start_time), 'end_time' => date('Y-m-d H:i:s', $t->end_time));
                if ($t->start_time <= $month_first_day) {
                    $start = $month_first_day;
                } else {
                    $start = $t->start_time;
                    if (date('His', $t->start_time) != '000000')
                        $positionlist[$one->id]['half_day'][] = $t->start_time;
                }

                if ($t->end_time >= $month_last_day) {
                    $end = $month_last_day;
                } else {
                    $end = $t->end_time;
                    if (date('His', $t->end_time) != '235959')
                        $positionlist[$one->id]['half_day'][] = $t->end_time;
                }
                $time[] = array('start_time' => $start, 'end_time' => $end);
                while ($start <= $end) {
                    $positionlist[$one->id]['ad_day'][] = date('Ymd', $start);
                    $start = strtotime('+1 day', $start);
                }
                //$positionlist[$one->id]['ad_day'] = array_unique($positionlist[$one->id]['ad_day']);
                $time = array_unique($time);
                foreach ($time as $t) {
                    foreach ($positionlist[$one->id]['half_day'] as $o) {
                        if ($o > $t['start_time'] && $o < $t['end_time'] && $o != $t['start_time'] && $o != $t['end_time'])
                            unset($positionlist[$one->id]['half_day'][array_search($o, $positionlist[$one->id]['half_day'])]);
                    }
                }
            }
        }

        $set = array(
            'positionlist' => $positionlist,
            'pages' => $pager,
            'first_day' => $month_first_day,
            'last_day' => $month_last_day,
            'ym' => $ym,
            'week_name' => array(1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '日')
        );
        $this->render('sitePositionTable', $set);
    }

}