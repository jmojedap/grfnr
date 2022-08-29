<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>'
var nav2RowId = '<?= $file_id ?>'
var sections = [
    {
        text: 'Información',
        id: 'files_info',
        cf: 'files/info/' + nav2RowId,
        roles: [1,2]
    },
    {
        text: 'Editar',
        id: 'files_edit',
        cf: 'files/edit/' + nav2RowId,
        roles: [1,2]
    },
    {
        text: 'Recortar',
        id: 'files_cropping',
        cf: 'files/cropping/' + nav2RowId,
        roles: [1,2]
    },
    {
        text: 'Cambiar',
        id: 'files_change',
        cf: 'files/change/' + nav2RowId,
        roles: [1,2]
    },
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