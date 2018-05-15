<?php defined('IN_IA') or exit('Access Denied');?><ol class="breadcrumb we7-breadcrumb">
	<a href="<?php  echo url('account/manage', array('account_type' => ACCOUNT_TYPE))?>"><i class="wi wi-back-circle"></i> </a>
	<li><a href="<?php  echo url('account/manage', array('account_type' => ACCOUNT_TYPE))?>"><?php echo ACCOUNT_TYPE_NAME;?>管理</a></li>
	<li><?php echo ACCOUNT_TYPE_NAME;?>设置</li>
</ol>
<div class="media media-wechat-setting">
	<a class="media-left">
		<span class="icon">
			<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_APP_NORMAL) { ?>
			<i class="wi wi-wxapp"></i>
			<?php  } else { ?>
			<i class="wi wi-wechat"></i>
			<?php  } ?>
		</span>
		<img src="<?php  echo $headimgsrc;?>" class="wechat-img">
	</a>
	<div class="media-body media-middle ">
		<h4 class="media-heading color-dark"><?php  echo $account['name'];?></h4>
		<span class="color-gray">
			<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_APP_NORMAL) { ?>
				小程序
			<?php  } else { ?>
				<?php  if($account['level'] == 1) { ?>类型：普通订阅号<?php  } ?><?php  if($account['level'] == 2) { ?>类型：普通服务号<?php  } ?><?php  if($account['level'] == 3) { ?>类型：认证订阅号<?php  } ?><?php  if($account['level'] == 4) { ?>类型：认证服务号/认证媒体/政府订阅号<?php  } ?>
			<?php  } ?>
		</span>
	</div>
	<?php  if($state == ACCOUNT_MANAGE_NAME_FOUNDER || $state == ACCOUNT_MANAGE_NAME_OWNER) { ?>
	<div class="media-right media-middle">
		<a href="<?php  echo url('account/manage/delete', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => ACCOUNT_TYPE))?>" class="btn btn-primary" onclick="return confirm('确认放入回收站吗？')">停  用</a>
	</div>
	<?php  } ?>
</div>
<div class="clearfix"></div>
<div class="btn-group we7-btn-group wechat-edit-group">
	<?php  if($state == ACCOUNT_MANAGE_NAME_FOUNDER || $state == ACCOUNT_MANAGE_NAME_OWNER || $state == ACCOUNT_MANAGE_NAME_VICE_FOUNDER) { ?>
	<a href="<?php  echo url('account/post/base', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => ACCOUNT_TYPE))?>" class="btn btn-default <?php  if($do == 'base') { ?> active<?php  } ?>">基础信息</a>
	<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_OFFCIAL_NORMAL) { ?>
	<a href="<?php  echo url('account/post/sms', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => ACCOUNT_TYPE))?>" class="btn btn-default <?php  if($do == 'sms') { ?> active<?php  } ?>">短信信息</a>
	<?php  } ?>
	<?php  } ?>
	<a href="<?php  echo url('account/post-user/edit', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => $_GPC['account_type']))?>" class="btn btn-default <?php  if($action == 'post-user' && $do == 'edit') { ?> active<?php  } ?>">使用者管理</a>
	<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_OFFCIAL_NORMAL) { ?>
	<a href="<?php  echo url('account/post/modules_tpl', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => ACCOUNT_TYPE))?>" class="btn btn-default <?php  if($do == 'modules_tpl') { ?> active<?php  } ?>">可用应用模块/模板</a>
	<?php  } ?>
	<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_APP_NORMAL) { ?>
	<a href="<?php  echo url('wxapp/manage/display', array('uniacid' => $_GPC['uniacid'], 'acid' => $_GPC['acid'], 'account_type' => ACCOUNT_TYPE_APP_NORMAL))?>" class="btn btn-default <?php  if($action == 'manage' && $do == 'display') { ?> active<?php  } ?>">版本管理</a>
	<?php  } ?>
	<?php  if(ACCOUNT_TYPE == ACCOUNT_TYPE_APP_NORMAL) { ?>
	<a href="<?php  echo url('account/post/modules_tpl', array('uniacid' => $account['uniacid'], 'acid' => $account['acid'], 'account_type' => ACCOUNT_TYPE_APP_NORMAL))?>" class="btn btn-default <?php  if($action == 'post' && $do == 'modules_tpl') { ?> active<?php  } ?>">可用应用模板/模块</a>
	<?php  } ?>
</div>