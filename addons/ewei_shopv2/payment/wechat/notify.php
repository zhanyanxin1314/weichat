<?php
error_reporting(0);
define('IN_MOBILE', true);
$input = file_get_contents('php://input');
libxml_disable_entity_loader(true);
if (!(empty($input)) && empty($_GET['out_trade_no'])) 
{
	$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
	$data = json_decode(json_encode($obj), true);
	if (empty($data)) 
	{
		exit('fail');
	}
	if (empty($data['version']) && (($data['result_code'] != 'SUCCESS') || ($data['return_code'] != 'SUCCESS'))) 
	{
		$result = array('return_code' => 'FAIL', 'return_msg' => (empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']));
		echo array2xml($result);
		exit();
	}
	if (!(empty($data['version'])) && (($data['result_code'] != '0') || ($data['status'] != '0'))) 
	{
		exit('fail');
	}
	$get = $data;
}
else 
{
	$get = $_GET;
}
require dirname(__FILE__) . '/../../../../framework/bootstrap.inc.php';
require IA_ROOT . '/addons/ewei_shopv2/defines.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/functions.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/plugin_model.php';
require IA_ROOT . '/addons/ewei_shopv2/core/inc/com_model.php';
new EweiShopWechatPay($get);
exit('fail');
class EweiShopWechatPay 
{
	public $get;
	public $type;
	public $total_fee;
	public $set;
	public $setting;
	public $sec;
	public $sign;
	public $isapp = false;
	public $is_jie = false;
	public function __construct($get) 
	{
		global $_W;
		$this->get = $get;
		$strs = explode(':', $this->get['attach']);
		$this->type = intval($strs[1]);
		$this->total_fee = round($this->get['total_fee'] / 100, 2);
		$GLOBALS['_W']['uniacid'] = intval($strs[0]);
		$_W['uniacid'] = intval($strs[0]);
		$this->init();
	}
	public function success() 
	{
		$result = array('return_code' => 'SUCCESS', 'return_msg' => 'OK');
		echo array2xml($result);
		exit();
	}
	public function fail() 
	{
		$result = array('return_code' => 'FAIL');
		echo array2xml($result);
		exit();
	}
	public function init() 
	{
		if ($this->type == '0') 
		{
			$this->order();
		}
		$this->success();
	}
	public function order() 
	{
		global $_W;
		if (!($this->publicMethod())) 
		{
			exit('order');
		}
		$tid = $this->get['out_trade_no'];
		$isborrow = 0;
		$borrowopenid = '';
		if (strpos($tid, '_borrow') !== false) 
		{
			$tid = str_replace('_borrow', '', $tid);
			$isborrow = 1;
			$borrowopenid = $this->get['openid'];
		}
		if (strpos($tid, '_B') !== false) 
		{
			$tid = str_replace('_B', '', $tid);
			$isborrow = 1;
			$borrowopenid = $this->get['openid'];
		}
		if (strexists($tid, 'GJ')) 
		{
			$tids = explode('GJ', $tid);
			$tid = $tids[0];
		}
		$ispeerpay = 0;
		$tid2 = 0;
		if (22 < strlen($tid)) 
		{
			$tid2 = $tid;
			$ispeerpay = 1;
		}
		$paytype = 21;
		if (strexists($borrowopenid, '2088') || is_numeric($borrowopenid)) 
		{
			$paytype = 22;
		}
		$tid = substr($tid, 0, 22);
		$order = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE ordersn = :ordersn AND uniacid = :uniacid', array(':ordersn' => $tid, ':uniacid' => $_W['uniacid']));
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `module`=:module AND `tid`=:tid  limit 1';
		$params = array();
		$params[':tid'] = $tid;
		$params[':module'] = 'ewei_shopv2';
		$log = pdo_fetch($sql, $params);
		if (!(empty($log)) && (($log['status'] == '0') || $ispeerpay) && (($log['fee'] == $this->total_fee) || $ispeerpay)) 
		{
			pdo_update('ewei_shop_order', array('paytype' => $paytype, 'isborrow' => $isborrow, 'borrowopenid' => $borrowopenid, 'apppay' => ($this->isapp ? 1 : 0)), array('ordersn' => $log['tid'], 'uniacid' => $log['uniacid']));
			$site = WeUtility::createModuleSite($log['module']);
			if (!(is_error($site))) 
			{
				$method = 'payResult';
				if (method_exists($site, $method)) 
				{
					$ret = array();
					$ret['acid'] = $log['acid'];
					$ret['uniacid'] = $log['uniacid'];
					$ret['result'] = 'success';
					$ret['type'] = $log['type'];
					$ret['from'] = 'return';
					$ret['tid'] = $log['tid'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					$ret['tag'] = $log['tag'];
					$result = $site->$method($ret);
					if ($result) 
					{
						$log['tag'] = iunserializer($log['tag']);
						$log['tag']['transaction_id'] = $this->get['transaction_id'];
						$record = array();
						$record['status'] = '1';
						$record['tag'] = iserializer($log['tag']);
						pdo_update('core_paylog', $record, array('plid' => $log['plid']));
					}
				}
			}
		}
		else 
		{
			$this->fail();
		}
	}
	public function publicMethod() 
	{
		global $_W;
		if (empty($_W['uniacid'])) 
		{
			return false;
		}
		list($set, $payment) = m('common')->public_build();
		$this->set = $set;
		if (empty($payment['is_new']) || ($this->get['trade_type'] == 'APP')) 
		{
			$this->setting = uni_setting($_W['uniacid'], array('payment'));
			if (is_array($this->setting['payment']) || ($this->set['weixin_jie'] == 1) || ($this->set['weixin_sub'] == 1) || ($this->set['weixin_jie_sub'] == 1) || ($this->get['trade_type'] == 'APP')) 
			{
				$this->is_jie = (strpos($this->get['out_trade_no'], '_B') !== false) || (strpos($this->get['out_trade_no'], '_borrow') !== false);
				$sec_yuan = m('common')->getSec();
				$this->sec = iunserializer($sec_yuan['sec']);
				if ((($this->set['weixin_jie'] == 1) && $this->is_jie) || ($this->set['weixin_sub'] == 1) || (($this->set['weixin_jie_sub'] == 1) && $this->is_jie)) 
				{
					if ($this->set['weixin_sub'] == 1) 
					{
						$wechat = array('version' => 1, 'key' => $this->sec['apikey_sub'], 'apikey' => $this->sec['apikey_sub']);
					}
					if (($this->set['weixin_jie'] == 1) && $this->is_jie) 
					{
						$wechat = array('version' => 1, 'key' => $this->sec['apikey'], 'apikey' => $this->sec['apikey']);
					}
					if (($this->set['weixin_jie_sub'] == 1) && $this->is_jie) 
					{
						$wechat = array('version' => 1, 'key' => $this->sec['apikey_jie_sub'], 'apikey' => $this->sec['apikey_jie_sub']);
					}
				}
				else if ($this->set['weixin'] == 1) 
				{
					$wechat = $this->setting['payment']['wechat'];
					if (IMS_VERSION <= 0.80000000000000004) 
					{
						$wechat['apikey'] = $wechat['signkey'];
					}
				}
				if (($this->get['trade_type'] == 'APP') && ($this->set['app_wechat'] == 1)) 
				{
					$this->isapp = true;
					$wechat = array('version' => 1, 'key' => $this->sec['app_wechat']['apikey'], 'apikey' => $this->sec['app_wechat']['apikey'], 'appid' => $this->sec['app_wechat']['appid'], 'mchid' => $this->sec['app_wechat']['merchid']);
				}
				if (!(empty($wechat))) 
				{
					ksort($this->get);
					$string1 = '';
					foreach ($this->get as $k => $v ) 
					{
						if (($v != '') && ($k != 'sign')) 
						{
							$string1 .= $k . '=' . $v . '&';
						}
					}
					$wechat['apikey'] = (($wechat['version'] == 1 ? $wechat['key'] : $wechat['apikey']));
					$this->sign = strtoupper(md5($string1 . 'key=' . $wechat['apikey']));
					$this->get['openid'] = ((isset($this->get['sub_openid']) ? $this->get['sub_openid'] : $this->get['openid']));
					if ($this->sign == $this->get['sign']) 
					{
						return true;
					}
				}
			}
		}
		else if (!(is_error($payment))) 
		{
			ksort($this->get);
			$string1 = '';
			foreach ($this->get as $k => $v ) 
			{
				if (($v != '') && ($k != 'sign')) 
				{
					$string1 .= $k . '=' . $v . '&';
				}
			}
			$this->sign = strtoupper(md5($string1 . 'key=' . $payment['apikey']));
			$this->get['openid'] = ((isset($this->get['sub_openid']) ? $this->get['sub_openid'] : $this->get['openid']));
			if ($this->sign == $this->get['sign']) 
			{
				return true;
			}
		}
		return false;
	}
}
?>
