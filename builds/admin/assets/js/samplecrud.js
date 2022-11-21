/*
| @author jbKim
| @since version 1.0
| @desc 개별 페이지 공통 스크립트
*/
(function (undefined) {
    //사용자 헬퍼 호출 샘플
    // let price = '52,395,100';
    // console.log($.strUtil.unComma(price)); //간략호출(권장사용)
    // console.log(custom.helper.stringUtil.unComma(price)); //풀호출

    //ajax호출 샘플
    $('#btn_ajax_submit').on('click', function (e) {
        //ajax유틸 호출 샘플
        let setData = {
            'idx_no': 1,
            'column_nm': 'comm_del_yn',
            'column_val': 'y'
        };
        let options = {
            url : '/'+ADMIN_URL+'/samplecrud/crudproc/put'
            , data : setData  //{ 'key' : 'value', 'key' : 'value' }
            //, dataType : 'json'
            , wait : true
            //, async : false
            , callBack : function (rdata) {
                if (rdata.result === true) {
                    alert(rdata.msg);
                    document.location.reload();
                }
            }
        };
        $.ajaxUtil.call(options);
    });

    /**
     * 폼 검증 예시
     */
    /*1)간단한 방식*/
    // $('#btn_submit').on('click', function (e) {
    //     if ($(this).is('a') || $(this).is('button')) e.preventDefault();

    //     try {
    //         var $frm_target = $('#samplecrud_form_frm');
    //         console.log($frm_target);

    //         //관리자 타이틀
    //         $.validUtil.simpleInputCheck($('#title'), '관리자 타이틀을 입력하세요.');
    //         //sms 수신동의
    //         if ($frm_target.find('input[name=all_send_mail]:checked').length == 0)
    //             throw new $.validUtil.appException('전체 메일 발송 여부를 체크해 주세요.');

    //         $frm_target.submit();
    //     } catch (e) {
    //         if (e.name == 'AppException') {
    //             alert(e.message);
    //             return false;
    //         }
    //     }
    // });

    /*2)jquery.validate 플러그인 방식 */
    $('#samplecrud_form_frm').is(function () {
        $('#samplecrud_form_frm').validate({
            rules: {
                title: {
                    required: true,
                },
                email_addr: {
                    required: true,
                    emailPattern: true
                },
                add_desc: {
                    required: true
                }
            },
            messages: {
                title: {
                    required: '를 입력하세요.',
                },
                email_addr: {
                    required: '를 입력하세요.',
                },
                add_desc: {
                    required: '를 입력하세요.',
                }
            }
        });
    });

    //기타유틸 호출 샘플
    // $.fnUtil.winOpenCenter('http://naver.com','네이버',1000,500);
    $('#btn_win_popup_open').on('click', function (e) {
        if ($(this).is('a') || $(this).is('button')) e.preventDefault();

        $.fnUtil.winOpenCenter('http://naver.com','네이버 윈도우창 오픈',1000,500);
    });

    //파일 삭제 토글 기능 처리
    let file_cnt = $('input[type="file"]').length + 1;
    $('.btn_image_remove').on('click', function (e) {
        e.preventDefault();

        let file_cloned = $('input[type="file"]').clone(true).prop('id', 'id_input_file_'+file_cnt).get(0);
        file_cnt++;

        $(this)
            .closest('label')
            .find('img').remove().end()
            .find('span').remove().end()
            .find('input[name^="filedelyn"]').val('Y').end()
            .append(file_cloned);
    });
})();