<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function wxapp_getpackage($data, $if_single = false) {
	load()->classs('cloudapi');

	$api = new CloudApi();
	$result = $api->post('wxapp', 'download', $data, 'html');
	if (is_error($result)) {
			return error(-1, $result['message']);
	} else {
		if (strpos($result, 'error:') === 0 ) {
			return error(-1, substr($result, 6));
		}
	}
	return $result;
}

function wxapp_account_create($account) {
	global $_W;
	load()->model('account');
	load()->model('user');
	$uni_account_data = array(
		'name' => $account['name'],
		'description' => $account['description'],
		'title_initial' => get_first_pinyin($account['name']),
		'groupid' => 0,
	);
	if (!pdo_insert('uni_account', $uni_account_data)) {
		return error(1, '添加公众号失败');
	}
	$uniacid = pdo_insertid();
	$account_data = array(
		'uniacid' => $uniacid,
		'type' => $account['type'],
		'hash' => random(8)
	);
	pdo_insert('account', $account_data);

	$acid = pdo_insertid();

	$wxapp_data = array(
		'acid' => $acid,
		'token' => random(32),
		'encodingaeskey' => random(43),
		'uniacid' => $uniacid,
		'name' => $account['name'],
		'original' => $account['original'],
		'level' => $account['level'],
		'key' => $account['key'],
		'secret' => $account['secret'],
	);
	pdo_insert('account_wxapp', $wxapp_data);

	if (empty($_W['isfounder'])) {
		uni_user_account_role($uniacid, $_W['uid'], ACCOUNT_MANAGE_NAME_OWNER);
	}
	if (user_is_vice_founder()) {
		uni_user_account_role($uniacid, $_W['uid'], ACCOUNT_MANAGE_NAME_VICE_FOUNDER);
	}
	if (!empty($_W['user']['owner_uid'])) {
		uni_user_account_role($uniacid, $_W['user']['owner_uid'], ACCOUNT_MANAGE_NAME_VICE_FOUNDER);
	}
	pdo_update('uni_account', array('default_acid' => $acid), array('uniacid' => $uniacid));

	return $uniacid;
}


function wxapp_support_wxapp_modules() {
	global $_W;
	load()->model('user');
	$modules = user_modules($_W['uid']);
	if (!empty($modules)) {
		foreach ($modules as $module) {
			if ($module['wxapp_support'] == MODULE_SUPPORT_WXAPP) {
				$wxapp_modules[$module['name']] = $module;
			}
		}
	}
	if (empty($wxapp_modules)) {
		return array();
	}
	$bindings = pdo_getall('modules_bindings', array('module' => array_keys($wxapp_modules), 'entry' => 'page'));
	if (!empty($bindings)) {
		foreach ($bindings as $bind) {
			$wxapp_modules[$bind['module']]['bindings'][] = array('title' => $bind['title'], 'do' => $bind['do']);
		}
	}
	return $wxapp_modules;
}


function wxapp_support_uniacid_modules() {
	$uni_modules = uni_modules();
	$wxapp_modules = array();
	if (!empty($uni_modules)) {
		foreach ($uni_modules as $module_name => $module_info) {
			if ($module_info['wxapp_support'] == MODULE_SUPPORT_WXAPP) {
				$wxapp_modules[$module_name] = $module_info;
			}
		}
	}
	return $wxapp_modules;
}


