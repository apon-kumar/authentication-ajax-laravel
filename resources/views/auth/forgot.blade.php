@extends('layouts.app')

@section('title', 'forgot password')
    

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="fw-bold text-secondary">Forgot Password</h2>
                </div>
                <div class="card-body">
                    <div id="forgot_alert"></div>
                    <form action="" id="forgot_form" method="post">
                        @csrf
                        <div class="mb-3 text-secondary">
                            Enter your E-mail address and we will send a link to reset your password.
                        </div>
                        <div class="mb-3">
                            <input type="email" id="email" name="email" class="form-control rounded-0" placeholder="E-mail">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3 d-grid">
                            <input type="submit" value="Reset Password" id="forgot_btn" class="btn btn-dark rounded-0">
                        </div>

                        <div class="text-center text-secondary">
                            <div>Back to <a href="/" class="text-decoration-none">Login</a></div>
                        </div>
                    </form>                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function(){
    $("#forgot_form").submit(function(e){
        e.preventDefault();
        $("#forgot_btn").val('Please Wait...');
        $.ajax({
            url: '{{ route('auth.forgot') }}',
            method: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res){
                if(res.status == 400){
                    $("#forgot_alert").html(showError('email', res.messages.email));
                    $("#forgot_btn").val("Reset Password");
                }
                else if(res.status == 200){
                    $("#forgot_alert").html(showMessage('success', res.messages));
                    $("#forgot_btn").val("Reset Password");
                    removeValidationClasses("#forgot_form");
                    $("#forgot_form")[0].reset();
                }
                else{
                    $("#forgot_btn").val("Reset Password");
                    $("#forgot_alert").html(showMessage('danger', res.messages));
                }
            }
        });
    });
});
</script>
@endsection