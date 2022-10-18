<?php $this->load->view('assets/recaptcha') ?>

<div id="inicioApp">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="inicioForm" @submit.prevent="validateSubmit">
                    <input type="hidden" name="gender" v-model="fields.gender">
                    <!-- Campo para validación Google ReCaptcha V3 -->
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

                    <fieldset v-bind:disabled="loading">
                        <div class="mb-3 row">
                            <label for="organization_id" class="col-md-4 col-form-label text-end">Institución</label>
                            <div class="col-md-8">
                                <select name="organization_id" v-model="fields.organization_id" class="form-select" required>
                                    <option v-for="optionInstitucion in optionsInstitucion" v-bind:value="`0` + optionInstitucion.id">
                                        {{ optionInstitucion.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="school_level" class="col-md-4 col-form-label text-end">Grado escolar</label>
                            <div class="col-md-8">
                                <select name="school_level" v-model="fields.school_level" class="form-select" required>
                                    <option v-for="optionSchoolLevel in optionsSchoolLevel" v-bind:value="optionSchoolLevel.str_cod">
                                        {{ optionSchoolLevel.cod }} -
                                        {{ optionSchoolLevel.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="first_name" class="col-md-4 col-form-label text-end">Nombres</label>
                            <div class="col-md-8">
                                <input
                                    name="first_name" type="text" class="form-control"
                                    required
                                    title="Nombres" placeholder="Nombres"
                                    v-model="fields.first_name"
                                >
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="last_name" class="col-md-4 col-form-label text-end">Apellidos</label>
                            <div class="col-md-8">
                                <input
                                    name="last_name" type="text" class="form-control" required
                                    title="Apellidos" placeholder="Apellidos"
                                    v-model="fields.last_name"
                                >
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="birth_date" class="col-md-4 col-form-label text-end">Fecha de nacimiento</label>
                            <div class="col-md-8">
                                <input
                                    name="birth_date" type="date" class="form-control" required title="Fecha de nacimiento"
                                    v-model="fields.birth_date"
                                >
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="gender" class="col-md-4 col-form-label text-end"></label>
                            <div class="col-md-8">
                                <div class="d-flex">
                                    <button class="btn me-2" type="button"
                                        v-for="optionGender in optionsGender"  v-on:click="setGender(optionGender.cod)"
                                        v-bind:class="{'btn-primary': optionGender.cod == fields.gender }"
                                        >
                                        <i class="far fa-circle" v-show="optionGender.cod != fields.gender"></i>
                                        <i class="far fa-circle-check" v-show="optionGender.cod == fields.gender"></i>
                                        {{ optionGender.name }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    <div class="mb-3 row">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-success w-100" type="submit">
                                Iniciar
                            </button>
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
var userData = {
    organization_id: '01',
    first_name: 'Lina María',
    last_name: 'López',
    school_level: '06',
    gender: '',
}

/*var userData = {
    organization_id: '',
    first_name: '',
    last_name: '',
    school_level: '',
    gender: '',
}*/
// VueApp
//-----------------------------------------------------------------------------
var inicioApp = createApp({
    data(){
        return{
            loading: false,
            fields: userData,
            optionsInstitucion: <?= json_encode($optionsInstituciones) ?>,
            optionsSchoolLevel: <?= json_encode($optionsSchoolLevel) ?>,
            optionsGender: <?= json_encode($optionsGender) ?>,
        }
    },
    methods: {
        handleSubmit: function(){

            this.loading = true
            var formValues = new FormData(document.getElementById('inicioForm'))
            axios.post(URL_API + 'app/start_session/', formValues)
            .then(response => {
                if ( response.data.status == 1 ) {
                    toastr['success'](response.data.message)
                    window.location = URL_APP + 'escenas/catalogo'
                }
                if ( response.data.recaptcha != 1 ) {
                    this.reloadPage()
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        validateSubmit: function(){
            console.log(this.fields.gender)
            if ( this.fields.gender > 0 ) {
                this.handleSubmit()
            } else {
                toastr['error']('Seleccione el género')
            }
        },
        setGender: function(value){
            this.fields.gender = value
        },
        reloadPage: function(){
            toastr['info']('Se reiniciará la página...', 'ReCaptcha falló')
            setTimeout(() => {
                window.location = URL_APP + 'app/inicio'
            }, 3000);
        },
    },
    mounted(){
        //this.getList()
    }
}).mount('#inicioApp')
</script>