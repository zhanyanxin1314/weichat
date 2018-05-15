<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$we7_system_menu = array();
$we7_system_menu['account'] = array(
	'title' => '公众号',
	'icon' => 'wi wi-white-collar',
	'url' => url('home/welcome/platform'),
	'section' => array(
		'platform_plus' => array(
			'title' => '增强功能',
			'menu' => array(
				'platform_reply' => array(
					'title' => '自动回复',
					'url' => url('platform/reply'),
					'icon' => 'wi wi-reply',
					'permission_name' => 'platform_reply',
					'sub_permission' => array(
																																																																													),
				),
				'platform_menu' => array(
					'title' => '自定义菜单',
					'url' => url('platform/menu'),
					'icon' => 'wi wi-custommenu',
					'permission_name' => 'platform_menu',
				),
				'platform_qr' => array(
					'title' => '二维码/转化链接',
					'url' => url('platform/qr'),
					'icon' => 'wi wi-qrcode',
					'permission_name' => 'platform_qr',
					'sub_permission' => array(),
				),
				'platform_material' => array(
					'title' => '素材/编辑器',
					'url' => url('platform/material'),
					'icon' => 'wi wi-redact',
					'permission_name' => 'platform_material',
				),
				'platform_site' => array(
					'title' => '微官网-文章',
					'url' => url('site/multi/display'),
					'icon' => 'wi wi-home',
					'permission_name' => 'platform_site',
					'sub_permission' => array(),
				)
			),
		),
		'platform_module' => array(
			'title' => '应用模块',
			'menu' => array(),
			'is_display' => true,
		),
		'mc' => array(
			'title' => '粉丝',
			'menu' => array(
				'mc_fans' => array(
					'title' => '粉丝管理',
					'url' => url('mc/fans'),
					'icon' => 'wi wi-fansmanage',
					'permission_name' => 'mc_fans',
				),
				'mc_member' => array(
					'title' => '会员管理',
					'url' => url('mc/member'),
					'icon' => 'wi wi-fans',
					'permission_name' => 'mc_member',
				)
			),
		),
		'profile' => array(
			'title' => '配置',
			'menu' => array(
				'profile' => array(
					'title' => '参数配置',
					'url' => url('profile/passport'),
					'icon' => 'wi wi-parameter-setting',
					'permission_name' => 'profile_setting',
				),
				'payment' => array(
					'title' => '支付参数',
					'url' => url('profile/payment'),
					'icon' => 'wi wi-pay-setting',
					'permission_name' => 'profile_pay_setting',
				),
			),
		),
		'statistics' => array(
			'title' => '统计',
			'menu' => array(
				'app' => array(
					'title' => '访问统计',
					'url' => url('statistics/app'),
					'icon' => 'wi wi-api',
					'permission_name' => 'statistics_app',
				),
			),
		),
	),
);
$we7_system_menu['wxapp'] = array(
	'title' => '小程序',
	'icon' => 'wi wi-small-routine',
	'url' => url('wxapp/display/home'),
	'section' => array(
		'wxapp_entrance' => array(
			'title' => '小程序入口',
			'menu' => array(
				'module_entrance_link' => array(
					'title' => "入口页面",
					'url' => url('wxapp/entrance-link'),
					'is_display' => 1,
					'icon' => 'wi wi-data-synchro',
					'permission_name' => 'wxapp_entrance_link',
				),
			),
			'is_display' => true,
		),
		'wxapp_module' => array(
			'title' => '应用',
			'menu' => array(),
			'is_display' => true,
		),
		'platform_manage_menu' => array(
			'title' => '管理',
			'menu' => array(
				'module_link' => array(
					'title' => "数据同步",
					'url' => url('wxapp/module-link-uniacid'),
					'is_display' => 1,
					'icon' => 'wi wi-data-synchro',
					'permission_name' => 'wxapp_module_link_uniacid',
				),
				'wxapp_profile' => array(
					'title' => '支付参数',
					'url' => url('wxapp/payment'),
					'is_display' => 1,
					'icon' => 'wi wi-appsetting',
					'permission_name' => 'wxapp_payment',
				),
				'front_download' => array(
					'title' => '上传微信审核',
					'url' => url('wxapp/front-download'),
					'is_display' => 1,
					'icon' => 'wi wi-examine',
					'permission_name' => 'wxapp_front_download',
				)
			)
		)
	),
);

