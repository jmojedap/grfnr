<?php
    $options_place_id = $this->App_model->options_place('type_id = 4 AND status = 1', 'full_name', 'Selecciona la ciudad');
?>

<div id="editPost" class="container">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="editForm" @submit.prevent="handleSubmit">
                    <input type="hidden" name="id" value="<?= $row->id ?>">
                    <fieldset v-bind:disabled="loading">
                        <div class="mb-3 row">
                            <label for="post_name" class="col-md-4 col-form-label text-right">Nombre institución</label>
                            <div class="col-md-8">
                                <input
                                    name="post_name" type="text" class="form-control"
                                    required
                                    title="Nombre institución" placeholder="Nombre institución"
                                    v-model="formValues.post_name"
                                >
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="place_id" class="col-md-4 col-form-label text-right">Ciudad</label>
                            <div class="col-md-8">
                                <select name="place_id" v-model="formValues.place_id" class="form-control" required>
                                    <option v-for="(optionPlace, keyPlace) in optionsPlace" v-bind:value="keyPlace">{{ optionPlace }}</option>
                                </select>
                            </div>
                        </div>
            
                        <div class="mb-3 row">
                            <label for="excerpt" class="col-md-4 col-form-label text-right">Descripción</label>
                            <div class="col-md-8">
                                <textarea
                                    name="excerpt" class="form-control" rows="3" maxlength="280"
                                    v-model="formValues.excerpt"
                                ></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-success w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    <fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var row = <?= json_encode($row) ?>;
row.place_id = '0<?= $row->place_id ?>';

// VueApp
//-----------------------------------------------------------------------------
var editPost = new Vue({
    el: '#editPost',
    data: {
        formValues: row,
        loading: false,
        optionsPlace: <?= json_encode($options_place_id) ?>
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('editForm'))
            axios.post(URL_API + 'posts/save/', form_data)
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