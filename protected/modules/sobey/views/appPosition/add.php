<?php
$form = $this->beginWidget('CActiveForm', array(
            'id' => 'app-position-form',
            'enableClientValidation' => true,
            'action' => array('Position/add'),
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
            'htmlOptions' => array()
        ));
?>
<link href="<?php echo $this->module->assetsUrl; ?>/css/adctrlstyle.css" rel="stylesheet" />
<div class="body">
	<div class="ad-tit">
    	<span class="closebox">关闭</span>
    	<h6>新建广告位</h6>
    </div>
    <div class="ad-bod">
    	<form>
    	<div class="tab-box tab-liubai">
            <table border="0"  cellpadding="8" class="one-table nofw">
                <tbody>
                    <tr>
                      <th><span class="notion">*</span><?php echo $form->label($position, 'sort');?></th>
                      <td><?php echo $form->textField($position, 'sort', array('class' => 'txt1','maxlength'=>10)) ;?><span>(数字越大,显示顺序越靠后)</span></td>
                    <tr>
                    <tr>
                      <th><span class="notion">*</span><?php echo $form->label($position, 'name');?></th>
                      <td><?php echo $form->textField($position, 'name', array('class' => 'txt1')) ;?></td>
                    </tr>
                    <tr valign="top">
                      <th><?php echo $form->label($position, 'description');?></th>
                      <td><?php echo $form->textArea($position, 'description', array('cols' => '80', 'rows' => '8', 'class' => 'txt1', 'style' => 'height:135px;width:450px;')) ;?></td>
                    </tr>
              </tbody>
            </table>
            </div>
            <div class="bott-f">
            	<input type="submit" value="完成" class="ml_20 butt2" />
                <input type="button" value="返回" class="ml_20 butt2" />
            </div>
        </form>
    </div>    
</div>

<div class="bgline"></div>
<br/>
<div class="add"> <a href="javascript:void(0);" >
  <input  type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn3.gif" />
  </a> <a href="javascript:void(0);" onClick="$('#dialog-form').dialog('close');"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn4.gif" /></a> </div>
<?php $this->endWidget(); ?>

        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/manhua_hoverTips.js?var=-1" type="text/javascript"></script>
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/manhua_hoverTips.css?var=-1" rel="stylesheet" type="text/css" />
        