$we7_system_menu['module'] = array(
	'title' => '应用',
	'icon' => 'wi wi-apply',
	'url' => url('module/display'),
	'section' => array(),
	'is_display' => 0
);

$we7_system_menu['system'] = array(
	'title' => '系统管理',
	'icon' => 'wi wi-setting',
	'url' => url('home/welcome/system'),
	'section' => array(
		'wxplatform' => array(
			'title' => '公众号',
			'menu' => array(
				'system_account' => array(
					'title' => ' 微信公众号',
					'url' => url('account/manage', array('account_type' => '1')),
					'icon' => 'wi wi-wechat',
					'permission_name' => 'system_account',
					'sub_permission' => array(
						array(
							'title' => '公众号管理设置',
							'permission_name' => 'system_account_manage',
						),
						array(
							'title' => '添加公众号',
							'permission_name' => 'system_account_post',
						),
						array(
							'title' => '公众号停用',
							'permission_name' => 'system_account_stop',
						),
						array(
							'title' => '公众号回收站',
							'permission_name' => 'system_account_recycle',
						),
						array(
							'title' => '公众号删除',
							'permission_name' => 'system_account_delete',
						),
						array(
							'title' => '公众号恢复',
							'permission_name' => 'system_account_recover',
						),
					),
				),
				'system_module' => array(
					'title' => '公众号应用',
					'url' => url('module/manage-system', array('account_type' => '1')),
					'icon' => 'wi wi-wx-apply',
					'permission_name' => 'system_module',
				),
				'system_template' => array(
					'title' => '微官网模板',
					'url' => url('system/template'),
					'icon' => 'wi wi-wx-template',
					'permission_name' => 'system_template',
				),
				'system_platform' => array(
					'title' => ' 微信开放平台',
					'url' => url('system/platform'),
					'icon' => 'wi wi-exploitsetting',
					'permission_name' => 'system_platform',
				),
			)
		),
		'module' => array(
			'title' => '小程序',
			'menu' => array(
				'system_wxapp' => array(
					'title' => '微信小程序',
					'url' => url('account/manage', array('account_type' => '4')),
					'icon' => 'wi wi-wxapp',
					'permission_name' => 'system_wxapp',
					'sub_permission' => array(
						array(
							'title' => '小程序管理设置',
							'permission_name' => 'system_wxapp_manage',
						),
						array(
							'title' => '添加小程序',
							'permission_name' => 'system_wxapp_post',
						),
						array(
							'title' => '小程序停用',
							'permission_name' => 'system_wxapp_stop',
						),
						array(
							'title' => '小程序回收站',
							'permission_name' => 'system_wxapp_recycle',
						),
						array(
							'title' => '小程序删除',
							'permission_name' => 'system_wxapp_delete',
						),
						array(
							'title' => '小程序恢复',
							'permission_name' => 'system_wxapp_recover',
						),
					),
				),
				'system_module_wxapp' => array(
					'title' => '小程序应用',
					'url' => url('module/manage-system', array('account_type' => '4')),
					'icon' => 'wi wi-wxapp-apply',
					'permission_name' => 'system_module_wxapp',
				),
			)
		),
		'user' => array(
			'title' => '帐户/用户',
			'menu' => array(
				'system_my' => array(
					'title' => '我的帐户',
					'url' => url('user/profile'),
					'icon' => 'wi wi-user',
					'permission_name' => 'system_my',
				),
				'system_user' => array(
					'title' => '用户管理',
					'url' => url('user/display'),
					'icon' => 'wi wi-user-group',
					'permission_name' => 'system_user',
					'sub_permission' => array(
						array(
							'title' => '编辑用户',
							'permission_name' => 'system_user_post',
						),
						array(
							'title' => '审核用户',
							'permission_name' => 'system_user_check',
						),
						array(
							'title' => '店员管理',
							'permission_name' => 'system_user_clerk',
						),
						array(
							'title' => '用户回收站',
							'permission_name' => 'system_user_recycle',
						),
						array(
							'title' => '用户属性设置',
							'permission_name' => 'system_user_fields',
						),
						array(
							'title' => '用户属性设置-编辑字段',
							'permission_name' => 'system_user_fields_post',
						),
						array(
							'title' => '用户注册设置',
							'permission_name' => 'system_user_registerset',
						),
					),
				),
				'system_user_founder_group' => array(
					'title' => '副创始人组',
					'url' => url('founder/display'),
					'icon' =>'wi wi-co-founder',
					'permission_name' =>'system_founder_manage',
					'sub_permission' => array(
						array(
							'title' => '添加创始人组',
							'permission_name' => 'system_founder_group_add',
						),
						array(
							'title' => '编辑创始人组',
							'permission_name' => 'system_founder_group_post',
						),
						array(
							'title' => '删除创始人组',
							'permission_name' => 'system_founder_group_del',
						),
						array(
							'title' => '添加创始人',
							'permission_name' => 'system_founder_user_add',
						),
						array(
							'title' => '编辑创始人',
							'permission_name' => 'system_founder_user_post',
						),
						array(
							'title' => '删除创始人',
							'permission_name' => 'system_founder_user_del',
						),
					),
				),
			)
		),
		'permission' => array(
			'title' => '权限管理',
			'menu' => array(
				'system_module_group' => array(
					'title' => '应用权限组',
					'url' => url('module/group'),
					'icon' => 'wi wi-appjurisdiction',
					'permission_name' => 'system_module_group',
					'sub_permission' => array(
						array(
							'title' => '添加应用权限组',
							'permission_name' => 'system_module_group_add',
						),
						array(
							'title' => '编辑应用权限组',
							'permission_name' => 'system_module_group_post',
						),
						array(
							'title' => '删除应用权限组',
							'permission_name' => 'system_module_group_del',
						),
					),
				),
				'system_user_group' => array(
					'title' => '用户权限组',
					'url' => url('user/group'),
					'icon' => 'wi wi-userjurisdiction',
					'permission_name' => 'system_user_group',
					'sub_permission' => array(
						array(
							'title' => '添加用户组',
							'permission_name' => 'system_user_group_add',
						),
						array(
							'title' => '编辑用户组',
							'permission_name' => 'system_user_group_post',
						),
						array(
							'title' => '删除用户组',
							'permission_name' => 'system_user_group_del',
						),
					),
				),
			)
		),
		'article' => array(
			'title' => '文章/公告',
			'menu' => array(
				'system_article' => array(
					'title' => '文章管理',
					'url' => url('article/news'),
					'icon' => 'wi wi-article',
					'permission_name' => 'system_article_news',
				),
				'system_article_notice' => array(
					'title' => '公告管理',
					'url' => url('article/notice'),
					'icon' => 'wi wi-notice',
					'permission_name' => 'system_article_notice',
				)
			)
		),
		'cache' => array(
			'title' => '缓存',
			'menu' => array(
				'system_setting_updatecache' => array(
					'title' => '更新缓存',
					'url' => url('system/updatecache'),
					'icon' => 'wi wi-update',
					'permission_name' => 'system_setting_updatecache',
				),
			),
		),
	),
);

