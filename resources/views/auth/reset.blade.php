@extends('layouts.app')

@section('title', 'change password')
    

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="fw-bold text-secondary">Change Password</h2>
                </div>
                <div class="card-body">
                    <div id="reset_alert"></div>
                    <form action="" id="reset_form" method="post">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <input type="email" id="email" name="email" class="form-control rounded-0" placeholder="E-mail" value="{{ $email }}" disabled>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <input type="password" id="npass" name="npass" class="form-control rounded-0" placeholder="New Password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <input type="password" id="cnpass" name="cnpass" class="form-control rounded-0" placeholder="Confirm New Password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3 d-grid">
                            <input type="submit" value="Update Password" id="reset_btn" class="btn btn-dark rounded-0">
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
    $("#reset_form").submit(function(e){
        e.preventDefault();
        $("#reset_btn").val("Please Wait...");
        $.ajax({
            url: '{{ route('auth.reset') }}',
            method: 'post',
            data: $(this).serialize(),
            // dataType: 'json',
            success: function(res){
                if(res.status == 400){
                    showError('npass', res.messages.npass);
                    showError('cnpass', res.messages.cnpass);
                    $("#reset_btn").val('Update Password');
                }
                else if(res.status == 401){
                    $("#reset_alert").html(showMessage('danger', res.messages));
                    removeValidationClasses('#reset_form');
                    $("#reset_btn").val('Update Password');
                }
                else{
                    $("#reset_form")[0].reset();
                    $("#reset_alert").html(showMessage('success', res.messages));
                    $("#reset_btn").val('Update Password');
                }
            }

        });
    });
});
</script>
@endsection