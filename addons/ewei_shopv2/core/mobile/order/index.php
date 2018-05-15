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
			include $this->template();
	}
	public function get_list() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$show_status = $_GPC['status'];
		$condition = ' and openid=:openid and ismr=0 and deleted=0 and uniacid=:uniacid and istrade=0 ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		$condition .= ' and merchshow=0 ';
		if ($show_status != '') 
		{
			$show_status = intval($show_status);
			switch ($show_status) 
			{
				case 0: $condition .= ' and status=0 and paytype<>3';
				break;
				case 1: $condition .= ' and (status=1 or (status=0 and paytype=3))';
				break;
				case 2: $condition .= ' and (status=2 or (status=1 and sendtype>0))';
				break;
				default: $condition .= ' and status=' . intval($show_status);
			}
			if ($show_status != 5) 
			{
				$condition .= ' and userdeleted=0 ';
			}
		}
		else 
		{
			$condition .= ' and userdeleted=0 ';
		}
		$list = pdo_fetchall('select id,addressid,ordersn,price,status,finishtime,paytype,isparent,userdeleted' .			              ' from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition . 
				     ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ','
				     . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where 1 ' . 
					 $condition, $params);
		foreach ($list as &$row ) 
		{
			$param = array();
			if ($row['isparent'] == 1) 
			{
				$scondition = ' og.parentorderid=:parentorderid';
				$param[':parentorderid'] = $row['id'];
			}
			else 
			{
				$scondition = ' og.orderid=:orderid';
				$param[':orderid'] = $row['id'];
			}
			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,g.status,og.price,og.sendtype,og.sendtime,
				       og.finishtime,og.remarksend' .' FROM ' . 
				       tablename('ewei_shop_order_goods') . ' og ' . ' left join ' .
				       tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' .
				       ' where ' . $scondition . ' order by og.id asc';
			$goods = pdo_fetchall($sql, $param);
			$goods = set_medias($goods, 'thumb');
			if (empty($goods)) 
			{
				$goods = array();
			}
			foreach ($goods as &$r ) 
			{
				$r['thumb'] .= '?t=' . random(50);
			}
			unset($r);
			$goods_list = array();
			$goods_list[0]['goods'] = $goods;
			$row['goods'] = $goods_list;
			$row['goods_num'] = count($goods);
			$statuscss = 'text-cancel';
			switch ($row['status']) 
			{
				case '0': if ($row['paytype'] == 3) 
				{
					$status = '待发货';
				}
				else 
				{
					$status = '待付款';
				}
				$statuscss = 'text-cancel';
				break;
				$status = '待发货';
				if (0 < $row['sendtype']) 
				{
					$status = '部分发货';
				}
				$statuscss = 'text-warning';
				break;
				case '2': $status = '待收货';
				$statuscss = 'text-danger';
				break;
				$statuscss = 'text-success';
				break;
			}
			$row['statusstr'] = $status;
			$row['statuscss'] = $statuscss;
		}
		unset($row);
		show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total));
	}
	public function alipay() 
	{
		global $_W;
		global $_GPC;
		$url = urldecode($_GPC['url']);
		if (!(is_weixin())) 
		{
			header('location: ' . $url);
			exit();
		}
		include $this->template();
	}
}
?>
