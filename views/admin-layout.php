<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J2 - <?php echo $titulo; ?></title>
    <!--
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet"> -->
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.3/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/r-3.0.1/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" href="/build/css/tailwindapp.css">
    <link rel="stylesheet" href="/build/css/app.css">
    <link rel="icon" type="image/x-icon" href="/build/img/favicon.ico"/>
    
</head>
<body class="dashboardno">
    <div class="dashboard">
        <?php include_once __DIR__."/templates/sidebar.php"; ?> <!-- barra lateral -->

        <div class="principal">
            <?php include_once __DIR__."/templates/header.php"; ?>  <!-- barra superior -->

            <div class="contenido relative">

                <!-- <?php if(isset($this->getData()['Aviso_vencimiento'])): ?>
                    <div class="absolute top-1 z-20 left-1/2 -translate-x-2/3  bg-yellow-50 shadow-[0_3px_10px_-3px_rgba(6,81,237,0.3)] text-slate-900 flex flex-col justify-center py-2 px-4 mx-4 min-w-xs max-w-lg rounded-md overflow-hidden">
                        <h2 class="text-xl font-semibold"><span class="text-3xl">🚨</span><?php echo $this->data['msj_titulo_aviso_vencimiento']; //$titulo; ?></h2>
                        <p class="text-base text-slate-400 mt-0.5"><?php echo $this->data['msj_texto_aviso_vencimiento']; ?></p>
                    </div>
                <?php endif; ?> -->

                <?php echo $contenido; ?>
            </div>
        </div>
    </div>

    <?php //$script = '<script src="build/js/app.js"></script>'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.js" integrity="sha384-P2rohseTZr3+/y/u+6xaOAE3CIkcmmC0e7ZjhdkTilUMHfNHCerfVR9KICPeFMOP" crossorigin="anonymous"></script> <!-- requerido para generar PDF en datatable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script> <!-- requerido para generar PDF en datatable -->
    <script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.3/b-3.2.4/b-colvis-3.2.4/b-html5-3.2.4/r-3.0.1/datatables.min.js"></script> <!-- funciones requeridaas para datatables -->
    <script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.print.min.js"></script> <!-- para habilitar btn de imprimir -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bwip-js/2.0.10/bwip-js-min.js" integrity="sha512-5uGnKj8h56cEozQZHq09metdJx1V6UM8qs7M+bG7EJMKYAKBl+CWo1SdmqU9RZTTlzIIa4xTCR5LWBY3spP9zw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> <!-- para el manejo de QR en almacen -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    
    <!--<script src="/build/js/bundle.min.js" defer></script>-->
    <script src="/build/js/bundle.ts.min.js" defer></script>

</body>
</html>