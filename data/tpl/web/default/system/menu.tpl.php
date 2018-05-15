<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">
	菜单管理
</div>
<ul class="we7-page-tab"></ul>
<div class="js-menu-container" ng-controller="MenuCtrl" ng-cloak>
	<div class="we7-padding-bottom clearfix">
		<div class="pull-right">
			<a class="btn btn-primary we7-padding-horizontal" ng-click="editItemPanel({group : 'frame'})">+新建菜单</a>
		</div>
	</div>
	<table class="table we7-table table-hover article-list">
		<col width="150px"/>
		<col width="380px"/>
		<col width="90px"/>
		<col width="280px"/>
		<tr>
			<th class="text-left">排序</th>
			<th class="text-left">菜单名称</th>
			<th>显示</th>
			<th class="text-right">操作</th>
		</tr>
		<?php  if(is_array($system_menu)) { foreach($system_menu as $permission_name => $menu) { ?>
			<tr>
				<td class="text-left"></td>
				<td class="text-left"> 
					<span><?php  echo $menu['title'];?></span>
				</td>
				<td>
					<?php  if(in_array($permission_name, $system_top_menu)) { ?>
					<div class="switch" ng-init="displayStatus['<?php  echo $permission_name;?>'] = <?php echo $menu['is_display'] ? 'true' : 'false'?>" ng-click="changeDisplay('<?php  echo $permission_name;?>')" ng-class="{'switchOn' : displayStatus['<?php  echo $permission_name;?>'], 'switchOff' : !displayStatus['<?php  echo $permission_name;?>']}"></div>
					<?php  } ?>
				</td>
				<td>
					<?php  if(empty($menu['is_system'])) { ?>
					<a href="javascript:;" ng-click="removeSubItem('<?php  echo $menu['permission_name'];?>')" class="btn btn-danger">删除</a>
					<?php  } ?>
				</td>
			</tr>
			<?php  if(is_array($menu['section'])) { foreach($menu['section'] as $section_name => $section) { ?>
				<tr>
					<td class="text-left"><div class="pad-bottom "></div></td>
					<td class="text-left"> 
						<span style="margin-left: 25px;"><?php  echo $section['title'];?></span>
					</td>
					<td></td>
					<td class="text-right we7-padding-right">
						<?php  if($section_name != 'platform_module') { ?>
						<a href="javascript:;" class="color-default we7-margin-right-sm" ng-click="addSubItem('<?php  echo $section_name;?>', {title : '', displayorder : 0, isDisplay : 1})">+添加下级分类</a>
						<?php  } ?>
					</td>
				</tr>
				<?php  if(is_array($section['menu'])) { foreach($section['menu'] as $sub_permission_name => $sub_menu) { ?>
					<tr class="bg-light-gray">
						<td class="text-left">
							<div class="pad-bottom "><?php  echo intval($sub_menu['displayorder'])?></div>
						</td>
						<td class="text-left"> 
							<span style="margin-left: 60px;"><?php  echo $sub_menu['title'];?></span>
						</td>
						<td>
							<?php  if($sub_permission_name == 'system_setting_menu') { ?>
								<span class="color-gray">不可关闭</span>
							<?php  } else { ?>
							<div class="switch" ng-init="displayStatus['<?php  echo $sub_menu['permission_name'];?>'] = <?php echo $sub_menu['is_display'] ? 'true' : 'false'?>" ng-click="changeDisplay('<?php  echo $sub_menu['permission_name'];?>')" ng-class="{'switchOn' : displayStatus['<?php  echo $sub_menu['permission_name'];?>'], 'switchOff' : !displayStatus['<?php  echo $sub_menu['permission_name'];?>']}"></div>
							<?php  } ?>
						</td>
						<td class="we7-padding-right">
							<div class="link-group">
								<a href="javascript:;" class="we7-margin-right-sm" ng-click="editItemPanel({displayorder: '<?php  echo $sub_menu['displayorder'];?>', title : '<?php  echo $sub_menu['title'];?>', url : '<?php  echo $sub_menu['url'];?>', permissionName : '<?php  echo $sub_menu['permission_name'];?>', isSystem : '<?php  echo $sub_menu['is_system'];?>', id : '<?php  echo $sub_menu['id'];?>', 'group' : '<?php  echo $sub_menu['group'];?>', 'icon' : '<?php  echo $sub_menu['icon'];?>'})">编辑</a>
								<?php  if(empty($sub_menu['is_system'])) { ?><a href="javascript:;" ng-click="removeSubItem('<?php  echo $sub_permission_name;?>')" class="del">删除</a><?php  } ?>
							</div>
						</td>
					</tr>
				<?php  } } ?>
				<tr class="bg-light-gray" ng-repeat="submenu in subItemGroup['<?php  echo $section_name;?>']">
					<td class="text-left">
						<div class="pad-bottom ">{{submenu.displayorder}}</div>
					</td>
					<td class="text-left">
						<span style="margin-left: 50px;">{{submenu.title}}</span>
					</td>
					<td>
						<label>
							<div ng-show="submenu.isDisplay" class="switch switchOn"></div>
							<div ng-show="!submenu.isDisplay" class="switch switchOff"></div>
						</label>
					</td>
					<td class="text-right we7-padding-right">
						<div class="link-group">
							<a href="javascript:;" class="we7-margin-right-sm" ng-click="editItemPanel({group : '<?php  echo $section_name;?>', displayorder: '0', title : submenu.title + submenu.$$hashKey})">编辑</a>
							<a href="javascript:;" ng-click="removeSubItem('<?php  echo $section_name;?>', $index)" class="del">删除</a>
						</div>
					</td>
				</tr>
			<?php  } } ?>
		<?php  } } ?>
	</table>

	<div class="modal fade bs-example-modal-sm js-edit-panel" id="edit" tabindex="-1" style="z-index:1039" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog we7-modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">编辑菜单</h4>
				</div>
				<form action="" method="post" enctype="multipart/form-data" class="we7-form form" >
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">菜单排序</label>
							<div class="col-sm-10">
								<input type="text" name="displayorder" ng-model="activeItem.displayorder" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">菜单名称</label>
							<div class="col-sm-10">
								<input type="text" name="title" ng-model="activeItem.title" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 菜单标识</label>
							<div class="col-sm-10">
								<input type="text" name="permission_name" ng-readonly="activeItem.isSystem == '1'" ng-model="activeItem.permissionName" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 菜单链接</label>
							<div class="col-sm-10">
								<input type="text" name="url" ng-readonly="activeItem.isSystem == '1'" ng-model="activeItem.url" class="form-control">
							</div>
						</div>
						<div class="form-group" ng-hide="activeItem.isSystem == '1'">
							<label class="col-sm-2 control-label"> 菜单图标</label>
							<div class="col-sm-10">
								<div class="input-group">
									<input type="text" name="icon" value="" ng-model="activeItem.icon" class="form-control">
									<span class="input-group-addon" style="width:35px; border-left:none"><i class="fa fa-external-link"></i></span>
									<span class="input-group-btn"> <a href="javascript:;" class="btn btn-default" ng-click="selectMenuIcon();">&nbsp;选择图标</a></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
						<button type="button" class="btn btn-primary" name="submit" value="保存" ng-click="editItem()">保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		angular.bootstrap($('.js-menu-container'), ['systemApp']);
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>