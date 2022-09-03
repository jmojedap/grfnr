<?php
    $general_colors = array(
        array('name' => 'info','background' => '#00c0ef', 'font_color' => '#FFFFFF'),
        array('name' => 'info hover','background' => '#0ab6e0', 'font_color' => '#FFFFFF'),
        array('name' => 'primary','background' => '#3E8EF7', 'font_color' => '#FFFFFF'),
        array('name' => 'primary hover','background' => '#589FFC', 'font_color' => '#FFFFFF'),
        array('name' => 'success','background' => '#11c26d', 'font_color' => '#FFFFFF'),
        array('name' => 'success hover','background' => '#28d17c', 'font_color' => '#FFFFFF'),
        array('name' => 'warning','background' => '#fdd835', 'font_color' => '#FFFFFF'),
        array('name' => 'warning hover','background' => '#f1cd2d', 'font_color' => '#FFFFFF'),
        array('name' => 'danger','background' => '#FF4C52', 'font_color' => '#FFFFFF'),
        array('name' => 'danger hover','background' => '#FF666B', 'font_color' => '#FFFFFF')
    );

    $app_colors = array(
        array('name' => 'main','background' => '#00ACC9', 'font_color' => '#FFF'),
        array('name' => 'light','background' => '#00BEE0', 'font_color' => '#000'),
        array('name' => 'dark','background' => '#008FA6', 'font_color' => '#FFF'),
        array('name' => 'darker','background' => '#00798C', 'font_color' => '#FFF'),
        array('name' => 'secondary','background' => '#80BA26', 'font_color' => '#000'),
        array('name' => 'color-2','background' => '#CAD401', 'font_color' => '#000'),
        array('name' => 'color-3','background' => '#202945', 'font_color' => '#FFFFFF'),
        array('name' => 'color-4','background' => '#462B72', 'font_color' => '#FFFFFF'),
        array('name' => 'color-5','background' => '#E33939', 'font_color' => '#FFFFFF'),
        array('name' => 'color-6','background' => '#ff6a00', 'font_color' => '#FFF'),
        array('name' => 'color-7','background' => '#ed1798', 'font_color' => '#FFFFFF'),
        array('name' => 'color-8','background' => '#FC3F71', 'font_color' => '#FFFFFF'),
        array('name' => 'emotion-1','background' => '#99D96C', 'font_color' => '#000'),
        array('name' => 'emotion-2','background' => '#FFBC69', 'font_color' => '#000'),
        array('name' => 'emotion-3','background' => '#C5ADED', 'font_color' => '#000'),
        array('name' => 'emotion-4','background' => '#FCD22A', 'font_color' => '#000'),
        array('name' => 'emotion-5','background' => '#9ED0FF', 'font_color' => '#000'),
        array('name' => 'emotion-6','background' => '#FFAFAB', 'font_color' => '#000'),
        array('name' => 'emotion-7','background' => '#FF94D6', 'font_color' => '#000'),
        array('name' => 'emotion-8','background' => '#EDEDED', 'font_color' => '#000'),
    );

    $arr_classes = array(
        'light',
        'info',
        'primary',
        'success',
        'warning',
        'danger',
        'secondary',
    );
?>

<script>
    $(document).ready(function(){
        $('.btn').click(function(){
            console.log('mostrando')
            toastr['success']('Correcto');
            toastr['error']('Ocurrió un error');
            toastr['info']('Estamos informando algo');
            toastr['warning']('Estamos informando algo');
        })
    })
</script>

<div class="row">
    <div class="col-md-4">
        <table class="table bg-white">
            <thead>
                <th>Color</th>
                <th></th>
            </thead>
            <?php foreach ( $general_colors as $color ) { ?>
                <tr>
                    <td><?= $color['name'] ?></td>
                    <td style="background-color: <?= $color['background'] ?>; color: <?= $color['font_color'] ?>"><?= $color['background'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-4">
        <h3>Colores aplicación</h3>
        <p>
        [
        <?php foreach ( $app_colors as $color ) : ?>
            '<?php echo $color['background'] ?>',
        <?php endforeach ?>
        ]
        </p>
        <table class="table bg-white">
            <thead>
                <th>Color</th>
                <th></th>
            </thead>
            <?php foreach ( $app_colors as $color ) { ?>
                <tr>
                    <td><?= $color['name'] ?></td>
                    <td style="background-color: <?= $color['background'] ?>; color: <?= $color['font_color'] ?>"><?= $color['background'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-4">
        <?php foreach ( $arr_classes as $class ) { ?>
            <button class="btn btn-<?= $class ?> btn-block"><?= $class ?></button>
        <?php } ?>
    </div>
</div>