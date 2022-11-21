<?php defined('BASEPATH') or exit('No direct script access allowed');

class MemberModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->table = [
            // 'idx' => 'acmg_idx',
            // 'driving' => 'account_manager',
            'idx' => 'id',
            'driving' => 'member',
            'driven' => '',
            'prefix' => 'ACMB'
        ];

        $this->base_sql = "
            ACMB.*
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

        // 기본 조건 선언
        if (isset($params['sec_default']) && is_bool($params['sec_default'])) {
            $where['ACMB.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
            $where['ACMB.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)
        }
        // 기본 조건 추가 : 정상회원 검색 조건 지정(탈퇴,휴면 제외)
        // 추후 테이블을 분리하면 다른 조건선정 필요
        if (isset($params['sec_default_use_status']) && is_bool($params['sec_default_use_status'])) {
            $where['ACMB.use_status ='] = 'YES';   // 회원상태 : 회원탈퇴 플래그 (회원 사용여부 용도) (YES:사용중,NO:탈퇴)
        }



        // 폼 조건 검색(키워드&검색어)
        if (isset($params['sec_column']) && isset($params['sec_keyword'])) {
            $this->db->like('ACMB.' . $params['sec_column'], $params['sec_keyword']);
        }

        // 폼 조건 검색(가입일&최종방문일)
        if (isset($params['sec_st_date']) || isset($params['sec_ed_date'])) {
            $stime = ' 00:00:00';
            $etime = ' 23:59:59';

            if (isset($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $this->db->where("ACMB.c_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                } else {
                    // acmb_last_date -> 기존필드 사용(login_datetime)
                    $this->db->where("ACMB.login_datetime BETWEEN '{$params['sec_st_date']}{$stime}' AND '{$params['sec_ed_date']}{$etime}' ");
                }
            } elseif (isset($params['sec_st_date']) && empty($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $where['ACMB.c_datetime >='] = "{$params['sec_st_date']}{$stime}";
                } else {
                    $where['ACMB.login_datetime >='] = "{$params['sec_st_date']}{$stime}";
                }
            } elseif (empty($params['sec_st_date']) && isset($params['sec_ed_date'])) {
                if ($params['sec_date_type'] === 'c_datetime') {
                    $where['ACMB.c_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                } else {
                    $where['ACMB.login_datetime <='] = "{$params['sec_ed_date']}{$etime}";
                }
            }
        }

        // 폼 조건 검색(회원구분) - PU:일반회원, DV:운행기사, RM:동승매니저
        if (isset($params['sec_member_type'])) {
            $where['ACMB.member_type'] = $params['sec_member_type'];
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

        $sub_query_from = '(' . $in_sql . ') as ACMB';
        $this->db->select("{$this->base_sql}");
        $this->db->from($sub_query_from);

        $result['list'] = $this->db->get()->result_array();
        $result['rows'] = $res['rows'];
        return $result;
    }

    /*
     * 회원 요약정보 통계
     * ************************************************************************/
    public function getMemberStatistics()
    {
        $this->db->from($this->table['driving'] . ' as ' . $this->table['prefix']);
        $this->db->select('COUNT(*) AS cnt_total');
        $this->db->select('COUNT(CASE WHEN member_type = \'PU\' THEN 1 END) AS cnt_pu');
        $this->db->select('COUNT(CASE WHEN member_type = \'RM\' THEN 1 END) AS cnt_rm');
        $this->db->select('COUNT(CASE WHEN member_type = \'DV\' THEN 1 END) AS cnt_dv');

        // 기본 조건 선언
        $where['ACMB.comm_use_yn ='] = 'Y';   // 사용여부(Y:사용중,N:미사용)
        $where['ACMB.comm_del_yn ='] = 'N';   // 삭제여부(Y:삭제됨,N:미삭제)

        return $this->db->get_where(null, $where)->row_array();
    }

    /*
     * 관리자
     * ************************************************************************/

    /*
     * 사용자
     * ************************************************************************/
}
