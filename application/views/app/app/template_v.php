<div id="template_app">
    <h1>Botones</h1>

    <button class="btn w120p mr-2" v-for="bs_class in classes" v-bind:class="`btn-` + bs_class">
        {{ bs_class }}
    </button>

    <hr>    
</div>

<script>
var template_app = new Vue({
    el: '#template_app',
    data: {
        classes: ['primary', 'secondary', 'success', 'info', 'warning', 'danger', 'light', 'main'],
    },
    methods: {
        
    }
})
</script>