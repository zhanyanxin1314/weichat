<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<ul class="we7-page-tab">
	<li <?php  if($operate == 'store_status') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('setting', array('operate' => 'store_status','direct' => '1'))?>">状态设置</a></li>
	<li <?php  if($operate == 'menu') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('setting', array('operate' => 'menu','direct' => '1'))?>">菜单设置</a></li>
</ul>
<?php  if($operate == 'store_status') { ?>
<form action="" class="form we7-form" method="post">
	<div class="form-group">
		<label class="control-label col-sm-2">关闭商城</label>
		<div class="col-sm-8 form-control-static">
			<input type="radio" name="status" id="status-1" <?php  if($settings['status'] == 1) { ?> checked="checked"<?php  } ?> value="1">
			<label class="radio-inline" for="status-1">
				是
			</label>
			<input type="radio" name="status" id="status-0" <?php  if($settings['status'] == 0) { ?> checked="checked"<?php  } ?> value="0">
			<label class="radio-inline" for="status-0">
				否
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="control-label col-sm-2"></div>
		<div class="col-sm-8">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input type="submit" class="btn btn-primary" name="submit" value="提交">
		</div>
	</div>
</form>
<?php  } else if($operate == 'menu') { ?>
<form action="" class="form we7-form" method="post">
	<?php  if(!empty($goods_menu)) { ?>
	<?php  if(is_array($goods_menu)) { foreach($goods_menu as $key => $menu) { ?>
	<div class="form-group">
		<label class="control-label col-sm-2"><?php  echo $menu['title'];?></label>
		<div class="col-sm-8 form-control-static">
			<input type="radio" name="hide[<?php  echo $key;?>]" id="status-<?php  echo $key;?>-0" <?php  if($settings[$key] == 0) { ?> checked="checked"<?php  } ?> value="0">
			<label class="radio-inline" for="status-<?php  echo $key;?>-0">
				显示
			</label>
			<input type="radio" name="hide[<?php  echo $key;?>]" id="status-<?php  echo $key;?>-1" <?php  if($settings[$key] == 1) { ?> checked="checked"<?php  } ?> value="1">
			<label class="radio-inline" for="status-<?php  echo $key;?>-1">
				隐藏
			</label>
		</div>
	</div>	
	<?php  } } ?>
	<?php  } ?>
	<div class="form-group">
		<div class="control-label col-sm-2"></div>
		<div class="col-sm-8">
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>">
			<input type="submit" class="btn btn-primary" name="submit" value="提交">
		</div>
	</div>
</form>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>