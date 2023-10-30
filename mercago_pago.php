<?php

require 'vendor/autoload.php';



MercadoPago\SDK::setAccessToken('TEST-3164937789388934-102101-bd8ab5e3e4affce98ba7dea344543a22-230891789');


$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = "01";
$item->title = "Producto nuevo";
$item->quantity = 1;
$item->unit_price = 150.00;
$item->currency_id = "ARS";
$preference->items = array($item);


$preference->back_urls = array(
  "success" => "http://localhost/carritodecompras_olmos_pablo/captura.php",
  "failure" => "http://localhost/carritodecompras_olmos_pablo/fallo.php"
);

$preference->auto_return = "approved";
$preference->binary_mode = true;

$preference->save();




?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <title>Document</title>
</head>
<body>
    <h3>Mercado Pago</h3>

    <div class="checkout-btn"></div>
    
    <script>
        const mp = new MercadoPago('TEST-0c888957-bed9-4870-b691-b6da12c9c7db', {
          locale: 'es-AR'
        });

        mp.checkout({
          preference: {
            id: '<?php echo $preference->id; ?>'
          },
          render: {
            container: '.checkout-btn',
            label: 'Pagar con Mercado Pago'
          }
        })

        
        </script>
</body>
</html>