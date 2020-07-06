@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid px-0">
    <div class="content-wrapper">
    <div class="userlogin" id="userlogin">

    <div id="logreg-forms">
        
        
        @if ($errors->has('password'))
        <div class="alert alert-danger">{{ $errors->first('password') }}</div>
        @endif

        @if ($errors->has('password_confirmation'))
        <div class="alert alert-danger">{{ $errors->first('password_confirmation') }}</div>
        @endif
                                        
        @if ($errors->has('username') || $errors->has('email'))
        <div class="alert alert-danger">{{ $errors->first('username') ?: $errors->first('email') }}</div>
        @endif
                                        
        @if ($errors->has('g-recaptcha-response'))
        <div class="alert alert-danger">Please prove you are human.</div>
        @endif
        
       
        
        <form class="form-signin" id="regform" action="{{ route('register') }}" method="post" onsubmit="" autocomplete="off">
            {{ csrf_field() }}
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Register - Step 1</h1>


      <div class="form-group">
        <label for="email">Email*</label>
<input type="text" class="form-control" name="email" placeholder="something@something.com" id="email" value="{{ old('email') }}" required="" autocomplete="on">
</div>


      <div class="form-group">
                <label for="password">Password*</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="" autocomplete="off">
      </div>
      <div class="form-group">
        <label for="password_confirmation">Confirm Password*</label>
               <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" id="password_confirmation" autocomplete="off">
        </div>


      <div class="{{ config('myconfig.istestsite') ? "hidden" : "" }}">
        <input type="hidden" class="hiddenRecaptcha required" name="g-recaptcha-response" id="hiddenRecaptcha">
        <div id="captcha"></div>
    </div>

        <button class="btn btn-primary btn-block" type="submit"><i class="mdi mdi-account-plus"></i> Register</button>
        <a href="{{ config('myconfig.config.server_url')  }}admin/reset" id="forgot_pswd">Forgot password?</a>
        <hr>

        <a href="{{ config('myconfig.config.server_url')  }}admin/login">
            <div class="btn btn-success btn-block"><i class="mdi mdi-login"></i> Or Login</div>
        </a>
        <!-- <p>Don't have an account!</p>  -->
        <!--<button class="btn btn-primary btn-block" type="button" id="btn-signup"><i class="mdi mdi-account-plus"></i> Sign up New Account</button>-->
</form>
        

</div>

</div>
</div>
</div>



<script type="text/javascript">
    $(function()
                {
                 $("#regform").validate(
                    {
                        ignore: ".hidden input",//important cause of google captcha, cause default is hidden element ignored!!
                        onkeyup: function(element) {
                        var element_id = jQuery(element).attr('id');
                        if (this.settings.rules[element_id].onkeyup !== false) {
                          jQuery.validator.defaults.onkeyup.apply(this, arguments);
                        }
                      },
                      onfocusout: function(element) {
                        var element_id = jQuery(element).attr('id');
                        if (this.settings.rules[element_id].onfocusout !== false) {
                          jQuery.validator.defaults.onfocusout.apply(this, arguments);
                        }
                      },
                      onclick: function(element) {
                        var element_id = jQuery(element).attr('id');
                        if (this.settings.rules[element_id].onclick !== false) {
                          jQuery.validator.defaults.onclick.apply(this, arguments);
                        }
                      },
                        // Rules for form validation
                        rules:
                        {
                            email:
                            {
                                required: true,
                                email: true,
                            },
                            password:
                            {
                            required: true,
                            minlength: 6,
                            ContainsAtLeastOneDigit: true,
                            },
                            password_confirmation:
                            {
                            required: true,
                            minlength: 6,
                            ContainsAtLeastOneDigit: true,
                            equalTo: "#password",
                            },
                            'g-recaptcha-response': {
                                required: true,
                            },
                        },
                        
                        // Messages for form validation
                        messages:
                        {
                            email:
                            {
                                required: 'Please add an email address.',
                                email: 'Please use a valid email address.',
                            },
                            password:
                            {
                                required: 'This field is required!',
                                minlength: 'The password should contain least 6 characters!',
                                ContainsAtLeastOneDigit: 'The password should contain at least 1 number and 1 letter!',
                            },
                            'password_confirmation':
                            {
                                required: 'This field is required!',
                                minlength: 'The password should contain least 6 characters!',
                                ContainsAtLeastOneDigit: 'The password should contain at least 1 number and 1 letter!',
                                equalTo: 'The two passwords do not match!',
                            },
                            'g-recaptcha-response': {
                                required: 'Please prove you are human.',
                            },
                        },						
                        errorClass: "text-danger is-invalid",
                        validClass: "text-success is-valid",
                        // Do not change code below
                        errorPlacement: function(error, element)
                        {
                                
                            error.appendTo(element.parent());
                            
                            
                        },
                        
                        
                    });
                    
                });
    jQuery.validator.addMethod("ContainsAtLeastOneDigit", function (value) { 
            return /[a-z].*[0-9]|[0-9].*[a-z]/i.test(value);
    }, 'Your input must contain at least 1 letter and 1 number.');
    
    jQuery.validator.addMethod("notEqual", function(value, element, param) {
      return this.optional(element) || value != param;
    }, "Please specify a different (non-default) value.");

    jQuery.validator.addMethod("soundcloudlinkcheck", function (value, element, param) {
    if (this.optional(element)) {
        return true;
    }
    var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:soundcloud\.com|snd\.sc)(?:\/\w+(?:-\w+)*)+$/);

        if (re.test(value)) {
            return true;
            }
            
        }, "Please add a valid soundcloud link");


    jQuery.validator.addMethod("spotifylinkcheck", function (value, element, param) {
    if (this.optional(element)) {
        return true;
    }
    vvar re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:open\.spotify\.com)(?:\/\w+(?:-\w+)*)+$/);

        if (re.test(value)) {
            return true;
            }
            
        }, "Please add a valid spotify link.");

    </script>
    
    <script type="text/javascript">
    // Displays the recpatcha form in the element with id "captcha"
    function onloadCallback() {
        
        grecaptcha.execute("{{ config('myconfig.captcha.captcha_sitekey') }}", 
                 {action: 'register'}).then(function(token) {
                    if (token) {
                    document.getElementById('hiddenRecaptcha').value = token;
                      $('#hiddenRecaptcha-error').hide();
                    }
    });
    }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render={{ config('myconfig.captcha.captcha_sitekey') }}" async defer></script>
    @endsection
    