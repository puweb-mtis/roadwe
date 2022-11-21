<?php defined('BASEPATH') or exit('No direct script access allowed');

class ManagerModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = [
            // 'idx' => 'acmg_idx',
            // 'driving' => 'account_manager',
            'idx' => 'id',
            'driving' => 'admin',
            'driven' => 'business',
            'prefix' => 'ACMG'
        ];

        $this->base_sql = "
            ACMG.*
            , IF(ACMG.super_status = 'YES','대표관리자','부관리자') super_txt
            , IF(ACMG.use_status = 'YES','접속허용','접속차단') status_txt
            , BS.business_type
            , (
                IFNULL((SELECT team_name FROM business_team BST WHERE BST.business_id = ACMG.business_id AND BST.id = ACMG.business_team_id), '미지정')
            ) AS business_team_name
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
        $this->db->join($this->table['driven'] . ' as BS', 'BS.id = ' . $this->table['prefix'] . '.business_id', 'LEFT');

        // 기본 조건 선언
        if (isset($params['sec_default']) && is_bool($params['sec_default'])) {
            $where['ACMG.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
            $where['ACMG.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)
        }

        // 폼 조건 검색(키워드&검색어)
        if (isset($params['sec_column']) && isset($params['sec_keyword'])) {
            $this->db->like('ACMG.' . $params['sec_column'], $params['sec_keyword']);
        }

        // 폼 조건 검색(가입일&최종방문일)
        if (isset($params['sec_st_date']) || isset($params['sec_ed_date'])) {
            $stime = ' 00:00:00';
            $etime = ' 23:59:59';

            if (isset($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $this->db->where("ACMG.c_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                } else {
                    $this->db->where("ACMG.m_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                }
            } elseif (isset($params['sec_st_date']) && empty($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['ACMG.c_datetime >='] = "{$params['sec_st_date']}{$stime}";
                } else {
                    $where['ACMG.m_datetime >='] = "{$params['sec_st_date']}{$stime}";
                }
            } elseif (empty($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'comm_reg_date') {
                    $where['ACMG.c_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                } else {
                    $where['ACMG.m_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                }
            }
        }

        // 폼 조건 검색(관리자 구분) - 대표관리자 여부 YES:맞음, NO:아님
        if (isset($params['sec_super_status_cd'])) {
            $where['ACMG.super_status'] = $params['sec_super_status_cd'];
        }

        // 폼 조건 검색(접속허용여부) - 접속 허용 여부 YES:맞음, NO:아님
        if (isset($params['sec_use_status_cd'])) {
            $where['ACMG.use_status'] = $params['sec_use_status_cd'];
        }

        // 조건 검색(회원일련번호)
        if (isset($params['acmg_idx'])) {
            $where['ACMG.id'] = $params['acmg_idx'];
        }

        // 조건 검색(회원아이디)
        if (isset($params['acmg_id'])) {
            $where['ACMG.account'] = $params['acmg_id'];
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

        $sub_query_from = '(' . $in_sql . ') as ACMG';
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
