<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Info_EweiShopV2Page extends MobileLoginPage 
{
	protected $member;
	public function __construct() 
	{
		global $_W;
		global $_GPC;
		parent::__construct();
		$this->member = m('member')->getInfo($_W['openid']);
	}
	public function main() 
	{
		global $_W;
		global $_GPC;
		$returnurl = urldecode(trim($_GPC['returnurl']));
		$member = $this->member;
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$show_data = 1;
		if ((!(empty($new_area)) && empty($member['datavalue'])) || (empty($new_area) && !(empty($member['datavalue'])))) 
		{
			$show_data = 0;
		}
		include $this->template();
	}
	public function submit() 
	{
		global $_W;
		global $_GPC;
		$memberdata = $_GPC['memberdata'];
		$arr = array('realname' => trim($memberdata['realname']), 'weixin' => trim($memberdata['weixin']), 'birthyear' => intval($memberdata['birthyear']), 'birthmonth' => intval($memberdata['birthmonth']), 'birthday' => intval($memberdata['birthday']), 'province' => trim($memberdata['province']), 'city' => trim($memberdata['city']), 'datavalue' => trim($memberdata['datavalue']), 'mobile' => trim($memberdata['mobile']));
			pdo_update('ewei_shop_member', $arr, array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));
		show_json(1);
	}
}
?>
