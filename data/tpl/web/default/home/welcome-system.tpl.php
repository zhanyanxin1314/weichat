<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<!--系统管理首页-->
<div class="welcome-container js-system-welcome" ng-controller="WelcomeCtrl" ng-cloak>
	<div class="ad-img we7-margin-bottom">
		<a ng-href="{{ad.url}}" target="_blank" ng-repeat="ad in ads"><img ng-src="{{ad.src}}" alt="" class="img-responsive" style="margin: 0 auto;"></a>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading">微信应用模块</div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-4 text-center">
						<div class="title">未安装应用</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 1))?>" class="color-default">{{account_uninstall_modules_nums}}</a>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">可升级应用</div>
						<div class="num">
						{{upgrade_module_nums.account_upgrade_module_nums}}
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">应用总数</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/installed', array('account_type' => 1))?>" class="color-default">{{account_modules_total}}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="panel we7-panel account-stat">
				<div class="panel-heading">小程序应用模块</div>
				<div class="panel-body we7-padding-vertical">
					<div class="col-sm-4 text-center">
						<div class="title">未安装应用</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/not_installed', array('account_type' => 4))?>" class="color-default">{{wxapp_uninstall_modules_nums}}</a>
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">可升级应用</div>
						<div class="num">
						{{upgrade_module_nums.wxapp_upgrade_module_nums}}
						</div>
					</div>
					<div class="col-sm-4 text-center">
						<div class="title">应用总数</div>
						<div class="num">
							<a href="<?php  echo url('module/manage-system/installed', array('account_type' => 4))?>" class="color-default">{{wxapp_modules_total}}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel we7-panel database">
		<div class="panel-heading">
			数据库备份提醒
		</div>
		<div class="panel-body clearfix">
			<div class="col-sm-9">
				<span class="day"><?php  echo $backup_days;?></span>
				<span class="color-default">天</span>
				没有备份数据库了,请及时备份!
			</div>
			<div class="col-sm-3 text-center">
				<a class="btn btn-default" href="<?php  echo url('system/database');?>">开始备份</a>
			</div>
		</div>
	</div>
	<div class="panel we7-panel apply-list">
		<div class="panel-heading">
			<span class="pull-right">
				<a href="<?php  echo url('module/manage-system', array('account_type' => 1))?>" class="color-default">查看更多公众号应用</a>
				<span class="we7-padding-horizontal inline-block color-gray">|</span>
				<a href="<?php  echo url('module/manage-system', array('account_type' => 4))?>" class="color-default">查看更多小程序应用</a>
			</span>
			可升级应用
		</div>
		<div class="panel-body">
			<a href="{{module.link}}" target="_blank" class="apply-item" ng-repeat="module in upgrade_module_list">
				<img src="{{module.logo}}" class="apply-img"/>
				<span class="text-over">{{module.title|limitTo:4}}</span>
				<span class="color-red">升级</span>
			</a>
			<div class="text-center" ng-if="upgrade_modules_show == 0">
				没有可升级的应用
			</div>
		</div>
	</div>
</div>
<!--end 系统管理首页-->
<script type="text/javascript">
	$(function(){
		angular.module('systemApp').value('config', {
			notices: <?php echo !empty($notices) ? json_encode($notices) : 'null'?>,
			systemUpgradeUrl : "<?php  echo url('home/welcome/get_system_upgrade')?>",
			upgradeModulesUrl: "<?php  echo url('home/welcome/get_upgrade_modules')?>",
			moduleStatisticsUrl: "<?php  echo url('home/welcome/get_module_statistics')?>"
		});
		angular.bootstrap($('.js-system-welcome'), ['systemApp']);
	});
</script>	
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>