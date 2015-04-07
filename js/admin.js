var groupProperty = (function() {
	return {
		init: function(){
			$('.admin-center').on('click', '.show-add-form-group', function(){
				$('.property-order-container.settings').hide();
				$('.property-order-container.new').toggle();
			});

			$('.admin-center').on('click', '.show-property-order', function(){
				$('.property-order-container.settings').toggle();
				$('.property-order-container.new').hide();
			});

			// Удаление характеристики из группы
			$('.admin-center').on('click','.delete-row a', function(){
				var idGroup = $(this).closest('.group').data('id-group');
				var idEl 	= $(this).closest('li').attr('id');
				var el 		= $(this).closest('li');
				$.ajax({
					url: mgBaseDir + '/ajax',
					type: 'POST',
					dataType: 'json',
					data:{
					  mguniqueurl: 'action/deletePropertyInGroup',
					  pluginHandler: 'group-property',
					  idEl: idEl,
					  idGroup: idGroup
					},
					success: function(response)
					{
						groupProperty.updateGroup(idGroup);
						admin.indication(response.status, response.msg);
					}
				});

				return false;
			});

			$('.admin-center').on('click','.edit', function(){
				$("#propertys").accordion('disable');
				if(!$(this).hasClass('save')){
					var nameGroup = $(this).closest('.group').find('.title-box').text();
					$(this).find('span').text('Сохранить');
					$(this).closest('.group').find('.title-box').html('<input type="text" value="'+nameGroup+'" style="width:200px;">');
					$(this).addClass('save');
				}
			});

			// Сохранение измений группы
			$('.admin-center').on('click','.edit.save', function(){
				var nameGroup = $(this).closest('.group').find('.title-box input[type="text"]').val();
				var idGroup = $(this).closest('.group').data('id-group');
				var btn = $(this);
				$.ajax({
					url: mgBaseDir + '/ajax',
					type: 'POST',
					dataType: 'json',
					data:{
					  mguniqueurl: 'action/editGroup',
					  pluginHandler: 'group-property',
					  idGroup: idGroup,
					  nameGroup: nameGroup
					},
					success: function(response)
					{
						admin.indication(response.status, response.msg);
						$("#propertys").accordion('enable');
						$(btn).removeClass('save');
						$(btn).find('span').text('Редактировать группу');
						$(btn).closest('.group').find('.title-box').html(nameGroup);
					}
				});
			});

			// Удаление группы характеристик
			$('.admin-center').on('click','.delete', function(){
				if(!confirm('Удалить группу характеристик?')){
				  return false;
				}
				var idGroup = $(this).closest('.group').data('id-group');
				var el = $(this).closest('.group');
				$.ajax({
					url: mgBaseDir + '/ajax',
					type: 'POST',
					dataType: 'json',
					data:{
					  mguniqueurl: 'action/deleteGroup',
					  pluginHandler: 'group-property',
					  idGroup: idGroup
					},
					success: function(response)
					{
						admin.indication(response.status, response.msg);
						$(el).remove();
						if($('#propertys .group').size() == 0) {
							$('#propertys').append('<div class="well">Создайте группу характеристик</div>');
						}
					}
				});
				return false;
			});

			// Создание новой группы
			$('.admin-center').on('click','.new .base-setting-save', function(){
				var nameGroup = $('#name-new-group').val();
				if(nameGroup.length){
					$.ajax({
						url: mgBaseDir + '/ajax',
						type: 'POST',
						dataType: 'json',
						data:{
						  mguniqueurl: 'action/createGroup',
						  pluginHandler: 'group-property',
						  nameGroup: nameGroup
						},
						success: function(response)
						{
							$('#name-new-group').val('');
							$('#propertys .well').remove();
							admin.indication(response.status, response.msg);
							var newGroupHtml = '<div class="group" id="'+response.data.idGroup+'" data-id-group="'+response.data.idGroup+'"><div class="clearfix btns-box"> <div class="right btns"> <a href="javascript:void(0);" class="delete"><span>Удалить</span></a><a href="javascript:void(0);" class="edit"><span>Редактировать</span></a><a href="javascript:void(0);" class="add-property custom-btn add-new-button"><span>Добавить</span></a></div></div> <h3 class="clearfix"> <span class="left title-box">'+response.data.nameGroup+'</span> </h3> <div class="content"> <div class="well">Добавьте в группу характеристики</div> </div></div>';
							$('#propertys').append(newGroupHtml);
							listGroupSort();
						}
					});
				}
				return false;
			});
			

			// Сохранение настроек
			$('.admin-center').on('click','.settings .base-setting-save', function(){
				var obj = '{';
				$('.settings .list-option input, .settings .list-option textarea, .settings .list-option select').each(function() {     
				  obj += '"' + $(this).attr('name') + '":"' + admin.htmlspecialchars($(this).val()) + '",';
				});
				obj += '}';    

				//преобразуем полученные данные в JS объект для передачи на сервер
				var data =  eval("(" + obj + ")");
				admin.ajaxRequest({
				  mguniqueurl: "action/saveOptions",
				  pluginHandler: 'group-property',
				  data: data 
				},

				function(response) {
				  admin.indication(response.status, response.msg);      
				}

				);
				return false;
			});

			$('.admin-center').on('click','.add-property', function(){
				admin.openModal($('.b-modal'));
				return false;
			});

			// Добавление характеристик в группу
			$('.b-modal .save-button').bind('click', function(){
				var data 	= [];
				var idGroup = $(this).attr('id');

				$('.b-modal .propertys input[type="checkbox"]').each(function(){
					if($(this).is(':checked')){
						data.push($(this).val());
					}
				});

				$.ajax({
					url: mgBaseDir + '/ajax',
					type: 'POST',
					dataType: 'json',
					data:{
					  mguniqueurl: 'action/addPropertyInGroup',
					  pluginHandler: 'group-property',
					  data: data,
					  idGroup: idGroup
					},
					success: function(response)
					{
						$('.b-modal .propertys input[type="checkbox"]').each(function(){
							if($(this).is(':checked')){
								$(this).closest('li').remove();
							}
						});
						admin.indication(response.status, response.msg);
						groupProperty.updateGroup(idGroup);
						admin.closeModal($('.b-modal')); 
					}
				});

				return false;
			});

			$('.admin-center').on('click','.add-property', function(){
				var idGroup = $(this).closest('.group').data('id-group');
				$('.b-modal .save-button').attr('id', idGroup);
			});

			$('.b-modal .propertys li input[type="checkbox"]').bind('click', function(){
				return false;
			});

			// Форма поиска
			$('.admin-center').on('keyup', '#search', function(){
				var searchStr = $(this).val();
				if (searchStr.length >= 2) {
					groupProperty.startSearch($(this).val());
				}
			});

			$('#seacrh-btn').bind('click', function(){
				groupProperty.startSearch($('#search').val());
				return false;
			});

			$('.admin-center').on('click','.b-modal .propertys li', function(){
				var checked = $(this).find('input[type="checkbox"]').prop('checked');
				var trigger = false;
				if(!checked) trigger = true;
				$(this).find('input[type="checkbox"]').prop('checked', trigger);
			});

			listGroupSort();
			listPropertySort();
		},
		updateGroup: function(idGroup){
			$.ajax({
				url: mgBaseDir + '/ajax',
				type: 'POST',
				data:{
				  mguniqueurl: 'action/getGroup',
				  pluginHandler: 'group-property',
				  idGroup: idGroup
				},
				success: function(response)
				{
					var data = $.parseJSON(response);
					$('.group').each(function(){
						if($(this).attr('id') == idGroup){
							var area   = $(this).find('.content');
							var result = '';
							$(area).empty();
							if(data != ''){
								result = '<ul class="list-property ui-sortable">';
								$.each(data, function(i, item){
									result += '<li id="'+data[i].id+'">'+data[i].name+'<span class="delete-row"><a href="javascript:void(0);"></a></span></li>';
								});
								result += '</ul>';
							} else {
								result = '<div class="well">Добавьте в группу характеристики</div>';
							}
							$(area).append(result);

							listPropertySort();
						}
					});
				}
			});
		},
		startSearch: function(str){
			$.ajax({
				url: mgBaseDir + '/ajax',
				type: 'POST',
				data:{
				  mguniqueurl: 'action/searchProperty',
				  pluginHandler: 'group-property',
				  query: str
				},
				success: function(response)
				{
					var data = $.parseJSON(response);
					$('#result-title').html('Результаты поиска');
					$('.b-modal .propertys').empty();
					if(data != ''){
						var result = '<ul>';
						$.each(data, function(i, item){
							result += '<li>'+data[i].name+'<input type="checkbox" value="'+data[i].id+'" name="propertys[]"></li>';
						});
						result += '</ul>';
						$('.b-modal .propertys').append(result);
					} else {
						$('.b-modal .propertys').append('<div class="well">Ничего не было найдено</div>');
					}
				}
			});
		}
	}
})();

