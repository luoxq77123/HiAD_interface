<style type='text/css'>
    .right{width:auto !important;padding:0 !important;}
    .taskbar .line3{line-height:18px;}
</style>

<div class="right font12" id="info_nav_box">
    <div class="taskbar">
        <div class="line line3"> 
            <a href="<?php echo Yii::app()->createUrl('sitePosition/index');?>" style="float:left;margin-left: 20px;" class="load_frame">&lt;&lt;返回广告位列表</a>
            <div style="float:right;">
                <li class="day_time_li"><span class="full_day"></span>全部售出</li>
                <li class="day_time_li"><span class="half_day"></span>部分售出</li>
                <li class="day_time_li"><span class="null_day"></span>空闲</li>
            </div>
        </div>
    </div>
    <div class="mainTable">
        <div class="timetable">
            <?php
            $start_timestamp = strtotime($ym . '01');
            $now_year = date('Y', time());
            $now_month = date('m', time());
            $this_year = substr($ym, 0, 4);
            $this_month = substr($ym, 4);
            $date_num = date('t', $start_timestamp);
            $get = $_GET;
            unset($get['ym']);
            ?>
            <div style="width: 284px;" class="timetable-left">
                <table class="left-table" style="table-layout:auto; display:table;" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <td style="width:281px">
                                <div class="date"> 
                                    <?php if ($now_year < $this_year): ?>
                                        <a href="<?php echo Yii::app()->createUrl('sitePosition/table', array('ym' => ($this_year - 1) . $this_month) + $_GET); ?>" area="ggw_box" class="load_frame turnL fl"><?php echo ($this_year - 1); ?></a>
                                    <?php endif; ?>
                                    <a href="<?php echo Yii::app()->createUrl('sitePosition/table', array('ym' => ($this_year + 1) . $this_month) + $_GET); ?>" area="ggw_box" class="load_frame turnR fr"><?php echo ($this_year + 1); ?></a>
                                    <?php
                                    $y_m_s = array();
                                    if ($now_year == $this_year) {
                                        $cont = (int) ($now_month);
                                    }else
                                        $cont = 1;
                                    for ($i = $cont; $i <= 12; $i++) {
                                        $y_m_s[$this_year . ($i < 10 ? '0' : '') . $i] = $this_year . '-' . ($i < 10 ? '0' : '') . $i;
                                    }
                                    ?>
                                    <?php echo CHtml::dropDownList('year_month_select', $ym, $y_m_s, array('id' => 'year_month_select')); ?>
                                </div>
                                <div class="adName">广告位名称</div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($positionlist as $one): ?>
                            <tr>
                                <td style="height:26px;" title="<?php echo $one['name']; ?>"><?php echo $one['name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-left: 282px;" class="timetable_list">
                <div class="schedule-list">
                    <table class="weektable" id="weektable" style="width:810px"  border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <td class=" monthTurn month" colspan="32">
                                    <?php if ($now_year < $this_year || ($now_year == $this_year && $now_month < $this_month)): ?>
                                        <a href="<?php echo Yii::app()->createUrl('sitePosition/table', array('ym' => date('Ym', strtotime('-1 month', $start_timestamp))) + $_GET); ?>" area="ggw_box" class="load_frame turnL fl">上一月</a>
                                    <?php endif; ?>
                                    <a href="<?php echo Yii::app()->createUrl('sitePosition/table', array('ym' => date('Ym', strtotime('+1 month', $start_timestamp))) + $_GET); ?>" area="ggw_box" class="load_frame turnR fr">下一月</a>
                                    <?php echo date('Y-m', $start_timestamp); ?>
                                </td>
                            </tr>
                            <tr class="cell">
                                <?php $first = $first_day; ?>
                                <?php while ($first <= $last_day): ?>
                                    <?php
                                    $day_name = $week_name[date('N', $first)];
                                    $class = date('N', $first) > 5 ? 'weekend' : 'cell';
                                    ?>
                                    <td class="<?php echo $class; ?>"><?php echo $day_name; ?></td>
                                    <?php $first = strtotime('+1 day', $first); ?>
                                <?php endwhile; ?>
                            </tr>
                            <tr class="cell">
                                <?php $first = $first_day;?>
                                <?php while ($first <= $last_day): ?>
                                    <?php
                                    $day_name = $week_name[date('N', $first)];
                                    $class = date('N', $first) > 5 ? 'weekend' : 'cell';
                                    ?>
                                    <td class="<?php echo $class; ?>"><?php echo date('j', $first); ?></td>
                                    <?php $first = strtotime('+1 day', $first); ?>
                                <?php endwhile; ?>
                            </tr>
                        </thead>
                        <tbody class="nima">
                            <?php foreach ($positionlist as $one): ?>
                                <tr class="cell" title="">
                                    <?php
                                    $half_day = array();
                                    foreach ($one['half_day'] as $o)
                                        $half_day[] = date('Ymd', $o);
                                    ?>
                                    <?php $first = $first_day; ?>
                                    <?php while ($first <= $last_day): ?>
                                        <td <?php echo in_array(date('Ymd', $first), $one['ad_day']) ? in_array(date('Ymd', $first), $half_day) ? "class='half-sell'" : "class='full-sell'"  : ''; ?>></td>
                                        <?php $first = strtotime('+1 day', $first); ?>
                                    <?php endwhile; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 分页-->
        <div class="pl_30 adbox">
            <!--page-->
            <?php
            $this->widget('HmLinkPager', array(
                'header' => '',
                'firstPageLabel' => '首页',
                'lastPageLabel' => '末页',
                'prevPageLabel' => '上一页',
                'nextPageLabel' => '下一页',
                'pages' => $pages,
                'refreshArea' => 'ggw_box',
                'selectedPageCssClass' => 'current',
                'maxButtonCount' => 6,
                'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
                    )
            );
            ?>
            <!--end page-->
            <!--page info-->
            <?php $this->widget('PageResize', array('pages' => $pages, 'refreshArea' => 'ggw_box')); ?>
            <!--end page info-->
        </div>
        <!-- end 分页-->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        ReSet();
        
        $('#year_month_select').change(function(){
            var url = '<?php echo Yii::app()->createUrl('sitePosition/table', $get); ?>&ym=' + $('#year_month_select option:selected').val();
            ajax_load('ggw_box', url);
        });
    });
</script>