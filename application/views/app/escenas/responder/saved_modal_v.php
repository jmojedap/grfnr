<!-- Modal -->
<div class="modal fade" id="savedModal" tabindex="-1" aria-labelledby="savedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="savedModalLabel">Guardar respuesta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center" v-show="savingStatus == 0">
                    <i class="fa fa-spin fa-spinner text-muted"></i> Guardando respuesta...
                </div>
                <div class="text-center" v-show="savingStatus == 1">
                    <i class="fa fa-check-circle text-success"></i> Respuesta guardada
                </div>
                <hr>
                <div class="text-center">
                    <p v-if="qtyWithoutEmotion == 0"><i class="fa fa-check"></i> Asignaste emociones a todos los personajes</p>
                    <p v-else><i class="fa fa-info-circle text-muted"></i> Hay {{ qtyWithoutEmotion }} personajes sin emoción asignada</p>
    
                    <p v-if="completedNarracion"><i class="fa fa-check"></i> Ya escribiste una historia</p>
                    <p v-else><i class="fa fa-info-circle text-muted"></i> Tu historia es demasiado corta todavía</p>
    
                    <p v-if="completedAnswer">Ya puedes finalizar tu respuesta</p>
                    <p v-else class="text-center">Completa la respuesta antes de finalizarla</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w120p" data-bs-dismiss="modal"
                    v-on:click="finalizeAnswer" v-bind:disabled="!completedAnswer"
                >
                    Finalizar
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Seguir editando</button>
            </div>
        </div>
    </div>
</div>