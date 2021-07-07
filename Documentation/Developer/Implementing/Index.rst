.. include:: ../../Includes.txt

.. _implementing:

==============================
Implementing in Your Extension
==============================

The demander extension is intended to deliver additional :php:`QueryBuilder`
restrictions to any extension. You should therefore implement it in your
repository classes or other places where you build queries.

.. _implementing-restrictions:

Adding restrictions to a query
==============================

Adding demander restrictions to a :php:`QueryBuilder`

.. code-block:: php

   use Pixelant\Demander\Service\DemandService;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   $demandService = GeneralUtility(DemandService::class);

   $queryBuilder->andWhere(
       $demandService->getRestrictions(
           [
               'p' => 'tx_myextension_products',
               'c' => 'tx_myextension_categories',
           ],
           $queryBuilder->expr()
       )
   );

or in some other language:

.. code-block:: javascript
   :linenos:
   :emphasize-lines: 2-4

   $(document).ready(
      function () {
         doStuff();
      }
   );
