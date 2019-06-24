<?php

defined('C5_EXECUTE') or die('Access Denied.');
$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (is_object($f) && $fp->canWrite()) {
    $to = $f->getTypeObject();
    if ($to->getGenericType() == FileType::T_IMAGE) {
        $imp = Loader::helper('concrete/image');
        $width = $f->getAttribute('width');
        $height = $f->getAttribute('height');
        $ext = $f->getExtension();

        $viewPortW = $_POST["viewPortW"];
        $viewPortH = $_POST["viewPortH"];
        $pWidth = $_POST["imageW"];
        $pHeight = $_POST["imageH"];

        $image = $imp->startImageProcess($f);
        if ($image) {
            $width = imagesx($image);
            $height = imagesy($image);

            // Resample
            $image_p = imagecreatetruecolor($pWidth, $pHeight);
            $imp->setTransparency($image, $image_p, $ext);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $pWidth, $pHeight, $width, $height);
            imagedestroy($image);
            $widthR = imagesx($image_p);
            $heightR = imagesy($image_p);

            $selectorX = $_POST["selectorX"];
            $selectorY = $_POST["selectorY"];

            if ($_POST["imageRotate"]) {
                $angle = 360 - $_POST["imageRotate"];
                $image_p = imagerotate($image_p, $angle, 0);

                $pWidth = imagesx($image_p);
                $pHeight = imagesy($image_p);

                $diffW = abs($pWidth - $widthR) / 2;
                $diffH = abs($pHeight - $heightR) / 2;

                $_POST["imageX"] = ($pWidth > $widthR ? $_POST["imageX"] - $diffW : $_POST["imageX"] + $diffW);
                $_POST["imageY"] = ($pHeight > $heightR ? $_POST["imageY"] - $diffH : $_POST["imageY"] + $diffH);
            }

            $dst_x = $src_x = $dst_y = $src_y = 0;

            if ($_POST["imageX"] > 0) {
                $dst_x = abs($_POST["imageX"]);
            } else {
                $src_x = abs($_POST["imageX"]);
            }
            if ($_POST["imageY"] > 0) {
                $dst_y = abs($_POST["imageY"]);
            } else {
                $src_y = abs($_POST["imageY"]);
            }

            $viewport = imagecreatetruecolor($_POST["viewPortW"], $_POST["viewPortH"]);
            $imp->setTransparency($image_p, $viewport, $ext);

            imagecopy($viewport, $image_p, $dst_x, $dst_y, $src_x, $src_y, $pWidth, $pHeight);
            imagedestroy($image_p);

            $selector = imagecreatetruecolor($_POST["selectorW"], $_POST["selectorH"]);
            $imp->setTransparency($viewport, $selector, $ext);
            imagecopy($selector, $viewport, 0, 0, $selectorX, $selectorY, $_POST["viewPortW"], $_POST["viewPortH"]);

            $file = Loader::helper('file')->getTemporaryDirectory() . '/' .time() . "." . $ext;
            $imp->parseImage($ext, $selector, $file);
            imagedestroy($viewport);

            $fi = new FileImporter();
            $resp = $fi->import($file, $f->getFileName(), $f);
        }
    }
}
