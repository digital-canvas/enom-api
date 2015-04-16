<?php
namespace DigitalCanvas\Enom\Service;

use DigitalCanvas\Enom\Enom;

class WebsiteBuilder extends Enom
{

    /**
     * Cancellation reasons
     */
    const CANCEL_REASON_NO_LONGER_NEEDED = 1;
    const CANCEL_REASON_NOT_AS_EXPECTED = 2;
    const CANCEL_REASON_PRICE = 3;
    const CANCEL_REASON_TECHNICAL = 4;
    const CANCEL_REASON_SUPPORT = 5;
    const CANCEL_REASON_FUNCTIONALITY = 6;
    const CANCEL_REASON_OTHER = 7;

    /**
     * Billing Cycles
     */
    const BILLING_CYCLE_MONTHLY = 'Monthly';
    const BILLING_CYCLE_YEARLY = 'Yearly';

    /**
     * Plans
     */
    const PLAN_BASIC = 'websitebuilderbasic';
    const PLAN_PRO = 'websitebuilderpro';
    const PLAN_BUSINESS = 'websitebuilderbusiness';

    /**
     * Creates a new account
     *
     * @param string $language
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function createAccount($language = 'en_us')
    {
        $params = [
          'Command' => 'WSB_CreateAccount',
          'Service' => 'websitebuilderfree',
          'LanguageCode' => $language
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Cancels an account
     *
     * @param string $account_id The account to cancel
     * @param int $reason The reason for cancelling
     * @param string $comments Comment on why the account is being cancelled
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function cancelAccount($account_id, $reason = 1, $comments = null)
    {

        if (!in_array($reason, range(1, 7))) {
            throw new \InvalidArgumentException("Invalid reason code");
        }

        $params = [
          'Command' => 'WSB_CancelAccount',
          'VasItemID' => $account_id,
          'ReasonID' => (int)$reason
        ];

        if (!is_null($comments)) {
            $params['Comment'] = $comments;
        }

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Returns list of currencies
     *
     * @param int $brand
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getCurrencies($brand)
    {
        $params = [
          'Command' => 'WSB_GetCurrencies',
          'Brand' => $brand
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Returns details of an account
     *
     * @param string $account_id
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getDetails($account_id)
    {
        $params = [
          'Command' => 'WSB_GetDetails',
          'VasItemID' => $account_id
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Returns languages
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getLanguages()
    {
        $params = [
          'Command' => 'WSB_GetLanguages'
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Generates a login token
     *
     * @param string $user
     * @param string $site
     * @param int $brand
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getLoginToken($user, $site, $brand)
    {
        $params = [
          'Command' => 'WSB_GetLoginToken',
          'UserRef' => $user,
          'SiteRef' => $site,
          'Brand' => $brand,
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Account overview
     *
     * @param int $start
     * @param string $sort
     * @param int $filter
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function getOverview($start = 0, $sort = 'ASC', $filter = null)
    {

        $params = [
          'Command' => 'WSB_GetOverview'
        ];
        if ($start > 0) {
            $params['StartRecordNum'] = (int)$start;
        }
        if (in_array($sort, ['ASC', 'DESC'])) {
            $params['SortOrder'] = $sort;
        }
        if (in_array($filter, range(1, 9))) {
            $params['StatusFilter'] = $filter;
        }
        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Reactivates an account
     *
     * @param string $account_id
     * @param string $comments
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function reactivateAccount($account_id, $comments = null)
    {
        $params = [
          'Command' => 'WSB_ReactivateAccount',
          'VasItemID' => $account_id
        ];

        if (!is_null($comments)) {
            $params['Comment'] = $comments;
        }

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Updates account details
     *
     * @param string $account_id
     * @param string $domain
     * @param string $username
     * @param string $email
     * @param string $billing_cycle
     * @param bool $dns
     * @param string $language
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function updateAccount(
      $account_id,
      $domain,
      $username,
      $email,
      $billing_cycle = 'Monthly',
      $dns = false,
      $language = 'en_us'
    ) {
        if (!in_array($billing_cycle, ['Monthly', 'Yearly'])) {
            throw new \InvalidArgumentException("Invalid billing cycle");
        } else {
            $billing_cycle = ($billing_cycle == 'Monthly') ? 1 : 12;
        }

        $params = [
          'Command' => 'WSB_UpdateAccount',
          'VasItemID' => $account_id,
          'BillingCycle' => $billing_cycle,
          'DomainName' => $domain,
          'UserName' => $username,
          'EmailAddress' => $email,
          'LanguageCode' => $language,
          'SetDNS' => ($dns) ? 1 : 0,
        ];

        $response = $this->sendRequest($params);

        return $response;
    }

    /**
     * Upgrades an existing account
     *
     * @param string $account_id
     * @param string $plan
     * @param string $billing_cycle
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function upgradeAccount($account_id, $plan, $billing_cycle = 'Monthly')
    {

        if (!in_array($billing_cycle, ['Monthly', 'Yearly'])) {
            throw new \InvalidArgumentException("Invalid billing cycle");
        }
        if (!in_array($plan, ['websitebuilderbasic', 'websitebuilderpro', 'websitebuilderbusiness'])) {
            throw new \InvalidArgumentException("Invalid plan");
        }

        $params = [
          'Command' => 'PurchaseServices',
          'VasItemID' => $account_id,
          'Service' => $plan,
          'ActionType' => 'upgrade',
          'BillingPeriod' => $billing_cycle
        ];

        $response = $this->sendRequest($params);

        return $response;
    }
}