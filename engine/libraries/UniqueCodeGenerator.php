<?php if (!defined("BASEPATH")) exit("No direct script access allowed");
/**
 * 고유코드 생성
 * 
 * @author 
 * @since version 0.1
 */
class UniqueCodeGenerator
{
    /**
     * CI
     *
     * @var Object
     */
    public $CI = null;

    /**
     * 최대 제한 횟수
     *
     * @var int
     */
    protected $numberOfTimesLimit = 5;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('form_validation');
    }

    /**
     * _unique
     *
     * @param  closure $fnCode
     * @param  string $rule
     * @return string
     */
    protected function _unique(closure $fnCode, string $rule)
    {
        for ($cnt = 0, $max = $this->numberOfTimesLimit; $cnt < $max; $cnt++) {
            $code = $fnCode();
            $this->CI->form_validation->set_data(['code' => $code]);
            $this->CI->form_validation->set_rules('code', 'code', $rule);
            if ($this->CI->form_validation->run() !== FALSE) {
                return $code;
            }
        }
        return '';
    }

    /**
     * 회원 고유번호
     *
     * @return string
     */
    public function getCodeOfMember()
    {
        return $this->_unique(
            fn () => strtoupper(sprintf('PU%s%07x', date('ym'), mt_rand(0, 0xfffffff))),
            'required|is_unique[member.member_code]'
        );
    }

    /**
     * 관리자 고유번호
     *
     * @return string
     */
    public function getCodeOfAdminAccount()
    {
        return $this->_unique(
            fn () => strtoupper(sprintf('AD%s%07x', date('ym'), mt_rand(0, 0xfffffff))),
            'required|is_unique[admin.admin_code]'
        );
    }
}
