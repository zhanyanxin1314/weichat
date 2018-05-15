<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Qrcode_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
	
		global $_W;
		global $_GPC;
		$mid = intval($_GPC['mid']);
		$openid = $_W['openid'];
		$member = m('member')->getMember($openid);
		$share_set = set_medias(m('common')->getSysset('share'), 'icon');
		$can = false;
		$set = $this->set;
		if (!empty($set['closed_qrcode']) && !intval($_GPC['goodsid'])) {
			$this->message('没有开启推广二维码!', mobileUrl('commission'), 'info');
		}
		if ($_W['ispost']) {
			$img = '';

			$img = $this->model->createShopImage();

			show_json(1, array('img' => $img . '?t=' . TIMESTAMP));
		}

		$set['qrcode_content'] = htmlspecialchars_decode($set['qrcode_content'], ENT_QUOTES);
		include $this->template();
	}
}

?>