$we7_system_menu['site'] = array(
	'title' => '站点管理',
	'icon' => 'wi wi-system-site',
	'url' => url('system/site'),
	'section' => array(
		'setting' => array(
			'title' => '设置',
			'menu' => array(
				'system_setting_site' => array(
					'title' => '站点设置',
					'url' => url('system/site'),
					'icon' => 'wi wi-site-setting',
					'permission_name' => 'system_setting_site',
				),
				'system_setting_menu' => array(
					'title' => '菜单设置',
					'url' => url('system/menu'),
					'icon' => 'wi wi-menu-setting',
					'permission_name' => 'system_setting_menu',
				),
				'system_setting_attachment' => array(
					'title' => '附件设置',
					'url' => url('system/attachment'),
					'icon' => 'wi wi-attachment',
					'permission_name' => 'system_setting_attachment',
				),
				'system_setting_systeminfo' => array(
					'title' => '系统信息',
					'url' => url('system/systeminfo'),
					'icon' => 'wi wi-system-info',
					'permission_name' => 'system_setting_systeminfo',
				),
				'system_setting_logs' => array(
					'title' => '查看日志',
					'url' => url('system/logs'),
					'icon' => 'wi wi-log',
					'permission_name' => 'system_setting_logs',
				),
				'system_setting_ipwhitelist' => array(
					'title' => 'IP白名单',
					'url' => url('system/ipwhitelist'),
					'icon' => 'wi wi-ip',
					'permission_name' => 'system_setting_ipwhitelist',
				),
			)
		),
		'utility' => array(
			'title' => '常用工具',
			'menu' => array(
				'system_utility_filecheck' => array(
					'title' => '系统文件校验',
					'url' => url('system/filecheck'),
					'icon' => 'wi wi-file',
					'permission_name' => 'system_utility_filecheck',
				),
				'system_utility_optimize' => array(
					'title' => '性能优化',
					'url' => url('system/optimize'),
					'icon' => 'wi wi-optimize',
					'permission_name' => 'system_utility_optimize',
				),
				'system_utility_database' => array(
					'title' => '数据库',
					'url' => url('system/database'),
					'icon' => 'wi wi-sql',
					'permission_name' => 'system_utility_database',
				),
				'system_utility_scan' => array(
					'title' => '木马查杀',
					'url' => url('system/scan'),
					'icon' => 'wi wi-safety',
					'permission_name' => 'system_utility_scan',
				),
				'system_utility_bom' => array(
					'title' => '检测文件BOM',
					'url' => url('system/bom'),
					'icon' => 'wi wi-bom',
					'permission_name' => 'system_utility_bom',
				),
			)
		),
		'workorder'=> array(
			'title' => '工单系统',
			'menu'=> array(
				'system_workorder'=> array(
					'title' => '工单系统',
					'url' => url('system/workorder/display'),
					'icon' => 'wi wi-system-work',
					'permission_name' => 'system_workorder',
				)
			)
		)
	),
	'founder' => true,
);

