<?php
class zcObserverPrintablePriceList extends base
{
    public function __construct()
    {
        global $current_page_base;
        if ($current_page_base !== FILENAME_PRICELIST) {
            $this->attach(
                $this,
                [
                    'NOTIFY_INFORMATION_SIDEBOX_ADDITIONS',
                ]
            );
        } else {
            // -----
            // Instantiate the price list for use by the template and observed notification.
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

    protected function updateNotifyInformationSideboxAdditions(&$class, $eventID, $not_used, &$information)
    {
        if (PL_SHOW_INFO_LINK === 'false' || !is_array($information)) {
            return;
        }

        // -----
        // The Bootstrap template (and possibly others) provides a specific set of
        // classes to apply to the information-sidebox links.
        //
        global $information_classes;
        $link_class = (isset($information_classes)) ? ' class="' . $information_classes . '"' : '';
        $link_target = (PL_INFO_LINK_NEW_PAGE === 'true') ? '_blank' : '_self';
        $pricelist_page_link =
            '<a ' . $link_class . ' href="' . zen_href_link(FILENAME_PRICELIST) . '" target="' . $link_target . '">' .
                BOX_HEADING_PRICELIST .
            '</a>';
        $link_position = ((int)PL_INFO_LINK_POSITION) - 1;
        if ($link_position < 0) {
            $link_position = 0;
        }
        array_splice($information, $link_position, 0, $pricelist_page_link);
    }
}
