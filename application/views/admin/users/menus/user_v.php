<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>'
var nav2RowId = '<?= $row->id ?>'
var sections = [
    {
        id: 'users_profile',
        text: 'Información',
        cf: 'users/profile/' + nav2RowId,
        roles: [1,2],
    },
    {
        id: 'users_edit',
        text: 'Editar',
        cf: 'users/edit/' + nav2RowId,
        anchor: true,
        roles: [1,2],
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