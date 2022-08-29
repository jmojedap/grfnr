<?php
//Evitar errores de definición de variables e índices de arrays, 2013-12-07
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ERROR);
?>

<!doctype html>
<html lang="es">

<head>
    <?php $this->load->view('templates/print/main/head') ?>
</head>

<body>
    <div class="container">
        <div class="text-center border-bottom">
            <div class="mb-3">
                <div class="lead">
                    Dirección Observatorio y Gestión del Conocimiento Cultural
                </div>
                <div>
                    Subsecretaría de Cultura Ciudadana y Gestión del Conocimiento
                </div>
                <div>
                    Secretaría de Cultura, Recreación y Deporte
                </div>
            </div>
            <div class="mb-2">
                <div>
                    Preparado por:
                </div>
                <div class="lead">
                    <strong>
                        Mauricio Ojeda Pepinosa
                    </strong>
                </div>
                <small>javier.ojeda@scrd.gov.co</small>
            </div>
        </div>
        <div id="view_a" class="mt-3">
            <?php $this->load->view($view_a); ?>
        </div>
        
    </div>

    <?php $this->load->view('templates/print/main/script') ?>
</body>

</html>