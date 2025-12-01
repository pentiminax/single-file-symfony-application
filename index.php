<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Console\Application as ConsoleApplication;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Runtime\SymfonyRuntime;

class Application extends Kernel
{
    use MicroKernelTrait;

    private array $quotes = [
        "motivation" => [
            ["text" => "Le succès, c'est tomber sept fois, se relever huit.", "author" => "Proverbe japonais"],
            ["text" => "La seule façon de faire du bon travail est d’aimer ce que vous faites.", "author" => "Steve Jobs"],
            ["text" => "L’échec est le fondement de la réussite.", "author" => "Lao Tseu"],
            ["text" => "Ce n’est pas parce que les choses sont difficiles que nous n’osons pas, c’est parce que nous n’osons pas qu’elles sont difficiles.", "author" => "Sénèque"],
        ],
        "humour" => [
            ["text" => "Je ne procrastine pas, je fais juste des pauses très longues entre mes moments productifs.", "author" => "Anonyme"],
            ["text" => "Le café est la meilleure chose que l’on puisse faire avec des grains après Java.", "author" => "Développeur anonyme"],
            ["text" => "Il existe deux types de personnes : celles qui peuvent extrapoler à partir de données incomplètes.", "author" => "Inconnu"],
            ["text" => "Le code le plus efficace est celui qu’on n’écrit pas.", "author" => "Sagesse développeur"],
        ],
        "sagesse" => [
            ["text" => "La vie est un mystère qu’il faut vivre, et non un problème à résoudre.", "author" => "Gandhi"],
            ["text" => "Connais-toi toi-même.", "author" => "Socrate"],
            ["text" => "Le bonheur n’est pas une destination, c’est une façon de voyager.", "author" => "Margaret Lee Runbeck"],
            ["text" => "La patience est l’art d’espérer.", "author" => "Vauvenargues"],
        ],
        "dev" => [
            ["text" => "N’importe quel imbécile peut écrire du code qu’un ordinateur peut comprendre. Les bons programmeurs écrivent du code que les humains peuvent comprendre.", "author" => "Martin Fowler"],
            ["text" => "D’abord, résous le problème. Ensuite, écris le code.", "author" => "John Johnson"],
            ["text" => "L’expérience est le nom que chacun donne à ses erreurs.", "author" => "Oscar Wilde"],
            ["text" => "Le code, c’est comme l’humour : si tu dois l’expliquer, c’est qu’il n’est pas bon.", "author" => "Cory House"],
        ],
    ];

    #[Route('/{category}', methods: 'GET')]
    public function index(?string $category = null): Response
    {
        return new JsonResponse($this->getRandomQuote($category));
    }

    private function getRandomQuote(?string $category = null): array
    {
        if ($category && !isset($this->quotes[$category])) {
            throw new \InvalidArgumentException("Category '$category' not found");
        }

        $availableQuotes = $category
            ? $this->quotes[$category]
            : array_merge(...array_values($this->quotes));

        $quote = $availableQuotes[array_rand($availableQuotes)];

        return [
            'quote' => $quote['text'],
            'author' => $quote['author'],
            'category' => $category ?? 'all',
        ];
    }
}

$app = new Application('dev', true);

(new SymfonyRuntime())
    ->getRunner(\PHP_SAPI === 'cli' ? new ConsoleApplication($app) : $app)
    ->run();
