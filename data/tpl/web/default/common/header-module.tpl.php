<?php defined('IN_IA') or exit('Access Denied');?><?php  global $_W;?>
<?php  if(file_exists(IA_ROOT. "/addons/". $_W['current_module']['name']. "/icon-custom.jpg")) { ?>
<img src="<?php  echo tomedia("addons/".$_W['current_module']['name']."/icon-custom.jpg")?>" class="head-app-logo" onerror="this.src='./resource/images/gw-wx.gif'">
<?php  } else { ?>
<img src="<?php  echo tomedia("addons/".$_W['current_module']['name']."/icon.jpg")?>" class="head-app-logo" onerror="this.src='./resource/images/gw-wx.gif'">
<?php  } ?>
<span class="font-lg"><?php  echo $_W['current_module']['title'];?></span>

<div class="pull-right related-info module-related-info">
</div>
<script>
	$.post('./index.php?c=module&a=display&do=accounts_dropdown_menu', {'module_name': "<?php  echo $_W['current_module']['name']?>", 'version_id': "<?php  echo $_GPC['version_id']?>"}, function(data){
		$('.module-related-info').html(data);
	}, 'html');
</script>
<!-- 兼容历史性问题：模块内获取不到模块信息$module的问题-start -->
<?php  if(CRUMBS_NAV == 1) { ?>
<?php  global $module;?>
<?php  } ?>
<!-- end -->