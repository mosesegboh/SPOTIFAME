
@extends('admin/adminlayouts.main')

@section('content')

@if (auth()->check())


    <div class="content-wrapper">
    <div class="userlogin" id="userlogin">

    <div id="logreg-forms">
        
        @if (count($errors))
        @foreach ($errors->all() as $error)
          <p class="alert alert-danger">{{$error}}</p>
        @endforeach
        @else
                @if (session()->has('success'))
                    @if(is_array(session()->get('success')))
                        @foreach (session()->get('success') as $message)
                        <p class="alert alert-success">{{ $message }}</p>
                        @endforeach
                    @else
                        <p class="alert alert-success">{{ session()->get('success') }}</p>
                    @endif
                @endif
       @endif  
    
       @if (session()->has('error'))
                    @if(is_array(session()->get('error')))
                        @foreach (session()->get('error') as $message)
                        <p class="alert alert-danger">{{ $message }}</p>
                        @endforeach
                    @else
                        <p class="alert alert-danger">{{ session()->get('error') }}</p>
                    @endif
                @endif

       
        
        <form class="form-signin" id="profileform" action="{{ route('profile.update') }}" method="post" onsubmit="" autocomplete="off">
            {{ csrf_field() }}
            @method('PATCH')


            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">My Profile</h1>


        <div id="basicblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">
                <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">Basic Informations</span>

            <div class="form-group">
                <label for="username">Username*</label>
        <input type="text" class="form-control" name="username" placeholder="Your username" id="username" value="{{ $user->username }}" required="">
      </div>

      <div class="form-group">
        <label for="email">Email*</label>
<input type="text" class="form-control" name="email" placeholder="something@something.com" id="email" value="{{ $user->email }}" required="">
</div>

            <div class="form-group">
                <label for="name">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Your name" id="name" value="{{ $user->name ? $user->name : old('name') }}">
            </div>

        </div>
        
           
        @if($user->isdjremixer)
       <div id="djblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">DJ Block</span>
    
        <div class="form-group">
            <label for="djartistlink">Artist link on Spotify (if any):</label>
            <input type="text" class="form-control" name="djartistlink" placeholder="Artist Spotify link" id="djartistlink" value="{{ old('djartistlink') ? old('djartistlink') : $user->djartistlink }}" autocomplete="off">
        </div> 
        
        <div class="form-group">
            <label for="remixgenre">Remix genre (choose most relevant):</label>
            <input type="text" class="suggestgenre form-control" name="remixgenre" placeholder="...start typing" id="remixgenre" value="{{ old('remixgenre') ? old('remixgenre') : $user->remixgenre }}" autocomplete="off">
        </div>
        
        
        
       </div>
       @endif

            @if($user->isjournalist)

       <div id="mediablockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">Media Block</span>
    
        <p>Three articles you have authored or been editor to:</p>

        <div class="form-group">
            <label for="articlelink1">First Article Link</label>
            <input type="text" class="form-control" name="articlelink1" placeholder="Article link no. 1" id="articlelink1" value="{{ old('articlelink1') ? old('articlelink1') : $user->articlelink1 }}" required="" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="articlelink2">Second Article Link</label>
            <input type="text" class="form-control" name="articlelink2" placeholder="Article link no. 2" id="articlelink2" value="{{ old('articlelink2') ? old('articlelink2') : $user->articlelink2 }}" required="" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="articlelink3">Third Article Link</label>
            <input type="text" class="form-control" name="articlelink3" placeholder="Article link no. 3" id="articlelink3" value="{{ old('articlelink3') ? old('articlelink3') : $user->articlelink3 }}" required="" autocomplete="off">
        </div>
                     
        <p>Would you like any of these articles to be published/displayed on spotifame.com:</p>
        <div class="form-group row">
            <div class="col-sm-9">
                <select class="form-control border-secondary width200" id="articlepublish" name="articlepublish" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Displaying your articles on spotifame.com may make it easier for artists to find you through search functions and keywords. If you selected YES to publishing them on Spotifame.com you must have all rights to the articles, or any third party must agree to the articles being published on Spotifame.com">
                    
                    @if (old('articlepublish'))
                    <option value="1"{!! old('articlepublish')=='1' ? ' selected="selected"' : '' !!}>Yes</option>
                    <option value="0"{!! old('articlepublish')=='0' ? ' selected="selected"' : '' !!}>No</option>
                    @else
                    <option value="1"{!! $user->articlepublish=='1' ? ' selected="selected"' : '' !!}>Yes</option>
                    <option value="0"{!! $user->articlepublish=='0' ? ' selected="selected"' : '' !!}>No</option>
                    @endif

                </select>
            </div>
    </div>

    
                        
        
       </div>
       @endif


            <div id="generalblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">
                <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">General Block</span>
        
                <p>Your website (if any):</p>
                <div class="form-group">
                    <input type="text" class="form-control" name="yourwebsite" placeholder="Your website (if any)" id="yourwebsite" value="{{ old('yourwebsite') ? old('yourwebsite') : $user->yourwebsite }}" autocomplete="off">
                </div>
        
                <p>Social links:</p>
        
                <div class="form-group">
                    <label for="facebooklink">Facebook Link</label>
                    <input type="text" class="form-control" name="facebooklink" placeholder="Facebook Link" id="facebooklink" value="{{ old('facebooklink') ? old('facebooklink') : $user->facebooklink }}" autocomplete="off">
                </div>
        
                <div class="form-group">
                    <label for="soundcloudlink">Soundcloud Link</label>
                    <input type="text" class="form-control" name="soundcloudlink" placeholder="Soundcloud Link" id="soundcloudlink" value="{{ old('soundcloudlink') ? old('soundcloudlink') : $user->soundcloudlink }}" autocomplete="off">
                </div>
        
                <div class="form-group">
                    <label for="instagramlink">Instagram Link</label>
                    <input type="text" class="form-control" name="instagramlink" placeholder="Instagram Link" id="instagramlink" value="{{ old('instagramlink') ? old('instagramlink') : $user->instagramlink }}" autocomplete="off">
                </div>
        
                <div class="form-group">
                    <label for="tiktoklink">Tik Tok Link</label>
                    <input type="text" class="form-control" name="tiktoklink" placeholder="Tik Tok Link" id="tiktoklink" value="{{  old('tiktoklink') ? old('tiktoklink') : $user->tiktoklink }}" autocomplete="off">
                </div>
        
                <div class="form-group">
                    <label for="twitterlink">Twitter Link</label>
                    <input type="text" class="form-control" name="twitterlink" placeholder="Twitter Link" id="twitterlink" value="{{ old('twitterlink') ? old('twitterlink') : $user->twitterlink }}" autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="otherlink">Other Link</label>
                    <input type="text" class="form-control" name="otherlink" placeholder="Other Link" id="otherlink" value="{{ old('otherlink') ? old('otherlink') : $user->otherlink }}" autocomplete="off">
                </div>
                
        
        
            </div>



        <button class="btn btn-primary btn-block" type="submit">Save</button>
        <hr>
        <a href="{{ config('myconfig.config.server_url')  }}admin/passwordchange" target="_blank">Want to change password?</a>
      


