<div id="responderApp">
    <div class="d-flex justify-content-between">
        <h2>{{ escena.title }}</h2>
        <div>
            <button class="btn btn-success w120p" v-on:click="saveAnswer" v-show="respuesta.status != 1"
                data-bs-toggle="modal" data-bs-target="#savedModal"
            >
                GUARDAR
            </button>
            <a class="btn btn-primary w120p" v-show="respuesta.status == 1" href="<?= URL_APP . 'escenas/catalogo' ?>">
                <i class="fa fa-arrow-left"></i> Escenas
            </a>
            
        </div>
    </div>
    <p class="lead">Asígnale una emoción a cada personaje y escribe una historia sobre la escena</p>
    <p class="text-center"><span class="badge bg-success" v-show="respuesta.status == 1">Respuesta finalizada</span></p>
    <?php $this->load->view('app/escenas/responder/escena_v') ?>
    <?php $this->load->view('app/escenas/responder/saved_modal_v') ?>
</div>

<?php $this->load->view('app/escenas/responder/vue_v') ?>