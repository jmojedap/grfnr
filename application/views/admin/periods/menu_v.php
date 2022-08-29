<script>
    var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3); ?>'
    var nav2RowId = '<?= $row->id ?>'
    var sections = [
        {
            id: 'periods_explore',
            text: 'Explorar',
            cf: 'periods/explore/',
            anchor: true,
            roles: [1]
        },
        {
            id: 'periods_info',
            text: 'Información',
            cf: 'periods/info/' + nav2RowId,
            roles: [1]
        },
        {
            id: 'periods_edit',
            text: 'Editar',
            cf: 'periods/edit/' + nav2RowId,
            anchor: true,
            roles: [1]
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