<?php
    $texts['link'] = URL_APP . "accounts/validate_login_link/{$user->activation_key}";
?>
<h2 style="<? $styles['h2'] ?>"><b><?= $user->display_name ?></b></h2>
<p>Haz clic en el siguiente link para ingresar</p>
<br>
<a style="<?= $styles['btn'] ?>" href="<?= $texts['link'] ?>" target="_blank">
    INGRESAR
</a>