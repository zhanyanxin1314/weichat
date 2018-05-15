<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Picker_EweiShopV2Page extends MobilePage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$action = trim($_GPC['action']);
		$goods = pdo_fetch('select id,thumb,title,marketprice,total,unit,showtotal,type,
				    maxprice, minprice,unite_total from ' . tablename('ewei_shop_goods') .
				    ' where id=:id and uniacid=:uniacid limit 1', 
				    array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($goods)) 
		{
			show_json(0);
		}
		$goods = set_medias($goods, 'thumb');
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		if (empty($openid)) 
		{
			show_json(4);
		}
		$minprice = $goods['minprice'];
		$maxprice = $goods['maxprice'];
		$goods['minprice'] = number_format($minprice, 2);
		$goods['maxprice'] = number_format($maxprice, 2);
		show_json(1, array('goods' => $goods));
	}
}
?>
