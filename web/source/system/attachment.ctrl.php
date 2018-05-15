<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('setting');
load()->model('attachment');

$dos = array('attachment', 'remote', 'buckets', 'oss', 'cos', 'qiniu', 'ftp');
$do = in_array($do, $dos) ? $do : 'global';
$_W['page']['title'] = '附件设置 - 系统管理';

if ($do == 'global') {
	$post_max_size = ini_get('post_max_size');
	$post_max_size = $post_max_size > 0 ? bytecount($post_max_size) / 1024 : 0;
	$upload_max_filesize = ini_get('upload_max_filesize');
	if (checksubmit('submit')) {
		$harmtype = array('asp','php','jsp','js','css','php3','php4','php5','ashx','aspx','exe','cgi');
		$upload = $_GPC['upload'];
		if (!empty($upload['image']['thumb'])) {
			$upload['image']['thumb'] = 1;
		} else {
			$upload['image']['thumb'] = 0;
		}
		$upload['image']['width'] = intval(trim($upload['image']['width']));
		if (!empty($upload['image']['thumb']) && empty($upload['image']['width'])) {
			itoast('请设置图片缩略宽度.', '', '');
		}
		$upload['image']['limit'] = max(0, min(intval(trim($upload['image']['limit'])), $post_max_size));
		if (empty($upload['image']['limit'])) {
			itoast('请设置图片上传支持的文件大小, 单位 KB.', '', '');
		}
		if (empty($upload['image']['extentions'])) {
			itoast('请添加支持的图片附件后缀类型', '', '');
		}
		if (!empty($upload['image']['extentions'])) {
			$upload['image']['extentions'] = explode("\n", $upload['image']['extentions']);
			foreach ($upload['image']['extentions'] as $key => &$row) {
				$row = trim($row);
				if (in_array($row, $harmtype)) {
					unset($upload['image']['extentions'][$key]);
					continue;
				}
			}
		}
		if (!is_array($upload['image']['extentions']) || count($upload['image']['extentions']) < 1) {
			itoast('请添加支持的图片附件后缀类型', '', '');
		}
		$upload['audio']['limit'] = max(0, min(intval(trim($upload['audio']['limit'])), $post_max_size));
		if (empty($upload['image']['limit'])) {
			itoast('请设置音频视频上传支持的文件大小, 单位 KB.', '', '');
		}
		if (!empty($upload['audio']['extentions'])) {
			$upload['audio']['extentions'] = explode("\n", $upload['audio']['extentions']);
			foreach ($upload['audio']['extentions'] as $key => &$row) {
				$row = trim($row);
				if (in_array($row, $harmtype)) {
					unset($upload['audio']['extentions'][$key]);
					continue;
				}
			}
		}
		if (!is_array($upload['audio']['extentions']) || count($upload['audio']['extentions']) < 1) {
			itoast('请添加支持的音频视频附件后缀类型', '', '');
		}
		setting_save($upload, 'upload');
		itoast('更新设置成功！', url('system/attachment'), 'success');
	}
	if (empty($_W['setting']['upload'])) {
		$upload = $_W['config']['upload'];
	} else {
		$upload = $_W['setting']['upload'];
	}
	if (empty($upload['image']['thumb'])) {
		$upload['image']['thumb'] = 0;
	} else {
		$upload['image']['thumb'] = 1;
	}
	$upload['image']['width'] = intval($upload['image']['width']);
	if (empty($upload['image']['width'])) {
		$upload['image']['width'] = 800;
	}
	if (!empty($upload['image']['extentions']) && is_array($upload['image']['extentions'])) {
		$upload['image']['extentions'] = implode("\n", $upload['image']['extentions']);
	}
	if (!empty($upload['audio']['extentions']) && is_array($upload['audio']['extentions'])) {
		$upload['audio']['extentions'] = implode("\n", $upload['audio']['extentions']);
	}
}

