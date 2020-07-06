
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

       
        
        
                   
        
        <form class="form-signin" id="passchangeform" action="{{ route('password.change') }}" method="post" onsubmit="" autocomplete="off">
            {{ csrf_field() }}
            @method('PATCH')


            <h1 class="h3 mb-3 font-weight-normal" style="text-align: center">Change Password</h1>


     <div class="form-group">
                <label for="oldpassword">Old Password*</label>
        <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Old Password" required="" autocomplete="off">
      </div>

      <div class="form-group">
                <label for="password">New Password*</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="New Password" required="" autocomplete="off">
      </div>
      <div class="form-group">
        <label for="password_confirmation">Confirm New Password*</label>
               <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password" id="password_confirmation" autocomplete="off">
        </div>


        <button class="btn btn-primary btn-block" type="submit">Submit</button>
     
       
</form>
        

</div>

</div>
</div>




@endif
@endsection