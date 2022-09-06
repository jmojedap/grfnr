<script>
// Variables
//-----------------------------------------------------------------------------
const arrGenero = <?= json_encode($arrGenero) ?>;
const arrGrupoEdad = <?= json_encode($arrGrupoEdad) ?>;
const arrEmocion = <?= json_encode($arrEmocion) ?>;
const escenaImage = document.getElementById("escena_image");
const narracionMinLength = 50;

// VueApp
var responderApp = createApp({
    data(){
        return{
            escena: <?= json_encode($escena) ?>,
            personajes: <?= json_encode($personajes->result()) ?>,
            currKey: -1,
            currPersonaje: {id:0},
            arrGenero: arrGenero,
            arrGrupoEdad: arrGrupoEdad,
            arrEmocion: arrEmocion,
            personajesEmociones: <?= $respuesta->content_json ?>,
            loading: false,
            respuesta: {
                id: <?= $respuesta->id ?>,
                status: <?= $respuesta->status ?>,
                content: <?= json_encode($respuesta->content) ?>,
            },
            section: 'emociones',
            savingStatus: 0
        }
    },
    computed: {
        //Verificar si los datos de respuesta están completos
        qtyWithoutEmotion: function(){
            withoutEmotion = this.personajesEmociones.filter(item => item.feeling_cod == 0)
            return withoutEmotion.length
        },
        completedNarracion: function(){
            var completedNarracion = false
            if ( this.respuesta.content.length > narracionMinLength ) {
                completedNarracion = true
            }
            return completedNarracion
        },
        completedAnswer: function(){
            var completedAnswer = true
            if ( this.qtyWithoutEmotion > 0 ) completedAnswer = false
            if ( ! this.completedNarracion ) completedAnswer = false
            return completedAnswer
        },
    },
    methods: {
        checkStatus: function(){
            if ( this.respuesta.status == 1 ) {
                this.loading = true
            }
        },
        generoName: function(value = '', field = 'name'){
            var generoName = ''
            var item = arrGenero.find(row => row.cod == value)
            if ( item != undefined ) generoName = item[field]
            return generoName
        },
        grupoEdadName: function(value = '', field = 'name'){
            var grupoEdadName = ''
            var item = arrGrupoEdad.find(row => row.cod == value)
            if ( item != undefined ) grupoEdadName = item[field]
            return grupoEdadName
        },
        setCurrent: function(key){
            this.currKey = key
            this.currPersonaje = this.personajes[key]
        },
        getSceneOffset() {
            var p = $('#escena_image');
            var position = p.position();
            return position
        },
        saveAnswer: function(){
            this.loading = true
            this.savingStatus = 0
            var formValues = new FormData()
            formValues.append('content', this.respuesta.content)
            formValues.append('content_json', JSON.stringify(this.personajesEmociones))
            axios.post(URL_API + 'escenas/guardar_respuesta/' + this.escena.id + '/' + this.respuesta.id, formValues)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.escena.status = response.data.respuesta_status
                    this.savingStatus = 1
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        finalizeAnswer: function(){
            this.loading = true
            axios.get(URL_API + 'escenas/finalizar_respuesta/' + this.escena.id + '/' + this.respuesta.id)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    this.respuesta.status = 1
                    toastr['success'](response.data.message)
                } else {
                    this.loading = false
                }
            })
            .catch(function(error) { console.log(error) })
        },
        setSection: function(value){
            this.section = value
        },
        emocionName: function(value = '', field = 'name'){
            var emocionName = ''
            var item = arrEmocion.find(row => row.cod == parseInt(value))
            if ( item != undefined ) emocionName = item[field]
            return emocionName
        },
    },
    mounted(){
        this.checkStatus()
    }
}).mount('#responderApp')
</script>