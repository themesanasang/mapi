
<?php
  $urlcheck =  explode("/", Request::path());
  //$activeurl = $urlcheck[0].'/'.$urlcheck[1];
  $activeurl = Request::path();

  if( count($urlcheck) == 1 ){
      $activeurl = Request::path();
  }
  if( count($urlcheck) == 2 ){
      $activeurl = $urlcheck[0].'/'.$urlcheck[1];
  }
   if( count($urlcheck) >= 3 ){
      $activeurl = $urlcheck[0];
  }
?>

<nav class="app-navbar uk-navbar uk-navbar-attached" data-uk-sticky="{boundary:'#define-an-offset'}">  
  @if (Auth::guest())
       <a class="uk-navbar-brand" href="{{ url('/') }}">MAPI</a>        
  @else
      @if (Auth::user()->type == 'admin')
        <a class="uk-navbar-brand" href="{{ url('/systems') }}">MAPI</a>      
       @else
        <a class="uk-navbar-brand" href="{{ url('/home') }}">MAPI</a>
       @endif
  @endif 

  <ul class="uk-navbar-nav uk-hidden-small">	
		
		@if (Auth::guest())
            
    @else	

        	<li class="{{ $activeurl == '/' ? 'uk-active' : '' }} {{ $activeurl == 'systems' ? 'uk-active' : '' }} {{ $activeurl == 'home' ? 'uk-active' : '' }}">
	    		 @if (Auth::user()->type == 'admin')
            <a href="{{ url('/systems') }}">หน้าหลัก</a>      
           @else
            <a href="{{ url('/home') }}">หน้าหลัก</a>
           @endif
           
	    	  
          </li>
          <li class="{{ ($activeurl == 'routes' || Request::is('routes/*')) ? 'uk-active' : '' }}">
            <a href="{{ url('/routes') }}">อุปกรณ์เชื่อมต่อ</a>
          </li>
        	@if (Auth::user()->type == 'admin')
				    @include('includes.menu-admin')      	
        	@else
				    @include('includes.menu-user')
        	@endif

        	<li><a href="{{ url('auth/logout') }}">ออกจากระบบ</a></li>

    @endif

	</ul>

	<a href="#my-menuleft" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
</nav>



<div id="my-menuleft" class="uk-offcanvas" aria-hidden="false">
   <div class="uk-offcanvas-bar uk-offcanvas-bar-show">
   		<div class="uk-panel uk-panel-box">
   		<h3 class="uk-panel-title">MAPI</h3>
		<ul class="uk-nav uk-nav-side uk-nav-parent-icon">
		    
		</ul>
		</div>
   </div>
</div>