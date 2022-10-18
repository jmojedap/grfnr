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
        <div class="card mb-2">
            <div class="card-body">
                <h3>Narración</h3>
                <p>{{ respuesta.content }}</p>
            </div>
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

        <table class="table table-sm bg-white">
            <tbody>
                <tr>
                    <td class="text-muted">Ciudad</td>
                    <td>{{ respuesta.city_name }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Institución</td>
                    <td>{{ user.institution }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Usuario</td>
                    <td>{{ user.username }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Edad</td>
                    <td><span v-show="respuesta.age > 0">{{ respuesta.age }} años</span></td>
                </tr>
                <tr>
                    <td class="text-muted">Género</td>
                    <td>
                        <span v-show="user.gender == 1">Femenino</span>
                        <span v-show="user.gender == 2">Masculino</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">ID respuesta</td>
                    <td>{{ respuesta.id }}</td>
                </tr>
                <tr>
                    <td class="text-muted">ID escena</td>
                    <td>{{ respuesta.escena_id }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>