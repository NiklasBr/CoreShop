<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class EmailColumn extends Document\Tag\Area\AbstractArea
{
    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }
}
