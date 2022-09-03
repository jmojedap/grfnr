<style>
    .escena-grid {
        display: grid;
        grid-template-columns: 802px 1fr;
        gap: 0.5rem;
    }

    #escena_image{
        width: 800px;
        height: 653px;
        position: relative;
        border: 1px solid #ced4da;
        border-radius: 3px;
    }

    #escena_image div{
        font-size: 0.8em;
        position: absolute;
        border-radius: 3px;
        border: 1px solid rgba(0,0,0,0.1);
    }

    #escena_image div.active{
        border: 1px solid #00BEE0;
    }

    .emocion-select{
        border: 1px solid #FFF;
        border-radius: 3px;
        width: 100%;
    }

    #escena_image div span{
        cursor: pointer;
    }

    #escena_image .marker{
        display: inline-block;
        width: 2em;
        background-color: #fdd835;
        text-align: center;
    }

    #escena_image div .emocion{
        text-align: center;
        display: inline-block;
        padding-right: 0.3em;
        padding-left: 0.3em;
        min-width: 3rem;
    }

</style>

<div id="responderApp">
    <div>
        <div class="d-flex justify-content-between">
            <div class="text-center w-100">
                <h2>
                    {{ escena.title }}
                </h2>
            </div>
            <button class="btn btn-success w120p float-end" v-on:click="saveAnswer">
                GUARDAR
            </button>
        </div>
        <p class="lead text-center">Asígnale una emoción a cada personaje y escribe una historia sobre la escena</p>
    </div>
    <?php $this->load->view('app/escenas/responder/escena_v') ?>
</div>

<?php $this->load->view('app/escenas/responder/vue_v') ?>