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

Adding demander restrictions to the :php:`QueryBuilder` instance
:php:`$queryBuilder` could look something like this:

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

.. _implementing-restrictions-aliases:

Aliases and table names
-----------------------

The first argument of :php:`$demandService->getRestrictions()` is an associative
array where the key is the table alias and the value the real table name. This
makes it possible to add restrictions to only a subset of the tables in a
complex join statement.

The above example might have defined the table aliases like this:

.. code-block:: php

   $queryBuilder
       ->select('p.*')
       ->from('tx_myextension_products', 'p')
       ->join('p', 'tx_myextension_categories', 'c', /** join condition **/);

If you're not using table aliases, the key should be the same as the value, e.g.
:php:`'tx_myextension_products' => 'tx_myextension_products'`.

.. _implementing-ordering:

Adding ordering to a query
==========================

Ordering of records is closely related to filtering. Imagine visiting a web shop
where you could limit the items displayed to those costing $100–199, but where
you couldn't order the items from lowest to highest price.

This code example adds :sql:`ORDER BY` clauses to the :php:`$queryBuilder`
object. This is done by internally running :php:`$queryBuilder->addOrderBy(...)`
for each of the supplied tables.

.. code-block:: php

   use Pixelant\Demander\Service\DemandService;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   $demandService = GeneralUtility(DemandService::class);

   $demandService->addSortBy(
       [
           'p' => 'tx_myextension_products',
           'c' => 'tx_myextension_categories',
       ],
       $queryBuilder
   );

Relationship to property configuration
--------------------------------------

The Demander extension uses the
:ref:`property configuration <configuration-typoscript-properties>` to determine
which field to sort by in a given table.

A :sql:`ORDER BY` clause will only be added if there is a demand to order by a
particular property. These demands are located in a special `orderBy` demand,
using the format `d[orderBy]=<propertyName>,<orderDirection>`
(`,<orderDirection>` is optional).

.. note::
   The Demander extension supports only a single `orderBy` demand. This means
   you cannot combine orderings through a demand (e.g. :sql:`ORDER BY a, b` is
   not possible). You can add your own default additional orderings in your PHP
   code.
