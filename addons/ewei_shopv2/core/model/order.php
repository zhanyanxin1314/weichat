<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Order_EweiShopV2Model 
{
	public function payResult($params) 
	{
		global $_W;
		$fee = intval($params['fee']);
		$data = array('status' => ($params['result'] == 'success' ? 1 : 0));
		$ordersn_tid = $params['tid'];
		$ordersn = rtrim($ordersn_tid, 'TR');
		$order = pdo_fetch('select id,ordersn, price,openid,dispatchtype,addressid,carrier,status,isverify,deductcredit2,`virtual`,isvirtual,couponid,isvirtualsend,isparent,paytype,merchid,agentid,createtime,buyagainprice,istrade,tradestatus from ' . tablename('ewei_shop_order') . ' where  ordersn=:ordersn and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':ordersn' => $ordersn));
		if (1 <= $order['status']) 
		{
			return true;
		}
		$orderid = $order['id'];
		if ($params['from'] == 'return') 
		{
			$address = false;
			if (empty($order['dispatchtype'])) 
			{
				$address = pdo_fetch('select realname,mobile,address from ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
			}
			$carrier = false;
			if (($order['dispatchtype'] == 1) || ($order['isvirtual'] == 1)) 
			{
				$carrier = unserialize($order['carrier']);
			}
			if ($params['type'] == 'cash') 
			{
				if ($order['isparent'] == 1) 
				{
					$change_data = array();
					$change_data['merchshow'] = 1;
					pdo_update('ewei_shop_order', $change_data, array('id' => $orderid));
					$this->setChildOrderPayResult($order, 0, 0);
				}
				return true;
			}
			if ($order['istrade'] == 0) 
			{
				if ($order['status'] == 0) 
				{
					if (!(empty($order['virtual'])) && com('virtual')) 
					{
						return com('virtual')->pay($order);
					}
					if ($order['isvirtualsend']) 
					{
						return $this->payVirtualSend($order['id']);
					}
					$isonlyverifygoods = $this->checkisonlyverifygoods($orderid);
					$time = time();
					$change_data = array();
					if ($isonlyverifygoods) 
					{
						$change_data['status'] = 2;
					}
					else 
					{
						$change_data['status'] = 1;
					}
					$change_data['paytime'] = $time;
					if ($order['isparent'] == 1) 
					{
						$change_data['merchshow'] = 1;
					}
					pdo_update('ewei_shop_order', $change_data, array('id' => $orderid));
					if ($order['isparent'] == 1) 
					{
						$this->setChildOrderPayResult($order, $time, 1);
					}
					if ($order['isparent'] == 1) 
					{
						$child_list = $this->getChildOrder($order['id']);
						foreach ($child_list as $k => $v ) 
						{
						}
					}
					else 
					{
					}
					if ($order['isparent'] == 1) 
					{
						$merchSql = 'SELECT id,merchid FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid = ' . intval($_W['uniacid']) . ' AND parentid = ' . intval($order['id']);
						$merchData = pdo_fetchall($merchSql);
						foreach ($merchData as $mk => $mv ) 
						{
							com_run('printer::sendOrderMessage', $mv['id']);
						}
					}
					else 
					{
						com_run('printer::sendOrderMessage', $orderid);
					}
					if (p('commission')) 
					{
						p('commission')->checkOrderPay($order['id']);
					}
				}
			}
			else 
			{
				$time = time();
				$change_data = array();
				$count_ordersn = $this->countOrdersn($ordersn_tid);
				if (($order['status'] == 0) && ($count_ordersn == 1)) 
				{
					$change_data['status'] = 1;
					$change_data['tradestatus'] = 1;
					$change_data['paytime'] = $time;
				}
				else if (($order['status'] == 1) && ($order['tradestatus'] == 1) && ($count_ordersn == 2)) 
				{
					$change_data['tradestatus'] = 2;
					$change_data['tradepaytime'] = $time;
				}
				pdo_update('ewei_shop_order', $change_data, array('id' => $orderid));
			}
			return true;
		}
		return false;
	}
	public function setOrderPayType($orderid, $paytype, $ordersn = '') 
	{
		global $_W;
		$count_ordersn = 1;
		$change_data = array();
		if (!(empty($ordersn))) 
		{
			$count_ordersn = $this->countOrdersn($ordersn);
		}
		if ($count_ordersn == 2) 
		{
			$change_data['tradepaytype'] = $paytype;
		}
		else 
		{
			$change_data['paytype'] = $paytype;
		}
		pdo_update('ewei_shop_order', $change_data, array('id' => $orderid));
		if (!(empty($orderid))) 
		{
			pdo_update('ewei_shop_order', array('paytype' => $paytype), array('parentid' => $orderid));
		}
	}
	public function getGoodsDiscountPrice($g, $level, $type = 0) 
	{
		global $_W;
		if ($type == 0) 
		{
			$total = $g['total'];
		}
		else 
		{
			$total = 1;
		}
		$gprice = $g['marketprice'] * $total;
		$price = $gprice;
		return array('unitprice' => $unitprice, 'price' => $price);
	}
	public function getOrderCommission($orderid, $agentid = 0) 
	{
		global $_W;
		if (empty($agentid)) 
		{
			$item = pdo_fetch('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid Limit 1', array('id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (!(empty($item))) 
			{
				$agentid = $item['agentid'];
			}
		}
		$level = 0;
		$pc = p('commission');
		if ($pc) 
		{
			$pset = $pc->getSet();
			$level = intval($pset['level']);
		}
		$commission1 = 0;
		$commission2 = 0;
		$commission3 = 0;
		$m1 = false;
		$m2 = false;
		$m3 = false;
		if (!(empty($level))) 
		{
			if (!(empty($agentid))) 
			{
				$m1 = m('member')->getMember($agentid);
				if (!(empty($m1['agentid']))) 
				{
					$m2 = m('member')->getMember($m1['agentid']);
					if (!(empty($m2['agentid']))) 
					{
						$m3 = m('member')->getMember($m2['agentid']);
					}
				}
			}
		}
		$order_goods = pdo_fetchall('select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		foreach ($order_goods as &$og ) 
		{
			if (!(empty($level)) && !(empty($agentid))) 
			{
				$commissions = iunserializer($og['commissions']);
				if (!(empty($m1))) 
				{
					if (is_array($commissions)) 
					{
						$commission1 += ((isset($commissions['level1']) ? floatval($commissions['level1']) : 0));
					}
					else 
					{
						$c1 = iunserializer($og['commission1']);
						$l1 = $pc->getLevel($m1['openid']);
						$commission1 += ((isset($c1['level' . $l1['id']]) ? $c1['level' . $l1['id']] : $c1['default']));
					}
				}
				if (!(empty($m2))) 
				{
					if (is_array($commissions)) 
					{
						$commission2 += ((isset($commissions['level2']) ? floatval($commissions['level2']) : 0));
					}
					else 
					{
						$c2 = iunserializer($og['commission2']);
						$l2 = $pc->getLevel($m2['openid']);
						$commission2 += ((isset($c2['level' . $l2['id']]) ? $c2['level' . $l2['id']] : $c2['default']));
					}
				}
				if (!(empty($m3))) 
				{
					if (is_array($commissions)) 
					{
						$commission3 += ((isset($commissions['level3']) ? floatval($commissions['level3']) : 0));
					}
					else 
					{
						$c3 = iunserializer($og['commission3']);
						$l3 = $pc->getLevel($m3['openid']);
						$commission3 += ((isset($c3['level' . $l3['id']]) ? $c3['level' . $l3['id']] : $c3['default']));
					}
				}
			}
		}
		unset($og);
		$commission = $commission1 + $commission2 + $commission3;
		return $commission;
	}
	public function checkOrderGoods($orderid) 
	{
		global $_W;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid, true);
		$flag = 0;
		$msg = '订单中的商品' . '<br/>';
		$uniacid = $_W['uniacid'];
		$item = pdo_fetch('select * from ' . tablename('ewei_shop_order') . '  where  id = :id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid));
		if ((empty($order['isnewstore']) || empty($order['storeid'])) && empty($order['istrade'])) 
		{
			$order_goods = pdo_fetchall('select og.id,g.title, og.goodsid,g.total as stock,og.total as buycount,g.status,g.deleted,g.totalcnf from  ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
			foreach ($order_goods as $data ) 
			{
				if (empty($data['status']) || !(empty($data['deleted']))) 
				{
					$flag = 1;
					$msg .= $data['title'] . '<br/> 已下架,不能付款!!';
				}
				$unit = ((empty($data['unit']) ? '件' : $data['unit']));
			}
		}
		$data = array();
		$data['flag'] = $flag;
		$data['msg'] = $msg;
		return $data;
	}
	public function checkpeerpay($orderid) 
	{
		global $_W;
		$sql = 'SELECT p.*,o.openid FROM ' . tablename('ewei_shop_order_peerpay') . ' AS p JOIN ' . tablename('ewei_shop_order') . ' AS o ON p.orderid = o.id WHERE p.orderid = :orderid AND p.uniacid = :uniacid AND (p.status = 0 OR p.status=1) AND o.status >= 0 LIMIT 1';
		$query = pdo_fetch($sql, array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
		return $query;
	}
	public function peerStatus($param) 
	{
		global $_W;
		if (!(empty($param['tid']))) 
		{
			$sql = 'SELECT id FROM ' . tablename('ewei_shop_order_peerpay_payinfo') . ' WHERE tid = :tid';
			$id = pdo_fetchcolumn($sql, array(':tid' => $param['tid']));
			if ($id) 
			{
				return $id;
			}
		}
		return pdo_insert('ewei_shop_order_peerpay_payinfo', $param);
	}
	public function getVerifyCardNumByOrderid($orderid) 
	{
		global $_W;
		$num = pdo_fetchcolumn('select SUM(og.total)  from ' . tablename('ewei_shop_order_goods') . ' og' . "\r\n\t\t" . ' inner join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id' . "\r\n\t\t" . ' where og.uniacid=:uniacid  and og.orderid =:orderid and g.cardid>0', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		return $num;
	}
	public function checkisonlyverifygoods($orderid) 
	{
		global $_W;
		$num = pdo_fetchcolumn('select COUNT(1)  from ' . tablename('ewei_shop_order_goods') . ' og' . "\r\n\t\t" . ' inner join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id' . "\r\n\t\t" . ' where og.uniacid=:uniacid  and og.orderid =:orderid and g.type<>5', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$num = intval($num);
		if (0 < $num) 
		{
			return false;
		}
		$num2 = pdo_fetchcolumn('select COUNT(1)  from ' . tablename('ewei_shop_order_goods') . ' og' . "\r\n" . '             inner join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id' . "\r\n" . '             where og.uniacid=:uniacid  and og.orderid =:orderid and g.type=5', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$num2 = intval($num2);
		if (0 < $num2) 
		{
			return true;
		}
		return false;
	}
	public function checkhaveverifygoods($orderid) 
	{
		global $_W;
		$num = pdo_fetchcolumn('select COUNT(1)  from ' . tablename('ewei_shop_order_goods') . ' og' . "\r\n\t\t" . ' inner join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id' . "\r\n\t\t" . ' where og.uniacid=:uniacid  and og.orderid =:orderid and g.type=5', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$num = intval($num);
		if (0 < $num) 
		{
			return true;
		}
		return false;
	}
	public function checkhaveverifygoodlog($orderid) 
	{
		global $_W;
		$num = pdo_fetchcolumn('select COUNT(1)  from ' . tablename('ewei_shop_verifygoods_log') . ' vl' . "\r\n\t\t" . ' inner join ' . tablename('ewei_shop_verifygoods') . ' v on vl.verifygoodsid = v.id' . "\r\n\t\t" . ' where v.uniacid=:uniacid  and v.orderid =:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));
		$num = intval($num);
		if (0 < $num) 
		{
			return true;
		}
		return false;
	}
	public function countOrdersn($ordersn, $str = 'TR') 
	{
		global $_W;
		$count = intval(substr_count($ordersn, $str));
		return $count;
	}
	public function getOrderVirtual($order = array()) 
	{
		global $_W;
		if (empty($order)) 
		{
			return false;
		}
		if (empty($order['virtual_info'])) 
		{
			return $order['virtual_str'];
		}
		$ordervirtual = array();
		$virtual_type = pdo_fetch('select fields from ' . tablename('ewei_shop_virtual_type') . ' where id=:id and uniacid=:uniacid and merchid = :merchid limit 1 ', array(':id' => $order['virtual'], ':uniacid' => $_W['uniacid'], ':merchid' => $order['merchid']));
		if (!(empty($virtual_type))) 
		{
			$virtual_type = iunserializer($virtual_type['fields']);
			$virtual_info = ltrim($order['virtual_info'], '[');
			$virtual_info = rtrim($virtual_info, ']');
			$virtual_info = explode(',', $virtual_info);
			if (!(empty($virtual_info))) 
			{
				foreach ($virtual_info as $index => $virtualinfo ) 
				{
					$virtual_temp = iunserializer($virtualinfo);
					if (!(empty($virtual_temp))) 
					{
						foreach ($virtual_temp as $k => $v ) 
						{
							$ordervirtual[$index][] = array('key' => $virtual_type[$k], 'value' => $v, 'field' => $k);
						}
						unset($k, $v);
					}
				}
				unset($index, $virtualinfo);
			}
		}
		return $ordervirtual;
	}
}
?>
