<?php defined('IN_IA') or exit('Access Denied');?><div class='menu-header'>商城</div>
<ul>
   <li <?php  if($_W['routes']=='sysset.shop' ||$_W['routes']=='sysset') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('sysset/shop')?>">基础设置</a></li>
   <li <?php  if($_W['routes']=='sysset.close') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('sysset/close')?>">商城状态</a></li>
</ul>

<div class='menu-header'>交易</div>
<ul>
 <li  <?php  if($_W['routes']=='sysset.payset') { ?>class="active"<?php  } ?>><a href="<?php  echo webUrl('sysset/payset')?>">支付方式</a></li>
</ul>

