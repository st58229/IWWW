<?php


class TableRows extends RecursiveIteratorIterator
{

    private $id;

    function __construct($it)
    {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function getMimeType($filename)
    {
        $mimetype = false;
        if(function_exists('finfo_open')) {
            // open with FileInfo
        } elseif(function_exists('getimagesize')) {
            // open with GD
        } elseif(function_exists('exif_imagetype')) {
            // open with EXIF
        } elseif(function_exists('mime_content_type')) {
            $mimetype = mime_content_type($filename);
        }
        return $mimetype;
    }

    function current()
    {
        if ($this->getMimeType(parent::current()) == false) return "<td style='width:150px;border:1px solid black;'>" . parent::current() . "</td>";
        else
        {
            $image = imagecreatefromstring(parent::current());
            ob_start(); //You could also just output the $image via header() and bypass this buffer capture.
            imagejpeg($image, null, 80);
            $data = ob_get_contents();
            ob_end_clean();
            $thumb = '<img src="data:image/jpg;base64,' .  base64_encode($data)  . '" />';
            return "<td style='width:150px;border:1px solid black;'>" . $thumb  . "</td>";
        }


    }

    function beginChildren()
    {
        $this->id = parent::current();
        echo "<tr>";
    }

    function endChildren()
    {
        echo "<td><a href='userEdit.php?id=$this->id' title='Editovat záznam'>&#x270e</a></td>" . "<td><a href='userDelete.php?id=$this->id' title='Vymazat záznam'>&#x1F5D1</a></td>" . "</tr>" . "\n";
    }
}