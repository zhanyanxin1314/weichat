define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		var checked_applytype = $('input[name="1"]:checked').data('type');
		if (checked_applytype == 2) {
			$('.ab-group').show();
			$('.ab-group2').hide();
			$('.alipay-group').show();
			$('.bank-group').hide()
		} else if (checked_applytype == 3) {
			$('.ab-group2').show();
			$('.ab-group').hide();
			$('.alipay-group').hide();
			$('.bank-group').show()
		} else {
			$('.ab-group').hide();
			$('.alipay-group').hide();
			$('.bank-group').hide()
		}
		$('.applyradio').click(function() {
			var applytype = $(this).find(".fui-radio").data("type");
			if (applytype == 2) {
				$('.ab-group').show();
				$('.ab-group2').hide();
				$('.alipay-group').show();
				$('.bank-group').hide()
			} else if (applytype == 3) {
				$('.ab-group2').show();
				$('.ab-group').hide();
				$('.alipay-group').hide();
				$('.bank-group').show()
			} else {
				$('.ab-group2').hide();
				$('.ab-group').hide();
				$('.alipay-group').hide();
				$('.bank-group').hide()
			}
		});
		$('#chargeinfo').click(function() {
			$('.charge-group').toggle()
		});
		$('.btn-submit').click(function() {
			var btn = $(this);
			if (btn.attr('stop')) {
				return
			}
			var current = core.getNumber($('#current').html());
			if (current < params.withdraw) {
				FoxUI.toast.show('满 ' + params.withdraw + ' 元才能提现!');
				return
			}
			var html = '';
			var realname = '';
			var alipay = '';
			var alipay1 = '';
			var bankname = '';
			var bankcard = '';
			var bankcard1 = '';
			var applytype = $('input[name="1"]:checked').data('type');
			var typename = $('input[name="1"]:checked').closest(".fui-cell").find(".fui-cell-info").html();
			if (applytype == undefined) {
				FoxUI.toast.show('未选择提现方式，请您选择提现方式后重试!');
				return
			}
			if (applytype == 0) {
				html = typename
			} else if (applytype == 1) {
				html = typename
			}
			if (applytype < 2) {
				var confirm_msg = '确认要' + html + "?"
			} else {
				var confirm_msg = '确认要' + html
			}
			FoxUI.confirm(confirm_msg, function() {
				btn.html('正在处理...').attr('stop', 1);
				core.json('commission/apply', {
					type: applytype,
					realname: realname,
					alipay: alipay,
					alipay1: alipay1,
					bankname: bankname,
					bankcard: bankcard,
					bankcard1: bankcard1
				}, function(ret) {
					if (ret.status == 0) {
						btn.removeAttr('stop').html(html);
						FoxUI.toast.show(ret.result.message);
						return
					}
					FoxUI.toast.show('申请已经提交，请等待审核!');
					location.href = core.getUrl('commission/withdraw')
				}, true, true)
			})
		})
	};
	return modal
});
