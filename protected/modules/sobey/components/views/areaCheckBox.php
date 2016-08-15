			<td>
			<div class="step1 fl">
				<div class="title">
					<ul class="w8Tbletit">
						<li class="w8">省/市</li>
						<li class="w9">站点</li>
						<li class="w5"><a href="javascript:void(0);" onclick="selectAllAddr();">全部添加&gt;&gt;</a></li>
					</ul>
				</div>
				<div class="mainCon" id="mainCon1">
					<ul class="tvProvince">
					<?php foreach($provice as $k=>$v) { ?>
						<li>
						  <input type="checkbox" onclick="selectProvinceAddr('<?php echo $k; ?>')" value="<?php echo $k; ?>" <?php if($v['selected']) echo 'checked="checked"'; ?> name="province[]" id="provice_<?php echo $k; ?>"/>
						  <label id="addr_name_<?php echo $k; ?>" for="provice_<?php echo $k; ?>" onmouseover="showCitys('<?php echo $k; ?>');" onmouseout="hideCitys('<?php echo $k; ?>');"> <?php echo $v['name']; ?></label>
						  <?php if (!empty($city[$k])) { ?>
						  <ul style="margin-left: 53px !important;margin-left: -6px;" id="province_city_<?php echo $k; ?>" class="hide citylist" onmouseover="showCitys('<?php echo $k; ?>');" onmouseout="hideCitys('<?php echo $k; ?>');">
						    <?php foreach($city[$k] as $key=>$val) { ?>
							<li>
							  <input type="checkbox" onclick="selectCityAddr('<?php echo $key; ?>')" value="<?php echo $key; ?>" name="<?php echo $k; ?>_city[]" id="city_<?php echo $key; ?>" <?php if($val['selected']) echo 'checked="checked"'; ?> /><label id="addr_name_<?php echo $key; ?>" for="city_<?php echo $key; ?>"> <?php echo $val['name']; ?></label>
							</li>
							<?php } ?>
						  </ul>
						  <?php } ?>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
			</td>
			<td>
			<div class="stepArral fl">&nbsp;</div>
			</td>
			<td class="align_top">
			<div class="step1 step2 fl">
				<div class="select_box_bar"></div>
				<div class="title">
					<ul>
						<li class="w6">定向条件</li>
						<li class="w7"><a href="javascript:void(0);" onclick="cleanAllAddr();">&lt;&lt;全部删除</a></li>
					</ul>
				</div>
				<div class="mainCon" id="mainCon2">
					<ul id="address_select_box" class="addr_select_list">
					<?php foreach($area as $key=>$val) { ?>
						<li id="address_select_<?php echo $key; ?>">
						<span> <?php echo $val; ?></span>
						<img class="mt7" onclick="deleteCurAddr('<?php echo $key; ?>');" src="<?php echo Yii::app()->request->baseUrl; ?>/images/view_clean.gif">
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
			</td>