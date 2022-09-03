<div id="catalogoApp">
    <h2 class="mb-2">Responde las escenas</h2>
    <div class="grid-columns-15rem">
        <div class="card" v-for="(escena, key) in list">
            <img v-bind:src="escena.url_thumbnail" class="card-img-top pointer" alt="Imagen de la escena"
                data-bs-toggle="modal" data-bs-target="#escenaModal" v-on:click="setCurrent(key)"
                onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'">
            <div class="card-body">
                {{ escena.title }}
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
                    <button type="button" class="btn w120p btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn w120p btn-primary" v-on:click="startAnswer">Iniciar</button>
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
            axios.post(URL_API + 'escenas/get/', formValues)
                .then(response => {
                    this.list = response.data.list
                    this.loading = false
                })
                .catch(function(error) {
                    console.log(error)
                })
        },
        setCurrent: function(key) {
            this.currEscena = this.list[key]
        },
        startAnswer: function() {
            this.loading = true
            axios.get(URL_APP + 'escenas/iniciar_respuesta/' + this.currEscena.id)
            .then(response => {
                var respuesta_id = response.data.saved_id
                if ( respuesta_id > 0 ) {
                    toastr['info']('Cargando escena...')
                    setTimeout(() => {
                        window.location = URL_APP + 'escenas/responder/' + this.currEscena.id + '/' + respuesta_id
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