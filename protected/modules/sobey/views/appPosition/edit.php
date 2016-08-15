<?php
$form = $this->beginWidget('CActiveForm', array(
            'id' => 'app-position-form',
            'enableClientValidation' => true,
            'action' => array('appPosition/edit?id='.$_GET['id']),
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
            'htmlOptions' => array()
        ));
?>

<table class="webadd new_web"  border="0" cellpadding="10" cellspacing="10">
    <tr>
        <th><span class="notion">*</span><?php echo $form->label($position, 'sort');?></th>
        <td><?php echo $form->textField($position, 'sort', array('class' => 'txt1')) ;?><span>(数字越大,显示顺序越靠后)</span></td>
    <tr>
        <th><span class="notion">*</span><?php echo $form->label($position, 'name');?></th>
        <td><?php echo $form->textField($position, 'name', array('class' => 'txt1')) ;?></td>
    </tr>
    <tr>
        <th>类型</th>
        <td><div class="system_tips_box_td">
        <?php if (!empty($adShows)): ?>
        <?php foreach($adShows as $k=>$one): ?>
            <input type="radio" name="Position[ad_show_id]" id="<?php echo $one['code'];?>" value="<?php echo $one['id'];?>" <?php if($one['id']==$position->ad_show_id) echo 'checked="checked"';?> />
            <label for="<?php echo $one['code'];?>"> <?php echo $one['name'];?> </label>
        <?php endforeach; ?>
        <?php endif; ?></div>
        <div class="system_tips_box" >
        <ul>
          <li><a href="javascript:void(0);" style="margin-left: -25px;"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tit4.gif" />
            <div>
              <dt>广告位类型有哪些？</dt>
              <dd>固定：在页面上占据固定位置的广告位。<br />
                插播：中断节目而出现的广告位。<br />
                播放器：在节目开始前出现的广告位。 </dd>
            </div>
            </a></li>
        </ul>
      </div></td>
    </tr>
    <tr>
        <th><?php echo $form->label($appPosition, 'app_id');?></th>
        <td><div class="system_tips_box_td">
            <?php echo $form->dropDownList($appPosition, 'app_id', $apps, array('class' => 'txt1')); ?>
            <a href="<?php echo $this->createUrl('app/add'); ?>" class="suosu" id="add_gs" title="新建应用">新建应用</a> 
            </div><div class="system_tips_box">
        <ul>
          <li><a href="javascript:void(0);"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tit4.gif" />
            <div>
              <dt>什么是应用？</dt>
              <dd>对自己的广告进行划分，明确其属于什么样的广告。 </dd>
            </div>
            </a></li>
        </ul>
      </div></td>
    </tr>
    <tr class="hiden size_select ios_attr" id="size_default">
        <th><span class="notion">*</span><?php echo $form->label($position, 'position_size');?></th>
        <td>
            <?php echo $form->dropDownList($position, 'position_size', $sizes, array('class' => 'txt1')); ?>
            <a class="cicun" onclick="size_select('defined');" href="javascript:void(0);">自定义尺寸</a>
        </td>
    </tr>
    <tr class="<?php if($appParams['appType']=='android') echo 'hiden';?> size_select" id="size_defined">
        <th><span class="notion">*</span><?php echo $form->label($position, 'position_size');?></th>
        <td>
            <input type="hidden" name="size_defined" value="0" />
            <input type="text" value="<?php echo !empty($appParams['attr']['width'])? $appParams['attr']['width'] : '';?>" name="size_x" class="txt1 size_input" />&nbsp;*&nbsp;<input type="text" name="size_y" value="<?php echo !empty($appParams['attr']['height'])? $appParams['attr']['height'] : '';?>" class="txt1 size_input" />&nbsp;(px)
            <a class="cicun" onclick="size_select('default');" href="javascript:void(0);">选择常用尺寸</a><br/>
            <label for="size_x" generated="true" class="error"></label>
            <label for="size_y" generated="true" class="error"></label>
        </td>
    </tr>
    <tr class="size_select android_attr <?php if($appParams['appType']=='ios') echo 'hiden';?>" id="size_scale_xs">
        <th><span class="notion">*</span>宽度比例</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['scale_xs'])? $appParams['attr']['scale_xs'] : '';?>" name="scale_xs" class="txt1 w_40" />% <span>(广告位宽度与屏幕宽度比例0-100之间)</span>
            <label for="scale_xs" generated="true" class="error"></label>
        </td>
    </tr>
    <tr class="<?php if($appParams['appType']=='ios') echo 'hiden';?> android_attr size_select" id="size_scale_xy">
        <th><span class="notion">*</span>宽高比例</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['scale_x'])? $appParams['attr']['scale_x'] : '';?>" name="scale_x" class="txt1 w_40" /> : 
            <input type="text" value="<?php echo !empty($appParams['attr']['scale_y'])? $appParams['attr']['scale_y'] : '';?>" name="scale_y" class="txt1 w_40" />
            <span>(广告位宽度与高度比例)</span>
            <label for="scale_x" generated="true" class="error"></label>
            <label for="scale_y" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top" class="ad_type_more pop_more hiden">
        <th>是否全屏</th>
        <td>
            <?php echo $form->radioButtonList($appPosition, 'is_full', AppPosition::model()->getIsOption(), array('separator' => ' '));?>
            <label for="AppPosition_is_full" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top" class="ad_type_more pop_more pop_ios_attr hiden">
        <th>距左距离</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['left'])? $appParams['attr']['left'] : '';?>" name="left" class="txt1 w_40" />
            <span>(广告位距左距离以像素为单位)</span>
            <label for="left" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top" class="ad_type_more pop_more pop_ios_attr hiden">
        <th>距上距离</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['top'])? $appParams['attr']['top'] : '';?>" name="top" class="txt1 w_40" />
            <span>(广告位距上距离以像素为单位)</span>
            <label for="top" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top" class="ad_type_more pop_android_attr pop_more hiden">
        <th>左偏移值</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['offset_left'])? $appParams['attr']['offset_left'] : '';?>" name="offset_left" class="txt1 w_40" />
            <span>(广告位距左比例（0-100）%)</span>
            <label for="offset_left" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top" class="ad_type_more pop_android_attr pop_more hiden">
        <th>上偏移值</th>
        <td>
            <input type="text" value="<?php echo !empty($appParams['attr']['offset_top'])? $appParams['attr']['offset_top'] : '';?>" name="offset_top" class="txt1 w_40" />
            <span>(广告位距上比例（0-100）%)</span>
            <label for="offset_top" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top">
        <th><?php echo $form->label($appPosition, 'staytime');?></th>
        <td>
            <span id="st_unlimit" class="st_limit">不限&nbsp;<a href="javascript:void(0);" style="margin:0;" onclick="st_limit('limit');">更改</a></span>
            <span id="st_limit" class="hiden st_limit">
                <?php echo $form->textField($appPosition, 'staytime', array('class' => 'txt1 pssinput')) ;?>秒
                <a href="javascript:void(0);" style="margin:0;" onclick="st_limit('unlimit');">不限</a>
            </span>&nbsp;
            <label for="AppPosition_staytime" generated="true" class="error"></label>
        </td>
    </tr>
    <tr valign="top">
        <th><?php echo $form->label($position, 'description');?></th>
        <td><?php echo $form->textArea($position, 'description', array('cols' => '80', 'rows' => '8', 'class' => 'txt1', 'style' => 'height:135px;width:450px;')) ;?></td>
    </tr>
</table>
<div >
    <div id="shao" ><a href="javascript:void(0);" onclick="javascript:xZhankai('shao','duo');"><span>︾</span>高级设置</a></div>
    <div id="duo" style="display:none;"><a href="javascript:void(0);" onclick="javascript:xZhankai('duo','shao');"><span>︽</span>高级设置</a>
        <p> 
            <?php echo $form->label($appPosition, 'idle_take');?>
            <?php echo $form->radioButtonList($appPosition, 'idle_take', AppPosition::model()->getIdleTakeOption(), array('separator' => ' '));?>
            <!--<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tit4.gif" /> -->
        </p>
    </div>
</div>
<div class="bgline"></div>
<br/>
<div class="add hicreat">
    <a href="javascript:void(0);" ><input  type="image" src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn3.gif" /></a>
    <a href="javascript:void(0);" onClick="$('#dialog-form').dialog('close');"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn4.gif" /></a> 
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
    displayAppAttr();
    <?php if($appParams['appType']=='ios'): ?>
    size_select("defined");
    <?php endif;?>
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
                'AppPosition[staytime]':{
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