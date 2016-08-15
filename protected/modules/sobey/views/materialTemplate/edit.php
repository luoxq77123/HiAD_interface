<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="#">物料库</a> &gt; <a href="#">新建广告物料模板</a></div>
</div>
<!--end 面包屑-->
<div class="taskbar">
    <div class="line4" id="banner_message" style="display:none;">
        <div class="line41 fr">
            <a href="javascript:void(0);" class="close_message"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/deltit.gif" /></a>
        </div>
        <div class="message_area"></div>
    </div>
</div>
<div class="top_prompt">
对一些固定的广告物料样式，如图文混排等，可以将其设计成广告物料模板。之后创建相同样式的广告物料，只需在富媒体类型中选择模板即可，避免重复写样式代码的麻烦。请按照语法规范，设计广告物料模板。<br/>
(1) 定义所需的参数   (2) 编写HTML代码，并使用定义的参数替换相应属性值。   <a href="">查看详细</a>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'material-form',
    'enableClientValidation' => true,
    'action' => array('materialTemplate/edit?id='.$_GET['id']),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array()
));
?>
<table class="webadd new_web" id="material_table" style="width:900px; margin-left:0px!important; margin-left:40px;"  border="0" cellpadding="10" cellspacing="10">
    <tr>
        <th style="width:180px;"><span class="notion">*</span><?php echo $form->label($material, 'name'); ?></th>
        <td><?php echo $form->textField($material, 'name', array('class' => 'txt1')); ?></td>
    </tr>
    <tr>
        <th style="width:180px;"><span class="notion">*</span><?php echo $form->label($material, 'code'); ?></th>
        <td><?php echo $form->textArea($material, 'code', array('class' => 'txt1', 'style' => 'width:400px;height:175px;')); ?>
        </td>
        <td>
            常用语法规则：<br/>
            (1)声明参数：var 参数名:数据类型=0或1;//参数含义说明
            数据类型包括media(图片或Flash)、link(点击链接)、
            text(文本)、number (数值)
            0或1表示是否必填，0为选填，1为必填
            例如，var imglink:link=1;//图片点击链接<br/>
            (2)引用参数：${参数名}
            例如，&lt;a href="${imglink}" target="_blank"&gt;…&lt;/a&gt; 
        </td>
    </tr>
    <tr>
        <th style="width:180px;"><?php echo $form->label($material, 'description'); ?></th>
        <td><?php echo $form->textArea($material, 'description', array('class' => 'txt1', 'style' => 'width:300px;height:115px;')); ?></td>
    </tr>
</table>

<div class="bgline"></div>
<br/>
<div class="ml_240 pt_35">
    <button type="submit" class="iscbut_2">完成</button>     
    <a href="javascript:void(0)" onclick="backout()" class="mgl38 tool_42_link"><button type="button" class="ml_40 iscbut_2">返回</button></a>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function(e) {
        // ajax提交
        $.validator.setDefaults({
            submitHandler: function() {
                $('#dialog-form').dialog('close')
                banner_message('后台处理中，请稍后');
                $('#material-form').ajaxSubmit({success:showResponse});
                return false;
            }
        });
        
        // 验证
        $("#material-form").validate({
            rules: {
                'MaterialTemplate[name]':{
                    required: true
                },
                'MaterialTemplate[code]':{
                    required: true
                }
            },
            messages: {
                'MaterialTemplate[name]':{
                    required: '&nbsp;物料模板名称不能为空'
                },
                'MaterialTemplate[code]':{
                    required: '&nbsp;代码不能为空'
                }
            }
        });
    });
    
    function showResponse(responseText, statusText) {
        var data = $.parseJSON(responseText);
        if (data.code<0) {
            banner_message(data.message);
        }else{
            jAlert(data.message, '提示');
            setTimeout('frame_load("<?php echo !empty($_GET['backUrl'])? $_GET['backUrl'] : $this->createUrl('materialTemplate/list'); ?>");', 1000);
        }
    }

    function backout(){
        jConfirm("你确定要放弃修改吗？", "提示", function(e){
           if(e){
                setTimeout('frame_load("<?php echo !empty($_GET['backUrl'])? $_GET['backUrl'] : $this->createUrl('materialTemplate/list'); ?>");', 1);
            }
         });
    }
</script>