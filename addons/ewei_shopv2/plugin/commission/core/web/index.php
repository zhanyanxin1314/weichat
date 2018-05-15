<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;

		if (cv('commission.agent')) {
			header('location: ' . webUrl('commission/agent'));
			exit();
		}
		else if (cv('commission.apply.view1')) {
			header('location: ' . webUrl('commission/apply', array('status' => 1)));
			exit();
		}
		else if (cv('commission.apply.view2')) {
			header('location: ' . webUrl('commission/apply', array('status' => 2)));
			exit();
		}
		else if (cv('commission.apply.view3')) {
			header('location: ' . webUrl('commission/apply', array('status' => 3)));
			exit();
		}
		else if (cv('commission.apply.view_1')) {
			header('location: ' . webUrl('commission/apply', array('status' => -1)));
			exit();
		}
		else if (cv('commission.level')) {
			header('location: ' . webUrl('commission/level'));
			exit();
		}
		else {
			if (cv('commission.set')) {
				header('location: ' . webUrl('commission/set'));
				exit();
			}
		}
	}

	public function set()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$data = (is_array($_GPC['data']) ? $_GPC['data'] : array());
			$data['withdraw'] = 1;
			$data['cashweixin'] = 1;
			$data['closemyshop'] = 1;
			$data['qrcode'] = 0;
			$data['commissiontype'] = 0;
			m('common')->updatePluginset(array('commission' => $data));
			show_json(1, array('url' => webUrl('commission/set', array('tab' => str_replace('#tab_', '', $_GPC['tab'])))));
		}

                $data = m('common')->getPluginset('commission');
		include $this->template();
	}
}

?>
