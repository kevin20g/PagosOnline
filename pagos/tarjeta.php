<?php
//LLamamos a Config.php
require '../config.php';

use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentCard;
use PayPal\Api\Transaction;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;

//Declaramos las variables a utilizar despues se cambiara por datos reales
$producto = 'cafe';
$precio = 50;
$pecio = (int) $precio;
$envio = 0;
$total = $precio + $envio;

//Ejemplo con Tarjeta de credito pedimos la siguiente informacion 
//Direccion -> Numero, calle, colonia, ciudad, estado, codigo postal, pais, telefono
$addr = new Address();
$addr->setLine1("851 Benito Juárez")
    ->setLine2("Centro")
    ->setCity("Monterrey")
    ->setState("NL")
    ->setPostalCode("64000")
    ->setCountryCode("MX")
    ->setPhone("01-800-120-5000");

//Informacion de tarjeta de credito
//Tarjeta -> tipo de tarjeta, numero, mes de expiracion, año de expiracion, cvv, primer nombre, apellido, pais, direccion
$paymentCard = new PaymentCard();
$paymentCard->setType("visa")
    ->setNumber("4417119669820331")
    ->setExpireMonth("11")
    ->setExpireYear("2019")
    ->setCvv2("012")
    ->setFirstName("Joe")
    ->setLastName("Shopper")
    ->setBillingCountry("US")
    ->setBillingAddress($addr);

//se envia datos de tarjeta
$fi = new FundingInstrument();
$fi->setPaymentCard($paymentCard);

//tipo de pago en este caso tarjeta de credito
$payer = new Payer();
$payer->setPaymentMethod("credit_card")
    ->setFundingInstruments(array($fi));


    $articulo = new Item();
    $articulo->setName($producto)
             ->setCurrency('USD')
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
$cantidad->setCurrency('USD')
	     ->setTotal($total)
         ->setDetails($detalles);
         
//Transaccion se coloca la transaccion a hacer en este caso es cantidad, donde guarda el total y todo distribuido
//se coloca tambien la lista de articulos, una descripcion en este caso sera un pago, y el identificados de este pago
$transaccion = new Transaction();
$transaccion->setAmount($cantidad)
			->setItemList($listaArticulos)
			->setDescription('Pago')
			->setInvoiceNumber(uniqid());


//Aqui ya se hace la transferencia aqui nos preguntara la intencion de la transferencia
//El tipo de pago, guardara el redireccionamiento que se hiso segun la opcion del usuario
//y pide la(s) transacciones a hacer
$payment = new Payment();
$payment->setIntent("authorize")
    ->setPayer($payer)
    ->setTransactions(array($transaccion));

// For Sample Purposes Only.
$request = clone $payment;

// ### Create Payment
// Create a payment by calling the payment->create() method
// with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
// The return object contains the state.
try {
    $payment->create($apiContext);
} catch (PayPal\Exception\PaypalConnectionException $pce) {
    print_r(json_decode($pce->getData()));
}

// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
// ResultPrinter::printResult('Authorize a Payment', 'Authorized Payment', $payment->getId(), $request, $payment);

$transactions = $payment->getTransactions();
$relatedResources = $transactions[0]->getRelatedResources();
$authorization = $relatedResources[0]->getAuthorization();
 


$approvalUrl = $payment->getApprovalLink();
echo $approvalUrl;
header("Location: {$approvalUrl}");


// var_dump($transaction);
// var_dump($relatedResources);
//print_r($authorization);
//print_r($payment->getId());