<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Repository;

use Eccube\Annotation\Repository;
use Eccube\Util\Str;

/**
 * ShippingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @Repository
 */
class ShippingRepository extends AbstractRepository
{
    /**
     *
     * @param  array        $searchData
     * @return QueryBuilder
     */
    public function getQueryBuilderBySearchDataForAdmin($searchData)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->leftJoin('s.OrderItems', 'si')
            ->leftJoin('si.Order', 'o');
        // order_id_start
        if (isset($searchData['shipping_id_start']) && Str::isNotBlank($searchData['shipping_id_start'])) {
            $qb
                ->andWhere('s.id >= :shipping_id_start')
                ->setParameter('shipping_id_start', $searchData['shipping_id_start']);
        }
        // multi
        if (isset( $searchData['multi']) && Str::isNotBlank($searchData['multi'])) {
            $multi = preg_match('/^\d+$/', $searchData['multi']) ? $searchData['multi'] : null;
            $qb
                ->andWhere('s.id = :multi OR s.name01 LIKE :likemulti OR s.name02 LIKE :likemulti OR ' .
                           's.kana01 LIKE :likemulti OR s.kana02 LIKE :likemulti OR s.company_name LIKE :likemulti')
                ->setParameter('multi', $multi)
                ->setParameter('likemulti', '%' . $searchData['multi'] . '%');
        }

        // shipping_id_end
        if (isset($searchData['shipping_id_end']) && Str::isNotBlank($searchData['shipping_id_end'])) {
            $qb
                ->andWhere('s.id <= :shipping_id_end')
                ->setParameter('shipping_id_end', $searchData['shipping_id_end']);
        }

        // order_id
        if (isset($searchData['order_id']) && Str::isNotBlank($searchData['order_id'])) {
            $qb
                ->andWhere('o.id = :order_id')
                ->setParameter('order_id', $searchData['order_id']);
        }

        // order status
        if (isset($searchData['order_status']) && count($searchData['order_status'])) {
            $s = $searchData['order_status'];
            $qb
                ->andWhere($qb->expr()->in('o.OrderStatus', ':order_status'))
                ->setParameter('order_status', $searchData['order_status']);
        }
        // shipping status
        if (isset($searchData['shipping_status']) && count($searchData['shipping_status'])) {
            $qb
                ->andWhere($qb->expr()->in('s.ShippingStatus', ':shipping_status'))
                ->setParameter('shipping_status', $searchData['shipping_status']);
        }
        // name
        if (isset($searchData['name']) && Str::isNotBlank($searchData['name'])) {
            $qb
                ->andWhere('CONCAT(s.name01, s.name02) LIKE :name')
                ->setParameter('name', '%' . $searchData['name'] . '%');
        }

        // kana
        if (isset($searchData['kana']) && Str::isNotBlank($searchData['kana'])) {
            $qb
                ->andWhere('CONCAT(s.kana01, s.kana02) LIKE :kana')
                ->setParameter('kana', '%' . $searchData['kana'] . '%');
        }

        // order_name
        if (isset($searchData['order_name']) && Str::isNotBlank($searchData['order_name'])) {
            $qb
                ->andWhere('CONCAT(o.name01, o.name02) LIKE :order_name')
                ->setParameter('order_name', '%' . $searchData['order_name'] . '%');
        }

        // order_kana
        if (isset($searchData['order_kana']) && Str::isNotBlank($searchData['order_kana'])) {
            $qb
                ->andWhere('CONCAT(o.kana01, s.kana02) LIKE :order_kana')
                ->setParameter('kana', '%' . $searchData['order_kana'] . '%');
        }

        // order_email
        if (isset($searchData['email']) && Str::isNotBlank($searchData['email'])) {
            $qb
                ->andWhere('o.email like :email')
                ->setParameter('email', '%' . $searchData['email'] . '%');
        }

        // tel
        if (isset($searchData['tel']) && Str::isNotBlank($searchData['tel'])) {
            $qb
                ->andWhere('CONCAT(s.tel01, s.tel02, s.tel03) LIKE :tel')
                ->setParameter('tel', '%' . $searchData['tel'] . '%');
        }

