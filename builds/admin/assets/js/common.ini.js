/*
| @author jbKim
| @since version 1.0
| @desc 모듈 공통 스크립트
*/
//공통 스크립트 변수 선언
(function (undefined) {
    if( !('custom' in window) ) window['custom'] = {}
    if( !('helper' in window['custom']) ) window['custom'].helper = {} //custom.helper
    if( !('vars' in window['custom']) ) window['custom'].vars = {} //custom.vars

    custom.vars['ajaxLoaderPath'] = '/builds/'+ADMIN_URL+'/assets/images/ajaxloader/ajax-loader.gif';
    custom.vars['calendarImgPath'] = '/builds/'+ADMIN_URL+'/assets/images/datepicker/icon_calendar_on.png';
})();

//사용자정의 헬퍼 함수 선언
(function ($$ , undefined) {//$$ = custom.helper
    /**
     * 문서유틸 함수
     *
     * @method documentUtil
     */
    $$.documentUtil = {
        //스크롤 위치값을 제공
        scrollTop: function() {
            return document.scrollTop || document.documentElement.scrollTop || document.body.scrollTop
        }
        //문서의 높이값을 제공
        , winHeight: function() {
            return window.innerHeight || document.documentElement.clientHeight;
        }
        //문서에서 가장 큰 높이값을 제공
        , getDocHeight: function() {
            var doc = document;
            return Math.max(Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight), Math.max(doc.body.offsetHeight, doc.documentElement.offsetHeight), Math.max(doc.body.clientHeight, doc.documentElement.clientHeight));
        }
    };

    /**
     * 문자열유틸 함수
     *
     * @method stringUtil
     */
    $$.stringUtil = {
        //콤마 추가 제공
        setComma: function(str) {
            str = String(str);
            return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
        }
        //콤마 제거 제공
        , unComma: function(str) {
            return parseInt(str.replace(/,/g, ''));
        }
        //문자열 카멜케이스 -로 치환 제공
        , unCamelCase: function(str) {
            return str.replace(/([a-z])([A-Z])/g, function(match, c1, c2){ return c1+'-'+c2.toLowerCase() })
        }
        //br태그 제거 제공
        , brTagRemove: function(str) {
            let regex = /<br\s*[\/]?>/gi;
            return str.replace(regex, '');
        }
    };

    /**
     * ajax유틸 함수
     *
     * @method ajaxUtil
     */
    let waiting = {
        start: function(){
            $.blockUI({ message: '<img src='+custom.vars['ajaxLoaderPath']+' alt="Loading..." />' });
        }
        , stop: function(){
            $.unblockUI();
        }
        , startId: function(id){
            $.blockUI({ message: $('#' + id) });
        }
        , stopId: function(){
            $.unblockUI();
        }
    };

    $$.ajaxUtil = {
        call: function(options){
            let wait = false;
            if(options.wait != undefined && options.wait != null){
                wait = options.wait;
            }

            if(wait){
                waiting.start();
            }

            jQuery.ajaxSettings.traditional = true;

            options.contentType = options.contentType || 'application/x-www-form-urlencoded;charset=UTF-8';
            options.type = options.type || 'POST';
            options.dataType = options.dataType || 'json';
            options.async = (options.async == null || options.async) ? true : false;

            $.ajax({
                url : options.url
                , type : options.type
                , dataType : options.dataType
                , contentType : options.contentType
                , cache : false
                , data : options.data
                , async: options.async
            })
            .done(function(data, textStatus, jqXHR){
                if(options.dataType == 'text' || options.dataType == 'html') {
                    try {
                        let obj = eval('('+ data +')');

                        if(obj.exCode != null && obj.exCode != undefined && obj.exCode != ''){
                            alert(obj.exMsg);
                            //waiting.stop();
                        } else {
                            options.callBack(data);
                        }
                    } catch (e) {
                        options.callBack(data);
                    }
                } else {
                    if(data.exCode != null && data.exCode != undefined && data.exCode != ''){
                        alert(data.exMsg);
                        //waiting.stop();
                    } else {
                        options.callBack(data);
                    }
                }
            })
            .fail(function( xhr, status, error ){
                if(xhr.status == 450) {
                    location.replace('/'+ADMIN_URL+'/auth/sign_in.do');
                } else {
                    alert('오류가 발생되었습니다. 관리자에게 문의하십시요.['+xhr.status+']['+error+']');
                }
            })
            .always(function(){
                if(wait){
                    waiting.stop();
                }
            })
            .then(function(data, textStatus, jqXHR ) {

            });
        }
    };
    
    /**
     * form유틸 함수
     *
     * @method formUtil
     */
    $$.formUtil = {
        dynamicForm: function (url, data, method, target) { //동적 form 전송 처리기능
            // url과 data를 입력받음
            if (url && data) {
                // data 는  string 또는 array/object 를 파라미터로 받는다.
                data = typeof data == 'string' ? data : $.param(data);
                // 파라미터를 form의 input으로 만든다.
                var inputs = '';
                $.each(data.split('&'), function () {
                    var pair = this.split('=');
                    inputs += '<input type="hidden" name="' + pair[0] + '" value="' + pair[1] + '" />';
                });
                target = (target != null) ? ' target="' + target + '" ' : '';
                // 폼 전송
                $('<form action="' + url + '" method="' + (method || 'post') + '" ' + target + '>' + inputs + '</form>')
                    .appendTo('body').submit().remove();
            };
        }
        , getParseHtml: function (url, selectorId, method) { //문서 파싱 기능 제공
            $.ajax({
                type: method || 'POST',
                url: url
            }).done(function (rdata) {
                var _html = $.parseHTML(rdata);
                $('#' + selectorId).empty().append(_html);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                alert('화면 호출에 실패하였습니다.');
            }).always(function (rdata) {
            });
        }
    };

    /**
     * 폼검증유틸 함수
     *
     * @method validateUtil
     */
    $$.validateUtil = {
        appException: function (message) {
            this.message = message;
            this.name = 'AppException';
        }
        , simpleInputCheck: function ($element, errMsg) {
            if($element.length === 0) {
                throw new $$.validateUtil.appException(errMsg);
            } else if($element.val().trim().length === 0) {
                $element.focus();
                throw new $$.validateUtil.appException(errMsg);
            } else if($element.val().trim() === $element.attr('placeholder')) {
                $element.focus();
                throw new $$.validateUtil.appException(errMsg);
            }
        }
    };

    /**
     * 기타유틸 함수
     *
     * @method functionUtil
     */
    $$.functionUtil = {
        //윈도우 팝업창 띄우기 제공
        winOpen: function (url, title, width, height) {
            window.open(url, title, 'top=0, left=0, width=' + width + ', height=' + height + ', toolbar=no, menubar=no, scrollbars=no, resizable=no');
        }
        //윈도우 팝업창 중앙 띄우기 제공(메인 모니터에서만 가능)
        , winOpenCenter: function (url, title, w, h) {
            let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            let dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

            let width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            let left = ((width / 2) - (w / 2)) + dualScreenLeft;
            let top = ((height / 2) - (h / 2)) + dualScreenTop;

            let newWindow = window.open(url, title, 'top=' + top + ', left=' + left + ', width=' + w + ', height=' + h + ', toolbar=no, menubar=no, scrollbars=yes, resizable=no');

            if (window.focus) {
                newWindow.focus();
            }
        }
        //즐겨찾기 기능 제공
        , addFavorite: function () {
            let title = document.title;
            let url = document.location.href;

            if (window.chrome) { // Google Chrome
                alert('Ctrl+D키를 누르시면 즐겨찾기에 추가하실 수 있습니다.');
            } else if (window.external) { // Internet Explorer(document.all은 ie11처리안됨)
                try {
                    window.external.AddFavorite(url, title);
                } catch (e) {
                }
            } else if (window.sidebar && window.sidebar.addPanel) { // Firefox
                window.sidebar.addPanel(url, title, '');
            } else if (window.opera && window.print) { // Opera
                let elem = document.createElement('a');
                elem.setAttribute('href', url);
                elem.setAttribute('title', title);
                elem.setAttribute('rel', 'sidebar');
                elem.click();
            }
        }
        //IE버전 확인 기능 제공
        , getInternetExplorerVersion: function () {
            let rv = -1;
            if (navigator.appName == 'Microsoft Internet Explorer') { //ie 7,8,9,10
                let ua = navigator.userAgent;
                let re = new RegExp('MSIE ([0-9]{1,}[\.0-9]{0,})');
                if (re.exec(ua) != null)
                    rv = parseFloat(RegExp.$1);
            } else if (navigator.appName == 'Netscape') { //ie 11
                let ua = navigator.userAgent;
                let re = new RegExp('Trident/.*rv:([0-9]{1,}[\.0-9]{0,})');
                if (re.exec(ua) != null)
                    rv = parseFloat(RegExp.$1);
            }
            return rv;
        }
    };

    $.domUtil = $$.documentUtil;    //문서유틸
    $.strUtil = $$.stringUtil;      //문자열유틸
    $.ajaxUtil = $$.ajaxUtil;       //ajax유틸
    $.frmUtil = $$.formUtil;        //form유틸
    $.validUtil = $$.validateUtil;  //폼검증유틸
    $.fnUtil = $$.functionUtil;     //기타유틸
})(custom.helper);

