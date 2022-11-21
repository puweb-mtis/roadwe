<?php defined('BASEPATH') or exit('No direct script access allowed');

class DriverModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = [
            'idx' => 'id',
            'driving' => 'driver',
            'driven' => 'member',
            'prefix' => 'VDDV'
        ];

        $this->base_sql = "
            VDDV.*
            , ACMB.member_name
            , TPCP.company_name 
            , TPCP.phone
            , TPCP.manager_name
            , TPCP.manager_landline_number
            , TPCP.manager_landline_ext
            , TPCP.manager_mobile_number
            , TPCP.manager_email
        ";
    }

    /*
     * 공통
     * ************************************************************************/
    function query($params = null, $type = null)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS
            {$this->base_sql}
        ", FALSE);

        $this->db->from($this->table['driving'] . ' as ' . $this->table['prefix']);
        $this->db->join($this->table['driven'] . ' as ACMB', 'ACMB.id = ' . $this->table['prefix'] . '.member_id', 'INNER');
        $this->db->join("transport as TPCP", "TPCP.id = VDDV.transport_id", "LEFT");

        // 기본 조건 선언
        if (isset($params['sec_default']) && is_bool($params['sec_default'])) {
            $where['VDDV.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
            $where['VDDV.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)
        }

        // 폼 조건 검색(키워드&검색어)
        if (isset($params['sec_column']) && isset($params['sec_keyword'])) {
            $this->db->like('VDDV.' . $params['sec_column'], $params['sec_keyword']);
        }

        // 폼 조건 검색(등록일&수정일)
        if (isset($params['sec_st_date']) || isset($params['sec_ed_date'])) {
            $stime = ' 00:00:00';
            $etime = ' 23:59:59';

            if (isset($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $this->db->where("VDDV.c_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                } else {
                    $this->db->where("VDDV.u_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                }
            } elseif (isset($params['sec_st_date']) && empty($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $where['VDDV.c_datetime >='] = "{$params['sec_st_date']}{$stime}";
                } else {
                    $where['VDDV.u_datetime >='] = "{$params['sec_st_date']}{$stime}";
                }
            } elseif (empty($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $where['VDDV.c_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                } else {
                    $where['VDDV.u_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                }
            }
        }

        // 조건 검색(운전기사 일련번호)
        if (isset($params['vddv_idx'])) {
            $where['VDDV.id'] = $params['vddv_idx'];
        }
        return $where;
    }

    /*
     * 기본 쿼리
     * ************************************************************************/
    public function selectList($params = [])
    {
        $limit = (isset($params['limit'])) ? $params['limit'] : null;
        $offset = (isset($params['offset'])) ? $params['offset'] : null;
        $where = $this->query($params);
        // 정렬관련
        if (isset($params['od_type']) && isset($params['od_column'])) {
            $this->db->order_by($params['od_column'], $params['od_type']);
        }
        return $this->db->get_where(null, $where, $limit, $offset)->result_array();
    }

    /*
     * 커버링 인덱스 쿼리
     * ************************************************************************/
    public function coveringindexActiveList($params = [])
    {
        $this->selectList($params);
        $res['sql_str'] = str_replace('SQL_CALC_FOUND_ROWS', '', $this->db->last_query());
        $res['rows'] = $this->db->query('SELECT FOUND_ROWS() count')->row()->count;

        $in_sql = $res['sql_str'];

        $sub_query_from = '(' . $in_sql . ') as VDDV';
        $this->db->select("{$this->base_sql}");
        $this->db->from($sub_query_from);

        $result['list'] = $this->db->get()->result_array();
        $result['rows'] = $res['rows'];
        return $result;
    }

    /*
     * 관리자
     * ************************************************************************/

    /*
     * 사용자
     * ************************************************************************/
}
