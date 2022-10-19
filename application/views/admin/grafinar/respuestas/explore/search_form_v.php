<form accept-charset="utf-8" method="POST" id="searchForm" @submit.prevent="getList">
    <input name="q" type="hidden"  v-model="filters.q">
    <div class="grid-columns-15rem mb-3">
        <div>
            <label for="fe1">Escena</label>
            <select name="fe1" v-model="filters.fe1" class="form-control">
                <option value="">[ Todos ]</option>
                <option v-for="optionEscena in arrEscena" v-bind:value="`0` + optionEscena.id">{{ optionEscena.name }}</option>
            </select>
        </div>
        <div>
            <label for="org">Institución</label>
            <select name="org" v-model="filters.org" class="form-control">
                <option value="">[ Todas ]</option>
                <option v-for="optionInstitucion in arrInstitucion" v-bind:value="`0` + optionInstitucion.id">{{ optionInstitucion.name }}</option>
            </select>
        </div>
        <div>
            <label for="level">Grado</label>
            <select name="level" v-model="filters.level" class="form-control">
                <option value="">[ Todos ]</option>
                <option v-for="optionLevel in arrLevel" v-bind:value="optionLevel.str_cod">{{ optionLevel.name }}</option>
            </select>
        </div>
        
        <!-- Botón ejecutar y limpiar filtros -->
        <div>
            <label for="" style="opacity: 0%">Enviar</label><br>
            <button class="btn btn-primary w100p" type="submit">Buscar</button>
            <button type="button" class="btn btn-light" title="Quitar los filtros de búsqueda"
                v-show="strFilters.length > 0" v-on:click="clearFilters">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</form>
