<?php
/* Smarty version 4.5.5, created on 2025-07-07 18:25:51
  from '/home/nucc1/Projects/pmacct-frontend/src/includes/views/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_686c031f265249_12029556',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fda81d09f8070f1fd43f9ec632b47a888d0d50ac' => 
    array (
      0 => '/home/nucc1/Projects/pmacct-frontend/src/includes/views/index.tpl',
      1 => 1595616591,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_686c031f265249_12029556 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_377496084686c031f256856_29780020', "body");
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_477615676686c031f264b56_33090124', "title");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'main.tpl');
}
/* {block "body"} */
class Block_377496084686c031f256856_29780020 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'body' => 
  array (
    0 => 'Block_377496084686c031f256856_29780020',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <h1>The Router UI</h1>


    <h3>Network Interfaces</h3>
    <div class="card-group" aria-label="List of Interfaces">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['devices']->value, 'd');
$_smarty_tpl->tpl_vars['d']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->do_else = false;
?>
        <div class="card">
            <div class="card-header">
                <h3><?php echo $_smarty_tpl->tpl_vars['d']->value['ifname'];?>
</h3>
            </div>
            <div class="card-body">
                <h5>Addresses</h5>
                <ul class="list-group">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value['addr_info'], 'addr');
$_smarty_tpl->tpl_vars['addr']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['addr']->value) {
$_smarty_tpl->tpl_vars['addr']->do_else = false;
?>
                        <li class="list-group-item"><?php echo $_smarty_tpl->tpl_vars['addr']->value['local'];?>
</li>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                </ul>

                <h5>Other Data</h5>
                <ul class="list-group" aria-label="Interface Details">
                    <li class="list-group-item"><strong>Type:</strong> Ethernet</li>
                    <li class="list-group-item"><strong>MAC:</strong> <?php echo $_smarty_tpl->tpl_vars['d']->value['address'];?>
</li>
                    <li class="list-group-item"><strong>Q Discipline:</strong> <?php echo $_smarty_tpl->tpl_vars['d']->value['qdisc'];?>
</li>
                </ul>

            </div>
        </div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>


<?php
}
}
/* {/block "body"} */
/* {block "title"} */
class Block_477615676686c031f264b56_33090124 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_477615676686c031f264b56_33090124',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
Router UI<?php
}
}
/* {/block "title"} */
}
