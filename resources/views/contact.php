<?php

use function Lib\Global\asset;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <title>Pruebas</title>
</head>

<body class="dark:bg-gray-600">
    <div class="container my-8">
        <h1 class="text-4xl font-bold text-white">Lista de Contactos</h1>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Correo electrónico
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Teléfono
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4"><?= $contact['id'] ?></td>
                            <td class="px-6 py-4"><?= $contact['name'] ?></td>
                            <td class="px-6 py-4"><?= $contact['email'] ?></td>
                            <td class="px-6 py-4"><?= $contact['phone'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>