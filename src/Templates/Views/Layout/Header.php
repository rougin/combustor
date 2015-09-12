<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Document <?php echo ($this->uri->segment(1)) ? ' - ' . ucwords(str_replace('_', ' ', $this->uri->segment(1))) : NULL; ?></title>
{% for styleSheet in styleSheets %}
    <link rel="stylesheet" type="text/css" href="{{ styleSheet }}">
{% endfor %}
</head>
<body>
    <div class="{{ bootstrapContainer }}">
