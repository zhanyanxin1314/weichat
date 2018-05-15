<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="we7-page-tab">
	<li class="active"><a href="<?php  echo url('statistics/app');?>">访问统计信息</a></li>
	<li><a href="<?php  echo url('statistics/setting');?>">访问统计设置</a></li>
</ul>
<div class="api">
	<div class="panel we7-panel api-target">
		<div class="panel-heading">今日/昨日关键指标</div>
		<div class="panel-body we7-padding-vertical">
			<div class="col-sm-4 text-center">
				<div class="title">应用总访问数</div>
				<div>
					<span class="today"><?php  echo $today_module_api['visit_sum'];?></span>
					<span class="yesterday">/ <?php  echo $yesterday_module_api['visit_sum'];?></span>
				</div>
			</div>
			<div class="col-sm-4 text-center">
				<div class="title">应用平均访问数</div>
				<div>
					<span class="today"><?php  echo $today_module_api['visit_avg'];?></span>
					<span class="yesterday">/ <?php  echo $yesterday_module_api['visit_avg'];?></span>
				</div>
			</div>
			<div class="col-sm-4 text-center">
				<div class="title">应用最高访问数</div>
				<div>
					<span class="today"><?php  echo $today_module_api['visit_highest'];?></span>
					<span class="yesterday">/ <?php  echo $yesterday_module_api['visit_highest'];?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="panel we7-panel">
		<div class="panel-heading tab">
			<a href="javascript:;">关键指标详解</a>
			<a href="javascript:;" class="active">总访问数</a>
			<a href="javascript:;" class="hidden">平均访问数</a>
			<a href="javascript:;" class="hidden">最高访问数</a>
		</div>
		<div class="panel-body data-view">
			<div class="tab-bar-time clearfrix">
				<span class="we7-margin-right">时间</span>
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-default active" ng-click="getModuleApi('today')">今日统计</button>
					<button type="button" class="btn btn-default hidden" ng-click="getModuleApi('week')">周统计</button>
					<button type="button" class="btn btn-default hidden" ng-click="getModuleApi('month')">月统计</button>
					<div class="btn-group hidden" role="group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							2017-07-01至2017-09-09
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="#">Dropdown link</a></li>
							<li><a href="#">Dropdown link</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-sm-12" id="js-statistics-app-display" ng-controller="HorizontalBarCtrl" ng-cloak>
				<canvas class="chart chart-horizontal-bar " chart-options="options" chart-data="data" chart-labels="labels" chart-colors="colors" height="{{height}}"></canvas>
				<div class="we7-margin-vertical text-right">
					<a href="javascript:;" class="color-default" ng-click="changeStatus()">
						解决访问统计没数据的方法 <span class="wi wi-angle-up"></span>
					</a>
				</div>
				<div class="distribution-steps" ng-show = "show==true">
					<div class="steps-container">
						<div>
							<div class="num">1</div>
							<div class="title">
								<span class="wi wi-warning-sign"></span>应用没有统计数据
							</div>
							<div class="content">
								没有应用的统计数据，是因为模块内没有统计数据的代码，需要复制第2步的代码到对应的模块内。然后联系应用模块开发者更新提交代码，完成后即可生成统计数据。
							</div>
						</div>
						<div>
							<div class="num">2</div>
							<div class="title">
								<span class="wi wi-code"></span>复制代码
							</div>
							<div class="content">
								<textarea class="form-control code-container we7-margin-bottom-sm" ng-model="code">
								</textarea>
								<div><a href="javascript:;" id="copy-1" class="btn btn-primary" clipboard supported="supported" text="code"
									 on-copied="success(1);">复制代码</a></div>
							</div>
						</div>
						<div>
							<div class="num">3</div>
							<div class="title">
								<span class="wi wi-help"></span>联系开发者
							</div>
							<div class="content">
								找到没有统计数据的应用，联系<span class="color-default">应用模块的开发者</span>，让开发者将代码更新提交后，则可生成模块的统计数据。
								<!--<div><a href="#" class="color-default">联系开发者 ></a></div>-->
							</div>
						</div>
					</div>
				</div>
			</div>
			<table class="table we7-table vertical-middle hidden">
				<col />
				<col />
				<col />
				<col />
				<tr>
					<th>时间</th>
					<th>总访问数</th>
					<th>平均访问数</th>
					<th>最高访问数</th>
				</tr>
				<tr>
					<td class="text-center">2017-08-03</td>
					<td class="text-center">10000000</td>
					<td class="text-center">10000000</td>
					<td class="text-center">10000000</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script>
require(['angular-chart'], function() {
	angular.module('statisticsApp').value('config', {
		'links': {
			'moduleApi': "<?php  echo url('statistics/app/get_module_api')?>",
		},
	});
	angular.bootstrap($('#js-statistics-app-display'), ['statisticsApp']);
})
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>