<script>
// Variables
//-----------------------------------------------------------------------------
var appStates = {
    add: {
        buttonText: 'Agregar',
        buttonClass: 'btn-success'
    },
    edit: {
        buttonText: 'Actualizar',
        buttonClass: 'btn-info'
    },
    saved: {
        buttonText: 'Guardado',
        buttonClass: 'btn-success'
    },
    inserted: {
        buttonText: 'Guardado',
        buttonClass: 'btn-success'
    },
    updated: {
        buttonText: 'Actualizado',
        buttonClass: 'btn-success'
    }
};

var categories = <?= json_encode($categories->result()) ?>;
var startCategory = categories.find(category => category.cod == 0)

// VueApp
//-----------------------------------------------------------------------------
    
var manageItemsApp = new Vue({
    el: '#manageItemsApp',
    created: function(){
        this.getItems()
    },
    data: {
        loading: false,
        filters: {q:''},
        categories: <?= json_encode($categories->result()) ?>,
        currCategory: startCategory,
        rowKey: 0,
        rowId: 0,
        items: [],
        fields: {
            item_name: '',
            cod: '',
            abbreviation: '',
            filter: '',
            parent_id: '',
            slug: '',
            description: '',
            long_name: '',
            short_name: '',
            item_group: '',
        },
        formConfig: {
            title: 'Nuevo elemento',
            buttonText: 'Agregar',
            buttonClass: 'btn-primary'
        },
        appState: appStates.add,
        marginValue: '50'
    },
    methods: {
        getCategories: function(){
            this.loading = true
            var formValues = new FormData()
            formValues.append('q', this.filters.q)
            formValues.append('cat_1', 0)
            axios.post(URL_API + 'items/get/', formValues)
            .then(response => {
                this.categories = response.data.list
                this.setFirstCategory()
                this.loading = false
            })
            .catch( function(error) {console.log(error)} )
        },
        setFirstCategory: function(){
            if ( this.categories.length > 0 ) {
                this.currCategory = this.categories[0]
                this.getItems()
                this.setMarginValue(0)
            }
        },
        getItems: function (){
            axios.get(URL_API + 'items/get_list/' + this.currCategory.cod)
            .then(response => {
                this.items = response.data;
                history.pushState(null, null, URL_API + 'items/manage/' + this.currCategory.cod);
                console.log(this.items[0].id);
            })
            .catch(function (error) { console.log(error) })
        },
        clearFilters: function(){
            this.filters.q = ''
            this.getCategories()
        },
        //Cargar el formulario con datos de un elemento (key) de la list
        loadFormValues: function (key){
            this.appState = appStates.edit
            this.rowId = this.items[key].id
            for ( field in this.fields ) { this.fields[field] = this.items[key][field] }
            this.fields.parent_id = '0' + this.items[key].parent_id
            this.formConfig.title = this.items[key].item_name
        },
        handleSubmit: function(){
            this.loading = true
            var formValues = new FormData(document.getElementById('itemForm'))
            axios.post(URL_API + 'items/save/' + this.rowId, formValues)
            .then(response => {
                console.log(response.data.status);
                
                if ( response.data.status == 1 ) 
                {
                    appState = appStates.saved;
                    toastr['success']('Registro guardado');
                    
                    if ( this.rowId > 0 ) {
                        this.appState = appStates.updated;
                    } else {
                        this.appState = appStates.inserted;
                        for ( key in this.fields ) { this.fields[key] = '';}
                    }
                    
                    this.getItems();
                    this.rowId = response.data.saved_id;
                }
                this.loading = false
                
            })
            .catch(function (error) { console.log(error) })
        },
        //Establece un elemento como el actual
        setCurrentElement: function(key) {
            this.rowId = this.items[key].id;
            this.rowKey = key;
            console.log(this.rowId);
        },
        delete_element: function() {
            axios.get(URL_API + 'items/delete/' + this.rowId + '/' + this.currCategory.cod)
            .then(response => {
                if ( response.data.status == 1 )
                {
                    this.items.splice(this.rowKey, 1)
                    toastr['info']('Elemento eliminado')
                }
            })
            .catch(function (error) { console.log(error) })
        },
        clearForm: function() {
            this.rowId = 0;
            this.rowKey = 0;
            for ( key in this.fields ) { this.fields[key] = ''; }
            this.formConfig.title = 'Nuevo elemento';
            this.appState = appStates.add;
            this.$refs.field_cod.focus();
        },
        setCategory: function(category_key){
            this.currCategory = this.categories[category_key]
            this.getItems()
            this.clearForm()
            this.setMarginValue(category_key)
        },
        setMarginValue: function(category_key){
            this.marginValue = 50 + category_key * 34
        },
        //COMPLEMENTARY FUNCTIONS
        autocomplete: function() {
            console.log(this.fields.item_name);
            this.setNames();
            if ( this.fields.abbreviation.length == 0 ) { this.setAbbreviation() }
            if ( this.fields.slug.length == 0 ) { this.setSlug() }
        },
        setAbbreviation: function() {
            console.log('Completando abreviatura');
            var abbreviation = '';
            abbreviation = this.fields.item_name.substr(0,3);
            abbreviation = abbreviation.toLowerCase();
            console.log(abbreviation);
            this.fields.abbreviation = abbreviation;
        },
        setNames: function() {
            if ( this.fields.description.length == 0 ) { 
                this.fields.description = this.currCategory.item_name + ', ' + this.fields.item_name;
            }
            if ( this.fields.long_name.length == 0 ) { this.fields.long_name = this.fields.item_name; }
            if ( this.fields.short_name.length == 0 ) { this.fields.short_name = this.fields.item_name; }
        },
        //Establecer item.slug
        setSlug: function() {
            const params = new URLSearchParams();
            params.append('text', this.fields.item_name);
            params.append('table', 'items');
            params.append('field', 'slug');
            
            axios.post(url_base + 'tools/unique_slug/', params)
            .then(response => {
                console.log(response.data);
                this.fields.slug = response.data
            })
            .catch(function (error) { console.log(error) })
        }
    }
});
</script>