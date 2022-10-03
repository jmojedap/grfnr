<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3) ?>';
var sections = [
    {
        id: 'accounts_profile',
        text: 'Perfil',
        cf: 'accounts/profile',
        roles: [1,2,4,21,32]
    },
    {
        id: 'accounts_edit',
        text: 'Editar',
        cf: 'accounts/edit/basic',
        roles: [1,2,4,21,31]
    },
];

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