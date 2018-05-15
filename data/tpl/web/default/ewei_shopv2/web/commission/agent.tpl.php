<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>
 
<div class="page-heading"> <h2>分销商管理 <small>总数: <span class='text-danger'><?php  echo $total;?></span></small></h2> </div>
   <form action="./index.php" method="get" class="form-horizontal" role="form" id="form1">
            <input type="hidden" name="c" value="site" />
            <input type="hidden" name="a" value="entry" />
            <input type="hidden" name="m" value="ewei_shopv2" />
            <input type="hidden" name="do" value="web" />
            <input type="hidden" name="r" value="commission.agent" />
            
<div class="page-toolbar row m-b-sm m-t-sm">
			 
                            <div class="col-sm-7 pull-right">
			 	
                                <div class="input-group">
                                          <input type="text" class="form-control input-sm"  name="keyword" value="<?php  echo $_GPC['keyword'];?>" placeholder="昵称/姓名/手机号"/>
				 <span class="input-group-btn">
                                
                                        <button class="btn btn-sm btn-primary" type="submit"> 搜索</button>
				</span>
                                </div>
								
                            </div>
</div>
            
				<div class="page-toolbar row" <?php  if($_GPC['followed']=='' && $_GPC['level']=='' && $_GPC['groupid']=='' && $_GPC['isblack']=='' && $_GPC['time']['start']==''  && $_GPC['time']['end']=='' ) { ?>style='display:none;'<?php  } ?> id='moresearch' >
					
					
					<div class="col-sm-12">
					 
			 	  <select name='followed' class='form-control  input-sm select-md' style="width:140px">
								<option value=''>关注</option>
								<option value='0' <?php  if($_GPC['followed']=='0') { ?>selected<?php  } ?>>未关注</option>
								<option value='1' <?php  if($_GPC['followed']=='1') { ?>selected<?php  } ?>>已关注</option>
								<option value='2' <?php  if($_GPC['followed']=='2') { ?>selected<?php  } ?>>取消关注</option>
							</select>	 
					 
	 
                      <select name='agentlevel' class='form-control  input-sm select-md' style="width:140px;"  >
                            <option value=''>等级</option>
                            <?php  if(is_array($agentlevels)) { foreach($agentlevels as $level) { ?>
                            <option value='<?php  echo $level['id'];?>' <?php  if($_GPC['agentlevel']==$level['id']) { ?>selected<?php  } ?>><?php  echo $level['levelname'];?></option>
                            <?php  } } ?>
                        </select>
		 
                     				
						 
		 
				 
                        <?php  echo tpl_daterange('time', array('sm'=>true, 'placeholder'=>'成为分销商时间'),true);?>
						 
 
  	
                 		
                            </div>
					
					
				</div>
  </form>
<?php  if(count($list)>0) { ?>
 
        <table class="table table-hover table-responsive">
            <thead class="navbar-inner" >
            <tr>
                  <th style="width:25px;"><input type='checkbox' /></th>
          
                <th style='width:100px;'>粉丝</th>
                <th style='width:110px;'>姓名<br/>手机号码</th>
                <th style='width:80px;'>等级</th>
                <th style='width:80px;'>累计佣金<br/>打款佣金</th>
                <th style='width:80px;'>下级分销商</th>
                
                <th style='width:90px;'>注册时间</th>
                <th style='width:90px;'>审核时间</th>
                <th style='width:70px;'>状态</th>
                <th style='width:70px;'>关注</th>
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
				
                       [<?php  echo $row['agentid'];?>]<?php  if(empty($row['parentname'])) { ?>未更新<?php  } else { ?><?php  echo $row['parentname'];?><?php  } ?>
					   <?php  } ?>">
               
								<td>
									<input type='checkbox'   value="<?php  echo $row['id'];?>"/>
							</td>
               
                <td >
                    <span data-toggle='tooltip' title='<?php  echo $row['nickname'];?>'>
                    <?php  if(empty($row['nickname'])) { ?>未更新<?php  } else { ?><?php  echo $row['nickname'];?><?php  } ?>
                    </span>
                </td>

                <td><?php  echo $row['realname'];?> <br/> <?php  echo $row['mobile'];?></td>
                <td><?php  if(empty($row['levelname'])) { ?> <?php echo empty($this->set['levelname'])?'普通等级':$this->set['levelname']?><?php  } else { ?><?php  echo $row['levelname'];?><?php  } ?></td>
             
                <td><?php  echo $row['commission_total'];?><br/><?php  echo $row['commission_pay'];?></td>
                <td >
                    <?php  echo $row['levelcount'];?> 
                    <?php  if($row['levelcount']>0) { ?>
                    <a data-toggle='popover' data-placement='bottom' data-html="true" data-content='<?php  if($level>=1 && $row['level1']>0) { ?>一级：<?php  echo $row['level1'];?> 人<?php  } ?><?php  if($level>=2  && $row['level2']>0) { ?><br/> 二级：<?php  echo $row['level2'];?> 人<?php  } ?><?php  if($level>=3  && $row['level3']>0) { ?><br/>三级：<?php  echo $row['level3'];?> 人<?php  } ?>'>
                        <i class='fa fa-question-circle'></i>
                    </a>
                    <?php  } ?>
                        
                    </td>
                       <td><?php  echo date('Y-m-d',$row['createtime'])?><br/><?php  echo date('H:i',$row['createtime'])?></td>
                <td><?php  if(!empty($row['agenttime'])) { ?>
                    <?php  echo date('Y-m-d',$row['agenttime'])?><br/><?php  echo date('H:i',$row['agenttime'])?>
                <?php  } else { ?>
                -
                <?php  } ?>
                </td>
                <td>
             
                   
                    <span class='label <?php  if($row['status']==1) { ?>label-success<?php  } else { ?>label-default<?php  } ?>' 
										  <?php if(cv('commission.agent.check')) { ?>
										  data-toggle='ajaxSwitch' 
                                                                                                                                                                                                          data-confirm ='确认要<?php  if($row['status']==1) { ?>取消审核<?php  } else { ?>审核通过<?php  } ?>?'
										  data-switch-value='<?php  echo $row['status'];?>'
										  data-switch-value0='0|未审核|label label-default|<?php  echo webUrl('commission/agent/check',array('status'=>1,'id'=>$row['id']))?>'  
										  data-switch-value1='1|已审核|label label-success|<?php  echo webUrl('commission/agent/check',array('status'=>0,'id'=>$row['id']))?>'  
										  <?php  } ?>
										>
										  <?php  if($row['status']==1) { ?>已审核<?php  } else { ?>未审核<?php  } ?></span>
                    <br/>
                    
                    
                     </td>
                <td>


                    <?php  if(empty($row['followed'])) { ?>
                    <?php  if(empty($row['unfollowtime'])) { ?>
                    <label class='label label-default'>未关注</label>
                    <?php  } else { ?>
                    <label class='label label-warning'>取消关注</label>
                    <?php  } ?>
                    <?php  } else { ?>
                    <label class='label label-success'>已关注</label>
                    <?php  } ?>


              
                   
           
                </td>
             
        
            </tr>
            <?php  } } ?>
            </tbody>
        </table>
        <?php  echo $pager;?>
        
                <?php  } else { ?>
<div class='panel panel-default'>
	<div class='panel-body' style='text-align: center;padding:30px;'>
		 暂时没有任何分销商!
	</div>
</div>
<?php  } ?>
    <script language="javascript">
			  

 
			require(['bootstrap'],function(){
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
 
			   
</script> 
 
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
