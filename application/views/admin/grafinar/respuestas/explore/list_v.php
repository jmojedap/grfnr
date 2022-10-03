<div class="table-responsive">
    <table class="table bg-white">
        <thead>
            <th width="10px">
                <input type="checkbox" @change="selectAll" v-model="allSelected">
            </th>
            <th width="20px" class="table-warning">ID</th>
            <th>Título</th>
            <th>Usuario</th>
            <th>Narración</th>

            <th width="50px"></th>
        </thead>
        <tbody>
            <tr v-for="(element, key) in list" v-bind:id="`row_` + element.id" v-bind:class="{'table-info': selected.includes(element.id) }">
                <td>
                    <input type="checkbox" v-bind:id="`check_` + element.id" v-model="selected" v-bind:value="element.id">
                </td>
                <td class="table-warning">
                    {{ element.id }}
                </td>
                <td>
                    <a v-bind:href="`<?= URL_ADMIN ?>respuestas/info/` + element.scene_id + `/` + element.id">
                        {{ element.title }}
                    </a>
                </td>
                <td>{{ element.user_display_name }}</td>
                <td>
                    <span v-show="element.narracion.length > 0">{{ element.narracion.substring(0,50) }}</span>
                    <span v-show="element.narracion.length > 50">...</span>
                </td>
                
                <td>
                    <button class="a4" data-toggle="modal" data-target="#detail_modal" @click="setCurrent(key)">
                        <i class="fa fa-info"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>