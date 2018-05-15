<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Shop_EweiShopV2Model 
{
	public function checkClose() 
	{
		if (strexists($_SERVER['REQUEST_URI'], '/web/')) 
		{
			return;
		}
		global $_S;
		global $_W;
		if ($_W['plugin'] == 'mmanage') 
		{
			return;
		}
		$close = $_S['close'];
		if (!(empty($close['flag']))) 
		{
			if (!(empty($close['url']))) 
			{
				header('location: ' . $close['url']);
				exit();
			}
			exit('<!DOCTYPE html>' . "\r\n\t\t\t\t\t" . '<html>' . "\r\n\t\t\t\t\t\t" . '<head>' . "\r\n\t\t\t\t\t\t\t" . '<meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'>' . "\r\n\t\t\t\t\t\t\t" . '<title>抱歉，商城暂时关闭</title><meta charset=\'utf-8\'><meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'><link rel=\'stylesheet\' type=\'text/css\' href=\'https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\'>' . "\r\n\t\t\t\t\t\t" . '</head>' . "\r\n\t\t\t\t\t\t" . '<body>' . "\r\n\t\t\t\t\t\t" . '<style type=\'text/css\'>' . "\r\n\t\t\t\t\t\t" . 'body { background:#fbfbf2; color:#333;}' . "\r\n\t\t\t\t\t\t" . 'img { display:block; width:100%;}' . "\r\n\t\t\t\t\t\t" . '.header {' . "\r\n\t\t\t\t\t\t" . 'width:100%; padding:10px 0;text-align:center;font-weight:bold;}' . "\r\n\t\t\t\t\t\t" . '</style>' . "\r\n\t\t\t\t\t\t" . '<div class=\'page_msg\'>' . "\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t" . '<div class=\'inner\'><span class=\'msg_icon_wrp\'><i class=\'icon80_smile\'></i></span>' . $close['detail'] . '</div></div>' . "\r\n\t\t\t\t\t\t" . '</body>' . "\r\n\t\t\t\t\t" . '</html>');
		}
	}
}
?>
