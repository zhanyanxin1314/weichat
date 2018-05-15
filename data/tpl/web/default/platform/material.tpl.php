<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">
	素材管理
</div>
<ul class="we7-page-tab">
	<li<?php  if($type == 'news') { ?> class="active"<?php  } ?>>
	<a href="<?php  echo url('platform/material', array('type' => 'news'))?>">微信图文</a>
	</li>
	<li<?php  if($type == 'image') { ?> class="active"<?php  } ?>>
	<a href="<?php  echo url('platform/material', array('type' => 'image', 'server' => MATERIAL_WEXIN))?>">图片</a>
	</li>
	<li<?php  if($type == 'voice') { ?> class="active"<?php  } ?>>
	<a href="<?php  echo url('platform/material', array('type' => 'voice', 'server' => MATERIAL_WEXIN))?>">语音</a>
	</li>
	<li<?php  if($type == 'video') { ?> class="active"<?php  } ?>>
	<a href="<?php  echo url('platform/material', array('type' => 'video', 'server' => MATERIAL_WEXIN))?>">视频</a>
	</li>
</ul>
<div id="main" ng-controller="materialDisplay" ng-cloak>
	<div class="material">
		<?php  if($type == 'news') { ?>
		<div class="material-appmsg">
			<div class="material-list-head clearfix">
				<div class="info col-sm-6 we7-padding-none">
					<div class="we7-form">
						<div class="form-controls">
							<form action="<?php  echo url('platform/material/list', array('type' => 'news', 'server' => $server))?>" method="post" >
								<div class="input-group">
									<input type="text" id="" name="title" class="form-control" size="40" value="<?php  echo $_GPC['title'];?>" placeholder="标题/作者/摘要">
									<span class="input-group-btn">
										<button class="btn btn-default"><i class="fa fa-search"></i></button>
									</span>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="pull-right">
					<?php  if($_W['account']['level'] > 2 && $_W['account']['isconnect'] = 1) { ?>
					<a href="javascript:;" ng-click="sync('<?php  echo $type;?>')" class="btn btn-default">同步微信</a>
					<?php  } ?>
					<a href="javascript:;" data-toggle="modal" data-target="#new_type" class="btn btn-primary we7-margin-left">新建图文消息</a>
				</div>
				<div class="modal fade" id="new_type" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="we7-modal-dialog modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<div class="modal-title">新建图文消息</div>
							</div>
							<div class="modal-body">
								<div class="reply" ng-click="createNew('reply')">
									<a href="javascript:;">
										<img src="./resource/images/reply.png" alt="" />
										<div class="content">
											<div class="title">创建微信图文</div>
											<div class="state">
												微信图文消息会自动同步至微信素材库，
												并可以直接群发给粉丝
											</div>
										</div>
									</a>
								</div>
								<div class="link" ng-click="createNew('link')">
									<a href="javascript:;">
										<img src="./resource/images/link.png" alt="" />
										<div class="content">
											<div class="title">创建图文链接</div>
											<div class="state">
												点击图文直接跳转至指定链接，可用于自动回复及认证号菜单
												配置，不能同步至微信素材库。
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="btn-group we7-btn-group" style="top:15px;">
				<a href="<?php  echo url('platform/material', array('type' => $type))?>" class="btn <?php  if($server != MATERIAL_WEXIN && $server != MATERIAL_LOCAL) { ?> active <?php  } ?>">全部</a>
				<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_WEXIN))?>" class="btn <?php  if($server == MATERIAL_WEXIN) { ?> active <?php  } ?>">微信</a>
				<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_LOCAL))?>" class="btn <?php  if($server == MATERIAL_LOCAL) { ?> active <?php  } ?>">服务器</a>
			</div>
			<div class="material-appmsg-list">
				<?php  if(is_array($material_list)) { foreach($material_list as $material) { ?>
				<div class="material-appmsg-item<?php  if(!empty($material['items']['1'])) { ?> multi<?php  } ?>">
					<div class="appmsg-content">
						<div class="appmsg-info">
							<em class="appmsg-date"><?php  echo date('Y年m月d', $material['createtime'])?></em>
							<?php  if($material['model'] == 'local') { ?>
							<i class="wi wi-local pull-right color-default" style="font-size: 20px;"></i>
							<?php  } else { ?>
							<i class="wi wi-wx-circle pull-right color-green" style="font-size: 20px;"></i>
							<?php  } ?>
							<?php  if($material['prompt_msg'] && $material['model'] != 'local') { ?>
							<div class="undone-tips">
								图文内容不完整
								<br> 请补全每一篇图文的封面图、标题和正文
							</div>
							<?php  } ?>
						</div>
						<div class="<?php  if(!empty($material['items']['1'])) { ?>cover-<?php  } ?>appmsg-item">
							<h4 class="appmsg-title">
								<a href="" target="-blank"><?php  echo $material['items']['0']['title'];?></a>
							</h4>
							<div class="appmsg-thumb" style="background-image:url('<?php  echo tomedia($material['items']['0']['thumb_url'])?>')">
							</div>
							<p class="appmsg-desc"><?php  echo $material['items']['0']['digest'];?></p>
							<?php  if($material['model'] == 'local') { ?>
								<?php  if(!empty($material['items'][0]['content'])) { ?>
								<a href="javascript:;" target="_blank" class="cover-dark">
								<?php  } ?>
							<?php  } else { ?>
							<a href="javascript:;" data-toggle="modal" data-target="#modalWechatView" ng-click="choiceSendType('<?php  echo $material['items']['0']['url'];?>', 'news', '<?php  echo $material['media_id'];?>')" class="cover-dark">
							<?php  } ?>
								<div class="edit-mask-content">
									<?php  if($material['model'] == 'local') { ?>
									<?php  if(!empty($material['items'][0]['content'])) { ?>
									本地预览<span data-toggle="tooltip" data-placement="bottom" title="本地文章，不可以群发，可以转换成为微信文章."><i class="wi wi-explain-sign"></i></span>
									<span class="hidden">外部链接预览<span data-toggle="tooltip" data-placement="bottom" title="外部链接，内容不是文章，不可以群发或转换为微信文章."><i class="wi wi-explain-sign"></i></span></span>
									<?php  } ?>
									<?php  } else { ?>
									微信预览
									<?php  } ?>
								</div>
								<span class="vm-box"></span>
							</a>
						</div>
						<?php  if(is_array($material['items'])) { foreach($material['items'] as $key => $material_row) { ?>
						<?php  if(!empty($key)) { ?>
						<div class="appmsg-item has-cover">
							<?php  if(!empty($material_row['thumb_url'])) { ?>
							<div class="appmsg-thumb" style="background-image:url('<?php  echo tomedia($material_row['thumb_url'])?>">
							</div>
							<?php  } ?>
							<h4 class="appmsg-title">
								<a href="" target="_blank" ><?php  echo $material_row['title'];?></a>
							</h4>
							<?php  if(!empty($material_row['thumb_url']) && !empty($material_row['url'])) { ?>
							<a href="<?php  echo $material_row['url'];?>" target="_blank" class="cover-dark">
								<div class="edit-mask-content">
									<p class="">
										预览文章 </p>
								</div>
								<span class="vm-box"></span>
							</a>
							<?php  } ?>
						</div>
						<?php  } ?>
						<?php  } } ?>
					</div>
					<div class="appmsg-opr">
						<ul>
							<?php  if($material['model'] == 'perm') { ?>
								<li class="appmsg-opr-item">
									<a href="javascript:;" class="" ng-click="checkGroup('news', <?php  echo $material['id'];?>)" data-toggle="tooltip" data-placement="bottom" title="群发"><i class="wi wi-send"></i></a>
								</li>
							<?php  } else { ?>
								<?php  if(!empty($material['items'][0]['content'])) { ?>
									<li class="appmsg-opr-item">
										<a href="javascript:;" ng-click="newsToWechat(<?php  echo $material['id'];?>)" class="" data-toggle="tooltip" data-placement="bottom" title="转换为微信文章"><i class="wi wi-transform"></i></a>
									</li>
								<?php  } ?>
							<?php  } ?>
							<li class="appmsg-opr-item">
								<a href="<?php  echo url('platform/material-post/news', array('newsid' => $material['id']))?>" class="" data-toggle="tooltip" data-placement="bottom" title="编辑">&nbsp;<i class="wi wi-text"></i></a>
							</li>
							<li class="appmsg-opr-item">
								<a class="" href="javascript:void(0);" ng-click="del_material('<?php  echo $type;?>', '<?php  echo $material['id'];?>', 'wechat')" data-toggle="tooltip" data-placement="bottom" title="删除">&nbsp;<i class="wi wi-delete2"></i></a>
							</li>
						</ul>
					</div>
				</div>
				<?php  } } ?>
			</div>
		</div>
		<?php  } else if($type == 'image') { ?>
		<div class="material-appmsg">
			<div class="upload-queue"></div>
			<div class="material-list-head clearfix">
				<div class="action">
					<?php  if($_W['account']['level'] > 2 && $_W['account']['isconnect'] = 1) { ?>
					<a href="javascript:;" class="btn btn-default" ng-click="sync('<?php  echo $type;?>')">同步微信</a>
					<?php  } ?>
					<?php  if($server == MATERIAL_WEXIN) { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('image', true, true)">上传微信图片</a>
					<?php  } else { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('image', true, false)">上传服务器图片</a>
					<?php  } ?>
				</div>
				<div class="btn-group we7-btn-group">
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_WEXIN))?>" class="btn <?php  if($server == MATERIAL_WEXIN) { ?> active <?php  } ?>">微信</a>
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_LOCAL))?>" class="btn <?php  if($server == MATERIAL_LOCAL) { ?> active <?php  } ?>">服务器</a>
				</div>
			</div>
			<ul class="material-img-list clearfix">
				<?php  if(is_array($material_list)) { foreach($material_list as $image) { ?>
				<li class="material-img-item">
					<div class="appimg-info">
						<?php  if($server == 'local') { ?>
						<i class="wi wi-local pull-right color-default" style="font-size: 20px;"></i>
						<?php  } else { ?>
						<i class="wi wi-wx-circle pull-right color-green" style="font-size: 20px;"></i>
						<?php  } ?>
						<span class="appimg-title"><?php  echo $image['filename'];?></span>
					</div>
					<div class="img-item">
						<?php  if($server == 'local') { ?>
						<img class="img-thumb" src="<?php  echo tomedia($image['attachment']);?>">
						<?php  } else { ?>
						<img class="img-thumb" src="<?php  echo tomedia($image['attachment'], true);?>">
						<?php  } ?>
					</div>
					<div class="img-opr">
						<ul>
							<?php  if($server == 'local') { ?>
							<li class="img-opr-item">
								<a href="javascript:;" class="" ng-click="transToWechat('<?php  echo $type;?>', '<?php  echo $image['id'];?>')" data-toggle="tooltip" data-placement="bottom" title="转换为微信图片"><i class="wi wi-transform"></i></a>
							</li>
							<?php  } else { ?>
							<li class="img-opr-item">
								<a href="javascript:;" class="" ng-click="checkGroup('image', <?php  echo $image['id'];?>)" data-toggle="tooltip" data-placement="bottom" title="群发"><i class="wi wi-send"></i></a>
							</li>
							<?php  } ?>
							<li class="img-opr-item">
								<a class="" href="javascript:void(0);" ng-click="del_material('<?php  echo $type;?>', '<?php  echo $image['id'];?>', '<?php echo $server == 'local'? local : wechat?>')" data-toggle="tooltip" data-placement="bottom" title="删除">&nbsp;<i class="wi wi-delete2"></i></a>
							</li>
						</ul>
					</div>
				</li>
				<?php  } } ?>
			</ul>
		</div>
		<?php  } else if($type == 'voice') { ?>
		<div class="material-appmsg">
		<div class="upload-queue"></div>
			<div class="material-list-head clearfix">
				<div class="action">
					<?php  if($_W['account']['level'] > 2 && $_W['account']['isconnect'] = 1) { ?>
					<a href="javascript:;" class="btn btn-default" ng-click="sync('voice')">同步微信</a>
					<?php  } ?>
					<?php  if($server == MATERIAL_WEXIN) { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('voice', true, true)">上传微信语音</a>
					<?php  } else { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('voice', true, false)">上传服务器语音</a>
					<?php  } ?>
				</div>
				<div class="btn-group we7-btn-group">
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_WEXIN))?>" class="btn <?php  if($server == MATERIAL_WEXIN) { ?> active <?php  } ?>">微信</a>
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_LOCAL))?>" class="btn <?php  if($server == MATERIAL_LOCAL) { ?> active <?php  } ?>">服务器</a>
				</div>
			</div>
			<ul class="material-audio-list">
				<?php  if(is_array($material_list)) { foreach($material_list as $voice) { ?>
				<li class="material-audio-item">
					<div class="audio-info clearfix">
						<?php  if($server == 'local') { ?>
						<i class="wi wi-local pull-right color-default" style="font-size: 20px;"></i>
						<?php  } else { ?>
						<i class="wi wi-wx-circle pull-right color-green" style="font-size: 20px;"></i>
						<?php  } ?>
						<span class="audio-time"><?php  echo date('m月d日', $voice['createtime'])?></span>
					</div>
					<div class="audio-item">
						<div class="icon-audio-wrp">
							<span class="icon-audio-msg"></span>
						</div>
						<div class="audio-content">
							<div class="audio-title"><?php  echo $voice['attachment'];?></div>
							<div class="audio-length"><?php  echo date('h:i', $voice['createtime'])?></div>
						</div>
					</div>
					<div class="audio-opr">
						<ul>
							<?php  if($server == 'local') { ?>
							<li class="audio-opr-item">
								<a href="javascript:;" class="" ng-click="transToWechat('<?php  echo $type;?>', '<?php  echo $voice['id'];?>')" data-toggle="tooltip" data-placement="bottom" title="转换为微信语音"><i class="wi wi-transform"></i></a>
							</li>
							<?php  } else { ?>
							<li class="audio-opr-item">
								<a href="javascript:;" class="" ng-click="checkGroup('voice', <?php  echo $voice['id'];?>)" data-toggle="tooltip" data-placement="bottom" title="群发"><i class="wi wi-send"></i></a>
							</li>
							<?php  } ?>
							<li class="audio-opr-item">
								<a class="" href="javascript:void(0);" ng-click="del_material('<?php  echo $type;?>', '<?php  echo $voice['id'];?>', '<?php echo $server == 'local'? local : wechat?>')" data-toggle="tooltip" data-placement="bottom" title="删除">&nbsp;<i class="wi wi-delete2"></i></a>
							</li>
						</ul>
					</div>
				</li>
				<?php  } } ?>
			</ul>
		</div>
		<?php  } else if($type == 'video') { ?>
		<div class="material-appmsg">
		<div class="upload-queue"></div>
			<div class="material-list-head clearfix">
				<div class="action">
					<?php  if($_W['account']['level'] > 2 && $_W['account']['isconnect'] = 1) { ?>
					<a href="javascript:;" class="btn btn-default" ng-click="sync('video')">同步微信</a>
					<?php  } ?>
					<?php  if($server == MATERIAL_WEXIN) { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('video', true, true)">上传微信视频</a>
					<?php  } else { ?>
					<a href="javascript:;" class="btn btn-primary we7-margin-left" ng-click="upload('video', true, false)">上传服务器视频</a>
					<?php  } ?>
				</div>
				<div class="btn-group we7-btn-group">
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_WEXIN))?>" class="btn <?php  if($server == MATERIAL_WEXIN) { ?> active <?php  } ?>">微信</a>
					<a href="<?php  echo url('platform/material', array('type' => $type, 'server' => MATERIAL_LOCAL))?>" class="btn <?php  if($server == MATERIAL_LOCAL) { ?> active <?php  } ?>">服务器</a>
				</div>
			</div>
			<ul class="material-img-list">
				<?php  if(is_array($material_list)) { foreach($material_list as $video) { ?>
				<li class="material-img-item">
					<div class="img-item">
						<div class="appimg-info">
							<?php  if($server == 'local') { ?>
							<i class="wi wi-local pull-right color-default" style="font-size: 20px;"></i>
							<?php  } else { ?>
							<i class="wi wi-wx-circle pull-right color-green" style="font-size: 20px;"></i>
							<?php  } ?>
							<span class="appimg-title"><?php echo ($server == 'local'? $video['filename'] : $video['tag']['title'])?></span>
						</div>
						<div class="img-thumb" style="background-image:url('<?php  echo tomedia($video['attachment'])?>')">

						</div>
					</div>
					<div class="img-opr">
						<ul>
							<?php  if($server == 'local') { ?>
							<li class="img-opr-item">
								<a href="javascript:;" class="" ng-click="transToWechat('<?php  echo $type;?>', '<?php  echo $video['id'];?>')" data-toggle="tooltip" data-placement="bottom" title="转换为微信视频"><i class="wi wi-transform"></i></a>
							</li>
							<?php  } else { ?>
							<li class="img-opr-item">
								<a href="javascript:;" class="" ng-click="checkGroup('video', <?php  echo $video['id'];?>)" data-toggle="tooltip" data-placement="bottom" title="群发"><i class="wi wi-send"></i></a>
							</li>
							<?php  } ?>
							<li class="img-opr-item">
								<a class="" href="javascript:void(0);" ng-click="del_material('<?php  echo $type;?>', '<?php  echo $video['id'];?>', '<?php echo $server == 'local'? local : wechat?>')" data-toggle="tooltip" data-placement="bottom" title="删除">&nbsp;<i class="wi wi-delete2"></i></a>
							</li>
						</ul>
					</div>
				</li>
				<?php  } } ?>
			</ul>
		</div>
		<?php  } ?>
	</div>
	<div class="modal fade" id="check-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">选择群发的粉丝组</h4>
				</div>
				<div class="modal-body">
					<select class="form-control" ng-model="group">
						<option value="">请选择粉丝组</option>
						<option value="-1">全部粉丝</option>
						<option ng-repeat="group in groups" value="{{ group.id }}">{{ group.name }}({{ group.count }})</option>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" ng-click="sendMaterial()">发送</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalWechatView" tabindex="-1" role="dialog" aria-labelledby="modalWechatViewLabel">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title default-color" id="modalWechatViewLabel">微信预览</h4>
				</div>
				<div class="modal-body material-content clearfix web-mobile-choice-type">
					<div class="material-wechat-view">
						<a href="javascript:;" ng-click="wabPreview()" data-url="" class="we7-margin-bottom">
							<span class="wi wi-wechat"></span>
							<span>网页预览</span>
						</a>
						<a href="javascript:;" ng-click="mobilePreview()" data-type="" data-media-id="" class="we7-mobile-material-preview">
							<span class="wi wi-iphone"></span>
							<span>手机预览</span>
						</a>
					</div>
					<div id="weixin-dialog" class="wechat-view-telephone hidden">
						<div class="nav">
							<span class="wi wi-back-circle color-gray" ng-click="previewBack()"></span>
							<a class="color-gray">微信预览</a>
							<span class="color-gray">/</span>
							<a href="javscript:;" class="color-default">输入微信号</a>
						</div>
						<form action="" class="form-horizontal">
							<div class="form-group we7-margin-top">
								<div class="col-sm-12">
									<div class="input-group">
										<input type="text" id="wxname" name="wxname" class="form-control" placeholder="输入接收人的微信号预览"/>
										<span class="input-group-btn">
											<button class="btn btn-default btn-send">
												发送
											</button>
										</span>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary btn-view" data-dismiss="modal" ng-click="previewBack()">取消</button>
				</div>
			</div>
		</div>
	</div>
	<!--end 素材:微信预览-->
</div>
<div class="text-right">
	<?php  echo $pager;?>
</div>
<script>
	require(['jquery.wookmark', 'fileUploader'], function() {
		//同步素材
		angular.module('materialApp').value('config', {
			'del_url' : "<?php  echo url('platform/material/delete')?>",
			'sync_url' : "<?php  echo url('platform/material/sync')?>",
			'send_url' : "<?php  echo url('platform/material/send')?>",
			'create_new_url' : "<?php  echo url('platform/material-post')?>",
			'trans_url' : "<?php  echo url('platform/material-post/upload_material')?>",
			'postwechat_url' : "<?php  echo url('platform/material-post/upload_news')?>",
			'group' : <?php  echo json_encode($group)?>,
			'syncNews' : "<?php  echo $_GPC['sync_news'];?>",
		});
		angular.bootstrap($('#main'), ['materialApp']);
		$('.material-appmsg-list .material-appmsg-item').wookmark({
			align: 'center',
			autoResize: false,
			container: $('.material-appmsg-list'),
			itemWidth: 289,
			offset: 30
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>