<?php
    $creator = $this->Db_model->row_id('users', $row->creator_id);
?>

<div class="center_box_750">
<table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">ID</td>
                    <td><?= $row->id ?></td>
                </tr>
                <tr class="d-none">
                    <td class="td-title"></td>
                    <td><a href="<?= URL_ADMIN . "posts/open/{$row->id}" ?>" class="btn btn-sm btn-light w120p" target="_blank">Abrir</a></td>
                </tr>
                <tr>
                    <td class="td-title">Tipo</td>
                    <td>Institución educativa</td>
                </tr>
                <tr>
                    <td class="td-title">Título</td>
                    <td><?= $row->post_name ?></td>
                </tr>
                <tr>
                    <td class="td-title">Ciudad</td>
                    <td><?= $this->App_model->place_name($row->place_id) ?></td>
                </tr>
                <tr>
                    <td class="td-title">Descripción</td>
                    <td><?= $row->excerpt ?></td>
                </tr>
            </tbody>
        </table>

        <table class="table bg-white">
            <tbody>
                <tr>
                    <td class="td-title">Comentarios</td>
                    <td><?= $row->qty_comments ?></td>
                </tr>
                <tr>
                    <td class="td-title">Publicada</td>
                    <td><?= $row->published_at ?></td>
                </tr>
                <tr>
                    <td class="td-title">Creada por</td>
                    <td>
                        <?= $row->updater_id ?> &middot;
                        <a href="<?= URL_ADMIN . "users/profile/{$row->creator_id}" ?>">
                            <?= $this->App_model->name_user($row->creator_id); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Creada en</td>
                    <td>
                        <?= $row->created_at ?> <br>
                        <?= $this->pml->ago($row->created_at); ?>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Actualizada por</td>
                    <td>
                        <?= $row->updater_id ?> &middot;
                        <a href="<?= URL_ADMIN . "users/profile/{$row->updater_id}" ?>">
                            <?= $this->App_model->name_user($row->updater_id); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="td-title">Actualizada en</td>
                    <td>
                        <?= $row->updated_at ?> <br>
                        <?= $this->pml->ago($row->updated_at); ?>
                    </td>
                </tr>
            </tbody>
        </table>
</div>