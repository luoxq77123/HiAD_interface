<div class="popMain">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'order-form',
        'enableClientValidation' => true,
        'action' => array('orders/edit?id=' . $_GET['id']),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
        'htmlOptions' => array()
            ));
    ?>
    <table border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="110"><span class="notion">*</span><strong><?php echo $form->label($order, 'name'); ?></strong></td>
                <td><?php echo $form->textField($order, 'name', array('class' => 'txt1 txt5')); ?></td>
            </tr>
            <tr>
                <td width="110"><span class="notion">*</span><strong><?php echo $form->label($order, 'client_company_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'client_company_id', $com, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td width="110">&nbsp;<strong><?php echo $form->label($order, 'salesman_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'salesman_id', $roleuser, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong class="advanced">高级设置</strong></td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'client_contact_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'client_contact_id', $contact, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'proxy_agency_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'proxy_agency_id', $com, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'proxy_contact_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'proxy_contact_id', $contact, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'other_contact_id'); ?></strong></td>
                <td>
                    <?php echo $form->dropDownList($order, 'other_contact_id', $contact, array('class' => 'dateSle')); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php echo $form->checkBox($order, 'limit_all', array('onchange'=>'setOrderPrice()'))?>&nbsp;<?php echo $form->label($order, 'limit_all'); ?>
                </td>
            </tr>
            <tr class="orders_limit_all <?php if(!$order['limit_all']) echo 'hide';?>">
                <td width="110"><strong><label for="Orders_price">订单金额</label></strong></td>
                <td>
                    <div class="state">
                        <?php echo $form->textField($order, 'price', array('class' => 'txt1', 'style'=>'width:143px;')); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $form->checkBox($order, 'limit_day')?>&nbsp;<?php echo $form->label($order, 'limit_day'); ?></td></td>
            </tr>
            <tr class="hide">
                <td width="110"><strong><?php echo $form->label($order, 'externalID'); ?></strong></td>
                <td><?php echo $form->textField($order, 'externalID', array('class' => 'txt1 txt5','maxlength'=>18)); ?></td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'start_time'); ?></strong></td>
                <td>
                    <input  id="set_start_time" class="Wdate" type="text" readonly="true" name="Orders[start_time]" class="txt1 txt10" size="30" value="<?php if ($order->start_time) echo date('Y-m-d H:i:s', $order->start_time);else echo date('Y-m-d ',time()).'00:00:00'; ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
            </tr>
            <tr>
                <td width="110"><strong><?php echo $form->label($order, 'end_time'); ?></strong></td>
                <td class="endDate" id="end_time">
                    <?php if ($order->end_time): ?>
                        <input class="Wdate" type="text" readonly="true" name="Orders[end_time]" class="txt1 txt10" size="30" value="<?php if ($order->end_time) echo date('Y-m-d H:i:s', $order->end_time); ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" />&nbsp;&nbsp;<span class="help"><a href="#" onclick="del_end_time()">日期不限</a></span>
                    <?php else: ?>
                        <span class="endDate1">日期不限</span><span class="help"><a href="#" onclick="add_end_time()">更改日期</a></span>
                    <?php endif; ?></td>
            </tr>
            <tr>
                <td width="110" valign="top"><strong><?php echo $form->label($order, 'description'); ?></strong></td>
                <td>
                    <div class="state">
                        <?php echo $form->textArea($order, 'description', array('class' => 'txt1','style'=>'width:250px;height:60px;')); ?>
                    </div>
                    <span class="xuantian">(选填)</span>
                </td>
            </tr>
            <tr>
                <th width="90">&nbsp;</th>
                <td>
                    <div class="pt_35">
                        <button type="submit" class="iscbut_2" >完成</button>
                        <button type="button" class="ml_40 iscbut_2"  onClick="javascript:dialog_close($('#dialog-form'));">返回</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        $.validator.setDefaults({
            submitHandler: function() {
                if($('#set_end_time') && $('#set_end_time').val()){
                    var timestart=$('#set_start_time').val();
                    var timesend=$('#set_end_time').val();
                    if(timesend.length !=0 && timestart.length == 0){
                       jAlert('请选择开始时间！', '提示');
                        return false;
                    }else if(timesend.length !=0 && timestart.length != 0){
                        re=new RegExp(":","g"); 
                        timestart=timestart.replace(re,"");
                        timesend=timesend.replace(re,"");
                        re=new RegExp("-","g"); 
                        timestart=timestart.replace(re,"");
                        timesend=timesend.replace(re,"");
                        re=new RegExp(" ","g"); 
                        timestart=timestart.replace(re,"");
                        timesend=timesend.replace(re,""); 
                        if(timesend-timestart < 0){
                            jAlert('开始时间应该在结束时间之前', '提示');
                            return false;
                        }
                    }
                }
                $('#dialog-form').dialog('close');
                banner_message('后台处理中，请稍后');
                $('#order-form').ajaxSubmit({success:showResponse});
                return false;
            }
        });

        $("#order-form").validate({
            rules: {
                'Orders[name]': "required", 
                'Orders[client_company_id]': {
                    required: true
                },
                'Orders[price]': {
                    number : true
                }
            },
			
            messages: {
                'Orders[name]': {
                    required: "&nbsp;订单名称不能为空"
                },
                'Orders[client_company_id]': {
                    required: "&nbsp;请选择公司"
                },
                'Orders[price]': {
                    number: "&nbsp;请填写一个有效的数字"
                }
            }
        });
    })

    function showResponse(responseText, statusText)  {
        var data = $.parseJSON(responseText);
        if(data.code < 0){
            banner_message(data.message);
        }else{
            jAlert(data.message, '提示');
            setTimeout('frame_load("<?php echo $this->createUrl('orders/list'); ?>", true);', 1000);
        }
    }

    function add_end_time(){
        var content='<input  id="set_end_time" class="Wdate" type="text" readonly="true" name="Orders[end_time]" class="txt1 txt10" size="30" onClick="WdatePicker({dateFmt:\'yyyy-MM-dd HH:mm:ss\'})">&nbsp;&nbsp;<span class="help"><a href="#" onclick="del_end_time()">日期不限</a></span>';
        $('#end_time').html(content);
    }

    function del_end_time(){
        var content='<span class="endDate1">日期不限</span><span class="help"><a href="#" onclick="add_end_time()">更改日期</a></span>';
        $('#end_time').html(content);
    }
    
    function setOrderPrice() {
        if ($("#Orders_limit_all").attr("checked")) {
            $(".orders_limit_all").removeClass("hide");
        } else {
            $(".orders_limit_all").addClass("hide");
        }
    }
</script>
