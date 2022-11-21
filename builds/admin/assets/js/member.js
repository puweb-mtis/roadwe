/**
 * 회원 공통 스크립트
 */
$(function () {
    if (METHOD_NAME === 'index') { //리스트
        /**
         * 폼 검색값 초기화(폼 type reset의 경우 입력한 값만 초기화 됨) => 기능 재확인 예정
         */
        $('#btn_reset').on('click', function (e) {
            let $this = $(this);
            if ($this.is('a') || $this.is('button')) e.preventDefault();
            
            let $frmobj = $this.closest('form')[0];
            let selector = $($frmobj).find(':input').not('.select_date, :button');
            $(selector).each(function(index, item) {
                if ($(item)[0].tagName === 'INPUT') {
                    $(item).val('');
                } else if ($(item)[0].tagName === 'SELECT') {
                    $(item).find('option:first').attr('selected', 'selected');
                }
            });
        });        
    } else { //상세
        $('#passwd_chg').prop('checked', false);
        /**
         * 비밀번호 변경(등록/수정)영역 토글 처리
         */
        $('#passwd_chg').on('click', function () {
            if ($(this).prop('checked')) {
                $('#password').removeAttr('disabled');
                $('#re_password').removeAttr('disabled');
                $('#password').focus();
            } else {
                $('#password').val('');
                $('#re_password').val('');
                $('#password').attr('disabled', 'disabled');
                $('#re_password').attr('disabled', 'disabled');
            }
        });
    }
});