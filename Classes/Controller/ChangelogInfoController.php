<?php
namespace Mogic\ChangelogInfo\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class ChangelogInfoController
{

    protected \TYPO3\CMS\Fluid\View\StandaloneView $view;

    public function __construct(
        protected ModuleTemplateFactory $moduleTemplateFactory,
        protected IconFactory $iconFactory,
        private UriBuilder $uriBuilder,
        private ResponseFactory $responseFactory,
        private readonly ExtensionConfiguration $extensionConfiguration,
        // ...
    ) {}

    /**
     * Show data
     *
     * @return string HTML
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

        $moduleTemplate = $this->moduleTemplateFactory->create($request);

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
            $moduleTemplate->assign(
                'changelogContent',
                $this->linkifyJiraTickets(
                    file_get_contents($changelogLocation),
                    $linkUrls
                )
            );
        }

        return $moduleTemplate->renderResponse('/ShowChangelog/Index');
    }

    protected function linkifyJiraTickets($text, $jiraUrls) {
        // Define the regex pattern to detect JIRA tickets
        $jiraPattern = '/\b([A-Z]+)-(\d+)\b/';

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
}
