module.exports = {"../../DefaultSkin/Assets/php/main.phpfe":"<?php\n\n$console.log(\"PHPFE Started\") ;","../../JobList/Assets/php/main.phpfe":"<?php\n\n$toggle_joblist_scope_closure = function() use ($jQuery) {\n\n    if ($jQuery('input#toggle_joblist_scope')->is(':checked')) {\n        $jQuery('div.joblist_scope')->fadeIn('slow') ;\n    } else {\n        $jQuery('div.joblist_scope')->fadeOut('slow') ;\n    }\n\n} ;\n\n$jQuery('input#toggle_joblist_scope')->on('change', $toggle_joblist_scope_closure) ;"};