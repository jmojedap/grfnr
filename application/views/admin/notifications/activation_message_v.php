<?php
    $texts['title'] = 'Bienvenido a ' . APP_NAME;
    $texts['paragraph'] = 'Para activar tu cuenta haz clic en el siguiente enlace:';
    $texts['button'] = 'ACTIVAR';
    $texts['link'] = "accounts/activation/{$user->activation_key}";
    
    if ( $activation_type == 'recovery' ) 
    {
        $texts['title'] = APP_NAME;
        $texts['paragraph'] = 'Para reestablecer tu contraseña haz clic en el siguiente enlace:';
        $texts['button'] = 'NUEVA CONTRASEÑA';
        $texts['link'] = "accounts/recover/{$user->activation_key}";
    }
?>
<h3><?= $user->display_name ?></h3>
<p><?= $texts['paragraph'] ?></p>
<a style="<?= $styles['btn'] ?>" href="<?= URL_APP . $texts['link'] ?>" target="_blank">
    <?= $texts['button'] ?>
</a>