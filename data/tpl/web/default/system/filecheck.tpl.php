<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="we7-page-title">系统文件校验</div>
<ul class="we7-page-tab"></ul>
<div class="alert we7-page-alert">
	<p><i class="wi wi-info-sign"></i><strong class="color-dark">文件校验功能可以查看您丢失，修改，添加的文件 </strong></p>
	<p><i class="wi wi-info-sign"></i><strong class="color-dark">注意: 使用文件校验的时候不会校验根目录下的&nbsp;&nbsp; /addons, &nbsp;&nbsp;/attachment, &nbsp;&nbsp; /data/tpl, &nbsp;&nbsp;/data/logs目录. </strong></p>
	<p><i class="wi wi-info-sign"></i><strong class="color-dark">注意:‘被修改’和‘未知’的文件应当引起您的注意，必须确认文件是您自己修改</strong></p>
</div>
<div class="main">
	<form action="" method="post" class="we7-form">
		
		<?php  if($do == 'check') { ?>
		<label class="control-label">校验结果</label>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 180px;"><span class="text-danger">被修改文件: <?php  echo $count_modify;?></span><a href="javascript:" onclick="$('.modify').show();$('.lose').hide();$('.unknown').hide();">[查看]</a></label>
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 180px;"><span class="text-danger">丢失文件: <?php  echo $count_lose;?></span><a href="javascript:" onclick="$('.modify').hide();$('.lose').show();$('.unknown').hide()">[查看]</a></label>
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label" style="width: 180px;"><span class="text-info">未知文件: <?php  echo $count_unknown;?></span><a href="javascript:" onclick="$('.modify').hide();$('.lose').hide();$('.unknown').show();">[查看]</a></label>
		</div>
		<div class="modify">
			<label class="control-label">被修改的文件</label>
			<?php  if(empty($modify)) { ?>
			<span class="form-control-static" >没有被被修改的文件</span>
			<?php  } ?>
			<div class="alert alert-info"  id="modify" style="<?php  if(empty($modify)) { ?>display:none;<?php  } ?>;line-height:20px;max-height: 1000px;overflow: hidden;">
				<?php  if(is_array($modify)) { foreach($modify as $modif) { ?>
				<div><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;<?php  echo $modif;?></div>
				<?php  } } ?>
			</div>
			<?php  if($count_modify > 50) { ?><a href="javascript:" onclick="$('#modify').css({'height': 'auto', 'max-height':''});$(this).hide();" class="btn btn-primary">显示全部</a><?php  } ?>
		</div>
		<div class="unknown">
		<label class="control-label">未知的文件</label>
			<?php  if(empty($unknown)) { ?>
			<div class="form-control-static">没有未知的文件</div>
			<?php  } ?>
			<div class="alert alert-info" id="unknown" style="<?php  if(empty($unknown)) { ?>display:none;<?php  } ?>line-height:20px;max-height: 1000px;overflow: hidden;">
				<?php  if(is_array($unknown)) { foreach($unknown as $unknow) { ?>
				<div><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;<?php  echo $unknow;?></div>
				<?php  } } ?>
			</div>
			<?php  if($count_unknown > 50) { ?><a href="javascript:" onclick="$('#unknown').css({'height': 'auto', 'max-height':''});$(this).hide();" class="btn btn-primary">显示全部</a><?php  } ?>
		</div>
		<div class="lose">
		<label class="control-label">丢失的文件</label>		
			<?php  if(empty($lose)) { ?>
			<div class="form-control-static">没有丢失的文件</div>
			<?php  } ?>
			<div class="alert alert-info" id="lose" style="<?php  if(empty($lose)) { ?>display:none;<?php  } ?>line-height:20px;max-height: 1000px;overflow: hidden">
				<?php  if(is_array($lose)) { foreach($lose as $los) { ?>
				<div><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;<?php  echo $los;?></div>
				<?php  } } ?>
			</div>
			<?php  if($count_lose > 50) { ?><a href="javascript:" onclick="$('#lose').css({'height': 'auto', 'max-height':''});$(this).hide();" class="btn btn-primary">显示全部</a><?php  } ?>
		</div>
		<?php  } ?>
		<div class="form-group" <?php  if($do == 'check') { ?>style="display: none;"<?php  } ?>>
			<div class="col-sm-offset-5 col-md-offset-5 col-lg-offset-5 col-xs-12 col-sm-10 col-md-10 col-lg-11">
				<a href="<?php  echo url('system/filecheck/check')?>" class="btn btn-primary">开始文件校验</a>
			</div>
		</div>
	</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
