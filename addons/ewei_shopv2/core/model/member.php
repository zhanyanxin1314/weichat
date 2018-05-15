<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Member_EweiShopV2Model 
{
	public function getInfo($openid = '') 
	{
		global $_W;
		$uid = intval($openid);
		if ($uid == 0) 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			if (empty($info)) 
			{
				if (strexists($openid, 'sns_qq_')) 
				{
					$openid = str_replace('sns_qq_', '', $openid);
					$condition = ' openid_qq=:openid ';
					$bindsns = 'qq';
				}
				else if (strexists($openid, 'sns_wx_')) 
				{
					$openid = str_replace('sns_wx_', '', $openid);
					$condition = ' openid_wx=:openid ';
					$bindsns = 'wx';
				}
				else if (strexists($openid, 'sns_wa_')) 
				{
					$openid = str_replace('sns_wa_', '', $openid);
					$condition = ' openid_wa=:openid ';
					$bindsns = 'wa';
				}
				if (!(empty($condition))) 
				{
					$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where ' . $condition . '  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					if (!(empty($info))) 
					{
						$info['bindsns'] = $bindsns;
					}
				}
			}
		}
		else 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=:id  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $uid));
		}
		if (!(empty($info['uid']))) 
		{
			load()->model('mc');
			$uid = mc_openid2uid($info['openid']);
			$fans = mc_fetch($uid, array('credit1', 'credit2', 'birthyear', 'birthmonth', 'birthday', 'gender', 'avatar', 'resideprovince', 'residecity', 'nickname'));
			$info['credit1'] = $fans['credit1'];
			$info['credit2'] = $fans['credit2'];
			$info['birthyear'] = ((empty($info['birthyear']) ? $fans['birthyear'] : $info['birthyear']));
			$info['birthmonth'] = ((empty($info['birthmonth']) ? $fans['birthmonth'] : $info['birthmonth']));
			$info['birthday'] = ((empty($info['birthday']) ? $fans['birthday'] : $info['birthday']));
			$info['nickname'] = ((empty($info['nickname']) ? $fans['nickname'] : $info['nickname']));
			$info['gender'] = ((empty($info['gender']) ? $fans['gender'] : $info['gender']));
			$info['sex'] = $info['gender'];
			$info['avatar'] = ((empty($info['avatar']) ? $fans['avatar'] : $info['avatar']));
			$info['headimgurl'] = $info['avatar'];
			$info['province'] = ((empty($info['province']) ? $fans['resideprovince'] : $info['province']));
			$info['city'] = ((empty($info['city']) ? $fans['residecity'] : $info['city']));
		}
		if (!(empty($info['birthyear'])) && !(empty($info['birthmonth'])) && !(empty($info['birthday']))) 
		{
			$info['birthday'] = $info['birthyear'] . '-' . ((strlen($info['birthmonth']) <= 1 ? '0' . $info['birthmonth'] : $info['birthmonth'])) . '-' . ((strlen($info['birthday']) <= 1 ? '0' . $info['birthday'] : $info['birthday']));
		}
		if (empty($info['birthday'])) 
		{
			$info['birthday'] = '';
		}
		if (!(empty($info))) 
		{
			if (!(strexists($info['avatar'], 'http://')) && !(strexists($info['avatar'], 'https://'))) 
			{
				$info['avatar'] = tomedia($info['avatar']);
			}
			if ($_W['ishttps']) 
			{
				$info['avatar'] = str_replace('http://', 'https://', $info['avatar']);
			}
		}
		return $info;
	}
	public function getMember($openid = '') 
	{
		global $_W;
		$uid = (int) $openid;
		if ($uid == 0) 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			if (empty($info)) 
			{
				if (strexists($openid, 'sns_qq_')) 
				{
					$openid = str_replace('sns_qq_', '', $openid);
					$condition = ' openid_qq=:openid ';
					$bindsns = 'qq';
				}
				else if (strexists($openid, 'sns_wx_')) 
				{
					$openid = str_replace('sns_wx_', '', $openid);
					$condition = ' openid_wx=:openid ';
					$bindsns = 'wx';
				}
				else if (strexists($openid, 'sns_wa_')) 
				{
					$openid = str_replace('sns_wa_', '', $openid);
					$condition = ' openid_wa=:openid ';
					$bindsns = 'wa';
				}
				if (!(empty($condition))) 
				{
					$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where ' . $condition . '  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					if (!(empty($info))) 
					{
						$info['bindsns'] = $bindsns;
					}
				}
			}
		}
		else 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $openid));
		}
		if (!(empty($info))) 
		{
			if (!(strexists($info['avatar'], 'http://')) && !(strexists($info['avatar'], 'https://'))) 
			{
				$info['avatar'] = tomedia($info['avatar']);
			}
			if ($_W['ishttps']) 
			{
				$info['avatar'] = str_replace('http://', 'https://', $info['avatar']);
			}
		}
		return $info;
	}
	public function updateCredits($info) 
	{
		global $_W;
		$openid = $info['openid'];
		if (empty($info['uid'])) 
		{
			$followed = m('user')->followed($openid);
			if ($followed) 
			{
				load()->model('mc');
				$uid = mc_openid2uid($openid);
				if (!(empty($uid))) 
				{
					$info['uid'] = $uid;
					$upgrade = array('uid' => $uid);
					if (0 < $info['credit1']) 
					{
						mc_credit_update($uid, 'credit1', $info['credit1']);
						$upgrade['credit1'] = 0;
					}
					if (0 < $info['credit2']) 
					{
						mc_credit_update($uid, 'credit2', $info['credit2']);
						$upgrade['credit2'] = 0;
					}
					if (!(empty($upgrade))) 
					{
						pdo_update('ewei_shop_member', $upgrade, array('id' => $info['id']));
					}
				}
			}
		}
		return $info;
	}
	public function getMobileMember($mobile) 
	{
		global $_W;
		$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile));
		return $info;
	}
	public function getMid() 
	{
		global $_W;
		$openid = $_W['openid'];
		$member = $this->getMember($openid);
		return $member['id'];
	}
	public function checkMember() 
	{
		global $_W;
		global $_GPC;
		$member = array();
		$shopset = m('common')->getSysset(array('shop', 'wap'));
		$openid = $_W['openid'];
		if (($_W['routes'] == 'order.pay_alipay') || ($_W['routes'] == 'creditshop.log.dispatch_complete') || ($_W['routes'] == 'threen.register.threen_complete') || ($_W['routes'] == 'creditshop.detail.creditshop_complete') || ($_W['routes'] == 'order.pay_alipay.recharge_complete') || ($_W['routes'] == 'order.pay_alipay.complete') || ($_W['routes'] == 'newmr.alipay') || ($_W['routes'] == 'newmr.callback.gprs') || ($_W['routes'] == 'newmr.callback.bill') || ($_W['routes'] == 'account.sns') || ($_W['plugin'] == 'mmanage') || ($_W['routes'] == 'live.send.credit') || ($_W['routes'] == 'live.send.coupon')) 
		{
			return;
		}
		if ($shopset['wap']['open']) 
		{
			if (($shopset['wap']['inh5app'] && is_h5app()) || (empty($shopset['wap']['inh5app']) && empty($openid))) 
			{
				return;
			}
		}
		if (empty($openid) && !(EWEI_SHOPV2_DEBUG)) 
		{
			$diemsg = ((is_h5app() ? 'APP正在维护, 请到公众号中访问' : '请在微信客户端打开链接'));
			exit('<!DOCTYPE html>' . "\r\n" . '                <html>' . "\r\n" . '                    <head>' . "\r\n" . '                        <meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'>' . "\r\n" . '                        <title>抱歉，出错了</title><meta charset=\'utf-8\'><meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'><link rel=\'stylesheet\' type=\'text/css\' href=\'https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\'>' . "\r\n" . '                    </head>' . "\r\n" . '                    <body>' . "\r\n" . '                    <div class=\'page_msg\'><div class=\'inner\'><span class=\'msg_icon_wrp\'><i class=\'icon80_smile\'></i></span><div class=\'msg_content\'><h4>' . $diemsg . '</h4></div></div></div>' . "\r\n" . '                    </body>' . "\r\n" . '                </html>');
		}
		$member = $this->getMember($openid);
		$followed = m('user')->followed($openid);
		$uid = 0;
		$mc = array();
		load()->model('mc');
		if ($followed || empty($shopset['shop']['getinfo']) || ($shopset['shop']['getinfo'] == 1)) 
		{
			$uid = mc_openid2uid($openid);
			if (!(EWEI_SHOPV2_DEBUG)) 
			{
				$userinfo = mc_oauth_userinfo();
			}
			else 
			{
				$userinfo = array('openid' => $member['openid'], 'nickname' => $member['nickname'], 'headimgurl' => $member['avatar'], 'gender' => $member['gender'], 'province' => $member['province'], 'city' => $member['city']);
			}
			$mc = array();
			$mc['nickname'] = $userinfo['nickname'];
			$mc['avatar'] = $userinfo['headimgurl'];
			$mc['gender'] = $userinfo['sex'];
			$mc['resideprovince'] = $userinfo['province'];
			$mc['residecity'] = $userinfo['city'];
		}
		if (empty($member) && !(empty($openid))) 
		{
			$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => (!(empty($mc['realname'])) ? $mc['realname'] : ''), 'mobile' => (!(empty($mc['mobile'])) ? $mc['mobile'] : ''), 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'nickname_wechat' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'avatar_wechat' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : '-1'), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : ''), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : ''), 'area' => (!(empty($mc['residedist'])) ? $mc['residedist'] : ''), 'createtime' => time(), 'status' => 0);
			pdo_insert('ewei_shop_member', $member);
			$member['id'] = pdo_insertid();
		}
		else 
		{
			if ($member['isblack'] == 1) 
			{
				show_message('暂时无法访问，请稍后再试!');
			}
			$upgrade = array('uid' => $uid);
			if (isset($mc['nickname']) && ($member['nickname_wechat'] != $mc['nickname'])) 
			{
				$upgrade['nickname_wechat'] = $mc['nickname'];
			}
			if (isset($mc['nickname']) && empty($member['nickname'])) 
			{
				$upgrade['nickname'] = $mc['nickname'];
			}
			if (isset($mc['avatar']) && ($member['avatar_wechat'] != $mc['avatar'])) 
			{
				$upgrade['avatar_wechat'] = $mc['avatar'];
			}
			if (isset($mc['avatar']) && empty($member['avatar'])) 
			{
				$upgrade['avatar'] = $mc['avatar'];
			}
			if (isset($mc['gender']) && ($member['gender'] != $mc['gender'])) 
			{
				$upgrade['gender'] = $mc['gender'];
			}
			if (!(empty($upgrade))) 
			{
				pdo_update('ewei_shop_member', $upgrade, array('id' => $member['id']));
			}
		}
		if (p('commission')) 
		{
			p('commission')->checkAgent($openid);
		}
		if (p('poster')) 
		{
			p('poster')->checkScan($openid);
		}
		if (empty($member)) 
		{
			return false;
		}
		return array('id' => $member['id'], 'openid' => $member['openid']);
	}
	public function getLevels($all = true) 
	{
		global $_W;
		$condition = '';
		if (!($all)) 
		{
			$condition = ' and enabled=1';
		}
		return pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid' . $condition . ' order by level asc', array(':uniacid' => $_W['uniacid']));
	}
	public function getLevel($openid) 
	{
		global $_W;
		global $_S;
		if (empty($openid)) 
		{
			return false;
		}
		$member = m('member')->getMember($openid);
		if (!(empty($member)) && !(empty($member['level']))) 
		{
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $member['level'], ':uniacid' => $_W['uniacid']));
			if (!(empty($level))) 
			{
				return $level;
			}
		}
		return array('levelname' => (empty($_S['shop']['levelname']) ? '普通会员' : $_S['shop']['levelname']), 'discount' => (empty($_S['shop']['leveldiscount']) ? 10 : $_S['shop']['leveldiscount']));
	}
	public function upgradeLevel($openid, $orderid = 0) 
	{
		global $_W;
		if (empty($openid)) 
		{
			return;
		}
		$shopset = m('common')->getSysset('shop');
		$leveltype = intval($shopset['leveltype']);
		$member = m('member')->getMember($openid);
		if (empty($member)) 
		{
			return;
		}
		$level = false;
		if (empty($leveltype)) 
		{
			$ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid ' . ' where o.openid=:openid and o.status=3 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid  and enabled=1 and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		else if ($leveltype == 1) 
		{
			$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status=3 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and enabled=1 and ' . $ordercount . ' >= ordercount and ordercount>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		if (!(empty($orderid))) 
		{
			$goods_level = $this->getGoodsLevel($openid, $orderid);
			if (empty($level)) 
			{
				$level = $goods_level;
			}
			else if (!(empty($goods_level))) 
			{
				if ($level['level'] < $goods_level['level']) 
				{
					$level = $goods_level;
				}
			}
		}
		if (empty($level)) 
		{
			return;
		}
		if ($level['id'] == $member['level']) 
		{
			return;
		}
		$oldlevel = $this->getLevel($openid);
		$canupgrade = false;
		if (empty($oldlevel['id'])) 
		{
			$canupgrade = true;
		}
		else if ($oldlevel['level'] < $level['level']) 
		{
			$canupgrade = true;
		}
		if ($canupgrade) 
		{
			pdo_update('ewei_shop_member', array('level' => $level['id']), array('id' => $member['id']));
			com_run('wxcard::updateMemberCardByOpenid', $openid);
		}
	}
	public function mc_update($mid, $data) 
	{
		global $_W;
		if (empty($mid) || empty($data)) 
		{
			return;
		}
		$wapset = m('common')->getSysset('wap');
		$member = $this->getMember($mid);
		if (!(empty($wapset['open'])) && isset($data['mobile']) && ($data['mobile'] != $member['mobile'])) 
		{
			unset($data['mobile']);
		}
		load()->model('mc');
		mc_update($this->member['uid'], $data);
	}
}
?>
