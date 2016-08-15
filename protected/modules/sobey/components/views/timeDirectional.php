			<td>
			<div class="fl" style="width:780px;">
				<div class="title">
					<ul class="w8Tbletit">
						<li class="help">快速选择： 
						  <a href="javascript:void(0);" onclick="fastSelectTime('all')">所有日期</a>
						  <a href="javascript:void(0);" onclick="fastSelectTime('workday')">工作</a>
						  <a href="javascript:void(0);" onclick="fastSelectTime('weekend')">周末</a>
						</li>
					</ul>
				</div>
				<div class="mainCon">
					<table border="0">
					  <tr>
					    <td width="100">&nbsp;</td>
						<td width="200" colspan="6" class="tacenter">0:00————6:00</td>
						<td width="10">&nbsp;</td>
						<td width="200" colspan="6" class="tacenter">6:00————12:00</td>
						<td width="10">&nbsp;</td>
						<td width="200" colspan="6" class="tacenter">12:00————18:00</td>
						<td width="10">&nbsp;</td>
						<td width="200" colspan="6" class="tacenter">18:00————24:00</td>
					  </tr>
					  <?php for($i=1; $i<8; $i++){ ?>
					  <tr>
					    <td><input type="checkbox" onchange="selectCurWeek('<?php echo $i; ?>')" value="<?php echo $i; ?>" name="week[]" id="week_<?php echo $i; ?>" <?php if(isset($week[$i])&&$week[$i]) echo 'checked="checked"'; ?> />
						  <label id="week_name_<?php echo $i; ?>" for="week_<?php echo $i; ?>"> <?php echo $weekList[$i]; ?></label></td>
					    <?php for($j=0; $j<24; $j++){ ?>
						<?php if($j!=0 && $j%6==0) { ?>
						<td>&nbsp;</td>
						<?php } ?>
						<td id="timebox_<?php echo ($i*100)+$j; ?>" <?php $temp=($i*100)+$j; if(in_array($temp, $time)) { ?>class="td_time_box_select"<?php } else { ?>class="td_time_box"<?php } ?> onmouseover="setCurTimeBoxStyle(<?php echo ($i*100)+$j; ?>);" onmouseout="revertCurTimeBoxStyle(<?php echo ($i*100)+$j; ?>);" onclick="selectCurTime(<?php echo ($i*100)+$j; ?>)"><?php echo $j; ?></td>
						<?php } ?>
					  </tr>
					  <?php } ?>
					</table>
				</div>
			</div>
			</td>