function changePassword() {
    rurl = $('#base_url').val() + '/index.php?control=UserManager&action=changepassword&output-format=JSON' ;
    console.log(rurl) ;

    $('#update_password').html('');
    $('#update_password_match').html('');
    $('#update_password_match').html('');

    $('#update_password_alert').html('');
    $('#update_password_alert').html('');
    $('#update_password_match_alert').html('');

    $('#password_match_error').html('');

    if ($('#update_password').val() == '') {
        $('#update_password_alert').html('&nbsp;&nbsp;Please enter your old password');
        $('#update_password').focus();
        return; }

    if ($('#update_password_match').val() == '') {
        $('#update_password_alert').html('&nbsp;&nbsp;Please enter your new password');
        $('#update_password_match').focus();
        return; }

    if ($('#update_password_match').val() == '') {
        $('#update_password_match_alert').html('&nbsp;&nbsp;Please enter new password Again');
        $('#update_password_match').focus();
        return; }

    if ($('#update_password').val() != $('#update_password_match').val()) {
        $('#password_match_error').html('&nbsp;&nbsp;Password Does Not Match...Try Again');
        $('#update_password_match').val('');
        $('#update_password_match').focus();
        return; }

    $.ajax({
        type: 'POST',
        url: rurl,
        data: {
            oldPassword:$('#update_password').val(),
            newPassword:$('#update_password_match').val()
        },
        dataType: "json",
        success: function(result) { console.log(result);
            $('#form_alert').html('&nbsp;&nbsp;'+result.msg);
            $('#form_alert').focus(); },
        error: function(result, textStatus, errorThrown) { console.log(result);
            $('#password_error_msg').html('&nbsp;&nbsp;'+textStatus+' '+errorThrown+' '+result.msg);
            $('#password_error_msg').focus(); }

    });
}


function createUser() {
    rurl = $('#base_url').val() + '/index.php?control=UserProfile&action=create&output-format=JSON' ;
    console.log(rurl) ;

//    $('#update_password').html('');
//    $('#update_password_match').html('');
//    $('#update_password_match').html('');
//
//    $('#update_password_alert').html('');
//    $('#update_password_alert').html('');
//    $('#update_password_match_alert').html('');
//
//    $('#password_match_error').html('');

    if ($('#create_username').val() == '') {
        $('#create_username_alert').html('Please enter a Username');
        $('#create_username').focus();
        return; }

    if ($('#create_email').val() == '') {
        $('#create_email_alert').html('Please enter an Email Address');
        $('#create_email').focus();
        return; }

    if ($('#update_password').val() == '') {
        $('#update_password_alert').html('Please enter your old password');
        $('#update_password').focus();
        return; }

    if ($('#update_password_match').val() == '') {
        $('#update_password_alert').html('Please enter your new password');
        $('#update_password_match').focus();
        return; }

    if ($('#update_password_match').val() == '') {
        $('#update_password_match_alert').html('Please enter a new password');
        $('#update_password_match').focus();
        return; }

    if ($('#update_password').val() != $('#update_password_match').val()) {
        $('#password_match_error').html('Password Does Not Match...Try Again');
        $('#update_password_match').val('');
        $('#update_password_match').focus();
        return; }

    if ($('#update_password').val() != $('#update_password_match').val()) {
        $('#password_match_error').html('Password Does Not Match...Try Again');
        $('#update_password_match').val('');
        $('#update_password_match').focus();
        return; }


    $.ajax({
        type: 'POST',
        url: rurl,
        data: {
            update_password:$('#update_password').val(),
            update_password_match:$('#update_password_match').val(),
            create_username:$('#create_username').val(),
            create_email:$('#update_email').val()
        },
        dataType: "json",
        success: function(result) { console.log(result);

            if (result.status == true) {
                $('#form_alert').addClass('successMessage');
                $('#form_alert').html(result.message);
                $('#form_alert').focus(); }
            else {
                $('#form_alert').addClass('errorMessage');
                $('#form_alert').html(result.message);
                $('#form_alert').focus(); } },
        error: function(result, textStatus, errorThrown) { console.log(result);
            $('#password_error_msg').html(''+textStatus+' '+errorThrown+' '+result.msg);
            $('#password_error_msg').focus(); }

    });
}


function refreshUserDetails(username) {

    $('#update_email').val("Loading...") ;
    $('#update_username_text').val("Loading...") ;
    $('#update_username').val("Loading...") ;

    rurl = $('#base_url').val() + '/index.php?control=UserProfile&action=get-user&output-format=JSON' ;
    console.log(rurl) ;
    ld_str  = '<span class="btn btn-info hvr-grow-shadow actionButton">' ;
    ld_str += '  Loading... ' ;
    ld_str += '</span>' ;
    $('.actionButtonWrap').html(ld_str) ;

    $.ajax({
        type: 'POST',
        url: rurl,
        data: {
            username: username
        },
        dataType: "json",
        success: function(result) {
            console.log(result);
            if (result !== undefined) {
                updateUserFields(result) ;
                $('#form_alert').focus(); }
            else {
                $('#form_alert').addClass('errorMessage');
                $('#form_alert').html(result.message);
                $('#form_alert').focus(); } },
        error: function(result, textStatus, errorThrown) { console.log(result);
            $('#password_error_msg').html(''+textStatus+' '+errorThrown+' '+result.msg);
            $('#password_error_msg').focus(); }

    });
}

function updateUserFields(userData) {
    $('#update_email').val(userData.email) ;
    $('#update_username_text').val(userData.username) ;
    $('#update_username').val(userData.username) ;
    btn_str  = '<button onclick="updateUser(); return false;" class="btn btn-success hvr-grow-shadow actionButton">' ;
    btn_str += '  Update Details ' ;
    btn_str += '</button>' ;
    $('.actionButtonWrap').html(btn_str) ;
}