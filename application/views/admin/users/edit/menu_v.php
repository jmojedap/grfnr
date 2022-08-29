<script>
var sectionId = '<?=$this->uri->segment(5);  ?>'
var element_id = '<?= $this->uri->segment(4) ?>';
var sections = [
    {
        text: 'General',
        id: 'basic',
        cf: 'users/edit/' + element_id + '/basic',
        roles: [1,2]
    },
    {
        text: 'Imagen',
        id: 'image',
        cf: 'users/edit/' + element_id + '/image',
        roles: [1,2]
    }
]

//Filter role sections
var nav_3 = sections.filter(section => section.roles.includes(parseInt(APP_RID)))

//Set active class
nav_3.forEach((section,i) => {
    nav_3[i].class = ''
    if ( section.id == sectionId ) nav_3[i].class = 'active'
})
//Expeciales
if ( sectionId == '' ) nav_3[0].class = 'active'
</script>

<?php
$this->load->view('common/nav_3_v');