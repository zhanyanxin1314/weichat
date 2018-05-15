<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Detail_EweiShopV2Page extends MobilePage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		$goods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') .
				   ' where id=:id and uniacid=:uniacid limit 1', 
				   array(':id' => $id, ':uniacid' => $_W['uniacid']));
		$goods['content'] = m('ui')->lazy($goods['content']);
		$goods['unit'] = ((empty($goods['unit']) ? '件' : $goods['unit']));
		$thumbs = iunserializer($goods['thumb_url']);
		if (empty($thumbs)) 
		{
			$thumbs = array($goods['thumb']);
		}
		$goods = set_medias($goods, 'thumb');
		include $this->template();
	}
	//获取商品详情
	public function get_detail() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$goods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		exit(m('ui')->lazy($goods['content']));
	}
}
?>
