<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

if (!($action == 'material' && $do == 'delete')) {
	checkaccount();
}

if ($action != 'material-post') {
	define('FRAME', 'account');
}
if ($action == 'qr') {
	$platform_qr_permission = permission_check_account_user('platform_qr', false);
	if ($platform_qr_permission ===  false) {
		header("Location: ". url('platform/url2qr'));
	}
}

if ($action == 'url2qr') {
	define('ACTIVE_FRAME_URL', url('platform/qr'));
}