$we7_system_menu['store'] = array(
	'title' => '商城',
	'icon' => 'wi wi-store',
	'url' => url('home/welcome/ext', array('m' => 'store')),
	'section' => array(
		'store_goods' => array(
			'title' => '商品分类',
			'menu' => array(
				'store_goods_module' =>array(
					'title' => '应用模块',
					'url' => url('store/goods-buyer'),
					'icon' => 'wi wi-goods',
					'permission_name' => 'store_goods_buyer'
				),
			),
		),
		'store_manage' => array(
			'title' => '商城管理',
			'founder' => true,
			'menu' => array(
				'store_manage_goods' => array(
					'title' => '添加商品',
					'url' => url('store/goods-seller'),
					'icon' => 'wi wi-goods-add',
					'permission_name' => 'store_manage_goods',
				),
				'store_manage_setting' => array(
					'title' => '商城设置',
					'url' => url('store/setting'),
					'icon' => 'wi wi-store',
					'permission_name' => 'store_manage_setting',
				),
				'store_manage_payset' => array(
					'title' => '支付设置',
					'url' => url('store/payset'),
					'icon' => 'wi wi-account',
					'permission_name' => 'store_manage_payset',
				),
			)
		),
		'store_orders' => array(
			'title' => '订单管理',
			'menu' => array(
				'store_orders_my' => array(
					'title' => '我的订单',
					'url' => url('store/orders'),
					'icon' => 'wi wi-sale-record',
					'permission_name' => 'store_orders_my',
				),
			),
		),
	),
);

$we7_system_menu['help'] = array(
	'title' => '帮助系统',
	'icon' => 'wi wi-market',
	'url' => url('help/display'),
	'section' => array(),
	'blank' => false
);

return $we7_system_menu;