<script type="text/javascript">
    $(function (){
    	$(".hover_tips").manhua_hoverTips({position : "r"});//改变了显示的位置参数
    });
    //详细信息展开收起
    var $id = function (id) 
    {
        return document.getElementById(id);
    }
    function xZhankai(onid,offid){
        if($id(onid)){
            $id(onid).style.display = "none";  
        }
        if($id(offid)){
            $id(offid).style.display = "block";
        }
    }
    $(document).ready(function(e) {
        $(".lakai").click(function(){
            $(".left").toggle();
        });

        dialog_ajax_ko({"list":$("#add_gs"),"width":660,"height":340});
        
        $('input[name="Position[ad_show_id]"]').change(function(){
            $('.ad_type_more').hide();
            displayAppAttr();
        });

        $("#AppPosition_app_id").change(function(){
            displayAppAttr();
        });

        // 绑定自定义高宽默认显示
        $('.size_input').focusin(function(){
            if($(this).val() == '高' || $(this).val() == '宽'){
                $(this).val('');
            } 
        }).focusout(function(){
            if($(this).val() == ''){
                var name = $(this).attr('name') == 'size_x' ? '宽' : '高';
                $(this).val(name);
            }
        });
        
        // ajax提交
        $.validator.setDefaults({
            submitHandler: function() {
                $('#dialog-form').dialog('close')
                banner_message('后台处理中，请稍后');
                $('#app-position-form').ajaxSubmit({success:showResponse});
                return false;
            }
        });
        
        // 验证
        $("#app-position-form").validate({
            rules: {
                'Position[sort]':{
                    required: true,
                    digits: true,
                    min: 1,
                    max: 10000
                },
                'Position[name]':'required',
                'AppPosition[space_x]':{
                    required: true,
                    digits: true,
                    min: 0,
                    max: 10000
                },   
                'AppPosition[app_id]':{
                   required: true
                },
                'AppPosition[space_y]':{
                    required: true,
                    digits: true,
                    min: 0,
                    max: 10000
                },
                'size_x': {
                    required: true,
                    digits: true,
                    min: 1,
                    max: 10000
                },
                'size_y' : {
                    required: true,
                    digits: true,
                    min: 1,
                    max: 10000
                },
                'scale_xs' : {
                    required: true,
                    digits: true,
                    min: 0,
                    max: 100
                },
                'scale_x':{
                    required: true,
                    digits: true,
                    min: 1
                },
                'scale_y':{
                    required: true,
                    digits: true,
                    min: 1
                }
            },
            messages: {
                'Position[sort]':{
                    required: '&nbsp;排序请填入1-10000的数字',
                    digits: '&nbsp;排序请填入1-10000的数字',
                    min: '&nbsp;排序请填入1-10000的数字',
                    max: '&nbsp;排序请填入1-10000的数字'
                },
                'Position[name]':'&nbsp;请填入广告位名称',
                'AppPosition[space_x]':{
                    required: '&nbsp;请填入0-10000的数字',
                    digits: '&nbsp;请填入0-10000的数字',
                    min: '&nbsp;请填入0-10000的数字',
                    max: '&nbsp;请填入0-10000的数字'
                }, 
                'AppPosition[app_id]':{
                   required: '&nbsp;您还没有选择所属应用'
                },
                'AppPosition[space_y]':{
                    required: '&nbsp;请填入0-10000的数字',
                    digits: '&nbsp;请填入0-10000的数字',
                    min: '&nbsp;请填入0-10000的数字',
                    max: '&nbsp;请填入0-10000的数字'
                },
                'size_x': {
                    required: '自定义宽请填入1-10000的数字',
                    digits: '自定义宽请填入1-10000的数字',
                    min: '自定义宽请填入1-10000的数字',
                    max: '自定义宽请填入1-10000的数字'
                },
                'size_y': {
                    required: '自定义高请填入1-10000的数字',
                    digits: '自定义高请填入1-10000的数字',
                    min: '自定义高请填入1-10000的数字',
                    max: '自定义高请填入1-10000的数字'
                },
                'AppPosition[staytime]':{
                    required: '<br/>请填入1-10000的数字',
                    digits: '<br/>请填入1-10000的数字',
                    min: '<br/>请填入1-10000的数字',
                    max: '<br/>请填入1-10000的数字'
                },
                'scale_xs' : {
                    required: '<br/>请填入0-100的数字',
                    digits: '<br/>请填入0-100的数字',
                    min: '<br/>请填入0-100的数字',
                    max: '<br/>请填入0-100的数字'
                },
                'scale_x':{
                    required: '<br/>请填入1-10000的数字',
                    digits: '<br/>请填入1-10000的数字',
                    min: '<br/>请填入1-10000的数字'
                },
                'scale_y':{
                    required: '<br/>请填入1-10000的数字',
                    digits: '<br/>请填入1-10000的数字',
                    min: '<br/>请填入1-10000的数字'
                }
            }
        });
    });
    
    function displayAppAttr() {
        var now = $('input[name="Position[ad_show_id]"]:checked').attr('id');
        $('.'+now+'_more').show();
        var label = $("#AppPosition_app_id option:selected").parent().attr('label');
        if (label == 'IOS应用') {
            showIOSAttr();
            if (now == 'pop') {
                $(".pop_android_attr").hide();
                $(".pop_ios_attr").show();
            }
        } else if (label == 'Android应用') {
            showAndroidAttr();
            if (now == 'pop') {
                $("#size_defined").hide();
                $(".pop_ios_attr").hide();
                $(".pop_android_attr").show();
            }
        }
    }
    
    function showIOSAttr() {
        $(".android_attr").hide();
        $(".ios_attr").show();
    }
    
    function showAndroidAttr() {
        $(".ios_attr").hide();
        $(".android_attr").show();
    }
    
    function showResponse(responseText, statusText)  {
        //alert(responseText);
        var data = $.parseJSON(responseText);
        if(data.code < 0){
            banner_message(data.message);
        }else{
            jAlert(data.message, '提示');
            var url = $('#t_nav li.now div.navtit a').attr('href');
            setTimeout('ajax_load("ggw_box", "'+url+'");', 1000);
        }
    }

    function size_select(i){
        $('.size_select').hide();
        $('#size_'+i).show();
        var v = i == 'default' ? 0 : 1;
        $('input[name="size_defined"]').val(v);
    }
    function st_limit(i){
        $('.st_limit').hide();
        $('#st_'+i).show();
        if(i == 'unlimit'){
            $('#AppPosition_staytime').val(0);
        }else if($('#AppPosition_staytime').val() == 0){
            $('#AppPosition_staytime').val(1);
        }
    }
</script>
<style type="text/css">
.pssinput,.size_input{width:30px;}
.w_40{ width:40px;}
</style>
