@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid px-0">
<div class="content-wrapper">

<div class="loginpage" id="loginpage" style="min-height:500px;">
<div class="userlogin" id="userlogin">

<div id="logreg-forms">
@if(session('success'))
	<div class="alert alert-success">The password reset was successful! Now you can <a style="text-decoration: underline;" href="{{ config('myconfig.config.server_url') }}admin">Login</a></div>
@elseif(session('failed'))
	<div class="alert alert-danger">Sorry there were some problems, please try it again.</div>
@elseif(session('wrongcaptcha'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">The captcha is wrong.
	<br />
	Please prove you are a human.</div>
@elseif(session('passwordsnotthesame'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">The passwords must be the same.
	<br />
	Please try again.</div>
@endif

@if ($errors->has('email'))
<div class="alert alert-danger">{{ $errors->first('email') }}</div>
@endif

@if ($errors->has('password'))
<div class="alert alert-danger">{{ $errors->first('password') }}</div>
@endif

@if ($errors->has('password_confirmation'))
<div class="alert alert-danger">{{ $errors->first('password_confirmation') }}</div>
@endif

@if ($errors->has('g-recaptcha-response'))
<div class="alert alert-danger">Please prove you are a human.</div>
@endif


<form class="userloginform" id="userloginform2" action="{{ route('password.ajaxrequest') }}" method="post" onsubmit="" autocomplete="on">

{{ csrf_field() }}

<input type="hidden" name="token" value="{{ $token }}">

<h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Reset Password</h1>

<div class="form-group">
<label for="email">Email address</label>
       <input type="email" class="form-control" name="email" id="email" value="{{ $email ? $email : (old('email') ?  old('email') : '') }}" placeholder="name@example.com" required="">
</div>

<div class="form-group">
<label for="password">Password</label>
       <input type="password" class="form-control" name="password" id="password" autocomplete="off">
</div>

<div class="form-group">
<label for="password_confirmation">Confirm Password</label>
       <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" autocomplete="off">
</div>

<div class="{{ config('myconfig.istestsite') ? "hidden" : "" }}">
	<input type="hidden" class="hiddenRecaptcha required" name="g-recaptcha-response" id="hiddenRecaptcha">
<div id="captcha"></div>
</div>
<button class="btn btn-success btn-block" type="submit">Reset Password</button>

</form>


</div>

</div>
</div>
</div>
</div>
<script type="text/javascript">
$(function()
			{
 			$("#userloginform2").validate(
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
}, 'Your input must contain at least 1 letter and 1 number');

jQuery.validator.addMethod("notEqual", function(value, element, param) {
  return this.optional(element) || value != param;
}, "Please specify a different (non-default) value");

</script>

<script type="text/javascript">
// Displays the recpatcha form in the element with id "captcha"
function onloadCallback() {
	
	grecaptcha.execute("{{ config('myconfig.captcha.captcha_sitekey') }}", 
			 {action: 'resetpassword'}).then(function(token) {
                if (token) {
				document.getElementById('hiddenRecaptcha').value = token;
				  $('#hiddenRecaptcha-error').hide();
                }
});
}
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render={{ config('myconfig.captcha.captcha_sitekey') }}" async defer></script>
@endsection
