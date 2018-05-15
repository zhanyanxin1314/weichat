<?php defined('IN_IA') or exit('Access Denied');?><ul class="menu-head-top">
    <li <?php  if($_W['action']=='member') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member')?>">会员概述 <i class="fa fa-caret-right"></i></a></li>
</ul>

<div class='menu-header'>会员</div>
<ul>
    	<li <?php  if($_W['action']=='member.list') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('member/list')?>">会员管理</a></li>
</ul>

