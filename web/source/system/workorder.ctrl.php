<?php
defined('IN_IA') or exit('Access Denied');

load()->classs('cloudapi');
if($do == 'display') {
	$siteurl = $_W['siteroot'];
	$cloud = new CloudApi();
	$data = $cloud->get('system','workorder', array('do'=>'siteworkorder'), 'json');
	if(is_error($data)) {
		itoast('无权限进入工单系统');
	}
	$iframe_url = $data['data']['url'].'&from='.urlencode($siteurl);
	template('system/workorder');
}

if($do == 'module') {

	$name = trim($_GPC['name']);
	if(empty($name)){
		itoast('模块参数错误');
	}
	$module = module_fetch($name);
	if(!$module) {
		itoast('未找到模块');
	}
	$module_name = $name;
	$module_version = $module['version'];
	$issystem = $module['issystem'];
	$module_type = 'module';
	$from = urlencode($_W['siteroot']);
	$param = http_build_query(compact('module_name', 'module_version', 'module_type', 'from'));
	$cloud = new CloudApi();
	$data = $cloud->get('system','workorder', array('do'=>'siteworkorder'), 'json');
	if(is_error($data)) {
		echo json_encode(array('errno'=>0, 'message'=>'无权限进入工单系统'));
	}
	$iframe_url = $data['data']['url'].'&'.$param;
	echo json_encode(array('errno'=>0, 'message'=>'', 'data'=> ['url'=>$iframe_url]));

}


