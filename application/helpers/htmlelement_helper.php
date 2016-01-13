<?php

defined('BASEPATH') OR exit('No direct script access allowed');


    function getHtmlForProducts($products)
    {
        $result = '';
        $divided_products = array_chunk($products, 3, true);
        $col_template = '<div class="col-md-4"><div class="img-circle"><a href=":base_url:products/product/:num"><img src="" alt=""></a></div><div class="bold"><a href=":base_url:products/product/:num" class="products_links">:title</a></div><p>:descr</p><p>Цена: &nbsp; :price</p><p><a href=":base_url:user/add/:product_id" class="addProduct btn btn-primary btn-xs">В корзину</a></p></div>';
        foreach ($divided_products as $products){
            $row = '<div class="row">';
            foreach ($products as $product){
                $temp = str_replace(':title', $product['title'], $col_template);
                $temp = str_replace(':descr', mb_substr($product['description'],0,25).'...', $temp);
                $temp = str_replace(':base_url:', base_url(), $temp);
                $temp = str_replace(':num', $product['id'], $temp);
                $temp = str_replace(':price', $product['price'], $temp);
                $temp = str_replace(':product_id', $product['id'], $temp);
                $row .= $temp;
            }
            $row .= '</div>';
            $result .= $row;
        }
        return $result;
    }

/**
 *
 * Генератор HTML-шаблона корзины
 *
 * @author Paintcast
 *
 * @param $basket – массив, содержимое корзины
 * @param $orders - массив заказов
 * @return string - HTML-код
 *
 */

function getHtmlForBasket($basket, $orders = null){

    // главный шаблон с табами
    $tabs_template = <<<END
<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#basket" aria-controls="home" role="tab" data-toggle="tab">Корзина</a></li>
    <li role="presentation"><a href="#orders" aria-controls="profile" role="tab" data-toggle="tab">Заказы</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="basket" style="margin-top: 20px;">{basket}</div>
    <div role="tabpanel" class="tab-pane" id="orders" style="margin-top: 20px;">{orders}</div>
  </div>

</div>

END;

    // если корзина не пуста
    if($basket)
    {
        $line_number = 1;
        $total_price = 0;
        $basket_html = '';

        // Шаблон корзины: начало
        $basket_head = <<<END
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Наименование товара</th>
                <th>Количество</th>
                <th>Цена</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
END;
        // Шаблон корзины: строчка
        $basket_line = <<<END
            <tr>
                <td>{line_number}</td>
                <td><a href="{url}">{title}</a></td>
                <td>{count}</td>
                <td>{price}</td>
                <td><a href="{href_delete}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
            </tr>
END;
        // Шаблон корзины: конец
        $basket_end = <<<END
            <tr>
                <td colspan="2">
                    <a href="{href_delete}" class="btn btn-primary btn-xs">Очистить корзину</a>
                    <a href="{href_new_order}" class="btn btn-primary btn-xs">Оформить заказ</a>
                </td>
                <td align="right" nowrap>Итого к оплате:</td>
                <td colspan="2">{total_price}</td>
            </tr>
        </tbody>
    </table>

END;
        // делаем корзину
        $basket_html .= $basket_head;

        foreach ( $basket as $item )
        {
            $basket_html .= str_replace(
                array(
                    '{line_number}',
                    '{url}',
                    '{title}',
                    '{count}',
                    '{price}',
                    '{href_delete}'
                ),
                array(
                    $line_number,
                    base_url() . 'products/product/' . $item['id_goods'],
                    $item['title'],
                    $item['cnt'],
                    $item['cnt'] * $item['price'],
                    base_url() . 'orders/clear/' . $item['id_goods']
                ),
                $basket_line
            );
            $line_number++;
            $total_price += $item['cnt'] * $item['price'];
        }

        $basket_html .= str_replace(
            array('{total_price}', '{href_delete}', '{href_new_order}'),
            array($total_price, base_url() . 'orders/clear/', base_url() . 'orders/make/'),
            $basket_end
        );
    }

    // иначе в корзине пусто
    else
    {
        $basket_html = 'В корзине пусто! Сорян!';
    }

    // если имеются заказы
    if($orders)
    {
        $orders_html = '';

        // Шаблон заказов: начало
        $orders_head = <<<END
    <table class="table">
        <thead>
            <tr>
                <th>Дата заказа</th>
                <th>Содержимое</th>
                <th>Сумма заказа</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
END;
        // Шаблон заказов: строчка
        $orders_line = <<<END
            <tr>
                <td>{data_order}</td>
                <td><a href="{url}{id}">Содержимое заказа #{id}</a></td>
                <td>{price}</td>
                <td>{status}</td>
            </tr>
END;
        // Шаблон заказов: конец
        $orders_end = <<<END
        </tbody>
    </table>
END;
        // делаем таблицу с заказами
        $orders_html .= $orders_head;

        foreach ( $orders as $item )
        {
            $orders_html .= str_replace(
                array(
                    '{data_order}',
                    '{url}',
                    '{id}',
                    '{price}',
                    '{status}'
                ),
                array(
                    $item['date_order'],
                    base_url() . 'orders/view/',
                    $item['id'],
                    $item['price'],
                    $item['status']
                ),
                $orders_line
            );
        }

        $orders_html .= $orders_end;

   }

    // иначе заказов нет
    else
    {
        $orders_html = 'Заказов нет. Сорян!';
    }

    $result = str_replace(
        array('{basket}','{orders}'),
        array($basket_html, $orders_html),
        $tabs_template
    );

    return $result;
}