function wxapp_fetch($uniacid, $version_id = '') {
	global $_GPC;
	load()->model('extension');
	$wxapp_info = array();
	$uniacid = intval($uniacid);
	if (empty($uniacid)) {
		return $wxapp_info;
	}
	if (!empty($version_id)) {
		$version_id = intval($version_id);
	}

	$wxapp_info = pdo_get('account_wxapp', array('uniacid' => $uniacid));
	if (empty($wxapp_info)) {
		return $wxapp_info;
	}

	if (empty($version_id)) {
		$wxapp_cookie_uniacids = array();
		if (!empty($_GPC['__wxappversionids'])) {
			$wxappversionids = json_decode(htmlspecialchars_decode($_GPC['__wxappversionids']), true);
			foreach ($wxappversionids as $version_val) {
				$wxapp_cookie_uniacids[] = $version_val['uniacid'];
			}
		}
		if (in_array($uniacid, $wxapp_cookie_uniacids)) {
			$wxapp_version_info = wxapp_version($wxappversionids[$uniacid]['version_id']);
		}

		if (empty($wxapp_version_info)) {
			$sql ="SELECT * FROM " . tablename('wxapp_versions') . " WHERE `uniacid`=:uniacid ORDER BY `id` DESC";
			$wxapp_version_info = pdo_fetch($sql, array(':uniacid' => $uniacid));
		}
	} else {
		$wxapp_version_info = pdo_get('wxapp_versions', array('id' => $version_id));
	}
	if (!empty($wxapp_version_info) && !empty($wxapp_version_info['modules'])) {
		$wxapp_version_info['modules'] = iunserializer($wxapp_version_info['modules']);
				if ($wxapp_version_info['design_method'] == WXAPP_MODULE) {
			$module = current($wxapp_version_info['modules']);
			$manifest = ext_module_manifest($module['name']);
			if (!empty($manifest)) {
				$wxapp_version_info['modules'][$module['name']]['version'] = $manifest['application']['version'];
			} else {
				$last_install_module = module_fetch($module['name']);
				$wxapp_version_info['modules'][$module['name']]['version'] = $last_install_module['version'];
			}
		}
	}
	$wxapp_info['version'] = $wxapp_version_info;
	$wxapp_info['version_num'] = explode('.', $wxapp_version_info['version']);
	return  $wxapp_info;
}

function wxapp_version_all($uniacid) {
	load()->model('module');
	$wxapp_versions = array();
	$uniacid = intval($uniacid);

	if (empty($uniacid)) {
		return $wxapp_versions;
	}

	$wxapp_versions = pdo_getall('wxapp_versions', array('uniacid' => $uniacid), array('id'), '', array("id DESC"));
	if (!empty($wxapp_versions)) {
		foreach ($wxapp_versions as &$version) {
			$version = wxapp_version($version['id']);
		}
	}
	return $wxapp_versions;
}


function wxapp_get_some_lastversions($uniacid) {
	$version_lasts = array();
	$uniacid = intval($uniacid);

	if (empty($uniacid)) {
		return $version_lasts;
	}
	$version_lasts = table('wxapp')->latestVersion($uniacid);
	$last_switch_version = wxapp_last_switch_version();
	if (!empty($last_switch_version[$uniacid]) && !empty($version_lasts[$last_switch_version[$uniacid]['version_id']])) {
		$version_lasts[$last_switch_version[$uniacid]['version_id']]['current'] = true;
	} else {
		reset($version_lasts);
		$firstkey = key($version_lasts);
		$version_lasts[$firstkey]['current'] = true;
	}
	return $version_lasts;
}


function wxapp_update_last_use_version($uniacid, $version_id) {
	global $_GPC;
	$uniacid = intval($uniacid);
	$version_id = intval($version_id);
	if (empty($uniacid) || empty($version_id)) {
		return false;
	}
	$cookie_val = array();
	if (!empty($_GPC['__wxappversionids'])) {
		$wxapp_uniacids = array();
		$cookie_val = json_decode(htmlspecialchars_decode($_GPC['__wxappversionids']), true);
		if (!empty($cookie_val)) {
			foreach ($cookie_val as &$version) {
				$wxapp_uniacids[] = $version['uniacid'];
				if ($version['uniacid'] == $uniacid) {
					$version['version_id'] = $version_id;
					$wxapp_uniacids = array();
					break;
				}
			}
			unset($version);
		}
		if (!empty($wxapp_uniacids) && !in_array($uniacid, $wxapp_uniacids)) {
			$cookie_val[$uniacid] = array('uniacid' => $uniacid,'version_id' => $version_id);
		}
	} else {
		$cookie_val = array(
				$uniacid => array('uniacid' => $uniacid,'version_id' => $version_id)
			);
	}
	isetcookie('__uniacid', $uniacid);
	isetcookie('__wxappversionids', json_encode($cookie_val));
	return true;
}


function wxapp_version($version_id) {
	$version_info = array();
	$version_id = intval($version_id);

	if (empty($version_id)) {
		return $version_info;
	}

	$version_info = pdo_get('wxapp_versions', array('id' => $version_id));
	if (empty($version_info)) {
		return $version_info;
	}
	if (!empty($version_info['modules'])) {
		$version_info['modules'] = iunserializer($version_info['modules']);
		if (!empty($version_info['modules'])) {
			foreach ($version_info['modules'] as $i => $module) {
				if (!empty($module['uniacid'])) {
					$account = uni_fetch($module['uniacid']);
				}
				$module_info = module_fetch($module['name']);
				$module_info['account'] = $account;
				unset($version_info['modules'][$module['name']]);
				$version_info['modules'][] = $module_info;
			}
		}
	}
	if (!empty($version_info['quickmenu'])) {
		$version_info['quickmenu'] = iunserializer($version_info['quickmenu']);
	}
	return $version_info;
}


