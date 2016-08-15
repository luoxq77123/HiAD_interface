<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <title>HiAD管理后台</title>
        <script src="<?php echo $this->module->assetsUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/main.css"/>
        <script src="<?php echo $this->module->assetsUrl; ?>/js/main.js" type="text/javascript"></script>
        <link href="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo $this->module->assetsUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
        <script src="<?php echo $this->module->assetsUrl; ?>/js/helper.js" type="text/javascript"></script>
        <link href="<?php echo $this->module->assetsUrl; ?>/js/jQueryAlert/jquery.alerts.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $this->module->assetsUrl; ?>/css/manhua_hoverTips.css" rel="stylesheet" type="text/css" />
        <script src="../js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
    </head>
    <body  style="background:#eee; width:100%;">
<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="javascript:void(0);">投放</a> &gt; <a href="javascript:void(0);">站点广告</a></div>
</div>
<!--end 面包屑-->
<div class="taskbar">
  <!-- class="line line3"> <a href="#" class="mgl38"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn1.gif"></a> </div-->
    <!--按钮-->
    <div class="tpboder pl_20 adbox">
        <div class="lxr_sx"><a href="javascript:void(0);" title="新建广告" id="add_new_ad" class="iscbut cbut_jia"><span>新建广告</span></a></div>
    </div>
    <!--end 按钮-->
    <div class="tpboder pl_30 adbox">
        <form onSubmit="return submit_search();" method="get" class="list_search_form">
            <div class="fl shaixuan">
                <label>状态：
                    <?php echo CHtml::dropDownList('search_status', @$_GET['status'], $status, array('class' => 'txt1', 'id' => 'search_status')); ?>
                </label>
            </div>
            <div class="fr sz_sc"><span>广告名称:&nbsp;</span><input type="text" name="key_word" id="key_word" class="txt1" value="<?php echo @urldecode($_GET['name']); ?>"/>&nbsp;<input type="button" class="iscbut_4" value="搜索" onClick="submit_search()" /></div>
        </form>
    </div>

    <div class="line4" id="banner_message" style="display:none;">
        <div class="line41 fr">
            <a href="javascript:void(0);" class="close_message"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/deltit.gif" /></a>
        </div>
        <div class="message_area"></div>
    </div>
    <div class="tpboder pl_30 adbox">
        <div class="butgn nobutgn" id="butgn">
            <input type="button" id="z_start" value="启用">
            <input type="button" id="z_stop" value="下线" />
            <input type="button" id="z_dele" value="删除" />
            <!--<span><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tit1.gif" /></span>-->
            <!--button  disabled="disabled" class="button3">批量修改<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tit2.gif" /></button-->
        </div>
    </div>
</div>
<div class="tpboder adbox">
    <table border="0" cellspacing="0" cellpadding="0" class="list_table" id="list_table" style="border-collapse:collapse">
        <tbody>
            <tr>
                <th class="tpboder tx_c" width="5%"><input type="checkbox" id="checkbox_on"/></th>
                <th class="tpboder">广告名称</th>
                <th class="tpboder">状态</th>
                <th class="tpboder">广告位</th>
                <th class="tpboder">起始时间</th>
                <th class="tpboder">结束时间</th>
                <th class="tpboder">订单</th>
                <th class="tpboder" width="15%">操作</th>
            </tr>
                   <?php if($adlist):?>
            <?php foreach ($adlist as $key => $one): ?>
                <tr>
                    <td><input type="checkbox" name="aids[]" id="checkbox_<?php echo $one->id; ?>" value="<?php echo $one->id; ?>"/></td>
                    <td><?php echo $one->name; ?></td>
                    <td id="ad_status_<?php echo $one->id; ?>"><?php if($one->status==1){if($one->ads_start_time>time()){echo $status[12];}else if($one->ads_end_time==0||$one->ads_end_time>time()){echo $status[13];}else{echo $status[14];}}else{echo $status[$one->status];} ?></td>
                    <td><?php echo $one->position_name; ?></td>
                    <td><?php echo date("Y-m-d H:i", $one->ads_start_time); ?></td>
                    <td><?php if ($one->ads_end_time > 0) echo date("Y-m-d H:i", $one->ads_end_time);else echo '不限'; ?></td>
                    <td><?php echo $one->order_name; ?></td>
                    <td>
                        <div class="option">
                            <a href="javascript:void(0);" onClick="modifyAd('<?php echo $one->id; ?>')">修改</a> | 
                            <a href="<?php echo Yii::app()->createUrl('sobey/material/list', array('aid' => $one->id, 'ad_name' => urlencode($one->name)));?>" title="查看广告物料" class="load_frame">广告物料</a> | 
                            <a href="javascript:void(0);" onClick="viewStatistics('<?php echo $one->id; ?>', '<?php echo $one->name; ?>')" title="查看广告统计">报告</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
          <?php else:?>
            <tr>
                <td></td>
               <td colspan="7"><span>没有查到相关的内容！</span></td>
            </tr>
          <?php endif;?>
        </tbody>
    </table>
