done = false ;
max = 0 ;

function changeModule(element) {
    module = $("#new_step_module_selector").find(":selected").text() ;
    console.log("one") ;
    if (module == "nomoduleselected") {
        $('#new_step_type_selector_wrap').html("<p></p>"); }
    else {
        selectoptions = window.steps[module] ;
        console.log("two") ;
        window.htmlzy  = '<select onchange="changeStepTypeSelector()" id="new_step_type_selector" name="new_step_type_selector">' ;
        window.htmlzy += '  <option value="">-- Select Step Type --</option>' ;
        console.log("three") ;
        console.log(selectoptions) ;
        for (var key in selectoptions) {
            window.htmlzy += '  <option value="'+key+'">'+key+'</option>';    }
        console.log("four") ;
        window.htmlzy += '</select>' ;
        console.log("five") ;
        console.log(window.htmlzy) ;
        $('#new_step_type_selector_wrap').html(window.htmlzy); }
}

function changeStepTypeSelector(element) {
    html = '<a class="btn btn-info hvr-grow-shadow" onclick="displayStepField()">Add Step</a>' ;
    $('#new_step_button_wrap').html(html);
}

function toggleConfSetting(toggler, element) {
    sel = $("#"+element) ;
    sliderFields = sel.find(".sliderFields");
    if (sliderFields.css('display') == 'none') {
        $(toggler).removeClass("fa fa-toggle-off");
        $(toggler).addClass("fa fa-toggle-on");
        sliderFields.slideDown(); }
    else {
        $(toggler).removeClass("fa fa-toggle-on");
        $(toggler).addClass("fa fa-toggle-off");
        sliderFields.slideUp();}
}

function toggleViewConfSetting(element) {
    sel = $("#"+element) ;
    if (sel.css('display') == 'none') {
        sliderFields.slideDown(); }
    else {
        sliderFields.slideUp();}
}

