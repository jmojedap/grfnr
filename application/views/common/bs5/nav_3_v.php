<div id="nav_3_app" class="mb-2">
    <div class="only-lg">
        <ul class="nav nav-pills justify-content-center">
            <li class="nav-item" v-for="(element, key) in nav_3">
                <a class="nav-link" href="#"
                    v-bind:class="element.class" v-on:click="activateMenu(key)"
                >
                {{ element.text }}
                </a>
            </li>
        </ul>
    </div>

    <div class="dropdown only-sm">
        <button class="btn btn-light w-100" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            {{ current.text }}
            <span class="float-end">
                <i class="fa fa-chevron-down"></i>
            </span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="width: 100%">
            <li>
                <a class="dropdown-item"
                    v-for="(element, key) in nav_3"
                    v-bind:class="element.class"
                    v-on:click="activateMenu(key)"
                    >
                    <i class="w30p" v-bind:class="element.icon"></i>
                    {{ element.text }}
                </a>
            </li>
            
        </ul>
    </div>
</div>

<script>
var nav_3_app = createApp({
    data(){
        return{
            nav_3: nav_3,  //Elementos contenido del menú
            current: { text: 'Menú' }
        }
    },
    methods: {
        activateMenu: function (key) {
            this.current = this.nav_3[key];
            for ( i in this.nav_3 ){
                this.nav_3[i].class = '';
            }
            this.nav_3[key].class = 'active';   //Elemento actual
            if ( this.nav_3[key].anchor ) {
                window.location = URL_APP + this.nav_3[key].cf;
            } else {
                this.loadViewA(key);
            }
        },
        loadViewA: function(key){
            app_cf = this.nav_3[key].cf;
            getSections('nav_3'); //Función global
        },
        setCurrent: function(){
            this.current = this.nav_3.find(item => item.class == 'active')
        }
    },
    mounted(){
        this.setCurrent();
    }
}).mount('#nav_3_app')
</script>