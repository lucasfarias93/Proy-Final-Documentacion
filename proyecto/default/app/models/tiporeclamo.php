    <?php

    class Tiporeclamo extends ActiveRecord

    {

        /**

         * Retorna los menu para ser paginados

         *

         */

        public function getTiporeclamo($page, $ppage=20)

        {

            return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');

        }

    }