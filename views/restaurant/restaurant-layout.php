<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J2 Restaurant - <?= htmlspecialchars($titulo ?? 'Restaurant') ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" >
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Tailwind JDOS -->
    <link rel="stylesheet" href="/build/css/tailwindapp.css">
    <!-- Estilos generales JDOS -->
    <link rel="stylesheet" href="/build/css/app.css">
    <link rel="icon" type="image/x-icon" href="/build/img/favicon.ico">
</head>

<body class="min-h-screen bg-slate-50 font-[Poppins] text-slate-800" data-page="<?= $page ?? '' ?>">
    <div class="min-h-screen">
        <?php include_once __DIR__ . '/templates/header.php';?>
        <main>
            <?= $contenido ?>
        </main>
    </div>

    <!-- Lo utilizaremos posteriormente para acciones Restaurant -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Bundle actual de JDOS-restaurant -->
    <script type="module" src="/build/js/restaurant/main.js" defer></script>

</body>

</html>