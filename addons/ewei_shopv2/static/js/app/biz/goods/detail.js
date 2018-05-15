define(['core', 'tpl', 'biz/goods/picker'], function(core, tpl, picker) {
    var modal = {};
    modal.init = function(params) {
        modal.goodsid = params.goodsid;
        modal.total = 1;
            FoxUI.tab({
                container: $('#tab'),
                handlers: {
                    tab1: function() {
                        $('.basic-block').show();
                        modal.hideDetail();
                        $(".fui-navbar").show();
                        $(".look-basic").hide()
                    },
                    tab2: function() {
                        modal.showDetail();
                        $(".fui-navbar").show();
                        $(".look-basic").hide()
                    }
                }
            })
        $(".bottom-buttons .buybtn").click(function() {
                modal.optionPicker('buy')
        });
        core.showImages('.goods-swipe .fui-swipe-item img');
    };
    modal.getDetail = function() {
        if ($('.detail-block').find('.content-block').html() != '') {
            return
        }
        FoxUI.loader.show('mini');
        $.ajax({
            url: core.getUrl('goods/detail/get_detail', {
                id: modal.goodsid
            }),
            cache: true,
            success: function(html) {
                FoxUI.loader.hide();
                var detailHeight = $('.detail-block').css('height');
                $('.detail-block').find('.content-block').css('height', detailHeight).html(html);
                setTimeout(function() {
                    $('.detail-block').lazyload();
                    $('.detail-block').find('.content-block').css('height', 'auto');
                    core.showImages('.content-block img');
                    var $html = $(html).find('img');
                    if ($html.length > 0) {
                        for (var i = 0, len = $html.length; i < len; i++) {
                            $html[i].onerror = function() {
                                var $this = $(this);
                                var data_lazy = $this.attr('data-lazy');
                                if (!$this.attr('check-src') && (data_lazy.indexOf('http://') > -1 || data_lazy.indexOf('https://') > -1)) {
                                    var src = data_lazy.indexOf(modal.attachurl_local) == -1 ? data_lazy.replace(modal.attachurl_remote, modal.attachurl_local) : data_lazy.replace(modal.attachurl_local, modal.attachurl_remote);
                                    $this.attr('data-lazy', src);
                                    $this.attr('check-src', true)
                                }
                            }
                        }
                    }
                }, 1000);
            }
        })
    };
    modal.showDetail = function() {
        $('.basic-block').hide();
        modal.getDetail();
        $('.detail-block').transition(300).addClass('in').transitionEnd(function() {
            $('.detail-block').transition('')
        })
    };
    modal.hideDetail = function() {
        $('.basic-block').show();
        $('.detail-block').transition(300).removeClass('in').transitionEnd(function() {
            $('.detail-block').transition('')
        })
    };
    modal.optionPicker = function(action) {
        picker.open({
            goodsid: modal.goodsid,
            total: modal.total,
            onConfirm: function(total) {
                modal.total = total;
                if (action == 'buy') {
                            picker.close();
                            $.router.load(core.getUrl('order/create', {
                                id: modal.goodsid,
                                total: modal.total
                            }), false)
                } else {
                    picker.close()
                }
            }
        })
    };
    return modal
});
