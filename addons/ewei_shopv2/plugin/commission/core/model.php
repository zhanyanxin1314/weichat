<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'TM_COMMISSION_AGENT_NEW');
define('TM_COMMISSION_ORDER_PAY', 'TM_COMMISSION_ORDER_PAY');
define('TM_COMMISSION_ORDER_FINISH', 'TM_COMMISSION_ORDER_FINISH');
define('TM_COMMISSION_APPLY', 'TM_COMMISSION_APPLY');
define('TM_COMMISSION_CHECK', 'TM_COMMISSION_CHECK');
define('TM_COMMISSION_PAY', 'TM_COMMISSION_PAY');
define('TM_COMMISSION_UPGRADE', 'TM_COMMISSION_UPGRADE');
define('TM_COMMISSION_BECOME', 'TM_COMMISSION_BECOME');
if (!(class_exists('CommissionModel'))) 
{
	class CommissionModel extends PluginModel 
	{
		public function getSet($uniacid = 0) 
		{
			$set = parent::getSet($uniacid);
			$set['texts'] = array('agent' => (empty($set['texts']['agent']) ? '分销商' : $set['texts']['agent']), 'shop' => (empty($set['texts']['shop']) ? '小店' : $set['texts']['shop']), 'myshop' => (empty($set['texts']['myshop']) ? '我的小店' : $set['texts']['myshop']), 'center' => (empty($set['texts']['center']) ? '分销中心' : $set['texts']['center']), 'become' => (empty($set['texts']['become']) ? '成为分销商' : $set['texts']['become']), 'withdraw' => (empty($set['texts']['withdraw']) ? '提现' : $set['texts']['withdraw']), 'commission' => (empty($set['texts']['commission']) ? '佣金' : $set['texts']['commission']), 'commission1' => (empty($set['texts']['commission1']) ? '分销佣金' : $set['texts']['commission1']), 'commission_total' => (empty($set['texts']['commission_total']) ? '累计佣金' : $set['texts']['commission_total']), 'commission_ok' => (empty($set['texts']['commission_ok']) ? '可提现佣金' : $set['texts']['commission_ok']), 'commission_apply' => (empty($set['texts']['commission_apply']) ? '已申请佣金' : $set['texts']['commission_apply']), 'commission_check' => (empty($set['texts']['commission_check']) ? '待打款佣金' : $set['texts']['commission_check']), 'commission_lock' => (empty($set['texts']['commission_lock']) ? '未结算佣金' : $set['texts']['commission_lock']), 'commission_detail' => (empty($set['texts']['commission_detail']) ? '提现明细' : (($set['texts']['commission_detail'] == '佣金明细' ? '提现明细' : $set['texts']['commission_detail']))), 'commission_pay' => (empty($set['texts']['commission_pay']) ? '成功提现佣金' : $set['texts']['commission_pay']), 'commission_wait' => (empty($set['texts']['commission_wait']) ? '待收货佣金' : $set['texts']['commission_wait']), 'commission_fail' => (empty($set['texts']['commission_fail']) ? '无效佣金' : $set['texts']['commission_fail']), 'commission_charge' => (empty($set['texts']['commission_charge']) ? '扣除提现手续费' : $set['texts']['commission_charge']), 'order' => (empty($set['texts']['order']) ? '分销订单' : $set['texts']['order']), 'c1' => (empty($set['texts']['c1']) ? '一级' : $set['texts']['c1']), 'c2' => (empty($set['texts']['c2']) ? '二级' : $set['texts']['c2']), 'c3' => (empty($set['texts']['c3']) ? '三级' : $set['texts']['c3']), 'mydown' => (empty($set['texts']['mydown']) ? '我的下线' : $set['texts']['mydown']), 'down' => (empty($set['texts']['down']) ? '下线' : $set['texts']['down']), 'up' => (empty($set['texts']['up']) ? '推荐人' : $set['texts']['up']), 'yuan' => (empty($set['texts']['yuan']) ? '元' : $set['texts']['yuan']), 'icode' => (empty($set['texts']['icode']) ? '邀请码' : $set['texts']['icode']));
			return $set;
		}
		public function calculate($orderid = 0, $update = true) 
		{
			global $_W;
			$set = $this->getSet();
			$levels = $this->getLevels();
			$order = pdo_fetch('select agentid,price,goodsprice,deductcredit2,discountprice,isdiscountprice,dispatchprice,changeprice,ispackage,packageid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$commission = m('common')->getPluginset('commission');
			if (empty($commission['commissiontype'])) 
			{
				$rate = 1;
			}
			else 
			{
				$numm = $order['goodsprice'] - $order['isdiscountprice'] - $order['discountprice'];
				if ($numm != 0) 
				{
					$rate = (($order['price'] - $order['changeprice'] - $order['dispatchprice']) + $order['deductcredit2']) / $numm;
				}
				else 
				{
					$rate = 1;
				}
			}
			$agentid = $order['agentid'];
			$hascommission = false;
			$goods = pdo_fetchall('select og.id,og.realprice,og.total,og.goodsid,og.optionid,og.commissions,og.seckill,og.seckill_taskid,og.seckill_timeid from ' . tablename('ewei_shop_order_goods') . '  og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			if (0 < $set['level']) 
			{
				foreach ($goods as &$cinfo ) 
				{
					$price = $cinfo['realprice'] * $rate;
					$seckill_goods = false;
					if ($cinfo['seckill']) 
					{
						$seckill_goods = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_seckill_task_goods') . "\r\n" . '                                            where  goodsid=:goodsid and optionid =:optionid and taskid=:taskid and timeid=:timeid and uniacid=:uniacid limit 1', array(':goodsid' => $cinfo['goodsid'], ':optionid' => $cinfo['optionid'], ':taskid' => $cinfo['seckill_taskid'], ':timeid' => $cinfo['seckill_timeid'], ':uniacid' => $_W['uniacid']));
					}
					if (!(empty($seckill_goods))) 
					{
						$hascommission = true;
						$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? $seckill_goods['commission1'] * $cinfo['total'] : 0));
						$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? $seckill_goods['commission2'] * $cinfo['total'] : 0));
						$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? $seckill_goods['commission3'] * $cinfo['total'] : 0));
						foreach ($levels as $level ) 
						{
							$cinfo['commission1']['level' . $level['id']] = $seckill_goods['commission1'] * $cinfo['total'];
							$cinfo['commission2']['level' . $level['id']] = $seckill_goods['commission2'] * $cinfo['total'];
							$cinfo['commission3']['level' . $level['id']] = $seckill_goods['commission3'] * $cinfo['total'];
						}
					}
					else 
					{
						$goods_commission = ((!(empty($cinfo['commission'])) ? json_decode($cinfo['commission'], true) : ''));
						if (empty($cinfo['nocommission'])) 
						{
							$hascommission = true;
							if ($cinfo['hascommission'] == 1) 
							{
								if (empty($goods_commission['type'])) 
								{
									$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? ((0 < $cinfo['commission1_rate'] ? round(($cinfo['commission1_rate'] * $price) / 100, 2) . '' : round($cinfo['commission1_pay'] * $cinfo['total'], 2))) : 0));
									$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? ((0 < $cinfo['commission2_rate'] ? round(($cinfo['commission2_rate'] * $price) / 100, 2) . '' : round($cinfo['commission2_pay'] * $cinfo['total'], 2))) : 0));
									$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? ((0 < $cinfo['commission3_rate'] ? round(($cinfo['commission3_rate'] * $price) / 100, 2) . '' : round($cinfo['commission3_pay'] * $cinfo['total'], 2))) : 0));
									foreach ($levels as $level ) 
									{
										$cinfo['commission1']['level' . $level['id']] = ((0 < $cinfo['commission1_rate'] ? round(($cinfo['commission1_rate'] * $price) / 100, 2) . '' : round($cinfo['commission1_pay'] * $cinfo['total'], 2)));
										$cinfo['commission2']['level' . $level['id']] = ((0 < $cinfo['commission2_rate'] ? round(($cinfo['commission2_rate'] * $price) / 100, 2) . '' : round($cinfo['commission2_pay'] * $cinfo['total'], 2)));
										$cinfo['commission3']['level' . $level['id']] = ((0 < $cinfo['commission3_rate'] ? round(($cinfo['commission3_rate'] * $price) / 100, 2) . '' : round($cinfo['commission3_pay'] * $cinfo['total'], 2)));
									}
								}
								else if (empty($cinfo['hasoption'])) 
								{
									$temp_price = array();
									$i = 0;
									while ($i < $set['level']) 
									{
										if (!(empty($goods_commission['default']['option0'][$i]))) 
										{
											if (strexists($goods_commission['default']['option0'][$i], '%')) 
											{
												$dd = floatval(str_replace('%', '', $goods_commission['default']['option0'][$i]));
												if ((0 < $dd) && ($dd < 100)) 
												{
													$temp_price[$i] = round(($dd / 100) * $price, 2);
												}
												else 
												{
													$temp_price[$i] = 0;
												}
											}
											else 
											{
												$temp_price[$i] = round($goods_commission['default']['option0'][$i] * $cinfo['total'], 2);
											}
										}
										++$i;
									}
									$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? $temp_price[0] : 0));
									$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? $temp_price[1] : 0));
									$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? $temp_price[2] : 0));
									foreach ($levels as $level ) 
									{
										$temp_price = array();
										$i = 0;
										while ($i < $set['level']) 
										{
											if (!(empty($goods_commission['level' . $level['id']]['option0'][$i]))) 
											{
												if (strexists($goods_commission['level' . $level['id']]['option0'][$i], '%')) 
												{
													$dd = floatval(str_replace('%', '', $goods_commission['level' . $level['id']]['option0'][$i]));
													if ((0 < $dd) && ($dd < 100)) 
													{
														$temp_price[$i] = round(($dd / 100) * $price, 2);
													}
													else 
													{
														$temp_price[$i] = 0;
													}
												}
												else 
												{
													$temp_price[$i] = round($goods_commission['level' . $level['id']]['option0'][$i] * $cinfo['total'], 2);
												}
											}
											++$i;
										}
										$cinfo['commission1']['level' . $level['id']] = $temp_price[0];
										$cinfo['commission2']['level' . $level['id']] = $temp_price[1];
										$cinfo['commission3']['level' . $level['id']] = $temp_price[2];
									}
								}
								else 
								{
									$temp_price = array();
									$i = 0;
									while ($i < $set['level']) 
									{
										if (!(empty($goods_commission['default']['option' . $cinfo['optionid']][$i]))) 
										{
											if (strexists($goods_commission['default']['option' . $cinfo['optionid']][$i], '%')) 
											{
												$dd = floatval(str_replace('%', '', $goods_commission['default']['option' . $cinfo['optionid']][$i]));
												if ((0 < $dd) && ($dd < 100)) 
												{
													$temp_price[$i] = round(($dd / 100) * $price, 2);
												}
												else 
												{
													$temp_price[$i] = 0;
												}
											}
											else 
											{
												$temp_price[$i] = round($goods_commission['default']['option' . $cinfo['optionid']][$i] * $cinfo['total'], 2);
											}
										}
										++$i;
									}
									$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? $temp_price[0] : 0));
									$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? $temp_price[1] : 0));
									$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? $temp_price[2] : 0));
									foreach ($levels as $level ) 
									{
										$temp_price = array();
										$i = 0;
										while ($i < $set['level']) 
										{
											if (!(empty($goods_commission['level' . $level['id']]['option' . $cinfo['optionid']][$i]))) 
											{
												if (strexists($goods_commission['level' . $level['id']]['option' . $cinfo['optionid']][$i], '%')) 
												{
													$dd = floatval(str_replace('%', '', $goods_commission['level' . $level['id']]['option' . $cinfo['optionid']][$i]));
													if ((0 < $dd) && ($dd < 100)) 
													{
														$temp_price[$i] = round(($dd / 100) * $price, 2);
													}
													else 
													{
														$temp_price[$i] = 0;
													}
												}
												else 
												{
													$temp_price[$i] = round($goods_commission['level' . $level['id']]['option' . $cinfo['optionid']][$i] * $cinfo['total'], 2);
												}
											}
											++$i;
										}
										$cinfo['commission1']['level' . $level['id']] = $temp_price[0];
										$cinfo['commission2']['level' . $level['id']] = $temp_price[1];
										$cinfo['commission3']['level' . $level['id']] = $temp_price[2];
									}
								}
							}
							else 
							{
								$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? round(($set['commission1'] * $price) / 100, 2) . '' : 0));
								$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? round(($set['commission2'] * $price) / 100, 2) . '' : 0));
								$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? round(($set['commission3'] * $price) / 100, 2) . '' : 0));
								foreach ($levels as $level ) 
								{
									$cinfo['commission1']['level' . $level['id']] = ((1 <= $set['level'] ? round(($level['commission1'] * $price) / 100, 2) . '' : 0));
									$cinfo['commission2']['level' . $level['id']] = ((2 <= $set['level'] ? round(($level['commission2'] * $price) / 100, 2) . '' : 0));
									$cinfo['commission3']['level' . $level['id']] = ((3 <= $set['level'] ? round(($level['commission3'] * $price) / 100, 2) . '' : 0));
								}
							}
							if (0 < $order['ispackage']) 
							{
								$packoption = array();
								if (!(empty($cinfo['optionid']))) 
								{
									$packoption = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_package_goods_option') . "\r\n" . '                                            where uniacid = ' . $_W['uniacid'] . ' and pid = ' . $order['packageid'] . ' and optionid = ' . $cinfo['optionid'] . ' ');
								}
								else 
								{
									$packoption = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_package_goods') . "\r\n" . '                                            where uniacid = ' . $_W['uniacid'] . ' and pid = ' . $order['packageid'] . ' and goodsid = ' . $cinfo['goodsid'] . ' ');
								}
								$cinfo['commission1'] = array('default' => (1 <= $set['level'] ? $packoption['commission1'] : 0));
								$cinfo['commission2'] = array('default' => (2 <= $set['level'] ? $packoption['commission2'] : 0));
								$cinfo['commission3'] = array('default' => (3 <= $set['level'] ? $packoption['commission3'] : 0));
								foreach ($levels as $level ) 
								{
									$cinfo['commission1']['level' . $level['id']] = $packoption['commission1'];
									$cinfo['commission2']['level' . $level['id']] = $packoption['commission2'];
									$cinfo['commission3']['level' . $level['id']] = $packoption['commission3'];
								}
							}
						}
						else 
						{
							$cinfo['commission1'] = array('default' => 0);
							$cinfo['commission2'] = array('default' => 0);
							$cinfo['commission3'] = array('default' => 0);
							foreach ($levels as $level ) 
							{
								$cinfo['commission1']['level' . $level['id']] = 0;
								$cinfo['commission2']['level' . $level['id']] = 0;
								$cinfo['commission3']['level' . $level['id']] = 0;
							}
						}
					}
					if ($update) 
					{
						$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
						if (!(empty($agentid))) 
						{
							$m1 = m('member')->getMember($agentid);
							if (($m1['isagent'] == 1) && ($m1['status'] == 1)) 
							{
								$l1 = $this->getLevel($m1['openid']);
								$commissions['level1'] = ((empty($l1) ? round($cinfo['commission1']['default'], 2) : round($cinfo['commission1']['level' . $l1['id']], 2)));
								if (!(empty($m1['agentid']))) 
								{
									$m2 = m('member')->getMember($m1['agentid']);
									$l2 = $this->getLevel($m2['openid']);
									$commissions['level2'] = ((empty($l2) ? round($cinfo['commission2']['default'], 2) : round($cinfo['commission2']['level' . $l2['id']], 2)));
									if (!(empty($m2['agentid']))) 
									{
										$m3 = m('member')->getMember($m2['agentid']);
										$l3 = $this->getLevel($m3['openid']);
										$commissions['level3'] = ((empty($l3) ? round($cinfo['commission3']['default'], 2) : round($cinfo['commission3']['level' . $l3['id']], 2)));
									}
								}
							}
						}
						pdo_update('ewei_shop_order_goods', array('commission1' => iserializer($cinfo['commission1']), 'commission2' => iserializer($cinfo['commission2']), 'commission3' => iserializer($cinfo['commission3']), 'commissions' => iserializer($commissions), 'nocommission' => $cinfo['nocommission']), array('id' => $cinfo['id']));
					}
				}
				unset($cinfo);
			}
			if (!($hascommission)) 
			{
				pdo_update('ewei_shop_order', array('agentid' => 0), array('id' => $orderid));
			}
			return $goods;
		}
		public function getOrderCommissions($orderid = 0, $ogid = 0) 
		{
			global $_W;
			$set = $this->getSet();
			$agentid = pdo_fetchcolumn('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $orderid));
			$goods = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $ogid, ':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
			$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);
			if (0 < $set['level']) 
			{
				$commission1 = iunserializer($goods['commission1']);
				$commission2 = iunserializer($goods['commission2']);
				$commission3 = iunserializer($goods['commission3']);
				if (!(empty($agentid))) 
				{
					$m1 = m('member')->getMember($agentid);
					if (($m1['isagent'] == 1) && ($m1['status'] == 1)) 
					{
						$l1 = $this->getLevel($m1['openid']);
						$commissions['level1'] = ((empty($l1) ? round($commission1['default'], 2) : round($commission1['level' . $l1['id']], 2)));
						if (!(empty($m1['agentid']))) 
						{
							$m2 = m('member')->getMember($m1['agentid']);
							$l2 = $this->getLevel($m2['openid']);
							$commissions['level2'] = ((empty($l2) ? round($commission2['default'], 2) : round($commission2['level' . $l2['id']], 2)));
							if (!(empty($m2['agentid']))) 
							{
								$m3 = m('member')->getMember($m2['agentid']);
								$l3 = $this->getLevel($m3['openid']);
								$commissions['level3'] = ((empty($l3) ? round($commission3['default'], 2) : round($commission3['level' . $l3['id']], 2)));
							}
						}
					}
				}
			}
			return $commissions;
		}
		public function getInfo($openid, $options = NULL) 
		{
			if (empty($options) || !(is_array($options))) 
			{
				$options = array();
			}
			global $_W;
			$set = $this->getSet();
			$level = intval($set['level']);
			$member = m('member')->getMember($openid);
			$agentLevel = $this->getLevel($openid);
			$time = time();
			$day_times = intval($set['settledays']) * 3600 * 24;
			$agentcount = 0;
			$ordercount0 = 0;
			$ordermoney0 = 0;
			$commission_total = 0;
			$commission_ok = 0;
			$commission_apply = 0;
			$commission_check = 0;
			$commission_lock = 0;
			$commission_pay = 0;
			$commission_wait = 0;
			$commission_fail = 0;
			$level1 = 0;
			$level2 = 0;
			$level3 = 0;
			$order10 = 0;
			$order20 = 0;
			$order30 = 0;
			if (1 <= $level) 
			{
				if (in_array('ordercount0', $options)) 
				{
					$level1_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0 ' . $where_time . ' limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					$order10 += $level1_ordercount['ordercount'];
					$ordercount0 += $level1_ordercount['ordercount'];
					$ordermoney0 += $level1_ordercount['ordermoney'];
				}
				//分销佣金
				if (in_array('total', $options)) 
				{
					$level1_commissions = pdo_fetchall('select og.commission1  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions as $c ) 
					{
						$commission = iunserializer($c['commission1']);
							$commission_total += $commission['level' . $agentLevel['id']];
					}
				}
				//可提现分销佣金
				if (in_array('ok', $options)) 
				{
					$level1_commissions = pdo_fetchall('select og.commission1  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and og.status1=0  and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions as $c ) 
					{
						$commission = iunserializer($c['commission1']);

							$commission_ok += $commission['level' . $agentLevel['id']];
					}
				}
				//成功提现佣金
				if (in_array('pay', $options)) 
				{
					$level1_commissions2 = pdo_fetchall('select og.commission1 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions2 as $c ) 
					{
						$commission = iunserializer($c['commission1']);
							$commission_pay += $commission['level' . $agentLevel['id']];
					}
				}
				if (in_array('fail', $options)) 
				{
					$level1_commissions2 = pdo_fetchall('select og.commission1  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status=3 and og.status1=-1  and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
					foreach ($level1_commissions2 as $c ) 
					{
						$commission = iunserializer($c['commission1']);
							$commission_fail += $commission['level' . $agentLevel['id']];
					}
				}
				$level1_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');
				$level1 = count($level1_agentids);
				$agentcount += $level1;
			}
			if (2 <= $level) 
			{
				if (0 < $level1) 
				{
					if (in_array('ordercount0', $options)) 
					{
						$level2_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0 ' . $where_time . ' limit 1', array(':uniacid' => $_W['uniacid']));
						$order20 += $level2_ordercount['ordercount'];
						$ordercount0 += $level2_ordercount['ordercount'];
						$ordermoney0 += $level2_ordercount['ordermoney'];
					}
					if (in_array('total', $options)) 
					{
						$level2_commissions = pdo_fetchall('select og.commission2  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions as $c ) 
						{
							$commission = iunserializer($c['commission2']);
							$commission_total += $commission['level' . $agentLevel['id']];
						}
					}
					if (in_array('ok', $options)) 
					{
						$level2_commissions = pdo_fetchall('select og.commission2 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions as $c ) 
						{
							$commission = iunserializer($c['commission2']);
							$commission_ok += $commission['level' . $agentLevel['id']];
						}
					}
					if (in_array('pay', $options)) 
					{
						$level2_commissions3 = pdo_fetchall('select og.commission2 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($level1_agentids)) . ')  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level2_commissions3 as $c ) 
						{
							$commission = iunserializer($c['commission2']);
							$commission_pay += $commission['level' . $agentLevel['id']];
						}
					}
					$level2_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($level1_agentids)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
					$level2 = count($level2_agentids);
					$agentcount += $level2;
				}
			}
			if (3 <= $level) 
			{
				if (0 < $level2) 
				{
					if (in_array('ordercount0', $options)) 
					{
						$level3_ordercount = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0 ' . $where_time . ' limit 1', array(':uniacid' => $_W['uniacid']));
						$order30 += $level3_ordercount['ordercount'];
						$ordercount0 += $level3_ordercount['ordercount'];
						$ordermoney0 += $level3_ordercount['ordermoney'];
					}
					if (in_array('total', $options)) 
					{
						$level3_commissions = pdo_fetchall('select og.commission3 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions as $c ) 
						{
							$commission = iunserializer($c['commission3']);
							$commission_total += $commission['level' . $agentLevel['id']];
						}
					}
					if (in_array('ok', $options)) 
					{
						$level3_commissions = pdo_fetchall('select og.commission3 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions as $c ) 
						{
							$commission = iunserializer($c['commission3']);
							$commission_ok += $commission['level' . $agentLevel['id']];
						}
					}
					if (in_array('pay', $options)) 
					{
						$level3_commissions3 = pdo_fetchall('select og.commission3 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($level2_agentids)) . ')  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid and o.isparent=0', array(':uniacid' => $_W['uniacid']));
						foreach ($level3_commissions3 as $c ) 
						{
							$commission = iunserializer($c['commission3']);
							$commission_pay += $commission['level' . $agentLevel['id']];
						}
					}
					$level3_agentids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($level2_agentids)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
					$level3 = count($level3_agentids);
					$agentcount += $level3;
				}
			}
			$member['agentcount'] = $agentcount;
			$member['ordercount0'] = $ordercount0;
			$member['ordermoney0'] = $ordermoney0;
			$member['order30'] = $order30;
			$member['commission_total'] = round($commission_total, 2);
			$member['commission_ok'] = round($commission_ok, 2);
			$member['commission_pay'] = round($commission_pay, 2);
			$member['level1'] = $level1;
			$member['level1_agentids'] = $level1_agentids;
			$member['level2'] = $level2;
			$member['level2_agentids'] = $level2_agentids;
			$member['level3'] = $level3;
			$member['level3_agentids'] = $level3_agentids;
			$member['agenttime'] = date('Y-m-d H:i', $member['agenttime']);
			$this->getInfo = $member;
			return $this->getInfo;
		}
		public function getAgents($orderid = 0) 
		{
			global $_W;
			global $_GPC;
			$agents = array();
			$order = pdo_fetch('select id,agentid,openid from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) 
			{
				return $agents;
			}
			$set = $this->getSet();
			$m1 = m('member')->getMember($order['agentid']);
			if (!(empty($m1)) && ($m1['isagent'] == 1) && ($m1['status'] == 1) && (0 < $set['level'])) 
			{
				$agents[] = $m1;
				if (!(empty($m1['agentid'])) && (1 < $set['level'])) 
				{
					$m2 = m('member')->getMember($m1['agentid']);
					if (!(empty($m2)) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) 
					{
						$agents[] = $m2;
						if (!(empty($m2['agentid'])) && (2 < $set['level'])) 
						{
							$m3 = m('member')->getMember($m2['agentid']);
							if (!(empty($m3)) && ($m3['isagent'] == 1) && ($m3['status'] == 1)) 
							{
								$agents[] = $m3;
							}
						}
					}
				}
			}
			return $agents;
		}
		public function getAgentsByMember($openid = '', $num = 3) 
		{
			global $_W;
			global $_GPC;
			$agents = array();
			$m = m('member')->getMember($openid);
			if (!(empty($m['agentid']))) 
			{
				$m1 = m('member')->getMember($m['agentid']);
				if (!(empty($m1)) && ($m1['isagent'] == 1) && ($m1['status'] == 1) && (0 < $num)) 
				{
					$agents[0] = $m1;
					if (!(empty($m1['agentid']))) 
					{
						$m2 = m('member')->getMember($m1['agentid']);
						if (!(empty($m2)) && ($m2['isagent'] == 1) && ($m2['status'] == 1) && (1 < $num)) 
						{
							$agents[1] = $m2;
							if (!(empty($m2['agentid']))) 
							{
								$m3 = m('member')->getMember($m2['agentid']);
								if (!(empty($m3)) && ($m3['isagent'] == 1) && ($m3['status'] == 1) && (2 < $num)) 
								{
									$agents[2] = $m3;
								}
							}
						}
					}
				}
			}
			return $agents;
		}
		public function getAgentsDownNum($openid = NULL) 
		{
			global $_W;
			$openid = ((isset($openid) ? $openid : $_W['openid']));
			$set = $this->getSet();
			$member = $this->getInfo($openid);
			$levelcount1 = $member['level1'];
			$levelcount2 = $member['level2'];
			$levelcount3 = $member['level3'];
			$level1 = $level2 = $level3 = 0;
			$level1 = (int) pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
			if ((2 <= $set['level']) && (0 < $levelcount1)) 
			{
				$level2 = (int) pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			}
			if ((3 <= $set['level']) && (0 < $levelcount2)) 
			{
				$level3 = (int) pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			}
			$total = $level1 + $level2 + $level3;
			return array('level1' => $level1, 'level2' => $level2, 'level3' => $level3, 'total' => $total);
		}
		public function isAgent($openid) 
		{
			if (empty($openid)) 
			{
				return false;
			}
			if (is_array($openid)) 
			{
				return ($openid['isagent'] == 1) && ($openid['status'] == 1);
			}
			$member = m('member')->getMember($openid);
			return ($member['isagent'] == 1) && ($member['status'] == 1);
		}
		public function createMyShopQrcode($mid = 0, $posterid = 0) 
		{
			global $_W;
			$path = IA_ROOT . '/addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'];
			if (!(is_dir($path))) 
			{
				load()->func('file');
				mkdirs($path);
			}
			$url = mobileUrl('commission/myshop', array('mid' => $mid), true);
			if (!(empty($posterid))) 
			{
				$url .= '&posterid=' . $posterid;
			}
			$file = 'myshop_' . $posterid . '_' . $mid . '.png';
			$qr_file = $path . '/' . $file;
			if (!(is_file($qr_file))) 
			{
				require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
				QRcode::png($url, $qr_file, QR_ECLEVEL_H, 4);
			}
			return $_W['siteroot'] . 'addons/ewei_shopv2/data/qrcode/' . $_W['uniacid'] . '/' . $file;
		}
		private function createImage($url) 
		{
			load()->func('communication');
			$resp = ihttp_request($url);
			return imagecreatefromstring($resp['content']);
		}
		public function createShopImage() 
		{
			global $_W;
			global $_GPC;
			$shop_set = set_medias($_W['shopset']['shop'], 'signimg');
			$path = IA_ROOT . '/addons/ewei_shopv2/data/poster/' . $_W['uniacid'] . '/';
			if (!(is_dir($path))) 
			{
				load()->func('file');
				mkdirs($path);
			}
			$mid = intval($_GPC['mid']);
			$openid = $_W['openid'];
			$me = m('member')->getMember($openid);
			if (($me['isagent'] == 1) && ($me['status'] == 1)) 
			{  
				$userinfo = $me;
			}
			else 
			{
				$mid = intval($_GPC['mid']);
				if (!(empty($mid))) 
				{
					$userinfo = m('member')->getMember($mid);
				}
			}
			$md5 = md5(json_encode(array('openid' => $openid, 'signimg' => $shop_set['signimg'], 'shopset' => $shop_set, 'version' => 4)));
			$file = $md5 . '.jpg';
			if (!(is_file($path . $file))) 
			{
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$font = IA_ROOT . '/addons/ewei_shopv2/static/fonts/iconfont.ttf';
				$target = imagecreatetruecolor(640, 1225);
				$bc = imagecolorallocate($target, 0, 3, 51);
				$cc = imagecolorallocate($target, 240, 102, 0);
				$wc = imagecolorallocate($target, 255, 255, 255);
				$yc = imagecolorallocate($target, 255, 255, 0);
				$bg = imagecreatefromjpeg(IA_ROOT . '/addons/ewei_shopv2/plugin/commission/static/images/poster.jpg');
				imagecopy($target, $bg, 0, 0, 0, 0, 640, 1225);
				imagedestroy($bg);
				if (!(empty($userinfo['avatar']))) 
				{
					$avatar = preg_replace('/\\/0$/i', '/96', $userinfo['avatar']);
					$head = $this->createImage($avatar);
					$w = imagesx($head);
					$h = imagesy($head);
					imagecopyresized($target, $head, 24, 32, 0, 0, 88, 88, $w, $h);
					imagedestroy($head);
				}
				if (!(empty($shop_set['signimg']))) 
				{
					$thumb = $this->createImage($shop_set['signimg']);
					$w = imagesx($thumb);
					$h = imagesy($thumb);
					imagecopyresized($target, $thumb, 0, 160, 0, 0, 640, 640, $w, $h);
					imagedestroy($thumb);
				}
				$qrcode_file = tomedia($this->createMyShopQrcode($userinfo['id']));
				$qrcode = $this->createImage($qrcode_file);
				$w = imagesx($qrcode);
				$h = imagesy($qrcode);
				imagecopyresized($target, $qrcode, 50, 835, 0, 0, 250, 250, $w, $h);
				imagedestroy($qrcode);
				$str1 = '我是';
				imagettftext($target, 20, 0, 150, 70, $bc, $font, $str1);
				imagettftext($target, 20, 0, 210, 70, $cc, $font, $userinfo['nickname']);
				$str2 = '我要为';
				imagettftext($target, 20, 0, 150, 105, $bc, $font, $str2);
				$str3 = $shop_set['name'];
				imagettftext($target, 20, 0, 240, 105, $cc, $font, $str3);
				$box = imagettfbbox(20, 0, $font, $str3);
				$width = $box[4] - $box[6];
				$str4 = '代言';
				imagettftext($target, 20, 0, 240 + $width + 10, 105, $bc, $font, $str4);
				imagejpeg($target, $path . $file);
				imagedestroy($target);
			}
			return $_W['siteroot'] . 'addons/ewei_shopv2/data/poster/' . $_W['uniacid'] . '/' . $file;
		}
		public function checkAgent($openid = '') 
		{
			global $_W;
			global $_GPC;
			$set = $this->getSet();
			if (empty($set['level'])) 
			{
				return;
			}
			if (empty($openid)) 
			{
				return;
			}
			$member = m('member')->getMember($openid);
			if (empty($member)) 
			{
				return;
			}
			$parent = false;
			$mid = intval($_GPC['mid']);
			if (!(empty($mid))) 
			{
				$parent = m('member')->getMember($mid);
			}
			$parent_is_agent = !(empty($parent)) && ($parent['isagent'] == 1) && ($parent['status'] == 1);
			if ($member['isagent'] == 1) 
			{
				return;
			}
			$first = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where id<>:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['id']));
			if ($first <= 0) 
			{
				pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
				return;
			}
			$time = time();
			if ($parent_is_agent && empty($member['agentid'])) 
			{
				if ($member['id'] != $parent['id']) 
				{
					     pdo_update('ewei_shop_member', array('agentid' => $parent['id'], 'childtime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
				}
			}
		}
		public function checkOrderConfirm($orderid = '0') 
		{
			global $_W;
			global $_GPC;
			if (empty($orderid)) 
			{
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) 
			{
				return;
			}
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) 
			{
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) 
			{
				return;
			}
			$parent = false;
			$parent = m('member')->getMember($member['agentid']);
			$parent_is_agent = !(empty($parent)) && ($parent['isagent'] == 1) && ($parent['status'] == 1);
			$time = time();
			$become_child = intval($set['become_child']);
			if ($parent_is_agent) 
			{
				if (empty($member['agentid']) && ($member['id'] != $parent['id'])) 
				{
					if (empty($member['fixagentid'])) 
					{
						$member['agentid'] = $parent['id'];
						pdo_update('ewei_shop_member', array('agentid' => $parent['id'], 'childtime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
					}
				}
			}
			$agentid = $member['agentid'];
			if (!(empty($agentid))) 
			{
				pdo_update('ewei_shop_order', array('agentid' => $agentid), array('id' => $orderid));
			}
			$this->calculate($orderid);
		}
		public function checkOrderPay($orderid = '0') 
		{
			global $_W;
			global $_GPC;
			if (empty($orderid)) 
			{
				return;
			}
			$set = $this->getSet();
			if (empty($set['level'])) 
			{
				return;
			}
			$order = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) 
			{
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) 
			{
				return;
			}
			$parent = false;
				$parent = m('member')->getMember($member['agentid']);
			$parent_is_agent = !(empty($parent)) && ($parent['isagent'] == 1) && ($parent['status'] == 1);
			$time = time();
			$isagent = ($member['isagent'] == 1) && ($member['status'] == 1);
			if (!($isagent)) 
			{
				if ((intval($set['become']) == 4) && !(empty($set['become_goodsid']))) 
				{
					if (empty($set['become_order'])) 
					{
						$order_goods = pdo_fetchall('select goodsid from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']), 'goodsid');
						if (in_array($set['become_goodsid'], array_keys($order_goods))) 
						{
							if (empty($member['agentblack'])) 
							{
								pdo_update('ewei_shop_member', array('status' => $become_check, 'isagent' => 1, 'agenttime' => ($become_check == 1 ? $time : 0)), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
							}
						}
					}
				}
				else 
				{
					if (($set['become'] == 2) || ($set['become'] == 3)) 
					{
						if (empty($set['become_order'])) 
						{
							$time = time();
							$parentisagent = true;
							if (!(empty($member['agentid']))) 
							{
								$parent = m('member')->getMember($member['agentid']);
								if (empty($parent) || ($parent['isagent'] != 1) || ($parent['status'] != 1)) 
								{
									$parentisagent = false;
								}
							}
							$can = false;
							if ($set['become'] == '2') 
							{
								$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
								$can = intval($set['become_ordercount']) <= $ordercount;
							}
							else if ($set['become'] == '3') 
							{
								$moneycount = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
								$can = floatval($set['become_moneycount']) <= $moneycount;
							}
							if ($can) 
							{
								if (empty($member['agentblack'])) 
								{
									pdo_update('ewei_shop_member', array('status' => $become_check, 'isagent' => 1, 'agenttime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
								}
							}
						}
					}
				}
			}
			if (!(empty($member['agentid']))) 
			{
				$parent = m('member')->getMember($member['agentid']);
				if (!(empty($parent)) && ($parent['isagent'] == 1) && ($parent['status'] == 1)) 
				{
					$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
					$goods = '';
					$commission_total1 = 0;
					$commission_total2 = 0;
					$commission_total3 = 0;
					$pricetotal = 0;
					foreach ($order_goods as $og ) 
					{
						$goods .= '' . $og['title'] . '( ';
						if (!(empty($og['optiontitle']))) 
						{
							$goods .= ' 规格: ' . $og['optiontitle'];
						}
						$goods .= ' 单价: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['realprice'] . '); ';
						$commissions = iunserializer($og['commissions']);
						$commission_total1 += ((isset($commissions['level1']) ? floatval($commissions['level1']) : 0));
						$commission_total2 += ((isset($commissions['level2']) ? floatval($commissions['level2']) : 0));
						$commission_total3 += ((isset($commissions['level3']) ? floatval($commissions['level3']) : 0));
						$pricetotal += $og['realprice'];
					}
				}
			}
			if ($isagent) 
			{
				if ($member['ispartner']) 
				{
					return;
				}
				$become_check = intval($set['become_check']);
				if ((intval($set['become']) == 4) && !(empty($set['become_goodsid']))) 
				{
					if (empty($set['become_order'])) 
					{
						$order_goods = pdo_fetchall('select goodsid from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']), 'goodsid');
						if (in_array($set['become_goodsid'], array_keys($order_goods))) 
						{
							if (empty($member['partnerblack'])) 
							{
								pdo_update('ewei_shop_member', array('partnerstatus' => $become_check, 'ispartner' => 1, 'partnertime' => ($become_check == 1 ? $time : 0)), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
							}
						}
					}
				}
				else 
				{
					if (($set['become'] == 2) || ($set['become'] == 3)) 
					{
						if (empty($set['become_order'])) 
						{
							$time = time();
							$can = false;
							if ($set['become'] == '2') 
							{
								$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
								$can = intval($set['become_ordercount']) <= $ordercount;
							}
							else if ($set['become'] == '3') 
							{
								$moneycount = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
								$can = floatval($set['become_moneycount']) <= $moneycount;
							}
							if ($can) 
							{
								if (empty($member['partnerblack'])) 
								{
									pdo_update('ewei_shop_member', array('partnerstatus' => $become_check, 'ispartner' => 1, 'partnertime' => $time), array('uniacid' => $_W['uniacid'], 'id' => $member['id']));
								}
							}
						}
					}
				}
			}
		}
		public function checkOrderFinish($orderid = '') 
		{
			global $_W;
			global $_GPC;
			if (empty($orderid)) 
			{
				return;
			}
			$order = pdo_fetch('select id,openid, ordersn,goodsprice,agentid,finishtime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=3 and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
			if (empty($order)) 
			{
				return;
			}
			$openid = $order['openid'];
			$member = m('member')->getMember($openid);
			if (empty($member)) 
			{
				return;
			}
			$time = time();
			$isagent = ($member['isagent'] == 1) && ($member['status'] == 1);
			$parentisagent = true;
			if (!(empty($member['agentid']))) 
			{
				$parent = m('member')->getMember($member['agentid']);
				if (empty($parent) || ($parent['isagent'] != 1) || ($parent['status'] != 1)) 
				{
					$parentisagent = false;
				}
			}
			if (!(empty($member['agentid']))) 
			{
				$parent = m('member')->getMember($member['agentid']);
				if (!(empty($parent)) && ($parent['isagent'] == 1) && ($parent['status'] == 1)) 
				{
					$order_goods = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,og.commission1,og.commissions from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $order['id']));
					$goods = '';
					$commission_total1 = 0;
					$commission_total2 = 0;
					$commission_total3 = 0;
					$pricetotal = 0;
					foreach ($order_goods as $og ) 
					{
						$goods .= '' . $og['title'] . '( ';
						$commissions = iunserializer($og['commissions']);
						$commission_total1 += ((isset($commissions['level1']) ? floatval($commissions['level1']) : 0));
						$commission_total2 += ((isset($commissions['level2']) ? floatval($commissions['level2']) : 0));
						$commission_total3 += ((isset($commissions['level3']) ? floatval($commissions['level3']) : 0));
						$pricetotal += $og['realprice'];
					}
				}
			}
		}
		public function getShop($m) 
		{
			global $_W;
			$member = m('member')->getMember($m);
			$sysset = m('common')->getSysset(array('shop', 'share'));
			$set = $sysset['shop'];
			$share = $sysset['share'];
			$desc = $share['desc'];
			if (empty($desc)) 
			{
				$desc = $set['description'];
			}
			if (empty($desc)) 
			{
				$desc = $set['name'];
			}
			$thisset = $this->getSet();
			if (empty($shop)) 
			{
				$shop = array('name' => $member['nickname'] . '的' . $thisset['texts']['shop'], 'logo' => $member['avatar'], 'desc' => $desc, 'img' => tomedia($set['img']));
			}
			else 
			{
				if (empty($shop['name'])) 
				{
					$shop['name'] = $member['nickname'] . '的' . $thisset['texts']['shop'];
				}
				if (empty($shop['logo'])) 
				{
					$shop['logo'] = tomedia($member['avatar']);
				}
				if (empty($shop['img'])) 
				{
					$shop['img'] = tomedia($set['img']);
				}
				if (empty($shop['desc'])) 
				{
					$shop['desc'] = $desc;
				}
			}
			return $shop;
		}
		public function getLevels($all = true, $default = false) 
		{
			global $_W;
			global $_S;
			if ($all) 
			{
				$levels = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			}
			else 
			{
				$levels = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
			}
			if ($default) 
			{
				$default = array('id' => '0', 'levelname' => (empty($_S['commission']['levelname']) ? '默认等级' : $_S['commission']['levelname']), 'commission1' => $_S['commission']['commission1'], 'commission2' => $_S['commission']['commission2'], 'commission3' => $_S['commission']['commission3'], 'withdraw' => (double) $_S['commission']['withdraw_default'], 'repurchase' => (double) $_S['commission']['repurchase_default']);
				$levels = array_merge(array($default), $levels);
			}
			return $levels;
		}
		public function getLevel($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$member = m('member')->getMember($openid);
			if (empty($member['agentlevel'])) 
			{
				return false;
			}
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $member['agentlevel']));
			return $level;
		}
		public function upgradeLevelByOrder($openid)
		{
			global $_W;

			if (empty($openid)) {
				return false;
			}

			$set = $this->getSet();

			if (empty($set['level'])) {
				return false;
			}

			$m = m('member')->getMember($openid);

			if (empty($m)) {
				return NULL;
			}

			$leveltype = intval($set['leveltype']);
			if (($leveltype == 4) || ($leveltype == 5)) {
				if (!empty($m['agentnotupgrade'])) {
					return NULL;
				}

				$oldlevel = $this->getLevel($m['openid']);

				if (empty($oldlevel['id'])) {
					$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
				}

				$orders = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				$ordermoney = $orders['ordermoney'];
				$ordercount = $orders['ordercount'];

				if ($leveltype == 4) {
					$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid  and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));

					if (empty($newlevel)) {
						return NULL;
					}

					if (!empty($oldlevel['id'])) {
						if ($oldlevel['id'] == $newlevel['id']) {
							return NULL;
						}

						if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) {
							return NULL;
						}
					}
				}
				else {
					if ($leveltype == 5) {
						$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));

						if (empty($newlevel)) {
							return NULL;
						}

						if (!empty($oldlevel['id'])) {
							if ($oldlevel['id'] == $newlevel['id']) {
								return NULL;
							}

							if ($newlevel['ordercount'] < $oldlevel['ordercount']) {
								return NULL;
							}
						}
					}
				}

				pdo_update('ewei_shop_member', array('agentlevel' => $newlevel['id']), array('id' => $m['id']));
			}
			else {
				if ((0 <= $leveltype) && ($leveltype <= 3)) {
					$agents = array();

					if (!empty($set['selfbuy'])) {
						$agents[] = $m;
					}

					if (!empty($m['agentid'])) {
						$m1 = m('member')->getMember($m['agentid']);

						if (!empty($m1)) {
							$agents[] = $m1;
							if (!empty($m1['agentid']) && ($m1['isagent'] == 1) && ($m1['status'] == 1)) {
								$m2 = m('member')->getMember($m1['agentid']);
								if (!empty($m2) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) {
									$agents[] = $m2;

									if (empty($set['selfbuy'])) {
										if (!empty($m2['agentid']) && ($m2['isagent'] == 1) && ($m2['status'] == 1)) {
											$m3 = m('member')->getMember($m2['agentid']);
											if (!empty($m3) && ($m3['isagent'] == 1) && ($m3['status'] == 1)) {
												$agents[] = $m3;
											}
										}
									}
								}
							}
						}
					}

					if (empty($agents)) {
						return NULL;
					}

					foreach ($agents as $agent) {
						$info = $this->getInfo($agent['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));

						if (!empty($info['agentnotupgrade'])) {
							continue;
						}

						$oldlevel = $this->getLevel($agent['openid']);

						if (empty($oldlevel['id'])) {
							$oldlevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
						}

						if ($leveltype == 0) {
							$ordermoney = $info['ordermoney3'];
							$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));

							if (empty($newlevel)) {
								continue;
							}

							if (!empty($oldlevel['id'])) {
								if ($oldlevel['id'] == $newlevel['id']) {
									continue;
								}

								if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) {
									continue;
								}
							}
						}
						else if ($leveltype == 1) {
							$ordermoney = $info['order13money'];
							$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by ordermoney desc limit 1', array(':uniacid' => $_W['uniacid']));

							if (empty($newlevel)) {
								continue;
							}

							if (!empty($oldlevel['id'])) {
								if ($oldlevel['id'] == $newlevel['id']) {
									continue;
								}

								if ($newlevel['ordermoney'] < $oldlevel['ordermoney']) {
									continue;
								}
							}
						}
						else if ($leveltype == 2) {
							$ordercount = $info['ordercount3'];
							$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));

							if (empty($newlevel)) {
								continue;
							}

							if (!empty($oldlevel['id'])) {
								if ($oldlevel['id'] == $newlevel['id']) {
									continue;
								}

								if ($newlevel['ordercount'] < $oldlevel['ordercount']) {
									continue;
								}
							}
						}
						else {
							if ($leveltype == 3) {
								$ordercount = $info['order13'];
								$newlevel = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid  and ' . $ordercount . ' >= ordercount and ordercount>0  order by ordercount desc limit 1', array(':uniacid' => $_W['uniacid']));

								if (empty($newlevel)) {
									continue;
								}

								if (!empty($oldlevel['id'])) {
									if ($oldlevel['id'] == $newlevel['id']) {
										continue;
									}

									if ($newlevel['ordercount'] < $oldlevel['ordercount']) {
										continue;
									}
								}
							}
						}

						pdo_update('ewei_shop_member', array('agentlevel' => $newlevel['id']), array('id' => $agent['id']));
					}
				}
			}
		}

		public function getTotals() 
		{
			global $_W;
			return array('total1' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where status=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'])), 'total2' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where status=2 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'])), 'total3' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where status=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'])), 'total_1' => pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where status=-1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'])));
		}
		protected function replaceArray(array $array, $str, $replace_str) 
		{
			foreach ($array as $key => &$value ) 
			{
				foreach ($value as $k => &$v ) 
				{
					$v = str_replace($str, $replace_str, $v);
				}
				unset($v);
			}
			unset($value);
			return $array;
		}
		public function getLastApply($mid, $type = -1) 
		{
			global $_W;
			$params = array(':uniacid' => $_W['uniacid'], ':mid' => $mid);
			$sql = 'select type,alipay,bankname,bankcard,realname from ' . tablename('ewei_shop_commission_apply') . ' where mid=:mid and uniacid=:uniacid';
			if (-1 < $type) 
			{
				$sql .= ' and type=:type';
				$params[':type'] = $type;
			}
			$sql .= ' order by id desc Limit 1';
			$data = pdo_fetch($sql, $params);
			return $data;
		}
		public function getRepurchase($openid, array $time) 
		{
			global $_W;
			if (empty($openid) || empty($time)) 
			{
				return;
			}
			$set = $this->getSet();
			$agentLevel = $this->getLevel($openid);
			if ($agentLevel) 
			{
				$repurchase_price = (double) $agentLevel['repurchase'];
			}
			else 
			{
				$repurchase_price = (double) $set['repurchase_default'];
			}
			$residue = 0;
			$month_array = array();
			foreach ($time as $value ) 
			{
				$time1 = strtotime(date($value . '-1'));
				$time2 = strtotime('+1 months', $time1);
				if (!(empty($repurchase_price))) 
				{
					$order_price = (double) pdo_fetchcolumn('SELECT SUM(price) as price FROM ' . tablename('ewei_shop_order') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid AND `status`>2 AND `createtime` BETWEEN :time1 AND :time2', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':time1' => $time1, ':time2' => $time2));
					$year_month = explode('-', $value);
					$year_month[0] = (int) $year_month[0];
					$year_month[1] = (int) $year_month[1];
					$residue_price = (double) pdo_fetchcolumn('SELECT SUM(repurchase) FROM ' . tablename('ewei_shop_commission_repurchase') . ' WHERE `uniacid`=:uniacid AND `openid`=:openid AND `year`=:year AND `month`=:month', array(':uniacid' => $_W['uniacid'], ':openid' => $openid, ':year' => $year_month[0], ':month' => $year_month[1]));
					$month_array[$value] = max($repurchase_price - ($order_price + $residue_price), 0);
				}
			}
			return $month_array;
		}
		public function compareLevel(array $level, array $levels = array()) 
		{
			global $_W;
			$old_key = -1;
			$new_key = -1;
			$levels = ((!(empty($levels)) ? $levels : $this->getLevels()));
			foreach ($levels as $kk => $vv ) 
			{
				if ($vv['id'] == $level[0]) 
				{
					$old_key = $kk;
				}
				if ($vv['id'] == $level[1]) 
				{
					$new_key = $kk;
				}
			}
			return $old_key < $new_key;
		}
		public function getAgentLevel($member, $mid) 
		{
			global $_W;
			$level1_agentids = $member['level1_agentids'];
			$level2_agentids = $member['level2_agentids'];
			$level3_agentids = $member['level3_agentids'];
			if (!(empty($level1_agentids))) 
			{
				if (array_key_exists($mid, $level1_agentids)) 
				{
					return 1;
				}
			}
			if (!(empty($level2_agentids))) 
			{
				if (array_key_exists($mid, $level2_agentids)) 
				{
					return 2;
				}
			}
			if (!(empty($level3_agentids))) 
			{
				if (array_key_exists($mid, $level3_agentids)) 
				{
					return 3;
				}
			}
			return 0;
		}
		public function getAllDown($openid) 
		{
			global $_W;
			if (empty($openid)) 
			{
				return false;
			}
			$uid = (int) $openid;
			if ($uid == 0) 
			{
				$info = pdo_fetch('select id,openid,uniacid,agentid,isagent,status from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
				if (empty($info)) 
				{
					return false;
				}
				$uid = $info['id'];
			}
			$agents = pdo_fetchall('select id,openid,uniacid,agentid,isagent,nickname,agenttime,createtime,avatar,status,realname,mobile,weixin from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid=:agentid', array(':uniacid' => $_W['uniacid'], ':agentid' => $uid));
			$ids = array();
			$openids = array();
			$users = array();
			foreach ($agents as $val ) 
			{
				$ids[] = $val['id'];
				$openids[] = $val['openid'];
				$users[$val['id']] = $val;
				if ($val['isagent'] && $val['status']) 
				{
					$arr = $this->getAllDown($val['id']);
					if ($arr) 
					{
						$ids = array_merge($ids, $arr['ids']);
						$openids = array_merge($openids, $arr['openids']);
						$users = array_merge($users, $arr['users']);
					}
				}
			}
			return array('ids' => $ids, 'openids' => $openids, 'users' => $users);
		}
		public function getAllDownOrder($openid, $start = 0, $end = 0) 
		{
			global $_W;
			$down = $this->getAllDown($openid);
			if (!(is_numeric($start))) 
			{
				$start = strtotime($start);
			}
			if (!(is_numeric($end))) 
			{
				$end = strtotime($end);
			}
			if (!(empty($down['openids']))) 
			{
				$order = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid=:uniacid AND openid IN (\'' . implode('\',\'', $down['openids']) . '\') AND createtime BETWEEN :time1 AND :time2 AND ccard>0', array(':uniacid' => $_W['uniacid'], ':time1' => $start, ':time2' => $end));
				if ($order) 
				{
					return array('openids' => $down['openids'], 'order' => $order);
				}
			}
			return false;
		}
	}
}
?>
