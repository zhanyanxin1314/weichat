define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		params = $.extend({
			returnurl: '',
			template_flag: 0,
			new_area: 0
		}, params || {});
		if (typeof(window.memberData) !== 'undefined') {
			if (memberData.avatar) {
				$(".avatar").attr('src', memberData.avatar)
			}
			if (memberData.nickname) {
				$(".nickname").text(memberData.nickname)
			}
		}
		var reqParams = ['foxui.picker'];
		if (params.new_area) {
			reqParams = ['foxui.picker', 'foxui.citydatanew']
		}
		require(reqParams, function() {
			$('#city').cityPicker({
				new_area: params.new_area,
				showArea: false
			});
			$('#birthday').datePicker()
		});
		$('#btn-submit').click(function() {
			var postdata = {};
			if (params.template_flag == 0) {
				if ($('#realname').isEmpty()) {
					FoxUI.toast.show('请填写姓名');
					return
				}
				if (!$('#mobile').isMobile() && !params.wapopen) {
					FoxUI.toast.show('请填写正确手机号码');
					return
				}
				if ($(this).attr('submit')) {
					return
				}
				var birthday = $('#birthday').val().split('-');
				var citys = $('#city').val().split(' ');
				$(this).html('处理中...').attr('submit', 1);
				postdata = {
					'memberdata': {
						'realname': $('#realname').val(),
						'weixin': $('#weixin').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'province': $('#city').val().length > 0 ? citys[0] : '',
						'city': $('#city').val().length > 0 ? citys[1] : '',
						'datavalue': $('#city').attr('data-value')
					},
					'mcdata': {
						'realname': $('#realname').val(),
						'gender': $('#sex').val(),
						'birthyear': $('#birthday').val().length > 0 ? birthday[0] : 0,
						'birthmonth': $('#birthday').val().length > 0 ? birthday[1] : 0,
						'birthday': $('#birthday').val().length > 0 ? birthday[2] : 0,
						'resideprovince': $('#city').val().length > 0 ? citys[0] : '',
						'residecity': $('#city').val().length > 0 ? citys[1] : ''
					}
				};
				if (!params.wapopen) {
					postdata.memberdata.mobile = $('#mobile').val();
					postdata.mcdata.mobile = $('#mobile').val()
				}
				core.json('member/info/submit', postdata, function(json) {
					modal.complete(params, json)
				}, true, true)
			} else {
				FoxUI.loader.show('mini');
				$(this).html('处理中...').attr('submit', 1);
				require(['biz/plugin/diyform'], function(diyform) {
					postdata = diyform.getData('.diyform-container');
					FoxUI.loader.hide();
					if (postdata) {
						core.json('member/info/submit', {
							memberdata: postdata
						}, function(json) {
							modal.complete(params, json)
						}, true, true)
					} else {
						$('#btn-submit').html('确认修改').removeAttr('submit')
					}
				})
			}
		})
	};
	modal.complete = function(params, json) {
		FoxUI.loader.hide();
		if (json.status == 1) {
			FoxUI.toast.show('保存成功');
			if (params.returnurl) {
				location.href = params.returnurl
			} else {
				history.back()
			}
		} else {
			$('#btn-submit').html('确认修改').removeAttr('submit');
			FoxUI.toast.showshow('保存失败!')
		}
	};
	modal.initFace = function() {
		$("#btn-getinfo").unbind('click').click(function() {
			FoxUI.confirm("确认使用微信昵称、头像吗？<br>使用微信资料保存后才生效", function() {
				var nickname = $.trim($("#nickname").data('wechat'));
				var avatar = $.trim($("#avatar").data('wechat'));
				$("#nickname").val(nickname);
				$("#avatar").attr('src', avatar).data('filename', avatar)
			})
		});
		$("#file-avatar").change(function() {
			var fileid = $(this).attr('id');
			FoxUI.loader.show('mini');
			$.ajaxFileUpload({
				url: core.getUrl('util/uploader'),
				data: {
					file: fileid
				},
				secureuri: false,
				fileElementId: fileid,
				dataType: 'json',
				success: function(res) {
					if (res.error == 0) {
						$("#avatar").attr('src', res.url).data('filename', res.filename)
					} else {
						FoxUI.toast.show("上传失败请重试")
					}
					FoxUI.loader.hide();
					return
				}
			})
		});
		$("#btn-submit").unbind('click').click(function() {
			var _this = $(this);
			if (_this.attr('stop')) {
				FoxUI.toast.show("保存中...");
				return
			}
			var nickname = $.trim($("#nickname").val());
			var avatar = $.trim($("#avatar").data('filename'));
			if (nickname == '') {
				FoxUI.toast.show("请填写昵称");
				return
			}
			if (avatar == '') {
				FoxUI.toast.show("请选择头像");
				return
			}
			_this.attr('stop', 1);
			core.json('member/info/face', {
				nickname: nickname,
				avatar: avatar
			}, function(json) {
				if (json.status == 0) {
					FoxUI.toast.show(json.result.message)
				} else {
					window.memberData = {
						nickname: nickname,
						avatar: $.trim($("#avatar").attr('src'))
					};
					window.history.back()
				}
				_this.removeAttr('stop')
			}, true, true)
		})
	};
	return modal
});
