<?php

function nc_timezone_widget() {
	?>
	<div class="nc-time-container">
		<div class="tzhd">
			<h4>World TimeZone Search</h4>
			<h6>Search for places around the world to build your own virtual world clock!</h6>
		</div>
		<div class="nc-timezone hidden">
			<div class="nc-tz-result">
				
			</div>
		</div>
		<div class="nc-timezone-search">
			<form id="nc_place_search_form" method="GET">
				<div class="nc-searchtxt">
					<input type="text" name="nc_place_search" id="nc_place_search" placeholder="Search for a place..." >
				</div>
				<div class="nc-searchbtn">
					<input type="submit" id="nc_place_submit" class="nc-time-btn" value="Add A Place">
				</div>
				<div class="clearfix"></div>
			</form>
			
		</div>
	</div>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCCm92aEtv62gFkxaA6t4kcJyUB8vAKy8I&libraries=places&callback=activate_places_search"></script>
	
	<?php
}

add_shortcode( 'nctimezone', 'nc_timezone_widget' );

