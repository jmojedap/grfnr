<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>'
var sections = [
    {
        text: 'Opciones',
        id: 'config_options',
        cf: 'config/options/',
        roles: [1,2]
    },
    {
        text: 'Procesos',
        id: 'config_processes',
        cf: 'config/processes/',
        roles: [1,2]
    },
    {
        text: 'Colores',
        id: 'config_colors',
        cf: 'config/colors/',
        roles: [1,2]
    },
    {
        text: 'Importar',
        id: 'config_import',
        cf: 'config/import/',
        roles: [1,2]
    }
]
    
//Filter role sections
var nav_2 = sections.filter(section => section.roles.includes(parseInt(APP_RID)))

//Set active class
nav_2.forEach((section,i) => {
    nav_2[i].class = ''
    if ( section.id == sectionId ) nav_2[i].class = 'active'
})
</script>

<?php
$this->load->view('common/nav_2_v');