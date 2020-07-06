
@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid px-0">
			<div class="content-wrapper">
			<div class="userlogin" id="userlogin">

			<div id="logreg-forms">
@if(session('inactive'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">This account is not activated yet. Check your email for activation.
	<br />
	Or <a class="activated" href="{{ config('myconfig.config.server_url')  }}contact" title="Contact us">contact</a> our developement team if you have any problems with the activation.</div>
@elseif(session('nonexistent'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">Username or password is not valid.
	<br />
	Please make sure you used the right username and password.</div>
@elseif(session('wrongcaptcha'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">The captcha is wrong.
	<br />
	Please prove you are a human.</div>
@endif

@if ($errors->has('password'))
<div class="alert alert-danger">{{ $errors->first('password') }}</div>
@endif
                                
@if ($errors->has('username') || $errors->has('email'))
<div class="alert alert-danger">{{ $errors->first('username') ?: $errors->first('email') }}</div>
@endif
								
@if ($errors->has('g-recaptcha-response'))
<div class="alert alert-danger">Please prove you are human.</div>
@endif

	<form class="form-signin" id="signinform" action="{{ route('admin.login') }}" method="post" onsubmit="" autocomplete="on">
					{{ csrf_field() }}

				<h1 class="h3 mb-3 font-weight-normal" style="text-align: center"> Sign in</h1>
			<!-- <div class="social-login">
					<button class="btn facebook-btn social-btn" type="button"><span><i class="mdi mdi-facebook"></i> Sign in with Facebook</span> </button>
					<button class="btn google-btn social-btn" type="button"><span><i class="mdi mdi-google-plus"></i> Sign in with Google+</span> </button>
				</div>
			-->
		
			<div class="form-group">
                      <label for="login">Email or Username</label>
              <input type="text" class="form-control" name="login" id="login" placeholder="name@example.com" required="">
			</div>
					
			<div class="form-group">
                      <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="" autocomplete="off">
			</div>

				<div class="form-check form-check-success">
                            <label class="form-check-label" for="remember">
             <input type="checkbox" name="remember" id="remember" class="form-check-input">
                              Remember me
                            <i class="input-helper"></i></label>
                          </div>
			<div class="{{ config('myconfig.istestsite') ? "hidden" : "" }}">
				<input type="hidden" class="hiddenRecaptcha required" name="g-recaptcha-response" id="hiddenRecaptcha">
				<div id="captcha"></div>
		    </div>

				<button class="btn btn-success btn-block" type="submit"><i class="mdi mdi-login"></i> Sign in</button>
				<a href="{{ config('myconfig.config.server_url')  }}admin/reset" id="forgot_pswd">Forgot password?</a>
				<hr>

				<a href="{{ config('myconfig.config.server_url')  }}admin/register">
				<div class="btn btn-primary btn-block"><i class="mdi mdi mdi-account-plus"></i> Or Register</div>
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
 			$("#signinform").validate(
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
						login:
						{
							required: true,
						},
						password:
						{
							required: true,
							minlength: 6,
							ContainsAtLeastOneDigit: true,
						},
						'g-recaptcha-response': {
							required: true,
						}
					},
					
					// Messages for form validation
					messages:
					{
						login:
						{
							required: 'Please add an email address or username.',
						},
						password:
						{
							required: 'Please type in password.',
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
}, 'Your input must contain at least 1 letter and 1 number');

</script>

@if(!config('myconfig.istestsite'))
<script type="text/javascript">
// Displays the recpatcha form in the element with id "captcha"
function onloadCallback() {
	
	grecaptcha.execute("{{ config('myconfig.captcha.captcha_sitekey') }}", 
			 {action: 'login'}).then(function(token) {
                if (token) {
				  document.getElementById('hiddenRecaptcha').value = token;
				  $('#hiddenRecaptcha-error').hide();
                }
});
}
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render={{ config('myconfig.captcha.captcha_sitekey') }}" async defer></script>
@endif
@endsection