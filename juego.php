<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}


$db = new SQLite3('ruta/a/tu/base_de_datos/sqlite.db');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['puntos'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $puntos = $_POST['puntos'];


    $stmt = $db->prepare('INSERT INTO puntuaciones (usuario_id, puntos) VALUES (:usuario_id, :puntos)');
    $stmt->bindValue(':usuario_id', $usuario_id, SQLITE3_INTEGER);
    $stmt->bindValue(':puntos', $puntos, SQLITE3_INTEGER);
    $stmt->execute();


    echo "Puntuación guardada exitosamente.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego Tap Tap</title>
    <style>
        body { text-align: center; }
        button { padding: 20px; font-size: 2em; margin: 10px; }
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 10px; margin: 0 auto; }
    </style>
</head>
<body>
    <h1>Juego Tap Tap</h1>
    <p>Puntuación: <span id="puntos">0</span></p>
    <button id="tapButton">Tap Tap!</button>
    <button id="saveScoreButton">Guardar Puntuación</button>
    <button id="logoutButton" onclick="window.location.href='logout.php'">Cerrar Sesión</button>


    <h1>Ranking de Puntuaciones</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Puntos</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody id="scoreTableBody">
            <?php
            $result = $db->query('SELECT usuarios.nombre, puntuaciones.puntos, puntuaciones.fecha FROM puntuaciones JOIN usuarios ON puntuaciones.usuario_id = usuarios.id ORDER BY puntuaciones.puntos DESC');
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                echo "<tr><td>{$row['nombre']}</td><td>{$row['puntos']}</td><td>{$row['fecha']}</td></tr>";
            }
            ?>
        </tbody>
    </table>


    <script>
        let puntos = 0;
        document.getElementById('tapButton').addEventListener('click', function() {
            puntos++;
            document.getElementById('puntos').innerText = puntos;
        });


        document.getElementById('saveScoreButton').addEventListener('click', function() {
            const puntosInput = document.createElement('input');
            puntosInput.type = 'hidden';
            puntosInput.name = 'puntos';
            puntosInput.value = puntos;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            form.appendChild(puntosInput);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>

