<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Participacion extends CI_Controller {
        
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/participacion/';
    public $url_controller = URL_APP . 'participacion/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();

        $this->load->model('Post_model');
        $this->load->model('Escena_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función
     */
    function index()
    {
        $this->asentimiento_informado();
    }

// FUNCIONES HERRAMIENTA
//-----------------------------------------------------------------------------

    /**
     * Vista para cargar/ver archivo de asentimiento informado de un usuario
     * participante.
     * 2022-09-07
     */
    function asentimiento_informado()
    {
        $data['head_title'] = 'Asentimiento informado';
        $data['view_a'] = $this->views_folder . 'asentimiento_informado_v';

        $this->App_model->view('templates/easypml/answers', $data);
    }
}