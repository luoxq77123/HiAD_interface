<!--面包屑-->
<div class="tpboder pl_35" id="pt">
    <div class="z"><a href="javascript:void(0);">广告位</a> &gt; <a href="javascript:void(0);">客户端广告</a> &gt; 
        <?php if(isset($_GET['a']) &&  $_GET['a'] == 'table'):?>
            <a href="javascript:void(0);">广告位投放视图</a>
        <?php else:?>
            <a href="javascript:void(0);">广告位列表</a>
        <?php endif;?>
    </div>
</div>
<!--end 面包屑-->
<!--左右盒子-->
<div class="lr_box">
	<!--左-->
    <?php $this->widget('AppPositionMenu'); ?>
    <!--end 左-->
    <!--右-->
    <div class="right font12" id="info_nav_box">
    	<!--隐藏左侧按钮-->
    	<span id="hideleft" class="l_s"></span>
		<script type="text/javascript">
           $(document).ready(function(e) {
               $("#hideleft").click(function(){
                   hide_left($("#left"),$("#info_nav_box"));
                });
            });
        </script>
        <!--end 隐藏左侧按钮-->
          
        <!--内容替换去区-->
        <div id="ggw_box">
            <?php 
			if(isset($_GET['a']) &&  $_GET['a'] == 'table')
				$this->widget('AppPositionTable');
			else
				$this->widget('AppPositionList');
			?>
        </div>
        <!--end 内容替换区-->
     	
    </div>
    <!--end 右-->
</div>
<!--end 左右盒子-->
<script type="text/javascript">
$(document).ready(function(){	
	ReSet();
});

</script>
