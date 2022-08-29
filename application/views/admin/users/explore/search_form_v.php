<?php
    $filters_style = ( strlen($str_filters) > 0 ) ? '' : 'display: none;' ;
?>

<form accept-charset="utf-8" method="POST" id="search_form" @submit.prevent="get_list">
    <div class="mb-3 row">
        <div class="col-md-8">
            <div class="input-group mb-2">
                <input
                    type="text" name="q" class="form-control"
                    placeholder="Buscar" title="Buscar"
                    autofocus
                    v-model="filters.q" v-on:change="get_list"
                    >
                <div class="input-group-append" title="Buscar">
                    <button type="button" class="btn" title="Mostrar filtros para búsqueda avanzada"
                        v-on:click="toggle_filters"
                        v-bind:class="{'btn-primary': display_filters, 'btn-light': !display_filters }"
                        >
                        <i class="fas fa-chevron-down" v-show="!display_filters"></i>
                        <i class="fas fa-chevron-up" v-show="display_filters"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="adv_filters" style="<?= $filters_style ?>" class="mb-2">
        <div class="mb-3 row">
            <div class="col-md-8">
                <select name="role" v-model="filters.role" class="form-control" title="Filtrar por rol">
                    <option value="">[ Todos ]</option>
                    <option v-for="optionRole in arrRole" v-bind:value="optionRole.str_cod">{{ optionRole.name }}</option>
                </select>
            </div>
            <label for="role" class="col-md-4 col-form-label">Rol</label>
        </div>

        <!-- Botón ejecutar y limpiar filtros -->
        <div class="mb-3 row">
            <div class="col-md-8 text-right">
                <button class="btn btn-light w120p" v-on:click="remove_filters" type="button" v-show="active_filters">Todos</button>
                <button class="btn btn-primary w120p" type="submit">Buscar</button>
            </div>
        </div>
    </div>
</form>