<?php
    $city_name = $this->App_model->place_name($respuesta->place_id, 'full_name');
    $institution = $this->Db_model->row_id('posts', $user->organization_id);
?>

<script>
// Variables
//-----------------------------------------------------------------------------
const arrGenero = <?= json_encode($arrGenero) ?>;
const arrGrupoEdad = <?= json_encode($arrGrupoEdad) ?>;
const arrEmocion = <?= json_encode($arrEmocion) ?>;
const escenaImage = document.getElementById("escena_image");
const narracionMinLength = 50;

// VueApp
//-----------------------------------------------------------------------------
var respuestaApp = new Vue({
    el: '#respuestaApp',
    created: function(){
        this.checkStatus()
    },
    data: {
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
            city_name: '<?= $city_name ?>',
            age: '<?= $respuesta->integer_1 ?>',
            escena_id: '<?= $respuesta->related_2 ?>',
        },
        user: {
            username: '<?= $user->username ?>',
            gender: <?= $user->gender ?>,
            institution: '<?= $institution->post_name ?>',
        },
        section: 'respuesta',
        savingStatus: 0
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
})
</script>