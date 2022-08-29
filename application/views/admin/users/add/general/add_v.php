<div id="addUserApp">
    <div class="card center_box_750">
        <div class="card-body">
            <form accept-charset="utf-8" method="POST" id="addUserForm" @submit.prevent="handleSubmit">
                <fieldset v-bind:disabled="loading">
                    <div class="mb-3 row">
                        <label for="role" class="col-md-4 col-form-label text-right">Rol</label>
                        <div class="col-md-8">
                            <select name="role" v-model="fields.role" class="form-control" required>
                                <option v-for="(option_role, key_role) in options_role" v-bind:value="key_role">
                                    {{ option_role }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="first_name" class="col-md-4 col-form-label text-right">Nombre &middot;
                            Apellidos</label>
                        <div class="col-md-4">
                            <input name="first_name" class="form-control" placeholder="Nombres"
                                title="Nombres del usuario" v-model="fields.first_name">
                        </div>
                        <div class="col-md-4">
                            <input name="last_name" class="form-control" placeholder="Apellidos"
                                title="Apellidos del usuario" v-model="fields.last_name" v-on:change="setDisplayName(false)">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="display_name" class="col-md-4 col-form-label text-right">Nombre completo</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input name="display_name" class="form-control" required v-model="fields.display_name">
                                <div class="input-group-append">
                                    <button class="btn btn-light" type="button"
                                        v-on:click="setDisplayName(true)"><i class="fas fa-magic"></i></button>
                                </div>
                            </div>
                            <small id="displayNameTip" class="form-text text-muted">Mostrar usuario como</small>
                        </div>
                    </div>

                    <div class="mb-3 row" id="mb-3_email">
                        <label for="email" class="col-md-4 col-form-label text-right">Correo electrónico</label>
                        <div class="col-md-8">
                            <input name="email" type="email" class="form-control"
                                title="Dirección de correo electrónico" required
                                v-bind:class="{ 'is-invalid': validation.email_unique == 0, 'is-valid': validation.email_unique == 1 }"
                                v-model="fields.email" v-on:change="validateForm">
                            <span class="invalid-feedback">
                                El correo electrónico ya fue registrado, por favor escriba otro
                            </span>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="password" class="col-md-4 col-form-label text-right">Contraseña</label>
                        <div class="col-md-8">
                            <input name="password" class="form-control"
                                title="Debe tener al menos un número y una letra minúscula, y al menos 8 caractéres"
                                required pattern="(?=.*\d)(?=.*[a-z]).{8,}" v-model="fields.password">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="gender" class="col-md-4 col-form-label text-right">Sexo</label>
                        <div class="col-md-8">
                            <select name="gender" v-model="fields.gender" class="form-control">
                                <option v-for="(option_gender, key_gender) in options_gender" v-bind:value="key_gender">
                                    {{ option_gender }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="offset-4 col-md-8">
                            <button class="btn btn-success w120p" type="submit">
                                <i class="fa fa-spin fa-spinner" v-show="loading"></i>
                                Crear
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_created" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Usuario creado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <i class="fa fa-check text-success"></i> Usuario creado correctamente
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" v-on:click="goToCreated">Abrir usuario</button>
                    <button type="button" class="btn btn-secondary" v-on:click="clearForm" data-dismiss="modal">
                        <i class="fa fa-plus"></i> Crear otro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view($this->views_folder . 'add/general/vue_v');