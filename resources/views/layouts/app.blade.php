<!doctype html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	@include('includes.head')
</head>
<body>

	@include('includes.header')	

		<div class="app-container">

			<div class="uk-container uk-container-center">
				
					@yield('content')
				
			</div>			

		</div><!-- .wrapper -->		
													
	@include('includes.footer')	

	@include('includes.script')

</body>
</html>