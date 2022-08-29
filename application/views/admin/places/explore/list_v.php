<div class="text-center mb-2" v-show="loading">
    <div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="10px">ID</th>
            <th>Nombre</th>
            <th>Status</th>
            <th width="150px">Tipo</th>
            <th>País</th>
            <th>Departamento</th>
            <th>Población</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                <td class="text-muted">{{ element.id }}</td>

                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "places/edit/" ?>` + element.id">
                        {{ element.place_name }}
                    </a>
                </td>
                <td>
                    <button v-if="element.status == 0" v-on:click="set_status(key, 1)" class="btn btn-warning w100p btn-sm">Inactivo</button>
                    <button v-if="element.status == 1" v-on:click="set_status(key, 0)" class="btn btn-light w100p btn-sm">Activo</button>
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "places/explore/1/?type=0" ?>` + element.type_id">{{ placeTypeLabel(element.type_id, 'name') }}</a>
                </td>

                <td>{{ element.country }}</td>
                <td>{{ element.region }}</td>
                <td>{{ element.population | number_format }} <i class="text-muted fa fa-caret-right"></i> <span class="text-muted">{{ element.population_year }}</span></td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>