@extends('layouts.app')

@section('title', 'register')
    

@section('content')
<div class="container-fluid">
    <div class="row d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="fw-bold text-secondary">Register</h2>
                </div>
                <div class="card-body">
                    <div id="show_success_alert">

                    </div>
                    <form action="" id="register_form" method="post">
                        @csrf
                        <div class="mb-3">
                            <input type="text" id="name" name="name" class="form-control rounded-0" placeholder="Name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <input type="email" id="email" name="email" class="form-control rounded-0" placeholder="E-mail">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <input type="password" id="password" name="password" class="form-control rounded-0" placeholder="Password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <input type="password" id="cpassword" name="cpassword" class="form-control rounded-0" placeholder="Confirm Password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3 d-grid">
                            <input type="submit" value="Register" id="register_btn" class="btn btn-dark rounded-0">
                        </div>

                        <div class="text-center text-secondary">
                            <div>Already have an account? <a href="/" class="text-decoration-none">Login</a></div>
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
        $("#register_form").submit(function(e){
            e.preventDefault();
            $("#register_btn").val('Please Wait...');
            $.ajax({
                url: '{{ route('auth.register') }}',
                method: 'post',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res){
                    if(res.status == 400){
                        showError('name', res.messages.name);
                        showError('email', res.messages.email);
                        showError('password', res.messages.password);
                        showError('cpassword', res.messages.cpassword);
                        $("#register_btn").val('Register');
                    }
                    else if(res.status == 200){
                        $("#show_success_alert").html(showMessage('success', res.messages));
                        $("#register_form")[0].reset();
                        removeValidationClasses("#register_form");
                        $("#register_btn").val('Register');
                    }
                }
            });
        });
    });
</script>
    
@endsection