<?php
    $fields = [
        ['name' => 'position', 'type' => 'number'],
        ['name' => 'place_id', 'type' => 'number'],
        ['name' => 'related_1', 'type' => 'number'],
        ['name' => 'related_2', 'type' => 'number'],
        ['name' => 'date_1', 'type' => 'text'],
        ['name' => 'date_2', 'type' => 'text'],
        ['name' => 'text_1', 'type' => 'text'],
        ['name' => 'text_2', 'type' => 'text'],
        ['name' => 'integer_1', 'type' => 'number'],
        ['name' => 'integer_2', 'type' => 'number'],
        ['name' => 'integer_3', 'type' => 'number']
    ];
?>

<?php $this->load->view('assets/summernote') ?>

<div id="editPost" class="container">
    <form accept-charset="utf-8" method="POST" id="edit_form" @submit.prevent="handleSubmit">
        <input type="hidden" name="id" value="<?= $row->id ?>">
        <fieldset v-bind:disabled="loading">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="post_name">Título</label>
                        <input
                            name="post_name" type="text" class="form-control"
                            required
                            title="Título" placeholder="Título"
                            v-model="formValues.post_name"
                        >
                    </div>
        
                    <div class="mb-3">
                        <label for="content">Resumen</label>
                        <textarea
                            name="excerpt" class="form-control" rows="4" maxlength="280"
                            title="Resumen" placeholder="Resumen"
                            v-model="formValues.excerpt"
                        ></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content">Contenido</label>
                        <textarea
                            name="content" class="summernote"
                            title="Resumen" placeholder="Resumen"
                        ><?= $row->content ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="content">Contenido incrustado (embed)</label>
                        <textarea name="content_embed" placeholder="<Inserte el código HTML>" class="form-control" rows="6"
                            v-model="formValues.content_embed"
                        ></textarea>
                    </div>

                    <?php foreach ( $fields as $field ) : ?>
                        <div class="mb-3 row">
                            <label for="<?= $field['name'] ?>" class="col-md-4 col-form-label text-right"><?= str_replace('_', ' ', $field['name']) ?></label>
                            <div class="col-md-8">
                                <input
                                    name="<?= $field['name'] ?>" type="<?= $field['type'] ?>" class="form-control" value="<?= $row->field['name'] ?>"
                                >
                            </div>
                        </div>
                    <?php endforeach ?>
                    
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <button class="btn btn-success btn-block" type="submit">Guardar</button>
                    </div>
                    <div class="mb-3">
                        <label for="status">Estado</label>
                        <select name="status" v-model="formValues.status" class="form-control" required>
                            <option v-for="(option_status, key_status) in optionsStatus" v-bind:value="key_status">{{ option_status }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cat_1">Categoría</label>
                        <select name="cat_1" v-model="formValues.cat_1" class="form-control" v-on:change="get_options_cat_2">
                            <option v-for="(option_cat_1, key_cat_1) in options_cat_1" v-bind:value="key_cat_1">{{ option_cat_1 }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cat_2">Subcategoría</label>
                        <select name="cat_2" v-model="formValues.cat_2" class="form-control">
                            <option v-for="(option_cat_2, key_cat_2) in options_cat_2" v-bind:value="key_cat_2">{{ option_cat_2 }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="published_at">Fecha publicación</label>
                        <input
                            name="published_at" type="date" class="form-control"
                            title="Fecha publicación" placeholder="Fecha publicación"
                            v-model="formValues.published_at"
                        >
                    </div>
                    <div class="mb-3">
                        <label for="keywords">Palabras clave</label>
                        <textarea
                            name="keywords" rows="2" class="form-control"
                            title="Palabras clave" placeholder="Palabras clave"
                            v-model="formValues.keywords"
                        ></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="slug">Slug</label>
                        <input
                            name="slug" type="text" class="form-control" required
                            title="Slug" placeholder="Slug"
                            v-model="formValues.slug"
                        >
                    </div>
                </div>
            </div>
        <fieldset>
    </form>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var row = <?= json_encode($row) ?>;
row.cat_1 = '0<?= $row->cat_1 ?>';
row.cat_2 = '0<?= $row->cat_2 ?>';
row.status = '0<?= $row->status ?>';
row.published_at = '<?= substr($row->published_at,0,10) ?>';

// VueApp
//-----------------------------------------------------------------------------
var editPost = new Vue({
    el: '#editPost',
    data: {
        formValues: row,
        loading: false,
        optionsStatus: <?= json_encode($options_status) ?>,
        options_cat_1: <?= json_encode($options_cat_1) ?>,
        options_cat_2: <?= json_encode($options_cat_2) ?>,
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            var form_data = new FormData(document.getElementById('edit_form'))
            axios.post(URL_API + 'posts/save/', form_data)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        get_options_cat_2: function(){            
            var form_data = new FormData
            form_data.append('condition', 'category_id = 21 AND level = 1 AND parent_id = ' + parseInt(this.formValues.cat_1))
            form_data.append('empty_value', 'Seleccione la subcategoría')
            axios.post(URL_API + 'items/get_options/', form_data)
            .then(response => {
                this.options_cat_2 = response.data.options
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>