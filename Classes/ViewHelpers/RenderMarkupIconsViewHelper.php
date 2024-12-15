<?php

namespace Mogic\ChangelogInfo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Format text with markdown-like icon notations in HTML
 * (e.g. @bus100@, @bus100@[50])
 */
class RenderMarkupIconsViewHelper extends AbstractViewHelper
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
     * Convert placeholder characters for spritemap icons into html
     * for usage in rte fields
     *
     * @param array                     $arguments
     * @param \Closure                  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        return RenderMarkupViewHelper::replaceIcons($arguments['text']);
    }
}