if ($do == 'remote') {
	if (checksubmit('submit')) {
		$remote = array(
			'type' => intval($_GPC['type']),
			'ftp' => array(
				'ssl' => intval($_GPC['ftp']['ssl']),
				'host' => $_GPC['ftp']['host'],
				'port' => $_GPC['ftp']['port'],
				'username' => $_GPC['ftp']['username'],
				'password' => strexists($_GPC['ftp']['password'], '*') ? $_W['setting']['remote']['ftp']['password'] : $_GPC['ftp']['password'],
				'pasv' => intval($_GPC['ftp']['pasv']),
				'dir' => $_GPC['ftp']['dir'],
				'url' => $_GPC['ftp']['url'],
				'overtime' => intval($_GPC['ftp']['overtime']),
			),
			'alioss' => array(
				'key' => $_GPC['alioss']['key'],
				'secret' => strexists($_GPC['alioss']['secret'], '*') ? $_W['setting']['remote']['alioss']['secret'] : $_GPC['alioss']['secret'],
				'bucket' => $_GPC['alioss']['bucket'],
			),
			'qiniu' => array(
				'accesskey' => trim($_GPC['qiniu']['accesskey']),
				'secretkey' => strexists($_GPC['qiniu']['secretkey'], '*') ? $_W['setting']['remote']['qiniu']['secretkey'] : trim($_GPC['qiniu']['secretkey']),
				'bucket' => trim($_GPC['qiniu']['bucket']),
				'url' => trim($_GPC['qiniu']['url'])
			),
			'cos' => array(
				'appid' => trim($_GPC['cos']['appid']),
				'secretid' => trim($_GPC['cos']['secretid']),
				'secretkey' => strexists(trim($_GPC['cos']['secretkey']), '*') ? $_W['setting']['remote']['cos']['secretkey'] : trim($_GPC['cos']['secretkey']),
				'bucket' => trim($_GPC['cos']['bucket']),
				'local' => trim($_GPC['cos']['local']),
				'url' => trim($_GPC['cos']['url'])
			)
		);
		if ($remote['type'] == ATTACH_OSS) {
			if (trim($remote['alioss']['key']) == '') {
				itoast('阿里云OSS-Access Key ID不能为空', '', '');
			}
			if (trim($remote['alioss']['secret']) == '') {
				itoast('阿里云OSS-Access Key Secret不能为空', '', '');
			}
			$buckets = attachment_alioss_buctkets($remote['alioss']['key'], $remote['alioss']['secret']);
			if (is_error($buckets)) {
				itoast('OSS-Access Key ID 或 OSS-Access Key Secret错误，请重新填写', '', '');
			}
			list($remote['alioss']['bucket'], $remote['alioss']['url']) = explode('@@', $_GPC['alioss']['bucket']);
			if (empty($buckets[$remote['alioss']['bucket']])) {
				itoast('Bucket不存在或是已经被删除', '', '');
			}
			$remote['alioss']['url'] = 'http://'.$remote['alioss']['bucket'].'.'.$buckets[$remote['alioss']['bucket']]['location'].'.aliyuncs.com';
			$remote['alioss']['ossurl'] = $buckets[$remote['alioss']['bucket']]['location'].'.aliyuncs.com';
			if(!empty($_GPC['custom']['url'])) {
				$url = trim($_GPC['custom']['url'],'/');
				if (!strexists($url, 'http://') && !strexists($url, 'https://')) {
					$url = 'http://'.$url;
				}
				$remote['alioss']['url'] = $url;
			}
		} elseif ($remote['type'] == ATTACH_FTP) {
			if (empty($remote['ftp']['host'])) {
				itoast('FTP服务器地址为必填项.', '', '');
			}
			if (empty($remote['ftp']['username'])) {
				itoast('FTP帐号为必填项.', '', '');
			}
			if (empty($remote['ftp']['password'])) {
				itoast('FTP密码为必填项.', '', '');
			}
		} elseif ($remote['type'] == ATTACH_QINIU) {
			if (empty($remote['qiniu']['accesskey'])) {
				itoast('请填写Accesskey', referer(), 'info');
			}
			if (empty($remote['qiniu']['secretkey'])) {
				itoast('secretkey', referer(), 'info');
			}
			if (empty($remote['qiniu']['bucket'])) {
				itoast('请填写bucket', referer(), 'info');
			}
			if (empty($remote['qiniu']['url'])) {
				itoast('请填写url', referer(), 'info');
			} else {
				$remote['qiniu']['url'] = strexists($remote['qiniu']['url'], 'http') ? trim($remote['qiniu']['url'], '/') : 'http://'. trim($remote['qiniu']['url'], '/');
			}
			$auth = attachment_qiniu_auth($remote['qiniu']['accesskey'], $remote['qiniu']['secretkey'], $remote['qiniu']['bucket']);
			if (is_error($auth)) {
				$message = $auth['message']['error'] == 'bad token' ? 'Accesskey或Secretkey填写错误， 请检查后重新提交' : 'bucket填写错误或是bucket所对应的存储区域选择错误，请检查后重新提交';
				itoast($message, referer(), 'info');
			}
		} elseif ($remote['type'] == ATTACH_COS) {
			if (empty($remote['cos']['appid'])) {
				itoast('请填写APPID', referer(), 'info');
			}
			if (empty($remote['cos']['secretid'])) {
				itoast('请填写SECRETID', referer(), 'info');
			}
			if (empty($remote['cos']['secretkey'])) {
				itoast('请填写SECRETKEY', referer(), 'info');
			}
			if (empty($remote['cos']['bucket'])) {
				itoast('请填写BUCKET', referer(), 'info');
			}
			if (empty($remote['cos']['url'])) {
				$remote['cos']['url'] = 'http://'.$remote['cos']['bucket'].'-'.$remote['cos']['appid'].'.cos.myqcloud.com';
			} else {
				if (strexists($remote['cos']['url'], '.cos.myqcloud.com') && !strexists($url, '//'.$remote['cos']['bucket'].'-')) {
					$remote['cos']['url'] = 'http://'.$remote['cos']['bucket'].'-'.$remote['cos']['appid'].'.cos.myqcloud.com';
				}
				$remote['cos']['url'] = strexists($remote['cos']['url'], 'http') ? trim($remote['cos']['url'], '/') : 'http://'. trim($remote['cos']['url'], '/');
			}
			$auth = attachment_cos_auth($remote['cos']['bucket'], $remote['cos']['appid'], $remote['cos']['secretid'], $remote['cos']['secretkey'], $remote['cos']['local']);

			if (is_error($auth)) {
				itoast($auth['message'], referer(), 'info');
			}
		}
		setting_save($remote, 'remote');
		itoast('远程附件配置信息更新成功！', url('system/attachment/remote'), 'success');
	}
	$remote = $_W['setting']['remote'];
	$bucket_datacenter = attachment_alioss_datacenters();
} 

