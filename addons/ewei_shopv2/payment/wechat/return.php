<?php
require '../../../../framework/bootstrap.inc.php';
require '../../../../addons/ewei_shopv2/defines.php';
require '../../../../addons/ewei_shopv2/core/inc/functions.php';
$ordersn = $_GET['outtradeno'];
$attachs = explode(':', $_GET['attach']);
if (empty($attachs) || !(is_array($attachs))) 
{
	exit();
}
$uniacid = $attachs[0];
$paytype = $attachs[1];
$url = $_W['siteroot'] . '../../app/index.php?i=' . $uniacid . '&c=entry&m=ewei_shopv2&do=mobile';
if (!(empty($ordersn))) 
{
	if ($paytype == 0) 
	{
		$url = $_W['siteroot'] . '../../app/index.php?i=' . $uniacid . '&c=entry&m=ewei_shopv2&do=mobile&r=order.pay.complete&ordersn=' . $ordersn . '&type=wechat';
	}
}
header('location: ' . $url);
exit();
?>
