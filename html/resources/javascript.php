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

    function copyInput(formName, excludeList) {
//        var exclude= ["antenna_id_", "antenna_orientation_", "change_logger_setting"];
        var elements = document.getElementById(formName).elements;
        for (var i=0, element; element = elements[i++];) {
            <!-- console.log(element.id); -->
            <!-- console.log(element.id.charAt(element.id.length-1)); -->
            var name=element.getAttribute("name");
            if (name==null || isNaN(parseInt(name.slice(-1),10)))
                continue;
            var nonum=name.substr(0,name.length-1);
            if (excludeList.indexOf(nonum)==-1 && name.charAt(name.length-1)!="0") {
                var ref=elements[nonum.concat("0")];
                if (element.type=="radio") {
                    document.getElementById(name.concat("_y")).checked = document.getElementById(nonum.concat("0_y")).checked;
                    document.getElementById(name.concat("_n")).checked = document.getElementById(nonum.concat("0_n")).checked;
                } else {
                    element.value = ref.value;
                }
            }
        }
        return 0;
    }
</script>