jQuery(document).ready(function($){
	var ajaxurl = nc_timezone.ajaxurl;

	initialize_clock();

	$('#nc_place_search_form').submit(function(){
		var search = $('#nc_place_search').val();

		if (is_valid_place_input(search) ){
			$.ajax({
				url : ajaxurl,
				type : 'get',
				data : {
					action : 'nc_timezone_search_timezone',
					nc_place_search : search,
				},
				success : function( response ) {
					response = JSON.parse(response);
					if( response.status == "OK"){
						$('.nc-timezone').removeClass('hidden').find('.nc-tz-result').append(response.result);
						var last = $('.nc-timezone .nc-tz-result').find('.nc-result-row:last-child').last();

						//add to cookie
						var timezonedata = get_cookies_by_prefix('timezonedata');
						var timezonedata_index = $(last).data('id');
						console.log(timezonedata_index);
						create_cookie('timezonedata_' + timezonedata_index, JSON.stringify( response.result ), 30);

						$("#nc_place_search_form")[0].reset();
					}
					else{
						alert("Search failed. Please try again or improve your search criteria.");
					}
					
				}
			});
		}

		else{
			alert('Please choose a specific place.');
		}

		return false;
	});

	$('.nc-timezone').on('click','.btnremove', function(){
		console.log('timezonedata_' + $(this).closest('.nc-result-row').data('id') );
		erase_cookie('timezonedata_' + $(this).closest('.nc-result-row').data('id') );
		$(this).closest('.nc-result-row').remove();
		
	});
	

	function update_clock(){

		$('.nc-timezone .nc-result-row').each(function(){
			$(this).find('.sep').toggleClass('blink');

			var timestamp = parseInt( $(this).data('actual-date') );
			$(this).data('actual-date', timestamp + 1);

			var months = [
			  'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
			];

			var days = [
			  'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
			];


			var now = new Date(timestamp * 1000);
			var now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(),  now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
			var ampm = now_utc.getHours() < 12 ? 'AM' : 'PM';
			var hours = now_utc.getHours() == 0 ? 12 : now_utc.getHours() > 12 ? now_utc.getHours() - 12 : now_utc.getHours();
			hours = hours < 10 ? '0' + hours : hours;
			var minutes = now_utc.getMinutes() < 10  ? '0' + now_utc.getMinutes()  : now_utc.getMinutes();
			var seconds = now_utc.getSeconds() < 10  ? '0' + now_utc.getSeconds() : now_utc.getSeconds();


			var dayOfWeek = days[now_utc.getDay()];
			var month = months[now_utc.getMonth()];
			var day = now_utc.getDate();
			var year = now_utc.getFullYear();
			var dateString = dayOfWeek + ', ' + month + ' ' + day + ' ' + year;


			$(this).find('.fulldate').html(dateString);
			$(this).find('.seconds').html(seconds);
			$(this).find('.hour').html(hours);
			$(this).find('.minute').html(minutes);
			$(this).find('.ante').html(ampm);

		});
	}

	function initialize_clock(){
		var timezonedata = get_cookies_by_prefix('timezonedata');

		if( timezonedata.length > 0){
			var html = '';

			for (var i = 0; i < timezonedata.length; i++) {
			    html += JSON.parse( timezonedata[i] );
			}

			$('.nc-timezone').removeClass('hidden').find('.nc-tz-result').html(html);
		}

		$('.nc-timezone').find('.nc-result-row').each(function(){
			var timestamp = parseInt( $(this).data('actual-date') ) + ( parseInt( new Date().getTime() / 1000) - $(this).data('id') );
			$(this).data('actual-date', timestamp);
			
		});

		

	}


	setInterval(update_clock, 1000);


});


function is_valid_place_input(place){
	if (place.indexOf(',') > -1) {
		return true;
	} else{
		return false;
	}
}

function activate_places_search () {
	var options = {
	  types: ['(regions)']
	};
	var input = document.getElementById('nc_place_search');
	var autocomplete = new google.maps.places.Autocomplete(input,options);
}

function create_cookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function erase_cookie(name) {
    create_cookie(name,"",-1);
}

function get_cookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}


function get_cookies_by_prefix(prefix){
	var arrSplit = document.cookie.split(";");
	var cookies = [];

	for(var i = 0; i < arrSplit.length; i++)
	{
	    var cookie = arrSplit[i].trim();
	    var cookieName = cookie.split("=")[0];

	    if(cookieName.indexOf( prefix ) === 0) {
	        cookies.push( get_cookie(cookieName) );
	    }
	}

	return cookies;
}

