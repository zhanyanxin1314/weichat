<?php defined('IN_IA') or exit('Access Denied');?><?php  $no_left=true;?>
<?php  if(IS_EWEI_SHOPV2_SYSTEM) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<?php  } else { ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header_base', TEMPLATE_INCLUDEPATH)) : (include template('_header_base', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>



<div class="panel panel-default" style="width:600px;margin:100px auto;padding:20px">


    <div class="panel-body">

		<div class="row">
			<div class="col-sm-2">
				<?php  if($type=='error') { ?>
                <i class='icon icon-infofill' style="font-size:72px;color:#f90"></i>
                <?php  } else if($type=='success') { ?>
                <i class='icon icon-roundcheckfill ' style="font-size:72px;color:#04be02"></i>
                <?php  } else { ?>
                <i class='icon icon-infofill ' style="font-size:72px;color:#ccc"></i>
                <?php  } ?>
			</div>

			<div class="col-sm-10">

				<div class="row" style="font-size:14px;padding-top:25px;padding-left:10px;word-break: break-all">
					<?php  echo $message;?>
				</div>
				 
			</div>
		</div>
		
		<div class="row">
			 
			<div class="col-sm-10 pull-right text-right">

				 
				<div class="row" style="border-top:1px solid #f8f8f8;padding-top:10px;padding-left:10px">
					<?php  if($redirect) { ?>
					<p><a href="<?php  echo $redirect;?>" class='btn btn-default'>如果你的浏览器没有自动跳转，请点击此链接</a></p>
					<script type="text/javascript">
						setTimeout(function () {
							location.href = "<?php  echo $redirect;?>";
						}, 2000);
					</script>
					<?php  } else { ?>
						<a href='javascript:history.back()' class='btn btn-default' style="width: 100px;"><?php  if(empty($buttontext)) { ?>确认<?php  } else { ?><?php  echo $buttontext;?><?php  } ?></a>
					<?php  } ?>
					
					
				</div>

			</div>
		</div>
		
	</div>
</div>


<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
