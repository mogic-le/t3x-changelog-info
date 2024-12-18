TYPO3 changelog information
===========================

This TYPO3 extension provides a backend module to show the current project's
``CHANGELOG.md`` file.

Features:

- Show the ``CHANGELOG.md`` file in the TYPO3 backend, with markdown rendering
- Lets you configure the location of the ``CHANGELOG.md`` file
- Links JIRA issue numbers


Setup
-----
1. Install this extension::

     $ composer require mogic/t3x-changelog-info
2. Configure the Jira links and the location of the ``CHANGELOG.md`` file
   in the extension settings.


Requirements
------------
- TYPO3 v11 to v13
- ``markdown`` shell utility, see `vhs docs`__


__ https://docs.typo3.org/p/fluidtypo3/vhs/7.0/en-us/ViewHelpers/Format/Markdown.html
