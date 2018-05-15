<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Agent_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$agentlevels = $this->model->getLevels(true, true);
		$level = $this->set['level'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$params = array();
		$condition = '';
		$keyword = trim($_GPC['keyword']);
		if (!empty($searchfield) && !empty($keyword)) {
				$condition .= ' and ( dm.realname like :keyword or dm.nickname like :keyword or dm.mobile like :keyword)';
				$params[':keyword'] = '%' . $keyword . '%';
		}

		$sql = 'select dm.*,dm.nickname,dm.avatar,l.levelname,p.nickname as parentname,p.avatar as parentavatar,f.follow as followed, f.unfollowtime from ' . tablename('ewei_shop_member') . ' dm ' . ' left join ' . tablename('ewei_shop_member') . ' p on p.id = dm.agentid ' . ' left join ' . tablename('ewei_shop_commission_level') . ' l on l.id = dm.agentlevel' . ' left join ' . tablename('mc_mapping_fans') . 'f on f.openid=dm.openid and f.uniacid=' . $_W['uniacid'] . ' where dm.uniacid = ' . $_W['uniacid'] . ' and dm.isagent =1  ' . $condition . ' ORDER BY dm.agenttime desc';
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('select count(dm.id) from' . tablename('ewei_shop_member') . ' dm  ' . ' left join ' . tablename('ewei_shop_member') . ' p on p.id = dm.agentid ' . ' left join ' . tablename('mc_mapping_fans') . 'f on f.openid=dm.openid' . ' where dm.uniacid =' . $_W['uniacid'] . ' and dm.isagent =1 ' . $condition, $params);
		foreach ($list as &$row) {
			$info = $this->model->getInfo($row['openid'], array('total', 'pay'));
			$row['levelcount'] = $info['agentcount'];

			if (1 <= $level) {
				$row['level1'] = $info['level1'];
			}

			if (2 <= $level) {
				$row['level2'] = $info['level2'];
			}

			if (3 <= $level) {
				$row['level3'] = $info['level3'];
			}

			$row['commission_total'] = $info['commission_total'];
			$row['commission_pay'] = $info['commission_pay'];

		}

		unset($row);


		$pager = pagination2($total, $pindex, $psize);
		load()->func('tpl');
		include $this->template();
	}


	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$wechatid = intval($_GPC['wechatid']);

		if (empty($wechatid)) {
			$wechatid = $_W['uniacid'];
		}

		$params = array();
		$params[':uniacid'] = $wechatid;
		$condition = ' and uniacid=:uniacid and isagent=1 and status=1';

		if (!empty($kwd)) {
			$condition .= ' AND ( `nickname` LIKE :keyword or `realname` LIKE :keyword or `mobile` LIKE :keyword )';
			$params[':keyword'] = '%' . $kwd . '%';
		}

		if (!empty($_GPC['selfid'])) {
			$condition .= ' and id<>' . intval($_GPC['selfid']);
		}

		$ds = pdo_fetchall('SELECT id,avatar,nickname,openid,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);
		include $this->template('commission/query');
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$status = intval($_GPC['status']);
		$members = pdo_fetchall('SELECT id,openid,agentid,nickname,realname,mobile,status FROM ' . tablename('ewei_shop_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();

		foreach ($members as $member) {
			if ($member['status'] === $status) {
				continue;
			}

			if ($status == 1) {
				pdo_update('ewei_shop_member', array('status' => 1, 'agenttime' => $time), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));

			}
			else {
				pdo_update('ewei_shop_member', array('status' => 0, 'agenttime' => 0), array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		show_json(1, array('url' => referer()));
	}

}

?>
