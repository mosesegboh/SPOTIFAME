@extends('admin/beforelogin.main')

@section('content')

<div class="container-fluid px-0">
    <div class="content-wrapper">
    <div class="userlogin" id="userlogin">

    <div id="logreg-forms">
        
        
        <form class="form-signin" id="regform" action="{{ route('regsteps.step2.submit') }}" method="post" onsubmit="" autocomplete="off">
            {{ csrf_field() }}
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Register - Step 2</h1>



  <div id="stateinprogwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">
    <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">What is your role?</span>

    <p class="text-muted text-center">(you may select several)</p>

                    <div>
                        <div class="form-check d-inline-block">
                        <label class="form-check-label">
                        <input name="isartist" id="isartist" type="checkbox" class="form-check-input"{{ $user->isartist ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                    Artist
                            <i class="input-helper"></i></label>
                        </div>
                    </div>
    
                    <div>
                      <div class="form-check d-inline-block">
                      <label class="form-check-label">
                      <input name="isplaylistowner" id="isplaylistowner" type="checkbox" class="form-check-input"{{ $user->isplaylistowner ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                  Playlist editor/owner
                          <i class="input-helper"></i></label>
                      </div>
                  </div>
  
                  <div>
                      <div class="form-check d-inline-block">
                      <label class="form-check-label">
                      <input name="islabel" id="islabel" type="checkbox" class="form-check-input"{{ $user->islabel ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                  Label
                          <i class="input-helper"></i></label>
                      </div>
                  </div>
  
                  <div>
                      <div class="form-check d-inline-block">
                      <label class="form-check-label">
                      <input name="ismanager" id="ismanager" type="checkbox" class="form-check-input"{{ $user->ismanager ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                  Manager
                          <i class="input-helper"></i></label>
                      </div>
                  </div>


                  <div>
                      <div class="form-check d-inline-block">
                      <label class="form-check-label">
                      <input name="isjournalist" id="isjournalist" type="checkbox" class="form-check-input"{{ $user->isjournalist ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                      Journalist/Media
                          <i class="input-helper"></i></label>
                      </div>
                  </div>

                  <div>
                      <div class="form-check d-inline-block">
                      <label class="form-check-label">
                      <input name="isdjremixer" id="isdjremixer" type="checkbox" class="form-check-input"{{ $user->isdjremixer ? ' checked="checked"' : '' }} value="1" autocomplete="off">
                                      Dj/Remixer
                          <i class="input-helper"></i></label>
                      </div>
                  </div>
    
                    
    
       </div>

<div class="clearfix">
  <div class="float-right">

  <button type="submit" class="btn btn-success btn-icon-text mt-0">Next<i class="mdi mdi-chevron-right btn-icon-append align-middle"></i></button>
  </div>
</div>
     
        
       
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
                            
                        },
                        
                        // Messages for form validation
                        messages:
                        {
                            
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
    var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:open\.spotify\.com)(?:\/\w+(?:-\w+)*)+$/);

        if (re.test(value)) {
            return true;
            }
            
        }, "Please add a valid spotify link.");

    </script>
    
    @endsection
    