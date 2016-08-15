<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="#">订单</a> &gt; <a href="#">订单管理</a></div>
</div>
<!--end 面包屑-->
<!--按钮-->
<div class="tpboder pl_20 adbox">
    <div class="lxr_sx" style="float:left;"><a href="<?php echo $this->createUrl('orders/add'); ?>" id="add" title="新建订单" class="iscbut cbut_jia"><span>新建订单</span></a>
    <div class="fr mr_40" style="float:right0; margin-left:950px; margin-top:-35px;">
        <a class=" ml_40 iscbut" title="下载订单列表" href="<?php echo $this->createUrl('excel/clientOrders'); ?>">
        <span>下载订单列表</span></a>
    </div>
    </div>
</div>
<!--end 按钮-->
<!--提示-->
<div class="taskbar">
    <div class="line4" id="banner_message" style="display: none;">
        <div class="line41 fr">
            <a href="javascript:void(0);" class="close_message"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/deltit.gif" /></a>
        </div>
        <div class="message_area"></div>
    </div>
</div>
<!--end 提示-->
<!--表单-->
<div class="tpboder pl_30 adbox">
    <form method="get" onsubmit="return com_search();" class="list_search_form">
        <div class="fl shaixuan">
            <label>状态:
                <?php echo CHtml::dropDownList('status_search', @$_GET['status'], $status, array('class' => 'txt1', 'id' => 'status_search')); ?>
            </label>
            <label class="pl_20">公司:
                <?php echo CHtml::dropDownList('type_search', @$_GET['com'], $com, array('class' => 'txt1', 'id' => 'type_search')); ?>
            </label>
        </div>
        <div class="fr sz_sc"><span>订单名称:&nbsp;</span><?php echo CHtml::textField('name_search', @$_GET['name'], array('class' => 'txt1', 'id' => 'name_search')); ?>&nbsp;<input type="button" class="iscbut_4" value="搜索" onclick="com_search()" /></div>
    </form>
</div>
<!--end 表单-->
<!--操作按钮-->
<div class="tpboder pl_30 adbox">
    <div class="butgn nobutgn" id="butgn">
        <input type="button" onclick="orderSetStatus(1);" value="启用">
        <input type="button" onclick="orderSetStatus(0);" value="禁用">
        <input type="button" onclick="orderSetStatus(-1);" value="删除">
    </div>
</div>
<!--end 操作按钮-->
<!-- 用户列表 -->
<div class="tpboder adbox">
    <table border="0" cellspacing="0" cellpadding="0" class="list_table" id="list_table">
        <tr>
            <th scope="col" width="5%" class="tpboder tx_c"><label><input type="checkbox" id="lxr_qx" /></label></th>
            <th scope="col" width="10%" class="tpboder">订单名称</th>
            <th scope="col" width="30%" class="tpboder">广告客户</th>
            <th scope="col" width="15%" class="tpboder">开始时间</th>
            <th scope="col" width="15%" class="tpboder">结束时间</th>
            <th scope="col" width="15%" class="tpboder">创建时间</th>
            <th scope="col" width="10%" class="tpboder">操作</th>
        </tr>
        <?php if($orderlist):?>
        <?php foreach ($orderlist as $one): ?>
            <tr>
                <td><input type="checkbox" class="checkbox_order" name="order[]" value="<?php echo $one->id; ?>" /></td>
                <td><?php echo $one->name; ?></td>
                <td><?php if (isset($com[$one->client_company_id])) echo $com[$one->client_company_id];else echo '--'; ?></td>
                <td><?php if ($one->start_time) echo date('Y-m-d H:i:s', $one->start_time);else echo '未设时间'; ?></td>
                <td><?php if ($one->end_time) echo date('Y-m-d H:i:s', $one->end_time);else echo '不限时间'; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $one->createtime); ?></td>
                <td>
                   
                     <a href="<?php echo $this->createUrl('orders/edit', array('id' => $one->id)); ?>" title="修改订单信息" class="kh_edit">修改</a><!-- &nbsp;|&nbsp;<a href="javascript:orderSetStatus(-1, <?php echo $one->id; ?>);">删除</a> -->
                    
                </td>
            </tr>
        <?php endforeach; ?>
                  <?php else:?>
            <tr>
                <td></td>
               <td colspan="8"><span>没有查到相关的内容！</span></td>
            </tr>
          <?php endif;?>
    </table>
</div>
<!--end 用户列表-->

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
        'selectedPageCssClass' => 'current',
        'maxButtonCount' => 6,
        'htmlOptions' => array('class' => 'fl pagination', 'id' => 'pagination')
            )
    );
    ?>
    <!--end page-->
    <!--page info-->
    <?php $this->widget('PageResize', array('pages' => $pages)); ?>  
    <!--end page info-->
</div>
<!-- end 分页-->
<script type="text/javascript">
    $(document).ready(function(e) {
        /*修改公司弹窗*/
			
        dialog_ajax_ko({"list":$("#add"),"width":630,"height":610});
        dialog_ajax_ko({"list":$(".kh_edit"),"width":630,"height":610});

        $("#lxr_qx").change(function(){
            if(xile_input_all($(this),$("#list_table"))){
                if($('.checkbox_order:checked').length > 0)
                    $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });
			
        $('.checkbox_order').click(function(){
            if($('.checkbox_order:checked').length > 0){
                $("#butgn").removeClass("nobutgn");	
            }else{
                $("#butgn").addClass("nobutgn");
            }
        });

    });

    function com_search(){
        var status = $("#status_search").val();
        var com = $('#type_search').val();
        var name = $.trim($('#name_search').val());
        var url = '<?php echo $this->createUrl('orders/list') ?>?status='+status+'&com='+com+'&name='+encodeURIComponent(name);
        if(typeof(ajax_load) == 'function')
            frame_load(url);
        else
            window.location = url;
        return false;
    }
    
    function orderSetStatus(status, id){
        order = new Array();
        if(id){
            order.push(id);
        }else{
            $('.checkbox_order:checked').each(function(){
                order.push($(this).val());
            });
        }
        if(order.length < 1){
            return;
        }
        var status_1 = $("#status_search").val();
        //banner_message('后台处理中，请稍后');
        $.post(
            '<?php echo $this->createUrl('orders/setStatus'); ?>',
            {'status':status, 'order[]':order}, 
            function(data){
                if(data.code < 0){
                    banner_message(data.message);
                }else{
                    jAlert(data.message, '提示');
                    setTimeout('frame_load("<?php echo $this->createUrl('orders/list?status='); ?>'+status_1+'", true);', 1000);
                }
            },
            'json'
        );
    }
</script>
