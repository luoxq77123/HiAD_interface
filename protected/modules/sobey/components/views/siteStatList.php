<!-- 统计列表 -->
<div class="tpboder adbox">
  <table border="0" cellspacing="0" cellpadding="0" class="list_table">
      <tr>
        <?php if($type=='order'):?>
        <th scope="col" width="8%" class="tpboder"><?php echo $typeName;?></th>
        <th scope="col" width="8%" class="tpboder">展现量</th>
        <th scope="col" width="8%" class="tpboder">独立访客</th>
        <th scope="col" width="8%" class="tpboder">独立IP</th>
        <th scope="col" width="8%" class="tpboder">点击量</th>
        <th scope="col" width="8%" class="tpboder">点击率</th>
        <th scope="col" width="8%" class="tpboder">CPD费用</th>
        <th scope="col" width="8%" class="tpboder">CPM费用</th>
        <th scope="col" width="8%" class="tpboder">CPC费用</th>
        <th scope="col" width="8%" class="tpboder">总费用</th>
        <th scope="col" width="8%" class="tpboder">订单余额</th>
        <th scope="col" width="8%" class="tpboder">其他</th>
        <?php else:?>
        <th scope="col" width="10%" class="tpboder"><?php echo $typeName;?></th>
        <th scope="col" width="10%" class="tpboder">展现量</th>
        <th scope="col" width="10%" class="tpboder">独立访客</th>
        <th scope="col" width="10%" class="tpboder">独立IP</th>
        <th scope="col" width="10%" class="tpboder">点击量</th>
        <th scope="col" width="10%" class="tpboder">点击率</th>
        <th scope="col" width="10%" class="tpboder">CPD费用</th>
        <th scope="col" width="10%" class="tpboder">CPM费用</th>
        <th scope="col" width="10%" class="tpboder">CPC费用</th>
        <th scope="col" width="10%" class="tpboder">总费用</th>
        <?php endif;?>
      </tr>
      <?php if (!empty($list)):?>
      <?php foreach($list as $key=>$one):?>
      <tr>
        <td><a href="javascript:void(0);" onclick="statisticsById('<?php echo $one['id'];?>', '<?php echo $one['name'];?>');"><?php echo $one['name'];?></a></td>
        <td><?php echo $one['show_num'];?></td>
        <td><?php echo $one['dedicgotd_ip'];?></td>
        <td><?php echo $one['dedicgotd_ip'];?></td>
        <td><?php echo $one['click_num'];?></td>
        <td><?php echo $one['ctr'];?>%</td>
        <td><?php echo $one['cpd_cost'];?></td>
        <td><?php echo $one['cpm_cost'];?></td>
        <td><?php echo $one['cpc_cost'];?></td>
        <td><?php echo $one['cost'];?></td>
        <?php if($type=='order'):?>
        <td><?php echo $one['balance'];?></td>
        <td><a href="javascript:void(0);" onclick="statAdDetailByOrder('<?php echo $one['id'];?>', '<?php echo $one['name'];?>');" >查看详细</a></td>
        <?php endif;?>
      </tr>
      <?php endforeach;?>
      <?php else:?>
      <tr>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <td>--</td>
        <?php if($type=='order'):?>
        <td>--</td>
        <td>--</td>
        <?php endif;?>
      </tr>
      <?php endif;?>
    </table>
</div>
<!--end 用户列表-->
<!-- 分页-->
<div class="pl_30 mb_40 adbox">
<?php if (!empty($pager)):?>
<?php
$this->widget('HmLinkPager', array(
    'header' => '',
    'firstPageLabel' => '首页',
    'lastPageLabel' => '末页',
    'prevPageLabel' => '<上一页',
    'nextPageLabel' => '下一页>',
    'refreshArea' => 'stat_list',
    'pages' => $pager,
    'selectedPageCssClass' => 'current',
    'maxButtonCount' => 6,
    'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
    )
);
?>
<!--end page-->
<!--page info-->
<?php $this->widget('PageResize', array('pages' => $pager, 'refreshArea' => 'stat_list',)); ?>
<!--end page info-->
<?php endif;?>
</div>
<!-- end 分页-->