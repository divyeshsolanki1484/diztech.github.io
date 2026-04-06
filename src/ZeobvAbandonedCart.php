<?php

declare(strict_types=1);

namespace Zeobv\AbandonedCart;

use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\Language\LanguageEntity;
use Symfony\Component\Finder\Finder;
use Zeobv\AbandonedCart\Checkout\AbandonedCart\AbandonedCartDefinition;

class ZeobvAbandonedCart extends Plugin
{
    public const MAIL_TEMPLATE_REMINDER_NAME = 'abandoned_cart.reminder';
    public const DEFAULT_PROMOTION_ID = 'fe5f4e10cd1a4f6e9710207638c0c9ec';

    public function install(InstallContext $installContext): void
    {
        $mailTemplateRepository = $this->container->get('mail_template.repository');
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', self::MAIL_TEMPLATE_REMINDER_NAME));
        $templateType = $mailTemplateTypeRepository->search($criteria, $installContext->getContext())->first();

        if ($templateType instanceof MailTemplateTypeEntity) {
            return;
        }

        $languageRepository = $this->container->get('language.repository');
        $criteria = new Criteria();
        $criteria->addAssociation('locale');
        $criteria->addFilter(new EqualsAnyFilter('locale.code', ['en-GB', 'de-DE', 'nl-NL']));

        $languages = $languageRepository->search($criteria, $installContext->getContext());

        $translations = [];

        /** @var LanguageEntity $language */
        foreach ($languages as $language) {
            $code = $language->getLocale()->getCode();
            $translations[$language->getId()] = [
                'languageId' => $language->getId(),
                'subject' => $this->getMailTemplate('subject', $code),
                'description' => 'Mail template send to customer who abandoned a shopping cart for longer than the configured time.',
                'senderName' => '{{ salesChannel.name }}',
                'contentPlain' => $this->getMailTemplate('plain', $code),
                'contentHtml' => $this->getMailTemplate('html', $code),
            ];
        }

        if (!key_exists(Defaults::LANGUAGE_SYSTEM, $translations)) {
            $translations[Defaults::LANGUAGE_SYSTEM] = current($translations);
        }

        $mailTemplateRepository->create([
            [
                'systemDefault' => false,
                'translations' => $translations,
                'mailTemplateType' => [
                    'technicalName' => self::MAIL_TEMPLATE_REMINDER_NAME,
                    'availableEntities' => [
                        'zeoAbandonedCart' => 'zeo_abandoned_cart',
                        'salesChannel' => 'sales_channel',
                        'customer' => 'customer',
                        'cart' => 'cart',
                    ],
                    'templateData' => $this->getTemplateData(),
                    'translations' => [
                        [
                            'languageId' => Defaults::LANGUAGE_SYSTEM,
                            'name' => 'Enter abandoned cart state: Reminded',
                        ],
                    ],
                ],
            ],
        ], $installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get('Doctrine\DBAL\Connection');
        $abandonedCartsTable = AbandonedCartDefinition::ENTITY_NAME;
        $connection->executeStatement("DROP TABLE IF EXISTS {$abandonedCartsTable}");

        $mailTemplateRepository = $this->container->get('mail_template.repository');
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        //Remove default promotion and promotion codes of abandoned cart
        $promotionUninstallRepository = $this->container->get('promotion.repository');
        $promotionCriteria = new Criteria();
        $promotionCriteria->addFilter(new EqualsFilter('id', self::DEFAULT_PROMOTION_ID));
        $promotions = $promotionUninstallRepository->search($promotionCriteria, $uninstallContext->getContext());

        if($promotions->getTotal() === 1){
            foreach ($promotions->getElements() as $promotion) {
                /** @var PromotionEntity $promotion */
                $promotionUninstallRepository->delete([['id' => $promotion->getId()]], $uninstallContext->getContext());
            }
        }

        $criteria = new Criteria();
        $criteria->addAssociation('mailTemplateType');
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', self::MAIL_TEMPLATE_REMINDER_NAME));
        $templates = $mailTemplateRepository->search($criteria, $uninstallContext->getContext());

        if ($templates->count() <= 0) {
            return;
        }

        $mailTemplateIds = [];
        $mailTemplateTypeIds = [];

        /** @var MailTemplateEntity $mailTemplate */
        foreach ($templates->getElements() as $mailTemplate) {
            $mailTemplateIds[] = ['id' => $mailTemplate->getId()];

            if (!in_array($mailTemplate->getMailTemplateTypeId(), $mailTemplateTypeIds)) {
                $mailTemplateTypeIds[] = ['id' => $mailTemplate->getMailTemplateTypeId()];
            }
        }

        if (!empty($mailTemplateIds)) {
            $mailTemplateRepository->delete($mailTemplateIds, $uninstallContext->getContext());
        }

        if (!empty($mailTemplateTypeIds)) {
            $mailTemplateTypeRepository->delete($mailTemplateTypeIds, $uninstallContext->getContext());
        }

        $customFieldName = 'ZeobvAbandonedCartMail';
        $query = "UPDATE `order` SET `custom_fields` = JSON_REMOVE(`custom_fields`, '$.{$customFieldName}');";
        $connection->executeStatement($query);

        parent::uninstall($uninstallContext);
    }

    private function getMailTemplate(string $filename, ?string $code = null): ?string
    {
        $finder = new Finder();

        $code = $code ?? 'en-GB';

        // find all files in the current directory
        $finder->files()->in(__DIR__ . "/Resources/views/email/{$code}/abandoned_cart.reminder");

        foreach ($finder as $file) {
            if ($filename === $file->getFilenameWithoutExtension()) {
                return $file->getContents();
            }
        }

        return null;
    }

    private function getTemplateData(): array
    {
        return [
            'zeoAbandonedCart' => [
                'id' => '0x4805CAC305EE4E9D994F7E986AE361B3',
                'cartToken' => 'yc3vn9MHseLx7dFxDo8iHMJ7duoBsG51',
                'email' => 'example@gmail.nl',
                'lastMailSendAt' => null,
                'scheduleIndex' => 0,
                'lineItems' => [
                    '0f134719b07c4bb8873c7ccbc1500ee8' => [
                        'id' => '0f134719b07c4bb8873c7ccbc1500ee8',
                        'good' => true,
                        'type' => 'product',
                        'cover' => [
                            'url' => 'https://media.istockphoto.com/id/1249496770/photo/running-shoes.jpg?s=612x612&w=0&k=20&c=b4MahNlk4LH6H1ksJApfnlQ5ZPM3KGhI5i_yqhGD9c4=',
                        ],
                        'label' => 'Example product',
                        'price' => [
                            'quantity' => 1,
                            'taxRules' => [
                                ['taxRate' => 21, 'extensions' => [], 'percentage' => 100],
                            ],
                            'listPrice' => null,
                            'unitPrice' => 223.99,
                            'extensions' => [],
                            'totalPrice' => 223.99,
                            'referencePrice' => null,
                            'calculatedTaxes' => [
                                [
                                    'tax' => 38.87,
                                    'price' => 223.99,
                                    'taxRate' => 21,
                                    'extensions' => [],
                                ],
                            ],
                        ],
                        'payload' => [
                            'isNew' => false,
                            'taxId' => 'f5bc0df0172c4c88b98b20e54733f3e2',
                            'tagIds' => null,
                            'options' => [],
                            'features' => [],
                            'createdAt' => '2021-09-02 07:07:23.515',
                            'optionIds' => null,
                            'isCloseout' => false,
                            'categoryIds' => [
                                '17aa7e210b2f44dd917cb69147536e89',
                            ],
                            'propertyIds' => null,
                            'releaseDate' => null,
                            'customFields' => [],
                            'productNumber' => 'SW10000',
                            'manufacturerId' => 'd857fa1a0e9549ce8de86b2536f278fc',
                            'purchasePrices' => [
                                'currencyId' => 'b7d2554b0ce847cd82f3ac9bd1c0dfca',
                                'net' => 0,
                                'gross' => 0,
                                'linked' => true,
                                'listPrice' => null,
                                'extensions' => [],
                            ],
                            'markAsTopseller' => null,
                        ],
                        'children' => [],
                        'modified' => false,
                        'quantity' => 1,
                        'removable' => true,
                        'stackable' => true,
                        'extensions' => [],
                        'description' => null,
                        'requirement' => null,
                        'referencedId' => '0f134719b07c4bb8873c7ccbc1500ee8',
                        'dataTimestamp' => '2021-09-12T07:13:04.631+00:00',
                        'dataContextHash' => '7116e36cd0356dd78186992022f6366e',
                        'priceDefinition' => [
                            'type' => 'quantity',
                            'price' => 223.99,
                            'quantity' => 1,
                            'taxRules' => [
                                [
                                    'taxRate' => 21,
                                    'extensions' => [],
                                    'percentage' => 100,
                                ],
                            ],
                            'listPrice' => null,
                            'extensions' => [],
                            'isCalculated' => true,
                            'referencePriceDefinition' => null,
                        ],
                        'deliveryInformation' => [
                            'stock' => 111,
                            'width' => null,
                            'height' => null,
                            'length' => null,
                            'weight' => 0,
                            'extensions' => [],
                            'restockTime' => null,
                            'deliveryTime' => null,
                            'freeDelivery' => false,
                        ],
                        'quantityInformation' => [
                            'extensions' => [],
                            'maxPurchase' => 100,
                            'minPurchase' => 1,
                            'purchaseSteps' => 1,
                        ],
                    ],
                ],
                'currencyId' => '0xB7D2554B0CE847CD82F3AC9BD1C0DFCA',
                'shippingMethodId' => '0x758ABD1C272A4979A4D4FAFA20A57D0C',
                'paymentMethodId' => '0x589863A7ED6C449ABD24D793E31D5EF6',
                'countryId' => '0x555534E7E0C646858866D5DC3D93813B',
                'customerId' => '0xA7E16507214040A48963F6E91463D758',
                'salesChannelId' => '0x197D1D8975F74A6EAC7270852DFAB90C',
                'salesChannelDomainId' => '0x36495AB4470241E59D075D663F1AB6E3',
                'createdAt' => '2021-09-12 07:03:36.377',
                'updatedAt' => null,
            ],
            'salesChannel' => [
                'typeId' => '8a243080f92e4c719546314b577cf82b',
                'languageId' => '2fbb5fe2e29a4d70aa5854ce7ce3e20b',
                'currencyId' => 'b7d2554b0ce847cd82f3ac9bd1c0dfca',
                'paymentMethodId' => '11491d8f829143c9a1f15c9c55e3df0c',
                'shippingMethodId' => '03168af91f804087b7bc24eea072c2d6',
                'countryId' => '36159d966ed844e6add50a0a370c99b2',
                'navigationCategoryId' => 'aee8846cc9214295bd832fc436ff0891',
                'navigationCategoryDepth' => 2,
                'homeSlotConfig' => null,
                'homeCmsPageId' => null,
                'homeCmsPage' => null,
                'homeEnabled' => null,
                'homeName' => null,
                'homeMetaTitle' => null,
                'homeMetaDescription' => null,
                'homeKeywords' => null,
                'footerCategoryId' => null,
                'serviceCategoryId' => null,
                'name' => 'Storefront',
                'shortName' => null,
                'accessKey' => 'SWSCATLQZHBWBVZ1SGHNQNBLAQ',
                'currencies' => null,
                'languages' => null,
                'configuration' => null,
                'active' => true,
                'maintenance' => false,
                'maintenanceIpWhitelist' => null,
                'taxCalculationType' => 'horizontal',
                'type' => null,
                'currency' => null,
                'language' => null,
                'paymentMethod' => null,
                'shippingMethod' => null,
                'country' => null,
                'orders' => null,
                'customers' => null,
                'countries' => null,
                'paymentMethods' => null,
                'shippingMethods' => null,
                'translations' => null,
                'domains' => [
                    [
                        'url' => "http => \/\/localhost\/development\/public",
                        'currencyId' => 'b7d2554b0ce847cd82f3ac9bd1c0dfca',
                        'currency' => null,
                        'snippetSetId' => '765a7f1059ee4f75a4592194291d1e1e',
                        'snippetSet' => null,
                        'salesChannelId' => '6d5b12a8049e411cb463296092b0e887',
                        'salesChannel' => null,
                        'languageId' => '2fbb5fe2e29a4d70aa5854ce7ce3e20b',
                        'language' => null,
                        'customFields' => null,
                        'productExports' => null,
                        'salesChannelDefaultHreflang' => null,
                        'hreflangUseOnlyLocale' => false,
                        '_uniqueIdentifier' => '7ed2b68f70284aea83d1c2747b622848',
                        'versionId' => null,
                        'translated' => [

                        ],
                        'createdAt' => '2021-04-08T07 => 30 => 38.224+00 => 00',
                        'updatedAt' => null,
                        'extensions' => [
                            'foreignKeys' => [
                                'apiAlias' => null,
                                'extensions' => [

                                ],
                            ],
                        ],
                        'id' => '7ed2b68f70284aea83d1c2747b622848',
                    ],
                ],
                'systemConfigs' => null,
                'customFields' => null,
                'navigationCategory' => null,
                'footerCategory' => null,
                'serviceCategory' => null,
                'productVisibilities' => null,
                'mailHeaderFooterId' => null,
                'numberRangeSalesChannels' => null,
                'mailHeaderFooter' => null,
                'customerGroupId' => 'cfbd5018d38d41d8adca10d94fc8bdd6',
                'customerGroup' => null,
                'newsletterRecipients' => null,
                'promotionSalesChannels' => null,
                'documentBaseConfigSalesChannels' => null,
                'productReviews' => null,
                'seoUrls' => null,
                'seoUrlTemplates' => null,
                'mainCategories' => null,
                'paymentMethodIds' => [
                    '02f698f2ca24408bbbd40370ac95275d',
                    '11491d8f829143c9a1f15c9c55e3df0c',
                    '68cd624bbe2946f4afbdc1ae2f389e0e',
                    'e226a70069af441f965e79459acccb41',
                ],
                'productExports' => null,
                'hreflangActive' => false,
                'hreflangDefaultDomainId' => null,
                'hreflangDefaultDomain' => null,
                'analyticsId' => null,
                'analytics' => null,
                'customerGroupsRegistrations' => null,
                'eventActions' => null,
                'boundCustomers' => null,
                'wishlists' => null,
                'landingPages' => null,
                '_uniqueIdentifier' => '6d5b12a8049e411cb463296092b0e887',
                'versionId' => null,
                'translated' => [
                    'name' => 'Storefront',
                    'customFields' => [],
                ],
                'createdAt' => '2021-04-08T07:30:38.224+00:00',
                'updatedAt' => null,
                'extensions' => [
                    'foreignKeys' => [
                        'apiAlias' => null,
                        'extensions' => [

                        ],
                    ],
                ],
                'id' => '6d5b12a8049e411cb463296092b0e887',
                'navigationCategoryVersionId' => '0fa91ce3e96a4bc2be4bd9ce752c3425',
                'footerCategoryVersionId' => null,
                'serviceCategoryVersionId' => null,
            ],
            'customer' => [
                'id' => 'ca87efbfbd2f23efbfbd4742efbfbd11',
                'autoIncrement' => 3,
                'customerGroupId' => '1323efbfbd32774576efbfbd1636efbf',
                'requestedCustomerGroupId' => null,
                'defaultPaymentMethodId' => null,
                'salesChannelId' => '6d5b12a8049e411cb463296092b0e887',
                'languageId' => '2fbb5fe2e29a4d70aa5854ce7ce3e20b',
                'lastPaymentMethodId' => null,
                'defaultBillingAddressId' => '68efbfbd58c2be034cefbfbdefbfbd56',
                'defaultShippingAddressId' => '68efbfbd58c2be034cefbfbdefbfbd56',
                'customerNumber' => '10000',
                'salutationId' => 'efbfbd76efbfbdd1a73b4b1cefbfbdef',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'company' => 'Shopware AG.',
                'password' => '$2y$10$Of2prbDQtxqFFjdb.8pXde6K9BqYyUGblqlJrwM67/E9WVjFPmZH2',
                'email' => 'example@mail.com',
                'title' => null,
                'vatIds' => ['NL123456789B01'],
                'active' => 1,
                'guest' => 0,
                'firstLogin' => '2021-10-19 12:39:23.593',
                'lastLogin' => null,
                'newsletterSalesChannelIds' => null,
                'newsletter' => 0,
                'birthday' => null,
                'lastOrderDate' => null,
                'orderCount' => 1,
                'orderTotalAmount' => 1234.95,
                'customFields' => null,
                'affiliateCode' => 'affiliate-code-example',
                'campaignCode' => 'campaign-code-example',
                'createdAt' => '2021-10-19 12:39:23.641',
                'updatedAt' => '2021-12-15 11:07:53.759',
                'remoteAddress' => '0.0.0.0',
                'tagIds' => null,
            ],
        ];
    }
}
