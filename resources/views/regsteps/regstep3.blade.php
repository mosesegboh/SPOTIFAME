@extends('admin/beforelogin.main')

@section('content')


<div class="container-fluid px-0">
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

        
        <form class="form-signin" id="regform" action="{{ route('regsteps.step3.submit') }}" method="post" onsubmit="" autocomplete="off">
            {{ csrf_field() }}
            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Register - Step 3</h1>
         

   @if($user->isartist || $user->islabel || $user->ismanager)
  <div id="artistblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

    <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">Artist/Manager/Label Block</span>


    <div class="form-group">
        <label for="labelname">Label Name (if none leave empty): </label>
        <input type="text" class="form-control" name="labelname" placeholder="Label Name" id="labelname" value="{{ old('labelname') }}" autocomplete="off">
    </div>

    <div class="position-relative p-4 mt-4 mb-3 border border-secondary">
        <div class="form-group">
            <label for="artistlink_0">Spotify Artist Link:</label>
            <input type="text" class="form-control" name="artistlink[]" placeholder="Spotify Artist Link" id="artistlink_0" value="{{ old('artistlink')[0] }}" required="" autocomplete="off">
        </div>
     

        <div class="form-group">
            <label for="artistgenre_0">Artist Genre (choose most relevant):</label>
            <input type="text" class="suggestgenre form-control" name="artistgenre[]" placeholder="...start typing" id="artistgenre_0" value="{{ old('artistgenre')[0] }}" required="" autocomplete="off">
            
        </div>

        

        @if(!empty(old('artistlink')))
            @foreach(old('artistlink') as $j => $single_element) 
                @if(last(old('artistlink')) === $single_element)
                <div id="artistaddwrap" data-artistid="{{ $j }}">
                @else
                   
                @endif
            @endforeach
        @else
        <div id="artistaddwrap" data-artistid="0">
        @endif


        @if(!empty(old('artistlink')))
        @foreach( old('artistlink') as $i => $field)


            @if($i>0)
        
        <div id="artistitemwrap_{{$i}}" class="border-top border-secondary mt-4 pt-2">
                <div class="form-group">
                    <label for="artistlink_{{$i}}">Spotify Artist Link:</label>
                    <div class="pr-4 position-relative">
                    <input type="text" class="form-control" name="artistlink[]" placeholder="Spotify Artist Link" id="artistlink_{{$i}}" value="{{ old('artistlink')[$i] }}" required="" autocomplete="off">
                    </div>
                </div>


    
            <div class="form-group">
                <div class="pr-4 position-relative">
                    <label for="artistgenre_{{$i}}">Artist Genre (choose most relevant):</label>
                    <div style="top:0px;" class="deletebuttonwrapper deleteartistclick" data-itemid="{{$i}}">
                     <i class="mdi mdi-delete-forever btn-icon-append align-middle"></i>
                    </div>
                </div>

                <div class="pr-4 position-relative">
                <input type="text" class="suggestgenre form-control" name="artistgenre[]" placeholder="...start typing" id="artistgenre_{{$i}}" value="{{ old('artistgenre')[$i] }}" required="" autocomplete="off">
                </div>
            </div>
        </div>
            
            @endif


        @endforeach
        @endif



     </div>


        <div class="clearfix mt-4">
            <div class="float-right">
          <button type="submit" class="addanotherartist btn btn-info btn-icon-text mt-0">Add artist<i class="mdi mdi-plus btn-icon-append align-middle"></i></button>
            </div>
        </div>
    </div>


       </div>
       @endif

       @if($user->isplaylistowner)
       <div id="playlistblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">Playlist Block</span>
    
        <div class="form-group">
            <label for="playlistlink_0">Spotify Playlist Link:</label>
            <input type="text" class="form-control" name="playlistlink[]" placeholder="Spotify Playlist Link" id="playlistlink_0" value="{{ old('playlistlink')[0] }}" required=""  autocomplete="off">
        </div>

        @if(!empty(old('playlistlink')))
            @foreach(old('playlistlink') as $j => $single_element) 
                @if(last(old('playlistlink')) === $single_element)
                <div id="playlistaddwrap" data-playlistid="{{ $j }}">
                @else
                   
                @endif
            @endforeach
        @else
        <div id="playlistaddwrap" data-playlistid="0">
        @endif

            @if(!empty(old('playlistlink')))
            @foreach( old('playlistlink') as $i => $field)

            @if($i>1)
            <div id="playlistitemwrap_{{$i}}" class="border-top border-secondary mt-4 pt-2 form-group">
            <label for="playlistlink_{{$i}}">Spotify Playlist Link:</label>
                <div class="pr-4 position-relative">
                    <input type="text" class="form-control" name="playlistlink[]" placeholder="Spotify Playlist Link" id="playlistlink_{{$i}}" value="{{ old('playlistlink')[$i] }}" required=""  autocomplete="off">
                    <div class="deletebuttonwrapper deleteplaylistclick" data-itemid="{{$i}}"><i class="mdi mdi-delete-forever btn-icon-append align-middle"></i></div>    
                </div>
            </div>
            @endif

            @endforeach
            @endif
        </div>
                     
        <div class="clearfix mt-4">
            <div class="float-right">
          <button type="submit" class="addanotherplaylist btn btn-info btn-icon-text mt-0">Add playlist<i class="mdi mdi-plus btn-icon-append align-middle"></i></button>
            </div>
        </div>
                        
        
       </div>
       @endif

       @if($user->isdjremixer)
       <div id="djblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">DJ Block</span>
    
        <div class="form-group">
            <label for="djartistlink">Artist link on Spotify (if any):</label>
            <input type="text" class="form-control" name="djartistlink" placeholder="Artist Spotify link" id="djartistlink" value="{{ old('djartistlink') }}" autocomplete="off">
        </div> 
        
        <div class="form-group">
            <label for="remixgenre">Remix genre (choose most relevant):</label>
            <input type="text" class="suggestgenre form-control" name="remixgenre" placeholder="...start typing" id="remixgenre" value="{{ old('remixgenre') }}" autocomplete="off">
        </div>
        
        
        
       </div>
       @endif

       @if($user->isjournalist)

       <div id="mediablockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">

        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">Media Block</span>
    
        <p>Please provide links to three articles you have authored or been editor to:</p>

        <div class="form-group">
            <input type="text" class="form-control" name="articlelink1" placeholder="Article link no. 1" id="articlelink1" value="{{ old('articlelink1') }}" required="" autocomplete="off">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="articlelink2" placeholder="Article link no. 2" id="articlelink2" value="{{ old('articlelink2') }}" required="" autocomplete="off">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="articlelink3" placeholder="Article link no. 3" id="articlelink3" value="{{ old('articlelink3') }}" required="" autocomplete="off">
        </div>
                     
        <p>Would you like any of these articles to be published/displayed on spotifame.com:</p>
        <div class="form-group row">
            <div class="col-sm-9">
                <select class="form-control border-secondary width200" id="articlepublish" name="articlepublish" autocomplete="off" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Displaying your articles on spotifame.com may make it easier for artists to find you through search functions and keywords. If you selected YES to publishing them on Spotifame.com you must have all rights to the articles, or any third party must agree to the articles being published on Spotifame.com">
                    <option value=""{!! (old('articlepublish')!='1' && old('articlepublish')!='0') ? ' selected="selected"' : '' !!}>- Choose -</option>
                    <option value="1"{!! old('articlepublish')=='1' ? ' selected="selected"' : '' !!}>Yes</option>
                    <option value="0"{!! old('articlepublish')=='0' ? ' selected="selected"' : '' !!}>No</option>
                </select>
            </div>
    </div>

    
                        
        
       </div>
       @endif


       <div id="generalblockwrap" class="position-relative p-4 mt-4 mb-3 border border-secondary">
        <span class="position-absolute pl-2 pr-2 myformwhitebg mymenutitle">General Block</span>

        <p>Your website (if any):</p>
        <div class="form-group">
            <input type="text" class="form-control" name="yourwebsite" placeholder="Your website (if any)" id="yourwebsite" value="{{ old('yourwebsite') }}" autocomplete="off">
        </div>

        <p>Social links:</p>

        <div class="form-group">
            <input type="text" class="form-control" name="facebooklink" placeholder="Facebook Link" id="facebooklink" value="{{ old('facebooklink') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="soundcloudlink" placeholder="Soundcloud Link" id="soundcloudlink" value="{{ old('soundcloudlink') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="instagramlink" placeholder="Instagram Link" id="instagramlink" value="{{ old('instagramlink') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="tiktoklink" placeholder="Tik Tok Link" id="tiktoklink" value="{{ old('tiktoklink') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="twitterlink" placeholder="Twitter Link" id="twitterlink" value="{{ old('twitterlink') }}" autocomplete="off">
        </div>
        
        <div class="form-group">
            <input type="text" class="form-control" name="otherlink" placeholder="Other Link" id="otherlink" value="{{ old('otherlink') }}" autocomplete="off">
        </div>
        


    </div>
    
       
       


       <div>
        <div class="form-check d-inline-block">
        <label class="form-check-label">
        <input name="iagree" id="iagree" type="checkbox" class="form-check-input" autocomplete="off" {!! old('iagree')=='on' ? ' checked="checked"':'' !!}>
        I agree to send all data above to spotifame.com and submit that there are no personal data included. All data above relates to a public artist or publicly available playlists or music available on Spotify.com. Spotifame.com does not store any personal data about users.
              <i class="input-helper"></i></label>
        </div>

    </div>

<div class="clearfix">
    <div class="float-left">
  <button type="button" data-href="{{ route('regsteps.step2') }}" class="btn btn-danger btn-icon-text backstep"><i class="mdi mdi-chevron-left btn-icon-prepend align-middle"></i>Back</button>
    </div>
  
    <div class="float-right">
  <button type="submit" class="btn btn-success btn-icon-text mt-0">Finish<i class="mdi mdi-chevron-right btn-icon-append align-middle"></i></button>
    </div>
</div>
     
        
       
</form>
        

</div>

</div>
</div>
</div>



<script type="text/javascript">
var csrf_token='{{ csrf_token() }}';

    $(function()
                {
                 $("#regform").validate(
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
    
    @endsection
    