function listGroupSort(){
	$("#propertys")
		.accordion({
			heightStyle: "content",
			active: false,
			collapsible: true,
			header: "h3"
		}).sortable({
	        axis: "y",
	        handle: "h3",
	        stop: function( event, ui ) {
	        	$( this ).accordion( "refresh" );
	        	ui.item.children( "h3" ).triggerHandler( "focusout" );
	          $.ajax({
	          	url: mgBaseDir + '/ajax',
	          	dataType: 'json',
	          	type: 'POST',
	          	data:{
	          	  mguniqueurl: 'action/sortedGroup',
	          	  pluginHandler: 'group-property',
	          	  groups: $(this).sortable('toArray')
	          	},
	          	success: function(response)
	          	{
	          		admin.indication(response.status, response.msg);
	          	}
	          });
	        }
	});
}

function listPropertySort(){
	$('.list-property').sortable({
		axis: 'y',
		stop: function(e, ui){
			$(this).sortable("refresh");
			$.ajax({
				url: mgBaseDir + '/ajax',
				dataType: 'json',
				type: 'POST',
				data:{
				  mguniqueurl: 'action/sortedProperty',
				  pluginHandler: 'group-property',
				  propertys: $(this).sortable('toArray'),
				  idGroup: $(this).closest('.group').data('id-group')
				},
				success: function(response)
				{
					admin.indication(response.status, response.msg);
				}
			});
		}
	});
}

groupProperty.init();