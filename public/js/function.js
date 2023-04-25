function showError(field, message){
    if(!message){
        $("#"+field)
            .addClass('is-valid')
            .removeClass('is-invalid')
            .siblings('.invalid-feedback')
            .text("");

    }else {
            $("#"+field)
            .addClass('is-invalid')
            .removeClass('is-valid')
            .siblings('.invalid-feedback')
            .text(message);
    }
}

function removeValidationClasses(form){
    $(form).each(function(){
        $(form).find(":input").removeClass("is-valid is-invalid");
    });
}

function showMessage(type, message){
    // bootstrap alert
    return `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
    <strong>${message}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>`;
}