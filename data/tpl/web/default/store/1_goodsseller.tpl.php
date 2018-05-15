<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('header', TEMPLATE_INCLUDEPATH)) : (include template('header', TEMPLATE_INCLUDEPATH));?>
<div id="js-goods-seller" ng-controller="goodsSellerCtrl" ng-cloak>
	<ul class="we7-page-tab">
		<li <?php  if($type == STORE_TYPE_MODULE || empty($type)) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_MODULE,'direct' => '1'))?>">应用模块</a></li>
		<li <?php  if($type == STORE_TYPE_WXAPP_MODULE) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_WXAPP_MODULE,'direct' => '1'))?>">小程序模块</a></li>
		<li <?php  if($type == STORE_TYPE_ACCOUNT) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_ACCOUNT,'direct' => '1'))?>">公众号个数</a></li>
		<li <?php  if($type == STORE_TYPE_WXAPP) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_WXAPP,'direct' => '1'))?>">小程序个数</a></li>
		<li <?php  if($type == STORE_TYPE_API) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_API,'direct' => '1'))?>">API浏览次数</a></li>
		<li <?php  if($type == STORE_TYPE_PACKAGE) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goodsSeller', array('type' => STORE_TYPE_PACKAGE,'direct' => '1'))?>">应用权限组</a></li>
	</ul>
	<?php  if(in_array($type, array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE))) { ?>
	<form action="" class="form-inline clearfix we7-margin-bottom" method="post">
		<input type="hidden" class="online" value="<?php  echo $status;?>">
		<div class="input-group form-group" style="width: 400px;">
			<input type="text" name="keyword" value="<?php  echo $keyword;?>" class="form-control" placeholder="搜索">
			<span class="input-group-btn"><button class="btn btn-default"><i class="fa fa-search"></i></button></span>
		</div>
	</form>
	<?php  } ?>
	<?php  if(in_array($type, array(STORE_TYPE_MODULE, STORE_TYPE_WXAPP_MODULE))) { ?>
	<button class="pull-right btn btn-primary" ng-click="showModule()">添加</button>
	<?php  } else if($type == STORE_TYPE_ACCOUNT) { ?>
	<a class="pull-right btn btn-primary" href="<?php  echo $this->createWebUrl('goodspost', array('direct' => 1, 'type' => STORE_TYPE_ACCOUNT))?>">添加</a>
	<?php  } else if($type == STORE_TYPE_WXAPP) { ?>
	<a class="pull-right btn btn-primary" href="<?php  echo $this->createWebUrl('goodspost', array('direct' => 1, 'type' => STORE_TYPE_WXAPP))?>">添加</a>
	<?php  } else if($type == STORE_TYPE_API) { ?>
	<a class="pull-right btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#add_api">添加</a>
	<?php  } else if($_GPC['type'] == STORE_TYPE_PACKAGE) { ?>
	<a class="pull-right btn btn-primary" href="<?php  echo $this->createWebUrl('goodspost', array('direct' => 1, 'type' => STORE_TYPE_PACKAGE))?>">添加</a>
	<?php  } ?>
	<div class="btn-group we7-btn-group we7-margin-bottom">
		<a href="<?php  echo $this->createWebUrl('goodsSeller', array('direct' => 1, 'type' => $type))?>" class="btn" ng-class="{'active': status}">已上架</a>
		<a href="<?php  echo $this->createWebUrl('goodsSeller', array('online' => STATUS_OFF, 'type' => $type, 'direct' => 1))?>" class="btn" ng-class="{'active': !status}">未上架</a>
	</div>
	<table class="table we7-table vertical-middle">
		<col width="105px"/>
		<col/>
		<col width="170px"/>
		<col width="220px"/>
		<tr>
			<th colspan="2">应用信息</th>
			<th>单价</th>
			<th class="text-right">操作</th>
		</tr>
		<?php  if(!empty($goods_list)) { ?>
		<?php  if(is_array($goods_list)) { foreach($goods_list as $goods) { ?>
		<tr>
			<td>
				<?php  if($_GPC['type'] == STORE_TYPE_API) { ?>
				<div class="icon icon-api"><span class="wi wi-api"></span></div>
				<?php  } else if(in_array($_GPC['type'], array(STORE_TYPE_PACKAGE, STORE_TYPE_ACCOUNT, STORE_TYPE_WXAPP))) { ?>
				<div class="icon icon-api"><span class="wi wi-appjurisdiction"></span></div>
				<?php  } else { ?>
				<img src="<?php  echo $goods['module_info']['logo'];?>" alt="" width="60" height="60"/>
				<?php  } ?>
			</td>
			<td>
				<div class="we7-margin-bottom-sm">
					<?php  if($type == STORE_TYPE_PACKAGE) { ?>
					<?php  echo $groups[$goods['module_group']]['name'];?>
					<?php  } else if($type == STORE_TYPE_WXAPP) { ?>
					创建<?php  echo $goods['wxapp_num'];?>个小程序
					<?php  } else if($type == STORE_TYPE_ACCOUNT) { ?>
					创建<?php  echo $goods['account_num'];?>个公众号
					<?php  } else { ?>
					<?php  echo $goods['title'];?>
					<?php  } ?></div>
				<div class="color-gray text-over" style="width:460px"><?php  echo $good['description'];?></div>
			</td>
			<td><?php  echo $goods['price'];?>元 / <?php  if($goods['unit'] == 'month') { ?>月<?php  } else if($goods['unit'] == 'ten_thousand') { ?><?php  echo $goods['api_num'];?>万次<?php  } else if($goods['unit'] == 'day') { ?>天<?php  } else if($goods['unit'] == 'year') { ?>年<?php  } ?></td>
			<td ng-if="!status">
				<div class="link-group">
					<a href="<?php  echo $this->createWebUrl('goodspost', array('id' => $goods['id'], 'type' => $type, 'direct' => 1))?>">编辑</a>
					<a href="<?php  echo $this->createWebUrl('goodsseller', array('operate' => 'changestatus', 'id' => $goods['id'], 'direct' => 1))?>">上架</a>
					<a href="<?php  echo $this->createWebUrl('goodsseller', array('operate' => 'delete', 'id' => $goods['id'], 'direct' => 1))?>" class="del">删除</a>
				</div>
			</td>
			<td ng-if="status">
				<div class="link-group">
					<a href="<?php  echo $this->createWebUrl('goodspost', array('id' => $goods['id'], 'type' => $type, 'direct' => 1))?>">编辑</a>
					<a href="<?php  echo $this->createWebUrl('goodsseller', array('operate' => 'changestatus', 'id' => $goods['id'], 'direct' => 1))?>">下架</a>
				</div>
			</td>
		</tr>
		<?php  } } ?>
		<?php  } else { ?>
		<tr>
			<td colspan="4" class="text-center">暂无数据</td>
		</tr>
		<?php  } ?>
	</table>
	<div class="pull-right">
		<?php  echo $pager;?>
	</div>
	<div class="uploader-modal modal fade module" id="add_module"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog  modal-dialog modal-lg we7-modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">模块(点击添加)</h4>
				</div>
				<div class="modal-body material-content clearfix">
					<div class="material-head">
						<div class="form-horizontal clearfix">
							<div class="input-group pull-left col-sm-4">
								<input type="search" ng-model='keyword' class="form-control" placeholder="搜索关键字"/>
								<span class="input-group-btn"><button type="button" class="btn btn-default"><i class="wi wi-search"></i></button></span>
							</div>
						</div>
					</div>
					<div class="material-body">
						<div class="row">
							<div class="col-sm-2 select-module" ng-repeat="module in moduleList | filter:keyword">
								<div class="item">
									<img ng-src="{{module.logo}}" class="icon" ng-click="selectModule(module, $event)">
									<div class="name text-center">{{module.title}}</div>
									<div class="mask">
										<span class="wi wi-right"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="editPrice('add_module')">编辑价格</button>
					<button type="button" class="btn btn-primary" ng-click="toOffline('add_module')">添加到未上架</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="add_api" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="we7-modal-dialog modal-dialog we7-form">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="modal-title">添加API商品</div>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="control-label col-sm-2">商品名称</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input type="text" class="form-control" value="API商品" readonly>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2">浏览次数</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input type="text" ng-model="visitTimes" class="form-control">
								<span class="input-group-addon">万次</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2">设置价格</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input type="text" ng-model="visitPrice" class="form-control">
								<span class="input-group-addon">元</span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" ng-click="editPrice('add_api')">保存并上架</button>
					<button type="button" class="btn btn-primary" ng-click="toOffline('add_api')">保存至未上架</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	angular.module('storeApp').value('config', {
		'status' : <?php  echo $status?>,
		'goodsList': <?php echo !empty($goods_list) ? json_encode($goods_list) : 'null'?>,
		'moduleList': <?php echo !empty($module_list) ? json_encode($module_list) : 'null'?>,
		'token': <?php  echo json_encode($_W['token'])?>,
		'links': {
			'changestatus': "<?php  echo $this->createWebUrl('goodsSeller', array('operate' => 'changestatus', 'direct' => 1))?>",
			'add': "<?php  echo $this->createWebUrl('goodsPost', array('operate' => 'add', 'type' => $type,'direct' => 1))?>",
			'online': "<?php  echo $this->createWebUrl('goodsSeller', array('online' => 1, 'type' => $type, 'direct' => 1))?>",
			'offline': "<?php  echo $this->createWebUrl('goodsSeller', array('online' => '0', 'type' => $type, 'direct' => 1))?>",
			'post': "<?php  echo $this->createWebUrl('goodsPost', array('direct' => 1, 'type' => $_GPC['type'], ))?>",
		}
	});
	angular.bootstrap($('#js-goods-seller'), ['storeApp']);
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>