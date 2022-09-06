<div class="escena-grid">
    <div id="escena-image" v-bind:style="`background-image: url(` + escena.url_image + `);`">
        <div class="marker" v-for="(personaje, key) in personajes" v-bind:class="{'active': personaje.id == currPersonaje.id }"
            v-bind:style="`top: ` + personaje.top + `px; left: ` + personaje.left + `px;`" v-on:click="setCurrent(key)">
            <div class="num-marker">
                {{ personaje.index }}
            </div>
            <div class="emocion-marker" v-bind:class="`emocion-` + parseInt(personajesEmociones[key].feeling_cod)">
                {{ emocionName(personajesEmociones[key].feeling_cod) }}
            </div>
        </div>
    </div>
    <div>
        <ul class="nav nav-tabs mb-2">
            <li class="nav-item pointer">
                <a class="nav-link" v-bind:class="{'active': section == 'emociones' }" v-on:click="setSection('emociones')">Emociones</a>
            </li>
            <li class="nav-item pointer">
                <a class="nav-link" v-bind:class="{'active': section == 'narracion' }" v-on:click="setSection('narracion')">Historia</a>
            </li>
        </ul>

        <div v-show="section == 'narracion'">
            <form accept-charset="utf-8" method="POST" id="respuestaForm" @submit.prevent="saveAnswer">
                <fieldset v-bind:disabled="loading">
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="20" required
                            title="Escribe una historia sobre esta escena"
                            placeholder="Escribe una historia sobre esta escena" v-model="respuesta.content"></textarea>
                    </div>
                    <fieldset>
            </form>
        </div>
        <div v-show="section == 'emociones'">
            <h3 class="text-center">Personajes y emociones</h3>
            <table class="table table-sm bg-white">
                <thead>
                    <th width="10px">No.</th>
                    <th>Personaje</th>
                    <th>Emoción</th>
                </thead>
                <tbody>
                    <tr v-for="(personaje, key) in personajes"
                        v-bind:class="{'table-warning': personaje.id == currPersonaje.id }">
                        <td class="text-center">{{ personaje.index }}</td>
                        <td>
                            {{ personaje.nombre }}
                        </td>
                        <td>
                            <select class="emocion-select" v-bind:disabled="loading"
                                v-bind:class="`emocion-` + parseInt(personajesEmociones[key].feeling_cod)"
                                v-model="personajesEmociones[key].feeling_cod">
                                <option v-for="optionEmocion in arrEmocion" v-bind:value="optionEmocion.str_cod">
                                    {{ optionEmocion.name }}</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>