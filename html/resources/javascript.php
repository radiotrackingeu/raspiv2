<script>
function openCity(evt, cityName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("city");
  for (i = 0; i < x.length; i++) {
     x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active-item", ""); 
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active-item"; 
}
function w3_switch(name) {
	var x = document.getElementById(name);
    if (x.style.display == "none") {
        x.style.display = "block";
    } else { 
        x.style.display = "none";
    }
}

function myAccordion(id) {
    var x = document.getElementById(id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}

	
	function createAjaxRequestObject() {
            var xmlhttp;

            if(window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            // Create the object
            return xmlhttp;
        }

        function AjaxPost(ajaxURL, parameters, onComplete) {
            var http3 = createAjaxRequestObject();

            http3.onreadystatechange = function() {
                if(http3.readyState == 4) {
                    if(http3.status == 200) {
                        if(onComplete) {
                            onComplete(http3.responseText);
                        }
                    }
                }
            };

            // Create parameter string
            var parameterString = "";
            var isFirst = true;
            for(var index in parameters) {
                if(!isFirst) {
                    parameterString += "&";
                } 
                parameterString += encodeURIComponent(index) + "=" + encodeURIComponent(parameters[index]);
                isFirst = false;
            }

            // Make request
            http3.open("POST", ajaxURL, true);
            http3.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http3.send(parameterString);
        }

        function completedAJAX(response) {
            alert(response);
        }
	
</script>