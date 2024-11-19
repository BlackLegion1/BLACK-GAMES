<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gb"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificacioooooooooooooooon
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// detalles en el carrito
$productos = [];
$subtotal_total = 0;
$iva_total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
        $consulta = "SELECT id_v, nom_v, precio FROM videojuegos WHERE id_v = $id_producto";
        $resultado = $conn->query($consulta);
        if ($resultado->num_rows > 0) {
            $producto = $resultado->fetch_assoc();
            $productos[] = $producto;

            // subtotal e IVA
            $subtotal = $producto['precio'] * $cantidad;
            $iva = $subtotal * 0.16; 

            $subtotal_total += $subtotal;
            $iva_total += $iva;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            max-width: 100px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<h1>Resumen de tu Compra</h1>

<?php if (count($productos) > 0): ?>
    <table>
        <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>IVA (16%)</th>
        </tr>
        <?php foreach ($productos as $producto): 
            $subtotal = $producto['precio'] * $_SESSION['carrito'][$producto['id_v']];
            $iva = $subtotal * 0.16;
        ?>
            <tr>
                <td><img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nom_v']); ?>"></td>
                <td><?php echo htmlspecialchars($producto['nom_v']); ?></td>
                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                <td><?php echo $_SESSION['carrito'][$producto['id_v']]; ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
                <td>$<?php echo number_format($iva, 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h3><strong>Subtotal:</strong> $<?php echo number_format($subtotal_total, 2); ?></h3>
    <h3><strong>IVA (16%):</strong> $<?php echo number_format($iva_total, 2); ?></h3>
    <h3><strong>Total:</strong> $<?php echo number_format($subtotal_total + $iva_total, 2); ?></h3>
<?php else: ?>
    <p>No hay productos en tu carrito.</p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