        // payment
        if (!empty($searchData['payment']) && count($searchData['payment'])) {
            $payments = array();
            foreach ($searchData['payment'] as $payment) {
                $payments[] = $payment->getId();
            }
            $qb
                ->leftJoin('o.Payment', 'p')
                ->andWhere($qb->expr()->in('p.id', ':payments'))
                ->setParameter('payments', $payments);
        }

        // oreder_date
        if (!empty($searchData['order_date_start']) && $searchData['order_date_start']) {
            $date = $searchData['order_date_start'];
            $qb
                ->andWhere('o.order_date >= :order_date_start')
                ->setParameter('order_date_start', $date);
        }
        if (!empty($searchData['order_date_end']) && $searchData['order_date_end']) {
            $date = clone $searchData['order_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('o.order_date < :order_date_end')
                ->setParameter('order_date_end', $date);
        }

        // shipping_delivery_date
        if (!empty($searchData['shipping_delivery_date_start']) && $searchData['shipping_delivery_date_start']) {
            $date = $searchData['shipping_delivery_date_start'];
            $qb
                ->andWhere('s.shipping_delivery_date >= :shipping_delivery_date_start')
                ->setParameter('shipping_delivery_date_start', $date);
        }
        if (!empty($searchData['shipping_delivery_date_end']) && $searchData['shipping_delivery_date_end']) {
            $date = clone $searchData['shipping_delivery_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('s.shipping_delivery_date < :shipping_delivery_date_end')
                ->setParameter('shipping_delivery_date_end', $date);
        }

        // shipping_date
        if (!empty($searchData['shipping_date_start']) && $searchData['shipping_date_start']) {
            $date = $searchData['shipping_date_start'];
            $qb
                ->andWhere('s.shipping_date >= :shipping_date_start')
                ->setParameter('shipping_date_start', $date);
        }
        if (!empty($searchData['shipping_date_end']) && $searchData['shipping_date_end']) {
            $date = clone $searchData['shipping_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('s.shipping_date < :shipping_date_end')
                ->setParameter('shipping_date_end', $date);
        }


        // update_date
        if (!empty($searchData['update_date_start']) && $searchData['update_date_start']) {
            $date = $searchData['update_date_start'];
            $qb
                ->andWhere('s.update_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        }
        if (!empty($searchData['update_date_end']) && $searchData['update_date_end']) {
            $date = clone $searchData['update_date_end'];
            $date = $date
                ->modify('+1 days');
            $qb
                ->andWhere('s.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }

        // payment_total
        if (isset($searchData['payment_total_start']) && Str::isNotBlank($searchData['payment_total_start'])) {
            $qb
                ->andWhere('o.payment_total >= :payment_total_start')
                ->setParameter('payment_total_start', $searchData['payment_total_start']);
        }
        if (isset($searchData['payment_total_end']) && Str::isNotBlank($searchData['payment_total_end'])) {
            $qb
                ->andWhere('o.payment_total <= :payment_total_end')
                ->setParameter('payment_total_end', $searchData['payment_total_end']);
        }

        // buy_product_name
        if (isset($searchData['buy_product_name']) && Str::isNotBlank($searchData['buy_product_name'])) {
            $qb
                ->andWhere('si.product_name LIKE :buy_product_name')
                ->setParameter('buy_product_name', '%' . $searchData['buy_product_name'] . '%');
        }

        // Order By
        $qb->orderBy('s.update_date', 'DESC');
        $qb->addorderBy('s.id', 'DESC');

        return $qb;
    }

    /**
     * 同一商品のお届け先情報を取得
     *
     * @param $Order
     * @return array
     */
    public function findShippingsProduct($Order, $productClass)
    {
        $shippings = $this->createQueryBuilder('s')
            ->innerJoin('Eccube\Entity\OrderItem', 'si', 'WITH', 'si.Shipping = s.id')
            ->where('si.Order = (:order)')
            ->andWhere('si.ProductClass = (:productClass)')
            ->setParameter('order', $Order)
            ->setParameter('productClass', $productClass)
            ->getQuery()
            ->getResult();

        return $shippings;

    }

}