(function ($ , undefined) {
    /**
     * 공통 함수
     */
    $.fn.center = function () { //화면 중앙에서 뜨도록 처리
        this.css('position', 'absolute');
        this.css('top', ($(window).height() - this.height()) / 2 + $(window).scrollTop() + 'px');
        this.css('left', ($(window).width() - this.width()) / 2 + $(window).scrollLeft() + 'px');
        return this;
    };

    $.fn.focusTextToEnd = function () { //포커스를 텍스트 맨 뒤로 이동 처리
        this.focus();
        let $thisVal = this.val();
        this.val('').val($thisVal);
        return this;
    };
    
    /**
     * validate 기본 포맷 지정
     *
     * @method validator
     */
    (function () {
        if ($.validator != null) {
            $.extend($.validator.messages, {
                required: '반드시 입력해야 합니다.',
                remote: '수정 바랍니다.',
                email: '이메일 주소를 올바로 입력하세요.',
                url: 'URL을 올바로 입력하세요.',
                date: '날짜가 잘못 입력됐습니다.',
                dateISO: 'ISO 형식에 맞는 날짜로 입력하세요.',
                number: '숫자만 입력하세요.',
                digits: '숫자(digits)만 입력하세요.',
                creditcard: '올바른 신용카드 번호를 입력하세요.',
                equalTo: '값이 서로 다릅니다.',
                accept: '승낙해 주세요.',
                maxlength: $.validator.format('{0}글자 이상은 입력할 수 없습니다.'),
                minlength: $.validator.format('적어도 {0}글자는 입력해야 합니다.'),
                rangelength: $.validator.format('{0}글자 이상 {1}글자 이하로 입력해 주세요.'),
                range: $.validator.format('{0}에서 {1} 사이의 값을 입력하세요.'),
                max: $.validator.format('{0} 이하로 입력해 주세요.'),
                min: $.validator.format('{0} 이상으로 입력해 주세요.')
            });

            //alert박스로 보이게 할때 사용함
            $.validator.setDefaults({
                onkeyup: false,
                onclick: false,
                onfocusout: false,
                showErrors: function (errorMap, errorList) {
                    if (this.numberOfInvalids()) { // 에러가 있을 때만
                        var title = $(errorList[0].element).attr('placeholder') || $(errorList[0].element).attr('title') || $(errorList[0].element).attr('name');
                        alert('[' + title + ']' + errorList[0].message);
                        $(errorList[0].element).focus();
                    }
                }
            });

            $.validator.addMethod('mobilephoneKO', function (phone_number, element) {
                return this.optional(element) || phone_number.length > 9 && phone_number.match(/^\(?(\d{3})\)?[- ]?(\d{3,4})[- ]?(\d{4})$/);
            }, '올바른 휴대폰 형식을 입력하세요.');

            $.validator.addMethod('emailPattern', function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/i.test(value);
            }, '유효한 이메일 주소 형식이 아닙니다.');

            $.validator.addMethod('alphanumeric', function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, '아이디는 알파벳과 숫자만 사용가능합니다.');

            $.validator.addMethod('useridPattern', function (value, element) {
                return this.optional(element) || /^(?=.*\d)(?=.*[a-zA-Z]).{8,16}$/i.test(value);
            }, '아이디는 영문,숫자를 한글자 이상 포함해야 합니다.');

            $.validator.addMethod('usernickPattern', function (value, element) {
                return this.optional(element) || !(/[ `~!@#$%^*()\-_=+\\\|\[\]{};:\'",.<>\/?]/gi.test(value));
            }, '닉네임은 특수문자및 공백을 포함할 수 없습니다.');

            $.validator.addMethod('trimPattern', function (value, element) {
                return this.optional(element) || !(/[ ]/gi.test(value));
            }, '비밀번호는 공백을 포함할 수 없습니다.');

            $.validator.addMethod('passwordPattern', function (value, element) {
                return this.optional(element) || /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^*()\-_=+\\\|\[\]{};:\'",.<>\/?]).{8,16}$/i.test(value);
            }, '비밀번호는 영문,숫자,특수문자를 한글자 이상 포함해야 합니다.');

            $.validator.addMethod('passwordPattern2', function (value, element) {
                return this.optional(element) || /^(?=.*\d)(?=.*[a-zA-Z]).{8,16}$/i.test(value);
            }, '비밀번호는 영문,숫자를 한글자 이상 포함해야 합니다.');
        }
    })();

    /**
     * datepicker 달력 셋팅 지정
     *
     * @method datepicker
     */
     (function () {
        /**
         * UI datepicker 한글 지정, 기본포맷지정
         */
        $(function (a) {
            try {
                a.datepicker.regional['ko'] = {
                    closeText: '닫기',
                    prevText: '이전달',
                    nextText: '다음달',
                    currentText: '오늘',
                    monthNames: ['1월(JAN)', '2월(FEB)', '3월(MAR)', '4월(APR)', '5월(MAY)', '6월(JUN)',
                        '7월(JUL)', '8월(AUG)', '9월(SEP)', '10월(OCT)', '11월(NOV)', '12월(DEC)'],
                    monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월',
                        '7월', '8월', '9월', '10월', '11월', '12월'],
                    dayNames: ['일', '월', '화', '수', '목', '금', '토'],
                    dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
                    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                    weekHeader: 'Wk',
                    dateFormat: 'yy-mm-dd',
                    firstDay: 0,
                    isRTL: false,
                    showMonthAfterYear: true,
                    changeMonth: true,
                    changeYear: true,
                    showOtherMonths: true,
                    selectOtherMonths: true
                    //yearSuffix: '년'
                };
                a.datepicker.setDefaults($.datepicker.regional['ko']);
            } catch (e) {
            }
        });

        // 달력 이미지 지정(리스트용)
        $('#sc_startdt').is(function () {
            $('#sc_startdt').datepicker({
                // 폰트아이콘의 날짜버튼을 사용하여 주석처리함
                // showOn: 'both', buttonImage: custom.vars['calendarImgPath'], buttonImageOnly: true,
                onClose: function (selectedDate) {
                    if (!moment(selectedDate, 'YYYY-MM-DD', true).isValid()) {
                        $('#sc_startdt').val(moment(new Date()).format('YYYY-MM-DD'));
                    }
					//제한 없음
					$('#sc_enddt').datepicker('option', 'minDate', selectedDate);
					//기간 선택 제한(한달)
					let minDate = $(this).datepicker('getDate');
					let maxDate = $(this).datepicker('getDate');
					if (minDate) {
						minDate.setDate(minDate.getDate());
						maxDate.setDate(maxDate.getDate() + 30);
					}
					$('#sc_enddt').datepicker('option', 'minDate', minDate || 1);
					$('#sc_enddt').datepicker('option', 'maxDate', maxDate || 1);
                }
            });
        });

        $('#sc_enddt').is(function () {
            $('#sc_enddt').datepicker({
                // 폰트아이콘의 날짜버튼을 사용하여 주석처리함
                // showOn: 'both', buttonImage: custom.vars['calendarImgPath'], buttonImageOnly: true,
                onClose: function (selectedDate) {
                    if (!moment(selectedDate, 'YYYY-MM-DD', true).isValid()) {
                        $('#sc_enddt').val(moment(new Date()).format('YYYY-MM-DD'));
                    }
					//제한 없음
					$('#sc_startdt').datepicker('option', 'maxDate', selectedDate);
					//기간 선택 제한(한달)
					let maxDate = $(this).datepicker('getDate');
					let minDate = $(this).datepicker('getDate');
					if (maxDate) {
						maxDate.setDate(maxDate.getDate());
						minDate.setDate(minDate.getDate() - 30);
					}
					$('#sc_startdt').datepicker('option', 'maxDate', maxDate || 1);
					$('#sc_startdt').datepicker('option', 'minDate', minDate || 1);
                }
            });
        });

        //달력 폰트 크키 지정
        $('div.ui-datepicker').css('fontSize', '12px');
    })();

    /**
     * datepicker 달력 셋팅 지정
     *
     * @method datepicker
     */
    (function () {
		let first_plus = 0;
		let last_plus = 1;
        $(document).on('click', '.select_date', function () { //기간 선택 버튼에 따른 날짜 셋팅 처리
		 	let now = new Date();
			let firstDate, lastDate;
			
            switch ($(this).data('datetype')) {
				case 'yesterday' :
					$(this).closest('td').find('input:eq(0)').val(getDate(1));
					$(this).closest('td').find('input:eq(1)').val(getDate(1));
					break;
                case 'today' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(0));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
                case '3day' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(3));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
                case '1week' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(7));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
                case '1month' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(30));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
                case '3month' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(90));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
					break;
                case '6month' :
                    $(this).closest('td').find('input:eq(0)').val(getDate(180));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
				case 'prevmonth' :
					first_plus--;
					last_plus--;
					firstDate = new Date(now.getFullYear(), now.getMonth()+first_plus, 1);
					lastDate = new Date(now.getFullYear(), now.getMonth()+last_plus, 0);
					$(this).closest('td').find('input:eq(0)').val(firstDate.getFullYear() + '-' + zerofill(firstDate.getMonth()+1) + '-' + zerofill(firstDate.getDate()));
					$(this).closest('td').find('input:eq(1)').val(lastDate.getFullYear() + '-' + zerofill(lastDate.getMonth()+1) + '-' + zerofill(lastDate.getDate()));
					break;
				case 'nextmonth' :
					first_plus++;
					last_plus++;
					firstDate = new Date(now.getFullYear(), now.getMonth()+first_plus, 1);
					lastDate = new Date(now.getFullYear(), now.getMonth()+last_plus, 0);
					$(this).closest('td').find('input:eq(0)').val(firstDate.getFullYear() + '-' + zerofill(firstDate.getMonth()+1) + '-' + zerofill(firstDate.getDate()));
					$(this).closest('td').find('input:eq(1)').val(lastDate.getFullYear() + '-' + zerofill(lastDate.getMonth()+1) + '-' + zerofill(lastDate.getDate()));
					break;
                case 'all' :
                    $(this).closest('td').find('input:eq(0)').val('');
                    $(this).closest('td').find('input:eq(1)').val('');
                    break;
                default :
                    $(this).closest('td').find('input:eq(0)').val(getDate(0));
                    $(this).closest('td').find('input:eq(1)').val(getDate(0));
                    break;
            }
        });
    })();    

    /**
     * 즐겨찾기 추가 기능
     */
    (function () {
        $(document).off('click', '.addFavorite')
            .on('click', '.addFavorite', function (e) {
            if ($(this).is('a') || $(this).is('button')) e.preventDefault();

            //IE버전 확인
            //let ie_ver = $.fnUtil.getInternetExplorerVersion();
            $.fnUtil.addFavorite();
        });
    })();
    
    /**
     * 체크박스 전체선택/해제 기능
     */
     (function () {
        $(document).off('click', '#all_check')
            .on('click', '#all_check', function () {
            $('.check').not(':disabled').prop('checked', $(this).is(':checked'));
        });
    })();    
})(jQuery);

/**
 * 자바스크립트 Date 객체를 Time 스트링으로 변환
 * parameter date: JavaScript Date Object
 */
 function toTimeString(date) {
    let year = date.getFullYear();
    let month = date.getMonth() + 1; // 1월=0,12월=11이므로 1 더함
    let day = date.getDate();

    if (('' + month).length == 1) {
        month = '0' + month;
    }
    if (('' + day).length == 1) {
        day = '0' + day;
    }

    return ('' + year + month + day)
}

/**
 * 현재 시각을 Time 형식으로 리턴
 */
function getCurrentTime(date) {
    return toTimeString(new Date(date));
}

/**
 * 현재 年을 YYYY형식으로 리턴
 */
function getYear(date) {
    return getCurrentTime(date).substr(0, 4);
}

/**
 * 현재 月을 MM형식으로 리턴
 */
function getMonth(date) {
    return getCurrentTime(date).substr(4, 2);
}

/**
 * 현재 日을 DD형식으로 리턴
 */
function getDay(date) {
    return getCurrentTime(date).substr(6, 2);
}

/**
 * 현재 날짜를 YYYY-MM-DD형식으로 리턴
 */
function getDate(day) {
    let d = new Date();
    let dt = d - day * 24 * 60 * 60 * 1000;
    return getYear(dt) + '-' + getMonth(dt) + '-' + getDay(dt);
}

/**
 * 현재 月 또는 日을 두자리 형식으로 리턴
 */
function zerofill(i) {
	return (i < 10 ? '0' : '') + i;
}