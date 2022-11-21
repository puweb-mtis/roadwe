<?php defined('BASEPATH') or exit('No direct script access allowed');

class CommonModel extends CI_Model
{
    protected $t_cmmt = 'common_meta';
    protected $t_cmmtf = 'common_meta_file';

    /**
     * set
     *
     * 사이트 메타 정보를 저장한다.
     */
    public function set($params)
    {
        $params = addCommColumn($params);
        $filter_fields = $this->db->list_fields($this->t_cmmt);
        $data = array_intersect_key($params, array_flip($filter_fields));
        $this->db->replace($this->t_cmmt, $data);
        return $this->db->insert_id();
    }

    /**
     * get
     *
     * 사이트 메타 정보를 추출한다.
     */
    public function get($meta_group, $meta_type, $meta_key)
    {
        $where['cmmt_group'] = $meta_group;
        $where['cmmt_type'] = $meta_type;
        if ($meta_key) {
            $where['cmmt_key'] = $meta_key;
        }
        $where['comm_use_yn'] = 'Y';

        $this->db->order_by('cmmt_sort', 'ASC');

        return $this->db->get_where($this->t_cmmt, $where)->result_array();
    }

    /**
     * uploadBatch
     *
     * 파일 업로드 정보를 DB에 일괄 저장한다.
     */
    public function uploadBatch($params)
    {
        foreach ($params as $key => $value) {
            $params[$key] = addCommColumn($value);
        }
        $this->db->insert_batch($this->t_cmmtf, $params);
        return $this->db->affected_rows();
    }

    /*
	 * insert
     *
     * 파일 업로드 정보를 DB에 저장한다.
	 * (insert_id 값 리턴)
     * 
	 */
    public function insert($params, $type = NULL)
    {
        $params = addCommColumn($params);
        $filter_fields = $this->db->list_fields($this->t_cmmtf);
        $data = array_intersect_key($params, array_flip($filter_fields));
        $this->db->insert($this->t_cmmtf, $data);
        return $this->db->insert_id();
    }

    /**
     * getFileLoad
     *
     * 파일 업로드 정보를 추출한다.
     */
    public function getFileLoad($parent_tbl, $parent_no, $gubun = 10, $cmmtf_idx = NULL)
    {
        if ($cmmtf_idx)     $where['cmmtf_idx'] = $cmmtf_idx;
        if ($parent_tbl)    $where['cmmtf_parent_tbl'] = $parent_tbl;
        if ($parent_no)     $where['cmmtf_parent_no'] = $parent_no;
        if ($gubun)         $where['cmmtf_type'] = $gubun;
        // 기본 설정
        $where['comm_del_yn']    = 'N';

        $this->db->order_by('cmmtf_idx', 'ASC');

        return $this->db->get_where($this->t_cmmtf, $where)->result_array();
    }

    /**
     * uploadRemove
     *
     * 파일 업로드 정보를 삭제한다.
     */
    public function uploadRemove($parent_tbl, $parent_no, $cmmtf_idx, $gubun = 10)
    {
        if ($cmmtf_idx)     $where['cmmtf_idx'] = $cmmtf_idx;
        if ($gubun)         $where['cmmtf_type'] = $gubun;
        if ($parent_tbl)    $where['cmmtf_parent_tbl'] = $parent_tbl;
        if ($parent_no)     $where['cmmtf_parent_no'] = $parent_no;

        $this->db->where($where);
        $this->db->delete($this->t_cmmtf);
        return $this->db->affected_rows();
    }    
}
