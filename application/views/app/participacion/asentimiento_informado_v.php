<div id="asentimientoApp">
    <div class="center_box_750">
        <div class="card center_box_750 mb-2">
            <div class="card-body">
                <div v-show="currentFile.id == 0">
                    <h3>Asentimiento informado</h3>
                    <p class="lead">
                        Para participar en <?= APP_NAME ?> carga aquí el archivo con el
                        asentimiento informado.
                    </p>
                    <?php $this->load->view('common/bs5/upload_file_form_v') ?>
                </div>
                <div v-show="currentFile.id > 0">
                    <p class="lead text-center"><i class="fa fa-check text-success"></i> Ya cargaste tu consentimiento informado</p>
                    <div v-show="parseInt(currentFile.is_image) == 1">
                        <img
                            v-bind:src="currentFile.url"
                            class="w-100"
                            v-bind:alt="currentFile.file_name"
                            onerror="this.src='<?= URL_IMG ?>app/sm_nd_square.png'"
                        >
                    </div>
                    <div v-show="parseInt(currentFile.is_image) == 0">
                        Ver archivo:
                        <a v-bind:href="currentFile.url" target="_blank" class="text-primary">
                            {{ currentFile.title }}
                        </a> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var asentimientoApp = createApp({
    data(){
        return{
            loading: false,
            file: null,
            userId: '<?= $this->session->userdata('user_id') ?>',
            files: [],
            currentFile: {id:0,is_image:0},
        }
    },
    methods: {
        getList: function(){
            this.loading = true
            var formValues = new FormData()
            formValues.append('fe3',10)
            formValues.append('sf','asentimiento')
            axios.post(URL_API + 'files/get/', formValues)
            .then(response => {
                this.files = response.data.list
                if ( this.files.length > 0 ) this.currentFile = this.files[0]
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        handleSubmit: function(){
            this.loading = true
            let formValues = new FormData();
            formValues.append('file_field', this.file)
            formValues.append('table_id', '1000')
            formValues.append('related_1', this.userId)
            formValues.append('album_id', 10)   //Asentimiento informado
            formValues.append('description', 'Asentimiento informado')

            axios.post(URL_API + 'files/upload/', formValues, {headers: {'Content-Type': 'multipart/form-data'}})
            .then(response => {
                //Cargar imágenes
                if ( response.data.status == 1 ) {
                    this.getList()
                    //Limpiar formulario
                    document.getElementById('field-file').value = null
                    this.file = null
                }
                //Mostrar respuesta html, si existe
                if ( response.data.html ) { $('#upload_response').html(response.data.html); }
                this.loading = false
            })
            .catch(function (error) { console.log(error) })
        },
        handleFileUpload(){
            this.file = this.$refs.file_field.files[0]
        },
        deleteElement: function(){
            var file_id = this.currentImage.id
            axios.get(URL_API + 'files/delete/' + file_id)
            .then(response => {
                this.getList()
            })
            .catch(function (error) { console.log(error) })
        },
    },
    mounted(){
        this.getList()
    }
}).mount('#asentimientoApp')
</script>