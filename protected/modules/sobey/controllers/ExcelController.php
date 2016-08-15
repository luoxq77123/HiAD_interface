<?php
/**
 * excel导出控制器
 */
class ExcelController extends BaseController {
    private $_objPHPExcel = null;

    function __construct() {
        $this->_objPHPExcel = new PHPExcel();
        $this->_objPHPExcel->getProperties()->setCreator("IHIMI")
            ->setLastModifiedBy("IHIMI system")
            ->setTitle("Office 2003 XLS Test Document")
            ->setSubject("Office 2003 XLS Test Document")
            ->setDescription("Test document for Office 2003 XLS, generated using PHP classes.")
            ->setKeywords("office 2003 openxml php")
            ->setCategory("Statistics file");
    }

    /**
     * 订单->客户->公司列表
     */
    public function actionClientCompany() {
        $user = Yii::app()->session['user'];
        $clientCompay = ClientCompany::model()->getCom($user['com_id']);
        $key = 1;
        $this->_objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $key, '公司名称')
            ->setCellValue('B' . $key, '类型')
            ->setCellValue('C' . $key, '创建时间')
            ->setCellValue('D' . $key, '描述');
        foreach ($clientCompay as $val) {
            $key ++;
            $type = ClientCompany::model()->getType($val['type']);
            $createtime = ($val['createtime']>0)? date("Y-m-d H:i:s", $val['createtime']) : '---' ;
            $this->_objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $key, $val['name'])
                ->setCellValue('B' . $key, $type[$val['type']])
                ->setCellValue('C' . $key, $createtime)
                ->setCellValue('D' . $key, $val['description']);
        }
        $this->_outputExcel("客户列表");
    }

    /**
     * 订单->订单管理->订单列表
     */
    public function actionClientOrders() {
        $user = Yii::app()->session['user'];
        $list = Orders::model()->getOrdersList($user['com_id']);
        $clientCompay = ClientCompany::model()->getCom($user['com_id']);
        $arrStatus = Orders::model()->getStatus();
        $key = 1;
        $this->_objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $key, '订单名称')
            ->setCellValue('B' . $key, '广告客户')
            ->setCellValue('C' . $key, '开始时间')
            ->setCellValue('D' . $key, '结束时间')
            ->setCellValue('E' . $key, '创建时间')
            ->setCellValue('F' . $key, '状态');
        foreach ($list as $val) {
            $key ++;
            $comName = (!empty($clientCompay[$val['client_company_id']]['name'])) ? $clientCompay[$val['client_company_id']]['name'] : "--";
            $startTime = ($val['start_time']) ? date('Y-m-d H:i:s', $val['start_time']) : '未设时间';
            $endTime = ($val['end_time']) ? date('Y-m-d H:i:s', $val['end_time']) : '未设时间';
            $createtime = date('Y-m-d H:i:s', $val['createtime']);
            $this->_objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $key, $val['name'])
                ->setCellValue('B' . $key, $comName)
                ->setCellValue('C' . $key, $startTime)
                ->setCellValue('D' . $key, $endTime)
                ->setCellValue('E' . $key, $createtime)
                ->setCellValue('F' . $key, $arrStatus[$val['status']]);
        }
        $this->_outputExcel("订单列表");
    }

    /**
     * 排期->排期表
     */
    public function actionSchedule() {
        $user = Yii::app()->session['user'];
        $list = Schedule::model()->getAll($user['com_id']);
        $positionList = Position::model()->getPositionInfo($user['com_id']);
        $clientCompay = ClientCompany::model()->getCom($user['com_id']);
        $adType = AdType::model()->getAdTypeName();
        $multiTime = array(0 => '否', 1 => '是');
        $status = array(1 => '启用', 0 => '删除', -1 => '禁用');
        $key = 1;
        $this->_objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $key, '排期名称')
            ->setCellValue('B' . $key, '广告位名称')
            ->setCellValue('C' . $key, '广告客户')
            ->setCellValue('D' . $key, '状态')
            ->setCellValue('E' . $key, '广告位类型')
            ->setCellValue('F' . $key, '多时间段')
            ->setCellValue('G' . $key, '创建时间');
        foreach ($list as $val) {
            $key ++;
            $pName = (!empty($positionList[$val['position_id']]))? $positionList[$val['position_id']]['name'] : "--";
            $comName = (!empty($clientCompay[$val['client_company_id']]['name'])) ? $clientCompay[$val['client_company_id']]['name'] : "--";
            $createTime = date('Y-m-d H:i:s', $val['createtime']);
            $this->_objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $key, $val['name'])
                ->setCellValue('B' . $key, $pName)
                ->setCellValue('C' . $key, $comName)
                ->setCellValue('D' . $key, $status[$val['status']])
                ->setCellValue('E' . $key, $adType[$positionList[$val['position_id']]['ad_type_id']])
                ->setCellValue('F' . $key, $multiTime[$val['multi_time']])
                ->setCellValue('G' . $key, $createTime);
        }
        $this->_outputExcel("排期列表");
    }

    /**
     * 排期->投放任务
     */
    public function actionScheduleTask() {
        $user = Yii::app()->session['user'];
        $list=Schedule::model()->getAll($user['com_id']);
        $positionList = Position::model()->getPositionInfo($user['com_id']);
        $clientCompay = ClientCompany::model()->getCom($user['com_id']);
        $arrScheduleid=Array();
        foreach($list as $key=>$val){
            $arrScheduleid[$key]=$val['id'];
        }
        $criteria = new CDbCriteria();
        $schedulelist = Schedule::model()->with('ScheduleTime')->findAll($criteria);
        $roleuser = User::model()->getUserByRole($user['com_id'], 3);
        $scheduletime= array();
        foreach ($schedulelist as $one) {
            if (count($one->ScheduleTime) > 1) {
                $scheduletime[$one->id]['start_time'] = '多时间段';
                $scheduletime[$one->id]['end_time'] = '多时间段';
            } else {
                if (isset($one->ScheduleTime[0]->start_time))
                    $scheduletime[$one->id]['start_time'] = date('Y-m-d H:i:s', $one->ScheduleTime[0]->start_time);
                if (isset($one->ScheduleTime[0]->end_time))
                    $scheduletime[$one->id]['end_time'] = date('Y-m-d H:i:s', $one->ScheduleTime[0]->end_time);
            }
        }
        $key = 1;
        $this->_objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $key, '排期名称')
            ->setCellValue('B' . $key, '广告位名称')
            ->setCellValue('C' . $key, '开始时间')
            ->setCellValue('D' . $key, '结束时间')
            ->setCellValue('E' . $key, '广告客户')
            ->setCellValue('F' . $key, '销售人员');
        if(!empty($list)) {
            foreach ($list as $val) {
                if($val['status']==1){
                $key ++;
                $pName = (!empty($positionList[$val['position_id']]))? $positionList[$val['position_id']]['name'] : "--";
                $startTime = (!empty($scheduletime[$val['id']])) ? $scheduletime[$val['id']]['start_time']:'--';
                $endTime = (!empty($scheduletime[$val['id']])) ? $scheduletime[$val['id']]['end_time']:'--';
                $comName = (!empty($clientCompay[$val['client_company_id']]['name'])) ? $clientCompay[$val['client_company_id']]['name'] : "--";
                $scheduleName=(!empty($roleuser[$val['salesman_id']]['name'])) ? $roleuser[$val['salesman_id']]['name']:"--";
                $this->_objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $key, $val['name'])
                    ->setCellValue('B' . $key, $pName)
                    ->setCellValue('C' . $key, $startTime)
                    ->setCellValue('D' . $key, $endTime)
                    ->setCellValue('E' . $key, $comName)
                    ->setCellValue('F' . $key, $scheduleName);
                }
            }
        }
        $this->_outputExcel("投放排期列表");
    }

    /**
     * 统计-> 统计报告
     */
    public function actionStatistics() {
        // 获取满足条件的统计数据
        $params = SiteStatistics::model()->parseParams();
        $return = array();
        if (empty($params['typeid']) && !empty($_GET['ad_name'])) {
            $return = SiteStatistics::model()->combineData(array(), $params['type']);
        } else {
            // 根据条件获取统计数据
            $statisticsData = array();
            if (isset($_GET['ad_type'])&&$_GET['ad_type']=='app') {
                $statisticsData = AppStatistics::model()->getAllList($params['type'], $params['typeid'],$params['startDate'], $params['endDate']);
            } else {
                $statisticsData = SiteStatistics::model()->getAllList($params['type'], $params['typeid'],$params['startDate'], $params['endDate']);
            }
            // 组合数据
            $return = SiteStatistics::model()->combineData($statisticsData, $params['type']);
        }
        // 解析数据 插入到excel对象
        $fileName = (isset($_GET['ad_type'])&&$_GET['ad_type']=='app')? "客户端" : "站点";
        $type = $params['type'];
        $arrType = SiteStatistics::model()->getStatTypeName();
        $typeName = $arrType[$type];
        $fileName .= $typeName;
        $key = 1;
        $this->_objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $key, $typeName)
            ->setCellValue('B' . $key, '展现量')
            ->setCellValue('C' . $key, '独立访客')
            ->setCellValue('D' . $key, '独立IP')
            ->setCellValue('E' . $key, '点击量')
            ->setCellValue('F' . $key, '点击率')
            ->setCellValue('G' . $key, 'CPD费用')
            ->setCellValue('H' . $key, 'CPM费用')
            ->setCellValue('I' . $key, 'CPC费用')
            ->setCellValue('J' . $key, '总费用');

        if (!empty($return['list'])) {
            $statRows = $return['list'];
            foreach ($statRows as $id=>$one) {
                $key ++;
                $this->_objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $key, $one['name'])
                    ->setCellValue('B' . $key, $one['show_num'])
                    ->setCellValue('C' . $key, $one['dedicgotd_ip'])
                    ->setCellValue('D' . $key, $one['dedicgotd_ip'])
                    ->setCellValue('E' . $key, $one['click_num'])
                    ->setCellValue('F' . $key, $one['ctr'].'%')
                    ->setCellValue('G' . $key, $one['cpd_cost'])
                    ->setCellValue('H' . $key, $one['cpm_cost'])
                    ->setCellValue('I' . $key, $one['cpc_cost'])
                    ->setCellValue('J' . $key, $one['cost']);
            }
            unset($return);
        }
        $fileName .= "统计报告";
        $this->_outputExcel($fileName);
    }

    /**
     * 输出excel信息
     */
    private function _outputExcel($fileName) {
        $this->_objPHPExcel->getActiveSheet()->setTitle('Simple');
        $this->_objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE"))
            header('Content-Disposition:attachment;filename="' . urlencode($fileName) . '.xls"');
        else
            header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}