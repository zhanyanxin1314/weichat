<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
<style type='text/css'>
    .trhead td {  background:#efefef;text-align: center}
    .trbody td {  text-align: center; vertical-align:top;border-left:1px solid #f2f2f2;overflow: hidden; font-size:12px;}
    .trorder { background:#f8f8f8;border:1px solid #f2f2f2;text-align:left;}
    .ops { border-right:1px solid #f2f2f2; text-align: center;}
</style>

<div class="page-heading">

    <h2>订单管理</h2>


</div>

<form action="./index.php" method="get" class="form-horizontal table-search" role="form">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="ewei_shopv2" />
    <input type="hidden" name="do" value="web" />
    <input type="hidden" name="r" value="order.list<?php  echo $st;?>" />
    <input type="hidden" name="status" value="<?php  echo $status;?>" />
    <input type="hidden" name="agentid" value="<?php  echo $_GPC['agentid'];?>" />
    <input type="hidden" name="refund" value="<?php  echo $_GPC['refund'];?>" />
    <div class="page-toolbar row m-b-sm m-t-sm">

        <div class="col-sm-5 pull-right">

            <div class="input-group">
                <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="请输入关键词"/>
                <span class="input-group-btn">

                    <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>


                </span>
            </div>

        </div>
    </div>

</form>


<?php  if(count($list)>0) { ?>
<table class='table table-responsive' style='table-layout: fixed;'>
    <tr style='background:#f8f8f8'>
        <td style='width:60px;border-left:1px solid #f2f2f2;'>商品</td>
        <td style='width:150px;'></td>
        <td style='width:70px;text-align: right;;'>单价/数量</td>
        <td  style='width:100px;text-align: center;'>买家</td>
        <td style='width:90px;text-align: center;'>支付/配送</td>
        <td style='width:100px;text-align: center;'>价格</td>
        <td style='width:100px;text-align: center;'>下单时间</td>
        <td style='width:90px;text-align: center'>状态</td>

    </tr>
    <?php  if(is_array($list)) { foreach($list as $item) { ?>
    <tr ><td colspan='8' style='height:20px;padding:0;border-top:none;'>&nbsp;</td></tr>
    <tr class='trorder'>
        <td colspan='4' >
            订单编号:  <?php  echo $item['ordersn'];?>
        </td>
        <td colspan='4' style='text-align:right;font-size:12px;' class='aops'>

            <a class='op'  href="<?php  echo webUrl('order/detail', array('id' => $item['id']))?>" >查看详情</a>

        </td>
    </tr>
    <?php  if(is_array($item['goods'])) { foreach($item['goods'] as $k => $g) { ?>
    <tr class='trbody'>
        <td style='overflow:hidden;'><img src="<?php  echo tomedia($g['thumb'])?>" style='width:50px;height:50px;border:1px solid #ccc; padding:1px;'></td>
        <td style='text-align: left;overflow:hidden;border-left:none;'  >
            <?php  echo $g['title'];?>
           </td>
        <td style='text-align:right;border-left:none;'><?php  echo number_format($g['realprice']/$g['total'],2)?><br/>x<?php  echo $g['total'];?></td>

        <?php  if($k==0) { ?>
        <td rowspan="<?php  echo count($item['goods'])?>"  style='text-align: center;' >
            <?php  echo $item['nickname'];?>

            <br/>
            <?php  echo $item['addressdata']['realname'];?><br/><?php  echo $item['addressdata']['mobile'];?></td>
        <td rowspan="<?php  echo count($item['goods'])?>" style='text-align:center;' >

            <?php  if($item['statusvalue'] > 0) { ?>
            <label class='label label-<?php  echo $item['css'];?>'><?php  echo $item['paytype'];?></label>
            <?php  } else if($item['statusvalue'] == 0) { ?>
            <?php  if($item['paytypevalue']!=3) { ?>
            <label class='label label-default'>未支付</label>
            <?php  } else { ?>
            <label class='label label-primary'>货到付款</label>
            <?php  } ?>
            <?php  } else if($item['statusvalue'] == -1) { ?>
            <label class='label label-default'><?php  echo $item['paytype'];?></label>
            <?php  } ?>
            <br/>


            <span style='margin-top:5px;display:block;'><?php  echo $item['dispatchname'];?></span>

        </td>
        <td  rowspan="<?php  echo count($item['goods'])?>" style='text-align:center' >
            ￥<?php  echo number_format($item['price'],2)?> <a data-toggle='popover' data-html='true' data-placement='top'
                                                           data-content="<table style='width:100%;'>
                <tr>
                    <td  style='border:none;text-align:right;'>商品小计：</td>
                    <td  style='border:none;text-align:right;;'>￥<?php  echo number_format( $item['goodsprice'] ,2)?></td>
                </tr>
                <tr>
                    <td  style='border:none;text-align:right;'>运费：</td>
                    <td  style='border:none;text-align:right;;'>￥<?php  echo number_format( $item['olddispatchprice'],2)?></td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>应收款：</td>
                    <td  style=`'border:none;text-align:right;color:green;'>￥<?php  echo number_format($item['price'],2)?></td>
                </tr>
               
            </table>
"
                ><i class='fa fa-question-circle'></i></a>
            <?php  if($item['dispatchprice']>0) { ?>
            <br/>(含运费:￥<?php  echo number_format( $item['dispatchprice'],2)?>)
            <?php  } ?>


        </td>
        <td  rowspan="<?php  echo count($item['goods'])?>" style='text-align:center' >
            <?php  echo date('Y-m-d',$item['createtime'])?><br/><?php  echo date('H:i:s',$item['createtime'])?>

        </td>

        <td   rowspan="<?php  echo count($item['goods'])?>" class='ops' style='line-height:20px;text-align:center' ><span class='text-<?php  echo $item['statuscss'];?>'><?php  echo $item['status'];?></span><br /><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('order/ops', TEMPLATE_INCLUDEPATH)) : (include template('order/ops', TEMPLATE_INCLUDEPATH));?>
        </td>

        <?php  } ?>
    </tr>
    <?php  } } ?>
    <?php  } } ?>
</table>
<div style="text-align:right;width:100%;">
    <?php  echo $pager;?>
</div>
<?php  } else { ?>

<div class='panel panel-default'>
    <div class='panel-body' style='text-align: center;padding:30px;'>
        暂时没有任何订单!
    </div>
</div>
<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>

