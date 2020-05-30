<?php

namespace GralhaDev\WebScraper;

use GuzzleHttp\Client;

class Scrapper
{

    /**
     * @var array
     */
    private static $pokedex;

    public static function getPokemons(): array
    {
        $httpClient   = new Client();
        $httpResponse = $httpClient->get('https://serebii.net/pokedex-swsh/');

        $domResponse  = new \DOMDocument();
        $domResponse->loadHTML(
            $httpResponse->getBody()->getContents(),
            LIBXML_NOWARNING | LIBXML_NOERROR,

        );
        $htmlResponse = simplexml_import_dom($domResponse);

        $pokedex = [];
        foreach ($htmlResponse->xpath("//form[@name='galar']/select/option") as $item) {
            [$number, $name] = explode(' ', $item);
            $pokedex[$number] = [$number, $name, (string)$item['value']];
        }
        unset($pokedex['Galar']);

        self::$pokedex = $pokedex;

        return $pokedex;
    }

    public static function getStat($pokemonNumber)
    {
        $httpClient   = new Client();
        $httpResponse = $httpClient->get('https://serebii.net' . self::$pokedex[$pokemonNumber][2]);

        $domResponse  = new \DOMDocument();
        $domResponse->loadHTML(
            $httpResponse->getBody()->getContents(),
            LIBXML_NOWARNING | LIBXML_NOERROR,

        );
        $htmlResponse = simplexml_import_dom($domResponse);

        $stats = [];
        $elements = $htmlResponse->xpath("((//h2[contains(text(), 'Stats')])[last()])/../../../tr");
        array_shift($elements);
        foreach ($elements as $item) {
            if ((string)$item->td[0] === 'Max Stats') {
                $stats[] = [
                    $item->td[0] . ' ' . $item->td[1],
                    (string)$item->td[2],
                    (string)$item->td[3],
                    (string)$item->td[4],
                    (string)$item->td[5],
                    (string)$item->td[6],
                    (string)$item->td[7],
                ];
                continue;
            }

            $stats[] = [
                (string)$item->td[0] === 'Lv. 100' ? str_repeat(' ', 10) . $item->td[0] : (string)$item->td[0] ,
                (string)$item->td[1],
                (string)$item->td[2],
                (string)$item->td[3],
                (string)$item->td[4],
                (string)$item->td[5],
                (string)$item->td[6],
            ];
        }

        return $stats;
    }
}