</div>

<div class="pl_30 adbox">
    <?php
    $this->widget('HmLinkPager', array(
        'header' => '',
        'firstPageLabel' => '首页',
        'lastPageLabel' => '末页',
        'prevPageLabel' => '<上一页',
        'nextPageLabel' => '下一页>',
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
<script type="text/javascript">
    $(document).ready(function(){
<?php if (isset($_GET['positionid']) && $_GET['positionid']): ?>
            banner_message('广告位&nbsp;“<span><?php echo urldecode($_GET['position_name']) ?></span>”&nbsp;上的广告');
<?php endif; ?>
    });

    $(function(){
        var $ul=$(".subNav").children("ul");
        $ul.children("li").click(function(){
            $(this).addClass("act").siblings().removeClass("act");
        })
    })
    $(function(){
        $("#list_table").find("tr:odd").addClass("trBg");

        $trs=$("#list_table").find("tr");
        var len=$trs.length;
        for(var i=0; i<len; i++){
            $thisTr=$trs.eq(i);
            $trA=$thisTr.children("td:first").children("input");
            $trB=$thisTr.children("th:first").children("input");
            $trA.click(function(){
                $(this).toggleClass("cboxOn");
                disabledBtn()
            })

            $trB.click(function(){
                $(this).toggleClass("cboxOn");
                if($(this).hasClass("cboxOn")){
                    for(var i=0; i<len; i++){
                        $thisTr=$trs.eq(i);
                        $trA=$thisTr.children("td:first").children("input");
                        $trA.addClass("cboxOn").attr("checked","checked");
                    }
                }else{
                    for(var i=0; i<len; i++){
                        $thisTr=$trs.eq(i);
                        $trA=$thisTr.children("td:first").children("input");
                        $trA.removeClass("cboxOn").removeAttr("checked");
                    }
                }
                disabledBtn();
            })
        }

        function disabledBtn(){
            if( $("#list_table").find(".cboxOn").length>0){
                $("#butgn").removeClass("nobutgn");
            }else{
                $("#butgn").addClass("nobutgn");
            }
        }
        // 启用
        $("#z_start").click(function(){
            var aids = "";
            var arrAid = new Array();
            var index = 0;
            $("#list_table").find(".cboxOn").each(function(){
                if(parseInt($(this).val())==$(this).val()){
                    aids += (aids=="")? $(this).val() : ","+$(this).val();
                    arrAid[index++] = $(this).val();
                }
            });
            if (aids=="") 
                return false;
            $.post(
            '<?php echo Yii::app()->createUrl("ad/setStartAd") ?>',
            {'aids':aids}, 
            function(data){
                if(data.code < 1){
                    jAlert(data.msg);
                }else{
                    jAlert('启用广告已成功！', '提示');
                    var localUrl = window.location.href;
                    var arrUrl = localUrl.split("?");
                    var url = (arrUrl.length>1)? '<?php echo Yii::app()->createUrl("ad/list") ?>?'+arrUrl[1] : '<?php echo Yii::app()->createUrl("ad/list") ?>';
                    setTimeout('frame_load("'+url+'", true);', 1000);
                }
            },
            'json'
        );
        })
        // 暂停
        $("#z_stop").click(function(){
            var aids = "";
            var arrAid = new Array();
            var index = 0;
            $("#list_table").find(".cboxOn").each(function(){
                if(parseInt($(this).val())==$(this).val()){
                    aids += (aids=="")? $(this).val() : ","+$(this).val();
                    arrAid[index++] = $(this).val();
                }
            });
            if (aids=="") 
                return false;
            $.post(
                '<?php echo Yii::app()->createUrl("ad/setStopAd") ?>',
                {'aids':aids}, 
                function(data){
                   //  alert(data);
                    if(data.code < 1){
                        jAlert(data.msg);
                    }else{
                        jAlert('广告下线已成功！', '提示');
                        var localUrl = window.location.href;
                        var arrUrl = localUrl.split("?");
                        var url = (arrUrl.length>1)? '<?php echo Yii::app()->createUrl("ad/list") ?>?'+arrUrl[1] : '<?php echo Yii::app()->createUrl("ad/list") ?>';
                        setTimeout('frame_load("'+url+'", true);', 1000);
                    }
                },
                'json'
            );
        })
        // 删除
        $("#z_dele").click(function(){
            var aids = "";
            var arrAid = new Array();
            var index = 0;
            $("#list_table").find(".cboxOn").each(function(){
                if(parseInt($(this).val())==$(this).val()){
                    aids += (aids=="")? $(this).val() : ","+$(this).val();
                    arrAid[index++] = $(this).val();
                }
            });
            if (aids=="") 
                return false;
            banner_message('后台处理中，请稍后');
            $.post(
                '<?php echo Yii::app()->createUrl("ad/del") ?>',
                {'aids':aids}, 
                function(data){
                    if(data.code < 1){
                        jAlert(data.message);
                    }else{
                        jAlert(data.message, '提示');
                        var localUrl = window.location.href;
                        var arrUrl = localUrl.split("?");
                        var url = (arrUrl.length>1)? '<?php echo Yii::app()->createUrl("ad/list") ?>?'+arrUrl[1] : '<?php echo Yii::app()->createUrl("ad/list") ?>';
                        setTimeout('frame_load("'+url+'", true);', 1000);
                    }
                },
                'json'
            );
        });

        $("#add_new_ad").click(function(){
            var positionId = '<?php echo (isset($_GET['positionid'])&&$_GET['positionid']>0)? $_GET['positionid'] : 0;?>';
            var url = "<?php echo Yii::app()->createURL('sobey/setAd?do=create'); ?>";
            if (positionId>0) {
                url += "&positionId="+positionId;
            }
            frame_load(url);
            return false;
        })
    })

    function submit_search(){
        var search_status = $('#search_status option:selected').val();
        var search_name = $.trim($('#key_word').val());
        <?php $get = $_GET; unset($get['status']); unset($get['name']);?>
        var url = '<?php echo Yii::app()->createUrl('ad/list', $get) ?>&status='+search_status+'&name='+encodeURIComponent(search_name);
        if(typeof(ajax_load) == 'function')
            frame_load(url);
        else
            window.location = url;
        return false;
    }

    function modifyAd(aid){
        var url = "<?php echo Yii::app()->createURL('ad/setAd?do=modify&aid='); ?>"+aid;
        frame_load(url);
    }
    
    function viewStatistics(aid, adName) {
        var url = "<?php echo Yii::app()->createURL('statistics/site?ad_id='); ?>"+aid+"&ad_name="+encodeURIComponent(adName);
        frame_load(url);
        return false;
    }
</script>
</body>
</html>