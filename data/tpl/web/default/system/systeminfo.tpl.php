<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">系统信息</div>
<ul class="we7-page-tab"></ul>
	<div class="alert alert-info">
		<i class="fa fa-exclamation-triangle"></i> 加入QQ群一起来学习微信运营吧！
		<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=405778176eee9d6bd1d25ee087bbdc8e9cf25e0e73c4f75c4544a21dd9990a9d"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="微信公众号管理系统" title="微信公众号管理系统"></a> 
	</div>
<div class="main">
		
	<table class="table we7-table table-hover site-list">
		<col widtd="120px" />
		<col widtd="120px" />
		<tr>
			<th colspan="2" class="text-left">用户信息</th>
		</tr>
		<tr>
			<td class="text-left">用户ID</td>
			<td class="text-left"><?php  echo $info['uid'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前公众号</td>
			<td class="text-left"><?php  echo $info['account'];?></td>
		</tr>
	</table>
	
	<table class="table we7-table table-hover site-list">
		<tr>
			<th colspan="2" class="text-left">系统信息</th>
		</tr>
		<tr>
			<td class="text-left">系统程序版本</td>
			<td class="text-left">WeEngine <?php  echo IMS_VERSION;?> Release <?php  echo IMS_RELEASE_DATE;?> &nbsp; &nbsp;
				<a href="https://www.wazyb.com" target="_blank" style="color: #428bca;">查看最新版本</a>
			</td>
		</tr>
		<tr>
			<td class="text-left">产品系列</td>
			<td class="text-left">
				<?php  if(IMS_FAMILY == 'v') { ?>
				您的产品是开源版, 没有购买商业授权, 不能用于商业用途
				<?php  } else if(IMS_FAMILY == 's') { ?>
				您的产品是授权版
				<?php  } else if(IMS_FAMILY == 'x') { ?>
				您的产品是商业版
				<?php  } else { ?>
				您的产品是单版
				<?php  } ?>
			</td>
		</tr>
		<tr>
			<td class="text-left">服务器系统</td>
			<td class="text-left"><?php  echo $info['os'];?></td>
		</tr>
		<tr>
			<td class="text-left">PHP版本 </td>
			<td class="text-left">PHP Version <?php  echo $info['php'];?></td>
		</tr>
		<tr>
			<td class="text-left">服务器软件</td>
			<td class="text-left"><?php  echo $info['sapi'];?></td>
		</tr>
		<tr>
			<td class="text-left">服务器 MySQL 版本</td>
			<td class="text-left"><?php  echo $info['mysql']['version'];?></td>
		</tr>
		<tr>
			<td class="text-left">上传许可</td>
			<td class="text-left"><?php  echo $info['limit'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前数据库尺寸</td>
			<td class="text-left"><?php  echo $info['mysql']['size'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前附件根目录</td>
			<td class="text-left"><?php  echo $info['attach']['url'];?></td>
		</tr>
		<tr>
			<td class="text-left">当前附件尺寸</td>
			<td class="text-left"><?php  echo $info['attach']['size'];?></td>
		</tr>
	</table>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
