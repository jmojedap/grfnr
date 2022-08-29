<?php
    $fields = [
        'position', 'year', 'month', 'day','start','end','week_day','week_number',
        'business_day', 'qty_days', 'qty_business_days'
    ];
?>

<div id="editPlaceApp">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="periodForm" @submit.prevent="handleSubmit">
                    <fieldset v-bind:disabled="loading">
                        <div class="mb-3 row">
                            <label for="type_id" class="col-md-4 col-form-label text-right">Tipo</label>
                            <div class="col-md-8">
                                <select name="type_id" v-model="fields.type_id" class="form-control" required>
                                    <option v-for="optionType in arrType" v-bind:value="optionType.str_cod">{{ optionType.name }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="mb-3 row">
                            <label for="period_name" class="col-md-4 col-form-label text-right">Nombre</label>
                            <div class="col-md-8">
                                <input name="period_name" type="text" class="form-control" v-model="fields.period_name">
                            </div>
                        </div>
    
                        <div class="mb-3 row">
                            <label for="business_day" class="col-md-4 col-form-label text-right">Día laboral</label>
                            <div class="col-md-8">
                                <input name="business_day" type="text" class="form-control" v-model="fields.business_day">
                            </div>
                        </div>

                        <?php foreach ( $fields as $field ) : ?>
                            <div class="mb-3 row">
                                <label for="<?= $field ?>" class="col-md-4 col-form-label text-right"><?= str_replace('_', ' ', $field) ?></label>
                                <div class="col-md-8">
                                    <input
                                        name="<?= $field ?>" type="text" class="form-control"
                                        title="<?= str_replace('_', ' ', $field) ?>"
                                        value="<?= $row->$field ?>"
                                    >
                                </div>
                            </div>
                        <?php endforeach ?>
    
                        <div class="mb-3 row">
                            <div class="col-md-8 offset-md-4">
                                <button class="btn btn-primary w120p" type="submit">Guardar</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
    var period = <?= json_encode($row) ?>;
    period.type_id = '0<?= $row->type_id ?>';

// Vue Applicación
//-----------------------------------------------------------------------------
var editPlaceApp = new Vue({
    el: '#editPlaceApp',
    data: {
        loading: false,
        row_id: <?= $row->id ?>,
        fields: period,
        arrType: <?= json_encode($arr_type) ?>,
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('periodForm'))
            axios.post(URL_API + 'periods/save/' + this.row_id, formValues)
            .then(response => {
                console.log(response.data)
                if ( response.data.saved_id > 0 )
                {
                    toastr['success']('Datos actualizados')
                }
                this.loading = false
            }).catch(function(error) {console.log(error)})  
        }
    }
})
</script>