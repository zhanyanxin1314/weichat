<?php defined('IN_IA') or exit('Access Denied');?><div class='menu-header'><?php  echo $this->plugintitle?></div>
<ul>
    <li <?php  if($_W['routes']=='commission.agent') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/agent')?>">分销商管理</a></li>
    <li <?php  if($_W['routes']=='commission.level') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/level')?>">分销商等级</a></li>

    

</ul>
<style type='text/css'>
	.commission-list a {
		position: relative;
	}
	.commission-list span  {
	 
	float:right;margin-right:20px;
	}
</style>
<div class='menu-header'>提现申请</div>
<ul class='commission-list'>
    <?php if(cv('commission.apply.view1')) { ?><li <?php  if($_W['routes']=='commission.apply' && $_GPC['status']==1) { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/apply',array('status'=>1))?>">待审核的</a></li><?php  } ?>
    <?php if(cv('commission.apply.view2')) { ?><li <?php  if($_W['routes']=='commission.apply' && $_GPC['status']==2) { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/apply',array('status'=>2))?>">待打款的</a></li><?php  } ?>
    <?php if(cv('commission.apply.view3')) { ?><li <?php  if($_W['routes']=='commission.apply' && $_GPC['status']==3) { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/apply',array('status'=>3))?>">已打款的</span></a></li><?php  } ?>
    
</ul>
<div class="menu-header">设置</div>

<ul>
<li <?php  if($_W['routes']=='commission.set') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('commission/set')?>">基础设置</a></li>
	
</ul>
