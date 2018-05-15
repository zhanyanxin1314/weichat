<?php
if(!defined('IN_IA')) {
     exit('Access Denied');
}
if (is_file(__DIR__ . '/defines-local.php'))
{
    require_once __DIR__ . '/defines-local.php';
}
!defined('EWEI_SHOPV2_DEBUG') && define('EWEI_SHOPV2_DEBUG',false);
!defined('EWEI_SHOPV2_PATH') && define('EWEI_SHOPV2_PATH',IA_ROOT.'/addons/ewei_shopv2/');
!defined('EWEI_SHOPV2_CORE') && define('EWEI_SHOPV2_CORE', EWEI_SHOPV2_PATH .'core/');
!defined('EWEI_SHOPV2_DATA') && define('EWEI_SHOPV2_DATA', EWEI_SHOPV2_PATH .'data/');
!defined('EWEI_SHOPV2_CORE_WEB') && define('EWEI_SHOPV2_CORE_WEB', EWEI_SHOPV2_CORE .'web/');
!defined('EWEI_SHOPV2_CORE_MOBILE') && define('EWEI_SHOPV2_CORE_MOBILE', EWEI_SHOPV2_CORE .'mobile/');
!defined('EWEI_SHOPV2_PLUGIN') && define('EWEI_SHOPV2_PLUGIN',EWEI_SHOPV2_PATH.'plugin/');
!defined('EWEI_SHOPV2_INC') && define('EWEI_SHOPV2_INC', EWEI_SHOPV2_CORE.'inc/');
!defined('EWEI_SHOPV2_URL') && define('EWEI_SHOPV2_URL',$_W['siteroot'].'addons/ewei_shopv2/');
!defined('EWEI_SHOPV2_LOCAL') && define('EWEI_SHOPV2_LOCAL','../addons/ewei_shopv2/');
!defined('EWEI_SHOPV2_STATIC') && define('EWEI_SHOPV2_STATIC', EWEI_SHOPV2_URL.'static/');
!defined('EWEI_SHOPV2_PREFIX') && define('EWEI_SHOPV2_PREFIX','ewei_shop_');
  
 
