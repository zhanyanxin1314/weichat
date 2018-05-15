<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<link rel="stylesheet" href="./resource/css/wxapp-icon.css">
<div id="js-wxapp-create">
	<div class="container">
		<div class="caret-wxapp">
			<div class="panel panel-app">
				<div class="panel-heading">
					<ol class="breadcrumb we7-breadcrumb">
						<a href="javascript:;" onclick="history.go(-1)" class="go-back">
						<i class="wi wi-back-circle"></i></a>
						<span class="font-lg">新建小程序</span>
					</ol>
				</div>
				<div class="panel-body">
					<?php  if(!$_W['isfounder'] && !empty($account_info['wxapp_limit'])) { ?>
						<div class="alert alert-warning">
							温馨提示：
							<i class="fa fa-info-circle"></i>
							Hi，<span class="text-strong"><?php  echo $_W['username'];?></span>，您所在的会员组： <span class="text-strong"><?php  echo $account_info['group_name'];?></span>，
							账号有效期限：<span class="text-strong"><?php  echo date('Y-m-d', $_W['user']['starttime'])?> ~~ <?php  if(empty($_W['user']['endtime'])) { ?>无限制<?php  } else { ?><?php  echo date('Y-m-d', $_W['user']['endtime'])?><?php  } ?></span>，
							可创建 <span class="text-strong"><?php  echo $account_info['maxwxapp'];?> </span>个小程序，已创建<span class="text-strong"> <?php  echo $account_info['wxapp_num'];?> </span>个，还可创建 <span class="text-strong"><?php  echo $account_info['wxapp_limit'];?> </span>个小程序。
						</div>
					<?php  } ?>
					<div class="wxapp-creat-select we7-padding clearfix">
						<div class="col-sm-6">
							<div class="title">
								<span class="wi wi-small-routine"></span>
								新建单个小程序
							</div>
							<div class="con">
								打包生成小程序，仅针对开发者单个小程序插件.
							</div>
							<div>
								<a href="<?php  echo url('wxapp/post/post', array('design_method' => WXAPP_MODULE, 'uniacid' => $uniacid))?>" class="btn btn-primary">新建小程序</a>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="title">
								<span class="wi wi-wxapp-pack"></span>
								打包多个小程序
							</div>
							<div class="con">
								打包生成小程序，可以选择多个开发者小程序插件进行打包，可以设置首页及底部导航.
							</div>
							<div>
								<a href="<?php  echo url('wxapp/post/post', array('design_method' => WXAPP_TEMPLATE, 'uniacid' => $uniacid))?>" class="btn btn-primary" disabled>打包小程序</a>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>