<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>
	.order-list a {
		position: relative;
	}
	.order-list span  {
	 
	float:right;margin-right:20px;
	}
</style>
<ul class="menu-head-top">
    <li <?php  if($_W['action']=='order') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('order')?>">订单概述 <i class="fa fa-caret-right"></i></a></li>
</ul>

<div class='menu-header'>订单</div>
<ul class='order-list'>

    <li <?php  if($_W['routes']=='order.list' && $_GPC['status']=='' && $_GPC['refund']!='1') { ?>class="active"<?php  } ?>>
        <a href="<?php  echo webUrl('order/list')?>" >全部订单</a>
    </li>
    
</ul>

