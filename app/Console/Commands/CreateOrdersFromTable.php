<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\OrderCreated;
use App\ImportSaleOrders;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CreateOrdersFromTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:create-from-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Orders In Zoho Inventory From Table';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $importOrders = ImportSaleOrders::where('status', 0)->take(2);

        if ($importOrders->count() === 0) {
            return;
        }

        $ordersIds = $importOrders->pluck('sale_order_id')->toArray();
        $ordersDbIds = $importOrders->pluck('id')->toArray();
        $ordersApiTokens = $importOrders->pluck('inventory_api_key')->toArray();
        $ordersOrganizations = $importOrders->pluck('organization_id')->toArray();
        $ordersMap = array_combine($ordersIds, $ordersDbIds);
        $ordersTokenMap = array_combine($ordersIds, $ordersApiTokens);
        $ordersOrganizationMap = array_combine($ordersIds, $ordersOrganizations);

        $client = new Client();
        $saleOrdersResponse = $client->get('https://granalix.com/wp-json/wc/v3/orders?' . http_build_query(array('include' => $ordersIds)), [
            'auth' => [
                'ck_43d11f8e088a0b681a3f13d875253b5e340f7ca0',
                'cs_e4040e577c9c5ef326849a7859c644d3bdf3ee95'
            ]
        ]);

        $saleOrders = json_decode($saleOrdersResponse->getBody(), true);

        foreach ($saleOrders as $saleOrder) {
            ImportSaleOrders::where('id', $ordersMap[$saleOrder['number']])->update(['status' => 1]);
            $saleOrder['organization_id'] = $ordersOrganizationMap[$saleOrder['number']];

            dispatch(new OrderCreated($saleOrder, $ordersTokenMap[$saleOrder['number']]));
        }
    }
}
