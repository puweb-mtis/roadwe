<?php defined('BASEPATH') or exit('No direct script access allowed');

class VehicleModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = [
            // 'idx' => 'VDIF_idx',
            // 'driving' => 'account_manager',
            'idx' => 'id',
            'driving' => 'car',
            'driven' => 'car_model',
            'prefix' => 'VDIF'
        ];

        $this->base_sql = "		
            VDIF.id
            , VDIF.car_code
            , VDIF.car_name
            , VDIF.car_owner_driver_id
            , (
                SELECT 
                    ACMB.member_name
                FROM
                    member ACMB 
                WHERE
                    ACMB.id = VDDV2.member_id
            ) AS car_owner_driver_name_txt
            , VDIF.car_owner_transport_id
            , (
                IFNULL((SELECT 
                    TP.company_name 
                FROM
                    transport TP WHERE TP.id = VDIF.car_owner_transport_id
                ),'운송회사 미등록')
            ) AS company_name_txt
            , VDIF.driver_id
            , (
                SELECT 
                    ACMB.member_name
                FROM
                    member ACMB 
                WHERE
                    ACMB.id = VDDV.member_id
            ) AS car_driver_name_txt
            , VDMD.car_type_id
            , VDMD.car_producer_id
            , CONCAT(VDPD.producer_name,' ',VDMD.car_name) AS car_name_full_txt
            , VDTP.car_type_name            
            , VDIF.car_number
            , VDIF.`year`
            , IFNULL(VDIF.personnel_limit,0) AS personnel_limit
            , VDIF.device_unit_id
            , VDIF.c_datetime
            , VDIF.c_name
            , VDIF.m_datetime
            , VDIF.m_name	
            , VDIF.car_model_id
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
        $this->db->join($this->table['driven'] . ' as VDMD', 'VDMD.id = ' . $this->table['prefix'] . '.car_model_id', 'LEFT');
        $this->db->join("car_type as VDTP", "VDTP.id = VDMD.car_type_id", "LEFT");
        $this->db->join("car_producer as VDPD", "VDPD.id = VDMD.car_producer_id", "LEFT");
        $this->db->join("driver as VDDV", "VDDV.id = VDIF.driver_id", "LEFT");
        $this->db->join("driver as VDDV2", "VDDV2.id = VDIF.car_owner_driver_id", "LEFT");

        // 기본 조건 선언
        if (isset($params['sec_default']) && is_bool($params['sec_default'])) {
            $where['VDIF.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
            $where['VDIF.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)
        }

        // 폼 조건 검색(키워드&검색어)
        if (isset($params['sec_column']) && isset($params['sec_keyword'])) {
            $this->db->like('VDIF.' . $params['sec_column'], $params['sec_keyword']);
        }

        // 폼 조건 검색(가입일&최종방문일)
        if (isset($params['sec_st_date']) || isset($params['sec_ed_date'])) {
            $stime = ' 00:00:00';
            $etime = ' 23:59:59';

            if (isset($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $this->db->where("VDIF.c_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                } else {
                    $this->db->where("VDIF.m_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                }
            } elseif (isset($params['sec_st_date']) && empty($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['VDIF.c_datetime >='] = "{$params['sec_st_date']}{$stime}";
                } else {
                    $where['VDIF.m_datetime >='] = "{$params['sec_st_date']}{$stime}";
                }
            } elseif (empty($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['VDIF.c_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                } else {
                    $where['VDIF.m_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                }
            }
        }

        // 폼 조건 검색(종류)
        if (isset($params['sec_car_grade_cd']) && $params['sec_car_grade_cd'] != 'all') {
            // $this->db->where_in('VDTP.car_type_name', $params['sec_car_grade_cd']);
        }

        // 폼 조건 검색(차량소유) - DRIVER:기사님, TRANSPORT:운송회사
        if (isset($params['sec_car_owner_type_cd'])) {
            $where['VDIF.car_owner_type'] = $params['sec_car_owner_type_cd'];
        }

        // 조건 검색(차량 일련번호)
        if (isset($params['vdif_id'])) {
            $where['VDIF.id'] = $params['vdif_id'];
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

        $sub_query_from = '(' . $in_sql . ') as VDIF';
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