function displayStepField() {
    steptype = $("#new_step_type_selector").find(":selected").text() ;
    module = $("#new_step_module_selector").find(":selected").text() ;
    allFields = window.steps[module][steptype] ;

    hash = getNewHash();

    html = "" ;

    if (Array.isArray(allFields) === false) {
        allFields = [allFields] ;
    }
    console.log("allFields is");
    console.log(allFields);


    html  = '<li class="form-group ui-state-default bg-primary singleBuildStep ui-sortable-handle" id="step'+hash+'">' ;
    html += '    <h3>New Step: '+module+', '+steptype+'</h3>' ;
    html += '   <div class="col-sm-12">' ;
    html += '    <input type="hidden" id="steps['+hash+'][module]" name="steps['+hash+'][module]" value="'+module+'" />' ;
    html += '    <input type="hidden" id="steps['+hash+'][steptype]" name="steps['+hash+'][steptype]" value="'+steptype+'" />' ;

    for (field in allFields) {

        console.log("one field is", allFields[field]) ;

        if (module === "ConditionalStepRunner" || module === "Plugin") {
            html  = '<li class="form-group ui-state-default bg-primary ui-sortable-handle" id="step'+hash+'">' ;
            html += '  <div class="col-sm-2">' ;
            html += '    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' ;
            html += '  </div><h3>'+module+'</h3>';
            html += '  <div class="col-sm-10">' ;
            html += '   <div class="form-group col-sm-12">' ;
            html += '    <h3>'+steptype+'</h3>' ;
            html += '    <input type="hidden" id="steps['+hash+'][module]" name="steps['+hash+'][module]" value="'+module+'" />' ;
            html += '    <input type="hidden" id="steps['+hash+'][steptype]" name="steps['+hash+'][steptype]" value="'+steptype+'" />' ;
            html += '   <div>' ;
            html += ' 	<label for="'+steptype+'" class="col-sm-2 control-label text-left"> </label>';
            html +=  '	<div class="col-sm-10">';

            var i; console.log(allFields[field]);
            for (i = 0; i < allFields[field].length; i++) {
                html += '    <h5>'+allFields[field][i].name+'</h5>' ;
                action = "";
                if (typeof(allFields[field][i].action != "undefined")) { action = allFields[field][i].action+'="'+allFields[field][i].funName+'(\''+hash+'\')"'; }
                if (allFields[field][i]["type"] === "text" || allFields[field][i]["type"] === "time" || allFields[field][i]["type"] === "number") {
                    html += '<span>'+ allFields[field][i].name +'</span>' + "\n";
                    html += ' <input type="'+allFields[field][i]["type"]+'" id="steps['+hash+']['+allFields[field][i].slug+']"' ;
                    html += ' name="steps['+hash+']['+allFields[field][i].slug+']" class="form-control" />' ;
                }
                if (allFields[field][i]["type"] == "password") {
                    html += '<span>'+ allFields[field][i].name +'</span>' + "\n";
                    html += ' <input type="password" id="steps['+hash+']['+allFields[field][i].slug+']"' ;
                    html += ' name="steps['+hash+']['+allFields[field][i].slug+']" class="form-control" />' ;
                }
                if (allFields[field][i]["type"] == "textarea") {
                    html += '<span>'+ allFields[field][i].name +'</span>' + "\n";
                    html += '<textarea id="steps['+hash+']['+allFields[field][i].slug+']"' ;
                    html += ' name="steps['+hash+']['+allFields[field][i].slug+']"  class="form-control"></textarea>' ;
                }
                if (allFields[field][i]["type"] == "dropdown") {
                    html += '<span>'+ allFields[field][i].name +'</span>' + "\n";
                    html += '<select id="steps['+hash+']['+allFields[field][i].slug+']" name="steps['+hash+']['+allFields[field][i].slug+']" '+action+' class="form-control">';
                    $.each(allFields[field][i].data, function(index, value) {
                        html += '<option value="'+index+'">'+value+'</option>';
                    });
                    html += '</select>';
                }
                if (allFields[field][i]["type"] == "radio" || allFields[field][i]["type"] == "checkbox") {
                    html += '<span>'+ allFields[field][i].name +'</span>' + "\n";
                    $.each(allFields[field][i].data, function(index, value) {
                        html += ' <input type="'+allFields[field][i]["type"]+'" name="steps['+hash+']['+allFields[field][i].slug+']" value="'+index+'" class="form-control">'+value;
                    });
                }
                if (allFields[field][i]["type"] == "div") {
                    html += '<div id="'+allFields[field][i].id	+hash+'"></div>';
                }
            }
            html += '  </div>' ;
            html += '  </div>';
            html += '  </div>';
            html += '  <div class="form-group">';
            html += ' 	<label for="delete" class="col-sm-2 control-label text-left"></label>';
            html += '   <div class="col-sm-10">' ;
            html += '    <a class="btn btn-warning" onclick="deleteStepField(\''+hash+'\')">Delete Step</a>' ;
            html += '  </div>' ;
            html += '  </div>' ;
            html += '  </div>' ;
            html += ' </li>';
        }

        if (allFields[field].type === "textarea") {
            html += '  <div class="fullWidth">' ;
            html += '    <div class="fullWidth">' ;
            html += '      <span>'+ allFields[field].name +'</span>' + "\n";
            html += '    </div>' ;
            html += '    <div class="fullWidth">' ;
            html += '      <textarea id="steps['+hash+']['+allFields[field].slug+']"' ;
            html += ' name="steps['+hash+']['+allFields[field].slug+']" class="form-control" ></textarea>' ;
            html += '    </div>' ;
            html += '  </div>' ;
        }

        else if (allFields[field].type === "text") {
            html += '  <div class="fullWidth">' ;
            html += '    <div class="fullWidth">' ;
            html += '      <span>'+ allFields[field].name +'</span>' + "\n";
            html += '    </div>' ;
            html += '    <div class="fullWidth">' ;
            html += ' <input type="text" id="steps['+hash+']['+allFields[field].slug+']'+'" ' ;
            html += ' name="steps['+hash+']['+allFields[field].slug+']'+'" class="form-control" />' ;
            html += '    </div>' ;
            html += '  </div>' ;
        }

        else if (allFields[field].type === "boolean") {
            html += '  <div class="fullWidth">' ;
            html += '    <div class="fullWidth">' ;
            html += '      <span>'+ allFields[field].name +'</span>' + "\n";
            html += '    </div>' ;
            html += '    <div class="fullWidth">' ;
            html += '      <input type="checkbox" id="steps['+hash+'][data]" name="steps['+hash+'][data]" />' ;
            html += '    </div>' ;
            html += '  </div>' ;
        }

    }

    html += '    </div>' ;
    html += '  <div class="col-sm-12">' ;
    html += '    <a class="btn btn-warning" onclick="deleteStepField(\''+hash+'\')">Delete Step</a>' ;
    html += '  </div>' ;
    html += '</li>';

    $("#sortableSteps").append(html);
    // $('#new_step_wrap').html(html);
}

function deleteStepField(hash) {
    var res = confirm("Are you sure you want to do this?") ;
    if (res === true) { $('#step'+hash).remove(); }
    return false ;
}

function CONDaysOfWeekDays(hash) {
	savedSteps = window.savedSteps ;
	//if (typeof savedsteps[hash] != 'undefined') {}
	value = $("#steps\\["+hash+"\\]\\[days\\]").find(":selected").val() ;
	dayofweek = { 1:"Monday", 2:"Tuesday", 3:"Wednesday", 4:"Thursday", 5:"Friday", 6:"Saturday", 0:"Sunday" };
    html = '';
	if (value == 'days') {
		$.each(dayofweek, function(index, value) {
			checked = '';
			if ( savedSteps != null)
				if ( hash in savedSteps )
					if ( 'exactdays' in savedSteps[hash] )
						if( index in savedSteps[hash]['exactdays'] )
							checked = "checked";
			html += '<input class="col-sm-2 control-label text-left" type="checkbox" name="steps['+hash+'][exactdays]['+index+']" value="true" '+checked+'><li>'+value+'</li>';
		});
	}
	$("#CONDaysOfWeekDays"+hash).html(html);
}

function modsDown() {
    $('.slideysWrapper').slideDown();
}

function modsUp() {
    $('.slideysWrapper').slideUp();
}

function getNewHash() {
    hash = "1234567890" ;
    hash = Math.random() ;
    hash = hash * 10000000000 ;
    hash = hash.toString().replace(".", "") ;
    return hash ;
}