<div id="catalogoApp">
    <h2 class="mb-2">Responde las escenas</h2>
    <div class="grid-columns-15rem">
        <div class="card" v-for="escena in list">
            <a v-bind:href="`<?= URL_APP . "escenas/responder/" ?>` + escena.id + `/` + escena.slug">
                <img src="<?= URL_IMG ?>app/nd.png" class="card-img-top" alt="Imagen de la escena">
            </a>
            <div class="card-body">
                <a v-bind:href="`<?= URL_APP . "escenas/responder/" ?>` + escena.id + `/` + escena.slug">
                    {{ escena.title }}
                </a>
                </div>
            </div>
    </div>
    <form accept-charset="utf-8" method="POST" id="searchForm" @submit.prevent="handleSubmit">
    </form>
</div>

<script>
var catalogoApp = createApp({
    data(){
        return{
            list: [],
            loading: false,
            filters: {q:''},
        }
    },
    methods: {
        getList: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('searchForm'))
            axios.post(URL_APP + 'escenas/get/', formValues)
            .then(response => {
                this.list = response.data.list
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
    },
    mounted(){
        this.getList()
    }
}).mount('#catalogoApp')
</script>