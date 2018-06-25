<!DOCTYPE html>
	<?php
		//load config
		require_once './cfg/baseConfig.php';
		//load top menu
		require_once RESOURCES_PATH.'/header.php';
	 ?>
	<!-- Enter text here-->

	<div class="w3-bar w3-brown w3-mobile">
		<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'license')">License</button>
		<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'further_notes')">Further Notes</button>
	</div>

	<div id="license" class="w3-container city" style="display:block">

		<!-- Enter text here-->
		<div class="w3-panel w3-green w3-round">
			<h4>MIT License</h4>

			<p>
				<h5>Copyright (c) 2017 Dipl.-Phys. Ralf Zeidler</h5>
				Permission is hereby granted, free of charge, to any person obtaining a copy
				of this software and associated documentation files (the "Software"), to deal
				in the Software without restriction, including without limitation the rights
				to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
				copies of the Software, and to permit persons to whom the Software is
				furnished to do so, subject to the following conditions:
				<br><br>
				The above copyright notice and this permission notice shall be included in all
				copies or substantial portions of the Software.
				<br><br>
				THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
				IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
				FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
				AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
				LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
				OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
				SOFTWARE.
			</p>
		</div>
		<div class="w3-panel w3-green w3-round">
			<p>
				If you have questions, please don't hesitate to contact us: <a href= "mailto:info@radio-tracking.eu">info@radio-tracking.eu</a>
			</p>
		</div>
	</div>
	<div id="further_notes" class="w3-container city" style="display:none">
	<p>
	I'll work on that :)
	</p>

	<!-- Enter text here-->

	</div>
	</div>
	<!-- Enter text here-->
	<?php
		//load footer
		require_once RESOURCES_PATH.'/footer.php';
		//load javascripts
		require_once RESOURCES_PATH.'/javascript.php';
	?>