<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>添加广告资源-第一步-广告设置</title>
    <link href="<?php echo $this->module->assetsUrl; ?>/css/adctrlstyle.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->module->assetsUrl; ?>/js/datepicker/jqueryui/jquery.ui.all.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->module->assetsUrl; ?>/js/jQueryAlert/jquery.alerts.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->module->assetsUrl; ?>/css/manhua_hoverTips.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $this->module->assetsUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/main.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/helper.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/datepicker/jquery.ui.datepicker.my.js" type="text/javascript"></script> 
    <style>
        body {background-color: #FFF;}
    </style>
</head>
<body>
<div class="body" style = "background-color: #FFF;">
	<div class="ad-tit">
    	<span class="closebox" onclick="closeIframe()">关闭</span>
    	<h6>添加广告资源</h6>
    </div>
    <div class="tang-tab ad-bod">
        <ul class="tang-title">
            <li class="tang-title-item tang-title-item-selected"><a href="#" onclick="return false"><span>第一步:广告设置</span></a></li>
            <li class="tang-title-item"><a href="#" onclick="return false"><span>第二步:投放策略</span></a></li>
            <li class="tang-title-item"><a href="#" onclick="return false"><span>第三步:上传素材</span></a></li>
        </ul>
        <form action = "">
            <div class="tab-box">
                <table class="one-table pb_35" border="0"  cellpadding="8">
                    <tbody>
                        <tr>
                          <th><label for="shunxu">广告名称:</label></th>
                            <td class="td-xk" ><span  class="notion" style="margin-left:-8px;"><font color="#FF0000">*</font><input type="text" id="name" name="name" class="txt1 txt5" value="<?php echo $ad['name']; ?>" /></span> </td>
                          <th><label for="shunxu">所属订单:</label></th>
                            <td><?php echo CHtml::dropDownList('order_id', @$ad['order_id'], $orderName, array('class' => 'text2 text21', 'id' => 'order_id')); ?>
                            <!--<a class="toolTips_tag" onmouseover="showMyToolTips(this, '什么是订单？', '客户订购广告的约定')" onmouseout="hideMyToolTips()" href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a>-->
                            </td>
                        </tr>
                        <tr>
                          <th><label for="adexpl">说明:</label></th>
                            <td><textarea id="description" name="description" class="txt1 txt6"><?php echo $ad['description']; ?></textarea></td>
                            <td> <input type="hidden" name="position_id" id="position_id" value=<?php echo $position_id  ?> /></td>
                        </tr>
                    </tbody>
                </table>
                <div class="fgx"></div>
                <table class="one-table pt_35 pb_20 bp-tab" border="0"  cellpadding="8">
                    <tbody>
                        <tr>
                          <th>广告状态:</th>
                          <td class="radio-box" style = "font:12px/28px sans-serif; color:#fff">
                            <?php foreach($playerCushion as $key=>$one):?>
                            <input type="radio" class="ml15" name="playerCushion" id="cushion_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(intval($key)==$ad['cushion'])echo 'checked="checked"'; ?> onclick="setShowAttr()" />&nbsp;<label for="cushion_<?php echo $key; ?>"><?php echo $one; ?></label>
                            <?php endforeach; ?>
                          </td>
                        </tr>
                        <tr class="feeAll mt15 cut-in-attr <?php if($ad['cushion']!=4) echo 'hide';?>" style = "font-family: "微软雅黑">
                            <th>展现方式:</th>
                            <td style = "font:12px/28px sans-serif;">
                            <?php foreach($showType as $key=>$one):?>
                            <input type="radio" class="ml15" name="show_type" id="show_type_<?php echo $key; ?>" value="<?php echo $key; ?>" <?php if(intval($key)==$ad['show_type'])echo 'checked="checked"'; ?> />&nbsp;<label for="show_type_<?php echo $key; ?>"><?php echo $one; ?></label>
                            <?php endforeach; ?>
                          </td>
                        </tr>
                        <tr class="feeAll mt15 cut-in-attr <?php if($ad['cushion']!=4) echo 'hide';?>">
                          <th>展现时间:</th>
                          <td>
                          <input type="text" class="select_box ml15" name="show_time" id="show_time" value="<?php echo $ad['show_time']; ?>" /> <font size="-1">(以秒为单位)</font>
                          </td>
                        </tr>
                        <tr>
                          <th><label for="adname">广告尺寸:</label></th>
                          <td class="input-box">
                            <label>宽度:<input name="width" id="width" type="text" class="txt1 txt1-3" value="<?php  if(isset($ad['width'])) echo $ad['width'];else echo "0";  ?>" /></label>
                            <label>高度:<input name="height" id="height" value="<?php if(isset($ad['height'])) echo $ad['height'];else echo "0";  ?>" type="text" class="txt1 txt1-3" /></label>
                          </td>
                        </tr>
                        <tr>
                          <th><label for="adexpl">广告坐标:</label></th>
                          <td class="input-box">
                            <label>X坐标:<input name="pos_x" id="pos_x" type="text" value="<?php if(isset($ad['pos_x'])) echo $ad['pos_x'];else echo "0"; ?>" class="txt1 txt1-3" /></label>
                            <label>Y坐标:<input name="pos_y" id="pos_y" value="<?php if(isset($ad['pos_y'])) echo $ad['pos_y'];else echo "0"; ?>" type="text" class="txt1 txt1-3" /></label>
                          </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="ad_id" id="ad_id" value="<?php echo $ad['aid']; ?>" />
            </div>
            <div class="bott-f">
            	<!--<input type="submit" value="上一步" class="butt5 butt-prve" />-->
                <a href="javascript:void(0);" id="add_new_ad"> <input type="button" value="下一步" class="butt5 butt-next" /></a>
                <!--<input type="submit" value="完成" class="butt5 butt-comp" />
                <input type="button" value="确认" class="butt5 butt-conf" />-->
            </div>
        </form>
    </div>    
</div>
<iframe id="frameC" style="height:1px;width:1px;display:none;"></iframe>  
<script type="text/javascript">
function setShowAttr(){
        var cushion = $("input[name='playerCushion']:checked").val();
        if (cushion == 4) {
            $(".cut-in-attr").removeClass("hide");
        }else{
            $(".cut-in-attr").addClass("hide");
        }
    }
$(function(){
    
    // 订单
    dialog_ajax_ko({"list":$("#add"),"width":630,"height":610});
    
    var $ul=$(".subNav").children("ul");
    $ul.children("li").click(function(){
        $(this).addClass("act").siblings().removeClass("act");
    })
    
    $("#add_new_ad").click(function(){
        var cushion = $("input:radio[name='playerCushion']").length>0? $("input:radio[name='playerCushion']:checked").val() : 0;
        var show_type = $("input:radio[name='show_type']").length>0? $("input:radio[name='show_type']:checked").val() : 0;
        var show_time = $("#show_time").length>0? $("#show_time").val() : 0;
        var name = $("#name").val();
        var radio = $("#radio").val();
        var width = $("#width").val();
        var height = $("#height").val();
        var pos_x = $("#pos_x").val();
        var pos_y = $("#pos_y").val();
        var order_id = $("#order_id").val();
        var description = $("#description").val();
        var position_id = $("#position_id").val();
        var aid = $("#ad_id").val();
        if ($("#name").val()=="") {
            jAlert("请填写广告名称!", "提示");
            return false;
        }
        $.post(
            '<?php echo Yii::app()->createUrl("sobey/ad/setAd")?>',
            {'do':'save','aid':aid,'cushion':cushion,'show_time':show_time,'show_type':show_type,'width':width,'height':height,'pos_x':pos_x,'pos_y':pos_y,'name':name,'order_id':order_id,'description':description,'position_id':position_id}, 
            function(data){
                if(data.code < 1){
                    jAlert(data.message);
                }else{
                    var url = "<?php echo Yii::app()->createURL('sobey/ad/setPolicy?aid=');?>"+data.code+'&adPosition='+data.adPosition;
                    window.location.href=url;
                }
            },
            'json'
        );
    })
})

// 搜索广告位
function user_search(){
    var search_status = $('#search_status option:selected').val();
    var search_type = $('#search_type option:selected').val();
    var search_size = $('#search_size option:selected').val();
    var search_name = $.trim($('#search_name').val());
    var url = '<?php echo Yii::app()->createUrl('ad/getAdPositionList')?>?siteGroupId=<?php echo @$_GET["siteGroupId"];?>&siteId=<?php echo @$_GET["siteId"];?>&status='+search_status+'&type='+search_type+'&size='+search_size+'&name='+encodeURIComponent(search_name);
    if(typeof(ajax_load) == 'function'){
        ajax_load('ggw_box', url);
    }else
        window.location = url;
    return false;
}

// 选择广告位动作
function selectPosition(obj){
    var pid = $(obj).val();
    var pname = $("#pname_"+pid).html();
    var ptype = $("#ptype_"+pid).html();
    //var site = $("#site_"+pid).html();
    var psize = $("#psize_"+pid).html();
    var html = "<b>"+pname+"</b><b>"+ptype+"</b><b>"+psize+"</b>";
    html += '<a href="javascript:void(0);" onclick="modifyPosition(this);">修 改</a>';
    $("#select_position_info").html(html);
    $("#position_id").val(pid);
    $("#ggw_box").hide();
}

//修改广告位
function modifyPosition(obj){
    if ($(obj).html()=="修 改") {
        $(obj).html("取消修改");
        $("#ggw_box").show();
    } else {
        $(obj).html("修 改");
        $("#ggw_box").hide();
    }
}
 function closeIframe(){
    var iframeTag = document.getElementById('frameC');
    iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
    iframeTag.src = iframeSrc;
  }
</script>
</body>
</html>
