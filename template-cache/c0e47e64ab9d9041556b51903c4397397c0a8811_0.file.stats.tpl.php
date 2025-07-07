<?php
/* Smarty version 4.5.5, created on 2025-07-07 17:19:51
  from '/home/nucc1/Projects/pmacct-frontend/src/includes/views/stats.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_686c01b79ed3d0_27748631',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c0e47e64ab9d9041556b51903c4397397c0a8811' => 
    array (
      0 => '/home/nucc1/Projects/pmacct-frontend/src/includes/views/stats.tpl',
      1 => 1595753867,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_686c01b79ed3d0_27748631 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_432288395686c01b79eaae0_87762715', "body");
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_309868152686c01b79ebac3_91280932', "title");
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1279540279686c01b79ec420_21845400', "cssFiles");
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_711113135686c01b79eccd2_85369199', "scriptFiles");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'main.tpl');
}
/* {block "body"} */
class Block_432288395686c01b79eaae0_87762715 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'body' => 
  array (
    0 => 'Block_432288395686c01b79eaae0_87762715',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <h1>The Stats</h1>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" id="dayTab" data-toggle="tab" href="#day">Day</a></li>
        <li class="nav-item"><a class="nav-link" id="monthTab" data-toggle="tab" href="#month">Month</a></li>
    </ul>

    <div class="tab-content h-100">
        <div class="tab-pane active h-100" id="day">
            <p>Day stats here.</p>

        </div>
        <div class="tab-pane h-100" id="month">
            <h3>The monthly stats are here for your viewing pleasure</h3>
        </div>
    </div>
<?php
}
}
/* {/block "body"} */
/* {block "title"} */
class Block_309868152686c01b79ebac3_91280932 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_309868152686c01b79ebac3_91280932',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
Router Bandwidth Statistics<?php
}
}
/* {/block "title"} */
/* {block "cssFiles"} */
class Block_1279540279686c01b79ec420_21845400 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'cssFiles' => 
  array (
    0 => 'Block_1279540279686c01b79ec420_21845400',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <link rel="stylesheet" href="css/datatables.min.css" />

<?php
}
}
/* {/block "cssFiles"} */
/* {block "scriptFiles"} */
class Block_711113135686c01b79eccd2_85369199 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'scriptFiles' => 
  array (
    0 => 'Block_711113135686c01b79eccd2_85369199',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <?php echo '<script'; ?>
 src="lib/DataTables/datatables.js" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/stats.js" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/popper.min.js" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/tooltip.min.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php
}
}
/* {/block "scriptFiles"} */
}
