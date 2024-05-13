<?php
class zcObserverPrintablePriceList extends base
{
    public function __construct()
    {
        global $current_page_base;

        if ($current_page_base !== FILENAME_PRICELIST) {
            return;
        }

        // -----
        // Instantiate the price list for use by the template and observed notifications.
        //
        require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'PrintablePriceList.php';
        global $price_list;
        $price_list = new PrintablePriceList();

        $this->attach(
            $this,
            [
                'NOTIFY_HTML_HEAD_END',
            ]
        );
    }

    protected function updateNotifyHtmlHeadEnd(&$class, $eventId)
    {
        global $template, $price_list;

        $ppl_base_css = $template->get_template_dir('profile-base.css', DIR_WS_TEMPLATE, FILENAME_PRICELIST, 'css') . 'profile-base.css';
        if (file_exists($ppl_base_css)) {
            echo '<link rel="stylesheet" href="' . $ppl_base_css . '">' . "\n";
        }

        $profile_css = 'profile-' . $price_list->getCurrentProfile() . '.css';
        $ppl_profile_css = $template->get_template_dir($profile_css, DIR_WS_TEMPLATE, FILENAME_PRICELIST, 'css') . $profile_css;
        if (file_exists($ppl_profile_css)) {
            echo '<link rel="stylesheet" href="' . $ppl_profile_css . '">' . "\n";
        }
    }
}
