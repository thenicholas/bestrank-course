<?php

namespace App\EventHandler\Crm;

use Bitrix24\SDK\Core\Contracts\Events\EventInterface;
use App\Application;
use Bitrix24\SDK\Core\Exceptions\BaseException;
use Bitrix24\SDK\Core\Exceptions\InvalidArgumentException;
use Bitrix24\SDK\Core\Exceptions\TransportException;
use Bitrix24\SDK\Core\Exceptions\UnknownScopeCodeException;
use Bitrix24\SDK\Core\Exceptions\WrongConfigurationException;
use Exception;
use Money\Money;

class DealEventHandler
{
    private const int ASSIGNED_BY_ID = 8;
    private const int DISCOUNT_THRESHOLD = 5000;
    private const int TOTAL_DISCOUNT = 200;
    private const int MIN_SUM_TO_CHANGE_ASSIGNEE = 1000;
    private const int MIN_PRODUCTS_TO_CHANGE_ASSIGNEE = 3;

    /**
     * @throws TransportException
     * @throws InvalidArgumentException
     * @throws BaseException
     * @throws WrongConfigurationException
     * @throws Exception
     */
    public static function onUpdate(EventInterface $b24Event): void
    {
        $dealId = (int)$b24Event->getEventPayload()['data']['FIELDS']['ID'];
        self::logDealUpdate($dealId);

        $b24ProductItems = Application::getB24Service()->getCRMScope()->dealProductRows()->get($dealId)->getProductRows();

        if (empty($b24ProductItems)) {
            return;
        }

        $totalInfo = self::calculateTotals($b24ProductItems);

        if ($totalInfo['totalSum'] < self::MIN_SUM_TO_CHANGE_ASSIGNEE && count($b24ProductItems) > self::MIN_PRODUCTS_TO_CHANGE_ASSIGNEE) {
            self::updateAssignedBy($dealId);
        } elseif ($totalInfo['totalSum'] > self::DISCOUNT_THRESHOLD) {
            self::applyDiscount($dealId, $b24ProductItems, $totalInfo);
        }
    }

    /**
     * @throws WrongConfigurationException
     * @throws InvalidArgumentException
     */
    private static function logDealUpdate(int $dealId): void
    {
        Application::getLog()->info(
            'processRemoteEvents.onCrmDealUpdate',
            ['dealId' => $dealId]
        );
    }

    private static function calculateTotals(array $b24ProductItems): array
    {
        $currency = $b24ProductItems[0]->PRICE->getCurrency()->getCode();
        $totalSum = Money::$currency(0);
        $totalDiscount = Money::$currency(0);

        foreach ($b24ProductItems as $productItem) {
            $totalSum = $totalSum->add($productItem->PRICE);
            $totalDiscount = $totalDiscount->add($productItem->DISCOUNT_SUM);
        }

        return [
            'totalSum' => $totalSum->getAmount() / 100,
            'totalDiscount' => $totalDiscount->getAmount() / 100
        ];
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnknownScopeCodeException
     * @throws BaseException
     * @throws TransportException
     * @throws WrongConfigurationException
     */
    private static function updateAssignedBy(int $dealId): void
    {
        $currentAssignedById = (int)Application::getB24Service()->getCRMScope()->deal()->get($dealId)->getCoreResponse()
            ->getResponseData()->getResult()['ASSIGNED_BY_ID'];
        if ($currentAssignedById !== self::ASSIGNED_BY_ID) {
            Application::getB24Service()->getCRMScope()->deal()->update($dealId, [
                'ASSIGNED_BY_ID' => self::ASSIGNED_BY_ID
            ]);
        } else {
            Application::getLog()->info(sprintf('Skipping update, ASSIGNED_BY_ID already equals \'%d\'', self::ASSIGNED_BY_ID));
        }
    }

    /**
     * @throws TransportException
     * @throws WrongConfigurationException
     * @throws InvalidArgumentException
     * @throws UnknownScopeCodeException
     * @throws BaseException
     */
    private static function applyDiscount(int $dealId, array $b24ProductItems, array $totalInfo): void
    {
        if (abs($totalInfo['totalDiscount'] - self::TOTAL_DISCOUNT) < 0.01) {
            Application::getLog()->info('Skipping update, discount already applied', [
                'dealId' => $dealId,
                'totalDiscount' => $totalInfo['totalDiscount']
            ]);
            return;
        }

        $productRows = self::calculateDiscountedProducts($b24ProductItems, $totalInfo['totalSum']);
        Application::getB24Service()->getCRMScope()->dealProductRows()->set($dealId, $productRows);
    }

    private static function calculateDiscountedProducts(array $b24ProductItems, float $totalSum): array
    {
        $discountRatio = self::TOTAL_DISCOUNT / $totalSum;
        $appliedDiscount = 0;
        $itemsCount = count($b24ProductItems);
        $productRows = [];

        foreach ($b24ProductItems as $index => $productItem) {
            $itemPrice = $productItem->PRICE->getAmount() / 100;
            $itemDiscount = floor($itemPrice * $discountRatio);

            if ($index === $itemsCount - 1) {
                $itemDiscount = self::TOTAL_DISCOUNT - $appliedDiscount;
            }

            $appliedDiscount += $itemDiscount;
            $newPrice = $itemPrice - $itemDiscount;

            $productRows[$index] = [
                'PRODUCT_ID' => $productItem->PRODUCT_ID,
                'PRICE' => $newPrice,
                'QUANTITY' => $productItem->QUANTITY,
                'DISCOUNT_TYPE_ID' => 1,
                'DISCOUNT_SUM' => $itemDiscount,
                'DISCOUNT_RATE' => round(($itemDiscount / $itemPrice) * 100, 2),
            ];
        }

        return $productRows;
    }
}