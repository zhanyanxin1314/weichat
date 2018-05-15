<?php

global $_W;
global $_GPC;
$id = intval($_GPC['id']);
if (!(empty($id))) 
{
	pdo_update('ewei_shop_goods', array('newgoods' => 0), array('id' => $id));
}
$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id = :id and uniacid = :uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));

$status = $item['status'];
$levels = array_merge(array( array('id' => 0, 'key' => 'default', 'levelname' => (empty($_W['shopset']['shop']['levelname']) ? '默认会员' : $_W['shopset']['shop']['levelname'])) ), $levels);

if ($_W['ispost']) 
{
	$data = array('uniacid' => intval($_W['uniacid']), 
				  'displayorder' => intval($_GPC['displayorder']), 
				  'title' => trim($_GPC['goodsname']), 
				  'type' => 1,
				  'goodssn' => trim($_GPC['goodssn']), 
				  'createtime' => TIMESTAMP, 
				  'total' => intval($_GPC['total']), 
				  'marketprice' => $_GPC['marketprice'], 
				  'costprice' => $_GPC['costprice'], 
				  'productprice' => trim($_GPC['productprice']), 
				  'total' => intval($_GPC['total']),
				  'keywords' => trim($_GPC['keywords']),
				  'status' => ($status != 2 ? intval($_GPC['status']) : $status));


	$data['content'] = m('common')->html_images($_GPC['content']);

	if (is_array($_GPC['thumbs'])) 
	{
		$thumbs = $_GPC['thumbs'];
		$thumb_url = array();
		foreach ($thumbs as $th ) 
		{
			$thumb_url[] = trim($th);
		}
		$data['thumb'] = save_media($thumb_url[0]);
		unset($thumb_url[0]);
		$data['thumb_url'] = serialize(m('common')->array_images($thumb_url));
	}

	if (empty($id)) 
	{

		pdo_insert('ewei_shop_goods', $data);
		$id = pdo_insertid();
		plog('goods.add', '添加商品 ID: ' . $id . '<br>' . ((!(empty($data['nocommission'])) ? '是否参与分销 -- 否' : '是否参与分销 -- 是')));
	}
	else 
	{
		unset($data['createtime']);
		pdo_update('ewei_shop_goods', $data, array('id' => $id));
		plog('goods.edit', '编辑商品 ID: ' . $id . '<br>' . ((!(empty($data['nocommission'])) ? '是否参与分销 -- 否' : '是否参与分销 -- 是')));
	}
	
	header('location:'.  webUrl('goods/index'));
}
if (!(empty($id))) 
{
	if (empty($item)) 
	{
		$this->message('抱歉，商品不存在或是已经删除！', '', 'error');
	}
	$cates = explode(',', $item['cates']);
	$item['content'] = m('common')->html_to_images($item['content']);
	if (!(empty($item['thumb']))) 
	{
		$piclist = array_merge(array($item['thumb']), iunserializer($item['thumb_url']));
	}
}
$areas = m('common')->getAreas();
include $this->template('goods/post');
exit();
?>
