<?php

class Products_Model extends LOFT_Model
{
    public $table = 'goods';

    public function products_count_by_cat($category_id)
    {
        $result = parent::getAll(array('id_category'=>$category_id));
        return count($result);
    }

    public function products_count()
    {
        $result = count(parent::getAll());
        return $result;
    }

    public function getProductsByCategory($cat, $limit ,$start)
    {
        $this->db->where(array('id_category'=>$cat));
        $this->db->limit($limit, $start);
        $result = $this->db->get($this->table);
        return $result->result_array();
    }

//    TODO: в getProductByID дописать джоин id_category, id_brand

    /**
     *
     * Метод получения информации из БД по товару с ID = $id
     *
     * @param $id - ID товара для поиска
     * @return mixed - инфа о товаре
     */
    public function getProductById($id)
    {
        $this->db->where(array('id'=>$id));
        $result = $this->db->get($this->table);
        return $result->row();
    }



}