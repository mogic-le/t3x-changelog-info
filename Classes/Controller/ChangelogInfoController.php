<?php
namespace Mogic\ChangelogInfo\Controller;

use Michelf\Markdown;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ChangelogInfoController
{
    protected \TYPO3\CMS\Backend\Template\ModuleTemplate $moduleTemplate;

    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected IconFactory $iconFactory,
        protected UriBuilder $uriBuilder,
        protected readonly ExtensionConfiguration $extensionConfiguration,
        protected readonly Typo3Version $typo3Version,
    ) {}

    /**
     * Render backend module
     */
    public function main(RequestInterface $request): ResponseInterface
    {
        /**
         * @var array $linksConfig ABC=http://www.abc.com,DEF=http://www.def.com
         */
         $linksConfig = $this->extensionConfiguration
            ->get('changelog_info', 'linkReplacements');

        $linkUrls = [];
        if (!empty($linksConfig)) {
            $lines = explode(",", $linksConfig);
            foreach ($lines as $line) {
                $parts = explode("=", $line);
                $linkUrls[$parts[0]] = $parts[1];
            }
        }

        $changelogLocation = $this->extensionConfiguration
            ->get('changelog_info', 'changelogLocation');

        if (empty($changelogLocation)) {
            $changelogLocation = \TYPO3\CMS\Core\Core\Environment::getProjectPath().'/CHANGELOG.md';
        } else {
            $changelogLocation = \TYPO3\CMS\Core\Core\Environment::getProjectPath().'/'.$changelogLocation;
        }

        $this->moduleTemplate = $this->moduleTemplateFactory->create($request);
        if ($this->typo3Version->getMajorVersion() >= 12) {
            $this->loadHeader($request);
        }

        $changelogHtml = null;
        if (!file_exists($changelogLocation)) {
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                'The changelog file does not exist: ' . $changelogLocation,
                'Error loading Changelog',
                FlashMessage::ERROR,
                true
            );
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $messageQueue->addMessage($message);

        } else {
            $changelogMarkdown = $this->linkifyJiraTickets(
                file_get_contents($changelogLocation),
                $linkUrls
            );
            $changelogHtml = Markdown::defaultTransform($changelogMarkdown);
        }


        if ($this->typo3Version->getMajorVersion() >= 12) {
            $this->moduleTemplate->assign('changelogHtml', $changelogHtml);
            return $this->moduleTemplate->renderResponse('/ShowChangelog/Index');
        }

        $view = new \TYPO3\CMS\Fluid\View\StandaloneView();
        $view->setTemplateRootPaths(['EXT:changelog_info/Resources/Private/Templates/ShowChangelog']);
        $view->setPartialRootPaths(['EXT:changelog_info/Resources/Private/Partials/']);
        $view->setLayoutRootPaths(['EXT:changelog_info/Resources/Private/Layouts/']);
        $view->setTemplate('Index');
        $content = $view->renderSection('Content', ['changelogHtml' => $changelogHtml]);
        return new HtmlResponse($content);
    }

    protected function linkifyJiraTickets($text, $jiraUrls) {
        // Define the regex pattern to detect JIRA tickets
        $jiraPattern = '/\b([A-Z][A-Z0-9]*)-(\d+)\b/';

        // Function to use for processing each regex match
        $callback = function ($matches) use ($jiraUrls) {
            $projectKey = $matches[1];
            $ticketNumber = $matches[2];

            // Check if the project key exists in the jiraUrls array
            if (isset($jiraUrls[$projectKey])) {
                $jiraUrl = $jiraUrls[$projectKey];
                // Create the full JIRA ticket URL
                $ticketUrl = $jiraUrl . "/browse/" . $projectKey . "-" . $ticketNumber;
                // Return the HTML link
                return '[' . htmlentities($matches[0]). '](' . htmlentities($ticketUrl) . ')';
            } else {
                // If the project key is not found in the array, return the raw text
                return htmlentities($matches[0]);
            }
        };

        // Use preg_replace_callback to find and replace JIRA tickets with HTML links
        return preg_replace_callback($jiraPattern, $callback, $text);
    }

    protected function loadHeader(RequestInterface $request): void
    {
        $id = (int)($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);
        $pageinfo = BackendUtility::readPageAccess(
            $id, $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW)
        ) ?: [];
        $currentModule = $request->getAttribute('module');

        $this->moduleTemplate->makeDocHeaderModuleMenu(['id' => $id]);

        $lang = $this->getLanguageService();
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $this->moduleTemplate->setTitle($lang->sL($currentModule->getTitle()));

        // Shortcut
        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier($currentModule->getIdentifier())
            ->setDisplayName($lang->sL($currentModule->getTitle()));
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);

        // Reload
        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref(
                (string)$this->uriBuilder->buildUriFromRoute($currentModule->getIdentifier())
            )
            ->setTitle($lang->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
