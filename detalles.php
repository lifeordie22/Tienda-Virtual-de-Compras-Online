<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == ''){
  echo 'Error al procesar la petición';
  exit;
}else{
  $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);


  if($token == $token_tmp){
    $sql = $con->prepare("SELECT count(id_producto) FROM productos WHERE id_producto=? AND activo=1");
    $sql->execute([$id]);
    if($sql->fetchColumn() > 0){
      $sql = $con->prepare("SELECT nombre_producto, descripcion_producto, precio_producto, descuento FROM productos WHERE id_producto=? AND activo=1 LIMIT 1");
      $sql->execute([$id]);
      $row = $sql->fetch(PDO::FETCH_ASSOC);
      $nombre = $row['nombre_producto'];
      $descripcion = $row['descripcion_producto'];
      $precio = $row['precio_producto'];
      $descuento = $row['descuento'];
      $precio_desc = $precio - (($precio * $descuento)) / 100;
      $dir_img = 'img/productos/'.$id.'/';

      $ruta_img = $dir_img . 'nombreIgual.jpg';

      if(!file_exists($ruta_img)){
        $ruta_img = 'img/no-photo.jpg';
      }

      $imagenes = array();
      if(file_exists($dir_img)){
      $dir = dir($dir_img);

      while(($archivo = $dir->read()) != false){
          if($archivo != 'nombreIgual.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))){
            $imagenes[] = $dir_img . $archivo;
        }
      }
      $dir->close();
      }
    }
  }else{
    echo 'Error al procesar la petición';
    exit;
  }
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
        
        <div class="col-md-6 order-md-1">
        <div id="carouselImages" class="carousel slide">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="<?php echo $ruta_img; ?>"  class="d-block w-100" >
    </div>

    <?php foreach($imagenes as $img){ ?>
    <div class="carousel-item">
    <img src="<?php echo $img; ?>"  class="d-block w-100" >
    </div>
    <?php   } ?>

  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="carouselImages" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="carouselImages" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
          
        </div>
        
        <div class="col-md-6 order-md-2">
        <h2><?php echo $nombre; ?></h2>
        <?php if($descuento > 0){ ?>
          <p><del><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></del></p>
          <h2>
          <?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?>
          <small class="text-success">-><?php echo $descuento; ?>% descuento</small>
        </h2>

        <?php   } else{ ?>
          <h2><?php echo MONEDA . number_format($precio, 2, '.', ','); ?></h2>

          <?php   } ?>

          <p class="lead">
            <?php echo $descripcion ?>
          </p>

          <div class="d-grid gap-3 col-10 mx-auto">
              <button class="btn btn-primary" type="button">COMPRAR</button>
              <button class="btn btn-outline-primary" type="button" onclick="addProducto(<?php echo 
              $id; ?>, '<?php echo $token_tmp ?>' )">AGREGAR AL CARRITO</button>
          </div>
        </div>

      </div>
    </div>
</main>
    <script>
      function addProducto(id, token){
        let url = 'clases/carrito.php';
        let formData = new FormData();
        formData.append('id', id);
        formData.append('token', token);

        fetch(url,{
          method: 'POST',
          body: formData,
          mode: 'cors'
        }).then(response => response.json())
        .then(data =>{
          if(data.ok){
            let elemento = document.getElementById("num_cart");
            elemento.innerHTML = data.numero;
          }
        })
      }
      </script>
</body>
</html>


        