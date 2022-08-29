<div id="inicioApp">
    <div class="center_box_750">
        <div class="card">
            <div class="card-body">
                <form accept-charset="utf-8" method="POST" id="inicioForm" @submit.prevent="handleSubmit">
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
                                    <button class="btn me-2 w120p" type="button"
                                        v-for="optionGender in optionsGender"  v-on:click="setGender(optionGender.cod)"
                                        v-bind:class="{'btn-success': optionGender.cod == fields.gender }"
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
var inicioApp = createApp({
    data(){
        return{
            loading: false,
            fields: {
                organization_id: '01',
                first_name: 'Javier',
                last_name: 'Ojeda',
                school_level: '06',
                gender: '2',
            },
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
                toastr['success'](response.data.message)
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        setGender: function(value){
            this.fields.gender = value
        },
    },
    mounted(){
        //this.getList()
    }
}).mount('#inicioApp')
</script>