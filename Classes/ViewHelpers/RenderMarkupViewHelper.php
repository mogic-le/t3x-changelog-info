<?php

namespace Mogic\ChangelogInfo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Format text with markdown-like notation in HTML
 */
class RenderMarkupViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'text', 'string', 'input string', true
        );
    }

    /**
     * Convert special characters to HTML entities
     *
     * @param array                     $arguments
     * @param \Closure                  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $text = htmlspecialchars($arguments['text']);

        // Convert [Text](http://example.org/) to a tag
        $text = preg_replace(
            '/\[(.+?)\]\((.+?)\)/',
            '<a class="font-medium text-blue-800 hover:underline" href="\2">\1</a>',
            $text
        );

        // convert ## into bullet point for unordered list (no ul markup)
        $text = preg_replace(
            '/##/',
            '<span class="mr-4">&#8226;</span>',
            $text
        );

        // wrap interpolated content ! bullet point one ! into a list element
        $text = preg_replace(
            '/\!(.+?)\!/',
            '<li>\1</li>',
            $text
        );

        // wrap interpolated content % list elements % into an unordered list element
        $text = preg_replace(
            '/\%(.+?)\%/',
            '<ul class="pl-10 list-disc list-outside">\1</ul>',
            $text
        );

        // convert ^2^ into superscripted 2
        $text = preg_replace(
            '/(\^)(.+?)(\^)/',
            '<sup>\2</sup>',
            $text
        );

        // convert _2_ into subscripted 2
        $text = preg_replace(
            '/(\_)(.+?)(\_)/',
            '<sub>\2</sub>',
            $text
        );

        $text = self::replaceIcons($text);

        // Convert *boldtext* into <strong>boldtext</strong> and return new string
        return preg_replace('/(\*)(.+?)(\*)/', '<strong class="font-medium">\2</strong>', $text);

    }

    /**
     * Replace placeholders with svg icons from spritemap
     *
     * @param string $text
     *
     * @return string
     */
    public static function replaceIcons(string $text): string
    {
        // KEEP THE ICON HANDLING ORDER LIKE THAT (SPECIFIED WIDTH FIRST)
        // Convert @iconName@[height] into svg icon with specified height

        // check with [set]@name@[width]
        $text = preg_replace(
            '/\[([\w_-]+?)]\@([\w_-]+?)\@\[([\w_-]+?)\]/',
            '<svg class="inline-block" height="\3"><use href="/typo3conf/ext/lde/Resources/Public/Assets/spritemap_\1.svg#sprite-\2"></use></svg>',
            $text
        );

        // check with [set]@name@
        $text = preg_replace(
            '/\[([\w_-]+?)]\@([\w_-]+?)\@/',
            '<svg class="inline-block" height="1em"><use href="/typo3conf/ext/lde/Resources/Public/Assets/spritemap_\1.svg#sprite-\2"></use></svg>',
            $text
        );

        $text = preg_replace(
            '/\@([\w_-]+?)\@\[([\w_-]+?)\]/',
            '<svg class="inline-block" height="\2"><use href="/typo3conf/ext/lde/Resources/Public/Assets/spritemap.svg#sprite-\1"></use></svg>',
            $text
        );
        // Convert @iconName@ into svg icon with 24px height
        $text = preg_replace(
            '/\@([\w_-]+?)\@/',
            '<svg class="inline-block align-middle" height="1em"><use href="/typo3conf/ext/lde/Resources/Public/Assets/spritemap.svg#sprite-\1"></use></svg>',
            $text
        );

        return $text;
    }
}
