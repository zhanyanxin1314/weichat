<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Page extends WeModuleSite
{
	public function template($filename = '', $type = TEMPLATE_INCLUDEPATH, $account = false)
	{
		global $_W;
		global $_GPC;
		$set = m('common')->getSysset('template');
		$isv3 = $set['style_v3'];

		if (isset($_W['shopversion'])) {
			$isv3 = $_W['shopversion'];
		}

		if ($isv3 && !empty($_GPC['v2'])) {
			$isv3 = false;
		}

		if (!empty($_W['plugin']) && $isv3) {
			$plugin_config = m('plugin')->getConfig($_W['plugin']);
			if ((is_array($plugin_config) && empty($plugin_config['v3'])) || !$plugin_config) {
				$isv3 = false;
			}
		}

		$bsaeTemp = array('_header', '_header_base', '_footer', '_tabs', 'funbar');
		if (($_W['plugin'] == 'merch') && $_W['merch_user'] && (!in_array($filename, $bsaeTemp) || !$isv3)) {
			return $this->template_merch($filename, $isv3);
		}

		if (empty($filename)) {
			$filename = str_replace('.', '/', $_W['routes']);
		}

		if (($_GPC['do'] == 'web') || defined('IN_SYS')) {
			$filename = str_replace('/add', '/post', $filename);
			$filename = str_replace('/edit', '/post', $filename);
			$filename_default = str_replace('/add', '/post', $filename);
			$filename_default = str_replace('/edit', '/post', $filename_default);
			$filename = 'web/' . $filename_default;
			$filename_v3 = 'web_v3/' . $filename_default;
		}

		$name = 'ewei_shopv2';
		$moduleroot = IA_ROOT . '/addons/ewei_shopv2';

		if (defined('IN_SYS')) {
			if (!$isv3) {
				$compile = IA_ROOT . '/data/tpl/web/' . $_W['template'] . '/' . $name . '/' . $filename . '.tpl.php';
				$source = $moduleroot . '/template/' . $filename . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/template/' . $filename . '/index.html';
				}
			}

			if ($isv3 || !is_file($source)) {
				if ($isv3) {
					$compile = IA_ROOT . '/data/tpl/web_v3/' . $_W['template'] . '/' . $name . '/' . $filename . '.tpl.php';
				}

				$source = $moduleroot . '/template/' . $filename_v3 . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/template/' . $filename_v3 . '/index.html';
				}
			}

			if (!is_file($source)) {
				$explode = array_slice(explode('/', $filename), 1);
				$temp = array_slice($explode, 1);

				if ($isv3) {
					$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web_v3/' . implode('/', $temp) . '.html';

					if (!is_file($source)) {
						$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web_v3/' . implode('/', $temp) . '/index.html';
					}
				}

				if (!$isv3 || !is_file($source)) {
					$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '.html';

					if (!is_file($source)) {
						$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '/index.html';
					}
				}
			}
		}
		else if ($account) {
			$template = $_W['shopset']['wap']['style'];

			if (empty($template)) {
				$template = 'default';
			}

			if (!is_dir($moduleroot . '/template/account/' . $template)) {
				$template = 'default';
			}

			$compile = IA_ROOT . '/data/tpl/app/' . $name . '/' . $template . '/account/' . $filename . '.tpl.php';
			$source = IA_ROOT . '/addons/' . $name . '/template/account/' . $template . '/' . $filename . '.html';

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/account/default/' . $filename . '.html';
			}

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/account/default/' . $filename . '/index.html';
			}
		}
		else {
			$template = m('cache')->getString('template_shop');

			if (empty($template)) {
				$template = 'default';
			}

			if (!is_dir($moduleroot . '/template/mobile/' . $template)) {
				$template = 'default';
			}

			$compile = IA_ROOT . '/data/tpl/app/' . $name . '/' . $template . '/mobile/' . $filename . '.tpl.php';
			$source = IA_ROOT . '/addons/' . $name . '/template/mobile/' . $template . '/' . $filename . '.html';

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/mobile/' . $template . '/' . $filename . '/index.html';
			}

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/mobile/default/' . $filename . '.html';
			}

			if (!is_file($source)) {
				$source = IA_ROOT . '/addons/' . $name . '/template/mobile/default/' . $filename . '/index.html';
			}

			if (!is_file($source)) {
				$names = explode('/', $filename);
				$pluginname = $names[0];
				$ptemplate = m('cache')->getString('template_' . $pluginname);
				if (empty($ptemplate) || ($pluginname == 'creditshop')) {
					$ptemplate = 'default';
				}

				if (!is_dir($moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate)) {
					$ptemplate = 'default';
				}

				unset($names[0]);
				$pfilename = implode('/', $names);
				$compile = IA_ROOT . '/data/tpl/app/' . $name . '/plugin/' . $pluginname . '/' . $ptemplate . '/mobile/' . $filename . '.tpl.php';
				$source = $moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate . '/' . $pfilename . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/plugin/' . $pluginname . '/template/mobile/' . $ptemplate . '/' . $pfilename . '/index.html';
				}
			}
		}

		if (!is_file($source)) {
			exit('Error: template source \'' . $filename . '\' is not exist!');
		}

		if (DEVELOPMENT || !is_file($compile) || (filemtime($compile) < filemtime($source))) {
			shop_template_compile($source, $compile, true);
		}

		return $compile;
	}

	public function template_merch($filename, $isv3)
	{
		global $_W;

		if (empty($filename)) {
			$filename = str_replace('.', '/', $_W['routes']);
		}

		$filename = str_replace('/add', '/post', $filename);
		$filename = str_replace('/edit', '/post', $filename);
		$name = 'ewei_shopv2';
		$moduleroot = IA_ROOT . '/addons/ewei_shopv2';
		$compile = IA_ROOT . '/data/tpl/web/' . $_W['template'] . '/merch/' . $name . '/' . $filename . '.tpl.php';
		$explode = explode('/', $filename);

		if ($isv3) {
			$source = $moduleroot . '/plugin/merch/template/web_v3/manage/' . implode('/', $explode) . '.html';

			if (!is_file($source)) {
				$source = $moduleroot . '/plugin/merch/template/web_v3/manage/' . implode('/', $explode) . '/index.html';
			}
		}

		if (!$isv3 || !is_file($source)) {
			$source = $moduleroot . '/plugin/merch/template/web/manage/' . implode('/', $explode) . '.html';

			if (!is_file($source)) {
				$source = $moduleroot . '/plugin/merch/template/web/manage/' . implode('/', $explode) . '/index.html';
			}
		}

		if (!is_file($source)) {
			$explode = explode('/', $filename);
			$temp = array_slice($explode, 1);

			if ($isv3) {
				$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web_v3/' . implode('/', $temp) . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web_v3/' . implode('/', $temp) . '/index.html';
				}
			}

			if (!$isv3 || !is_file($source)) {
				$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '.html';

				if (!is_file($source)) {
					$source = $moduleroot . '/plugin/' . $explode[0] . '/template/web/' . implode('/', $temp) . '/index.html';
				}
			}
		}

		if (!is_file($source)) {
			exit('Error: template source \'' . $filename . '\' is not exist!');
		}

		if (DEVELOPMENT || !is_file($compile) || (filemtime($compile) < filemtime($source))) {
			shop_template_compile($source, $compile, true);
		}

		return $compile;
	}

	public function message($msg, $redirect = '', $type = '')
	{
		global $_W;
		$title = '';
		$buttontext = '';
		$message = $msg;
		$buttondisplay = true;

		if (is_array($msg)) {
			$message = (isset($msg['message']) ? $msg['message'] : '');
			$title = (isset($msg['title']) ? $msg['title'] : '');
			$buttontext = (isset($msg['buttontext']) ? $msg['buttontext'] : '');
			$buttondisplay = (isset($msg['buttondisplay']) ? $msg['buttondisplay'] : true);
		}

		if (empty($redirect)) {
			$redirect = 'javascript:history.back(-1);';
		}
		else if ($redirect == 'close') {
			$redirect = 'javascript:WeixinJSBridge.call("closeWindow")';
		}
		else {
			if ($redirect == 'exit') {
				$redirect = '';
			}
		}

		include $this->template('_message');
		exit();
	}

	public function checkSubmit($key, $time = 2, $message = '操作频繁，请稍后再试!')
	{
		global $_W;
		$open_redis = function_exists('redis') && !is_error(redis());

		if ($open_redis) {
			$redis_key = $_W['setting']['site']['key'] . '_' . $_W['account']['key'] . '_' . $_W['uniacid'] . '_' . $_W['openid'] . '_mobilesubmit_' . $key;
			$redis = redis();

			if ($redis->setnx($redis_key, time())) {
				$redis->expireAt($redis_key, time() + $time);
			}
			else {
				return error(-1, $message);
			}
		}

		return true;
	}

	public function checkSubmitGlobal($key, $time = 2, $message = '操作频繁，请稍后再试!')
	{
		global $_W;
		$open_redis = function_exists('redis') && !is_error(redis());

		if ($open_redis) {
			$redis_key = $_W['setting']['site']['key'] . '_' . $_W['account']['key'] . '_' . $_W['uniacid'] . '_mobilesubmit_' . $key;
			$redis = redis();

			if ($redis->setnx($redis_key, time())) {
				$redis->expireAt($redis_key, time() + $time);
			}
			else {
				return error(-1, $message);
			}
		}

		return true;
	}
}

?>
