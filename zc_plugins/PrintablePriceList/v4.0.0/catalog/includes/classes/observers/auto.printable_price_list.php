<?php
class zcObserverPrintablePriceList extends base
{
    public function __construct()
    {
        global $current_page_base;

        if ($current_page_base === FILENAME_PRICELIST) {
            require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'PrintablePriceList.php';
        }
    }
}
