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

    function mis_escenas($user_id)
    {
        $select = 'posts.id, posts.post_name AS title, posts.excerpt AS description, 
            posts.slug, posts.url_thumbnail, posts.url_image,
            respuestas.id AS respuesta_id, respuestas.status AS respuesta_status';

        $this->db->select($select);
        $join_condition = "respuestas.type_id = 129 AND respuestas.related_1 = {$user_id}";
        $this->db->join('posts as respuestas', "posts.id = respuestas.related_2 AND {$join_condition}", 'left');
        $this->db->where('posts.type_id', 121);
        //$this->db->where('respuestas.related_1', $user_id);
        //$this->db->where('respuestas.related_2', 129);
        $this->db->order_by('posts.id', 'ASC');
        $escenas = $this->db->get('posts');

        //echo $this->db->last_query();

        return $escenas;
    }

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
     * String formato json, con array personajes para guardar en posts.content_json
     * en blanco, para asignar emociones
     * 2022-09-02
     */
    function personajes_json($escena_id)
    {
        $personajes_json = [];

        $personajes = $this->personajes($escena_id);

        foreach ($personajes->result_array() as $row) {
            $personaje['character_id'] = $row['id'];
            $personaje['feeling_cod'] = 0;
            $personajes_json[] = $personaje;
        }

        return json_encode($personajes_json);
    }

    /**
     * Marcar un posts respuesta como finalizado
     * 2022-09-04
     */
    function finalizar_respuesta($escena_id, $respuesta_id)
    {
        $data = array('saved_id' => 0, 'message' => 'No se pudo finalizar la respuesta');

        //Condición de identificación, estado y datos
        $condition = "type_id = 129 AND id = {$respuesta_id}
            AND related_2 = {$escena_id} AND status = 2";
        $respuesta = $this->Db_model->row('posts', $condition);

        if ( ! is_null($respuesta) ) {
            $arr_row['status'] = 1; //Finalizada
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['updated_at'] = date('Y-m-d H:i:s');

            $condition = "id = {$respuesta->id}";
            $data['saved_id'] = $this->Db_model->save('posts', $condition, $arr_row);
            if ( $data['saved_id'] > 0 ) {
                $data['message'] = 'Respuesta finalizada';
            }

            $data['details'] = $this->guardar_detalle_respuesta($escena_id, $respuesta_id);
        }

        return $data;
    }

    /**
     * Guardar detalle de emociones de una respuesta en la tabla gfr_answers
     * 2022-09-05
     */
    function guardar_detalle_respuesta($escena_id, $respuesta_id)
    {
        $data = array('status' => 0, 'qty_details' => 0);

        $escena = $this->row($escena_id);
        $respuesta = $this->Db_model->row_id('posts', $respuesta_id);

        $emocionesPersonajes = json_decode($respuesta->content_json);
        
        //Preventivo, eliminar detalle ya existente
        $sql = "DELETE FROM grf_answers WHERE scene_id = {$escena_id} AND answer_id = {$respuesta_id}";
        $this->db->query($sql);

        $arr_row['user_id'] = $respuesta->related_1;
        $arr_row['scene_id'] = $escena_id;
        $arr_row['answer_id'] = $respuesta_id;

        foreach ($emocionesPersonajes as $emocionPersonaje) {
            $arr_row['character_id'] = $emocionPersonaje->character_id;
            $arr_row['feeling_cod'] = $emocionPersonaje->feeling_cod;

            $this->db->insert('grf_answers', $arr_row);

            if ( $this->db->insert_id() ) { $data['qty_details']++; }
        }

        //Extra control, eliminar detalles huérfanos
        $this->limpiar_detalle_respuesta();

        return $data;
    }

    function limpiar_detalle_respuesta()
    {
        $sql = 'DELETE FROM grf_answers
            WHERE answer_id NOT IN (SELECT id FROM posts)';
        $this->db->query($sql);
    }
}