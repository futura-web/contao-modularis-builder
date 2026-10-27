<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_content']['modularisTemplate'] = ['Modularis template', 'Please choose a template. Its fields will appear afterwards.'];
$GLOBALS['TL_LANG']['tl_content']['snippet_legend'] = 'Modularis';
$GLOBALS['TL_LANG']['tl_content']['snippet_fields_legend'] = 'Modularis content';

// Companion fields that automatically appear next to every image placeholder
$GLOBALS['TL_LANG']['tl_content']['snippet_image'] = [
    'size' => ['Image size', 'Here you can set the dimensions of the image and the scaling mode. Without a selection, the template image size is used.'],
    'fullsize' => ['Fullsize/New window', 'Show the image in a lightbox or open the link in a new browser window.'],
    'alt' => ['Alternative text', 'Here you can enter alternative text for the image (alt attribute).'],
    'title' => ['Image title', 'Here you can enter the title of the image (title attribute).'],
    'url' => ['Image link address', 'A custom image link address overwrites the lightbox link, so the image can no longer be shown in the fullsize view.'],
    'caption' => ['Caption', 'Here you can enter a short text that will be shown below the image.'],
];
