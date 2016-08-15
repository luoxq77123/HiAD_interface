<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>添加广告资源-第三步-上传素材</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/main.css"/>
    <link href="<?php echo $this->module->assetsUrl; ?>/css/adctrlstyle.css" rel="stylesheet" />
    <link href="<?php echo $this->module->assetsUrl; ?>/js/jQueryAlert/jquery.alerts.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $this->module->assetsUrl; ?>/css/manhua_hoverTips.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $this->module->assetsUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/main.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/helper.js" type="text/javascript"></script>
    <script src="<?php echo $this->module->assetsUrl; ?>/js/datepicker/jquery.ui.datepicker.my.js" type="text/javascript"></script> 
    <style>
        body {background-color: #FFF;}
        .w7Ttit2 input{width:54px;}
    </style>
</head>
<body>
<div class="body">
    <div class="ad-tit">
        <span class="closebox" onclick="closeIframe()">关闭</span>
        <h6>添加广告资源</h6>
    </div>    
    <div class="tang-tab ad-bod">
            <ul class="tang-title">
                <li class="tang-title-item"><a href="#" onclick="return false"><span>第一步:广告设置</span></a></li>
                <li class="tang-title-item"><a href="#" onclick="return false"><span>第二步:投放策略</span></a></li>
                <li class="tang-title-item tang-title-item-selected"><a href="#" onclick="return false"><span>第三步:上传素材</span></a></li>
            </ul>
        <div class="tab-box">
            <input type="hidden" name="ad_id" id="ad_id" value="<?php echo $material['aid']; ?>" />
                <table border="0" cellpadding="0" cellspacing="10" class=" pt_15 one-table guwulh">
                    <tbody>
                        <tr>
                            <td width="90" height="34" align="left" style="margin-right:85px;"><strong>广告物料轮换</strong></td>
                            <td class="radio-box">
                               <?php echo CHtml::dropDownList('rotate_mode', @$material['mrotate_mode'], $rotateList, array('class' => 'text2 txt22 txt221', 'id' => 'rotate_mode', 'onchange'=>'setRotateMode()')); ?>
                               <a style="margin-left: -65px; margin-top: -4px;" class="toolTips_tag" onmouseover="showMyToolTips(this, '广告物料轮换方式的有几种？', '均匀：每条广告物料获得均等的展现概率。<br/>手动权重：手工设置广告物料的权重，高权重的广告物料展现概率更高。<br/>幻灯片轮换：以幻灯片播放的方式在一次浏览中依次展现全部广告物料，只支持文字和图片广告物料。')" onmouseout="hideMyToolTips()"  href="javascript:void(0);"><img src="<?php echo $this->module->assetsUrl; ?>/images/tit4.gif" /></a>
                                 <span class="txt22 hide" id="rotate_time_box" >轮换时间间隔 <input type="text" name="rotate_time" id="rotate_time" class="text2" style="width:50px;background:#red;border:1px solid #ccc" value="<?php echo $material['mrotate_time']; ?>" /> 秒</span>
                            </td>
                        </tr>
                        <tr style="vertical-align:top;">
                            <td align="left"><strong>广告物料</strong></td>
                            <td align="left">
                            	<div class="wuliao_box">
                                    <ul id="material_box">
                                      <?php echo $material['material']; ?>
                                    </ul>
                                </div>
                            	<div><a href="" class="butt6">从广告物料库选择</a><a href="javascript:void(0);" id="create_material" class="butt7">新建广告物料</a></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>
            <div class="clear"></div>
            <div>
                <div class="w4Table">
                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr  onmouseout="this.style.backgroundColor='' " onmouseover="this.style.backgroundColor='#fff'" >
                                <td id="ggw_box" onmouseout="this.style.backgroundColor='' " onmouseover="this.style.backgroundColor='#fff'">
                                 <?php $this->widget('MaterialListWidget', array('arrPageSize' => array(3 => 3, 10 => 10, 20 => 20))); ?>
                                 </td>
                            </tr>
                        </tbody>
                    </table>                 
                </div>
                <label for="textarea"></label>
                <input id="xinjian" value="<?php echo  $material['adPosition']; ?>" type="hidden" />
                <textarea class="xinjian" name="xinjian" id="xinjians" value="<?php echo  $material['adPosition']; ?>" cols="45" rows="5" style="display:none"></textarea>
                <label for="textarea"></label>
                <div class="clear"></div>
            <!--end 物料盒子--></div>
            
            <div class="bott-f">
            	<a href="javascript:void(0);" id="step_prev"><input type="submit" value="上一步" class="butt5 butt-prve" /></a>
                <a href="javascript:void(0);" id="step_next"><input type="button" value="确认" class="butt5 butt-conf" /></a>
            </div>
    </div>    
</div>
<iframe id="frameC" style="height:1px;width:1px;display:none;"></iframe> 
<script language="javascript">
    $(document).ready(function(e) {
        //$.ajaxSetup({cache:false}) 
        $('form.list_search_form select').live('change', function(){
            $('form.list_search_form').submit();
        });
      
    });
</script>
<script type="text/javascript">

$(document).ready(function(e) {
    //dialog_ajax_ko({"list":$("#create_material"),"width":630,"height":610});
    $("#create_material").click(function(){
        var aid = $("#ad_id").val();
        var url = "<?php echo $this->createUrl('material/add');?>?backUrl=<?php  echo $this->createUrl('ad/setMaterial');?>&adPosition=<?php echo  $material['adPosition']; ?>";
        window.location.href=url;
    });
    $("#step_prev").click(function(){
        var url = "<?php echo Yii::app()->createURL('sobey/ad/setPolicy');?>?adPosition=<?php echo  $material['adPosition']; ?>";
        window.location.href=url;
    });
    $("#step_next").click(function(){
        var aid = $("#ad_id").val();
        var rotate = $("#rotate_mode option:selected").val();
        var rotateTime =  0;
        var material = "";
        if (parseInt(aid)!=aid || aid<1){
            alert("参数错误，请重新编辑");
            return false;
        }
        if (rotate=='3') {
            if ($("#rotate_time").val()!=parseInt($("#rotate_time").val())){
                alert("轮换时间间隔必须是整数");
                return false;
            } else {
                rotateTime = $("#rotate_time").val();
            }
        }
        $("#material_box li").each(function(i){
            var mid = $(this).attr("id");
            var mweights = $("#mweights_"+mid).val();
            if (material=="") {
                material = mid+"||"+mweights;
            } else {
                material += "=="+mid+"||"+mweights;
            }
        });
        if (material==""){
            alert("请至少选择一个物料");
            return false;
        }
        
        $.post(
            '<?php echo Yii::app()->createUrl("sobey/ad/setMaterial")?>?adPosition=<?php echo  $material['adPosition']; ?>',
            {'do':'save','aid':aid,'rotate':rotate,'rotateTime':rotateTime,'material':material}, 
            function(data){
                if(data.code < 1){
                    jAlert(data.msg);
                }else{
                    var iframeTag = document.getElementById('frameC');
                    iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
                    iframeTag.src = iframeSrc;
                    /*var url = "<?php echo Yii::app()->createURL('sobey/ad/list');?>";
                    window.location.href=url;*/
                }
            },
            'json'
        );
    });
});

function opAllCheckbox(){
    if ($("#all_checkbox").attr("checked")){
        $("input[name='material[]']").each(function(){
            $(this).attr("checked", "checked");
        });
    } else {
        $("input[name='material[]']").each(function(){
            $(this).attr("checked", false);
        });
    }
}

function setRotateMode(){
    var select = $("#rotate_mode option:selected").val();
    if(select == '1') {
        $("#rotate_time_box").hide();
        $("#material_box li").each(function(i){
            var mid = $(this).attr("id");
            $("#mweights_"+mid).parent().hide();
        });
    } else if (select == '2') {
        $("#rotate_time_box").hide();
        $("#material_box li").each(function(i){
            var mid = $(this).attr("id");
            $("#mweights_"+mid).parent().show();
        });
    } else if (select == '3') {
        $("#rotate_time_box").show();
        $("#material_box li").each(function(i){
            var mid = $(this).attr("id");
            $("#mweights_"+mid).parent().hide();
        });
    }
}

function selectMaterial(){
    if($("#ggw_box").is(":hidden")) {
        $("#ggw_box").show();
    } else {
        $("#ggw_box").hide();
    }
}

// 搜索广告位
function user_search(){
    var search_status = $('#search_status option:selected').val();
    var search_type = $('#search_type option:selected').val();
    var search_size = $('#search_size option:selected').val();
    var search_name = $.trim($('#search_name').val());
    var url = '<?php echo Yii::app()->createUrl('sobey/ad/getMaterialList')?>?status='+search_status+'&type='+search_type+'&size='+search_size+'&name='+encodeURIComponent(search_name);
    if(typeof(ajax_load) == 'function') {
        ajax_load('ggw_box', url);
    } else 
        window.location = url;
    return false;
}

function removeCutData(id){
    if ($("#"+id).length>0){
        $("#"+id).remove();
    }
}

function completeMaterial(){
    var mids = "";
    var adposition = $("#xinjian").val();
    var arrSelectMids = new Array();
    // 检查是否重复提交
    $("#material_box li").each(function(i){
        var mid = $(this).attr("id");
        arrSelectMids[i] = mid;
    });
    $("input[name='material[]']").each(function(){
        if($(this).attr("checked")=="checked"){
            if (!in_array($(this).val(), arrSelectMids)) {
                mids += (mids=="")? $(this).val() : ","+$(this).val();
            }
        }
    });
    if (mids=="") {
        return false;
    }
    $.post(
        '<?php echo Yii::app()->createUrl("sobey/ad/getMaterialInfo")?>',
        {'mids':mids,'adPosition':adposition}, 
        function(data){
            if(data.code < 1){
                jAlert(data.msg);
            }else{
                $("#material_box").append(data.msg);
                var select = $("#rotate_mode option:selected").val();
                if (select == '2') {
                    $("#material_box li").each(function(i){
                        var mid = $(this).attr("id");
                        $("#mweights_"+mid).parent().show();
                    });
                }
            }
        },
        'json'
    );
    hideMaterial();
}

function hideMaterial(){
    $("#ggw_box").hide();
}

function in_array(needle, haystack){
    for(var i in haystack){
        if (haystack[i]==needle){
            return true;
        }
    }
    return false;
}

function material_status(status, uid){
    var ids = new Array();
    var adposition = $("#xinjian").val();
    banner_message('后台处理中，请稍后');
    $.post(
    '<?php echo Yii::app()->createUrl('sobey/Material/Status'); ?>',
    {'ids[]':uid, status:status}, 
    function(data){
        if(data.code < 0){
            banner_message(data.message);
            } else{
            jAlert(data.message, '提示');
            setTimeout(url, 1000);
            }
        },
    'json'
    );
}
function url(){
   var adposition = $("#xinjian").val();
   var url = '<?php  echo Yii::app()->createUrl('sobey/ad/setMaterial'); ?>?adPosition='+adposition;
   window.location.href=url; 
}

 function closeIframe(){
    var iframeTag = document.getElementById('frameC');
    iframeSrc = 'http://admin.sobeycloud.com/AdService/closeDialog.jsp';
    iframeTag.src = iframeSrc;
  }

function xinjian() {
    var adposition = $("#xinjian").val();
    var url = '<?php  echo Yii::app()->createUrl('sobey/material/add'); ?>?adPosition='+adposition;
    window.location.href=url; 
}

function bianji(id) {
    var adposition = $("#xinjian").val();
    var id = id;
    var url = '<?php  echo Yii::app()->createUrl('sobey/material/edit'); ?>?adPosition='+adposition+'&id='+id;
    window.location.href=url; 
}
</script>
</body>
</html>
