<form method="get" id="searchform" class="search-form" action="<?php echo home_url(); ?>" _lpchecked="1">
	<fieldset>
		<input type="text" name="s" id="s" value="" onfocus="if(this.value=='Search this Site...')this.value='';" x-webkit-speech onwebkitspeechchange="transcribe(this.value)"> 
		<input id="search-image" class="sbutton" type="submit" style="vertical-align: top;" value="Search">
	</fieldset>
</form>