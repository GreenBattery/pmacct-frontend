<?php
/* Smarty version 4.5.5, created on 2025-07-07 18:25:51
  from '/home/nucc1/Projects/pmacct-frontend/src/includes/views/main.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_686c031f27f057_90495955',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8573bedb375d65389d110c828704f9acb226edd6' => 
    array (
      0 => '/home/nucc1/Projects/pmacct-frontend/src/includes/views/main.tpl',
      1 => 1595753477,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_686c031f27f057_90495955 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_393047596686c031f27c363_38554994', 'title');
?>
</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <?php echo '<script'; ?>
 src="js/jquery.js"><?php echo '</script'; ?>
>
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1882991380686c031f27cc86_63544358', 'scriptFiles');
?>

        <?php echo '<script'; ?>
 src="js/bootstrap.js"><?php echo '</script'; ?>
>
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1828008901686c031f27d386_51117932', 'cssFiles');
?>


        <style type="text/css">
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1451823572686c031f27dbe3_60399906', 'css');
?>

        </style>
    </head>

    <body class="h-100 mt-100">
        <div class="sticky-top">
            <nav class="navbar navbar-expand navbar-expand-lg navbar-expand-md bg-dark navbar-dark">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link " href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="stats.php">Stats</a></li>
                    <li class="nav-item"><a class="nav-link" href="firewall.php">Firewall</a></li>
                </ul>
            </nav>
        </div>
        <div class="container mt-100 h-100">
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1279687947686c031f27e284_62249107', 'body');
?>

        </div>



    </body>
    <?php echo '<script'; ?>
 type="text/javascript">
        $(function() {
            //add active class to active link.
            $('a[href="' + this.location.pathname + '"]').parents('li,ul').addClass('active');
        });
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_511078630686c031f27ea21_55654292', 'script');
?>

    <?php echo '</script'; ?>
>


</html><?php }
/* {block 'title'} */
class Block_393047596686c031f27c363_38554994 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_393047596686c031f27c363_38554994',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'title'} */
/* {block 'scriptFiles'} */
class Block_1882991380686c031f27cc86_63544358 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'scriptFiles' => 
  array (
    0 => 'Block_1882991380686c031f27cc86_63544358',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'scriptFiles'} */
/* {block 'cssFiles'} */
class Block_1828008901686c031f27d386_51117932 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'cssFiles' => 
  array (
    0 => 'Block_1828008901686c031f27d386_51117932',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'cssFiles'} */
/* {block 'css'} */
class Block_1451823572686c031f27dbe3_60399906 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'css' => 
  array (
    0 => 'Block_1451823572686c031f27dbe3_60399906',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'css'} */
/* {block 'body'} */
class Block_1279687947686c031f27e284_62249107 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'body' => 
  array (
    0 => 'Block_1279687947686c031f27e284_62249107',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'body'} */
/* {block 'script'} */
class Block_511078630686c031f27ea21_55654292 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'script' => 
  array (
    0 => 'Block_511078630686c031f27ea21_55654292',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'script'} */
}
