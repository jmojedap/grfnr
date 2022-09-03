<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
public $views_folder = 'app/app/';
public $url_controller = URL_APP . 'app/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función de la aplicación
     */
    function index()
    {
        if ( $this->session->userdata('logged') )
        {
            $this->logged();
        } else {
            redirect('app/accounts/login');
        }    
    }

    function denied()
    {
        $data['head_title'] = 'Acceso no permitido';
        $data['view_a'] = 'app/app/denied_v';

        $this->load->view('templates/easypml/start', $data);
    }

    function template()
    {
        $data['head_title'] = 'Template elements';
        $data['view_a'] = $this->views_folder . 'template_v';

        $this->load->view(TPL_FRONT, $data);
    }

// Funciones especiales
//-----------------------------------------------------------------------------

    function inicio()
    {
        $data['head_title'] = APP_NAME;
        $data['view_a'] = $this->views_folder . 'inicio_v';

        $data['optionsInstituciones'] = $this->App_model->options_post('type_id = 101');
        $data['optionsSchoolLevel'] = $this->Item_model->arr_options('category_id = 3 AND item_group = 1');
        $data['optionsGender'] = $this->Item_model->arr_options('category_id = 59 AND item_group = 1');

        $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
        
        $this->load->view(TPL_FRONT, $data);

    }

    function test(){
        $this->load->view('templates/test/main');
    }
}