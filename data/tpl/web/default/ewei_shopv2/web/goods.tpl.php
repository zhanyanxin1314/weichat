<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style>
    .newgoodsflag{
        width: 18px;height: 16px;
        background-color: #ff0000;
        color: #fff;
        text-align: center;
        position: relative;
        bottom: 40px;
        left: 22px;
        z-index:99;
        font-size: 12px;
    }
</style>
<div class="page-heading"> 
    <span class='pull-right'>
        <a class='btn btn-primary btn-sm' href="<?php  echo webUrl('goods/add')?>"><i class='fa fa-plus'></i> 添加商品</a>
    </span>
    <h2>商品列表</h2> </div>

<form action="./index.php" method="get" class="form-horizontal form-search" role="form">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r"  value="goods" />
    <input type="hidden" name="goodsfrom" value="<?php  echo $goodsfrom;?>" />
    <div class="page-toolbar row m-b-sm m-t-sm">


        <div class="col-sm-8 pull-right">

            <div class="input-group">				 
                <input type="text" class="input-sm form-control" name='keyword' value="<?php  echo $_GPC['keyword'];?>" placeholder="ID/名称/编号/条码"> <span class="input-group-btn">
                    		
                    <button class="btn btn-sm btn-primary" type="submit"> 搜索</button> </span>
            </div>

        </div>
    </div>
</form>

<?php  if(count($list)>0 && cv('goods.main')) { ?>
<table class="table table-hover table-responsive"> 
    <thead class="navbar-inner">
        <tr>
            <th style="width:25px;"><input type='checkbox' /></th>
            <th style="width:60px;text-align:center;">排序</th>
            <th style="width:60px;">商品</th>
            <th  style="width:200px;">&nbsp;</th>
            <th style="width:90px;" >价格</th>
            <th style="width:70px;" >库存</th>
            <th style="width:80px;" >销量</th>
            <th  style="width:60px;" >状态</th>
            <th style="width:70px;">操作</th>
        </tr>
    </thead>
    <tbody>
        <?php  if(is_array($list)) { foreach($list as $item) { ?>
        <tr>
            <td>
                <input type='checkbox'  value="<?php  echo $item['id'];?>"/>
            </td>
            <td style='text-align:center;'>
                <a href='javascript:;' ><?php  echo $item['displayorder'];?></a>
            </td>
            <td>
                <img src="<?php  echo tomedia($item['thumb'])?>" class="imgg" style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"  />
            </td>
            <td class='full' style="overflow-x: hidden">
                <?php  if(!empty($category[$item['pcate']])) { ?>
                	<span class="text-danger">[<?php  echo $category[$item['pcate']]['name'];?>]</span>
                <?php  } ?>
                <?php  if(!empty($category[$item['ccate']])) { ?>
                	<span class="text-info">[<?php  echo $category[$item['ccate']]['name'];?>]</span>
                <?php  } ?>
                <?php  if(!empty($category[$item['tcate']]) && intval($shopset['catlevel'])==3) { ?>
                	<span class="text-info">[<?php  echo $category[$item['tcate']]['name'];?>]</span>
                <?php  } ?>
                <br/>
                <a href='javascript:;' ><?php  echo $item['title'];?></a>
            </td>

            <td>

                <a href='javascript:;' ><?php  echo $item['marketprice'];?></a>

            </td>

            <td>
                        <a href='javascript:;' ><?php  echo $item['total'];?></a>
            </td>
            <td><?php  echo $item['salesreal'];?></td>

            <?php  if($goodsfrom!='cycle') { ?>
            <td  style="overflow:visible;">
                
                <span class='label <?php  if($item['status']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>' 
                      <?php if(cv('goods.edit')) { ?>
                      data-toggle='ajaxSwitch' 
                      data-confirm = "确认是<?php  if($item['status']==1) { ?>下架<?php  } else { ?>上架<?php  } ?>？"
                      data-switch-refresh='true'
                      data-switch-value='<?php  echo $item['status'];?>'
                      data-switch-value0='0|下架|label label-default|<?php  echo webUrl('goods/status',array('status'=>1,'id'=>$item['id']))?>'  
                      data-switch-value1='1|上架|label label-success|<?php  echo webUrl('goods/status',array('status'=>0,'id'=>$item['id']))?>'  
                      <?php  } ?>
                      >
                      <?php  if($item['status']==1) { ?>上架<?php  } else { ?>下架<?php  } ?></span>
               
                </td>
                <?php  } ?>
                <td  style="overflow:visible;position:relative">
                        <a  class='btn btn-default btn-sm' href="<?php  echo webUrl('goods/edit', array('id' => $item['id'],'goodsfrom'=>$goodsfrom,'page'=>$page))?>" title="<?php if(cv('goods.edit')) { ?>编辑<?php  } else { ?>查看<?php  } ?>"><i class='fa fa-edit'></i> </a>

                        <a  class='btn btn-default btn-sm' data-toggle='ajaxRemove' href="<?php  echo webUrl('goods/delete1', array('id' => $item['id']))?>" data-confirm='如果此商品存在购买记录，会无法关联到商品, 确认要彻底删除吗?？'><i class='fa fa-remove'></i> </a>



                    </td>
                </tr>

                            <?php  } } ?>
                           </tbody>
                          </table>
                          <?php  echo $pager;?>
                          <?php  } else { ?>
                          <div class='panel panel-default'>
                              <div class='panel-body' style='text-align: center;padding:30px;'>
                                  暂时没有任何商品!
                              </div>
                          </div>
                          <?php  } ?>
                          <?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

