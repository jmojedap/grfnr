<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>'
var nav2RowId = '<?= $respuesta->id ?>'
var sceneId = '<?= $escena->id ?>'
var sections = [
    {
        id: 'respuestas_info',
        text: 'Información',
        cf: 'respuestas/info/' + sceneId + '/' + nav2RowId,
        roles: [1,2,3]
    },
]
    
//Filter role sections
var nav_2 = sections.filter(section => section.roles.includes(parseInt(APP_RID)));

//Set active class
nav_2.forEach((section,i) => {
    nav_2[i].class = ''
    if ( section.id == sectionId ) nav_2[i].class = 'active'
})
</script>

<?php
$this->load->view('common/nav_2_v');