<?php
    $options_place_id = $this->App_model->options_place('type_id = 4 AND status = 1', 'full_name', 'Selecciona la ciudad');
    $arrStatus = $this->Item_model->arr_options('category_id = 42');
?>

<div id="editPost" class="container">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="editForm" @submit.prevent="handleSubmit">
                    <input type="hidden" name="id" value="<?= $row->id ?>">
                    <fieldset v-bind:disabled="loading">
                        <div class="mb-3 row">
                            <label for="code" class="col-md-4 col-form-label text-right">Abreviatura</label>
                            <div class="col-md-8">
                                <input
                                    name="code" type="text" class="form-control"
                                    required
                                    title="Abreviatura" placeholder="Abreviatura"
                                    v-model="formValues.code"
                                >
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="post_name" class="col-md-4 col-form-label text-right">Título escena</label>
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
                            <label for="text_1" class="col-md-4 col-form-label text-right">Nombre</label>
                            <div class="col-md-8">
                                <input
                                    name="text_1" type="text" class="form-control"
                                    required
                                    title="Nombre" placeholder="Nombre"
                                    v-model="formValues.text_1"
                                >
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

                        <div class="mb-3 row">
                            <label for="slug" class="col-md-4 col-form-label text-right">Slug</label>
                            <div class="col-md-8">
                                <input
                                    name="slug" type="text" class="form-control"
                                    required
                                    title="Slug" placeholder="Slug"
                                    v-model="formValues.slug"
                                >
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="text_2" class="col-md-4 col-form-label text-right">Código UCC</label>
                            <div class="col-md-8">
                                <input
                                    name="text_2" type="text" class="form-control"
                                    required
                                    title="Abreviatura" placeholder="Abreviatura"
                                    v-model="formValues.text_2"
                                >
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="status" class="col-md-4 col-form-label text-right">Estado publicación</label>
                            <div class="col-md-8">
                                <select name="status" v-model="formValues.status" class="form-control" required>
                                    <option v-for="optionStatus in arrStatus" v-bind:value="optionStatus.cod">{{ optionStatus.name }}</option>
                                </select>
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
        arrStatus: <?= json_encode($arrStatus) ?>,
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