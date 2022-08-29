<script>
var sectionId = '<?= $this->uri->segment(2) . '_' . $this->uri->segment(3); ?>'
var sections = [
    {
        id: 'periods_explore',
        text: 'Explorar',
        cf: 'periods/explore',
        roles: [1]
    },
    {
        id: 'periods_calendar',
        text: 'Calendario',
        cf: 'periods/calendar',
        roles: [1]
    },
    {
        id: 'periods_add',
        text: 'Nuevo',
        cf: 'periods/add',
        roles: [1]
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