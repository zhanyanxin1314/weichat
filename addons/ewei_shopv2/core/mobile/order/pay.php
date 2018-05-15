<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Pay_EweiShopV2Page extends MobileLoginPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid, true);
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		$og_array = m('order')->checkOrderGoods($orderid);
		if (!(empty($og_array['flag']))) 
		{
			$this->message($og_array['msg'], '', 'error');
		}
		if (empty($orderid)) 
		{
			header('location: ' . mobileUrl('order'));
			exit();
		}
		if (empty($order)) 
		{
			header('location: ' . mobileUrl('order'));
			exit();
		}
		if (empty($log)) 
		{
			$log = array('uniacid' => $uniacid, 'openid' => $member['uid'], 'module' => 'ewei_shopv2', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
			pdo_insert('core_paylog', $log);
			$plid = pdo_insertid();
		}
		$set = m('common')->getSysset(array('shop', 'pay'));
		$set['pay']['weixin'] = ((!(empty($set['pay']['weixin_sub'])) ? 1 : $set['pay']['weixin']));
		$param_title = $set['shop']['name'] . '订单';
		$order['price'] = floatval($order['price']);
		if (empty($order['price'])) 
		{
			header('location: ' . mobileUrl('order/pay/complete', array('id' => $order['id'], 'type' => 'credit', 'ordersn' => $order['ordersn'])));
			exit();
		}
		load()->model('payment');
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$wechat = array('success' => false);
		$jie = intval($_GPC['jie']);
		if (is_weixin()) 
		{
			$params = array();
			$params['tid'] = $log['tid'];
			if (!(empty($order['ordersn2']))) 
			{
				$var = sprintf('%02d', $order['ordersn2']);
				$params['tid'] .= 'GJ' . $var;
			}
			$params['user'] = $openid;
			$params['fee'] = $order['price'];
			$params['title'] = $param_title;
			if (isset($set['pay']) && ($set['pay']['weixin'] == 1) && ($jie !== 1)) 
			{
				$options = array();
				if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) 
				{
					load()->model('payment');
					$setting = uni_setting($_W['uniacid'], array('payment'));
					if (is_array($setting['payment'])) 
					{
						$options = $setting['payment']['wechat'];
						$options['appid'] = $_W['account']['key'];
						$options['secret'] = $_W['account']['secret'];
					}
				}
				$wechat = m('common')->wechat_build($params, $options, 0);
				if (!(is_error($wechat))) 
				{
					$wechat['success'] = true;
					$wechat['weixin'] = true;
				}
			}
			if ((isset($set['pay']) && ($set['pay']['weixin_jie'] == 1) && !($wechat['success'])) || ($jie === 1)) 
			{
				if (!(empty($order['ordersn2']))) 
				{
					$params['tid'] = $params['tid'] . '_B';
				}
				else 
				{
					$params['tid'] = $params['tid'] . '_borrow';
				}
				$options = array();
				$options['appid'] = $sec['appid'];
				$options['mchid'] = $sec['mchid'];
				$options['apikey'] = $sec['apikey'];
				if (!(empty($set['pay']['weixin_jie_sub'])) && !(empty($sec['sub_secret_jie_sub']))) 
				{
					$wxuser = m('member')->wxuser($sec['sub_appid_jie_sub'], $sec['sub_secret_jie_sub']);
					$params['openid'] = $wxuser['openid'];
				}
				else if (!(empty($sec['secret']))) 
				{
					$wxuser = m('member')->wxuser($sec['appid'], $sec['secret']);
					$params['openid'] = $wxuser['openid'];
				}
				$wechat = m('common')->wechat_native_build($params, $options, 0);
				if (!(is_error($wechat))) 
				{
					$wechat['success'] = true;
						$wechat['weixin'] = true;
				}
			}
			$wechat['jie'] = $jie;
		}
		$payinfo = array('orderid' => $orderid, 'ordersn' => $log['tid'],  'alipay' => $alipay, 'wechat' => $wechat,  'money' => $order['price']);
		if (is_h5app()) 
		{
			$payinfo = array('wechat' => (!(empty($sec['app_wechat']['merchname'])) && !(empty($set['pay']['app_wechat'])) && !(empty($sec['app_wechat']['appid'])) && !(empty($sec['app_wechat']['appsecret'])) && !(empty($sec['app_wechat']['merchid'])) && !(empty($sec['app_wechat']['apikey'])) && (0 < $order['price']) ? true : false), 'alipay' => (!(empty($set['pay']['app_alipay'])) && !(empty($sec['app_alipay']['public_key'])) ? true : false), 'mcname' => $sec['app_wechat']['merchname'], 'aliname' => (empty($_W['shopset']['shop']['name']) ? $sec['app_wechat']['merchname'] : $_W['shopset']['shop']['name']), 'ordersn' => $log['tid'], 'money' => $order['price'], 'attach' => $_W['uniacid'] . ':0', 'type' => 0, 'orderid' => $orderid, 'credit' => $credit, 'cash' => $cash);
			if (!(empty($order['ordersn2']))) 
			{
				$var = sprintf('%02d', $order['ordersn2']);
				$payinfo['ordersn'] .= 'GJ' . $var;
			}
		}
		include $this->template();
	}
	public function orderstatus() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select status from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid));
		if (1 <= $order['status']) 
		{
			@session_start();
			$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
			show_json(1);
		}
		show_json(0);
	}
	public function complete() 
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$gpc_ordersn = ((empty($_GPC['ordersn']) ? $_GPC['ordersn'] : str_replace(array('_borrow', '_B'), '', $_GPC['ordersn'])));
		if (is_h5app() && empty($orderid)) 
		{
			if (strexists($gpc_ordersn, 'GJ')) 
			{
				$ordersns = explode('GJ', $gpc_ordersn);
				$ordersn = $ordersns[0];
			}
			else 
			{
				$ordersn = $gpc_ordersn;
			}
			$ordersn = rtrim($ordersn, 'TR');
			$orderid = pdo_fetchcolumn('select id from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn' => $ordersn, ':uniacid' => $uniacid, ':openid' => $openid));
		}
		if (empty($orderid)) 
		{
			if ($_W['ispost']) 
			{
				show_json(0, '参数错误');
			}
			else 
			{
				$this->message('参数错误', mobileUrl('order'));
			}
		}
		$set = m('common')->getSysset(array('shop', 'pay'));
		$set['pay']['weixin'] = ((!(empty($set['pay']['weixin_sub'])) ? 1 : $set['pay']['weixin']));
		$member = m('member')->getMember($openid, true);
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		if (!(empty($gpc_ordersn))) 
		{
			$order['ordersn'] = $gpc_ordersn;
		}
		$go_flag = 0;
		if (empty($order['istrade']) && (1 <= $order['status'])) 
		{
			$go_flag = 1;
		}
		if (!(empty($order['istrade']))) 
		{
			if ((1 < $order['status']) || (($order['status'] == 1) && ($order['tradestatus'] == 2))) 
			{
				$go_flag = 1;
			}
		}
		if ($go_flag == 1) 
		{
			$pay_result = true;
			if ($_W['ispost']) 
			{
				$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
				show_json(1, array('result' => $pay_result));
			}
			else 
			{
				header('location:' . mobileUrl('order/pay/success', array('id' => $order['id'], 'result' => $pay_result)));
				exit();
			}
		}
		if (empty($order)) 
		{
			if ($_W['ispost']) 
			{
				show_json(0, '订单未找到');
			}
			else 
			{
				$this->message('订单未找到', mobileUrl('order'));
			}
		}
		$type = $_GPC['type'];
		if (!(in_array($type, array('wechat', 'alipay', 'credit', 'cash')))) 
		{
			if ($_W['ispost']) 
			{
				show_json(0, '未找到支付方式');
			}
			else 
			{
				$this->message('未找到支付方式', mobileUrl('order'));
			}
		}
		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));
		if (empty($log) && empty($ispeerpay)) 
		{
			if ($_W['ispost']) 
			{
				show_json(0, '支付出错,请重试!');
			}
			else 
			{
				$this->message('支付出错,请重试!', mobileUrl('order'));
			}
		}
		$ps = array();
		$ps['tid'] = $log['tid'];
		$ps['user'] = $openid;
		$ps['fee'] = $log['fee'];
		$ps['title'] = $log['title'];
		if ($type == 'wechat') 
		{
			if (!(is_weixin()) && empty($_W['shopset']['wap']['open'])) 
			{
				if ($_W['ispost']) 
				{
					show_json(0, (is_h5app() ? 'APP正在维护' : '非微信环境!'));
				}
				else 
				{
					$this->message((is_h5app() ? 'APP正在维护' : '非微信环境!'), mobileUrl('order'));
				}
			}
			if ((empty($set['pay']['weixin']) && empty($set['pay']['weixin_jie']) && is_weixin()) || (empty($set['pay']['app_wechat']) && is_h5app())) 
			{
				if ($_W['ispost']) 
				{
					show_json(0, '未开启微信支付!');
				}
				else 
				{
					$this->message('未开启微信支付!', mobileUrl('order'));
				}
			}
			$ordersn = $order['ordersn'];
			if (!(empty($order['ordersn2']))) 
			{
				$ordersn .= 'GJ' . sprintf('%02d', $order['ordersn2']);
			}
			if (!(empty($ispeerpay))) 
			{
				$payquery = m('finance')->isWeixinPay($_SESSION['peerpaytid'], $order['price'], (is_h5app() ? true : false));
				$payquery_jie = m('finance')->isWeixinPayBorrow($_SESSION['peerpaytid'], $order['price']);
			}
			else 
			{
				$payquery = m('finance')->isWeixinPay($ordersn, $order['price'], (is_h5app() ? true : false));
				$payquery_jie = m('finance')->isWeixinPayBorrow($ordersn, $order['price']);
			}
			if (!(is_error($payquery)) || !(is_error($payquery_jie)) || !(empty($ispeerpay))) 
			{
				$record = array();
				$record['status'] = '1';
				$record['type'] = 'wechat';
				pdo_update('core_paylog', $record, array('plid' => $log['plid']));
				m('order')->setOrderPayType($order['id'], 21, $gpc_ordersn);
				if (is_h5app()) 
				{
					pdo_update('ewei_shop_order', array('apppay' => 1), array('id' => $order['id']));
				}
				$ret = array();
				$ret['result'] = 'success';
				$ret['type'] = 'wechat';
				$ret['from'] = 'return';
				$ret['tid'] = $log['tid'];
				$ret['user'] = $log['openid'];
				$ret['fee'] = $log['fee'];
				$ret['weid'] = $log['weid'];
				$ret['uniacid'] = $log['uniacid'];
				$ret['deduct'] = intval($_GPC['deduct']) == 1;
				$pay_result = m('order')->payResult($ret);
				@session_start();
				$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
				if ($_W['ispost']) 
				{
					show_json(1, array('result' => $pay_result));
				}
				else 
				{
					header('location:' . mobileUrl('order/pay/success', array('id' => $order['id'], 'result' => $pay_result)));
				}
				exit();
			}
			if ($_W['ispost']) 
			{
				show_json(0, '支付出错,请重试!');
			}
			else 
			{
				$this->message('支付出错,请重试!', mobileUrl('order'));
			}
		}
	}
	public function success() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid, true);
		$orderid = intval($_GPC['id']);
		if (empty($orderid)) 
		{
			$this->message('参数错误', mobileUrl('order'), 'error');
		}
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		@session_start();
		if (!(isset($_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete']))) 
		{
			if (empty($order['istrade'])) 
			{
				header('location: ' . mobileUrl('order'));
			}
			else 
			{
				header('location: ' . mobileUrl('newstore/norder'));
			}
			exit();
		}
		unset($_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete']);
		$ispeerpay = m('order')->checkpeerpay($orderid);
		if (!(empty($ispeerpay))) 
		{
			$peerpay = floatval($_GPC['peerpay']);
			$openid = pdo_fetchcolumn('select openid from ' . tablename('ewei_shop_order') . ' where id=:orderid and uniacid=:uniacid limit 1', array(':orderid' => $orderid, ':uniacid' => $uniacid));
			$order['price'] = $ispeerpay['realprice'];
			$peerpayuid = m('member')->getInfo($_W['openid']);
			$peerprice = pdo_fetch('SELECT `price` FROM ' . tablename('ewei_shop_order_peerpay_payinfo') . ' WHERE uid = :uid ORDER BY id DESC LIMIT 1', array(':uid' => $peerpayuid['id']));
			if ($activity) 
			{
				$share = true;
			}
			else 
			{
				$share = false;
			}
		}
		else 
		{
			if (!(empty($order['istrade']))) 
			{
				if (($order['status'] == 1) && ($order['tradestatus'] == 1)) 
				{
					$order['price'] = $order['dowpayment'];
				}
				else if (($order['status'] == 1) && ($order['tradestatus'] == 2)) 
				{
					$order['price'] = $order['betweenprice'];
				}
			}
			$merchid = $order['merchid'];
			$goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));
			$address = false;
			if (!(empty($order['addressid']))) 
			{
				$address = iunserializer($order['address']);
				if (!(is_array($address))) 
				{
					$address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
				}
			}
			$carrier = @iunserializer($order['carrier']);
			if (!(is_array($carrier)) || empty($carrier)) 
			{
				$carrier = false;
			}
			$store = false;
			if (!(empty($order['storeid']))) 
			{
				if (0 < $merchid) 
				{
					$store = pdo_fetch('select * from  ' . tablename('ewei_shop_merch_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
				}
				else 
				{
					$store = pdo_fetch('select * from  ' . tablename('ewei_shop_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
				}
			}
		}
		include $this->template();
	}
	protected function str($str) 
	{
		$str = str_replace('"', '', $str);
		$str = str_replace('\'', '', $str);
		return $str;
	}
	public function check() 
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$og_array = m('order')->checkOrderGoods($orderid);
		if (!(empty($og_array['flag']))) 
		{
			show_json(0, $og_array['msg']);
		}
		show_json(1);
	}
	public function message($msg, $redirect = '', $type = '') 
	{
		global $_W;
		$title = '';
		$buttontext = '';
		$message = $msg;
		if (is_array($msg)) 
		{
			$message = ((isset($msg['message']) ? $msg['message'] : ''));
			$title = ((isset($msg['title']) ? $msg['title'] : ''));
			$buttontext = ((isset($msg['buttontext']) ? $msg['buttontext'] : ''));
		}
		if (empty($redirect)) 
		{
			$redirect = 'javascript:history.back(-1);';
		}
		else if ($redirect == 'close') 
		{
			$redirect = 'javascript:WeixinJSBridge.call("closeWindow")';
		}
		include $this->template('_message');
		exit();
	}
}
?>
