<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>


<div class="page-heading"> <h2>会员管理</h2> </div>

  <form action="./index.php" method="get" class="form-horizontal table-search" role="form">
                <input type="hidden" name="c" value="site" />
                <input type="hidden" name="a" value="entry" />
                <input type="hidden" name="m" value="ewei_shopv2" />
                <input type="hidden" name="do" value="web" />
                <input type="hidden" name="r" value="member.list" />
<div class="page-toolbar row m-b-sm m-t-sm">


                            <div class="col-sm-6 pull-right">

				<div class="input-group">
                                          <input type="text" class="form-control input-sm"  name="realname" value="<?php  echo $_GPC['realname'];?>" placeholder="可搜索昵称/姓名/手机号/ID"/>
				 <span class="input-group-btn">

                                        <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>
				</span>
                                </div>

                            </div>
</div>
  </form>

 
        <table class="table table-hover table-responsive">
            <thead class="navbar-inner">
                <tr>
                    <th style="width:25px;"><input type='checkbox' /></th>
                    <th style="width:150px;">粉丝</th>
                    <th style="width:150px;">会员信息</th>
                    <th style="width:280px;">注册时间</th>
                    <th style="width:100px;">成交</th>
                </tr>
            </thead>
            <tbody>
                <?php  if(is_array($list)) { foreach($list as $row) { ?>
                <tr rel="pop" data-title="ID: <?php  echo $row['id'];?> " data-content="推荐人 <br/> <?php  if(empty($row['agentid'])) { ?>
				  <?php  if($row['isagent']==1) { ?>
				      <label class='label label-primary'>总店</label>
				      <?php  } else { ?>
				       <label class='label label-default'>暂无</label>
				      <?php  } ?>
				<?php  } else { ?>
				
                       [<?php  echo $row['agentid'];?>]<?php  if(empty($row['agentnickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['agentnickname'];?><?php  } ?>
					   <?php  } ?>">
					
			
                   	<td style="position: relative; ">
					<input type='checkbox'   value="<?php  echo $row['id'];?>"/></td>
                    <td  >
			<div  >


                       <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
                        </div>
                    </td>
                    <td><?php  echo $row['realname'];?><br/><span><?php  echo $row['mobile'];?></span></td>
                    
      
                    <td><?php  echo date("Y-m-d",$row['createtime'])?><?php  echo date("H:i:s",$row['createtime'])?></td>
                    
                    <td><label class="label label-primary">订单: <?php  echo $row['ordercount'];?></label>
			<br /><label class="label label-danger">金额: <?php  echo floatval($row['ordermoney'])?></label></td>
                    <td> 
		 
					
                      
                            <td  style="overflow:visible;">
                        
                        <div class="btn-group btn-group-sm" >
                                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                               
                        <?php if(cv('member.list.detail')) { ?>
                        	<li><a href="<?php  echo webUrl('member/list/detail',array('id' => $row['id']));?>" title="会员详情"><i class='fa fa-edit'></i> 会员详情</a></li>
                        <?php  } ?>
                        <?php if(cv('member.list.delete')) { ?><li><a  data-toggle='ajaxRemove'  href="<?php  echo webUrl('member/list/delete',array('id' => $row['id']));?>" title='删除会员' data-confirm="确定要删除该会员吗？"><i class='fa fa-remove'></i> 删除会员</a></li><?php  } ?>
                                </ul>
                            </div>

               
                    </td>
                </tr>
                <?php  } } ?>
            </tbody>
        </table>
           <?php  echo $pager;?>
	 <script language="javascript">
	 <?php  if($opencommission) { ?>
	 require(['bootstrap', 'jquery', 'jquery.ui'], function (bs, $, $) {
        $("[rel=pop]").popover({
            trigger:'manual',
            placement : 'left', 
            title : $(this).data('title'),
            html: 'true', 
            content : $(this).data('content'),
            animation: false
        }).on("mouseenter", function () {
                    var _this = this;
                    $(this).popover("show"); 
                    $(this).siblings(".popover").on("mouseleave", function () {
                        $(_this).popover('hide');
                    });
                }).on("mouseleave", function () {
                    var _this = this;
                    setTimeout(function () {
                        if (!$(".popover:hover").length) {
                            $(_this).popover("hide")
                        }
                    }, 100);
                });
 
	 
	   });
	<?php  } ?>
			   
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
