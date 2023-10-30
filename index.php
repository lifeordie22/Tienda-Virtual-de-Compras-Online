<?php
require 'config/config.php';
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT id_producto, nombre_producto, precio_producto FROM productos where activo=1");
$sql->execute();
$resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

//session_destroy();
//print_r($_SESSION);

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
                    <a href="#" class="nav-link active">Catálogo</a>
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
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <?php foreach($resultado as $row){ ?>
        <div class="col">
          <div class="card shadow-sm">
            <?php
            
            $id = $row['id_producto'];
            $imagen = "img/productos/".$id."/nombreIgual.jpg";

            if(!file_exists($imagen)){
                $imagen = "img/no-photo.jpg";
            }

            ?>
            <img src="<?php echo $imagen; ?>">
           <div class="card-body">
              <h5 class="card-title"><?php echo $row['nombre_producto']; ?></h5>
              <p class="card-text"><?php echo number_format($row['precio_producto'], 2, '.', ','); ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                    <a href="detalles.php?id=<?php echo $row['id_producto'] ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                </div>
                <button class="btn btn-outline-success" type="button" onclick="addProducto(<?php echo 
              $row['id_producto']; ?>, '<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>' )">AGREGAR AL CARRITO</button>
              </div>
              </div>
            </div>
          </div>
          <?php   } ?>
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