if ($do == 'buckets') {
	$key = $_GPC['key'];
	$secret = $_GPC['secret'];
	$buckets = attachment_alioss_buctkets($key, $secret);
	if (is_error($buckets)) {
		iajax(-1, '');
	}
	$bucket_datacenter = attachment_alioss_datacenters();
	$bucket = array();
	foreach ($buckets as $key => $value) {
		$value['loca_name'] = $key. '@@'. $bucket_datacenter[$value['location']];
		$bucket[] = $value;
	}
	iajax(1, $bucket, '');
}

if($do == 'ftp') {
	load()->library('ftp');
	$ftp_config = array(
		'hostname' => trim($_GPC['host']),
		'username' => trim($_GPC['username']),
		'password' => strexists($_GPC['password'], '*') ? $_W['setting']['remote']['ftp']['password'] : trim($_GPC['password']),
		'port' => intval($_GPC['port']),
		'ssl' => trim($_GPC['ssl']),
		'passive' => trim($_GPC['pasv']),
		'timeout' => intval($_GPC['overtime']),
		'rootdir' => trim($_GPC['dir']),
	);
	$url = trim($_GPC['url']);
	$filename = 'MicroEngine.ico';
	$ftp = new Ftp($ftp_config);
	if (true === $ftp->connect()) {
				if ($ftp->upload(ATTACHMENT_ROOT .'images/global/'. $filename, $filename)) {
			load()->func('communication');
			$response = ihttp_get($url. '/'. $filename);
			if (is_error($response)) {
				iajax(-1, '配置失败，FTP远程访问url错误');
			}
			if (intval($response['code']) != 200) {
				iajax(-1, '配置失败，FTP远程访问url错误');
			}
			$image = getimagesizefromstring($response['content']);
			if (!empty($image) && strexists($image['mime'], 'image')) {
				iajax(0,'配置成功');
			} else {
				iajax(-1, '配置失败，FTP远程访问url错误');
			}
		} else {
			iajax(-1, '上传图片失败，请检查配置');
		}
	} else {
		iajax(-1, 'FTP服务器连接失败，请检查配置');
	}
}

