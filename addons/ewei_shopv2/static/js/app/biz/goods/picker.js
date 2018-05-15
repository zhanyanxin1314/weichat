define(['core', 'tpl'], function(core, tpl) {
    var modal = {
        goodsid: 0,
        goods: [],
        option: false,
        specs: [],
        options: [],
        params: {
            titles: '',
            optionthumb: '',
            split: ';',
            option: false,
            total: 1,
            optionid: 0,
            onSelected: false,
            onConfirm: false,
            autoClose: true
        }
    };
    modal.open = function(params) {
        modal.params = $.extend(modal.params, params || {});
        if (modal.goodsid != params.goodsid) {
            modal.goodsid = params.goodsid;
            var obj = {
                id: params.goodsid
            };
            core.json('goods/picker', obj, function(ret) {
                if (ret.status == 0) {
                    FoxUI.toast.show('未找到商品!');
                    return
                }	
                modal.containerHTML = tpl('option-picker', ret.result);
                modal.goods = ret.result.goods;
                if (modal.goods.unit == '') {
                    modal.goods.unit = '件'
                }
                modal.show()
            }, true, false)
        } else {
            modal.show()
        }
    };
    modal.close = function() {
        modal.container.close()
    };
    modal.init = function() {
        $('.closebtn', modal.container.container).unbind('click').click(function() {
            modal.close()
        });
        $('.fui-mask').unbind('click').click(function() {
            modal.close()
        });
        modal.params.total = 1
        $('.buybtn', modal.container.container).unbind('click').click(function() {
               location.href = core.getUrl('order/create', {
                    id: modal.goods.id,
                    total: modal.params.total
                })
	    if (modal.params.autoClose) {
                modal.close()
            }
        });
        $('.confirmbtn', modal.container.container).unbind('click').click(function() {
            if (modal.params.onConfirm) {
                modal.params.total = parseInt($('.num', modal.container.container).val());
                modal.params.onConfirm(modal.params.total)
            }
            if (modal.params.autoClose) {
                modal.close()
            }
        });
        var height = $(document.body).height() * 0.8;
        var optionsHeight = height - $('.option-picker-cell').outerHeight() - $('.option-picker .fui-navbar').outerHeight();
        modal.container.container.find('.option-picker').css('height', height);
        modal.container.container.find('.option-picker .option-picker-options').css('height', optionsHeight)
    };
    modal.show = function() {
        modal.container = new FoxUIModal({
            content: modal.containerHTML,
            extraClass: "picker-modal"
        });
        modal.init();
        $('.confirmbtn', modal.container.container).show()
        modal.container.show();
    };
    return modal
});
