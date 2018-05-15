<?php defined('IN_IA') or exit('Access Denied');?><style type='text/css'>
	.goods-list a {
		position: relative; 
	}
	.goods-list span  {
	float:right;margin-right:20px;
	 
	}
</style>

<?php if(cv('goods')) { ?>
<div class='menu-header'>商品列表</div>
<ul class='goods-list'>
    <li <?php  if($_GPC['goodsfrom'] == 'sale') { ?> class="active" <?php  } ?>><a href="<?php  echo webUrl('goods',array('goodsfrom'=>'sale'))?>">商品列表</a></li>

</ul>
<?php  } ?>

  
