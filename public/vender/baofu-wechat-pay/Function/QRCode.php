<?php
class QRCodePng{    
    public static function generate($text, $outfile=false, $level=QR_ECLEVEL_L, $size=5, $margin=4, $saveandprint=false){   
                $enc = QRencode::factory($level, $size, $margin);
                return $enc->encodePNG($text, $outfile, $saveandprint=false);   
    }
}

