<!DOCTYPE html>
<html lang="en">

<head>
    <title>Docs</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Roboto:300,400,700" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

    </style>
</head>

<redoc spec-url="{{ asset('shine-swagger/api-spec.json') }}"></redoc>
<script src="https://cdn.jsdelivr.net/npm/redoc@next/bundles/redoc.standalone.js"> </script>
</body>

</html>
