<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class List_EweiShopV2Page extends WebPage 
{
	public function main() 
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and dm.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if (!(empty($_GPC['realname']))) 
		{
			$_GPC['realname'] = trim($_GPC['realname']);
			$condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname or dm.id like :realname)';
			$params[':realname'] = '%' . $_GPC['realname'] . '%';
		}
		$sql = 'select * from ' . tablename('ewei_shop_member') . ' dm ' . ' where 1 ' . $condition . '  ORDER BY id DESC';
		$list = pdo_fetchall($sql, $params);
		$list_agent = array();
		foreach ($list as $val ) 
		{
			$list_agent[] = trim($val['agentid'], ',');
		}
		$memberids = array_keys($list);
		isset($list_agent) && ($list_agent = array_values(array_filter($list_agent)));
		if (!(empty($list_agent))) 
		{
			$res_agent = pdo_fetchall('select id,nickname as agentnickname,avatar as agentavatar from ' . tablename('ewei_shop_member') . ' where id in (' . implode(',', $list_agent) . ')', array(), 'id');
		}
		foreach ($list as &$row ) 
		{
			$row['agentnickname'] = ((isset($res_agent[$row['agentid']]) ? $res_agent[$row['agentid']]['agentnickname'] : ''));
			$row['ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
			$row['ordermoney'] = pdo_fetchcolumn('select sum(price) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
		}
		unset($row);
		$total = pdo_fetchcolumn('select count(*) from' . tablename('ewei_shop_member') . ' dm ' . $join . ' where 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		$opencommission = false;
		$plug_commission = p('commission');
		if ($plug_commission) 
		{
			$comset = $plug_commission->getSet();
			if (!(empty($comset))) 
			{
				$opencommission = true;
			}
		}
		include $this->template();
	}
	public function detail() 
	{
		
		global $_W;
		global $_GPC;
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$hascommission = false;
		$plugin_com = p('commission');
		if ($plugin_com) 
		{
			$plugin_com_set = $plugin_com->getSet();
			$hascommission = !(empty($plugin_com_set['level']));
		}
		$id = intval($_GPC['id']);
		if ($hascommission) 
		{
			$agentlevels = $plugin_com->getLevels();
		}
		
		$member = m('member')->getMember($id);
		if ($hascommission) 
		{
			$member = $plugin_com->getInfo($id, array('total', 'pay'));
		}
		$member['self_ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$member['self_ordermoney'] = pdo_fetchcolumn('select sum(price) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		if (!(empty($member['agentid']))) 
		{
			$parentagent = m('member')->getMember($member['agentid']);
		}
		$order = pdo_fetch('select finishtime from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status>=1 Limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$member['last_ordertime'] = $order['finishtime'];
		if ($_W['ispost']) 
		{
			$data = ((is_array($_GPC['data']) ? $_GPC['data'] : array()));
			pdo_update('ewei_shop_member', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			$member = array_merge($member, $data);
			plog('member.list.edit', '修改会员资料  ID: ' . $member['id'] . ' <br/> 会员信息:  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			if ($hascommission) 
			{
				if (cv('commission.agent.edit')) 
				{
					$adata = ((is_array($_GPC['adata']) ? $_GPC['adata'] : array()));
					if (!(empty($adata))) 
					{
						pdo_update('ewei_shop_member', $adata, array('id' => $id, 'uniacid' => $_W['uniacid']));
					}
				}
			}
			show_json(1);
		}
		if ($hascommission) 
		{
			$agentlevels = $plugin_com->getLevels();
		}
		if (!(empty($member['agentid']))) 
		{
			$parentagent = m('member')->getMember($member['agentid']);
		}
		$order = pdo_fetch('select finishtime from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status=3 order by id desc limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$member['last_ordertime'] = $order['finishtime'];
		include $this->template();
	}
	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$members = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($members as $member ) 
		{
			pdo_delete('ewei_shop_member', array('id' => $member['id']));
			plog('member.list.delete', '删除会员  ID: ' . $member['id'] . ' <br/>会员信息: ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		}
		show_json(1, array('url' => referer()));
	}
}
?>
