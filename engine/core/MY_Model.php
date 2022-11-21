<?php defined('BASEPATH') or exit('No direct script access allowed');
class MY_Model extends CI_Model
{
    protected $base_sql = null;

    /**
     * 테이블에 따른 공통 변수 선언
     * idx: 기준 테이블의 고유 일련번호
     * driving: 결합되기 위한 기준이 되는 테이블명
     * driven: 결합되는 테이블명
     * prefix: driving 테이블의 AS선언명
     * 
     * @var array
     */
    protected $table = [
        'idx' => null,
        'driving' => null,
        'driven' => null,
        'prefix' => null
    ];

    public function query($params = null, $type = null)
    {
        $where['1'] = '1';
        return $where;
    }

    public function selectCount($params = array())
    {
        $where = $this->query($params);
        $this->db->where($where);
        return $this->db->count_all_results();
    }

    public function selectCountAll()
    {
        $this->db->from($this->table['driving']);
        return $this->db->count_all_results();
    }

    public function selectRow($where)
    {
        return $this->db->where($where)->get($this->table['driving'], 1)->row_array();
    }

    public function insert($params)
    {
        $params = addCommColumn($params);
        $filter_fields = $this->db->list_fields($this->table['driving']);
        $data = array_intersect_key($params, array_flip($filter_fields));
        $this->db->insert($this->table['driving'], $data);
        return $this->db->insert_id();
    }

    public function insertBatch($params)
    {
        foreach ($params as $k => $val) {
            $params[$k] = addCommColumn($val, 'write');
        }
        $this->db->insert_batch($this->table['driving'], $params);
        return $this->db->affected_rows();
    }

    public function update($params, $where)
    {
        $params = addCommColumn($params, 'modify');
        $filter_fields = $this->db->list_fields($this->table['driving']);
        $data = array_intersect_key($params, array_flip($filter_fields));
        $this->db->update($this->table['driving'], $data, $where);
        return $this->db->affected_rows();
    }

    public function updateBatch($params)
    {
        foreach ($params as $k => $val) {
            $params[$k] = addCommColumn($val, 'modify');
        }
        $this->db->update_batch($this->table['driving'], $params, $this->table['idx']);
        return $this->db->affected_rows();
    }

    public function delete($where)
    {
        $this->db->where($where);
        $this->db->delete($this->table['driving']);
        return $this->db->affected_rows();
    }

    public function set($params, $where = NULL)
    {
        $this->db->set($params, NULL, FALSE);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->update($this->table['driving']);
    }

    public function replace($params)
    {
        return $this->db->replace($this->table['driving'], $params);
    }

    public function listFields()
    {
        $filter_fields = $this->db->list_fields($this->table['driving']);

        $rdata = [];
        foreach ($filter_fields as $k => $v) {
            $rdata[$v] = '';
        }
        return $rdata;
    }
}
