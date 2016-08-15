<?php
class StatisticsClient extends CActiveRecord {
    public $id;
    public $name;
    public $ctr;
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{statistics_client}}';
    }

    /**
     * get all statistics data.
     * used statistics total data and curve ploting
     */
    public function getAll($arrId, $adType=1, $startDate, $endDate) {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->select = 'client_id as id,client_id,show_num,click_num,unique_users,dedicgotd_ip,cpm_cost,cpc_cost,cost,dates';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type' => $adType));
        // append condition of search
        if (!empty($arrId)) {
            $criteria->addInCondition('client_id', $arrId);
        }
        if ($startDate != "" && $endDate != "") {
            $criteria->addBetweenCondition('dates', $startDate, $endDate);
        } else if ($startDate != "" && $endDate = "") {
            $criteria->addBetweenCondition('dates', $startDate, date("Y-m-d", time()));
        }
        $list = $this->findAll($criteria);
        return $list;
    }

    /**
     * get statistcis list of details data. 
     * used show statistics list data 
     */
    public function getPageList($arrId, $adType=1, $startDate, $endDate, $route=null) {
        $user = Yii::app()->session['user'];
        $criteria = new CDbCriteria();
        $criteria->select = 'client_id as id,client_id,sum(show_num) as show_num,sum(click_num) as click_num,sum(unique_users) as unique_users,sum(dedicgotd_ip) as dedicgotd_ip,sum(cpd_cost) as cpd_cost,sum(cpm_cost) as cpm_cost,sum(cpc_cost) as cpc_cost,sum(cost) as cost,dates';
        $criteria->group = 'client_id';
        $criteria->addColumnCondition(array('com_id' => $user['com_id']));
        $criteria->addColumnCondition(array('ad_type' => $adType));
        // append condition of search
        if (!empty($arrId)) {
            $criteria->addInCondition('client_id', $arrId);
        }
        if ($startDate != "" && $endDate != "") {
            $criteria->addBetweenCondition('dates', $startDate, $endDate);
        } else if ($startDate != "" && $endDate = "") {
            $criteria->addBetweenCondition('dates', $startDate, date("Y-m-d", time()));
        }
        // paging
        $count = $this->count($criteria);
        $pageSize = (isset($_GET['pagesize']) && $_GET['pagesize']) ? $_GET['pagesize'] : 10;
        $pager = new CPagination($count);
        $pager->pageSize = $pageSize;
        $pager->applyLimit($criteria);
        if ($route != null) {
            $pager->route = $route;
        }
        $list = $this->findAll($criteria);
        
        $data = array();
        $data['list'] = $list;
        $data['pager'] = $pager;
        return $data;
    }
}