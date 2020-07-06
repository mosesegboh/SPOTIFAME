@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid px-0">
<div class="content-wrapper">

<div class="loginpage" id="loginpage" style="min-height:500px;">
<div class="userlogin" id="userlogin">

<div id="logreg-forms">

@if(session('success'))
	<div class="alert alert-success">We sent you the password reset link in Email. Please click on it to change your password.</div>
@elseif (session('failed'))
	<div class="alert alert-danger">This Email address is not registered with us.</div>
@elseif (session('wrongcaptcha'))
<a class="anchorhelp2" id="failanchor"></a>
<div class="alert alert-danger">The captcha is wrong.
	<br />
	Please prove you are a human.</div>
@endif

@if (session('status'))
<div class="alert alert-success">We sent you the password reset link in Email. Please click on it to change your password.</div>
@endif

@if ($errors->has('email'))
<div class="alert alert-danger">{{ $errors->first('email') }}</div>
@endif
 
@if ($errors->has('g-recaptcha-response'))
<div class="alert alert-danger">Please prove you are a human.</div>
@endif




<form class="form-signin" id="userloginform1" action="{{ route('password.email') }}" method="post" onsubmit="" autocomplete="on">

{{ csrf_field() }}

<h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Send Password Reminder</h1>

<div class="form-group">
<label for="email">Email address</label>
       <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required="">
</div>


<div class="{{ config('myconfig.istestsite') ? "hidden" : "" }}">
	<input type="hidden" class="hiddenRecaptcha required" name="g-recaptcha-response" id="hiddenRecaptcha">
<div id="captcha"></div>
</div>

<button class="btn btn-success btn-block" type="submit">Send Password Reset Link</button>

</form>
	</div>		

</div>
</div>

</div>
</div>
<script type="text/javascript">
$(function()
			{
 			$("#userloginform1").validate(
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
</script>

<script type="text/javascript">
// Displays the recpatcha form in the element with id "captcha"
function onloadCallback() {
	
	grecaptcha.execute("{{ config('myconfig.captcha.captcha_sitekey') }}", 
			 {action: 'forgotpassword'}).then(function(token) {
                if (token) {
				document.getElementById('hiddenRecaptcha').value = token;
				  $('#hiddenRecaptcha-error').hide();
                }
});
}
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render={{ config('myconfig.captcha.captcha_sitekey') }}" async defer></script>
@endsection
