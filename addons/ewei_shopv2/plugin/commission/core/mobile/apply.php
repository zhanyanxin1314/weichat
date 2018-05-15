<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Apply_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$level = $this->set['level'];
		$member = $this->model->getInfo($openid, array());
		$become_reg = $this->set['become_reg'];

		if (empty($become_reg)) {
			if (empty($member['realname'])) {
				$returnurl = urlencode(mobileUrl('commission/apply'));
				$this->message('需要您完善资料才能继续操作!', mobileUrl('member/info', array('returnurl' => $returnurl)), 'info');
			}
		}

		$time = time();
		$day_times = intval($this->set['settledays']) * 3600 * 24;
		$agentLevel = $this->model->getLevel($openid);
		$commission_ok = 0;
		$orderids = array();

		if (1 <= $level) {
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3  and og.status1=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));

			foreach ($level1_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}

				$hasorder = false;

				foreach ($orderids as $or) {
					if ($or['orderid'] == $o['id']) {
						$hasorder = true;
						break;
					}
				}

				if ($hasorder) {
					continue;
				}

				$orderids[] = array('orderid' => $o['id'], 'level' => 1);
			}
		}

		if (2 <= $level) {
			if (0 < $member['level1']) {
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')  and o.status>=3  and og.status2=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid']));

				foreach ($level2_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$hasorder = false;

					foreach ($orderids as $or) {
						if ($or['orderid'] == $o['id']) {
							$hasorder = true;
							break;
						}
					}

					if ($hasorder) {
						continue;
					}

					$orderids[] = array('orderid' => $o['id'], 'level' => 2);
				}
			}
		}

		if (3 <= $level) {
			if (0 < $member['level2']) {
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')  and o.status>=3  and  og.status3=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ')   and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid']));

				foreach ($level3_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$hasorder = false;

					foreach ($orderids as $or) {
						if ($or['orderid'] == $o['id']) {
							$hasorder = true;
							break;
						}
					}

					if ($hasorder) {
						continue;
					}

					$orderids[] = array('orderid' => $o['id'], 'level' => 3);
				}
			}
		}

		foreach ($orderids as $o) {
			$goods = pdo_fetchall('SELECT ' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $o['orderid']));

			foreach ($goods as $g) {
				$commissions = iunserializer($g['commissions']);
				if (($o['level'] == 1) && ($g['status1'] == 0)) {
					$commission1 = iunserializer($g['commission1']);

					if (empty($commissions)) {
						$commission_ok += (isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default']);
					}
					else {
						$commission_ok += (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
					}
				}

				if (($o['level'] == 2) && ($g['status2'] == 0)) {
					$commission2 = iunserializer($g['commission2']);

					if (empty($commissions)) {
						$commission_ok += (isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default']);
					}
					else {
						$commission_ok += (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
					}
				}

				if (($o['level'] == 3) && ($g['status3'] == 0)) {
					$commission3 = iunserializer($g['commission3']);

					if (empty($commissions)) {
						$commission_ok += (isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default']);
					}
					else {
						$commission_ok += (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
					}
				}
			}
		}

		$withdraw = floatval($this->set['withdraw']);

		if ($withdraw <= 0) {
			$withdraw = 1;
		}

		$cansettle = $withdraw <= $commission_ok;
		$member['commission_ok'] = number_format($commission_ok, 2);
		$set_array = array();
		$set_array['charge'] = $this->set['withdrawcharge'];
		$set_array['begin'] = floatval($this->set['withdrawbegin']);
		$set_array['end'] = floatval($this->set['withdrawend']);
		$realmoney = $commission_ok;
		$last_data = $this->model->getLastApply($member['id']);
		$canusewechat = !strexists($openid, 'wap_user_') && !strexists($openid, 'sns_qq_') && !strexists($openid, 'sns_wx_') && !strexists($openid, 'sns_wa_');
		$type_array = array();

		if ($this->set['cashcredit'] == 1) {
			$type_array[0]['title'] = $this->set['texts']['withdraw'] . '到' . $_W['shopset']['trade']['moneytext'];
		}

		if (($this->set['cashweixin'] == 1) && $canusewechat) {
			$type_array[1]['title'] = $this->set['texts']['withdraw'] . '到微信钱包';
		}

		if (!empty($last_data)) {
			if (array_key_exists($last_data['type'], $type_array)) {
				$type_array[$last_data['type']]['checked'] = 1;
			}
		}

		if ($_W['ispost']) {
			if (empty($_SESSION['commission_apply_token'])) {
				show_json(0, '不要短时间重复下提交!');
			}

			unset($_SESSION['commission_apply_token']);
			if (($commission_ok <= 0) || empty($orderids)) {
				show_json(0, '参数错误,请刷新页面后重新提交!');
			}

			$type = intval($_GPC['type']);

			if (!array_key_exists($type, $type_array)) {
				show_json(0, '未选择提现方式，请您选择提现方式后重试!');
			}

			$apply = array();

			foreach ($orderids as $o) {
				pdo_update('ewei_shop_order_goods', array('status' . $o['level'] => 1, 'applytime' . $o['level'] => $time), array('orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
			}

			$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
			$apply['uniacid'] = $_W['uniacid'];
			$apply['applyno'] = $applyno;
			$apply['orderids'] = iserializer($orderids);
			$apply['mid'] = $member['id'];
			$apply['commission'] = $commission_ok;
			$apply['type'] = $type;
			$apply['status'] = 1;
			$apply['applytime'] = $time;
			$apply['realmoney'] = $realmoney;
			$apply['deductionmoney'] = $deductionmoney;
			$apply['charge'] = $set_array['charge'];
			$apply['beginmoney'] = $set_array['begin'];
			$apply['endmoney'] = $set_array['end'];
			pdo_insert('ewei_shop_commission_apply', $apply);
			$apply_type = array('微信钱包');
			$mcommission = $commission_ok;

			if (!empty($deductionmoney)) {
				$mcommission .= ',实际到账金额:' . $realmoney . ',提现手续费金额:' . $deductionmoney;
			}

			show_json(1, '已提交,请等待审核!');
		}

		$token = md5(microtime());
		$_SESSION['commission_apply_token'] = $token;
		include $this->template();
	}
}

?>
