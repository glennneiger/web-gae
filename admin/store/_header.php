<?
$page="shops";
global $D_R;
include_once($D_R."/lib/_cart.php");
include("../_header.htm");
?>
<html>
<head>
<link rel="stylesheet" href="../_style.css" />
<script src="/lib/_script.js"></script>

</head>

<body>
<?if(!$hide_ui){?>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<TR>
<TD class="navigation" style="text-align:left">	</TD>

<TD align=right class="navigation">
<a href="../editpage.htm?filename=/assets/data/order_confirmation.html&page=shops&title=Shops%3A%20Order%20Confirmation%20Email">email</a> |
<a href="../editpage.htm?filename=/assets/data/order_thanks.html&page=shops&title=Shops%3A%20Order%20Confirmation%20Thanks%20Page ">thanks page</a> |
<a href="categories.php">edit categories</a> |
<a href="products.php">edit products</a> |
<a href="orders.php">edit orders</a> </TD>
</TR>
</table>

<?}?>