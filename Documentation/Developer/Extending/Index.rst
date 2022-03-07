.. include:: ../../Includes.txt

.. _extending:

=========
Extending
=========

.. _extending-demandprovider:

Creating a custom :php:`DemandProvider`
=======================================

:php:`DemandProvider` classes add new sources for demands. I.e. places where
filterrestrictions are defined. The extension already includes providers that
provide demands from the HTTP request (:php:`RequestDemandProvider`) and
TypoScript (:php:`TypoScriptDemandProvider`).

All :php:`DemandProvider` classes must implement
:php:`Pixelant\Demander\DemandProvider\DemandProviderInterface`.

This example fetches demand from an environment variable:

.. code-block:: php

   use Pixelant\Demander\DemandProvider\DemandProviderInterface;

   class EnvironmentVariableDemandProvider implements DemandProviderInterface
   {
       public function getDemand(): array
       {
           return json_decode(getenv('APP_DEMANDER'), true);
       }
   }

The :php:`getDemand()` method always returns an associative array of demands.
The format is explained in the
:ref:`Configuration chapter's demand section <configuration-typoscript-demands>`
.
