<div id="recoverApp">
    <div class="mt-2" v-show="user_id > 0">
        <h4 class="white"><?= $row->display_name ?></h4>
        <p class="text-muted">
            <i class="fa fa-user"></i>
            <?= $row->username ?>
        </p>
        <p>Establece tu nueva contraseña en <?= APP_NAME ?></p>
    </div>

    <div v-show="user_id > 0">
        <form id="recoverForm" method="post" accept-charset="utf-8" @submit.prevent="handleSubmit">
            <fieldset v-bind:disabled="loading">
                <div class="mb-3">
                    <input
                        name="password" type="password"
                        class="form-control" 
                        placeholder="contrase&ntilde;a" title="Debe tener un número y una letra minúscula, y al menos 8 caractéres"
                        required autofocus pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                        >
                </div>
                <div class="mb-3">
                    <input
                        name="passconf" type="password"
                        class="form-control" placeholder="confirma tu contrase&ntilde;a" title="passconf contrase&ntilde;a"
                        required
                        >
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fa fa-spin fa-spinner" v-show="loading"></i>
                        Guardar
                    </button>
                </div>
            <fieldset>
        </form>
    
        
    </div>

    <div class="alert alert-danger" v-show="user_id == 0">
        Usuario no identificado con código: <strong>{{ activation_key }}</strong>
    </div>

    <div class="alert alert-danger" v-show="errors.length">
        {{ errors }}
    </div>
    
</div>

<script>
var recoverApp = createApp({
    data(){
        return{
            loading: false,
            user_id: <?= $user_id ?>,
            activation_key: '<?= $activation_key ?>',
            hide_message: true,
            errors: '',
        }
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('recoverForm'))
            axios.post(URL_API + 'accounts/reset_password/' + this.activation_key, formValues)
            .then(response => {
                this.errors = response.data.errors
                if ( response.data.status == 1 ) {
                    toastr['success']('Tu contraseña fue cambiada exitosamente');
                    setTimeout(function(){
                        window.location = URL_APP + 'accounts/logged' },
                        3000
                    );
                } else {
                    this.loading = false
                }
            })
            .catch(function (error) { console.log(error)})
        }
    },
}).mount('#recoverApp')
</script>