<script>
// Variables
//-----------------------------------------------------------------------------
const arrGenero = <?= json_encode($arrGenero) ?>;
const arrGrupoEdad = <?= json_encode($arrGrupoEdad) ?>;
const arrEmocion = <?= json_encode($arrEmocion) ?>;
const escenaImage = document.getElementById("escena_image");

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
                content: <?= json_encode($respuesta->content) ?>,
            },
            section: 'emociones',
        }
    },
    methods: {
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
            var formValues = new FormData()
            formValues.append('content', this.respuesta.content)
            formValues.append('content_json', JSON.stringify(this.personajesEmociones))
            axios.post(URL_APP + 'escenas/guardar_respuesta/' + this.escena.id + '/' + this.respuesta.id, formValues)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Guardado')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
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
        //this.getList()
    }
}).mount('#responderApp')
</script>