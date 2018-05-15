<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class MobilePage extends Page 
{
	public $footer = array();
	public $followBar = false;
	public function __construct() 
	{
		global $_W;
		global $_GPC;
		$preview = intval($_GPC['preview']);
		$wap = m('common')->getSysset('wap');
		if ($wap['open'] && !(is_weixin()) && empty($preview)) 
		{
			if (($this instanceof MobileLoginPage) || ($this instanceof PluginMobileLoginPage)) 
			{
				if (empty($_W['openid'])) 
				{
					$_W['openid'] = m('account')->checkLogin();
				}
			}
			else 
			{
				$_W['openid'] = m('account')->checkOpenid();
			}
		}
		else 
		{
			if ($preview && !(is_weixin())) 
			{
				$_W['openid'] = 'ooyv91cPbLRIz1qaX7Fim_cRfjZk';
			}
			if (EWEI_SHOPV2_DEBUG) 
			{
				$_W['openid'] = 'ooyv91cPbLRIz1qaX7Fim_cRfjZk';
			}
		}
		$member = m('member')->checkMember();
		$_W['mid'] = ((!(empty($member)) ? $member['id'] : ''));
		$_W['mopenid'] = ((!(empty($member)) ? $member['openid'] : ''));
	}
	public function footerMenus() 
	{
		global $_W;
		global $_GPC;
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$commission = array();
		if (p('commission') && intval(0 < $_W['shopset']['commission']['level'])) 
		{
			$member = m('member')->getMember($_W['openid']);
			if (!($member['agentblack'])) 
			{
				if (($member['isagent'] == 1) && ($member['status'] == 1)) 
				{
					$commission = array('url' => mobileUrl('commission'), 'text' => (empty($_W['shopset']['commission']['texts']['center']) ? '分销中心' : $_W['shopset']['commission']['texts']['center']));
				}
				else 
				{
					$commission = array('url' => mobileUrl('commission/register'), 'text' => (empty($_W['shopset']['commission']['texts']['become']) ? '成为分销商' : $_W['shopset']['commission']['texts']['become']));
				}
			}
		}
		$routes = explode('.', $_W['routes']);
		$controller = $routes[0];
		include $this->template('_menu');
	}
	public function shopShare() 
	{
		global $_W;
		global $_GPC;
		$trigger = false;
		if (empty($_W['shopshare'])) 
		{
			$set = $_W['shopset'];
			$_W['shopshare'] = array('title' => (empty($set['share']['title']) ? $set['shop']['name'] : $set['share']['title']), 'imgUrl' => (empty($set['share']['icon']) ? tomedia($set['shop']['logo']) : tomedia($set['share']['icon'])), 'desc' => (empty($set['share']['desc']) ? $set['shop']['description'] : $set['share']['desc']), 'link' => (empty($set['share']['url']) ? mobileUrl('', NULL, true) : $set['share']['url']));
			$plugin_commission = p('commission');
			if ($plugin_commission) 
			{
				$set = $plugin_commission->getSet();
				if (!(empty($set['level']))) 
				{
					$openid = $_W['openid'];
					$member = m('member')->getMember($openid);
					if (!(empty($member)) && ($member['status'] == 1) && ($member['isagent'] == 1)) 
					{
						if (empty($set['closemyshop'])) 
						{
							$myshop = $plugin_commission->getShop($member['id']);
							$_W['shopshare'] = array('title' => $myshop['name'], 'imgUrl' => tomedia($myshop['logo']), 'desc' => $myshop['desc'], 'link' => mobileUrl('commission/myshop', array('mid' => $member['id']), true));
						}
						else 
						{
							$_W['shopshare']['link'] = ((empty($_W['shopset']['share']['url']) ? mobileUrl('', array('mid' => $member['id']), true) : $_W['shopset']['share']['url']));
						}
						if (empty($set['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) 
						{
							$trigger = true;
						}
					}
					else if (!(empty($_GPC['mid']))) 
					{
						$m = m('member')->getMember($_GPC['mid']);
						if (!(empty($m)) && ($m['status'] == 1) && ($m['isagent'] == 1)) 
						{
							if (empty($set['closemyshop'])) 
							{
								$myshop = $plugin_commission->getShop($_GPC['mid']);
								$_W['shopshare'] = array('title' => $myshop['name'], 'imgUrl' => tomedia($myshop['logo']), 'desc' => $myshop['desc'], 'link' => mobileUrl('commission/myshop', array('mid' => $member['id']), true));
							}
							else 
							{
								$_W['shopshare']['link'] = ((empty($_W['shopset']['share']['url']) ? mobileUrl('', array('mid' => $_GPC['mid']), true) : $_W['shopset']['share']['url']));
							}
						}
						else 
						{
							$_W['shopshare']['link'] = ((empty($_W['shopset']['share']['url']) ? mobileUrl('', array('mid' => $_GPC['mid']), true) : $_W['shopset']['share']['url']));
						}
					}
				}
			}
		}
		return $trigger;
	}
	public function wapQrcode() 
	{
		global $_W;
		global $_GPC;
		$currenturl = '';
		if (!(is_mobile())) 
		{
			$currenturl = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
		}
		$shop = m('common')->getSysset('shop');
		$shopname = $shop['name'];
		include $this->template('_wapqrcode');
	}
}
?>
