<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>'
var nav2RowId = '<?= $row->id ?>'
var sections = [
    {
        id: 'escenas_responder',
        text: 'Responder',
        cf: 'escenas/responder/' + nav2RowId,
        roles: [1,2,32]
    },
    {
        id: 'escenas_narracion',
        text: 'Narración',
        cf: 'escenas/narracion/' + nav2RowId,
        roles: [1,2,32]
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
$this->load->view('common/bs5/nav_2_v');