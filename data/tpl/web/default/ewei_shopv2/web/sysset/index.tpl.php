<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_header', TEMPLATE_INCLUDEPATH)) : (include template('_header', TEMPLATE_INCLUDEPATH));?>

<div class="page-heading"> <h2>商城设置</h2> </div>

    <form action="" method="post" class="form-horizontal form-validate" enctype="multipart/form-data" >
        <div class="form-group">
            <label class="col-sm-2 control-label">商城名称</label>
            <div class="col-sm-9 col-xs-12">
                    <input type="text" name="data[name]" class="form-control" value="<?php  echo $data['name'];?>" />

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">商城简介</label>
            <div class="col-sm-9 col-xs-12">
                <textarea name="data[description]" class="form-control richtext" cols="70"><?php  echo $data['description'];?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">商城海报</label>
            <div class="col-sm-9 col-xs-12">
                <?php  echo tpl_form_field_image('data[signimg]', $data['signimg'])?>

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                    <input type="submit" value="提交" class="btn btn-primary"  />
             </div>
        </div>
    </form>
 
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('_footer', TEMPLATE_INCLUDEPATH)) : (include template('_footer', TEMPLATE_INCLUDEPATH));?>
