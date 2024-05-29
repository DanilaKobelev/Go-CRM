<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>photo</title>
</head>
<body>
<?php
function is_image_safe($file) {
    $finfo = new finfo(FILEINFO_MIME);
    $mime = $finfo->file($file);
    if (strpos($mime, 'image/') !== 0) {
        return false;
    }

    if (filesize($file) <= 0) {
        return false;
    }

    return true;
}

function resize_image($file, $w, $h, $crop=FALSE, $is_show_msg = false) {
    if (!is_image_safe($file)) {
        if ($is_show_msg) {
            echo "Error: unsafe image file $file <br>\r\n";
        }
        return false;
    }

    try {
        $src = imagecreatefrompng($file);
    } catch (Exception $e) {
        $src = false;
    }

    if($src==false) {
        try {
            $src = imagecreatefromjpeg($file);
        } catch (Exception $e) {
            $src = false;
        }
    }
    if(!$src) {
        if ($is_show_msg) {
            echo "Error read image $file <br>\r\n";
        }
        return false;
    }
    $width = imagesx($src);
    $height = imagesy($src);
    if ($is_show_msg) {
        echo "W: $width H: $height" . PHP_EOL;
    }

    $r = $width / $height;
    if ($crop) {
        $width_orig = $width;
        $height_orig = $height;
        if ($width > $height) {
            $width = $height;
        } else {
            $height = $width;
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = intval($h*$r);
            $newheight = $h;
        } else {
            $newheight = intval($w/$r);
            $newwidth = $w;
        }
    }
    if ($is_show_msg) {
        echo "W: $width H: $height NW:$newwidth NH: $newheight" . PHP_EOL;
    }

    $dst = imagecreatetruecolor($newwidth, $newheight);
    if ($crop) {
        if ($is_show_msg) {
            echo "Original: ($width_orig x $height_orig) Thumb: ($width x $height) \r\n";
        }
        $src_x = intval($width_orig / 2 - $width / 2);
        $src_y = intval($height_orig / 2 - $height / 2);

        imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $newwidth, $newheight, $width, $height);
    } else {
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    return $dst;
}

function clear_old_cache_files($cache_dir, $max_age_seconds) {
    if (!is_dir($cache_dir)) {
        return;
    }

    $files = glob($cache_dir . '/*');
    $now = time();

    foreach ($files as $file) {
        if (is_file($file) && ($now - filemtime($file)) > $max_age_seconds) {
            unlink($file);
        }
    }
}

$cache_dir = '/test';
$cache_file = $cache_dir . '/test.jpg';
$max_cache_age_seconds = 86400; // 1 день

clear_old_cache_files($cache_dir, $max_cache_age_seconds);

$img = resize_image("/test/test.jpg", 200, 100);

if (!file_exists($cache_file)) {
    $img = resize_image("/test/test.jpg", 200, 100);
    if ($img!=false) {
        imagepng($img,$cache_file,9);
    }
}
if (file_exists($cache_file)) {
    header('Content-Type: image/png');
    header('Content-Length: ' . filesize($cache_file));
    readfile($cache_file);
    exit;
}

$result = $cache_file;
?>
<br>
<img class="banner" src="<?=$result?>"/>

</body>
</html>