<?php

return [
    // Provide collection names as keys and corresponding field handles as values
    'collections' => [],
    // By default we only use h2 (level 2) if nothing is set here.
    // Use the corresponding integer for the html element if you want to add specific heading levels:
    // h3 -> 3
    // h4 -> 4
    // ...
    'includeLevels' => [],
    // link to sluggified title fragments - 
    // you will need to provide these ids by extending your Markdown Parser (see README)
    'anchorLinks' => false
];