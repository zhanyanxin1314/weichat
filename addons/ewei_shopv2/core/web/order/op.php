<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Op_EweiShopV2Page extends WebPage 
{
	protected function opData() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($item)) 
		{
			if ($_W['isajax']) 
			{
				show_json(0, '未找到订单!');
			}
			$this->message('未找到订单!', '', 'error');
		}
		return array('id' => $id, 'item' => $item);
	}
	public function pay($a = array(), $b = array()) 
	{
		global $_W;
		global $_GPC;
		$opdata = $this->opData();
		extract($opdata);
		if (1 < $item['status']) 
		{
			show_json(0, '订单已付款，不需重复付款！');
		}
		else 
		{
			pdo_update('ewei_shop_order', array('status' => 1, 'paytype' => 11, 'paytime' => time()), array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			if (p('commission')) 
			{
				p('commission')->checkOrderPay($item['id']);
			}
		}
		header('location:'.  webUrl('order/list'));
	}
	public function finish() 
	{
		global $_W;
		global $_GPC;
		$opdata = $this->opData();
		extract($opdata);
		pdo_update('ewei_shop_order', array('status' => 3, 'finishtime' => time()), array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
		if (p('commission')) 
		{
			p('commission')->checkOrderFinish($item['id']);
		}
		header('location:'.  webUrl('order/list'));
	}
	public function fetch() 
	{
		global $_W;
		global $_GPC;
		$opdata = $this->opData();
		extract($opdata);
		if ($item['status'] != 1) 
		{
			show_json(0, '订单未付款，无法确认取货！');
		}
		$time = time();
		$d = array('status' => 3, 'sendtime' => $time, 'finishtime' => $time);
		pdo_update('ewei_shop_order', $d, array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
		if (p('commission')) 
		{
			p('commission')->checkOrderFinish($item['id']);
		}
		show_json(1);
	}
	public function send() 
	{
		global $_W;
		global $_GPC;
		$opdata = $this->opData();
		extract($opdata);
		if (empty($item['addressid'])) 
		{
			show_json(0, '无收货地址，无法发货！');
		}
		if ($_W['ispost']) 
		{
			if (!(empty($_GPC['isexpress'])) && empty($_GPC['expresssn'])) 
			{
				show_json(0, '请输入快递单号！');
			}
			$time = time();
			$data = array('sendtype' => (0 < $item['sendtype'] ? $item['sendtype'] : intval($_GPC['sendtype'])), 'express' => trim($_GPC['express']), 'expresscom' => trim($_GPC['expresscom']), 'expresssn' => trim($_GPC['expresssn']), 'sendtime' => $time);
			if ((intval($_GPC['sendtype']) == 1) || (0 < $item['sendtype'])) 
			{
				if (empty($_GPC['ordergoodsid'])) 
				{
					show_json(0, '请选择发货商品！');
				}
				$ogoods = array();
				$ogoods = pdo_fetchall('select sendtype from ' . tablename('ewei_shop_order_goods') . "\r\n" . '                    where orderid = ' . $item['id'] . ' and uniacid = ' . $_W['uniacid'] . ' order by sendtype desc ');
				$senddata = array('sendtype' => $ogoods[0]['sendtype'] + 1, 'sendtime' => $data['sendtime']);
				$data['sendtype'] = $ogoods[0]['sendtype'] + 1;
				$goodsid = $_GPC['ordergoodsid'];
				foreach ($goodsid as $key => $value ) 
				{
					pdo_update('ewei_shop_order_goods', $data, array('id' => $value, 'uniacid' => $_W['uniacid']));
				}
				$send_goods = pdo_fetch('select * from ' . tablename('ewei_shop_order_goods') . "\r\n" . '                    where orderid = ' . $item['id'] . ' and sendtype = 0 and uniacid = ' . $_W['uniacid'] . ' limit 1 ');
				if (empty($send_goods)) 
				{
					$senddata['status'] = 2;
				}
				$senddata['refundid'] = 0;
				pdo_update('ewei_shop_order', $senddata, array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			}
			else 
			{
				$data['status'] = 2;
				$data['refundid'] = 0;
				pdo_update('ewei_shop_order', $data, array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			}
			header('location:'.  webUrl('order/list'));
		}
		$order_goods = pdo_fetchall('select og.id,g.title,g.thumb,og.sendtype,g.ispresell from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $item['id']));
		$address = iunserializer($item['address']);
		if (!(is_array($address))) 
		{
			$address = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_address') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $item['addressid'], ':uniacid' => $_W['uniacid']));
		}
		include $this->template();
	}
}
?>
