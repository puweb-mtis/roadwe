//공통 스크립트 변수 선언
(function(undefined) {
    if( !('crud' in window['custom']) ) window['custom'].crud = {} //custom.crud
})();

//사용자정의 CRUD 함수 선언
(function($$ , undefined) {//$$ = custom.crud
    $$.crudUtil = {
        /*
         * ex) data-crud="buttons" data-crud-method="deleteall" data-crud-seqname="spmg_idx"
         * *data-crud="buttons" //CRUD버튼 이벤트 지정
         * *data-crud-method="deleteall" //CRUD버튼 이벤트 타입 지정
         * *data-crud-seqname="spmg_idx" //리스트에서 체크할 input 체크박스 명
         * data-crud-altmsg="" //출력할 체크에러 메시지
         */
        deleteAll: function ($this, $frm, options) {
            let defaults = {
                seqName: $this.data('crud-seqname') || 'pk_idx',
                altMsg: $this.data('crud-altmsg') || '처리 항목을 선택해주세요.',
                action: '/' + ADMIN_URL + '/' + CONTROLLER_NAME + '/crudproc/deleteall' || ''
            };
            let settings = $.extend({}, defaults, options );

            let checked = $('input[name^="'+settings.seqName+'"]:checked');
            if (checked.length === 0) {
                alert(settings.altMsg);
                return;
            } else {
                let action = settings.action;
                let set_data = $frm.serialize();
                let ajax_options = {
                    url : action
                    , data :  set_data
                    , callBack : function (rdata) {
                        if (rdata.result == true) {
                            alert(rdata.msg);
                            document.location.reload();
                        }
                    }
                };
                $.ajaxUtil.call(ajax_options);
            }
        },
        /*
         * ex) data-crud="buttons" data-crud-method="put" data-crud-pkidx="{row.spmg_idx}" data-crud-columnnm="comm_use_yn" data-crud-columnval="Y"
         * *data-crud="buttons" //CRUD버튼 이벤트 지정
         * *data-crud-method="put" //CRUD버튼 이벤트 타입 지정
         * *data-crud-pkidx="{row.spmg_idx}" //처리할 데이터 일련번호
         * *data-crud-columnnm="comm_use_yn" //처리할 데이터 컬럼명
         * *data-crud-columnval="Y" //처리할 데이터 값
         * data-crud-confmsg="" //출력할 confirm창 메세지
         */
        put: function ($this, $frm, options) {
            let defaults = {
                pkIdx: $this.data('crud-pkidx') || '',
                confMsg: $this.data('crud-confmsg') || '정말 처리 하시겠습니까?',
                action: '/' + ADMIN_URL + '/' + CONTROLLER_NAME + '/crudproc/put' || ''
            };
            let settings = $.extend({}, defaults, options );

            if (confirm(settings.confMsg)) {
                let action = settings.action;

                let column_nm = $this.data('crud-columnnm') || null;
                let column_val = $this.data('crud-columnval') || null;
                let set_data = {
                    'pk_idx': settings.pkIdx,
                    'column_nm': column_nm,
                    'column_val': column_val
                };

                let ajax_options = {
                    url : action
                    , data :  set_data
                    , callBack : function (rdata) {
                        if (rdata.result) {
                            alert(rdata.msg);
                            window.location.reload();
                        } else {
                            alert(rdata.msg);
                            return false;
                        }
                    }
                };
                $.ajaxUtil.call(ajax_options);
            }
        },
        /*
         * ex) data-crud="buttons" data-crud-method="beforeconfirm" data-crud-confmsg="선택된 항목을 정말 삭제 처리 하시겠습니까?"
         * *data-crud="buttons" //CRUD버튼 이벤트 지정
         * *data-crud-method="beforeconfirm" //CRUD버튼 이벤트 타입 지정
         * *data-crud-confmsg="" //출력할 confirm창 메세지
         * *data-crud-action="" //처리단 경로 지정
         */
        beforeConfirm: function ($this) {
            let confMsg = $this.data('crud-confmsg') || '선택된 항목을 정말 처리하시겠습니까?';
            if (confirm(confMsg)) {
                action = $this.attr('href') || $this.data('crud-action');
                document.location.href = action;
            }
        }
    };

    $.crudUtil = $$.crudUtil; //CRUD유틸
})(custom.crud);

//문서 사용준비 완료
jQuery(function($) {
    /**
     * 해당 페이지에서 CRUD 공통 처리
     */
    $(document).off('click', '[data-crud="buttons"]')
        .on('click', '[data-crud="buttons"]', function (e) {
            let $this = $(this);
            if ($this.is('a') || $this.is('button')) e.preventDefault();

            let $frmobj = $this.closest('form');
            let $crudmode = (typeof ($this.data('crud-method')) !== 'undefined') ? $this.data('crud-method') : 'undefined';

            if($crudmode === 'deleteall') { //리스트 전체 삭제
                $.crudUtil.deleteAll($this,$frmobj);
            } else if($crudmode === 'put') { //개별 컬럼 업데이트 처리
                $.crudUtil.put($this,$frmobj);
            } else if($crudmode === 'beforeconfirm') { //단일 삭제 confirm창 처리
                $.crudUtil.beforeConfirm($this);
            } else {
                alert('처리 타입을 지정해주세요.');
                return;
            }
        });

    /**
     * 리스트 보기 갯수 공통 처리
     */
    $(document).off('change', 'select[id=list-limit]')
        .on('change', 'select[id=list-limit]', function () {
            let limit = $(this).val();
            let $target = $('form[id$="search_frm"]');
            let frm_nm = $target.attr('id');
            let action_url = $target.attr('action');
            let method = $target.attr('method');
            let data = $('#'+frm_nm).serialize()+'&limit='+limit;
            $.frmUtil.dynamicForm(action_url, data, method);
    });
});
