    <?php

    class Operacionrol extends ActiveRecord

    {

        /**

         * Retorna los modelos para ser paginados

         *

         */

        public function getOperacionRol($page, $ppage=20)

        {

            return $this->paginate("page: $page", "per_page: $ppage", 'order: id desc');

        }

    }