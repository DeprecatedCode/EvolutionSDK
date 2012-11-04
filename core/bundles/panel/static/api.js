function api(path, data, callback) {
	$.ajax({
		'url': 'api/' + path,
		'data': data,
		'success': function(data) {
			if(data) {
				if(data.error) {
					alert(data.error + '\n\nPress OK to try again!');
					return window.location.reload();
				}
				callback(data);
			}
		},
		'dataType': 'json',
		'type': data != null ? 'POST' : 'GET'
	});
}

$(function() {
	api('authentication', null, function(data) {
		if(data.authenticated) {
			load_sites();
			populate_guide();
		} else {
			showfn('login')();
		}
	});
})

function showfn(page) {
	return function() {
		$('.page').hide();
		$('.page.page-' + page).fadeIn();
	}
}

function populate_guide() {
	api('info', null, function(data) {
		ul = $('#e-guide-bundle-paths');
		ul.text('');
		data.bundle_paths.forEach(function(path) {
			ul.append($('<li class="code">').text(path));
		});
		ul = $('#e-guide-bundles');
		ul.text('');
		for(name in data.bundles) {
			path = data.bundles[name];
			ul.append($('<li>')
				.append(
					$('<span class="name">').text(name)
				).append(
					$('<code>').text(path)
				)
			);
		};
	});
}

function load_sites() {
	api('sites', null, function(data) {
		menu = $('#e-sites-menu');
		$('#e-sites-menu-count').text(data.length);
		for(i = 0; i < data.length; i++) {
			menu.append($('<li>').text(data[i].name).click(function(id) {
				return function() {
					window.location = '/preview/' + id + '/';
				};
			}(data[i].id)));
		}
		menu.append($('<li class="separator">'));
		menu.append($('<li>').html('<i>Add site &rarr;</i>')
			.click(showfn('add-site')));
	})
}

function add_site() {
	api('add_site', {
		'name': $('#e-add-site-name').val(),
		'path': $('#e-add-site-path').val()
	}, function(data) {
		alert('Site added, press OK to reload!');
		window.location.reload();
	});
}