<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <title>HiAD管理后台</title>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js" type="text/javascript"></script>
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/cupertino/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/helper.js" type="text/javascript"></script>
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/js/jQueryAlert/jquery.alerts.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/manhua_hoverTips.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
    </head>
    <body onLoad="ReSet()" onResize="ReSet()" style="background:#eee; width:100%;">
        <!--头部-->
        <div id="header_top">
            <!--LOGO-->
            <h1><a href="<?php echo $this->createUrl('backend/index'); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo.png"></a></h1>
            <!--end LOGO-->
            <!-- 右侧导航-->
            <div class="fr ">
                <ul class="top_nav">
                    <li class="nav_wel">
                        <a href="javascript:void(0);">欢迎你，<?php echo Yii::app()->session['user']['name']; ?>。</a>
                    </li>
                    <li class="nav_cp">
                        <a href="<?php echo Yii::app()->createUrl('personal/index'); ?>" class="load_frame" >个人设置</a>
                    </li>
                    <li class="nav_help">
                        <a href="<?php echo Yii::app()->createUrl('help/oprocess'); ?>" class="load_frame" >帮助中心</a>
                    </li>
                    <li class="nav_quit">
                        <a href="javascript:void(0)" onclick="logout()">退出</a>
                    </li>
                </ul>    	
            </div>
            <!--右侧导航-->
        </div>
        <!--end 头部-->

        <?php $this->widget('UserMenuWidget'); ?> 

        <!--内容部分-->
        <!--内容显示区-->
        <div class="content">
            <div id="frame_container">
                <?php echo $content; ?>
            </div>
            <!-- end .content -->
        </div>
        <!--end 内容显示区-->
        <!--end 内容部分-->
        <div class="clearfloat"></div>

        <script language="javascript">
            function ReSet(){
                var winHeight,winWidth;
                //获取窗口宽度
                if(window.innerWidth){
                    winWidth=window.innerWidth;
                }else if((document.body)&&(document.body.clientWidth)){
                    winWidth=document.body.clientWidth;
                }
                //获取窗口高度
                if(window.innerHeight){
                    winHeight=window.innerHeight;}
                else if((document.body)&&(document.body.clientHeight)){
                    winHeight = document.documentElement.clientHeight;
                    winHeight = $(window).height();
                }
                winWidth = $(window).width();
                $(".lr_box").css("min-height",winHeight-185);
                $(".lr_box>.left").css("min-height",winHeight-185);
                $(".lr_box>.right").css("min-height",winHeight-185);
                $("#weektable").css("width",winWidth-282-196-28);
            }

            $(document).ready(function(e) {
                $.ajaxSetup({cache:false}) 
                ReSet(); 
                _ntabqh("vnavtab","li","vnavtabbod","ul","onclick");//绑定导航tab切换	
                // 设置默认导航栏
                $("#Setting").parent().trigger("click");
                var lastLi=null; 
                var timeOutVar=null; 
                $("#vnavtab li").mousemove(function(){ //子菜单的背景
                    $("#vnavtab li").removeClass("show_now");
                    $(this).addClass("show_now");
                    if(timeOutVar !=null)clearTimeout(timeOutVar);
                    $("#vnavtabbod>ul").css("display","none");
                    this.bod.style.display = "block";
                    timeOutVar == null;
                });
                $("#vnavtab li").mouseout(function(){ //子菜单的背景
                    if(timeOutVar!=null)clearTimeout(timeOutVar);
                    lastLi = function(){
                        $("#vnavtabbod>ul").css("display","none");
                        $("#vnavtab li").removeClass("show_now");
                        for(var i = 0 ; i < $("#vnavtab li").length ; i ++){
                            if($($("#vnavtab li")[i]).hasClass("now")){
                                $($("#vnavtab li")[i].bod).css("display","block");
                                break ;
                            }
                        }
                    }
                    timeOutVar = setTimeout(lastLi,500); 
                });
                $("#vnavtabbod").mousemove(function(){
                    if(timeOutVar!=null)clearTimeout(timeOutVar);
                });
                $("#vnavtabbod").mouseout(function(){
                    if(timeOutVar!=null)clearTimeout(timeOutVar);
                    if(lastLi != null){
                        timeOutVar = setTimeout(lastLi,500);
                    }
                });
                $(".subnav li").mousemove(function(){ //子菜单的背景
                    $(this).addClass("show_now");
                });
                $(".subnav li").mouseout(function(){
                    $(this).removeClass("show_now");
                });
    
                $("#vnavtabbod").find("a").click(function(){
                    $("#vnavtabbod").find("a").removeClass("now");
                    $(this).addClass("now");
                    $($("#vnavtab li")[$(this).parents("ul").index()]).click();
                    frame_load($(this).attr("href"));

                    return false;
                });
                
                $('form.list_search_form select').live('change', function(){
                    $('form.list_search_form').submit();
                });
    
                $("#banner_message .close_message").live('click', function(){
                    $('#banner_message').slideUp();
                    // 回调函数，用于处理提示信息关闭后的事务
                    deal_after_bmclose();
                });
    
                $('a.load_frame').live('click', function(){
                    var area = $(this).attr('area');
                    if(area){
                        ajax_load(area, $(this).attr('href'));
                    }else{
                        frame_load($(this).attr('href'));
                    }
                    return false;
                });

                $.history.init(function(hash){
                    load_hash_url(hash);
                });
            });

            function load_hash_url(hash){
                if(hash != ''){
                    $('#vnavtabbod a').each(function(){
                        _index = hash.lastIndexOf("?");
                        hash_url = _index > 0 ? hash.substring(0, _index) : hash;
  
                        if($(this).attr("href") == hash_url){
                            $('#vnavtab ul li').removeClass('now');
                            $('#vnavtabbod a').removeClass('now');
                            $(this).addClass("now");
                            $($("#vnavtab li")[$(this).parents("ul").index()]).click();
                            return;
                        }
                    });
                    frame_load(encodeURI(hash), true);
                }
            }

            function banner_message(message){
                $("#banner_message .message_area").html(message);
                $("#banner_message").slideDown();
            }
            
            function deal_after_bmclose(){}

            function logout(){
                jConfirm("你确定要退出吗？", "提示", function(e){
                    if(e){
                        window.location.href="<?php echo Yii::app()->createUrl('user/logout'); ?>";
                    }
                });
            }
        </script>
    </body>
</html>
