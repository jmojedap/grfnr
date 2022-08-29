<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {
        
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/info/';
    public $url_controller = URL_APP . 'info/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();

        $this->load->model('Info_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función
     */
    function index()
    {
        $this->inicio();
            
    }

// FUNCIONES FRONT INFO
//-----------------------------------------------------------------------------

    function inicio()
    {
        $data['view_a'] = $this->views_folder . 'home_v';
        $data['head_title'] = 'Documentación grafinar';

        $this->load->view(TPL_FRONT, $data);
    }

    
}