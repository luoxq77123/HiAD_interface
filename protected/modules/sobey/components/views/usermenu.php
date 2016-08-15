<!--主导航-->
<div class="nav" id="vnavtab">
	<ul>
        <?php foreach($menu_tree as $one):?>
            <li><a href="javascript:void(0);" id="<?php echo $one['system'];?>"><?php echo $one['name'];?></a></li>
        <?php endforeach;?>
    </ul>
</div>
<!--end 主导航-->
<!--二级导航-->
<div class="nav_child" id="vnavtabbod">
    <?php foreach($menu_tree as $one):?>
        <ul style="display:none" fid="<?php echo $one['system'];?>">
            <?php foreach($one['child'] as $a):?>
            <li><a href="<?php echo $a['url'];?>"><?php echo $a['name'];?></a></li>
            <?php endforeach;?>
        </ul>
    <?php endforeach;?>
</div>
<!--end 二级导航-->