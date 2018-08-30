<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    
<?php


//833 000 1234

















function permutations(array $elements)
{
    if (count($elements) <= 1) {
        yield $elements;
    } else {
        foreach (permutations(array_slice($elements, 1)) as $permutation) {
            // foreach (range(0, count($elements) - 1) as $i) {
            for ($i = 0; $i <= 9; $i++) {
                yield array_merge(
                    array_slice($permutation, 0, $i),
                    [$elements[0]],
                    array_slice($permutation, $i)
                );

            }
        }
    }
}

$list = ['0', '1', '2', '3', '4'];

foreach (permutations($list) as $permutation) {
    echo implode(',', $permutation) . "<br>";
}


?>
</body>
</html>
