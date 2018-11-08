<?php
require 'config.php';

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;


$producto = 'cafe';
$precio = 50;
$pecio = (int) $precio;
$envio = 0;
$total = $precio + $envio;

$compra = new Payer();
$compra->setPaymentMethod('paypal');

$articulo = new Item();
$articulo->setName($producto)
	     ->setCurrency('MXN')
	     ->setQuantity(1)
	     ->setPrice($precio);

$listaArticulos = new ItemList();
$listaArticulos->setItems(array($articulo));

$detalles = new Details();
$detalles->setShipping($envio)
	     ->setSubtotal($precio);


$cantidad = new Amount();
$cantidad->setCurrency('MXN')
	     ->setTotal($precio)
	     ->setDetails($detalles);

$transaccion = new Transaction();
$transaccion->setAmount($cantidad)
			->setItemList($listaArticulos)
			->setDescription('Pago')
			->setInvoiceNumber(uniqid());

$redireccionar = new RedirectUrls();
$redireccionar->setReturnUrl(URL_SITIO . "/pago_finalizado.php?exito=true")
			  ->setCancelUrl(URL_SITIO . "/pago_finalizado.php?exito=false");

$pago = new Payment();
$pago->setIntent("sale")
	 ->setPayer($compra)	
	 ->setRedirectUrls($redireccionar)
	 ->setTransactions(array($transaccion));

	 try {
	 	$pago->create($apiContext);
	 } catch (PayPal\Exception\PaypalConnectionException $pce) {
	 	print_r(json_decode($pce->getData()));
	 	exit;
	 }


$aprovado = $pago->GetApprovalLink();
header("Location: {$aprovado}");