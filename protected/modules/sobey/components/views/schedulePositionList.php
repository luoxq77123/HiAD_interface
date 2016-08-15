<div class="titBar">
    <div class="searchT">选择广告位：</div>
    <div class="search">
        <form method="get" onsubmit="return position_search();">
            <?php echo CHtml::textField('search_name', @$_GET['name']?@$_GET['name']:'请输入广告位名称', array('class' => 'txt1 txt3 fl', 'id' => 'search_name','onclick'=>'text_n()')); ?>
            <a href="javascript:void(0);" onclick="position_search();" class="fr"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/btn5.gif" /></a>
        </form>
    </div>
</div>
<script type="text/javascript">
   // var positions = <?php echo json_encode($positions);?>;
   function text_n(){
       var search_name = $.trim($('#search_name').val());
        if(search_name == '请输入广告位名称'){
            $('#search_name').val('');
        }
   }
</script>
<div class="maincon">
    <table border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr class="title">
                <th class="w1">广告位名称</th>
                <th class="w2">广告位类型</th>
                <th class="w3">尺寸</th>
                <th class="w4">类型</th>
                <th class="w5"><a href="#" id="alladd">全部添加>></a></th>
            </tr>
        </thead>
        <tbody id="maincon1">
                   <?php if($positions):?>
		<?php foreach($positions as $one):?>
           <tr class="biaoji_<?php echo $one['id'];?>">
                <td class="w1"><?php echo $one['name'];?></td>
                <td class="w2"><?php if(isset($adType[$one['ad_type_id']]))echo $adType[$one['ad_type_id']];else echo '--';?></td>
                <td class="w3"><?php if($one['position_size'])echo $one['position_size'];else echo '--';?></td>
                <td class="w4"><?php if(isset($adShows[$one['ad_show_id']]))echo $adShows[$one['ad_show_id']];else echo '--';?></td>
                <td class="w5"><a href="javascript:void(0)">添加>></a></td>
            </tr>
          <?php endforeach;?>
          <?php else:?>
            <tr>
                <td></td>
               <td colspan="4"><span>没有查到相关的内容！</span></td>
            </tr>
          <?php endif;?>
<script type="text/javascript">
    $(document).ready(function() {
		var select=$("#maincon2").children('tr');
		var old=$("#maincon1").children('tr');
		if(select.length > 0){
			var s=new Array();
			for(var j=0;j<select.length;j++){
				var val=select.eq(j).attr('class');
				s.push(val.replace('biaoji_',''));
			}

			for(var j=0;j<old.length;j++){
				var val1=old.eq(j).attr('class');
				if(jQuery.inArray(val1.replace('biaoji_',''),s) > -1){
					old.eq(j).find("td").eq(4).html("<a href='javascript:void(0)' style='color:gray;'>已添加</a>");
				}
			}
		}
    });
</script>
            <!--<tr>
                <td class="w1">记录片左1</td>
                <td class="w2">山东卫视-新闻</td>
                <td class="w3">123 * 23</td>
                <td class="w4">固定</td>
                <td class="w5"><a href="#">添加>></a></td>
            </tr>
            <tr>
                <td class="w1">记录片左1</td>
                <td class="w2">山东卫视-新闻</td>
                <td class="w3">123 * 23</td>
                <td class="w4">固定</td>
                <td class="w5"><a href="#">添加>></a></td>
            </tr>-->
        </tbody>
    </table>
