<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Goods_EweiShopV2Model
{
	public function getList($args = array())
	{
		global $_W;
		$openid = $_W['openid'];
		$page = ((!(empty($args['page'])) ? intval($args['page']) : 1));
		$pagesize = ((!(empty($args['pagesize'])) ? intval($args['pagesize']) : 10));
		$displayorder = 'displayorder';
		$order = ((!(empty($args['order'])) ? $args['order'] : ' ' . $displayorder . ' desc,createtime desc'));
		$orderby = ((empty($args['order']) ? '' : ((!(empty($args['by'])) ? $args['by'] : ''))));
		$condition = ' and `uniacid` = :uniacid AND `deleted` = 0 and status=1';
		$params = array(':uniacid' => $_W['uniacid']);
		$condition .= ' and `checked` = 0';
		$ids = ((!(empty($args['ids'])) ? trim($args['ids']) : ''));
		if (!(empty($ids))) {
			$condition .= ' and id in ( ' . $ids . ')';
		}
		$keywords = ((!(empty($args['keywords'])) ? $args['keywords'] : ''));
		if (!(empty($keywords))) {
			$condition .= ' AND (`title` LIKE :keywords OR `keywords` LIKE :keywords)';
			$params[':keywords'] = '%' . trim($keywords) . '%';
		}
		$total = '';

		$sql = 'SELECT id,title,thumb,thumb_url,marketprice,productprice,minprice,maxprice,total,type' . 
		      ' FROM ' . tablename('ewei_shop_goods') . ' where 1 ' . $condition . ' ORDER BY rand() LIMIT ' . $pagesize;
		$total = $pagesize;

		$list = pdo_fetchall($sql, $params);

		$list = set_medias($list, 'thumb');
		return array('list' => $list, 'total' => $total);
	}

}


?>
