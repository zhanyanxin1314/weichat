<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">附件设置</div>
<ul class="we7-page-tab">
	<li <?php  if($do == 'global') { ?>class="active"<?php  } ?>><a href="<?php  echo url('system/attachment/global')?>">全局设置</a></li>
	<li <?php  if($do == 'remote') { ?>class="active"<?php  } ?>><a href="<?php  echo url('system/attachment/remote')?>">远程附件</a></li>
</ul>
<?php  if($do == 'global') { ?>
<div class="clearfix">
	<form action="" method="post" class="we7-form form" id="form">
	<?php  if(!empty($upload_max_filesize) && !empty($post_max_size)) { ?>

		<div class="form-group">
			<label class="col-sm-2 control-label">PHP 环境说明</label>
			<div class="col-sm-10">
				<div class="form-control-static">1. 当前 PHP 环境允许最大单个上传文件大小为: <?php  echo $upload_max_filesize;?></div>
				<div class="form-control-static">2. 当前 PHP 环境允许最大 POST 表单大小为: <?php  echo $post_max_size;?></div>
			</div>
		</div>
	<?php  } ?>
		<h5 class="page-header">附件缩略设置</h5>
		<div class="form-group">
			<label class="col-sm-2 control-label">缩略设置</label>
			<div class="col-sm-10">
				<input type="radio" name="upload[image][thumb]" id="radio_image_thumb_0" value="0" <?php  if(empty($upload['image']['thumb'])) { ?> checked<?php  } ?> />
				<label for="radio_image_thumb_0" class="radio-inline">
					 不启用缩略
				</label>
				<input type="radio" name="upload[image][thumb]" id="radio_image_thumb_1" value="1" <?php  if(!empty($upload['image']['thumb'])) { ?> checked<?php  } ?> />
				<label for="radio_image_thumb_1" class="radio-inline">
					 启用缩略
				</label>
				<div class="help-block"></div>
			</div>
		</div>
		<div class="form-group upload-image-thumb-width-height" <?php  if(empty($upload['image']['thumb'])) { ?> style="display:none;"<?php  } ?>>
			<label class="col-sm-2 control-label"></label>
			<div class="col-sm-3">
				<div class="input-group">
					<span class="input-group-addon">宽</span>
					<input name="upload[image][width]" value="<?php  echo $upload['image']['width'];?>" type="text" class="form-control">
					<span class="input-group-addon">px</span>
				</div>
				<span class="help-block">缩略后图片 <b>最大宽度</b></span>
			</div>
		</div>
		<h5 class="page-header">图片附件设置</h5>
		<div class="form-group">
			<label class="col-sm-2 control-label">支持文件后缀</label>
			<div class="col-sm-5">
				<textarea name="upload[image][extentions]" class="form-control" rows="4"><?php  echo $upload['image']['extentions'];?></textarea>
				<div class="help-block">填写图片后缀名称, 如: jpg, 换行输入, 一行一个后缀.</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">支持文件大小</label>
			<div class="col-sm-5">
				<div class="input-group">
					<input name="upload[image][limit]" value="<?php  echo $upload['image']['limit'];?>" type="text" class="form-control">
					<span class="input-group-addon">KB</span>
				</div>
			</div>
		</div>
		<h5 class="page-header">音频视频附件设置</h5>
		<div class="form-group">
			<label class="col-sm-2 control-label">支持文件后缀</label>
			<div class="col-sm-5">
				<textarea name="upload[audio][extentions]" class="form-control" rows="4"><?php  echo $upload['audio']['extentions'];?></textarea>
				<div class="help-block">填写音频视频后缀名称, 如: mp3, 换行输入, 一行一个后缀.</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">支持文件大小</label>
			<div class="col-sm-5">
				<div class="input-group">
					<input name="upload[audio][limit]" value="<?php  echo $upload['audio']['limit'];?>" type="text" class="form-control">
					<span class="input-group-addon">KB</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-md-offset-2 col-lg-offset-2 col-sm-10 col-md-10 col-lg-10">
				<input name="submit" type="submit" value="提交" class="btn btn-primary span3" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
		<script type="text/javascript">
				$(function(){
					$('input[name="upload[image][thumb]"]').click(function(){
						if($(this).val() == 1){
							$('.upload-image-thumb-width-height').css('display', '');
						} else {
							$('.upload-image-thumb-width-height').css('display', 'none');
						}
					});
				});
		</script>
	</form>
</div>
<?php  } else if($do == 'remote') { ?>
<form action="" method="post" class="we7-form form" id="form">
	<div class="form-group">
		<div class="col-sm-12">
			<input type="radio" name="type" id="type-0" value="0" onclick="$('.remote-qiniu').hide();$('.remote-alioss').hide();$('.remote-ftp').hide();$('.remote-close').show();$('.remote-cos').hide();" <?php  if(empty($remote['type']) || $remote['type'] == '0') { ?> checked="checked" <?php  } ?>>
			<label class="radio-inline" for="type-0">
				 关闭
			</label>
			<input type="radio" name="type" id="type-1" value="1" onclick="$('.remote-qiniu').hide();$('.remote-ftp').show();$('.remote-alioss').hide();$('.remote-close').hide();$('.remote-cos').hide();" <?php  if(!empty($remote['type']) && $remote['type'] == '1') { ?> checked="checked" <?php  } ?>>
			<label class="radio-inline" for="type-1">
				 FTP服务器
			</label>
			<input type="radio" name="type" id="type-2" value="2" onclick="$('.remote-qiniu').hide();$('.remote-alioss').show();$('.remote-ftp').hide();$('.remote-close').hide();$('.remote-cos').hide();" <?php  if(!empty($remote['type']) && $remote['type'] == '2') { ?> checked="checked" <?php  } ?>>
			<label class="radio-inline" for="type-2">
				 阿里云OSS <span class="label label-success">推荐，快速稳定</span>
			</label>
			<input type="radio" name="type" id="type-3" value="3" onclick="$('.remote-qiniu').show();$('.remote-alioss').hide();$('.remote-ftp').hide();$('.remote-close').hide();$('.remote-cos').hide();" <?php  if(!empty($remote['type']) && $remote['type'] == '3') { ?> checked="checked" <?php  } ?>>
			<label class="radio-inline" for="type-3">
				 七牛云存储 <span class="label label-success">推荐，快速稳定</span>
			</label>
			<input type="radio" name="type" id="type-4" value="4" onclick="$('.remote-qiniu').hide();$('.remote-alioss').hide();$('.remote-ftp').hide();$('.remote-close').hide();$('.remote-cos').show();" <?php  if(!empty($remote['type']) && $remote['type'] == '4') { ?> checked="checked" <?php  } ?>>
			<label class="radio-inline" for="type-4">
				 腾讯云存储 <span class="label label-success">推荐，快速稳定</span>
			</label>
			<span class="help-block"></span>
		</div>
	</div>
	<div class="remote-ftp" <?php  if(empty($remote['type']) || $remote['type'] != '1') { ?> style="display:none;" <?php  } ?>>
		<div class="form-group">
			<label class="col-sm-2 control-label">启用SSL连接</label>
			<div class="col-sm-9">
				<input type="radio" id="ftp[ssl]-1" name="ftp[ssl]" value="1" <?php  if(!empty($remote['ftp']['ssl'])) { ?> checked="checked" <?php  } ?>>
				<label class="radio-inline" for="ftp[ssl]-1">
					 是
				</label>
				<input type="radio" id="ftp[ssl]-0" name="ftp[ssl]" value="0" <?php  if(empty($remote['ftp']['ssl'])) { ?> checked="checked" <?php  } ?>>
				<label class="radio-inline" for="ftp[ssl]-0">
					否
				</label>
				<span class="help-block">注意：选择是后，FTP 服务器必须开启了 SSL，一般情况选择否即可</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">FTP服务器地址</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[host]" class="form-control" value="<?php  echo $remote['ftp']['host'];?>" />
				<span class="help-block">可以是 FTP 服务器的 IP 地址或域名</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">FTP服务器端口</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[port]" class="form-control" value="<?php  if(empty($remote['ftp']['port'])) { ?>21<?php  } else { ?><?php  echo $remote['ftp']['port'];?><?php  } ?>" />
				<span class="help-block">默认为21</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">FTP帐号</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[username]" class="form-control" value="<?php  echo $remote['ftp']['username'];?>" />
				<span class="help-block">该帐号必须具有以下权限：读取文件、写入文件、删除文件、创建目录、子目录继承</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">FTP密码</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[password]" class="form-control encrypt" value="<?php  echo $remote['ftp']['password'];?>" />
				<span class="help-block">基于安全考虑将只显示 FTP 密码的第一位和最后一位，中间显示八个 * 号</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">被动模式(pasv)连接：</label>
			<div class="col-sm-9">
				<label class="radio-inline">
					<input type="radio" name="ftp[pasv]" value="1" <?php  if(!empty($remote['ftp']['pasv'])) { ?> checked="checked" <?php  } ?>> 是
				</label>
				<label class="radio-inline">
					<input type="radio" name="ftp[pasv]" value="0" <?php  if(empty($remote['ftp']['pasv'])) { ?> checked="checked" <?php  } ?>> 否
				</label>
				<span class="help-block">一般情况下非被动模式即可，如果存在上传失败问题，可尝试打开此设置</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">远程附件目录</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[dir]" class="form-control" value="<?php  echo $remote['ftp']['dir'];?>" />
				<span class="help-block">远程附件目录的绝对路径或相对于 FTP 主目录的相对路径，结尾不要加斜杠 "/" , 例如：/attachment</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">远程访问URL</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[url]" class="form-control" value="<?php  echo $remote['ftp']['url'];?>" />
				<span class="help-block">支持 HTTP 和 FTP 协议，结尾不要加斜杠 "/" ; 例如：http://mydomin.com/attachment 如果使用 FTP 协议，FTP 服务器必须支持 PASV 模式，为了安全起见，
				使用 FTP 连接的帐号不要设置可写权限和列表权限</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">FTP传输超时时间</label>
			<div class="col-sm-9">
				<input type="text" name="ftp[overtime]" class="form-control" value="<?php  if(empty($remote['ftp']['overtime'])) { ?>0<?php  } else { ?><?php  echo $remote['ftp']['overtime'];?><?php  } ?>" />
				<span class="help-block">单位：秒，0为服务器默认</span>
			</div>
		</div>
		<div class="form-group">
			<div class="">
				<button name="submit" class="btn btn-primary" value="submit">保存配置</button>
				<button name="button" type="button" class="btn btn-info js-checkremoteftp" value="check">测试配置（无需保存）</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
	<div class="remote-alioss" <?php  if(empty($remote['type']) || $remote['type'] != '2') { ?> style="display:none;" <?php  } ?>>
		<div class="alert alert-info">
			启用阿里oss后，请把/attachment目录（不包括此目录）下的子文件及子目录上传至阿里云oss, 相关工具：<br>
			<ul class="link-list">
			<li><a target="_blank" href="http://market.aliyun.com/products/52738003/cmgj000304.html?spm=5176.383663.9.21.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.21">cloudfs4oss(ECS挂载文件盘工具)</a></li>
			<li><a target="_blank" href="http://market.aliyun.com/products/53690006/cmgj000281.html?spm=5176.383663.9.22.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.22">官方推荐OSS客户端工具（Windows版）</a></li>
			<li><a target="_blank" href="http://market.aliyun.com/products/53690006/cmgj000282.html?spm=5176.383663.9.23.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.23">官方推荐OSS客户端工具（Mac版）</a></li>
			<li><a target="_blank" href="http://market.aliyun.com/products/53690006/cmgj000208.html?spm=5176.383663.9.24.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.24">Ftp4ossServer（OSS的FTP云工具）</a></li>
			<li><a target="_blank" href="http://bbs.aliyun.com/read/239565.html?spm=5176.383663.9.25.faitxp&amp;pos=2" class="product-grey-font" data-spm-anchor-id="5176.383663.9.25">OSS图片服务Demo工具</a></li>
			<li><a target="_blank" href="http://docs.aliyun.com/?spm=5176.383663.9.26.faitxp#/pub/oss/utilities/osscmd&amp;install" class="product-grey-font" data-spm-anchor-id="5176.383663.9.26">批量上传工具(Python)版</a></li>
			<li><a target="_blank" href="https://docs.aliyun.com/?spm=5176.383663.9.27.faitxp#/pub/oss/utilities/oss-import&amp;index" class="product-grey-font" data-spm-anchor-id="5176.383663.9.27">OSS数据迁移工具-OSS Import</a></li>
			<li><a target="_blank" href="http://market.aliyun.com/products/52738004/cmfw000394.html?spm=5176.383663.9.28.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.28">海量数据迁移至OSS服务</a></li>
			<li><a target="_blank" href="http://bbs.aliyun.com/read/247023.html?spm=5176.383663.9.29.faitxp" class="product-grey-font" data-spm-anchor-id="5176.383663.9.29">更多官方推荐工具</a></li>
			</ul>

		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Access Key ID</label>
			<div class="col-sm-9">
				<input type="text" name="alioss[key]" class="form-control" value="<?php  echo $remote['alioss']['key'];?>" placeholder="" />
				<span class="help-block">
					Access Key ID是您访问阿里云API的密钥，具有该账户完全的权限，请您妥善保管。
				</span>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Access Key Secret</label>
			<div class="col-sm-9">
				<input type="text" name="alioss[secret]" class="form-control encrypt" value="<?php  echo $remote['alioss']['secret'];?>" placeholder="" />
				<span class="help-block">
					Access Key Secret是您访问阿里云API的密钥，具有该账户完全的权限，请您妥善保管。(填写完Access Key ID 和 Access Key Secret 后请选择bucket)
				</span>
			</div>
		</div>
		<div class="form-group" id="bucket" <?php  if(empty($remote['alioss']['key'])) { ?>style="display: none;<?php  } ?>">
			<label class="col-sm-2 control-label">Bucket选择</label>
			<div class="col-sm-9">
				<select name="alioss[bucket]" class="form-control">
				</select>
				<span class="help-block">
					完善Access Key ID和Access Key Secret资料后可以选择存在的Bucket(请保证bucket为可公共读取的)，否则请手动输入。
				</span>
			</div>
		</div>
		<div class="form-group">
		 <label class="col-sm-2 control-label">自定义URL</label>
			<div class="col-sm-9">
				<input type="text" name="custom[url]" class="form-control" <?php  if(!strexists($remote['alioss']['url'],'aliyuncs.com') && $_W['setting']['remote']['type'] == 2) { ?>value="<?php  echo $remote['alioss']['url'];?>"<?php  } ?> placeholder="默认URL不需要填写"/>
					<span class="help-block">
						阿里云oss支持用户自定义访问域名，如果自定义了URL则用自定义的URL，如果未自定义，则用系统生成出来的URL。注：自定义url开头加http://或https://结尾不加 ‘/’例：http://abc.com
					</span>
			</div>
		</div>
		<div class="form-group">
			<div class="">
				<button name="submit" class="btn btn-primary" value="submit">保存配置</button>
				<button name="button" type="button" class="btn btn-info js-checkremoteoss" value="check">测试配置（无需保存）</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
	<div class="remote-qiniu" <?php  if(empty($remote['type']) || $remote['type'] != '3') { ?> style="display:none;" <?php  } ?>>
	<div class="alert alert-info">
		启用七牛云存储后，请把/attachment目录（不包括此目录）下的子文件及子目录上传至七牛云存储<br>七牛 是目前国内 云存储技术最强 的 技术型公司，每月免费10G+ 融合CDN加速，无论是性能、速度、价格，均推荐使用七牛云存储。
		<ul class="link-list" style="margin-top:8px;">
			<li><a target="_blank" href="https://portal.qiniu.com/signup?code=3leigl4lgsitu" class="product-grey-font" >注册【七牛云存储】</a></li>
		</ul>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Accesskey</label>
		<div class="col-sm-9">
			<input type="text" name="qiniu[accesskey]" class="form-control" value="<?php  echo $remote['qiniu']['accesskey'];?>" placeholder="" />
			<span class="help-block">用于签名的公钥</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Secretkey</label>
		<div class="col-sm-9">
			<input type="text" name="qiniu[secretkey]" class="form-control encrypt" value="<?php  echo $remote['qiniu']['secretkey'];?>" placeholder="" />
			<span class="help-block">用于签名的私钥</span>
		</div>
	</div>
	<div class="form-group" id="qiniubucket">
		<label class="col-sm-2 control-label">Bucket</label>
		<div class="col-sm-9">
			<input type="text" name="qiniu[bucket]" class="form-control" value="<?php  echo $remote['qiniu']['bucket'];?>" placeholder="" />
			<span class="help-block">请保证bucket为可公共读取的</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Url</label>
		<div class="col-sm-9">
			<input type="text" name="qiniu[url]" class="form-control" value="<?php  echo $remote['qiniu']['url'];?>" placeholder="" />
			<span class="help-block">七牛支持用户自定义访问域名。注：url开头加http://或https://结尾不加 ‘/’例：http://abc.com</span>
		</div>
	</div>
	<div class="form-group">
		<div class="">
			<button name="submit" class="btn btn-primary" value="submit">保存配置</button>
			<button name="button" type="button" class="btn btn-info js-checkremoteqiniu" value="check">测试配置（无需保存）</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</div>
	</div>
	<div class="remote-cos" <?php  if(empty($remote['type']) || $remote['type'] != '4') { ?> style="display:none;" <?php  } ?>>
	<div class="alert alert-info">
		启用腾讯云cos对象存储后，请把/attachment目录（不包括此目录）下的子文件及子目录上传至腾讯云存储, 相关工具：<br>
		<ul class="link-list">
			<li><a target="_blank" href="https://console.qcloud.com/cos/bucket" class="product-grey-font" >腾讯云存储</a></li>
		</ul>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">APPID</label>
		<div class="col-sm-9">
			<input type="text" name="cos[appid]" class="form-control" value="<?php  echo $remote['cos']['appid'];?>" placeholder="" />
			<span class="help-block">APPID 是您项目的唯一ID</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">SecretID</label>
		<div class="col-sm-9">
			<input type="text" name="cos[secretid]" class="form-control" value="<?php  echo $remote['cos']['secretid'];?>" placeholder="" />
			<span class="help-block">SecretID 是您项目的安全秘钥，具有该账户完全的权限，请妥善保管</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">SecretKEY</label>
		<div class="col-sm-9">
			<input type="text" name="cos[secretkey]" class="form-control encrypt" value="<?php  echo $remote['cos']['secretkey'];?>" placeholder="" />
			<span class="help-block">SecretKEY 是您项目的安全秘钥，具有该账户完全的权限，请妥善保管</span>
		</div>
	</div>
	<div class="form-group" id="cosbucket">
		<label class="col-sm-2 control-label">Bucket</label>
		<div class="col-sm-9">
			<input type="text" name="cos[bucket]" class="form-control" value="<?php  echo $remote['cos']['bucket'];?>" placeholder="" />
			<span class="help-block">请保证bucket为可公共读取的</span>
		</div>
	</div>
	<div class="form-group" id="cos_local">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">bucket所在区域</label>
		<div class="col-sm-9 col-xs-12">
			<select class="form-control" name="cos[local]">
				<option value="" <?php  if($remote['cos']['local'] == '') { ?>selected<?php  } ?>>无</option>
				<option value="tj" <?php  if($remote['cos']['local'] == 'tj') { ?>selected<?php  } ?>>华北</option>
				<option value="sh" <?php  if($remote['cos']['local'] == 'sh') { ?>selected<?php  } ?>>华东</option>
				<option value="gz" <?php  if($remote['cos']['local'] == 'gz') { ?>selected<?php  } ?>>华南</option>
			</select>
			<span class="help-block">选择bucket对应的区域，如果没有选择无</span>
		</div>
	</div>
	<div class="form-group" >
		<label class="col-sm-2 control-label">Url</label>
		<div class="col-sm-9">
			<input type="text" name="cos[url]" class="form-control" value="<?php  echo $remote['cos']['url'];?>" placeholder="" />
			<span class="help-block">腾讯云支持用户自定义访问域名。注：url开头加http://或https://结尾不加 ‘/’例：http://abc.com</span>
		</div>
	</div>
	<div class="form-group">
		<div class="">
			<button name="submit" class="btn btn-primary" value="submit">保存配置</button>
			<button name="button" type="button" class="btn btn-info js-checkremotecos" value="check">测试配置（无需保存）</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</div>
	</div>
	<div class="remote-close" <?php  if(!empty($remote['type'])) { ?> style="display:none;" <?php  } ?>>
		<div class="form-group">
			<div class="">
				<button name="submit" class="btn btn-primary" value="submit">保存配置</button>
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>

</form>

<script type="text/javascript">
	$(function() {
		$('.encrypt').val(function() {
			return util.encrypt($(this).val());
		});
	});
	$('.js-checkremoteftp').on('click', function(){
		var ssl =  parseInt($(':radio[name="ftp[ssl]"]:checked').val());
		var pasv = parseInt($(':radio[name="ftp[pasv]"]:checked').val());
		var param = {
			'ssl' : ssl,
			'host' : $.trim($(':text[name="ftp[host]"]').val()),
			'username'  : $.trim($(':text[name="ftp[username]"]').val()),
			'password' : $.trim($(':text[name="ftp[password]"]').val()),
			'pasv' : pasv,
			'dir': $.trim($(':text[name="ftp[dir]"]').val()),
			'url': $.trim($(':text[name="ftp[url]"]').val()),
			'port' : parseInt($(':text[name="ftp[port]"]').val()),
			'overtime' : parseInt($(':text[name="ftp[overtime]"]').val())
		};
		$.post("<?php  echo url('system/attachment/ftp')?>", param, function(data){
			var data = $.parseJSON(data);
			if(data.message.errno == 0) {
				util.message(data.message.message);
				return false;
			}
			if(data.message.errno < 0) {
				util.message(data.message.message);
				return false;
			}
		});
	});
	$('.js-checkremoteoss').on('click', function(){
		var bucket = $.trim($('select[name="alioss[bucket]"]').val());
		if (bucket == '') {
			bucket = $.trim($(':text[name="alioss[bucket]"]').val());
		}
		var param = {
			'key' : $.trim($(':text[name="alioss[key]"]').val()),
			'secret' : $.trim($(':text[name="alioss[secret]"]').val()),
			'url'  : $.trim($(':text[name="custom[url]"]').val()),
			'bucket' : bucket
		};
		$.post("<?php  echo url('system/attachment/oss')?>",param, function(data) {
			var data = $.parseJSON(data);
			if(data.message.errno == 0) {
				util.message('配置成功');
				return false;
			}
			if(data.message.errno < 0) {
				util.message(data.message.message);
				return false;
			}
		});
	});
	$('.js-checkremoteqiniu').on('click', function(){
		var key = $.trim($(':text[name="qiniu[accesskey]"]').val());
		if (key == '') {
			util.message('请填写Accesskey');
			return false;
		}
		var secret = $.trim($(':text[name="qiniu[secretkey]"]').val());
		if (secret == '') {
			util.message('请填写Secretkey');
			return false;
		}
		var param = {
			'accesskey' : $.trim($(':text[name="qiniu[accesskey]"]').val()),
			'secretkey' : $.trim($(':text[name="qiniu[secretkey]"]').val()),
			'url'  : $.trim($(':text[name="qiniu[url]"]').val()),
			'bucket' :  $.trim($(':text[name="qiniu[bucket]"]').val())
		};
		$.post("<?php  echo url('system/attachment/qiniu')?>",param, function(data) {
			var data = $.parseJSON(data);
			if(data.message.errno == 0) {
				util.message('配置成功');
				return false;
			}
			if(data.message.errno < 0) {
				util.message(data.message.message);
				return false;
			}
		});
	});
	$('.js-checkremotecos').on('click', function(){
		var appid = $.trim($(':text[name="cos[appid]"]').val());
		if (appid == '') {
			util.message('请填写APPID');
			return false;
		}
		var secretid = $.trim($(':text[name="cos[secretid]"]').val());
		if (secretid == '') {
			util.message('请填写secretid');
			return false;
		}
		var secretkey = $.trim($(':text[name="cos[secretkey]"]').val());
		if (secretkey == '') {
			util.message('请填写Secretkey');
			return false;
		}
		var bucket = $.trim($(':text[name="cos[bucket]"]').val());
		if (bucket == '') {
			util.message('请填写bucket');
			return false;
		}
		var url = $.trim($(':text[name="cos[url]"]').val());
		var local = $('[name="cos[local]"]').val();
		var param = {
			'appid' : appid,
			'secretid' : secretid,
			'secretkey'  : secretkey,
			'bucket' :  bucket,
			'url' : url,
			'local' : local
		};
		$.post("<?php  echo url('system/attachment/cos')?>",param, function(data) {
			var data = $.parseJSON(data);
			if(data.message.errno == 0) {
				util.message('配置成功');
				return false;
			}
			if(data.message.errno < 0) {
				util.message(data.message.message);
				return false;
			}
		});
	});
	var alibucket = '<?php  echo $_W['setting']['remote']['alioss']['bucket'];?>';
	var buck =  function() {
		var key = $(':text[name="alioss[key]"]').val();
		var secret = $(':text[name="alioss[secret]"]').val();
		if (secret.indexOf('*') > 0) {
			secret = '<?php  echo $_W['setting']['remote']['alioss']['secret'];?>';
		}
		if (key == '' || secret == '') {
			$('#bucket').hide();
			return false;
		}
		$.post("<?php  echo url('system/attachment/buckets')?>", {'key' : key, 'secret' : secret}, function(data) {
			try {
				var data = $.parseJSON(data);
			} catch (error) {
				util.message('Access Key ID 或 Access Key Secret 填写错误，请重新填写。', '', 'error');
				$('#bucket').hide();
				$('select[name="alioss[bucket]"]').val('');
				return false;
			}

			if (data.message.errno < 0 ) {
				return false;
			} else {
				$('#bucket').show();
				var bucket = $('select[name="alioss[bucket]"]');
				bucket.empty();
				var buckets = eval(data.message.message);
				for (var i in buckets) {
					var selected = alibucket == buckets[i]['name'] || alibucket ==  buckets[i]['name'] + '@@' + buckets[i]['location'] ? 'selected' : '';
					bucket.append('<option value="' + buckets[i]['name'] + '@@' + buckets[i]['location'] + '"' + selected + '>'+buckets[i]['loca_name'] + '</option>');
				}
			}
		});
	};
	buck();
	$(':text[name="alioss[secret]"]').blur(function() {buck();});
	$('form').submit(function() {
		if ($('[name="type"]:checked').val() == 2 && ($('select[name="alioss[bucket]"]').val() == null || $('select[name="alioss[bucket]"]').val() == '')) {
			util.message('请完善信息后再保存设置！');
			return false;
		}
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
