<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header_base', TEMPLATE_INCLUDEPATH)) : (include template('_header_base', TEMPLATE_INCLUDEPATH));?>
<style>
    .deg180{
        transform:rotate(180deg);
        -ms-transform:rotate(180deg); 	/* IE 9 */
        -moz-transform:rotate(180deg); 	/* Firefox */
        -webkit-transform:rotate(180deg); /* Safari 和 Chrome */
        -o-transform:rotate(180deg); 	/* Opera */
    }

    .nav.navbar-right {
        width:90px !important;
    }

</style>
<div class="navbar-collapse collapse" id="navbar">
    <?php  $routes = explode(".", $GLOBALS['_W']['routes']);?>
    <ul class="nav navbar-nav gray-bg">

	<li <?php  if($_W['controller']=='goods') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('goods')?>"> 商品</a></li>
        <li <?php  if($_W['controller']=='member') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('member')?>"> 会员</a></li>
        <li <?php  if($_W['controller']=='order') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('order.list')?>"> 订单</a></li>
        <li <?php  if($_W['controller']=='plugins' || !empty($_W['plugin'])) { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('commission.agent')?>">分销管理</a></li>
	<li <?php  if($_W['controller']=='sysset') { ?> class="active"<?php  } ?>><a href="<?php  echo webUrl('sysset')?>"> 设置</a></li>

    </ul>
    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown" style="position:relative;" onclick="$(this).find('span').toggleClass('deg180')">
                <name style="width: 80px;white-space:nowrap;overflow:hidden;text-overflow: ellipsis;display: block;text-align: center"><?php  echo $_W['uniaccount']['name'];?></name>
                <span class="caret" style="position: absolute;top: 22px;right: 12px;"></span></a>
            <ul role="menu" class="dropdown-menu">
                <li><a href="<?php  echo webUrl('sysset/account')?>"><i class="icon icon-similar"></i>  切换公众号</a></li>
                <?php  if($_W['role'] == 'manager' || $_W['role'] == 'founder') { ?>
                <li><a href="./index.php?c=account&a=post&uniacid=<?php  echo $GLOBALS['_W']['uniacid'];?>&acid=<?php  echo $GLOBALS['_W']['acid'];?>" target="_blank"><i class="icon icon-wechat"></i>  编辑公众号</a></li>
                <li><a href="<?php  echo webUrl('sysset/payset')?>"><i class="icon icon-pay"></i>  支付方式</a></li>
                <?php  } ?>

                <?php if(cv('perm')) { ?>
                <li class="divider"></li>
                <li><a href="<?php  echo webUrl('perm')?>"><i class="icon icon-person2"></i> 权限管理</a></li>
                <?php  } ?>
                <li><a href="./index.php?c=user&a=profile&" target="_blank"><i class="icon icon-lock"></i>  修改密码</a></li>
                <li><a href="./index.php?c=account&a=display&" target="_blank"><i class="icon icon-back"></i>  返回系统</a></li>
            </ul>
        </li>
    </ul>
</div>
</nav>
</div>
<div class='wrapper main-wrapper wrapper-content '>
    <?php  if($no_left) { ?>
    <div class="page-content" style="width:1000px">
        <?php  } else { ?>
        <div class="page-menubar">
            <?php  echo $this->frame_menus()?>
        </div>
        <div class="page-content">
            <?php  } ?>
        
