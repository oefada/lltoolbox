<?php
class ClientNotifierShell extends Shell
{
    /**
     * @var MerchDataEntries
     */
    private $merchDataModel;

    /**
     * @var ClientNotification
     */
    private $clientNotificationModel;

    private $clientEntries;

    private $notificationTypes = array(
        'Billboard',
        'Homepage Tabs',
        'Inspiration',
        'Featured Auction',
        'Listing & Destination Featured Auctions'
    );

    public function initialize()
    {
        APP::import('Model', 'MerchDataEntries');
        APP::import('Model', 'ClientNotification');
        $this->merchDataModel = new MerchDataEntries();
        $this->clientNotificationModel = new ClientNotification();
    }

    public function main()
    {
        $merchEntries = $this->merchDataModel->getEntriesForToday();
        $clientsToNotify = array();
        if ($merchEntries !== false) {
            $notificationEntries = $this->getNotificationEntries($merchEntries);
            if ($notificationEntries !== false) {
                $clientsToNotify = $this->getClientsToNotify($notificationEntries);
                if ($clientsToNotify !== false) {
                    var_dump($this->clientNotificationModel->saveAll($clientsToNotify));
                } else {

                }
            } else {

            }
        } else {

        }
    }

    /**
     * @param $notificationEntries
     * @return array|bool
     */
    private function getClientsToNotify($notificationEntries)
    {
        $clientsToNotify = array();

        if (isset($notificationEntries['Billboard'])) {
            $billboardEntries =
                $this->getClientEntries(
                    $notificationEntries['Billboard']['merchDataEntryId'],
                    $notificationEntries['Billboard']['merchDataTypeId'],
                    $notificationEntries['Billboard']['merchData']
                );

            if ($billboardEntries !== false) {
                $clientsToNotify = array_merge($clientsToNotify, $billboardEntries);
            }
        }

        if (isset($notificationEntries['Homepage Tabs'])) {
            $tabEntries =
                $this->getClientEntries(
                    $notificationEntries['Homepage Tabs']['merchDataEntryId'],
                    $notificationEntries['Homepage Tabs']['merchDataTypeId'],
                    $notificationEntries['Homepage Tabs']['merchData']
                );

            if ($tabEntries !== false) {
                $clientsToNotify = array_merge($clientsToNotify, $tabEntries);
            }
        }

        if (isset($notificationEntries['Inspiration'])) {
            $inspirationEntries =
                $this->getClientEntries(
                    $notificationEntries['Inspiration']['merchDataEntryId'],
                    $notificationEntries['Inspiration']['merchDataTypeId'],
                    $notificationEntries['Inspiration']['merchData']['clients']
                );

            if ($inspirationEntries !== false) {
                $clientsToNotify = array_merge($clientsToNotify, $inspirationEntries);
            }
        }

        if (isset($notificationEntries['Featured Auction'])) {
            $featuredAuctionEntries =
                $this->getClientEntries(
                    $notificationEntries['Featured Auction']['merchDataEntryId'],
                    $notificationEntries['Featured Auction']['merchDataTypeId'],
                    $notificationEntries['Featured Auction']['merchData']['clients']
                );

            if ($featuredAuctionEntries !== false) {
                $clientsToNotify = array_merge($clientsToNotify, $featuredAuctionEntries);
            }
        }

        if (isset($notificationEntries['Listing & Destination Featured Auctions'])) {
            $featuredAuctionListingAndDestinationEntries =
                $this->getClientEntries(
                    $notificationEntries['Listing & Destination Featured Auctions']['merchDataEntryId'],
                    $notificationEntries['Listing & Destination Featured Auctions']['merchDataTypeId'],
                    $notificationEntries['Listing & Destination Featured Auctions']['merchData']
                );

            if ($featuredAuctionListingAndDestinationEntries !== false) {
                $clientsToNotify = array_merge($clientsToNotify, $featuredAuctionListingAndDestinationEntries);
            }
        }

        return (empty($clientsToNotify)) ? false : $clientsToNotify;
    }

    /**
     * @param $entries
     * @param $type
     * @return array|bool
     */
    private function getClientEntries($entryId, $typeId, $entries)
    {
        $notificationEntries = array();
        foreach($entries as $entry) {
            if (isset($entry['clientId'])) {
                $notificationEntries[] = array(
                    'clientId' => $entry['clientId'],
                    'merchDataEntryId' => $entryId,
                    'merchDataGroupId' => $typeId
                );
            }
        }

        return (empty($notificationEntries)) ? false : $notificationEntries;
    }

    /**
     * @param $merchEntries
     * @return array|bool
     */
    private function getNotificationEntries($merchEntries)
    {
        $notificationEntries = array();

        foreach ($merchEntries as $entry) {
            if ($this->isNotification($entry['MerchDataType']['merchDataTypeName'])) {
                $notificationEntries[$entry['MerchDataType']['merchDataTypeName']] = array(
                    'merchDataEntryId' => $entry['MerchDataEntries']['id'],
                    'merchDataTypeId' => $entry['MerchDataEntries']['merchDataTypeId'],
                    'merchDataGroupId' => $entry['MerchDataEntries']['merchDataGroupId'],
                    'merchData' => $entry['MerchDataEntries']['merchDataArr']
                );
            }
        }

        return (empty($notificationEntries)) ? false : $notificationEntries;
    }

    /**
     * @param $merchType
     * @return bool
     */
    private function isNotification($merchType)
    {
        return in_array($merchType, $this->getNotificationTypes());
    }

    /**
     * @return array
     */
    private function getNotificationTypes()
    {
        return $this->notificationTypes;
    }

    /**
     * @param string $title
     * @param string $msg
     */
    public function error($title, $msg = '')
    {
        $this->out('Error: ' . $title);
        if ($msg !== '') {
            $this->out($msg);
        }

        exit(1);
    }

    /**
     * Overriding this to mute the welcome display
     */
    public function _welcome() {}
}
