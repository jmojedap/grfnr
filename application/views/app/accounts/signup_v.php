<?php $this->load->view('assets/recaptcha') ?>

<div id="signupApp">
    <div v-show="savedId == 0">
        <p class="only-lg">
            Crear nueva cuenta de usuario
        </p>

        <div class="text-center mb-2" v-show="loading == true">
            <i class="fa fa-spin fa-spinner fa-3x"></i>
        </div>

        <form id="signupForm" @submit.prevent="handleSubmit" v-show="loading == false">
            <!-- Campo para validación Google ReCaptcha V3 -->
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">

            <div class="mb-3">
                <label class="sr-only" for="display_name">Tu nombre</label>
                <input
                    class="form-control" name="display_name" v-model="fields.display_name"
                    title="Debe tener al menos cinco letras" placeholder="¿Cómo te llamas?"
                    minlength="5" required autofocus
                    >
            </div>

            <div class="mb-3">
                <label class="sr-only" for="email">Correo electrónico</label>
                <input
                    name="email" type="email" class="form-control" required
                    title="Correo electrónico" placeholder="Correo electrónico"
                    v-on:change="validateForm" v-model="fields.email"
                    v-bind:class="{'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1}"
                    >
                <div class="invalid-feedback" v-show="validation.email_unique == 0">
                    Ya existe una cuenta con este correo electrónico
                </div>
            </div>

            <div class="mb-3">
                <label class="sr-only" for="password">Contraseña</label>

                    <div class="input-group mb-3">
                        <input
                            name="new_password" v-bind:type="passwordType" v-model="fields.password"
                            class="form-control" placeholder="Elige tu contraseña"
                            required
                            pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                            title="8 caractéres o más, al menos un número y una letra minúscula"
                            >
                        <button class="btn btn-light" type="button" v-on:click="togglePassword">
                            <i class="far fa-eye-slash" v-show="passwordType == 'password'"></i>
                            <i class="far fa-eye" v-show="passwordType == 'text'"></i>
                        </button>
                    </div>
            </div>    
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary btn-lg w-100" v-bind:disabled="loading == true">Crear</button>
            </div>
        </form>
    </div>

    <!-- Sección si se registró exitosamente -->
    <div v-show="savedId > 0">
        <div class="text-center mb-2">
            <h1>
                <i class="fa fa-check text-success"></i><br/>
                Listo {{ fields.display_name }}
            </h1>
            <p>
                ¡Ya haces parte de <?= APP_NAME ?>!
            </p>
            <a href="<?= URL_APP . 'accounts/logged' ?>" class="btn btn-primary btn-lg">
                CONTINUAR <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <p>
        ¿Ya tienes una cuenta? <a href="<?= URL_APP . 'accounts/login_link' ?>">Ingresa</a>
    </p>
</div>

<script>
var signupApp = createApp({
    data(){
        return{
            fields:{
                display_name: '',
                email: '',
                password: ''
            },
            validated: -1,
            validation: {
                email_unique: -1
            },
            passwordType: 'password',
            loading: false,
            savedId: 0,
        }
    },
    methods: {
        handleSubmit: function(){
            if ( this.validated )
            {
                this.loading = true
                var payload = new FormData(document.getElementById('signupForm'))
                
                axios.post(URL_API + 'accounts/create/', payload)
                .then(response => {
                    this.loading = false
                    this.savedId = response.data.saved_id

                    if ( response.data.recaptcha == -1 ) {
                        toastr['error']('No se realizó la validación recaptcha')
                        setTimeout(() => {
                            window.location = URL_APP + 'accounts/signup'
                        }, 3000);
                    }
                })
                .catch(function (error) { console.log(error) })
            } else {
                toastr['error']('Revisa las casillas en rojo')
            }
        },
        validateForm: function(){
            var payload = new FormData()
            payload.append('email', this.fields.email)
            axios.post(URL_API + 'accounts/validate_signup/', payload)
            .then(response => {
                this.validation = response.data.validation
                this.validated = response.data.status
            })
            .catch(function (error) { console.log(error) })
        },
        togglePassword: function(){
            if ( this.passwordType == 'text' )
            {
                this.passwordType = 'password'
            } else {
                this.passwordType = 'text'
            }
        },
    },
    mounted(){
        //this.getList()
    }
}).mount('#signupApp')
</script>