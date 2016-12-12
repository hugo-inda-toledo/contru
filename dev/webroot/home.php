<?php $img_path="img/"; ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ('AChEE - Intranet') ?></title>
    <link href="favicon.png" type="image/x-icon" rel="icon">
    <link href="favicon.png" type="image/x-icon" rel="shortcut icon">
    <!-- css -->
    <link rel="stylesheet" href="metroui/css/metro-icons.css">
    <link rel="stylesheet" href="metroui/css/metro.min.css">
    <link rel="stylesheet" href="metroui/css/metro-ani-icons.css">
    <link rel="stylesheet" href="css/mediaqueries.css">
    <link rel="stylesheet" href="css/home.css">
    <!-- js -->
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/achee.js"></script>
    <script src="metroui/js/metro.js"></script>
    <script src="js/select2.min.js"></script>
</head>


<body class="body">
    <div class="container header">
        <div class="grid">
            <a class="brand"><img src="<?php echo $img_path ?>logo.png" class="logo imgResponse"></a>
            <ul class="main-menu horizontal-menu compact">
                <li>
                    <a class="active" href="<? __('#Link'); ?>"><?php echo ('Inicio'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Trámites'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Comunicación'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Utilidad'); ?></a>
                </li>
                <li>
                    <a href="<? __('#Link'); ?>"><?php echo ('Documentos'); ?></a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container page-content">
        <div class="flex-grid">
            <div class="row">
                <div class="cell size4">
                    <h2 class="bg-green">Destacados</h2>
                </div>
                <div class="cell size4">
                    <div class="tile">
                        <div class="tile-content">
                            <span class="tile-label">Label</span>
                            <span class="tile-badge">5</span>
                        </div>
                    </div>
                </div>
                <div class="cell size4">
                    <div class="tile">
                        <div class="tile-content iconic">
                            <span class="mif-earth" style="color: red;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="flex-grid">
                <div class="row">
                    <div class="cell size1">
                        <img src="<?php echo $img_path ?>iso.png" alt="" class="isoFoot">
                    </div>
                    <div class="cell size8">
                        <p>Agencia Chilena de Eficiencia Energética (AChEE) Monseñor Nuncio Sótero Sanz n° 221. Providencia. Santiago - Chile.</p>
                        <p>Email: <a href="mailto:info@acee.cl">info@acee.cl</a></p>
                    </div>
                    <div class="cell size1"></div>
                    <div class="cell size1"></div>
                    <div class="cell size1"></div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
