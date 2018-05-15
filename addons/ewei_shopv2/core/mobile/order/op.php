<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Op_EweiShopV2Page extends MobileLoginPage 
{
	public function finish() 
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select id,status,openid,ordersn,price from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
		if (empty($order)) 
		{
			show_json(0, '订单未找到');
		}
		if ($order['status'] != 2) 
		{
			show_json(0, '订单不能确认收货');
		}
		pdo_update('ewei_shop_order', array('status' => 3, 'finishtime' => time(), 'refundstate' => 0), array('id' => $order['id'], 'uniacid' => $_W['uniacid']));
		if (p('commission')) 
		{
			p('commission')->checkOrderFinish($orderid);
		}
		show_json(1, array('url' => mobileUrl('order', array('status' => 3))));
	}
}
?>
