<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Index_EweiShopV2Page extends MobileLoginPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$member = m('member')->getMember($_W['openid'], true);
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		include $this->template();
	}
}
?>
