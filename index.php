<?php

require_once __DIR__ . "/vendor/autoload.php";

$cli = new \League\CLImate\CLImate;

$cli->info('scrapping......');

$pokemonList = \GralhaDev\WebScraper\Scrapper::getPokemons();

$cli->table($pokemonList);

$pok = $cli->input('Escolha um pokemon da tabela, gigante: ')->prompt();

$pokemonStat = \GralhaDev\WebScraper\Scrapper::getStat($pok);

$cli->table($pokemonStat);
