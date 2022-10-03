<style>
    #escena_image{
        width: 800px;
        height: 662px;
        position: absolute;
    }

    #escena_image .marker{
        display: inline-block;
        background-color: #11c26d;
        color: #FFF;
        width: 2em;
        text-align: center;
        border-radius: 0.5em;
        position: absolute;
        font-size: 0.9em;
    }

    #escena_image .marker.active{
        background-color: red;
    }


</style>

<div id="personajesApp">
    <div class="row" style="min-height: 670px;">
        <div class="col-md-8">
            <div id="escena_image" class="border"
                v-bind:style="`background-image: url(` + escena.url_image + `);`"
                v-on:click="setPosition"
                >
                <div class="marker" v-for="(personaje, key) in list"
                    v-bind:style="`top: ` + personaje.top + `px; left: ` + personaje.left + `px;`"
                    v-bind:class="{'active': personaje.id == currPersonaje.id }"
                >
                    {{ personaje.code_ucc }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <table class="table bg-white">
                <thead>
                    <th></th>
                    <th width="10px">Cód.</th>
                    <th width="10px">ID</th>
                    <th>Personaje</th>
                    <th>left</th>
                    <th>top</th>
                </thead>
                <tbody>
                    <tr v-for="(personaje, key) in list" v-bind:class="{'table-warning': personaje.id == currPersonaje.id }">
                        <td>
                            <button class="a4" v-on:click="setCurrent(key)">
                                <i class="far fa-circle"></i>
                            </button>
                        </td>
                        <td>{{ personaje.id }}</td>
                        <td>{{ personaje.code_ucc }}</td>
                        <td>
                            {{ personaje.nombre }}
                            <br>
                            {{ generoName(personaje.cod_genero) }}
                            &middot;
                            {{ grupoEdadName(personaje.cod_grupo_edad) }}
                        </td>
                        <td>{{ personaje.left }}</td>
                        <td>{{ personaje.top }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Variables
//-----------------------------------------------------------------------------
var arrGenero = <?= json_encode($arrGenero) ?>;
var arrGrupoEdad = <?= json_encode($arrGrupoEdad) ?>;
const escenaImage = document.getElementById("escena_image");

// VueApp
//-----------------------------------------------------------------------------
var personajesApp = new Vue({
    el: '#personajesApp',
    created: function(){
        //this.get_list()
    },
    data: {
        escena: <?= json_encode($row) ?>,
        list: <?= json_encode($personajes->result()) ?>,
        currKey: -1,
        currPersonaje: {id:0},
        arrGenero: arrGenero,
        arrGrupoEdad: arrGrupoEdad,
        loading: false,
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
            this.currPersonaje = this.list[key]
        },
        setPosition: function(e){
            var offset = this.getSceneOffset()
            console.log(escenaImage)
            console.log(e.clientX)
            key = this.currKey
            this.list[key].top = e.clientY - 110
            this.list[key].left = e.clientX - 245
            this.updateCharacter(key)
        },
        getSceneOffset() {
            var p = $('#escena_image');
            var position = p.position();
            return position
        },
        updateCharacter: function(key){
            this.loading = true
            var formValues = new FormData()
            formValues.append('id',this.list[key].id)
            formValues.append('integer_1',this.list[key].left)
            formValues.append('integer_2',this.list[key].top)
            axios.post(URL_API + 'posts/save/', formValues)
            .then(response => {
                if ( response.data.saved_id > 0 ) {
                    toastr['success']('Posición actualizada')
                }
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    }
})
</script>