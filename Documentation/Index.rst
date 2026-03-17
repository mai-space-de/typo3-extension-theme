.. include:: /Documentation/Includes.rst.txt

.. _start:

==========================
maispace/theme — TYPO3 Theme Loader
==========================

:Extension key: theme
:Package name: maispace/theme
:Version: |release|
:Language: en
:Author: Joel Maximilian Mai
:License: This document is published under the `Open Publication License
          <https://www.opencontent.org/openpub/>`__.

**maispace/theme** is a TYPO3 extension that loads assets, backend theme
configurations, and record module registrations from all active packages in a
TYPO3 instance. It ships a complete ITCSS stylesheet bundle (compiled
server-side via :t3ext:`maispace/assets`), a set of base Fluid page templates
ready to extend, six pre-built ``sitegeist/fluid-components`` components, and
dedicated backend modules for TCA record types.

.. toctree::
   :maxdepth: 2
   :titlesonly:

   Introduction
   Installation
   Configuration
   StyleSheets
   Components
   Templates
   Changelog
