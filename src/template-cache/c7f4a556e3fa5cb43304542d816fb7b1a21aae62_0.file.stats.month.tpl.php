<?php
/* Smarty version 4.5.5, created on 2025-07-07 18:30:18
  from '/home/nucc1/Projects/pmacct-frontend/src/includes/views/stats.month.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_686c123a7800d3_82072361',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c7f4a556e3fa5cb43304542d816fb7b1a21aae62' => 
    array (
      0 => '/home/nucc1/Projects/pmacct-frontend/src/includes/views/stats.month.tpl',
      1 => 1751794766,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_686c123a7800d3_82072361 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="row">

    <div class="btn-group btn-group-sm w-100">
        <button data-month="<?php echo $_smarty_tpl->tpl_vars['links']->value['lm'];?>
" class="btn btn-outline-primary monthNav" role='button'
                data-year="<?php echo $_smarty_tpl->tpl_vars['links']->value['py'];?>
">Prev Month</button>
        <button data-month="<?php echo $_smarty_tpl->tpl_vars['links']->value['nm'];?>
" class="btn btn-outline-primary monthNav" role="button"
                data-year="<?php echo $_smarty_tpl->tpl_vars['links']->value['ny'];?>
">Next Month</button>
    </div>

<h2><?php echo $_smarty_tpl->tpl_vars['date']->value;?>
</h2>
</div>

<div class="row">
    <div class="col">
        <div class="card-group card-header-tabs">
            <div class="card">
                <div class="card-header">Downld</div>
                <div class="card-body"><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['bytes_in_formatted'];?>
</div>
            </div>
            <div class="card">
                <div class="card-header">Upload</div>
                <div class="card-body"><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['bytes_out_formatted'];?>
</div>
            </div>
            <div class="card">
                <div class="card-header">Aggreg</div>
                <div class="card-body"><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['aggregate_formatted'];?>
</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div id="monthcontainer" class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <table id="monthSummary" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Hostname</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['stats'], 'stat', false, 'ip');
$_smarty_tpl->tpl_vars['stat']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ip']->value => $_smarty_tpl->tpl_vars['stat']->value) {
$_smarty_tpl->tpl_vars['stat']->do_else = false;
?>
                        <tr>
                            <td class="w-75"><span class="badge badge-info"><?php echo $_smarty_tpl->tpl_vars['stat']->value['hostname'];?>
</span> <span><?php echo $_smarty_tpl->tpl_vars['stat']->value['ip'];?>
</span></td>
                            <td><?php echo $_smarty_tpl->tpl_vars['stat']->value['bytes_in'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['stat']->value['bytes_out'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['stat']->value['total'];?>
</td>
                        </tr>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="1">Totals</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['bytes_in'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['bytes_out'];?>
</td>
                        <td><?php echo $_smarty_tpl->tpl_vars['data']->value['totals']['total'];?>
</td>
                    </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
<?php echo '<script'; ?>
 id="monthScript">
    $(function() {
        var script=document.createElement('script');
        script.src="/js/stats.month.js";

        $("#monthScript").before(script);
    })

<?php echo '</script'; ?>
>
<?php }
}
