<?php
//LLamamos a Config.php
require 'config.php';

//Importamos las clases a utilizar
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

//Declaramos las variables a utilizar 
$producto = 'cafe';
$precio = 50;
$pecio = (int) $precio;
$envio = 0;
$total = $precio + $envio;

//Utilizamos la clase payer que sera el tipo de pago en este caso sera mediante PayPal
$compra = new Payer();
$compra->setPaymentMethod('paypal');

//Creamos el articulo a comprar, le damos nombre, tipo de moneda, cantidad a comprar, y el precio
$articulo = new Item();
$articulo->setName($producto)
	     ->setCurrency('MXN')
	     ->setQuantity(1)
	     ->setPrice($precio);

//Cremamos la lista de articulos a comprar
$listaArticulos = new ItemList();
$listaArticulos->setItems(array($articulo));
//Si fuera mas de un articulo se pone en arreglo los 2 articulos
//$listaArticulos->setItems(array($articulo, $articulo2));

//Detalles es el subtotal y el costo del envio
$detalles = new Details();
$detalles->setShipping($envio)
	     ->setSubtotal($precio);

//En Amount se coloca el tipo de moneda, el costo total de la transferencia, y detalles
$cantidad = new Amount();
$cantidad->setCurrency('MXN')
	     ->setTotal($total)
	     ->setDetails($detalles);

//Transaccion se coloca la transaccion a hacer en este caso es cantidad, donde guarda el total y todo distribuido
//se coloca tambien la lista de articulos, una descripcion en este caso sera un pago, y el identificados de este pago
$transaccion = new Transaction();
$transaccion->setAmount($cantidad)
			->setItemList($listaArticulos)
			->setDescription('Pago')
			->setInvoiceNumber(uniqid());

//Despues de hacer la Transferencia y todo salio correcto nos mostrara la ventana de paypal 
//el usuario tiene dos opciones realizar la compra o arrepentirse y cancelar la compra
//estas 2 opciones retornan una url segun el caso 
$redireccionar = new RedirectUrls();
$redireccionar->setReturnUrl(URL_SITIO . "/pago_finalizado.php?exito=true")
			  ->setCancelUrl(URL_SITIO . "/pago_finalizado.php?exito=false");

//Aqui ya se hace la transferencia aqui nos preguntara la intencion de la transferencia
//El tipo de pago, guardara el redireccionamiento que se hiso segun la opcion del usuario
//y pide la(s) transacciones a hacer
$pago = new Payment();
$pago->setIntent("sale")
	 ->setPayer($compra)	
	 ->setRedirectUrls($redireccionar)
	 ->setTransactions(array($transaccion));

	 try {
		 //Crea ya el pago apicontext guarda todas las credenciales de nuestra app creada en paypal se declaro en config,php
	 	$pago->create($apiContext);
	 } catch (PayPal\Exception\PaypalConnectionException $pce) {
	 	print_r(json_decode($pce->getData()));
	 	exit;
	 }

//Ya por ultimo redirecciona la pagina segun la opcion del usuario si compro o cancelo la transaccion
$aprovado = $pago->GetApprovalLink();
header("Location: {$aprovado}");