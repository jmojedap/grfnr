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
                <a class="nav-link" v-bind:class="{'active': section == 'narracion' }" v-on:click="setSection('narracion')">Historia</a>
            </li>
            <li class="nav-item pointer">
                <a class="nav-link" v-bind:class="{'active': section == 'emociones' }" v-on:click="setSection('emociones')">Emociones</a>
            </li>
        </ul>

        <div v-show="section == 'narracion'">
            <div class="card">
                <div class="card-body">
                    <p>{{ respuesta.content }}</p>
                </div>
            </div>
        </div>
        <div v-show="section == 'emociones'">
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
                        <td v-bind:class="`emocion-` + parseInt(personajesEmociones[key].feeling_cod)">
                            <span>
                                {{ emocionName(personajesEmociones[key].feeling_cod) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>