function wxapp_save_switch($uniacid) {
	global $_W, $_GPC;
	if (empty($_GPC['__switch'])) {
		$_GPC['__switch'] = random(5);
	}

	$cache_key = cache_system_key(CACHE_KEY_ACCOUNT_SWITCH, $_GPC['__switch']);
	$cache_lastaccount = (array)cache_load($cache_key);
	if (empty($cache_lastaccount)) {
		$cache_lastaccount = array(
			'wxapp' => $uniacid,
		);
	} else {
		$cache_lastaccount['wxapp'] = $uniacid;
	}
	cache_write($cache_key, $cache_lastaccount);
	isetcookie('__uniacid', $uniacid);
	isetcookie('__switch', $_GPC['__switch'], 7 * 86400);
	return true;
}

function wxapp_switch($uniacid, $redirect = '') {
	global $_W;
	wxapp_save_switch($uniacid);
	isetcookie('__uid', $_W['uid'], 7 * 86400);
	if (!empty($redirect)) {
		header('Location: ' . $redirect);
		exit;
	}
	return true;
}

function wxapp_site_info($multiid) {
	$site_info = array();
	$multiid = intval($multiid);

	if (empty($multiid)) {
		return array();
	}

	$site_info['slide'] = pdo_getall('site_slide', array('multiid' => $multiid));
	$site_info['nav'] = pdo_getall('site_nav', array('multiid' => $multiid));
	if (!empty($site_info['nav'])) {
		foreach ($site_info['nav'] as &$nav) {
			$nav['css'] = iunserializer($nav['css']);
		}
		unset($nav);
	}
	$recommend_sql = "SELECT a.name, b.* FROM " . tablename('site_category') . " AS a LEFT JOIN " . tablename('site_article') . " AS b ON a.id = b.pcate WHERE a.parentid = 0 AND a.multiid = :multiid";
	$site_info['recommend'] = pdo_fetchall($recommend_sql, array(':multiid' => $multiid));
	return $site_info;
}


function wxapp_payment_param() {
	global $_W;
	$setting = uni_setting_load('payment', $_W['uniacid']);
	$pay_setting = $setting['payment'];
	return $pay_setting;
}

function wxapp_update_daily_visittrend() {
	global $_W;
	$yesterday = date('Ymd', strtotime('-1 days'));
	$trend = pdo_get('wxapp_general_analysis', array('uniacid' => $_W['uniacid'], 'type' => WXAPP_STATISTICS_DAILYVISITTREND, 'ref_date' => $yesterday));
	if (!empty($trend)) {
		return true;
	}
	$account_api = WeAccount::create();
	$wxapp_stat = $account_api->getDailyVisitTrend();
	if (is_error($wxapp_stat) || empty($wxapp_stat)) {
		return error(-1, '调用微信接口错误');
	} else {
		$update_stat = array(
			'uniacid' => $_W['uniacid'],
			'session_cnt' => $wxapp_stat['session_cnt'],
			'visit_pv' => $wxapp_stat['visit_pv'],
			'visit_uv' => $wxapp_stat['visit_uv'],
			'visit_uv_new' => $wxapp_stat['visit_uv_new'],
			'type' => WXAPP_STATISTICS_DAILYVISITTREND,
			'stay_time_uv' => $wxapp_stat['stay_time_uv'],
			'stay_time_session' => $wxapp_stat['stay_time_session'],
			'visit_depth' => $wxapp_stat['visit_depth'],
			'ref_date' => $wxapp_stat['ref_date'],
		);
		pdo_insert('wxapp_general_analysis', $update_stat);
	}
	return true;
}

