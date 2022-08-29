<div class="text-center mb-2" v-show="loading">
    <div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>
</div>

<div class="table-responsive" v-show="!loading">
    <table class="table bg-white">
        <thead>
            <th width="10px"><input type="checkbox" @change="select_all" v-model="all_selected"></th>
            <th width="50px"></th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Username</th>
            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td><input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id"></td>
                
                <td>
                    <a v-bind:href="`<?= URL_ADMIN . "users/profile/" ?>` + element.id">
                        <img
                            v-bind:src="element.url_thumbnail"
                            class="rounded-circle w40p"
                            v-bind:alt="element.id"
                            onerror="this.src='<?= URL_IMG ?>users/sm_user.png'"
                        >
                    </a>
                </td>
                <td>
                    <a v-bind:href="URL_APP + `users/profile/` + element.id + `/` + element.username">
                        {{ element.display_name }}
                    </a>
                    <br>
                    <span class="text-muted">
                        {{ element.email }}
                    </span>
                </td>
                <td>{{ roleLabel(element.role) }}</td>
                <td>{{ element.username }}</td>
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="set_current(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>