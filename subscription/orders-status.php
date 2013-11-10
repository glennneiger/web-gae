<?
session_start();
$viaId = $_POST['viaId'];

$objVia=new Via();
$cancelledOrderStatus = $objVia->get_cancelled_order_status_with_ocId($viaId);
set_sess_vars("cancelledOrdersStatus",$cancelledOrderStatus);
?>