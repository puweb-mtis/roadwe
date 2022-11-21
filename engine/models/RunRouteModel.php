<?php defined('BASEPATH') or exit('No direct script access allowed');

class RunRouteModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = [
            'idx' => 'id',
            'driving' => 'line',
            'driven' => 'company',
            'prefix' => 'VDRR'
        ];

        $this->base_sql = "
            VDRR.id
            , VDRR.driver_id
            , IF(ISNULL(VDRR.driver_id),'미배자','배차') AS car_dispatch_txt
            , VDRR.line_driving_status
            , CASE
                WHEN VDRR.line_driving_status = 'BEFORE' THEN '운행예정'
                WHEN VDRR.line_driving_status = 'ING' THEN '운행중'
                WHEN VDRR.line_driving_status = 'END' THEN '운행종료'
                WHEN VDRR.line_driving_status = 'STOP' THEN '중지'
                ELSE '미상태값'
            END AS line_driving_status_txt
            , '차량현재위치'
            , VDRR.line_type
            , CASE
                WHEN VDRR.line_type = 'JEONSE' THEN '전세'
                WHEN VDRR.line_type = 'WORK' THEN '통근'
                WHEN VDRR.line_type = 'SCHOOL' THEN '통학'
                ELSE '미상태값'
            END AS line_type_txt
            , VDRR.line_code
            , VDRR.line_name
            , VDRR.start_station_name
            , VDRR.end_station_name            
            , VDRR.start_date
            , VDRR.end_date
            , VDRR.driving_week
            , VDRR.start_time
            , VDRR.end_time
            , '탑승자이동'
            , '총 탑승자'
            , (
                SELECT 
                    COUNT(VDRRD.id)
                FROM
                    line_detail VDRRD
                WHERE
                    VDRRD.line_id = VDRR.id 
            ) AS cnt
            , VDRR.line_distance
            , CP.company_name
            , '사업장'
            , VDRR.c_datetime
            , VDRR.c_name
            , VDRR.m_datetime
            , VDRR.m_name            
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
        $this->db->join($this->table['driven'] . ' as CP', 'CP.id = ' . $this->table['prefix'] . '.company_id', 'LEFT');

        // 기본 조건 선언
        if (isset($params['sec_default']) && is_bool($params['sec_default'])) {
            $where['VDRR.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
            $where['VDRR.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)
        }

        // 폼 조건 검색(키워드&검색어)
        if (isset($params['sec_column']) && isset($params['sec_keyword'])) {
            if ($params['sec_column'] === 'company_name') { // 거래처 검색
                $this->db->like('CP.' . $params['sec_column'], $params['sec_keyword']);
            } else {
                $this->db->like('VDRR.' . $params['sec_column'], $params['sec_keyword']);
            }
        }

        // 폼 조건 검색(등록일&수정일)
        if (isset($params['sec_st_date']) || isset($params['sec_ed_date'])) {
            $stime = ' 00:00:00';
            $etime = ' 23:59:59';

            if (isset($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $this->db->where("VDRR.c_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                } else {
                    $this->db->where("VDRR.m_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                }
            } elseif (isset($params['sec_st_date']) && empty($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['VDRR.c_datetime >='] = "{$params['sec_st_date']}{$stime}";
                } else {
                    $where['VDRR.m_datetime >='] = "{$params['sec_st_date']}{$stime}";
                }
            } elseif (empty($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['VDRR.c_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                } else {
                    $where['VDRR.m_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                }
            }
        }

        // 조건 검색(노선 일련번호)
        if (isset($params['vdrr_idx'])) {
            $where['VDRR.id'] = $params['vdrr_idx'];
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

        $sub_query_from = '(' . $in_sql . ') as VDRR';
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