if ($do == 'oss') {
	load()->model('attachment');
	$key = $_GPC['key'];
	$secret = strexists($_GPC['secret'], '*') ? $_W['setting']['remote']['alioss']['secret'] : $_GPC['secret'];
	$bucket = $_GPC['bucket'];
	$buckets = attachment_alioss_buctkets($key, $secret);
	list($bucket, $url) = explode('@@', $_GPC['bucket']);
	$result = attachment_newalioss_auth($key, $secret, $bucket,$url);
	if (is_error($result)) {
		iajax(-1, 'OSS-Access Key ID 或 OSS-Access Key Secret错误，请重新填写');
	}
	$ossurl = $buckets[$bucket]['location'].'.aliyuncs.com';
	if (!empty($_GPC['url'])) {
		if (!strexists($_GPC['url'], 'http://') && !strexists($_GPC['url'],'https://')) {
			$url = 'http://'. trim($_GPC['url']);
		} else {
			$url = trim($_GPC['url']);
		}
		$url = trim($url, '/').'/';
	} else {
		$url = 'http://'.$bucket.'.'.$buckets[$bucket]['location'].'.aliyuncs.com/';
	}
	load()->func('communication');
	$filename = 'MicroEngine.ico';
	$response = ihttp_request($url. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
	if (is_error($response)) {
		iajax(-1, '配置失败，阿里云访问url错误');
	}
	if (intval($response['code']) != 200) {
		iajax(-1, '配置失败，阿里云访问url错误,请保证bucket为公共读取的');
	}
	$image = getimagesizefromstring($response['content']);
	if (!empty($image) && strexists($image['mime'], 'image')) {
		iajax(0,'配置成功');
	} else {
		iajax(-1, '配置失败，阿里云访问url错误');
	}
}

if ($do == 'qiniu') {
	load()->model('attachment');
	$_GPC['secretkey'] = strexists($_GPC['secretkey'], '*') ? $_W['setting']['remote']['qiniu']['secretkey'] : $_GPC['secretkey'];
	$auth= attachment_qiniu_auth(trim($_GPC['accesskey']), trim($_GPC['secretkey']), trim($_GPC['bucket']));
	if (is_error($auth)) {
		iajax(-1, '配置失败，请检查配置。注：请检查存储区域是否选择的是和bucket对应<br/>的区域', '');
	}
	load()->func('communication');
	$url = $_GPC['url'];
	$url = strexists($url, 'http') ? trim($url, '/') : 'http://'.trim($url, '/');
	$filename = 'MicroEngine.ico';
	$response = ihttp_request($url. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
	if (is_error($response)) {
		iajax(-1, '配置失败，七牛访问url错误');
	}
	if (intval($response['code']) != 200) {
		iajax(-1, '配置失败，七牛访问url错误,请保证bucket为公共读取的');
	}
	$image = getimagesizefromstring($response['content']);
	if (!empty($image) && strexists($image['mime'], 'image')) {
		iajax(0,'配置成功');
	} else {
		iajax(-1, '配置失败，七牛访问url错误');
	}
}

if ($do == 'cos') {
	load()->model('attachment');
	$url = $_GPC['url'];
	if (empty($url)) {
		$url = 'http://'.$_GPC['bucket'].'-'. $_GPC['appid'].'.cos.myqcloud.com';
	}
	$bucket =  trim($_GPC['bucket']);
	$_GPC['secretkey'] = strexists($_GPC['secretkey'], '*') ? $_W['setting']['remote']['cos']['secretkey'] : $_GPC['secretkey'];
	if (!strexists($url, '//'.$bucket.'-') && strexists($url, '.cos.myqcloud.com')) {
		$url = 'http://'.$bucket.'-'.trim($_GPC['appid']).'.cos.myqcloud.com';
	}
	$auth= attachment_cos_auth(trim($_GPC['bucket']), trim($_GPC['appid']), trim($_GPC['secretid']), trim($_GPC['secretkey']), $_GPC['local']);

	if (is_error($auth)) {
		iajax(-1, '配置失败，请检查配置', '');
	}
	load()->func('communication');
	$url = strexists($url, 'http') ? trim($url, '/') : 'http://'.trim($url, '/');
	$filename = 'MicroEngine.ico';
	$response = ihttp_request($url. '/'.$filename, array(), array('CURLOPT_REFERER' => $_SERVER['SERVER_NAME']));
	if (is_error($response)) {
		iajax(-1, '配置失败，腾讯cos访问url错误');
	}
	if (intval($response['code']) != 200) {
		iajax(-1, '配置失败，腾讯cos访问url错误,请保证bucket为公共读取的');
	}
	$image = getimagesizefromstring($response['content']);
	if (!empty($image) && strexists($image['mime'], 'image')) {
		iajax(0,'配置成功');
	} else {
		iajax(-1, '配置失败，腾讯cos访问url错误');
	}
}

template('system/attachment');