<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">


		<title>@yield('title', config('app.name', '온스토리'))</title>
    <link media="all" type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
		
		@yield('styles')   
	</head>
	<body class="@yield('body_class')">
			@yield('content')
	</body>

@yield('scripts')
</html>