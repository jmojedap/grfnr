<div id="passwordApp" class="center_box_750">
    <div class="card" v-show="success == 0">
        <div class="card-body">
            <form accept-charset="utf-8" id="passwordForm" @submit.prevent="handleSubmit">
                <fieldset v-bind:disabled="loading">
                    <div class="mb-3 row">
                        <label for="current_password" class="col-md-5 col-form-label text-end">
                            <span class="float-right">Contraseña actual</span>
                        </label>
                        <div class="col-md-7">
                            <input
                                name="current_password" type="password" class="form-control"
                                title="Contraseña actual" required
                                v-bind:class="{'is-invalid': validation.current_password == 0 }" v-model="current_password" v-on:change="clearCurrentPassword"
                                >
                                <div class="invalid-feedback">Su contraseña actual es incorrecta</div>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                            <label for="password" class="col-md-5 col-form-label text-end">
                                <span class="float-right">Nueva contraseña</span>
                            </label>
                            <div class="col-md-7">
                                <input
                                    name="password" type="password" class="form-control"
                                    title="Al menos un número y una letra minúscula, y al menos 8 caractéres"
                                    pattern="(?=.*\d)(?=.*[a-z]).{8,}" required v-model="password" v-on:change="clearPassconf"
                                    >
                            </div>
                        </div>
                
                    <div class="mb-3 row">
                        <label for="passconf" class="col-md-5 col-form-label text-end">
                            <span class="float-right">Confirmar contraseña</span>
                        </label>
                        <div class="col-md-7">
                            <input
                                name="passconf" type="password" class="form-control" title="Confirme la nueva contraseña"
                                pattern="(?=.*\d)(?=.*[a-z]).{8,}" required
                                v-bind:class="{'is-invalid': validation.passwords_match == 0 }" v-model="passconf" v-on:change="checkMatch"
                                >
                            <div class="invalid-feedback">La contraseña de confirmación no coincide</div>
                        </div>
                    </div>
    
                    <div class="mb-3 row">
                        <div class="col-md-7 offset-md-5">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fa fa-spin fa-spinner" v-show="loading"></i>
                                Cambiar
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="alert alert-success" role="alert" v-show="success == 1">
        <i class="fa fa-check"></i> La contraseña fue cambiada exitosamente.
    </div>
</div>

<script>
// VueApp
//-----------------------------------------------------------------------------
var passwordApp = createApp({
    data(){
        return{
            loading: false,
            fields: {},
            current_password: '',
            password: '',
            passconf: '',
            validation: { current_password: -1, passwords_match: -1 },
            success: 0,
        }
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('passwordForm'))
            axios.post(URL_API + 'accounts/change_password/', formValues)
            .then(response => {
                this.validation = response.data.validation
                if ( response.data.status == 1)
                {
                    this.success = 1
                } else {
                    this.loading = false
                    this.clearForm()
                }
            })
            .catch(function(error) {console.log(error)})
        },
        checkMatch: function(){
            this.validation.passwords_match = -1
            if ( this.password != this.passconf && this.passconf.length > 0 ) {
                this.validation.passwords_match = 0
            } else {
                this.validation.passwords_match = 1
            }
        },
        clearPassconf: function(){
            this.passconf = ''
            this.checkMatch()
        },
        clearForm: function(){
            if ( this.validation.current_password == 0 ) this.current_password = ''  
            if ( this.validation.passwords_match == 0 ) {
                this.password = ''
                this.passconf = ''
            }
        },
        clearCurrentPassword: function(){
            this.validation.current_password = -1  
        },
    }
}).mount('#passwordApp')
</script>