</form>
        

</div>

</div>
</div>

<script type="text/javascript">
    var csrf_token='{{ csrf_token() }}';
    
        $(function()
                    {
                     $("#profileform").validate(
                        {
                            ignore: ".ignorefields input,.ignorefields select",//important cause of google captcha, cause default is hidden element ignored!!
                            onkeyup: function(element) {
                            var element_id = jQuery(element).attr('id');
                            var element_name = jQuery(element).attr('name');
                        
                            if (this.settings.rules[element_name].onkeyup !== false) {
                              jQuery.validator.defaults.onkeyup.apply(this, arguments);
                            }
                          },
                          onfocusout: function(element) {
                            var element_id = jQuery(element).attr('id');
                            var element_name = jQuery(element).attr('name');
                            if (this.settings.rules[element_name].onfocusout !== false) {
                              jQuery.validator.defaults.onfocusout.apply(this, arguments);
                            }
                          },
                          onclick: function(element) {
                            var element_id = jQuery(element).attr('id');
                            var element_name = jQuery(element).attr('name');
                            if (this.settings.rules[element_name].onclick !== false) {
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
                                remixgenre:
                                {
                                    required: function(element){
                                        return $("#djartistlink").val()!="";
                                    }
                                },
                                articlelink1:
                                {
                                    validurlcheck: true,
                                    required: true,
                                },
                                articlelink2:
                                {
                                    validurlcheck: true,
                                    required: true,
                                },
                                articlelink3:
                                {
                                    validurlcheck: true,
                                    required: true,
                                },
                                articlepublish:
                                {
                                    required: true,
                                },
                                iagree:
                                {
                                    required: true,
                                },
                                'artistlink[]':
                                {
                                    required: true,
                                    spotifyartistlinkcheck: true,
                                },
                                'artistgenre[]':
                                {
                                    required: true,
                                },
                                'playlistlink[]':
                                {
                                    required: true,
                                    spotifyplaylistlinkcheck: true,
                                },
                                soundcloudlink:
                                {
                                    soundcloudlinkcheck: true,
                                },
                                facebooklink:
                                {
                                    facebooklinkcheck: true,
                                },
                                twitterlink:
                                {
                                    twitterlinkcheck: true,
                                },
                                instagramlink:
                                {
                                    instagramlinkcheck: true,
                                },
                                tiktoklink:
                                {
                                    tiktoklinkcheck: true,
                                },
                                otherlink:
                                {
                                    validurlcheck: true,
                                },
                                yourwebsite:
                                {
                                    validurlcheck: true,
                                },
                                djartistlink:
                                {
                                    spotifyartistlinkcheck: true,
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
                                remixgenre:
                                {
                                    required: 'This field is required if you added an artist link.',
                                },
                                articlelink1:
                                {
                                    validurlcheck: 'Please add a valid article URL.',
                                    required: 'This field is required.',
                                },
                                articlelink2:
                                {
                                    validurlcheck: 'Please add a valid article URL.',
                                    required: 'This field is required.',
                                },
                                articlelink3:
                                {
                                    validurlcheck: 'Please add a valid article URL.',
                                    required: 'This field is required.',
                                },
                                articlepublish:
                                {
                                    required: 'Please choose something.',
                                },
                                iagree:
                                {
                                    required: 'You must agree before proceeding!',
                                },
                                'artistlink[]':
                                {
                                    required: 'Add an artist.',
                                    spotifyartistlinkcheck: 'Please add a valid spotify artist link.',
                                },
                                'artistgenre[]':
                                {
                                    required: 'Choose a genre for artist.',
                                },
                                'playlistlink[]':
                                {
                                    required: 'Add an playlist.',
                                    spotifyplaylistlinkcheck: 'Please add a valid spotify playlist link.',
                                },
                                soundcloudlink:
                                {
                                    soundcloudlinkcheck: 'Please add a valid soundcloud link.',
                                },
                                facebooklink:
                                {
                                    facebooklinkcheck: 'Please add a valid facebook link.',
                                },
                                twitterlink:
                                {
                                    twitterlinkcheck: 'Please add a valid twitter link.',
                                },
                                instagramlink:
                                {
                                    instagramlinkcheck: 'Please add a valid instagram link.',
                                },
                                tiktoklink:
                                {
                                    tiktoklinkcheck: 'Please add a valid tik tok link.',
                                },
                                otherlink:
                                {
                                    validurlcheck: 'Please add a valid URL.',
                                },
                                yourwebsite:
                                {
                                    validurlcheck: 'Please add a valid URL.',
                                },
                                djartistlink:
                                {
                                    spotifyartistlinkcheck: 'Please add a valid spotify artist link.',
                                },
                            },						
                            errorClass: "text-danger is-invalid",
                            validClass: "text-success is-valid",
                            // Do not change code below
                            errorPlacement: function(error, element)
                            {
                                    
                                error.appendTo(element.parent());
                                
                                
                            },
                            focusInvalid: false,
                            invalidHandler: function(form, validator) {
    
                            if (!validator.numberOfInvalids())
                                return;
                               
                            $('html, body').animate({
                                scrollTop: $(validator.errorList[0].element).offset().top-100
                            }, 500, function() {
                                // Animation complete. 
                                $(validator.errorList[0].element).focus();
    
                            });
                           
                            
    
                            }
                            
                            
                        });
                        
                    });
        jQuery.validator.addMethod("ContainsAtLeastOneDigit", function (value) { 
                return /[a-z].*[0-9]|[0-9].*[a-z]/i.test(value);
        }, 'Your input must contain at least 1 letter and 1 number.');
        
        jQuery.validator.addMethod("notEqual", function(value, element, param) {
          return this.optional(element) || value != param;
        }, "Please specify a different (non-default) value.");
    
        jQuery.validator.addMethod("validurlcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        
        var re = new RegExp(/^(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\.@:%_\+~#=]+)+((\.[a-zA-Z]{2,3})+)(\/(.)*)?(\?(.)*)?/i);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid URL.");
    
        jQuery.validator.addMethod("soundcloudlinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:soundcloud\.com|snd\.sc)(?:\/\w+(?:-\w+)*)+$/);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid soundcloud link");
            
        jQuery.validator.addMethod("facebooklinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(mbasic.facebook|m\.facebook|facebook|fb)\.(com|me)\/(?:(?:\w\.)*#!\/)?(?:pages\/)?(?:[\w\-\.]*\/)*([\w\-\.]*).+/i);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid facebook link");
    
    
        jQuery.validator.addMethod("instagramlinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
    
        var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:instagram.com|instagr.am)\/.+/i);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid instagram link");
    
        jQuery.validator.addMethod("twitterlinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^((http|https):\/\/)(?:www\.)?twitter\.com\/(#!\/)?[a-zA-Z0-9_]+$/i);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid twitter link");
    
        jQuery.validator.addMethod("tiktoklinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^((http|https):\/\/)(www[.])?tiktok.com\/.+/i);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid tik tok link");
    
    
        jQuery.validator.addMethod("spotifylinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^((http|https):\/\/)(?:www\.)?(?:open\.spotify\.com)(?:\/\w+(?:-\w+)*)+$/);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid spotify link.");
    
    
    
        jQuery.validator.addMethod("spotifyplaylistlinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^(https?:\/\/(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/playlist\/)([a-zA-Z0-9]+)(.*)$/);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid spotify playlist link.");
    
        jQuery.validator.addMethod("spotifyartistlinkcheck", function (value, element, param) {
        if (this.optional(element)) {
            return true;
        }
        var re = new RegExp(/^(https?:\/\/(?:www\.)?open.spotify.com(?:\/user)?(?:\/spotify)?\/artist\/)([a-zA-Z0-9]+)(.*)$/);
    
            if (re.test(value)) {
                return true;
                }
                
            }, "Please add a valid spotify artist link.");
    
         
</script>


@endif
@endsection