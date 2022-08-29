<div id="editApp">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="editForm" accept-charset="utf-8" @submit.prevent="handleSubmit">
                <div class="mb-3 row">
                    <label for="display_name" class="col-md-4 col-form-label text-end">Nombre y Apellidos <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <input
                            name="display_name" class="form-control"
                            placeholder="Tu nombre"
                            required autofocus
                            v-model="fields.display_name"
                            >
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="email" class="col-md-4 col-form-label text-end">Correo electrónico <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <input
                            name="email" type="text" class="form-control"
                            v-bind:class="{ 'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1 }"
                            required
                            title="Correo electrónico" placeholder="Correo electrónico"
                            v-model="fields.email" v-on:change="validateForm"
                        >
                        <span class="invalid-feedback">
                            El correo electrónico escrito ya fue registrado por otro usuario
                        </span>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="document_number" class="col-md-4 col-form-label text-end">No. Documento</label>
                    <div class="col-md-4">
                        <input
                            name="document_number" class="form-control"
                            v-bind:class="{ 'is-invalid': validation.document_number_unique == 0, 'is-valid': validation.document_number_unique == 1 }"
                            placeholder="Número de documento"
                            title="Solo números, sin puntos, debe tener al menos 5 dígitos"
                            pattern=".{5,}[0-9]" v-model="fields.document_number"
                            v-on:change="validateForm"
                            >
                        <span class="invalid-feedback">
                            El número de documento escrito ya fue registrado para otro usuario
                        </span>
                    </div>
                    <div class="col-md-4">
                        <select name="document_type" v-model="fields.document_type" class="form-select" required>
                            <option v-for="(option_document_type, key_document_type) in options_document_type" v-bind:value="key_document_type">{{ option_document_type }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="city_id" class="col-md-4 col-form-label text-end">Ciudad residencia</label>
                    <div class="col-md-8">
                        <select name="city_id" v-model="fields.city_id" class="form-select">
                            <option v-for="(option_city_id, key_city_id) in options_city_id" v-bind:value="key_city_id">{{ option_city_id }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="birth_date" class="col-md-4 col-form-label text-end">Fecha de nacimiento</label>
                    <div class="col-md-8">
                        <input name="birth_date" class="form-control" type="date" v-model="fields.birth_date">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="gender" class="col-md-4 col-form-label text-end">Género <span class="text-danger">*</span></label>
                    <div class="col-md-8">
                        <select name="gender" v-model="fields.gender" class="form-select" required>
                            <option v-for="(option_gender, key_gender) in options_gender" v-bind:value="key_gender">{{ option_gender }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="offset-md-4 col-md-8">
                        <button class="btn btn-primary w120p" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var fields = {
    display_name: '<?= $row->display_name ?>',
    email: '<?= $row->email ?>',
    document_number: '<?= $row->document_number ?>',
    document_type: '0<?= $row->document_type ?>',
    city_id: '0<?= $row->city_id ?>',
    birth_date: '<?= $row->birth_date ?>',
    gender: '0<?= $row->gender ?>',
    phone_number: '<?= $row->phone_number ?>',
};

// VueApp
//-----------------------------------------------------------------------------
var editApp = createApp({
    data(){
        return{
            loading: false,
            fields: fields,
            row_id: '<?= $row->id ?>',
            validated: -1,
            validation: {
                email_unique: -1,
                document_number_unique: -1
            },
            options_city_id: <?= json_encode($options_city_id) ?>,
            options_document_type: <?= json_encode($options_document_type) ?>,
            options_gender: <?= json_encode($options_gender) ?>,
            options_privacy: <?= json_encode($options_privacy) ?>,
        }
    },
    methods: {
        validateForm: function() {
            var formValues = new FormData(document.getElementById('editForm'))
            axios.post(URL_API + 'accounts/validate_form/', formValues)
            .then(response => {
                this.validated = response.data.status
                this.validation = response.data.validation;
            })
            .catch(function (error) { console.log(error) })
        },
        handleSubmit: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('editForm'))
            axios.post(URL_API + 'accounts/update/', formValues)
            .then(response => {   
                this.loading = false 
                if (response.data.saved_id > 0) toastr['success']('Guardado');
            })
            .catch(function (error) { console.log(error) })
        },
    }
}).mount('#editApp')
</script>