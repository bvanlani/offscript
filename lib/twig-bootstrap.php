<?php

use Twig\Extra\Markdown\LeagueMarkdown;

    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../lib/common.php';

    $isProduction = (getenv('APP_ENV') === 'production');

    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
    $twig = new \Twig\Environment($loader, [
        'cache' => $isProduction? __DIR__ .'/compilation_cache/' : false,
        'auto_reload' => !$isProduction,
    ]);

    $twig->addFilter(new \Twig\TwigFilter('convertSqlDate', 'convertSqlDate'));
    $twig->addFilter(new \Twig\TwigFilter('convertNewLinesToParagraphs', 'convertNewLinesToParagraphs'));

    $twig->addExtension(new Twig\Extra\Markdown\MarkdownExtension());

    $twig->addRuntimeLoader(new class implements \Twig\RuntimeLoader\RuntimeLoaderInterface {
        public function load($class){
            if(\Twig\Extra\Markdown\MarkdownRuntime::class === $class){
                return new \Twig\Extra\Markdown\MarkdownRuntime(new \Twig\Extra\Markdown\DefaultMarkdown());
            }
        }
    });

    $config = [
        'renderer' => [
            'block_separator' => "\n",
            'inner_separator' => "\n",
            'soft_break'      => "\n",
        ],
        'commonmark' => [
            'enable_em' => true,
            'enable_strong' => true,
            'use_asterisk' => true,
            'use_underscore' => true,
            'unordered_list_markers' => ['-', '*', '+'],
        ],
        'html_input' => 'escape',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 100,
        'max_delimiters_per_line' => PHP_INT_MAX,
        'xml' => [
            'max_indentation_level' => 16,
        ],
        'slug_normalizer' => [
            'max_length' => 255,
        ],
    ];

    $environment = new League\CommonMark\Environment\Environment($config);
    $environment->addExtension(new League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
    $commonMarkConverter = new League\CommonMark\MarkdownConverter($environment);

    $markdownConverter = new Twig\Extra\Markdown\LeagueMarkdown($commonMarkConverter);

    $twig->addRuntimeLoader(new class($markdownConverter) implements \Twig\RuntimeLoader\RuntimeLoaderInterface {
        private $converter;
        
        public function __construct($converter) {
            $this->converter = $converter;
        }
        
        public function load(string $class) {
            if (\Twig\Extra\Markdown\MarkdownRuntime::class === $class) {
                return new \Twig\Extra\Markdown\MarkdownRuntime($this->converter);
            }
            return null;
        }
    });
?>