</div>
<div class="tableFooter tableFooter1">
    <div class="in">
        <?php
        $this->widget('HmLinkPager',array(
            'header'=>'',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '末页',
            'prevPageLabel' => '上一页',
            'nextPageLabel' => '下一页',
            'refreshArea' => 'position_list_area',
            'pages' => $pages,
            'selectedPageCssClass' => 'current',
            'maxButtonCount' => 6,
            'htmlOptions' => array('class' => 'msgL fl', 'id' => 'pagination')
            )
        );
        ?>
        <?php
        $item_from = $pages->getItemCount() > 0 ? $pages->getOffset()+1 : 0;
        $item_end = $pages->getOffset() + $pages->getLimit() > $pages->getItemCount() ? 
                $pages->getItemCount() : $pages->getOffset() + $pages->getLimit();
        ?>
        <div class="msgR fr"><?php echo $item_from;?>-<?php echo $item_end;?>条(共<?php echo $pages->getItemCount();?>条)</div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        main_start();
        $("#alldelete").click(function(){//全部删除绑定
            $("#maincon2 .w5 a").click();//运行盒子下全部删除动作
            return false;
        });
        $("#alladd").click(function(){//全部添加绑定
            $("#maincon1 .w5 a").click();//运行盒子下全部添加动作
            return false;
        });
    });
    function main_start(){//画布绘制
       /*
		$("#maincon1 tr").removeClass("lightBg"); //先清空
        $("#maincon1  tr:even").addClass("lightBg"); //变色
        $("#maincon2  tr").removeClass("lightBg"); //先清空
        $("#maincon2  tr:even").addClass("lightBg"); //变色
		*/
        $("#maincon1 .w5 a").unbind("click").click(function(){//绑定添加按钮点击事件
            main_add_del($(this).parents("td"),"add");
            return false;
        });
      /*  $("#maincon1 tr").unbind("dblclick").dblclick(function(){//绑定添加双击点击事件
            $(this).find(".w5 > a").click();
        }).attr("title","双击添加");//添加提示信息*/
        $("#maincon2 .w5 a").unbind("click").click(function(){//绑定删除按钮点击事件
            main_add_del($(this).parents("td"),"delete");
            return false;
        });
        $("#maincon2 tr").unbind("dblclick").dblclick(function(){//绑定删除双击点击事件
            $(this).find(".w5 > a").click();
        }).attr("title","双击删除");//添加提示信息
    }
    function main_add_del(t,sj){ 
        /*参数 t = tr 下的链接或按钮 jQuery 方法取得(必填)
              sj = add  delete  增加或删除(必填)
         */
        var boxid,v_title;
        if(sj == "add"){
			if($("#maincon2").children('tr').length > 19){
				$('#position_max').css({color: "red"});
				return false;
			}
			var clas=$(t).parents("tr").attr('class');
			if($('.'+clas).length < 2){
				boxid = "maincon2"; // 增加盒子的 id;
				v_title = "&lt;&lt;删除"; //需要替换的按钮内容
				
				//$(t).parents("tr").css("display","");//先隐藏,进行后续操作.
				$(t).parents("tr").find("td").eq(4).html("<a href='javascript:void(0)'>" + v_title + "</a>");//交换添加删除按钮文字
				var $trhtml = $(t).parents("tr").clone();//在原来盒子中移除元素并保存
				$trhtml.appendTo("#"+boxid);//显示盒子,并添加到新的盒子
				$(t).html("<a href='javascript:void(0)' style='color:gray;'>已添加</a>");
				$('#ggw_len').html('共'+$("#maincon2").children('tr').length+'条');
			
			}else{
				$(t).html("<a href='javascript:void(0)' style='color:gray;'>已添加</a>");
			}
			//$(t).unclick();
        }else if(sj == "delete"){
			if($("#maincon2").children('tr').length < 21){
				$('#position_max').css({color: "black"});
			}
            var boxid = "maincon1"; //删除后盒子容器id
            v_title = "添加&gt;&gt;"; //需要替换的按钮内容
						
			$(t).parents("tr").css("display","none");//先隐藏,进行后续操作.
			$(t).parents("tr").find("td").eq(4).html("<a href='javascript:void(0)'>" + v_title + "</a>");//交换添加删除按钮文字
			//alert($(t).parents("tr").attr('class'));
			var clas=$(t).parents("tr").attr('class');
			$(t).parents("tr").remove();//在原来盒子中移除元素并保存
			$('.'+clas).find("td").eq(4).html("<a href='javascript:void(0)'>添加&gt;&gt;</a>");
			$('#ggw_len').html('共'+$("#maincon2").children('tr').length+'条');
			//$trhtml.css("display","").appendTo("#"+boxid);//显示盒子,并添加到新的盒子
        }

		/*$(t).parents("tr").css("display","none");//先隐藏,进行后续操作.
			$(t).parents("tr").find("td").eq(4).html("<a href='javascript:void(0)'>" + v_title + "</a>");//交换添加删除按钮文字
			var $trhtml = $(t).parents("tr").remove();//在原来盒子中移除元素并保存
			$trhtml.css("display","").appendTo("#"+boxid);//显示盒子,并添加到新的盒子*/
        main_start();//重绘画布
    }
    
    function position_search(){
        var search_name = $.trim($('#search_name').val());
        if(search_name == '请输入广告位名称'){
            search_name='';
        }
        var url = '<?php echo Yii::app()->createUrl('schedule/positionList')?>?name='+encodeURIComponent(search_name);
        if(typeof(ajax_load) == 'function')
            ajax_load('position_list_area', url);
        else
            window.location = url;
        return false;
    }
</script>