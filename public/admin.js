$(document).ready(function () {
    $(document).on('keyup', '#value_in_pound', function (e) {
        $("#value_in_dollar").val(Math.round($(this).val() / doller));
    })
})
