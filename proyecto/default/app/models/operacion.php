    <?php

    class Operacion extends ActiveRecord

    {

        /**

         * Retorna los menu para ser paginados

         *

         */

        public function getOperacion($page, $ppage=20)

        {

            return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');

        }

    }