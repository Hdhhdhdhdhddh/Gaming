<?php
session_start();
$db = new SQLite3('ruta/a/tu/base_de_datos/sqlite.db');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


        $stmt = $db->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)');
        $stmt->bindValue(':nombre', $nombre, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $password, SQLITE3_TEXT);
        $stmt->execute();


        echo "Usuario registrado exitosamente.";
    } elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];


        $stmt = $db->prepare('SELECT * FROM usuarios WHERE email = :email');
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        $usuario = $result->fetchArray(SQLITE3_ASSOC);


        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            header('Location: juego.php');
            exit();
        } else {
            echo "Credenciales incorrectas.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro, Inicio de Sesión y Juego Tap Tap</title>
    <style>
        body { text-align: center; }
        form { margin: 20px auto; width: 300px; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; margin-top: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        #game, #scoreboard { display: none; }
    </style>
</head>
<body>
    <div id="register">
        <h1>Registro</h1>
        <form action="" method="POST">
            <input type="hidden" name="action" value="register">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
    </div>


    <div id="login">
        <h1>Inicio de Sesión</h1>
        <form action="" method="POST">
            <input type="hidden" name="action" value="login">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>


    <script>
        if (<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
            document.getElementById('register').style.display = 'none';
            document.getElementById('login').style.display = 'none';
            document.getElementById('game').style.display = 'block';
        } else {
            document.getElementById('register').style.display = 'block';
            document.getElementById('login').style.display = 'block';
            document.getElementById('game').style.display = 'none';
        }
    </script>
</body>
</html>

