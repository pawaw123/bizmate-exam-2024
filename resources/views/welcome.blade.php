<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Vue Coding Exam Tourist Weather Locator</title>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])
</head>
<body class='bg-gray-300'>
    <div id="app" class="flex justify-center pt-16">
        <tourist-app></tourist-app>
    </div>
</body>
</html>