function wxapp_search_link_account($module_name = '') {
	global $_W;
	$module_name = trim($module_name);
	if (empty($module_name)) {
		return array();
	}
	$owned_account = uni_owned();
	if (!empty($owned_account)) {
		foreach ($owned_account as $key => $account) {
			$account['role'] = permission_account_user_role($_W['uid'], $account['uniacid']);
			if (!in_array($account['role'], array(ACCOUNT_MANAGE_NAME_OWNER, ACCOUNT_MANAGE_NAME_FOUNDER))) {
				unset($owned_account[$key]);
			}
		}
		foreach ($owned_account as $key => $account) {
			$account_modules = uni_modules_by_uniacid($account['uniacid']);
			if (empty($account_modules[$module_name])) {
				unset($owned_account[$key]);
			} elseif ($account_modules[$module_name]['app_support'] != MODULE_SUPPORT_ACCOUNT || $account_modules[$module_name]['wxapp_support'] != MODULE_SUPPORT_WXAPP) {
				unset($owned_account[$key]);
			}
		}
	}
	return $owned_account;
}


function wxapp_last_switch_version() {
	global $_GPC;
	static $wxapp_cookie_uniacids;
	if (empty($wxapp_cookie_uniacids) && !empty($_GPC['__wxappversionids'])) {
		$wxapp_cookie_uniacids = json_decode(htmlspecialchars_decode($_GPC['__wxappversionids']), true);
	}
	return $wxapp_cookie_uniacids;
}




function wxapp_code_generate($version_id) {
	global $_W;
	load()->classs('cloudapi');
	$api = new CloudApi();
	$version_info = wxapp_version($version_id);
	$account_wxapp_info = wxapp_fetch($version_info['uniacid'], $version_id);
	if (empty($account_wxapp_info)) {
		return error(1, '版本不存在');
	}
	$siteurl = $_W['siteroot'].'app/index.php';
	if(!empty($account_wxapp_info['appdomain'])) {
		$siteurl = $account_wxapp_info['appdomain'];
	}
	$appid = $account_wxapp_info['key'];
	$siteinfo = array(
		'name' => $account_wxapp_info['name'],
		'uniacid' => $account_wxapp_info['uniacid'],
		'acid' => $account_wxapp_info['acid'],
		'multiid' => $account_wxapp_info['version']['multiid'],
		'version' => $account_wxapp_info['version']['version'],
		'siteroot' => $siteurl,
		'design_method' => $account_wxapp_info['version']['design_method'],
	);
	$commit_data = array('do' => 'generate',
		'appid' => $appid,
		'modules' => $account_wxapp_info['version']['modules'],
		'siteinfo' => $siteinfo,
		'tabBar' => json_decode($account_wxapp_info['version']['quickmenu'], true),
	);
	$data = $api->post('wxapp', 'upload', $commit_data,
		'json', false);

	return $data;
}


function wxapp_check_code_isgen($code_uuid) {
	load()->classs('cloudapi');
	$api = new CloudApi();
	$data = $api->get('wxapp', 'upload', array('do'=>'check_gen',
		'code_uuid'=>$code_uuid),
		'json', false);
	return $data;
}


function wxapp_code_token() {
	global $_W;
	load()->classs('cloudapi');
	$cloud_api = new CloudApi();
	$data = $cloud_api->get('wxapp', 'upload', array('do' => 'code_token'), 'json', false);
	return $data;
}


function wxapp_code_qrcode($code_token) {

	$cloud_api = new CloudApi();
	$data = $cloud_api->get('wxapp', 'upload', array('do' => 'qrcode',
		'code_token' => $code_token),
		'html', false);
	return $data;
}


function wxapp_code_check_scan($code_token, $last) {
	$cloud_api = new CloudApi();
	$data = $cloud_api->get('wxapp', 'upload',
		array('do' => 'checkscan',
			'code_token' => $code_token,
			'last' => $last
		),
		'json', false);
	return $data;
}

function wxapp_code_preview_qrcode($code_uuid, $code_token) {
	$cloud_api = new CloudApi();

	$commit_data =  array(
		'do' => 'preview_qrcode',
		'code_uuid'=> $code_uuid,
		'code_token' => $code_token,
	);
	$data = $cloud_api->post('wxapp', 'upload', $commit_data,
		'json', false);

	return $data;
}

function wxapp_code_commit($code_uuid, $code_token, $user_version = 3, $user_desc = '代码提交') {
	$cloud_api = new CloudApi();

	$commit_data =  array(
		'do' => 'commitcode',
		'code_uuid'=> $code_uuid,
		'code_token' => $code_token,
		'user_version' => $user_version,
		'user_desc' => $user_desc,
	);
	$data = $cloud_api->post('wxapp', 'upload', $commit_data,
		'json', false);

	return $data;
}
