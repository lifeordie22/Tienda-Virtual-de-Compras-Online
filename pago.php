<?php
require 'config/config.php';
require 'config/database.php';
require 'vendor/autoload.php';



MercadoPago\SDK::setAccessToken(TOKEN_MP);

$preference = new MercadoPago\Preference();
$productos_mp = array();

$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

//print_r($_SESSION);

$lista_carrito = array();

if($productos != null){
    foreach($productos as $clave => $cantidad){
        $sql = $con->prepare("SELECT id_producto, nombre_producto, precio_producto, descuento, $cantidad AS cantidad
         FROM productos WHERE id_producto=? AND activo=1");
        $sql->execute([$clave]);
        $temp_producto = $sql->fetch(PDO::FETCH_ASSOC);
            if($temp_producto) $lista_carrito[]= $temp_producto;
    }
} else{
    header("Location: index.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de compras</title>
    <link href="css/estilos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
<header data-bs-theme="dark">
  
  <div class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a href="index.php" class="navbar-brand">
        <strong>Tienda Online</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarHeader">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">Catálogo</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Contacto</a>
                </li>
            </ul>
            <a href="checkout.php" class="btn btn-primary">
              Mi Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
            </a>
      </div>
    </div>
  </div>
</header>

<main>
    <div class="container">

        <div class="row">
            <div class="col-6">
                <h4>Detalles de Pago</h4>
            <div class="row">
            <div class="col-12">
            <div id="wallet_container"></div>
            </div> 
        </div>
</div>
        <div class="col-6">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($lista_carrito == null){
                        echo '<tr><td colspan="5" class="text-center"><b>Lista vacía</b></td></tr>';
                    }else{
                        $total = 0;
                        foreach($lista_carrito as $producto){
                            $_id = $producto['id_producto'];
                            $_nombre = $producto['nombre_producto'];
                            $_precio = $producto['precio_producto'];
                            $cantidad = $producto['cantidad'];
                            $_descuento = $producto['descuento'];
                            $_precio_desc = $_precio - (($_precio * $_descuento) / 100);
                            $subtotal = $cantidad * $_precio_desc;
                            $total += $subtotal;

                            $item = new MercadoPago\Item();
                            $item->id = $_id;
                            $item->title = $_nombre;
                            $item->quantity = $cantidad;
                            $item->unit_price = $_precio_desc;
                            $item->currency_id = "ARS";
                            array_push($productos_mp, $item);
                            unset($item);
                         ?>   
                        
                    
                    <tr>
                        <td><?php echo $_nombre; ?></td>
                        <td>
                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                    <td colspan="3"></td>
                    <td colspan="2"><p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p></td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>

    </div>
    </div>
    </div>
   
</main>

<?php
$preference->items = $productos_mp;

$preference->back_urls = array(
    "success" => "http://localhost/carritodecompras_olmos_pablo/captura.php",
    "failure" => "http://localhost/carritodecompras_olmos_pablo/fallo.php"
  );

    $preference->auto_return = "approved";
    $preference->binary_mode = true;

$preference->save();
?>

<script>
        const mp = new MercadoPago('TEST-0c888957-bed9-4870-b691-b6da12c9c7db', {
          locale: 'es-AR'
        });

        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: '<?php echo $preference->id; ?>',
                redirectMode: "modal"
          },
        })

        
        </script>


</body>
</html>