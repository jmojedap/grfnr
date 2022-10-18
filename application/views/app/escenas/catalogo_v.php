<div id="catalogoApp">
    <h2 class="mb-2">Responde las escenas</h2>
    <div class="grid-columns-15rem">
        <div class="card" v-for="(escena, key) in list">
            <img v-bind:src="escena.url_thumbnail" class="card-img-top pointer" alt="Imagen de la escena"
                data-bs-toggle="modal" data-bs-target="#escenaModal" v-on:click="setCurrent(key)"
                onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'">
            <div class="card-body">
                {{ escena.title }}
                <div v-show="escena.respuesta_status > 0">
                    <span class="badge bg-success" v-show="escena.respuesta_status == 1">Respondido</span>
                    <span class="badge bg-info" v-show="escena.respuesta_status == 2">Iniciado</span>
                </div>
            </div>
        </div>
    </div>
    <form accept-charset="utf-8" method="POST" id="searchForm" @submit.prevent="getList">
    </form>
    <!-- Modal -->
    <div class="modal fade" id="escenaModal" tabindex="-1" aria-labelledby="escenaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="escenaModalLabel">{{ currEscena.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <img v-bind:src="currEscena.url_thumbnail" class="card-img-top pointer" alt="Imagen de la escena"
                    onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn w120p btn-primary"
                        v-on:click="startAnswer" v-show="currEscena.respuesta_id == null"
                    >Iniciar</button>
                    <button type="button" class="btn w120p btn-primary"
                        v-on:click="openAnswer" v-show="currEscena.respuesta_id != null"
                    >Abrir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var catalogoApp = createApp({
    data() {
        return {
            list: [],
            currEscena: {
                id: 0, title: ''
            },
            loading: false,
            filters: {
                q: ''
            },
        }
    },
    methods: {
        getList: function() {
            this.loading = true
            var formValues = new FormData(document.getElementById('searchForm'))
            axios.post(URL_API + 'escenas/get_mis_escenas/', formValues)
                .then(response => {
                    this.list = response.data.escenas
                    this.loading = false
                })
                .catch(function(error) { console.log(error) })
        },
        setCurrent: function(key) {
            this.currEscena = this.list[key]
        },
        openAnswer: function(){
            window.location = URL_APP + 'escenas/responder/' + this.currEscena.id + '/' + this.currEscena.respuesta_id
        },
        startAnswer: function() {
            this.loading = true
            axios.get(URL_API + 'respuestas/iniciar/' + this.currEscena.id)
            .then(response => {
                var respuestaId = response.data.saved_id
                if ( respuestaId > 0 ) {
                    toastr['info']('Cargando escena...')
                    setTimeout(() => {
                        window.location = URL_APP + 'escenas/responder/' + this.currEscena.id + '/' + respuestaId
                    }, 500);
                } else {
                    this.loading = false
                }
            })
            .catch(function(error) { console.log(error) })
        },
    },
    mounted() {
        this.getList()
    }
}).mount('#catalogoApp')
</script>