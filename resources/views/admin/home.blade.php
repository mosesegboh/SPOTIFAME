
@extends('admin/adminlayouts.main')

@section('content')
@if (auth()->check())


<div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <div class="card-body d-flex align-items-center justify-content-between">
                    <h4 class="mt-1 mb-1">Hi {{ auth()->user()->username }}, Welcome back!</h4>
                    
                  </div>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @if (auth()->user()->isUser())
                    <p>Our site is currently in beta testing mode, and the functionality is not available to everyone. While we are developing our core site structure, our site functionality is only available in beta testing mode, to a select group of users:</p>
                    
                    <ul>
                    <li>A)	Major artists and  their managers. (Artists with more than 250k followers on Spotify)</li>
                    <li>B)	Top 1000 DJs in the world, as curate by our partner, The Official Global DJ Rankings.</li>
                    </ul>
                    <br>
                   <p>If you are eligible for beta testing access, as described in points A and B above, please fill out this form: 

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

           <form class="form-signin" id="homeform" action="{{ route('admin.sendhomeform') }}" method="post" onsubmit="" autocomplete="on">

            {{ csrf_field() }}


                    <div class="form-group">
                      <label for="name">Your name*:</label>
              <input type="text" class="form-control" name="name" id="name" placeholder="Your name" value="{{ old('name') }}" required="">
                    </div>


                    <div class="form-group">
                      <label for="djname">Artist/DJ name:</label>
              <input type="text" class="form-control" name="djname" id="djname" value="{{ old('djname') }}" placeholder="Artist/DJ name">
                    </div>

                    <div class="form-group">
                      <label for="djname">Email*:</label>
              <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="Email" required="">
                    </div>


                    <div class="form-group">
                      <label for="djname">Phone:</label>
              <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone">
                    </div>

                    <div class="form-group">
                      <label for="description">Describe your business relationship with the artist/DJ*:</label>
                      <textarea class="form-control" name="description" id="description" rows="4" required="">{{ old('description') }}</textarea>
                    </div>

  <button class="btn btn-primary btn-block" type="submit">Submit</button>

                  </form>

                </div>

                    @endif
            </div>
          </div>
         
</div>
<!-- content-wrapper ends -->



@endif
@endsection