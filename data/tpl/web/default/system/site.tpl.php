<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">站点设置</div>
<ul class="we7-page-tab">
	<li<?php  if($do == 'copyright') { ?> class="active"<?php  } ?>><a href="<?php  echo url('system/site');?>">站点信息</a></li>
</ul>
<div class="clearfix">
	<form action="" method="post"  class="we7-form" role="form" enctype="multipart/form-data" id="form1">
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">关闭站点</label>
			<div class="col-sm-8 form-control-static">
				<input type="radio" name="status" id="status-1" <?php  if($settings['status'] == 1) { ?> checked="checked" <?php  } ?> value="1" />
				<label class="radio-inline" for="status-1">
					 是
				</label>
				<input type="radio" name="status" id="status-0" <?php  if($settings['status'] == 0) { ?> checked="checked" <?php  } ?> value="0" /> 
				<label class="radio-inline" for="status-0">
					否
				</label>
			</div>
		</div>
		<div class="form-group reason" <?php  if($settings['status'] == 0) { ?> style="display:none;" <?php  } ?>>
			<label class="col-sm-2 control-label" style="text-align:left;">关闭原因</label>
			<div class="col-sm-8">
				<textarea style="height:150px;" class="form-control" cols="70" name="reason" autocomplete="off"><?php  echo $settings['reason'];?></textarea>
				<input type="hidden" name="reasons" value="<?php  echo $settings['reason'];?>">
			</div>
		</div>
		<h5 class="page-header">登录站点</h5>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">是否开启验证码</label>
			<div class="col-sm-8 form-control-static">
				<input type="radio" id="verifycode-1" name="verifycode" <?php  if($settings['verifycode'] == 1) { ?> checked="checked" <?php  } ?> value="1" /> 
				<label class="radio-inline" for="verifycode-1">
					是
				</label>
				<input type="radio" id="verifycode-0" name="verifycode" <?php  if($settings['verifycode'] == 0) { ?> checked="checked" <?php  } ?> value="0" /> 
				<label class="radio-inline" for="verifycode-0">
					否
				</label>
			</div>
		</div>
	<h5 class="page-header">版权信息</h5>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">是否显示首页</label>
			<div class="col-sm-8 form-control-static">
				<input type="radio" name="showhomepage" value="1" id="showhomepage_1" <?php  if(!empty($settings['showhomepage'])) { ?> checked<?php  } ?>>
				<label for="showhomepage_1" class="radio-inline"> 是</label>
				<input type="radio" name="showhomepage" value="0" id="showhomepage_2" <?php  if(empty($settings['showhomepage'])) { ?> checked<?php  } ?>>
				<label for="showhomepage_2" class="radio-inline"> 否</label>
				<div class="help-block">设置“否”后，打开地址时将直接跳转到登录页面，否则会跳转到首页。</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">网站名称</label>
			<div class="col-sm-8">
				<input type="text" name="sitename" class="form-control" value="<?php  echo $settings['sitename'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">网站URL</label>
			<div class="col-sm-8">
				<input type="text" name="url" class="form-control" value="<?php  echo $settings['url'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">keywords</label>
			<div class="col-sm-8">
				<input type="text" name="keywords" class="form-control" value="<?php  echo $settings['keywords'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">description</label>
			<div class="col-sm-8">
				<input type="text" name="description" class="form-control" value="<?php  echo $settings['description'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">左侧菜单定位</label>
			<div class="col-sm-8 form-control-static">
				<input type="radio" name="leftmenu_fixed" id="leftmenu_fixed_status-1" <?php  if($settings['leftmenufixed'] == 1) { ?> checked="checked" <?php  } ?> value="1" />
				<label class="radio-inline" for="leftmenu_fixed_status-1">
					 是
				</label>
				<input type="radio" name="leftmenu_fixed" id="leftmenu_fixed_status-0" <?php  if($settings['leftmenufixed'] == 0) { ?> checked="checked" <?php  } ?> value="0" /> 
				<label class="radio-inline" for="leftmenu_fixed_status-0">
					否
				</label>
				<span class="help-block">选择“否”并保存后，左侧菜单随页面滚动而上下滚动</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">后台风格设置</label>
			<div class="col-sm-8">
				<select name="template" class="form-control">
					<?php  if(is_array($template)) { foreach($template as $tpl) { ?>
					<option value="<?php  echo $tpl;?>" <?php  if($_W['setting']['basic']['template'] == $tpl) { ?>selected<?php  } ?>>
						<?php  if(!empty($template_ch_name[$tpl])) { ?><?php  echo $template_ch_name[$tpl];?><?php  } else { ?><?php  echo $tpl;?><?php  } ?>
					</option>
					<?php  } } ?>
				</select>
				<span class="help-block">favorite icon</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">favorite icon</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_image('icon', $settings['icon'], '', array('global' => true, 'extras' => array('image'=> ' width="32" ')));?>
				<span class="help-block">favorite icon</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">前台LOGO</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_image('flogo', $settings['flogo'], '', array('global' => true));?>
				<span class="help-block">最佳尺寸：220px*50px</span>
				<span class="help-block">此logo是指首页及登录页面logo。</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">背景图片</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_image('background_img', $settings['background_img'], '', array('global' => true));?>
				<span class="help-block">此图片是指登录页面的背景图。</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">前台幻灯片</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_multi_image('slides', $settings['slides'], '', array('global' => true, 'thumb' => 0));?>
				<span class="help-block">设置首页幻灯片。</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">前台幻灯片显示文字</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="notice" value="<?php  echo $settings['notice'];?>"/>
				<span class="help-block">该文字显示在幻灯片上。</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">后台LOGO</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_image('blogo', $settings['blogo'], '', $options = array('global' => true));?>
				<span class="help-block">最佳尺寸：110px*35px</span>
				<span class="help-block">此logo是指登录后在本系统左上角显示的logo。</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2  control-label" style="text-align:left;">第三方统计代码</label>
			<div class="col-sm-8">
				<textarea style="height:150px;" class="form-control" cols="70" name="statcode" autocomplete="off"><?php  echo $settings['statcode'];?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">底部右侧信息（上）</label>
			<div class="col-sm-8">
				<textarea style="height:150px;" class="form-control" cols="70" name="footerright" autocomplete="off"><?php  echo $settings['footerright'];?></textarea>
				<span class="help-block">自定义底部右侧信息，支持HTML</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">底部左侧信息（下）</label>
			<div class="col-sm-8">
				<textarea style="height:150px;" class="form-control" cols="70" name="footerleft" autocomplete="off"><?php  echo $settings['footerleft'];?></textarea>
				<span class="help-block">自定义底部左侧信息，支持HTML</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">联系人</label>
			<div class="col-sm-8">
				<input type="text" name="person" class="form-control" value="<?php  echo $settings['person'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">联系电话</label>
			<div class="col-sm-8">
				<input type="text" name="phone" class="form-control" value="<?php  echo $settings['phone'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">QQ</label>
			<div class="col-sm-8">
				<input type="text" name="qq" class="form-control" value="<?php  echo $settings['qq'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">邮箱</label>
			<div class="col-sm-8">
				<input type="text" name="email" class="form-control" value="<?php  echo $settings['email'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">公司名称</label>
			<div class="col-sm-8">
				<input type="text" name="company" value="<?php  echo $settings['company'];?>"  class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">关于我们</label>
			<div class="col-sm-8">
				<?php  echo tpl_ueditor('companyprofile', $settings['companyprofile']);?>
				<span class="help-block">该文字显示在个人中心->关于我们中</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">详细地址</label>
			<div class="col-sm-8">
				<input type="text" name="address" value="<?php  echo $settings['address'];?>"  class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" style="text-align:left;">地理位置</label>
			<div class="col-sm-8">
				<?php  echo tpl_form_field_coordinate('baidumap', $settings['baidumap'])?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-1 col-xs-12 col-sm-10 col-md-10 col-lg-11">
				<input name="submit" type="submit" value="提交" class="btn btn-primary span3" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</form>
	<script type="text/javascript">
			$("#form1").submit(function() {
				if ($("input[name='status']:checked").val() == 1) {
					if ($("textarea[name='reason']").val() == '') {
						util.message('请填写站点关闭原因');
						return false;
					}
				}
			});
			$("input[name='status']").click(function() {
				if ($(this).val() == 1) {
					$(".reason").show();
					var reason = $("input[name='reasons']").val();
					$("textarea[name='reason']").text(reason);
				} else {
					$(".reason").hide();
				}
			});
	</script>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>