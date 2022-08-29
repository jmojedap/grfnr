<div id="pictureApp" class="center_box_450">
    <div class="card">
        <div class="card-body">
            <div v-show="row.image_id > 0">
                <div class="d-flex justify-content-between">
                    <a class="btn btn-light me-2" id="btn_crop" href="<?= URL_APP . "accounts/edit/cropping" ?>">
                        <i class="fa fa-crop"></i> Recortar
                    </a>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div v-show="row.image_id == 0">
                <?php $this->load->view('common/bs5/upload_file_form_v') ?>
            </div>
        </div>
        <img
            v-bind:src="row.url_image" class="card-img-top" alt="user picture"
            onerror="this.src='<?= URL_IMG ?>users/user.png'"
        >
    </div>
    <?php $this->load->view('common/bs5/modal_single_delete_v') ?>
</div>

<script>
// VueApp
//-----------------------------------------------------------------------------
var pictureApp = createApp({
    data(){
        return{
            loading: false,
            file: null,
            row: {
                id: <?= $row->id ?>,
                image_id: <?= $row->image_id ?>,
                url_image: '<?= $row->url_image ?>'
            },
            defaultImage: '<?= URL_IMG ?>users/user.png'
        }
    },
    methods: {
        handleSubmit: function(){
            this.loading = true
            let form_data = new FormData();
            form_data.append('file_field', this.file);
            form_data.append('table_id', '1000');
            form_data.append('related_1', this.row.id);

            axios.post(URL_API + 'accounts/set_image/', form_data, {headers: {'Content-Type': 'multipart/form-data'}})
            .then(response => {
                //Cargar imagen
                if ( response.data.status == 1 )
                { 
                    this.row.image_id = response.data.image_id;
                    this.row.url_image = response.data.url_image;
                    //Limpiar formulario
                }
                //Mostrar respuesta html, si existe
                if ( response.data.html ) { 
                    $('#upload_response').html(response.data.html)
                }
                document.getElementById('field-file').value = null
            }).catch(function (error) { console.log(error) })
        },
        handleFileUpload(){
            this.file = this.$refs.file_field.files[0]
        },
        deleteElement: function(){
            this.loading = true
            axios.get(URL_API + 'accounts/remove_image/')
            .then(response => {
                if ( response.data.status == 1 ) {
                    this.row.image_id = 0;
                    this.row.url_image = this.defaultImage;
                    toastr['info']('Imagen eliminada');
                }
                this.loading = false
            }).catch(function (error) { console.log(error)})
        },
    }
}).mount('#pictureApp')
</script>