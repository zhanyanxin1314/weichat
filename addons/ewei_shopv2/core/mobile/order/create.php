<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Create_EweiShopV2Page extends MobileLoginPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$shop = m('common')->getSysset('shop');
		$member = m('member')->getMember($_W['openid']);
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$goodsid = intval($_GPC['id']);
		$commission = m('common')->getPluginset('commission');
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$address_street = intval($area_set['address_street']);
		if (!($packageid)) 
		{
			$total_array = array();
			$member = m('member')->getMember($openid, true);
			$id = intval($_GPC['id']);
			$total = intval($_GPC['total']);
			if ($total < 1) 
			{
				$total = 1;
			}
			$buytotal = $total;
			$changenum = false;
			$goods = array();
			$sql = 'SELECT id as goodsid,type,title,weight, thumb,marketprice,total as stock,checked '.
			       ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
			$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $id));
			$data['total'] = $total;
			$goods[] = $data;
			$goods = set_medias($goods, 'thumb');
			unset($g);
			$weight = 0;
			$total = 0;
			$goodsprice = 0;
			$realprice = 0;
			$address = false;
			foreach ($goods as &$g ) 
			{
				if (empty($g['total']) || (intval($g['total']) < 1)) 
				{
					$g['total'] = 1;
				}
					$gprice = $g['marketprice'] * $g['total'];
					$prices = m('order')->getGoodsDiscountPrice($g, $level);
					$g['ggprice'] = $prices['price'];
					$g['unitprice'] = $prices['unitprice'];
				$realprice += $g['ggprice'];
				if ($g['ggprice'] < $gprice) 
				{
					$goodsprice += $gprice;
				}
				else 
				{
					$goodsprice += $g['ggprice'];
				}
				$total += $g['total'];
			}
			unset($g);
			$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
			$goodsdata = array();
			foreach ($goods as $g ) 
			{
				$goodsdata[] = array('goodsid' => $g['goodsid'],
						     'total' => $g['total'],
					   	     'marketprice' => $g['marketprice'], 
						     'type' => $g['type'],  
						     'goodsalltotal' => $g['goodsalltotal']);
			}
			$createInfo = array('id' => $id,  
					    'addressid' =>  $address['id'],
					    'goods' => $goodsdata, 
				            'new_area' => $new_area, 
					    'address_street' => $address_street);
		}
		$goods_list = array();
		$goods_list[0]['goods'] = $goods;
		include $this->template();
	}
	public function submit() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid);
		$goods = $_GPC['goods'];
		if (empty($goods) || !(is_array($goods))) 
		{
			show_json(0, '未找到任何商品');
		}
		$allgoods = array();
		$totalprice = 0;
		$goodsprice = 0;
		$grprice = 0;
		$weight = 0;
		$total_array = array();
		foreach ($goods as $g ) 
		{
			if (empty($g)) 
			{
				continue;
			}
			$goodsid = intval($g['goodsid']);
			$goodstotal = intval($g['total']);
			$total_array[$goodsid]['total'] += $goodstotal;
		}
		foreach ($goods as $g ) 
		{
			if (empty($g)) 
			{
				continue;
			}
			$goodsid = intval($g['goodsid']);
			$optionid = intval($g['optionid']);
			$goodstotal = intval($g['total']);
			if ($goodstotal < 1) 
			{
				$goodstotal = 1;
			}
			if (empty($goodsid)) 
			{
				show_json(0, '参数错误');
			}
			$sql = 'SELECT id as goodsid,' . $sql_condition . 'title,type,weight,total,thumb,marketprice, goodssn,productsn,unit,deleted,unite_total, status' . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
			$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));
			$data['stock'] = $data['total'];
			$data['total'] = $goodstotal;
			$unit = ((empty($data['unit']) ? '件' : $data['unit']));
			$gprice = $data['marketprice'] * $goodstotal;
			$goodsprice += $gprice;
			$prices = m('order')->getGoodsDiscountPrice($data, $level);
			$data['ggprice'] = $prices['price'];
			$totalprice += $data['ggprice'];
			$allgoods[] = $data;
		}
		$grprice = $totalprice;
		if (empty($allgoods)) 
		{
			show_json(0, '未找到任何商品');
		}
		$addressid = intval($_GPC['addressid']);
		$address = false;
		if (!(empty($addressid))) 
		{
			$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));
			if (empty($address)) 
			{
				show_json(0, '未找到地址');
			}
		}
		if ($totalprice <= 0) 
		{
			$totalprice = 0;
		}
		$ordersn = m('common')->createNO('order', 'ordersn', 'SH');
		$order = array();
		$order['parentid'] = 0;
		$order['uniacid'] = $uniacid;
		$order['openid'] = $openid;
		$order['ordersn'] = $ordersn;
		$order['price'] = $totalprice;
		$order['oldprice'] = $totalprice;
		$order['grprice'] = $grprice;
		$order['status'] = 0;
		$order['remark'] = trim($_GPC['remark']);
		$order['addressid'] = ((empty($dispatchtype) ? $addressid : 0));
		$order['goodsprice'] = $goodsprice;
		$order['createtime'] = time();
		$order['paytype'] = 0;
		$author = p('author');
		if (!(empty($address))) 
		{
			$order['address'] = iserializer($address);
		}
		pdo_insert('ewei_shop_order', $order);
		$orderid = pdo_insertid();
		foreach ($allgoods as $goods ) 
		{
			$order_goods = array();
			$order_goods['uniacid'] = $uniacid;
			$order_goods['orderid'] = $orderid;
			$order_goods['goodsid'] = $goods['goodsid'];
			$order_goods['price'] = $goods['marketprice'] * $goods['total'];
			$order_goods['total'] = $goods['total'];
			$order_goods['createtime'] = time();
			$order_goods['goodssn'] = $goods['goodssn'];
			$order_goods['productsn'] = $goods['productsn'];
			$order_goods['realprice'] = $goods['ggprice'];
			$order_goods['oldprice'] = $goods['ggprice'];
			$order_goods['openid'] = $openid;
			pdo_insert('ewei_shop_order_goods', $order_goods);
		}
		$pluginc = p('commission');
		if ($pluginc) 
		{
			if ($multiple_order == 0) 
			{
				$pluginc->checkOrderConfirm($orderid);
			}
			else if (!(empty($merch_array))) 
			{
				foreach ($merch_array as $key => $value ) 
				{
					$pluginc->checkOrderConfirm($value['orderid']);
				}
			}
		}
		show_json(1, array('orderid' => $orderid));
	}
}
?>
