<?php
    $period_id = date('Ymd');
?>

<div id="summary_app">
    <div class="center_box_750">
        <div class="my-2">
            <a class="btn btn-light w120p" href="<?= URL_APP ?>">
                <i class="fa fa-home"></i> INICIO
            </a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <a href="<?= URL_ADMIN . "users/explore" ?>">
                    <div class="card mb-2">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-1">{{ summary.users.num_rows }}</h3>
                                        <span>Usuarios</span>
                                        <p class="text-muted">Usuarios registrados en la aplicación</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="fa fa-users fa-3x float-right color-text-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>            
            
            <div class="col-md-6">
                <div class="card mb-2">
                    <a href="<?= URL_ADMIN . "posts/explore" ?>">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left w-100">
                                        <h3 class="text-color-2">{{ summary.posts.num_rows }}</h3>
                                        <span>Publicaciones</span>
                                        <p class="text-muted">Publicaciones de usuarios y grafinar</p>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="far fa-file-alt fa-3x float-right color-text-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>    
</div>

<script>
var summary_app = new Vue({
    el: '#summary_app',
    created: function(){
        //this.get_list()
    },
    data: {
        summary: <?= json_encode($summary) ?>,
        loading: false,
    },
    methods: {
        
    }
})
</script>