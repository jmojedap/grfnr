<?php
class Escena_model extends CI_Model{

    function basic($post_id)
    {
        $row = $this->Db_model->row_id('posts', $post_id);

        $data['row'] = $row;
        $data['head_title'] = $data['row']->post_name;
        $data['view_a'] = $this->views_folder . 'post_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';

        return $data;
    }

    /**
     * Personajes de una escena
     * 2022-08-30
     */
    function personajes($escena_id)
    {
        $select = $this->Post_model->select('125_personajes');

        $this->db->select($select);
        $this->db->where('type_id', 125);
        $this->db->where('parent_id', $escena_id);
        $this->db->order_by('position', 'ASC');
        $personajes = $this->db->get('posts');

        return $personajes;
    }

// PROCESO DE RESPUESTA
//-----------------------------------------------------------------------------

    function row($escena_id)
    {
        $row = NULL;

        $select = $this->Post_model->select('121_escenas');
        $this->db->select($select);
        $this->db->where('id', $escena_id);
        $query = $this->db->get('posts',1);

        if ( $query->num_rows() > 0 ) $row = $query->row();

        return $row;
    }

    /**
     * Crea el registro en la tabla posts, type_id 129, respuesta
     * a una escena, con los valores de emociones en blanco
     * 2022-09-02
     * 
     */
    function iniciar_respuesta($escena_id, $user_id)
    {
        // Resultado por defecto
        $data = array('status' => 0, 'saved_id' => 0);

        $escena = $this->row($escena_id);
        $user = $this->Db_model->row_id('users', $user_id);

        // Si la escena y el usuario existen:
        if ( ! is_null($escena) && ! is_null($user) ) {
            $arr_row = $this->Db_model->arr_row(false); //Datos base

            $arr_row['type_id'] = 129;
            $arr_row['post_name'] = $escena->title;
            $arr_row['content_json'] = $this->personajes_json($escena_id);
            $arr_row['status'] = 10; //Iniciada
            $arr_row['related_1'] = $user->id;
            $arr_row['related_2'] = $escena->id;
            $arr_row['integer_1'] = $this->pml->age($user->birth_date);

            //Condición, un usuario solo puede responder una escena una vez
            $condition = "type_id = {$arr_row['type_id']}
                AND related_1 = {$arr_row['related_1']}
                AND related_2 = {$arr_row['related_2']}";

            $data['saved_id'] = $this->Db_model->insert_if('posts', $condition, $arr_row);
            if ( $data['saved_id'] > 0 ) { $data['status'] = 1; }
        }
    
        return $data;
    }

    /**
     * Actualiza registro de respuesta type_id 129, tabla posts
     * 2022-09-03
     */
    function guardar_respuesta($escena_id, $respuesta_id)
    {
        $arr_row['content'] = $this->input->post('content');
        $arr_row['content_json'] = $this->input->post('content_json');
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        $condition = "id = {$respuesta_id} AND related_2 = {$escena_id}";
        $data['saved_id'] = $this->Db_model->save('posts', $condition, $arr_row);
    
        return $data;
    }

    /**
     * String formato json, con array personajes para guardar en posts.content_json
     * en blanco, para asignar emociones
     * 2022-09-02
     */
    function personajes_json($escena_id)
    {
        $personajes_json = [];

        $personajes = $this->personajes($escena_id);

        foreach ($personajes->result_array() as $row) {
            $personaje['id'] = $row['id'];
            $personaje['emocion_cod'] = 0;
            $personajes_json[] = $personaje;
        }

        return json_encode($personajes_json);
    }
}