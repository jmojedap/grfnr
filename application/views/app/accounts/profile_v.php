<div class="center_box_750">
    <div class="text-center">
        <?php if ( $this->session->userdata('role') <= 21  ) : ?>
            <a href="<?= URL_APP . "accounts/edit/image" ?>">
                <img src="<?= $row->url_image ?>" alt="Imagen de perfil del usuario" class="w120p rounded rounded-circle border mb-2" onerror="this.src='<?= URL_IMG ?>users/user.png'">
            </a>
        <?php endif; ?>
    </div>
    <table class="table bg-white text-left">
        <tbody>
            <tr>
                <td><span class="text-muted">Nombre</span></td>
                <td><?= $row->display_name ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Nombre de usuario</span></td>
                <td><?= $row->username ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Correo electrónico</span></td>
                <td><?= $row->email ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Género</span></td>
                <td><?= $this->Item_model->name(59, $row->gender) ?></td>
            </tr>

            <tr>
                <td><span class="text-muted">Rol de usuario</span></td>
                <td><?= $this->Item_model->name(58, $row->role) ?></td>
            </tr>
        </tbody>
    </table>
</div>