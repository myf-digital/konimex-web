<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// drive imagemagick / gd
$config['image_driver'] = 'imagemagick';
// quality compress (0–100)
$config['image_quality'] = 75;
// for imagemagick: path ImageMagick
$config['image_magick_path'] = '/opt/homebrew/bin/magick';
