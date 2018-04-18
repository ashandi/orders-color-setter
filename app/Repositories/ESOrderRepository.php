<?php

namespace  App\Repositories;

use App\Models\Order;
use App\Models\OrderDbContext;

class ESOrderRepository implements OrderRepository
{

    /**
     * @var string
     */
    private $index = "orders";

    /**
     * @var string
     */
    private $indexType = "orders";

    /**
     * @var string
     */
    private $defaultPhone = '+79000000000';

    /**
     * @var OrderDbContext
     */
    private $orderDbContext;

    /**
     * ESOrderRepository constructor.
     * @param OrderDbContext $orderDbContext
     */
    public function __construct(OrderDbContext $orderDbContext)
    {
        $this->orderDbContext = $orderDbContext;
    }

    /**
     * Returns orders from the same user who made given order
     *
     * @param Order $order
     * @return mixed
     */
    public function getSimilarOrders(Order $order)
    {
        $orderIds = $this->getSimilarOrderIds($order);

        return $this->orderDbContext
            ->whereIn('id', $orderIds)
            ->get();
    }

    /**
     * Returns array with ids of orders from the same user
     * who made given $order
     *
     * @param Order $order
     * @return array
     */
    private function getSimilarOrderIds(Order $order)
    {
        $query = $this->createGetSimilarQuery($order);

        $response = $this->match($query, 1000);

        return $this->getIds($response);
    }

    /**
     * Returns query for searching similar orders
     * with given order in elasticSearch
     *
     * @param Order $order
     * @return array
     */
    private function createGetSimilarQuery(Order $order)
    {
        $query = [
            "query" => [
                "bool" => [
                    "must_not" => [
                        "term" => [
                            "order_number" => $order->number //Исключили из выборки сам переданный заказ
                        ]
                    ],
                    //Помимо ФИО должен совпасть хотя бы email, телефон или адрес
                    "should" => [
                        [ "term" => [
                            "email" => $order->email //Поиск по email
                        ]]
                    ],
                    "minimum_should_match" => 1
                ]
            ]
        ];

        if ($order->firstname && $order->lastname && $order->patronymic) {
            //Совпадение ФИО, если оно указано - обязательно
            $query['query']['bool']['must'] = [
                [ "match" => [
                    "lastname" => [
                        "query" => $order->lastname,
                        "fuzziness" => 2
                    ]
                ]],
                [ "match" => [
                    "firstname" => [
                        "query" => $order->firstname,
                        "fuzziness" => 1
                    ]
                ]],
                [ "match" => [
                    "patronymic" => [
                        "query" => $order->patronymic,
                        "fuzziness" => 1
                    ]
                ]]
            ];
        }

        //Телефон проверяется только если он отличается от дефолтного
        if ($order->phone != $this->defaultPhone) {
            $query['query']['bool']['should'][] = [ "term" => [
                "phone" => $order->phone //Поиск по телефону
            ]];
        }

        //Поиск по адресу
        $subQuery = [
            "bool" => [
                "must" => [
                    [ "term" => [
                        "postcode" => $order->postcode
                    ]],
                    [ "term" => [
                        "region_id" => $order->regionId
                    ]],
                    [ "match" => [
                        "city" => [
                            "query" => $order->city,
                            "fuzziness" => 2
                        ]
                    ]]
                ]
            ]
        ];

        //Если заказ не "до востребования", дополнительно проверяем улицу, дом
        if (!$order->isPosteRestante()) {
            $subQuery["bool"]["must"][] = [ "match" => [
                "street" => [
                    "query" => $order->street,
                    "fuzziness" => 2
                ]
            ]];
            $subQuery["bool"]["must"][] = [ "term" => [
                "house" => $order->house
            ]];
            //Должен совпасть либо номер квартиры, либо отметка, что это частный дом
            $subQuery["bool"]["should"] = [
                [ "term" => [
                    "apartment" => $order->apartment ?? ''
                ]],
                [ "term" => [
                    "is_private_house" => intval($order->isPrivateHouse)
                ]]
            ];
            $subQuery["bool"]["minimum_should_match"] = 1;
        }

        $query['query']['bool']['should'][] = $subQuery;

        return $query;
    }

    /**
     * Returns data from elasticSearch by given query
     *
     * @param $query
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    private function match($query, $perPage = 10, $page = 1)
    {
        $params = [
            'index' => $this->index,
            'type' => $this->indexType,
            'body' =>  $query,
            "size" => $perPage,
            "from" => ($page == 1) ? 0 : ($page * $perPage - $perPage)
        ];

        return \Elasticsearch::search($params);
    }

    /**
     * Extracts array of founded ids
     *
     * @param $response
     * @return array
     */
    public function getIds($response)
    {
        $arrayResults = $response['hits']['hits'];

        return array_map(function ($value) {
            return $value['_source']['id'];
        }, $arrayResults);
    }
}
