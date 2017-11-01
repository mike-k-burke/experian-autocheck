<?php

namespace MikeKBurke\ExperianAutocheck;

use Wa72\HtmlPageDom\HtmlPage;

/**
 * Class ExperianAutocheckConvert
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/experian-autocheck
 */
class ExperianAutocheckConvert
{
    /**
     * Convert html
     *
     * @param string $html Results page html
     *
     * @return ExperianAutocheckEntity|ExperianAutocheckError|null
     */
    public static function htmlToEntity($html)
    {
        $result = [];

        $page = new HtmlPage($html);

        $values = $page->filter('div#bbLiveData > div.bb > div.bb-header.bb-c33.bb-f13 > div.bb-header-right.bb-border-b > div.bb-header-right-c1 > div.bb-header-pad')->text();
        $values = explode('£', $values);
        $result['CapAverage'] = $values[2];

        $result['Vrm'] = trim($page->filter('p.reg')->text());

        $values = $page->filter('ul.list-info > li > b');
        $i = 0;
        foreach ($values as $value) {
            if ($i == 0) {
                $result['Vin'] = $value->textContent;
            } elseif ($i == 1) {
                $result['Make'] = $value->textContent;
            } elseif ($i == 2) {
                $result['Model'] = $value->textContent;
            } elseif ($i == 3) {
                $result['Colour'] = $value->textContent;
            } elseif ($i == 4) {
                $result['DateFirstRegistered'] = (new \DateTime())->createfromFormat('d/m/Y', $value->textContent)->format('Y-m-d');
            }
            $i++;
        }
        $result['provider'] = 'experian';
        $result['created'] = date('Y-m-d H:i:s');

        $entity = new ExperianAutocheckEntity($result);

        return $entity;
    }
}