/**
 *
 *  Генератор HTML-шаблона для отображения содержимого заказа
 *
 * @author Paintcast
 *
 * @param $order_content - массив с содержимым заказа
 * @return string = html-код
 */

function getHtmlForOrderView($order_content){
    if($order_content)
    {
        $order_content_html = '';
        $order_total_price = 0;

        // Шаблон отображения заказа: начало
        $order_content_head = <<<END
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название товара</th>
                <th>Количество</th>
                <th>Цена</th>
            </tr>
        </thead>
        <tbody>
END;
        // Шаблон отображения заказа: строчка
        $order_content_line = <<<END
            <tr>
                <td>{id}</td>
                <td>{title}</td>
                <td>{cnt}</td>
                <td>{price}</td>
            </tr>
END;
        // Шаблон заказов: конец
        $order_content_end = <<<END
            <tr>
                <td colspan = "3" align="right">Итого: </td>
                <td>{total}</td>
            </tr>
        </tbody>
    </table>
END;
        // делаем таблицу с содержимым заказа
        $order_content_html .= $order_content_head;

        foreach ( $order_content as $item )
        {
            $order_content_html .= str_replace(
                array(
                    '{id}',
                    '{title}',
                    '{cnt}',
                    '{price}'
                ),
                array(
                    $item['id_goods'],
                    $item['title'],
                    $item['cnt'],
                    $item['price']
                ),
                $order_content_line
            );
            $order_total_price += $item['price'];
        }

        $order_content_html .= str_replace('{total}', $order_total_price, $order_content_end);
    }
    else
    {
        $order_content_html = 'Заказ пуст. Сорян!';
    }

    $result = $order_content_html;

    return $result;
}

/**
 *
 * Генератор HTML-кода для страницы оформления заказа
 *
 * @author Paintcast
 *
 * @param $order_id - ID нового заказа
 * @return mixed - HTML-код
 */

function getHtmlForOrderMake($order_id){
    $html_template = <<<END
<div>
    <p>Заказ #{order_id} успешно офрмлен. <a href="{url}">Перейти</a> на страницу заказа.</p>
</div>
END;
    $result = str_replace(array('{order_id}', '{url}'), array($order_id, base_url() . 'orders/view/' . $order_id), $html_template);

    return $result;

}
