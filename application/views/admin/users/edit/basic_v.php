<?php
    $options_city = $this->App_model->options_place('type_id = 4', 'cr', 'Ciudad');
    $options_document_type = $this->Item_model->options('category_id = 53', 'Tipo documento');
?>

<div id="editUserApp">
    <div class="card center_box_750">
        <div class="card-body">
            <form id="userForm" accept-charset="utf-8" @submit.prevent="validateAndSubmit">
                <fieldset v-bind:disabled="loading">
                    <input type="hidden" name="id" value="<?= $row->id ?>">
                    <div class="mb-3 row">
                        <label for="role" class="col-md-4 col-form-label text-right">Rol <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select name="role" v-model="fields.role" class="form-control" required>
                                <option v-for="(option_role, key_role) in options_role" v-bind:value="key_role">{{ option_role }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="first_name" class="col-md-4 col-form-label text-right">Nombre y Apellidos</label>
                        <div class="col-md-4">
                            <input
                                name="first_name" class="form-control"
                                placeholder="Nombres" title="Nombres del usuario"
                                v-model="fields.first_name"
                                >
                        </div>
                        <div class="col-md-4">
                            <input
                                name="last_name" class="form-control"
                                placeholder="Apellidos" title="Apellidos del usuario"
                                v-model="fields.last_name"
                                >
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="display_name" class="col-md-4 col-form-label text-right">Nombre mostrar <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input
                                name="display_name" required class="form-control"
                                placeholder="Nombre mostrar" title="Nombre mostrar"
                                v-model="fields.display_name"
                                >
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-md-4 col-form-label text-right">Correo electrónico <span class="text-danger">*</span></label>
                            
                        <div class="col-md-8">
                            <input
                                name="email" class="form-control"
                                placeholder="Correo electrónico" title="Correo electrónico"
                                v-bind:class="{ 'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1 }"
                                v-model="fields.email"
                                v-on:change="validateForm"
                                >
                            <span class="invalid-feedback">
                                El correo electrónico ya fue registrado, por favor escriba otro
                            </span>
                        </div>
                    </div>

                    <div class="mb-3 row" id="mb-3_username">
                        <label for="username" class="col-md-4 col-form-label text-right">Username <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <!-- /btn-group -->
                                <input
                                    id="field-username" name="username" class="form-control"
                                    placeholder="username" title="Puede contener letras y números, entre 6 y 25 caractéres, no debe contener espacios ni caracteres especiales"
                                    required pattern="^[A-Za-z0-9_.]{6,25}$"
                                    v-bind:class="{ 'is-invalid': validation.username_unique == 0, 'is-valid': validation.username_unique == 1 }"
                                    v-model="fields.username"
                                    v-on:change="validateForm"
                                    >
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" title="Generar username" v-on:click="generateUsername">
                                        <i class="fa fa-magic"></i>
                                    </button>
                                </div>
                                
                                <span class="invalid-feedback">
                                    El username escrito no está disponible, por favor elija otro
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row" id="mb-3_document_number">
                        <label for="document_number" class="col-md-4 col-form-label text-right">No. Documento</label>
                        <div class="col-md-4">
                            <input
                                name="document_number" class="form-control"
                                title="Solo números, sin puntos, debe tener al menos 5 dígitos"
                                pattern=".{5,}[0-9]"
                                v-bind:class="{ 'is-invalid': validation.document_number_unique == 0, 'is-valid': validation.document_number_unique == 1 && fields.document_number > 0 }"
                                v-model="fields.document_number"
                                v-on:change="validateForm"
                                >
                            <span class="invalid-feedback">
                                El número de documento escrito ya fue registrado para otro usuario
                            </span>
                        </div>
                        <div class="col-md-4">
                            <select name="document_type" v-model="fields.document_type" class="form-control">
                                <option v-for="(option_document_type, key_document_type) in options_document_type" v-bind:value="key_document_type">{{ option_document_type }}</option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="mb-3 row">
                        <label for="gender" class="col-md-4 col-form-label text-right">Género</label>
                        <div class="col-md-8">
                            <select name="gender" v-model="fields.gender" class="form-control" required>
                                <option v-for="(option_gender, key_gender) in options_gender" v-bind:value="key_gender">{{ option_gender }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="birth_date" class="col-md-4 col-form-label text-right">Fecha de nacimiento</label>
                        <div class="col-md-8">
                            <input
                            name="birth_date" class="form-control" type="date"
                            v-model="fields.birth_date"
                            >
                        </div>
                    </div>
                    
                    <hr>

                    <div class="mb-3 row">
                        <label for="city_id" class="col-md-4 col-form-label text-right">Ciudad residencia</label>
                        <div class="col-md-8">
                            <select name="city_id" v-model="fields.city_id" class="form-control form-control-chosen">
                                <option v-for="(option_city, key_city) in options_city" v-bind:value="key_city">{{ option_city }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="address" class="col-md-4 col-form-label text-right">Dirección</label>
                        <div class="col-md-8">
                            <input
                                name="address" type="text" class="form-control" title="Dirección"
                                v-model="fields.address"
                            >
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="celular" class="col-md-4 col-form-label text-right">No. celular</label>
                        <div class="col-md-8">
                            <input
                                name="phone_number" class="form-control" title="Número celular"
                                v-model="fields.phone_number"
                                >
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="admin_notes" class="col-md-4 col-form-label text-right">Notas internas</label>
                        <div class="col-md-8">
                            <textarea
                                name="admin_notes" class="form-control"
                                title="Notas internas"
                                v-model="fields.admin_notes"
                                ></textarea>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-primary w120p" type="submit">Guardar</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var fields = {
    first_name: '<?= $row->first_name ?>',
    last_name: '<?= $row->last_name ?>',
    display_name: '<?= $row->display_name ?>',
    username: '<?= $row->username ?>',
    email: '<?= $row->email ?>',
    role: '0<?= $row->role ?>',
    document_number: '<?= $row->document_number ?>',
    document_type: '0<?= $row->document_type ?>',
    city_id: '0<?= $row->city_id ?>',
    birth_date: '<?= $row->birth_date ?>',
    gender: '0<?= $row->gender ?>',
    address: '<?= $row->address ?>',
    phone_number: '<?= $row->phone_number ?>',
    admin_notes: '<?= $row->admin_notes ?>',
};

// Vue App
//-----------------------------------------------------------------------------
var editUserApp = new Vue({
    el: '#editUserApp',
    data: {
        loading: false,
        fields: fields,
        rowId: '<?= $row->id ?>',
        validation: {
            document_number_unique: -1,
            username_unique: -1,
            email_unique: -1
        },
        options_role: <?= json_encode($options_role) ?>,
        options_city: <?= json_encode($options_city) ?>,
        options_gender: <?= json_encode($options_gender) ?>,
        options_document_type: <?= json_encode($options_document_type) ?>,
    },
    methods: {
        validateForm: function() {
            var formValues = new FormData(document.getElementById('userForm'))
            axios.post(URL_APP + 'users/validate/' + this.rowId, formValues)
            .then(response => {
                this.validation = response.data.validation
            })
            .catch(function (error) { console.log(error) })
        },
        validateAndSubmit: function () {
            axios.post(URL_APP + 'users/validate/' + this.rowId, $('#userForm').serialize())
            .then(response => {
                if (response.data.status == 1) {
                    this.handleSubmit()
                } else {
                    toastr['error']('Hay casillas incompletas o incorrectas')
                    this.loading = false
                }
            })
            .catch(function (error) { console.log(error) })
        },
        handleSubmit: function() {
            this.loading = true
            axios.post(URL_APP + 'users/save/', $('#userForm').serialize())
            .then(response => {
                if (response.data.saved_id > 0) toastr['success']('Guardado')
                this.loading = false
            })
            .catch(function (error) { console.log(error) })
        },
        generateUsername: function() {
            const formValues = new FormData();
            formValues.append('email', this.fields.email)
            
            axios.post(URL_APP + 'users/username/', formValues)
            .then(response => {
                this.fields.username = response.data
            })
            .catch(function (error) { console.log(error) })
        }
    }
});
</script>