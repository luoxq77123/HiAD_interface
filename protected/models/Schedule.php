<?php

class Schedule extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{schedule}}';
    }

    public function relations() {
        return array(
            'ScheduleTime' => array(self::HAS_MANY, 'ScheduleTime', 'schedule_id'),
            'ClientCompany' => array(self::BELONGS_TO, 'ClientCompany', 'client_company_id')
        );
    }

    public function rules() {
        return array(
            array('name,client_company_id,position_id', 'required', 'message' => '{attribute}不能为空', 'on' => 'add,edit'),
            array('description,multi_time,client_company_id,salesman_id,other_contact_id,price', 'safe', 'on' => 'add,edit')
        );
    }

    public function attributeLabels() {
        return array(
            'name' => '排期名称:',
            'description' => '说明:',
            'client_company_id' => '广告客户:',
            'salesman_id' => '销售人员:',
            'other_contact_id' => '其他联系人:',
            'price' => '价格:'
        );
    }

    public function getScheduleByMonth($com_id, $month) {
        $cache_name = md5('model_Schedule_getScheduleByMonth' . $com_id . '_' . $month);
        $schedule = Yii::app()->memcache->get($cache_name);
        if (!$schedule) {
            $month_first_day = strtotime($month . '01 00:00:00');
            $date_num = date('t', $month_first_day);
            $month_last_day = strtotime($month . $date_num . ' 23:59:59');

            $st_with = "(start_time < $month_first_day AND end_time > $month_first_day) OR
                        (start_time < $month_last_day AND end_time > $month_last_day) OR
                        (start_time >= $month_first_day AND end_time <= $month_last_day)";

            $schedule_data = $this->with(array('ScheduleTime' => array('condition' => $st_with)))->findAll("com_id = $com_id");

            $position_ids = array();
            $list = array();
            foreach ($schedule_data as $s) {
                $position_ids[] = $s->position_id;
                $list[$s->position_id][$s->id] = array('id' => $s->id,
                    'name' => $s->name,
                    'client_company_id' => $s->client_company_id,
                    'position_id' => $s->position_id,
                    'schedule_day' => array());
                foreach ($s->ScheduleTime as $t) {
                    $start_date = $t->start_time < $month_first_day ? 1 : date('j', $t->start_time);
                    $end_date = $t->end_time > $month_last_day ? $date_num : date('j', $t->end_time);
                    $offset = $end_date - $start_date + 1;
                    $dates = $offset > 0 ? array_fill($start_date, $offset, 'date') : array();
                    $list[$s->position_id][$s->id]['schedule_day'] = array_merge($list[$s->position_id][$s->id]['schedule_day'], array_keys($dates));
                    $list[$s->position_id][$s->id]['schedule_day'] = array_unique($list[$s->position_id][$s->id]['schedule_day']);
                    $list[$s->position_id][$s->id]['time'][] = array('start_time' => $t->start_time, 'end_time' => $t->end_time);
                }
            }
            $schedule = array('list' => $list, 'position_ids' => $position_ids);
            Yii::app()->memcache->set($cache_name, $schedule, 300);
        }
        return $schedule;
    }
	
	public function getOneById($scheduleid){
		$data = $this->find('id=:id', array(':id'=>$scheduleid));
		return $data;
	}

}