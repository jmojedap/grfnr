<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Probando</title>
    <style>
        body {
            color: white;
        }
        .layout {
            display: grid;
            grid-template-rows: 40px 1fr;
            grid-template-columns: minmax(12rem, 270px) 1fr;
            height: 100vh;
            width: 100%;
        }

        .layout div,aside,header{
            padding: 0.5em;
        }

        .header{
            grid-row: 1/2;
            grid-column: 1/3;
            background-color: green;
        }
        .sidebar{
            grid-row: 2/3;
            grid-column: 1/2;
            background-color: #222d32;
            color: white;
        }

        .content{
            grid-column: 2 / 3;
            grid-row: 2/3;
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="layout">
        <header class="header">header</header>
        <aside class="sidebar">sidebar</aside>
        <div class="content">content</div>
    </div>
</body>
</html>