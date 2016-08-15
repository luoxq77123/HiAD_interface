<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
<div class="fr page_info">
    <?php
    $item_from = $this->pages->getItemCount() > 0 ? $this->pages->getOffset()+1 : 0;
    $item_end = $this->pages->getOffset() + $this->pages->getLimit() > $this->pages->getItemCount() ? 
            $this->pages->getItemCount() : $this->pages->getOffset() + $this->pages->getLimit();
    ?>
    <span><?php echo $item_from;?>-<?php echo $item_end;?>条(共<?php echo $this->pages->getItemCount();?>条)</span>
    <?php echo CHtml::dropDownList('pageNum', $this->pages->getPageSize(), $this->arrPageSize, array('id' => 'pageNum'));?>
    条/页
</div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        // 绑定分页数切换
        $('#pageNum').change(function(){
            <?php 
            $route = $this->pages->route ? $this->pages->route : Yii::app()->getController()->getId().'/'.Yii::app()->getController()->getAction()->id;
            $pagesize_url = Yii::app()->createUrl('sobey/'.$route).'?';
            
            foreach($_GET as $key => $one){
                if($key != 'pagesize' )
                    $pagesize_url .=  $key.'='.$one.'&';
            }
            ?>
            var url = "<?php echo $pagesize_url?>pagesize=" + $('#pageNum option:selected').val();
            if(typeof(ajax_load) == 'function'){
                <?php if($this->refreshArea != ''):?>
                ajax_load("<?php echo $this->refreshArea;?>", url);      
                <?php else:?>
                frame_load(url);
                <?php endif;?>
            }else{
                window.location = url;
            }
        });
    });
</script>
</html>