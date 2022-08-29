<?php
    $hidden_fields = array(
        'id',
        'created_at',
        'creator_id',
        'updater_id',
        'updated_at',
    );
?>

<div id="edit_row">
    <div class="center_box_750">
        <form accept-charset="utf-8" method="POST" id="edit_form" @submit.prevent="send_form">
            <div class="card">
                <div class="card-body">
                    <fieldset v-bind:disabled="loading">
                        <input type="hidden" name="id" value="<?= $row->id ?>">
                        <?php foreach ( $fields as $field ) : ?>
                            <?php if ( ! in_array($field,$hidden_fields) ) : ?>
                                <div class="mb-3 row">
                                    <label for="<?= $field?>" class="col-md-4 col-form-label text-right"><?= $field ?></label>
                                    <div class="col-md-8">
                                        <input
                                            name="<?= $field?>" type="text" class="form-control" title="<?= $field ?>" v-model="form_values.<?= $field?>"
                                        >
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach ?>
                        
                        <div class="mb-3 row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var row = <?= json_encode($row) ?>;
var form_destination = '<?= $form_destination ?>';

// VueApp
//-----------------------------------------------------------------------------
var edit_row = new Vue({
    el: '#edit_row',
    data: {
        form_values: row,
        loading: false,
    },
    methods: {
        send_form: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('edit_form'))
            axios.post(form_destination, form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>